#!/bin/bash
# Quality Review Agent — vérifie les vues Blade/Livewire après build
# Usage: bash quality-review.sh [dir]
# Retourne un rapport structuré, exit 0 si propre, 1 si problèmes

DIR="${1:-/opt/data/LifeCheck}"
cd "$DIR" || exit 1

ERRORS=0
WARNINGS=0
REPORT=""

report_line() {
    REPORT+="$1"$'\n'
}

check_blade_syntax() {
    local file="$1"
    local issues=()

    # @if sans @endif
    local opens
    local closes
    opens=$(grep -cP '@(if|unless|forelse|foreach|for|while|auth|guest|can|cannot|canany|production|env|switch)' "$file" 2>/dev/null || echo 0)
    closes=$(grep -cP '@(endif|endunless|endforelse|endforeach|endfor|endwhile|endauth|endguest|endcan|endcannot|endcanany|endproduction|endenv|endswitch)' "$file" 2>/dev/null || echo 0)
    if [ "$opens" -ne "$closes" ]; then
        issues+=("⚠️  $opens directives d'ouverture ≠ $closes fermetures")
    fi

    # @section sans @endsection/@stop
    sec_opens=$(grep -cP '@section\b' "$file" 2>/dev/null || echo 0)
    sec_closes=$(grep -cP '@(endsection|stop)' "$file" 2>/dev/null || echo 0)
    if [ "$sec_opens" -ne "$sec_closes" ]; then
        issues+=("⚠️  @section: $sec_opens ouvertures ≠ $sec_closes fermetures")
    fi

    # @props mal formé
    if grep -qP '@props\[' "$file" 2>/dev/null; then
        issues+=("❌ @props[ ] syntaxe invalide — utiliser @props([ ])")
    fi

    # {!! !!} sans échappement — warning seulement
    if grep -qP '\{!!.*\$[a-zA-Z_]' "$file" 2>/dev/null; then
        issues+=("ℹ️  {!! !!} utilisé — vérifier que c'est intentionnel (XSS possible)")
    fi

    # echo dans du PHP inline
    if grep -qP '@php\s+echo\s' "$file" 2>/dev/null; then
        issues+=("ℹ️  @php echo — préférer {{\$var}}")
    fi

    printf '%s\n' "${issues[@]}"
}

check_livewire() {
    local file="$1"
    local issues=()

    # wire:model sans wire:key sur les boucles (problème connu Livewire)
    local in_loop=0
    local has_wire_key=0
    while IFS= read -r line; do
        if echo "$line" | grep -qP '@(foreach|forelse)'; then
            in_loop=1
        elif echo "$line" | grep -qP '@(endforeach|endforelse)'; then
            if [ "$in_loop" -eq 1 ] && [ "$has_wire_key" -eq 0 ]; then
                issues+=("ℹ️  Boucle sans wire:key — problèmes de réactivité Livewire")
            fi
            in_loop=0
            has_wire_key=0
        fi
        if echo "$line" | grep -qP 'wire:key\b'; then
            has_wire_key=1
        fi
        # wire:model sans wire:model.live ou .blur — juste info
        if echo "$line" | grep -qP 'wire:model[= ]' && ! echo "$line" | grep -qP 'wire:model\.(live|blur|debounce)'; then
            issues+=("ℹ️  wire:model sans modifieur (.live/.blur) — perf réduite")
        fi
    done < <(grep -n '' "$file" 2>/dev/null)

    # wire:loading sans wire:target
    local loading_count
    loading_count=$(grep -cP 'wire:loading[.\s]' "$file" 2>/dev/null || echo 0)
    local target_count
    target_count=$(grep -cP 'wire:target\b' "$file" 2>/dev/null || echo 0)
    if [ "$loading_count" -gt 0 ] && [ "$target_count" -eq 0 ]; then
        issues+=("ℹ️  wire:loading sans wire:target — peut cibler le mauvais élément")
    fi

    printf '%s\n' "${issues[@]}"
}

