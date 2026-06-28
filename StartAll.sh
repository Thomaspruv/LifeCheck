#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

info()    { echo -e "${BLUE}ℹ${NC}  $*"; }
success() { echo -e "${GREEN}✓${NC}  $*"; }
warn()    { echo -e "${YELLOW}⚠${NC}  $*"; }
error()   { echo -e "${RED}✗${NC}  $*" >&2; }

check_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        error "Commande introuvable : $1"
        exit 1
    fi
}

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  LifeCheck — démarrage local"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

info "Vérification des prérequis…"
check_command php
check_command composer
check_command node
check_command npm

PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
if ! php -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);'; then
    error "PHP 8.3+ requis (version actuelle : ${PHP_VERSION})"
    exit 1
fi
success "PHP ${PHP_VERSION}, Composer, Node et npm sont disponibles"

if [ ! -f vendor/autoload.php ]; then
    if [ -d vendor ]; then
        warn "Dossier vendor incomplet — réinstallation des dépendances PHP…"
        rm -rf vendor
    else
        info "Installation des dépendances PHP (composer install)…"
    fi
    if ! composer install --no-interaction; then
        error "composer install a échoué — vérifie l'espace disque disponible (df -h)"
        exit 1
    fi
    if [ ! -f vendor/autoload.php ]; then
        error "vendor/autoload.php introuvable après composer install"
        exit 1
    fi
    success "Dépendances PHP installées"
else
    success "Dépendances PHP déjà présentes"
fi

if [ ! -f .env ]; then
    info "Création du fichier .env à partir de .env.example…"
    cp .env.example .env
    success "Fichier .env créé"
    warn "Pense à renseigner TELEGRAM_BOT_TOKEN et OPENAI_API_KEY dans .env si besoin"
else
    success "Fichier .env déjà présent"
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    info "Génération de la clé d'application…"
    php artisan key:generate --force
    success "Clé APP_KEY générée"
fi

if grep -q '^DB_CONNECTION=sqlite' .env; then
    if [ ! -f database/database.sqlite ]; then
        info "Création de la base SQLite…"
        touch database/database.sqlite
        success "Fichier database/database.sqlite créé"
    fi
fi

info "Application des migrations…"
php artisan migrate --force --no-interaction
success "Base de données à jour"

if [ ! -f node_modules/vite/package.json ]; then
    if [ -d node_modules ]; then
        warn "Dossier node_modules incomplet — réinstallation des dépendances Node…"
        rm -rf node_modules
    else
        info "Installation des dépendances Node (npm install)…"
    fi
    if ! npm install --ignore-scripts; then
        error "npm install a échoué — essaie : rm -rf node_modules && npm install"
        exit 1
    fi
    if [ ! -f node_modules/vite/package.json ]; then
        error "node_modules/vite introuvable après npm install"
        exit 1
    fi
    success "Dépendances Node installées"
else
    success "Dépendances Node déjà présentes"
fi

chmod -R u+rwX storage bootstrap/cache 2>/dev/null || true

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
success "Environnement prêt — lancement des services"
echo ""
echo "  Application : http://127.0.0.1:8000"
echo "  Vite (HMR)  : http://localhost:5173"
echo ""
echo "  Services : serve | queue | logs (pail) | vite"
echo "  Arrêt    : Ctrl+C"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

exec composer dev
