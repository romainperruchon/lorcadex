# Guide d'intégration du sélecteur de thème

## 🎨 Qu'est-ce qui a été amélioré ?

Un sélecteur de thème a été ajouté pour permettre aux utilisateurs de choisir entre :
- **☀️ Clair** : Toujours afficher en thème clair
- **🌙 Sombre** : Toujours afficher en thème sombre
- **🔄 Auto** : Utiliser les préférences système

## 📁 Fichiers créés/modifiés

### Nouveaux fichiers :
- `includes/theme_switcher.php` - Composant réutilisable
- `assets/css/theme-switcher.css` - Styles partagés

### Fichiers modifiés :
- `liste_collection.php` - Exemple d'implémentation

## 🚀 Comment intégrer sur d'autres pages

### Étape 1 : Ajouter le lien CSS dans le `<head>`
```html
<link href="./assets/css/theme-switcher.css" rel="stylesheet">
```

### Étape 2 : S'assurer que le fichier `color-modes.js` est chargé
```html
<script src="./assets/js/color-modes.js"></script>
```

### Étape 3 : Ajouter le sélecteur après le tag `<main>`

#### Option A : Sans bouton retour
```php
<?php include('includes/theme_switcher.php'); ?>
```

#### Option B : Avec bouton retour (défaut)
```php
<?php 
  $show_back_button = true;
  include('includes/theme_switcher.php'); 
?>
```

#### Option C : Code inline (si vous préférez ne pas utiliser l'include)
```html
<div class="container-fluid mb-3" style="display: flex; align-items: center; justify-content: space-between;">
	<div>
		<a href="dashboard.php" class="btn-back-dashboard">
			<span>🏠</span>
			<span>Retour au tableau de bord</span>
		</a>
	</div>
	
	<div class="theme-switcher-wrapper">
		<small style="margin-right: 6px;">Thème:</small>
		<div class="theme-switcher" role="group" aria-label="Choix du thème">
			<button type="button" class="btn-sm" data-bs-theme-value="light" aria-pressed="false" title="Thème clair">☀️ Clair</button>
			<button type="button" class="btn-sm" data-bs-theme-value="dark" aria-pressed="false" title="Thème sombre">🌙 Sombre</button>
			<button type="button" class="btn-sm" data-bs-theme-value="auto" aria-pressed="true" title="Thème automatique">🔄 Auto</button>
		</div>
	</div>
</div>
```

## 💾 Fonctionnement technique

Le sélecteur utilise le script `color-modes.js` de Bootstrap qui :
1. Détecte le thème préféré de l'utilisateur (système ou localStorage)
2. Applique l'attribut `data-bs-theme` au tag `<html>`
3. Bootstrap réagit automatiquement et change les couleurs

Les choix sont sauvegardés dans `localStorage`, donc persister entre les sessions.

## ✨ Personnalisation

Les boutons et styles peuvent être modifiés dans `assets/css/theme-switcher.css`.

Pour changer les icônes, modifiez les emojis dans le code HTML :
- `☀️` → Thème clair
- `🌙` → Thème sombre
- `🔄` → Thème automatique
