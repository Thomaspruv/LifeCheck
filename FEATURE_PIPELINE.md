# Feature Pipeline — Process de Développement

## Architecture

```
Idée → PM → Tech Lead → QA → Dev → Security → Merge → GitHub
```

6 phases, chacune avec un rôle, des livrables, et une validation avant de passer à la suivante.

---

## Les 6 Phases

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
5. **Branche dédiée** pour chaque feature : `feature/<slug>`