check_alpine() {
    local file="$1"
    local issues=()

    # x-data sans JSON valide
    while IFS= read -r line; do
        if echo "$line" | grep -qP 'x-data="[^"]*\{'; then
            local json_part
            json_part=$(echo "$line" | grep -oP 'x-data="\K[^"]+')
            # tentative de détection basique : {} pas fermé
            local open_b
            local close_b
            open_b=$(echo "$json_part" | grep -o '{' | wc -l)
            close_b=$(echo "$json_part" | grep -o '}' | wc -l)
            if [ "$open_b" -ne "$close_b" ]; then
                issues+=("⚠️  x-data: accolades non équilibrées")
            fi
        fi
    done < <(grep -n 'x-data=' "$file" 2>/dev/null)

    # x-init avec des chaînes potentiellement dangereuses
    if grep -qP 'x-init="[^"]*\$wire' "$file" 2>/dev/null; then
        issues+=("ℹ️  x-init avec \$wire — préférer mount() Livewire")
    fi

    printf '%s\n' "${issues[@]}"
}

check_routes() {
    local file="$1"
    local issues=()
    local named_routes

    # Extraire les route() dans les templates
    named_routes=$(grep -oP "route\('([^']+)'\)" "$file" 2>/dev/null | grep -oP "'[^']+'" | tr -d "'" | sort -u)

    if [ -n "$named_routes" ]; then
        while IFS= read -r r; do
            [ -z "$r" ] && continue
            if ! grep -qR "->name('$r')" "$DIR/routes/" 2>/dev/null && \
               ! grep -qR "->name('$r')" "$DIR/routes/web.php" 2>/dev/null && \
               ! grep -qR "'$r'" "$DIR/routes/" 2>/dev/null; then
                # Vérification plus large
                if ! grep -qR "$r" "$DIR/routes/" 2>/dev/null; then
                    issues+=("⚠️  route('$r') — nom de route non trouvé dans routes/")
                fi
            fi
        done <<< "$named_routes"
    fi

    printf '%s\n' "${issues[@]}"
}

check_php_syntax() {
    local file="$1"
    # Vérifie le PHP inline dans les vues
    local has_php
    has_php=$(grep -cP '@php' "$file" 2>/dev/null || echo 0)
    local has_endphp
    has_endphp=$(grep -cP '@endphp' "$file" 2>/dev/null || echo 0)
    if [ "$has_php" -ne "$has_endphp" ]; then
        echo "⚠️  @php/$has_php ouvertures ≠ @endphp/$has_endphp fermetures"
    fi
}

echo "🔍 QUALITY REVIEW — $(date '+%Y-%m-%d %H:%M:%S')"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━"

# Scanner tous les fichiers Blade
while IFS= read -r file; do
    rel="${file#$DIR/}"
    file_errors=0

    blade_issues=$(check_blade_syntax "$file")
    livewire_issues=$(check_livewire "$file")
    alpine_issues=$(check_alpine "$file")
    route_issues=$(check_routes "$file")
    php_issues=$(check_php_syntax "$file")

    all_issues=""
    [ -n "$blade_issues" ] && all_issues+="$blade_issues"$'\n'
    [ -n "$livewire_issues" ] && all_issues+="$livewire_issues"$'\n'
    [ -n "$alpine_issues" ] && all_issues+="$alpine_issues"$'\n'
    [ -n "$route_issues" ] && all_issues+="$route_issues"$'\n'
    [ -n "$php_issues" ] && all_issues+="$php_issues"$'\n'

    if [ -n "$all_issues" ]; then
        echo ""
        echo "📄 $rel"
        while IFS= read -r issue; do
            [ -z "$issue" ] && continue
            echo "   $issue"
            if echo "$issue" | grep -q '❌'; then
                ((ERRORS++))
            else
                ((WARNINGS++))
            fi
        done <<< "$all_issues"
    fi
done < <(find "$DIR/resources/views" -name "*.blade.php")

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 RÉSULTAT : $ERRORS erreur(s), $WARNINGS avertissement(s)"

if [ "$ERRORS" -gt 0 ]; then
    echo "❌ Revue ÉCHOUÉE — des erreurs bloquantes détectées"
    exit 1
else
    echo "✅ Revue PASSÉE"
    exit 0
fi
