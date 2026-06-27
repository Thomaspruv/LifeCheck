#!/bin/bash
# Quality Review Agent — vérifie les vues Blade/Livewire après build
# Usage: bash quality-review.sh [dir]
# 0 = propre, 1 = problèmes bloquants, 2 = avertissements

DIR="${1:-/opt/data/LifeCheck}"
cd "$DIR" || exit 1
ARTISAN="php artisan"
STATUS=0

echo "🔍 QUALITY REVIEW — $(date '+%Y-%m-%d %H:%M:%S')"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━"

# ── 1. Syntaxe Blade brute ──────────────────────────────────
echo ""
echo "📋 1. Syntaxe Blade / PHP inline"
echo "    ────────────────────────────"
has_blade_issue=false

while IFS= read -r file; do
    rel="${file#$DIR/}"
    out=$(mktemp)

    # @if/@endif balance
    opens=$(grep -coP '@(if|unless|forelse|foreach|for|while|auth|guest|can|cannot|canany|production|env|switch)\b' "$file")
    closes=$(grep -coP '@(endif|endunless|endforelse|endforeach|endfor|endwhile|endauth|endguest|endcan|endcannot|endcanany|endproduction|endenv|endswitch)\b' "$file")
    if [ "$opens" -ne "$closes" ] 2>/dev/null; then
        echo "   ❌  $rel — $opens directives ≠ $closes fermetures"
        has_blade_issue=true
    fi

    # @section/@endsection
    so=$(grep -coP '@section\b' "$file")
    sc=$(grep -coP '@(endsection|stop)\b' "$file")
    if [ "$so" -ne "$sc" ] 2>/dev/null; then
        echo "   ❌  $rel — @section: $so ouvertures ≠ $sc fermetures"
        has_blade_issue=true
    fi

    # @php/@endphp
    po=$(grep -coP '@php\b' "$file")
    pc=$(grep -coP '@endphp\b' "$file")
    if [ "$po" -ne "$pc" ] 2>/dev/null; then
        echo "   ❌  $rel — @php: $po ouvertures ≠ $pc fermetures"
        has_blade_issue=true
    fi

    rm -f "$out"
done < <(find "$DIR/resources/views" -name "*.blade.php" 2>/dev/null)

if [ "$has_blade_issue" = false ]; then
    echo "   ✅ Aucune erreur de syntaxe"
fi

if $has_blade_issue; then
    STATUS=1
fi

# ── 2. Routes nommées valides ────────────────────────────────
echo ""
echo "📋 2. Routes nommées dans les vues"
echo "    ──────────────────────────────"

# Cache les routes une fois
$ARTISAN route:list --json 2>/dev/null > /tmp/_rroutes.json
has_route_issue=false

while IFS= read -r file; do
    rel="${file#$DIR/}"
    # Ne PAS matcher $request->route('...') — c'est un appel de méthode, pas le helper
    routes=$(grep -oP "(?<!\$[a-zA-Z_]+->)route\('[^']+'\)" "$file" 2>/dev/null | grep -oP "'[^']+'" | tr -d "'" | sort -u)
    [ -z "$routes" ] && continue

    while IFS= read -r r; do
        [ -z "$r" ] && continue
        if ! jq -e ".[] | select(.name == \"$r\")" /tmp/_rroutes.json >/dev/null 2>&1; then
            echo "   ⚠️  $rel — route('$r') introuvable"
            has_route_issue=true
        fi
    done <<< "$routes"
done < <(find "$DIR/resources/views" -name "*.blade.php" 2>/dev/null)

if [ "$has_route_issue" = false ]; then
    echo "   ✅ Toutes les routes référencées existent"
fi

# ── 3. PHP syntax check ──────────────────────────────────────
echo ""
echo "📋 3. Syntaxe PHP (fichiers app/ + tests/)"
echo "    ───────────────────────────────────────"
has_php_issue=false

while IFS= read -r file; do
    if ! php -l "$file" >/dev/null 2>&1; then
        rel="${file#$DIR/}"
        echo "   ❌  $rel — erreur de syntaxe PHP"
        php -l "$file" 2>&1 | sed 's/^/       /'
        has_php_issue=true
    fi
done < <(find "$DIR/app" "$DIR/tests" "$DIR/database" -name "*.php" -not -path "*/migrations/*" 2>/dev/null)

if [ "$has_php_issue" = false ]; then
    echo "   ✅ Pas d'erreur de syntaxe"
fi

if $has_php_issue; then
    STATUS=1
fi

# ── 4. Contrôleurs → vues qui existent ──────────────────────
echo ""
echo "📋 4. Vues référencées par les contrôleurs"
echo "    ───────────────────────────────────────"
has_view_issue=false

while IFS= read -r file; do
    views=$(grep -oP "(view\('|->render\(')['\"]?([a-zA-Z0-9._-]+)['\"]?" "$file" 2>/dev/null | grep -oP "'[^']+'" | tr -d "'")
    [ -z "$views" ] && continue

    while IFS= read -r v; do
        [ -z "$v" ] && continue
        view_path="$DIR/resources/views/$(echo "$v" | tr '.' '/').blade.php"
        if [ ! -f "$view_path" ]; then
            rel="${file#$DIR/}"
            echo "   ⚠️  $rel — vue '$v' absente ($view_path)" >&2
            has_view_issue=true
        fi
    done <<< "$views"
done < <(find "$DIR/app/Http/Controllers" -name "*.php" 2>/dev/null)

if [ "$has_view_issue" = false ]; then
    echo "   ✅ Toutes les vues référencées existent"
fi

# ── 5. Livewire components existants ─────────────────────────
echo ""
echo "📋 5. Composants Livewire référencés"
echo "    ─────────────────────────────────"
has_lw_issue=false

while IFS= read -r file; do
    rel="${file#$DIR/}"
    components=$(grep -oP '@livewire\s*\(\s*['\"'"'\"'"]?[a-zA-Z0-9._-]+['\"'"'\"'"]?' "$file" 2>/dev/null | grep -oP "[a-zA-Z0-9._-]+$" | sort -u)
    
    while IFS= read -r comp; do
        [ -z "$comp" ] && continue
        # Convert dots to slashes/path separators
        comp_path=$(echo "$comp" | sed 's/\./\//g')
        found=false
        for base in "$DIR/app/Livewire" "$DIR/app/Http/Livewire"; do
            [ -f "$base/${comp_path}.php" ] && found=true
        done
        if [ "$found" = false ]; then
            echo "   ⚠️  $rel — composant Livewire '$comp' introuvable"
            has_lw_issue=true
        fi
    done <<< "$components"
done < <(find "$DIR/resources/views" -name "*.blade.php" 2>/dev/null)

if [ "$has_lw_issue" = false ]; then
    echo "   ✅ Tous les composants Livewire existent"
fi

# ── 6. Migrations en attente ─────────────────────────────────
echo ""
echo "📋 6. Migrations"
echo "    ────────────"
mig_out=$($ARTISAN migrate:status 2>&1)
pending=$(echo "$mig_out" | grep -c "Pending" || true)
if [ "$pending" -gt 0 ]; then
    echo "   ⚠️  $pending migration(s) en attente — lancer 'php artisan migrate'"
fi

# ── Résumé ────────────────────────────────────────────────────
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ "$STATUS" -eq 0 ]; then
    echo "✅ REVUE PASSÉE — tout est propre"
else
    echo "❌ REVUE ÉCHOUÉE — corrige les erreurs ci-dessus"
fi

exit $STATUS
