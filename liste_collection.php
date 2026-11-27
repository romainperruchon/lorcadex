<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"])) {
	
	$id_user = $_SESSION["id_user"];
	
?>
<!doctype html>
<html lang="fr" data-bs-theme="auto">
  <head>
	<script src="./assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Romain PERRUCHON">
    <title>Lorcadex</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
	
	<link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
	
	<style>	
	/*
	html { 
	  background: url(img/fond2.jpg) no-repeat center center fixed; 
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}
	
	main { 
	  background: url(img/fond2.jpg) no-repeat center center fixed; 
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}
	*/
	
	.circle{
		width:50px;
		height:50px;
		border-radius:100%;
		background-color:#3D6AA2;
		color:#fff;
		text-align:center;
		font-size:12px;
		overflow:hidden;
		margin: 2px;
		margin-left:8px;
	}

	.circle.one-line{line-height:48px;}
	
	.short-div {
		height:50%;
	}
	
	a.nounderline:link 
	{ 
	 text-decoration:none; 
	}
	
	.btn-back-dashboard {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 10px 20px;
		background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
		color: white;
		text-decoration: none;
		border-radius: 8px;
		font-weight: 500;
		font-size: 15px;
		border: none;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 2px 8px rgba(0, 86, 179, 0.3);
	}
	
	.btn-back-dashboard:hover {
		background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
		color: white;
		text-decoration: none;
		box-shadow: 0 4px 16px rgba(0, 86, 179, 0.5);
		transform: translateY(-2px);
	}
	
	.btn-back-dashboard:active {
		transform: translateY(0);
		box-shadow: 0 2px 8px rgba(0, 86, 179, 0.3);
	}
	
	.theme-switcher-wrapper {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-left: auto;
	}
	
	.theme-switcher {
		display: inline-flex;
		gap: 6px;
		padding: 6px;
		background: rgba(0, 0, 0, 0.1);
		border-radius: 8px;
	}
	
	[data-bs-theme-value] {
		padding: 6px 12px;
		border: 2px solid transparent;
		background: none;
		cursor: pointer;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 500;
		transition: all 0.2s ease;
		color: var(--bs-body-color);
	}
	
	[data-bs-theme-value]:hover {
		background: rgba(0, 0, 0, 0.1);
	}
	
	[data-bs-theme-value].active {
		background: var(--bs-primary);
		color: white;
		border-color: var(--bs-primary);
	}
	
	.btn-logout {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 8px 14px;
		background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
		color: white;
		text-decoration: none;
		border-radius: 6px;
		font-weight: 500;
		font-size: 14px;
		border: none;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
	}
	
	.btn-logout:hover {
		background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
		color: white;
		text-decoration: none;
		box-shadow: 0 4px 12px rgba(220, 53, 69, 0.5);
		transform: translateY(-2px);
	}
	
	.btn-logout:active {
		transform: translateY(0);
		box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
	}
	</style>
  </head>
  <body>
  
  <main>
	<div class="container-fluid mb-3" style="display: flex; align-items: center; justify-content: space-between;">
		<a href="dashboard.php" class="btn-back-dashboard">
			<span>🏠</span>
			<span>Retour au tableau de bord</span>
		</a>
		
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
	
	<div class="container">
	
	<?php
	$sql_cat = "SELECT DISTINCT(categorie) FROM chapitres ORDER BY id DESC";
	$res_cat = $mysqli->query($sql_cat);
	
	while ($t_cat = $res_cat->fetch_assoc()) {
		
		$catégorie_chapitre	= $t_cat['categorie'];
		
		echo "<h1>$catégorie_chapitre</h1>";
		
		$sql_c = "SELECT * FROM chapitres WHERE categorie='$catégorie_chapitre' ORDER BY id DESC";
		$res_c = $mysqli->query($sql_c);
		
		while ($t_c = $res_c->fetch_assoc()) {
		
			$id_chapitre		= $t_c['id_chapitre'];
			$nom_chapitre 		= $t_c['nom_chapitre'];
			$nb_cartes_chapitre	= $t_c['nb_cartes'];
			$nb_ench_chapitre	= $t_c['nb_enchantees'];
			
			$nb_cartes_total = $nb_cartes_chapitre + $nb_ench_chapitre;
			
			echo "<div class='row'>";
		
			if($catégorie_chapitre == 'Promos') {
				echo "	<div class='col-1' style='width:50px;'><a href='collection.php?collection=$id_chapitre'><img src='img/promo.png' width='100%' height='100%' class='img-fluid' /></a></div>";
			}
			else {				
				$sigle_chapitre		= $t_c['sigle'];
				echo "	<div class='col-1 circle one-line'>$sigle_chapitre</div>";
			}
			
			$sql_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND chapitre='$id_chapitre' AND rarete != 6 GROUP BY user_as_carte.id_carte";
			$res_user = $mysqli->query($sql_user);
			$nb_cartes_user = $res_user->num_rows;
			
			$sql_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND chapitre='$id_chapitre' AND rarete != 6 AND foil='1' GROUP BY user_as_carte.id_carte";
			$res_user = $mysqli->query($sql_user);
			$nb_cartes_foil_user = $res_user->num_rows;
			
			$sql_user_ench = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND chapitre='$id_chapitre' AND rarete = 6 GROUP BY user_as_carte.id_carte";
			$res_user_ench = $mysqli->query($sql_user_ench);
			$nb_cartes_ench_user = $res_user_ench->num_rows;
			
			echo "	<div class='col-8' style='padding-left:10px;'>";
			echo "		<div class='short-div col-12'><a class='nounderline' href='collection.php?collection=$id_chapitre'>$nom_chapitre</a></div>";
			echo "		<div class='short-div col-12'>";
			echo "			<div class='col12' "; 
			if ($nb_ench_chapitre == 0) {
				echo " style='margin-top:3px; ";
			}
			echo "'>";
			echo affiche_jauge($nb_cartes_user, $nb_cartes_chapitre); 
			echo "			</div>";
			echo "			<div class='col12'>";
			echo affiche_jauge($nb_cartes_foil_user, $nb_cartes_chapitre); 
			echo "			</div>";
			if ($nb_ench_chapitre > 0) {
			echo "			<div class='col12'>";
			echo $pourc = affiche_jauge($nb_cartes_ench_user, $nb_ench_chapitre); 
			echo "			</div>";
			}
			echo "		</div>";
			echo "	</div>";
			
			
			
			echo "	<div class='col-2' style='padding-left:10px;'>";
			echo "		<div class=' col-12' style='font-size:12px;'>".round(pourc_jauge($nb_cartes_user, $nb_cartes_chapitre))."% / ".round(pourc_jauge($nb_cartes_foil_user, $nb_cartes_chapitre))."%</div>";
			echo "		<div class=' col-12' style='font-size:12px;'>$nb_cartes_user/$nb_cartes_chapitre</div>";
			echo "		<div class=' col-12' style='font-size:12px;'>$nb_cartes_foil_user/$nb_cartes_chapitre</div>";
			if ($nb_ench_chapitre > 0) {
			echo "		<div class=' col-12' style='font-size:12px;'>$nb_cartes_ench_user/$nb_ench_chapitre</div>";
			}
			echo "	</div>";
			echo "	<div style='padding-top:10px'></div>";
			
			echo "</div>";
		
		}
		
		echo "<hr>";
	}
	?>
	
	</div>
  </main>
  
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js" crossorigin="anonymous"></script>

  </body>
</html>
<?php 
}
else {
	header("location:login.php");
}
?>