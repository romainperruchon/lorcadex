<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"])) {
	
	$id_user = $_SESSION["id_user"];
	
	if (isset($_GET['id_carte']) && $_GET['id_carte'] != "") {
	
		$verif = preg_match("#^[0-9]+$#i",$_GET['id_carte']);
			
		if($verif){
	
			$id_carte = $_GET['id_carte'];
	
			// récupération de la carte
			$sql = "SELECT * FROM cartes WHERE id_carte='$id_carte'";
			$res = $mysqli->query($sql);
			$t_carte = $res->fetch_assoc();
			
			$num_carte 	= $t_carte["numero_carte"];
			$nom_carte	= $t_carte["nom_carte"];
			$cat_carte	= $t_carte["categorie_carte"];
			$rarete		= $t_carte["rarete"];
			$encre		= $t_carte["encre"];
			$chapitre	= $t_carte["chapitre"];
			
			$sql = "SELECT nom_chapitre FROM chapitres WHERE id_chapitre = '$chapitre'";
			$res = $mysqli->query($sql);
			$t_chap = $res->fetch_assoc();
			
			$nom_chapitre = $t_chap['nom_chapitre'];
			
			$t_nom_carte = explode("\n", $nom_carte);
			$nom_carte_princ = $t_nom_carte[0];
			$nom_carte_sec = "";
			if (count($t_nom_carte) > 1) {
				$nom_carte_sec = $t_nom_carte[1];
			}

			if (isset($_POST['nb_norm_moins'])) {
				$sql = "UPDATE user_as_carte SET quantite = quantite - 1 WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='0' AND quantite > 0";
				$mysqli->query($sql);
			}
			
			if (isset($_POST['nb_foil_moins'])) {
				$sql = "UPDATE user_as_carte SET quantite = quantite - 1 WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='1' AND quantite > 0";
				$mysqli->query($sql);
			}			
			
			// Nombre non foil
			$sql_nb_nf = "SELECT quantite FROM user_as_carte WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='0'";
			$res_nb_nf = $mysqli->query($sql_nb_nf);
			if($t_norm = $res_nb_nf->fetch_assoc()) {
				$nb_nf = $t_norm['quantite'];
			}
			else {
				$nb_nf = 0;
			}
			
			if (isset($_POST['nb_norm_plus'])) {
				if (isset($t_norm)) {
					$sql = "UPDATE user_as_carte SET quantite = quantite + 1 WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='0'";
				}
				else {
					$sql = "INSERT INTO user_as_carte (id_user, id_carte, quantite, foil) VALUES ('$id_user', '$id_carte', 1, 0)";
				}
				$mysqli->query($sql);
				
				$nb_nf++;
			}
			
			// Nombre foil
			$sql_nb_f = "SELECT quantite FROM user_as_carte WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='1'";
			$res_nb_f = $mysqli->query($sql_nb_f);
			if($t_foil = $res_nb_f->fetch_assoc()) {
				$nb_f = $t_foil['quantite'];
			}
			else {
				$nb_f = 0;
			}
			
			if (isset($_POST['nb_foil_plus'])) {
				if (isset($t_foil)) {
					$sql = "UPDATE user_as_carte SET quantite = quantite + 1 WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='1'";
				}
				else {
					$sql = "INSERT INTO user_as_carte (id_user, id_carte, quantite, foil) VALUES ('$id_user', '$id_carte', 1, 1)";
				}
				$mysqli->query($sql);
				
				$nb_f++;
			}
	
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
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

  </head>
  <body>
  
<header data-bs-theme="dark">
  <div class="navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a href="collection.php<?php echo "?collection=$chapitre"; ?>" class="navbar-brand d-flex align-items-center">
        <img src="./img/logo.png" alt="" width="52" height="72"><strong><?php echo $nom_chapitre; ?></strong>
      </a>
    </div>
  </div>
</header>

<main>
	<div class="container">
		<div class='row'>
			<div class='col-6 py-3 px-3'>
				<?php echo "<img src='cartes/FR/Chapitre ".$chapitre."/".$num_carte.".jpg' width='100%' height='100%' class='img-fluid' />"; ?>
			</div>
			<div class='col-6 py-3 px-3'>
				<?php
				
				echo "<table>";
				echo "	<tr>";
				echo "		<td><b>".$nom_carte_princ."</b>&nbsp;<img src='img/encre".$encre.".png' width='20' height='20' title='encre' class='img-fluid' />&nbsp;<img src='img/rarete_".$rarete.".png' width='20' height='20' class='img-fluid' title='rareté' /></td>";
				echo "	</tr>";
				echo "	<tr>";
				echo "		<td><i>".$nom_carte_sec."</i></td>";
				echo "	</tr>";
				echo "	</tr>";
				echo "</table>";
				
				?>
			</div>
		</div>
		<?php
		echo "<form method='post' action='detail_carte.php?id_carte=".$id_carte."'>";
		echo "	<div class='row px-2'>";
		echo "		<div class='col-4'><b>Normale :</b></div>";
		echo "		<div class='col-2'><input type='submit' class='form-control' value='-' name='nb_norm_moins'></div>";
		echo "		<div class='col-2' style='text-align:center;'>".$nb_nf."</div>";
		echo "		<div class='col-2'><input type='submit' class='form-control' value='+' name='nb_norm_plus'></div>";
		echo "	</div>";
		if ($rarete != 6) {
			echo "	<div class='row px-2'>";
			echo "		<div class='col-4'><b>Foil :</b></div>";
			echo "		<div class='col-2'><input type='submit' class='form-control' value='-' name='nb_foil_moins'></div>";
			echo "		<div class='col-2' style='text-align:center;'>".$nb_f."</div>";
			echo "		<div class='col-2'><input type='submit' class='form-control' value='+' name='nb_foil_plus'></div>";
			echo "	</div>";
			echo "</form>";
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
			echo "<center><font color='red'>Pas bien !!!</font></center>";
		}
	}
	else {
		header("location:collection.php");
	}
}
else {
	header("location:login.php");
}
?>