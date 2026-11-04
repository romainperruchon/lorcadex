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
	</style>
  </head>
  <body>
  
  <main>
	<div class="container-fluid mb-3">
		<a href="dashboard.php" class="btn btn-primary">← Retour au tableau de bord</a>
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