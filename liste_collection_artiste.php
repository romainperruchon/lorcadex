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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lorcadex - Collection par artiste</title>

    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<style>
      .artist-card {
        transition: transform 0.2s;
        cursor: pointer;
      }
      
      .artist-card:hover {
        transform: scale(1.02);
      }
      
      .artist-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
      }
    </style>
  </head>
  <body>	
	<main class="container py-4">
	  <div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Collection par artiste 
		<?php
		// Get the count of distinct artists
		$count_sql = "SELECT COUNT(DISTINCT(c.artiste)) as artist_count
		              FROM cartes AS c 
		              JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
		              WHERE uac.id_user = ? AND c.artiste IS NOT NULL AND c.artiste != ''";
		
		$count_stmt = $mysqli->prepare($count_sql);
		$count_stmt->bind_param("i", $id_user);
		$count_stmt->execute();
		$count_result = $count_stmt->get_result();
		$count_data = $count_result->fetch_assoc();
		$artist_count = $count_data['artist_count'];
		
		echo "(" . $artist_count . " artiste" . ($artist_count > 1 ? "s" : "") . ")";
		?>
		</h1>
		<a href="dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a>
	  </div>
	  
	  <?php
	  // Get all artists from user's collection
	  $sql = "SELECT DISTINCT(c.artiste) 
	          FROM cartes AS c 
	          JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
	          WHERE uac.id_user = ? AND c.artiste IS NOT NULL AND c.artiste != ''
	          ORDER BY c.artiste";
	  
	  $stmt = $mysqli->prepare($sql);
	  $stmt->bind_param("i", $id_user);
	  $stmt->execute();
	  $res_artists = $stmt->get_result();
	  
	  if ($res_artists->num_rows > 0) {
		echo "<div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>";
		
		while ($artist = $res_artists->fetch_assoc()) {
			$artist_name = $artist['artiste'];
			
			// Count total cards for this artist (in the entire database)
			$sql_total_artist = "SELECT COUNT(*) as total_artist_cards 
						  FROM cartes AS c 
						  WHERE c.artiste = ?";
			
			$stmt_total = $mysqli->prepare($sql_total_artist);
			$stmt_total->bind_param("s", $artist_name);
			$stmt_total->execute();
			$res_total_artist = $stmt_total->get_result();
			$total_artist_data = $res_total_artist->fetch_assoc();
			$total_artist_cards = $total_artist_data['total_artist_cards'];
			
			// Count total cards for this artist in user's collection
			$sql_count = "SELECT COUNT(*) as total_cards 
						  FROM cartes AS c 
						  JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
						  WHERE uac.id_user = ? AND c.artiste = ?";
			
			$stmt_count = $mysqli->prepare($sql_count);
			$stmt_count->bind_param("is", $id_user, $artist_name);
			$stmt_count->execute();
			$res_count = $stmt_count->get_result();
			$count_data = $res_count->fetch_assoc();
			$total_cards = $count_data['total_cards'];
			
			// Count foil cards for this artist in user's collection
			$sql_foil = "SELECT COUNT(*) as foil_cards 
						 FROM cartes AS c 
						 JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
						 WHERE uac.id_user = ? AND c.artiste = ? AND uac.foil = 1";
			
			$stmt_foil = $mysqli->prepare($sql_foil);
			$stmt_foil->bind_param("is", $id_user, $artist_name);
			$stmt_foil->execute();
			$res_foil = $stmt_foil->get_result();
			$foil_data = $res_foil->fetch_assoc();
			$foil_cards = $foil_data['foil_cards'];
			
			echo "<div class='col'>";
			echo "<a href='collection_artiste.php?artiste=" . urlencode($artist_name) . "' class='text-decoration-none'>";
			echo "<div class='card artist-card h-100 border-info'>";
			echo "<div class='card-body text-center'>";
			echo "<div class='artist-icon text-info'>";
			echo "<span class='material-icons'>person</span>";
			echo "</div>";
			echo "<h5 class='card-title'>" . htmlspecialchars($artist_name) . "</h5>";
			echo "<p class='card-text'>Possédées : " . $total_cards . " cartes (Dont " . $foil_cards . " foil)</p>";
			echo "<p class='card-text'>Total : " . $total_artist_cards . " cartes</p>";
			echo "</div>";
			echo "</div>";
			echo "</a>";
			echo "</div>";
		}
		
		echo "</div>";
	  } else {
		echo "<p>Aucun artiste trouvé dans votre collection.</p>";
	  }
	  ?>
	</main>
  </body>
</html>
<?php 
}
else {
	header("location:login.php");
}
?>