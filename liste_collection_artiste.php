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
		<h1>Collection par artiste</h1>
		<a href="dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a>
	  </div>
	  
	  <?php
	  // Get all artists from user's collection
	  $sql = "SELECT DISTINCT(c.artiste) 
	          FROM cartes AS c 
	          JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
	          WHERE uac.id_user = '$id_user' AND c.artiste IS NOT NULL AND c.artiste != ''
	          ORDER BY c.artiste";
	  
	  $res_artists = $mysqli->query($sql);
	  
	  if ($res_artists->num_rows > 0) {
		echo "<div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>";
		
		while ($artist = $res_artists->fetch_assoc()) {
			$artist_name = $artist['artiste'];
			
			// Count total cards for this artist
			$sql_count = "SELECT COUNT(*) as total_cards 
						  FROM cartes AS c 
						  JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
						  WHERE uac.id_user = '$id_user' AND c.artiste = '$artist_name'";
			
			$res_count = $mysqli->query($sql_count);
			$count_data = $res_count->fetch_assoc();
			$total_cards = $count_data['total_cards'];
			
			// Count foil cards for this artist
			$sql_foil = "SELECT COUNT(*) as foil_cards 
						 FROM cartes AS c 
						 JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
						 WHERE uac.id_user = '$id_user' AND c.artiste = '$artist_name' AND uac.foil = 1";
			
			$res_foil = $mysqli->query($sql_foil);
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
			echo "<p class='card-text'>" . $total_cards . " cartes (" . $foil_cards . " foil)</p>";
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