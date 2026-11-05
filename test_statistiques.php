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
	
?>
<!doctype html>
<html lang="fr" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lorcadex - Statistiques</title>
    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
			  <h2 class="text-success">14</h2>
			  <p class="card-text">Chapitres collectés</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-warning bg-light">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-warning">35</h2>
			  <p class="card-text">Artistes différents</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-info bg-light">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-info">5</h2>
			  <p class="card-text">Types de cartes</p>
			</div>
		  </div>
		</div>
	  </div>
	  
	  <!-- Graphiques -->
	  <div class="row mb-4">
		<div class="col-lg-6 mb-4">
		  <div class="card stat-card h-100">
			<div class="card-header bg-primary text-white">Répartition par chapitre</div>
			<div class="card-body">
			  <canvas id="chapitreChart" height="300"></canvas>
			</div>
		  </div>
		</div>
		<div class="col-lg-6 mb-4">
		  <div class="card stat-card h-100">
			<div class="card-header bg-success text-white">Répartition par artiste</div>
			<div class="card-body">
			  <canvas id="artisteChart" height="300"></canvas>
			</div>
		  </div>
		</div>
	  </div>
	  
	  <div class="d-flex justify-content-center mt-4">
		<a href="dashboard.php" class="btn btn-primary">Retour au tableau de bord</a>
	  </div>
	</main>
	
	<script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
	
	<script>
	// Graphique chapitre
	const ctx1 = document.getElementById('chapitreChart').getContext('2d');
	new Chart(ctx1, {
		type: 'bar',
		data: {
			labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
			datasets: [{
				label: 'Nombre de cartes',
				data: [15, 22, 18, 25, 12, 30, 28, 20, 17, 23, 19, 26, 21, 14],
				backgroundColor: 'rgba(255, 99, 132, 0.2)',
				borderColor: 'rgba(255, 99, 132, 1)',
				borderWidth: 1
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						stepSize: 1
					}
				}
			}
		}
	});
	
	// Graphique artiste
	const ctx2 = document.getElementById('artisteChart').getContext('2d');
	new Chart(ctx2, {
		type: 'pie',
		data: {
			labels: ['Artiste A', 'Artiste B', 'Artiste C', 'Artiste D', 'Artiste E'],
			datasets: [{
				data: [35, 25, 20, 15, 5],
				backgroundColor: [
					'rgba(255, 99, 132, 0.8)',
					'rgba(54, 162, 235, 0.8)',
					'rgba(255, 205, 86, 0.8)',
					'rgba(75, 192, 192, 0.8)',
					'rgba(153, 102, 255, 0.8)'
				]
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false
		}
	});
	</script>
  </body>
</html>
<?php 
}
else {
	header("location:login.php");
}
?>