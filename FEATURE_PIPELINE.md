# Feature Pipeline — Process de Développement

## Architecture

```
Idée (ID) → PM → Tech Lead → QA → Dev → Security → Merge → GitHub
```

7 phases, chacune avec un rôle, des livrables, et une validation avant de passer à la suivante.

---

## Les 7 Phases

### Phase 0 : ID (Ideation / Discovery)
**Rôle :** Générateur d'idées produit fondées sur des données réelles.

**Sources d'inspiration :**
- **Marché** — scraper/analyser les concurrents directs et indirects, les tendances du marché, les features populaires des apps similaires (`web_search`, `web_extract`)
- **Utilisateurs** — analyser les données de prod (quand la DB sera accessible) : pages vues, drop-off, fonctionnalités les plus/moins utilisées, patterns de rétention
- **Vision produit** — vérifier la cohérence avec la roadmap et le positionnement de LifeCheck
- **Feedback implicite** — analyser les logs d'erreur, les comportements répétés, les abandons en cours de flow

**Livrable :** `.hermes/features/<feature>/00-ideation.md`
- Contexte marché (qui fait quoi, tendances identifiées)
- Analyse utilisateur (données disponibles, patterns, frictions)
- **2-3 propositions concrètes de feature** avec pour chacune : objectif métier, KPIs attendus, faisabilité estimée (rough), priorité suggérée

**Gate :** ✅ **Pierre choisit une idée** à pousser en Phase 1

---

## Les 6 Phases Restantes

### Phase 1 : PM (Product Manager)
Analyser le produit existant, comprendre la vision, rédiger les **specs fonctionnelles**.

**Livrable :** `.hermes/features/<feature>/01-specs-fonctionnelles.md`
**Gate :** ✅ Pierre valide

### Phase 2 : Tech Lead
Transcrire les specs fonctionnelles en **instructions techniques précises** (modèles, contrôleurs, vues, routes, découpage en tâches).

**Livrable :** `.hermes/features/<feature>/02-specs-techniques.md`
**Gate :** ✅ Pierre valide

### Phase 3 : QA (Test Plan)
Définir la **liste des tests** nécessaires : tests PHP (Pest), tests E2E Playwright, tests visuels.

**Livrable :** `.hermes/features/<feature>/03-plan-de-tests.md`
**Gate :** ✅ Pierre valide

### Phase 4 : Dev (Implémentation)
Implémenter la feature en TDD par sous-tâches atomiques. Chaque sous-tâche = 1 commit.

**Règles :** `php artisan test` vert avant chaque commit, push systématique
**Gate :** ✅ Tous les tests passent

### Phase 5 : Security (Revue Sécurité)
Vérifier : XSS, CSRF, SQL Injection, Mass Assignment, Authorization, Validation, dépendances.

**Livrable :** `.hermes/features/<feature>/05-rapport-securite.md`
**Gate :** ✅ Aucune vulnérabilité bloquante

### Phase 6 : Merge
Tests finaux → merge sur `main` → push → nettoyage branche.

**Livrable :** Feature sur `main` sur GitHub

---

## Règles d'or

1. **Toutes les phases sont obligatoires**, même pour une petite feature
2. **Pierre valide chaque gate** avant de passer à la suivante
3. **Toujours commit + push** — rien ne reste en local
4. **Tests verts obligatoires** avant chaque commit
6. **Branche dédiée** pour chaque feature : `feature/<slug>`

## Résumé des Phases

| Phase | Rôle | Livrable | Gate |
|-------|------|----------|------|
| 0 | ID (Ideation) | `00-ideation.md` | ✅ Pierre choisit une idée |
| 1 | PM | `01-specs-fonctionnelles.md` | ✅ Pierre valide |
| 2 | Tech Lead | `02-specs-techniques.md` | ✅ Pierre valide |
| 3 | QA | `03-plan-de-tests.md` | ✅ Pierre valide |
| 4 | Dev | Code + tests verts | ✅ `php artisan test` |
| 5 | Security | `05-rapport-securite.md` | ✅ Aucune vuln |
| 6 | Merge | Feature sur `main` | ✅ Push réussi |
