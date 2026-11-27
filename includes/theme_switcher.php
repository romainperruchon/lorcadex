<?php
// Composant réutilisable pour le sélecteur de thème et bouton de déconnexion
// À inclure après le <main> tag
?>
<div class="container-fluid mb-3" style="display: flex; align-items: center; justify-content: space-between;">
	<div>
		<?php if (isset($show_back_button) && $show_back_button): ?>
		<a href="dashboard.php" class="btn-back-dashboard">
			<span>🏠</span>
			<span>Retour au tableau de bord</span>
		</a>
		<?php endif; ?>
	</div>
	
	<div style="display: flex; gap: 12px; align-items: center;">
		<div class="theme-switcher-wrapper" style="margin-left: 0;">
			<small style="margin-right: 6px;">Thème:</small>
			<div class="theme-switcher" role="group" aria-label="Choix du thème">
				<button type="button" class="btn-sm" data-bs-theme-value="light" aria-pressed="false" title="Thème clair">☀️ Clair</button>
				<button type="button" class="btn-sm" data-bs-theme-value="dark" aria-pressed="false" title="Thème sombre">🌙 Sombre</button>
				<button type="button" class="btn-sm" data-bs-theme-value="auto" aria-pressed="true" title="Thème automatique">🔄 Auto</button>
			</div>
		</div>
		
		<a href="logout.php" class="btn-logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">
			<span>🚪</span>
			<span>Déconnexion</span>
		</a>
	</div>
</div>

