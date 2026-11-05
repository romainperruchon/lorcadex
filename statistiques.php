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
	
	// Récupération des données pour les graphiques détaillés
	// Répartition par chapitre
	$sql_chapitre_details = "SELECT c.chapitre, COUNT(*) as count FROM user_as_carte uac JOIN cartes c ON uac.id_carte = c.id_carte WHERE uac.id_user = '$id_user' GROUP BY c.chapitre ORDER BY c.chapitre";
	$result_chapitre_details = $mysqli->query($sql_chapitre_details);
	$chapitre_data = [];
	while ($row = $result_chapitre_details->fetch_assoc()) {
		$chapitre_data[] = $row;
	}
	
	// Répartition par artiste
	$sql_artiste_details = "SELECT c.artiste, COUNT(*) as count FROM user_as_carte uac JOIN cartes c ON uac.id_carte = c.id_carte WHERE uac.id_user = '$id_user' AND c.artiste IS NOT NULL AND c.artiste != '' GROUP BY c.artiste ORDER BY count DESC";
	$result_artiste_details = $mysqli->query($sql_artiste_details);
	$artiste_data = [];
	while ($row = $result_artiste_details->fetch_assoc()) {
		$artiste_data[] = $row;
	}
	
	// Répartition par type de carte
	$sql_type_details = "SELECT c.type_carte, COUNT(*) as count FROM user_as_carte uac JOIN cartes c ON uac.id_carte = c.id_carte WHERE uac.id_user = '$id_user' AND c.type_carte IS NOT NULL AND c.type_carte != '' GROUP BY c.type_carte ORDER BY count DESC";
	$result_type_details = $mysqli->query($sql_type_details);
	$type_data = [];
	while ($row = $result_type_details->fetch_assoc()) {
		$type_data[] = $row;
	}
	
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
        cursor: pointer;
      }
      
      .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,.125);
      }
      
      .chart-container {
        position: relative;
        height: 400px;
        margin-top: 20px;
      }
      
      .table-responsive {
        margin-top: 20px;
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
		  <div class="card stat-card h-100 text-center border-primary bg-light" data-target="total-cartes">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-primary"><?php echo $total_cartes; ?></h2>
			  <p class="card-text">Cartes totales</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-success bg-light" data-target="chapitres-collectes">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-success"><?php echo $chapitres_collectes; ?></h2>
			  <p class="card-text">Chapitres collectés</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-warning bg-light" data-target="artistes-diff">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-warning"><?php echo $artistes_diff; ?></h2>
			  <p class="card-text">Artistes différents</p>
			</div>
		  </div>
		</div>
		<div class="col-md-3 col-sm-6 mb-3">
		  <div class="card stat-card h-100 text-center border-info bg-light" data-target="types-cartes">
			<div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
			  <h2 class="text-info"><?php echo $types_cartes; ?></h2>
			  <p class="card-text">Types de cartes</p>
			</div>
		  </div>
		</div>
	  </div>
	  
	  <!-- Contenu dynamique pour les détails -->
	  <div id="chart-content" class="mt-4"></div>
	  
	  <div class="d-flex justify-content-center mt-4">
		<a href="dashboard.php" class="btn btn-primary">Retour au tableau de bord</a>
	  </div>
	</main>
	
	<script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// Fonction pour créer un graphique
		function createChart(ctx, data, labels, title, color) {
			return new Chart(ctx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: [{
						label: title,
						data: data,
						backgroundColor: color,
						borderColor: color.replace('0.2', '1'),
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
		}

		// Fonction pour créer une table HTML
		function createTable(data, columns) {
			let table = '<table class="table table-striped table-bordered">';
			table += '<thead><tr>';
			columns.forEach(col => {
				table += `<th>${col}</th>`;
			});
			table += '</tr></thead><tbody>';
			
			data.forEach(row => {
				table += '<tr>';
				columns.forEach(col => {
					table += `<td>${row[col]}</td>`;
				});
				table += '</tr>';
			});
			
			table += '</tbody></table>';
			return table;
		}

		// Fonction pour afficher les détails
		function showDetails(target) {
			const contentDiv = document.getElementById('chart-content');
			
			switch(target) {
				case 'total-cartes':
					contentDiv.innerHTML = `
						<div class="card">
							<div class="card-header">
								<h4>Répartition par chapitre</h4>
							</div>
							<div class="card-body chart-container">
								<canvas id="chapitreChart"></canvas>
							</div>
						</div>`;
					
					const ctx1 = document.getElementById('chapitreChart').getContext('2d');
					const chapitreLabels = <?php echo json_encode(array_column($chapitre_data, 'chapitre')); ?>;
					const chapitreData = <?php echo json_encode(array_column($chapitre_data, 'count')); ?>;
					
					createChart(ctx1, chapitreData, chapitreLabels, 'Nombre de cartes', 'rgba(255, 99, 132, 0.2)');
					break;
					
				case 'chapitres-collectes':
					contentDiv.innerHTML = `
						<div class="card">
							<div class="card-header">
								<h4>Chapitres collectés</h4>
							</div>
							<div class="card-body table-responsive">
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Chapitre</th>
											<th>Nombre de cartes</th>
										</tr>
									</thead>
									<tbody>` +
									<?php
									$tbody = '';
									foreach ($chapitre_data as $row) {
										$tbody .= "<tr><td>{$row['chapitre']}</td><td>{$row['count']}</td></tr>";
									}
									echo json_encode($tbody);
									?> + 
									'</tbody></table>' +
								'</div></div>';
					break;
					
				case 'artistes-diff':
					contentDiv.innerHTML = `
						<div class="card">
							<div class="card-header">
								<h4>Répartition par artiste</h4>
							</div>
							<div class="card-body chart-container">
								<canvas id="artisteChart"></canvas>
							</div>
						</div>`;
					
					const ctx2 = document.getElementById('artisteChart').getContext('2d');
					const artisteLabels = <?php echo json_encode(array_column($artiste_data, 'artiste')); ?>;
					const artisteData = <?php echo json_encode(array_column($artiste_data, 'count')); ?>;
					
					createChart(ctx2, artisteData, artisteLabels, 'Nombre de cartes', 'rgba(54, 162, 235, 0.2)');
					break;
					
				case 'types-cartes':
					contentDiv.innerHTML = `
						<div class="card">
							<div class="card-header">
								<h4>Répartition par type de carte</h4>
							</div>
							<div class="card-body chart-container">
								<canvas id="typeChart"></canvas>
							</div>
						</div>`;
					
					const ctx3 = document.getElementById('typeChart').getContext('2d');
					const typeLabels = <?php echo json_encode(array_column($type_data, 'type_carte')); ?>;
					const typeData = <?php echo json_encode(array_column($type_data, 'count')); ?>;
					
					createChart(ctx3, typeData, typeLabels, 'Nombre de cartes', 'rgba(255, 205, 86, 0.2)');
					break;
					
				default:
					contentDiv.innerHTML = '';
			}
		}

		// Ajouter les événements clic aux cartes
		document.addEventListener('DOMContentLoaded', function() {
			const statCards = document.querySelectorAll('.stat-card');
			
			statCards.forEach(card => {
				card.addEventListener('click', function() {
					const target = this.getAttribute('data-target');
					showDetails(target);
				});
			});
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