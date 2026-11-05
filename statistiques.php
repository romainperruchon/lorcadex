<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"])) {
	
	$id_user = $_SESSION["id_user"];
	
	// Récupération des statistiques globales
	// Nombre total de cartes
	$sql_total = "SELECT COUNT(*) as total FROM user_as_carte WHERE id_user = '$id_user'";
	$result_total = $mysqli->query($sql_total);
	$total_cartes = $result_total->fetch_assoc()['total'];
	
	// Nombre de chapitres collectés
	$sql_chapitres = "SELECT DISTINCT(c.chapitre) as chapitre_id FROM user_as_carte uac JOIN cartes c ON uac.id_carte = c.id_carte WHERE uac.id_user = '$id_user' ORDER BY c.chapitre";
	$result_chapitres = $mysqli->query($sql_chapitres);
	$chapitres_collectes = $result_chapitres->num_rows;
	
	// Nombre d'artistes différents
	$sql_artistes = "SELECT DISTINCT(c.artiste) as artiste FROM user_as_carte uac JOIN cartes c ON uac.id_carte = c.id_carte WHERE uac.id_user = '$id_user' AND c.artiste IS NOT NULL AND c.artiste != ''";
	$result_artistes = $mysqli->query($sql_artistes);
	$artistes_diff = $result_artistes->num_rows;
	
	// Nombre de types de cartes
	$sql_types = "SELECT DISTINCT(c.type_carte) as type FROM user_as_carte uac JOIN cartes c ON uac.id_carte = c.id_carte WHERE uac.id_user = '$id_user' AND c.type_carte IS NOT NULL AND c.type_carte != ''";
	$result_types = $mysqli->query($sql_types);
	$types_cartes = $result_types->num_rows;
	
?>
<!doctype html>
<html lang="fr" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lorcadex - Statistiques</title>
    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .stat-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,.125);
      }
      
      .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,.125);
      }
    </style>
  </head>
  <body>	
	<main class="container py-5">
	  <div class="text-center mb-5">
		<h1>Statistiques de votre collection</h1>
		<p class="lead">Vos données et analyses sur vos cartes Lorca</p>
	  </div>
	  
	  <!-- Statistiques globales -->
	  <div class="row mb-4">
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-primary bg-light">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-primary"><?php echo $total_cartes; ?></h2>
			  <p class="card-text">Cartes totales</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-success bg-light">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-success"><?php echo $chapitres_collectes; ?></h2>
			  <p class="card-text">Chapitres collectés</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-warning bg-light">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-warning"><?php echo $artistes_diff; ?></h2>
			  <p class="card-text">Artistes différents</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-info bg-light">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-info"><?php echo $types_cartes; ?></h2>
			  <p class="card-text">Types de cartes</p>
			</div>
		  </div>
		</div>
	  </div>
	  
	  <div class="d-flex justify-content-center mt-4">
		<a href="dashboard.php" class="btn btn-primary">Retour au tableau de bord</a>
	  </div>
	</main>
	
	<script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
<?php 
}
else {
	header("location:login.php");
}
?>