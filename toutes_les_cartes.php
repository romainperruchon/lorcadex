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
    <title>Lorcadex - Toutes les cartes</title>

    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<style>
      .card-img-top {
        height: 200px;
        object-fit: cover;
      }
      
      .card {
        transition: transform 0.2s;
      }
      
      .card:hover {
        transform: translateY(-5px);
      }
      
      .collection-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
      }
    </style>
  </head>
  <body>	
	<main class="container py-4">
	  <div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Toutes les cartes de votre collection</h1>
		<a href="dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a>
	  </div>
	  
	  <?php
	  // Get all cards from user's collection, grouped by chapter
	  $sql = "SELECT DISTINCT(c.id_chapitre), c.nom_chapitre, c.sigle 
	          FROM cartes AS ca 
	          JOIN chapitres AS c ON ca.chapitre = c.id_chapitre 
	          JOIN user_as_carte AS uac ON ca.id_carte = uac.id_carte
	          WHERE uac.id_user = '$id_user'
	          ORDER BY c.id DESC";
	  
	  $res_chapters = $mysqli->query($sql);
	  
	  if ($res_chapters->num_rows > 0) {
		while ($chapter = $res_chapters->fetch_assoc()) {
			$chapter_id = $chapter['id_chapitre'];
			$chapter_name = $chapter['nom_chapitre'];
			$chapter_sigle = $chapter['sigle'];
			
			echo "<h2>" . htmlspecialchars($chapter_name) . " (" . htmlspecialchars($chapter_sigle) . ")</h2>";
			
			// Get all cards from this chapter for the user
			$sql_cards = "SELECT ca.*, uac.quantite, uac.foil 
						  FROM cartes AS ca 
						  JOIN user_as_carte AS uac ON ca.id_carte = uac.id_carte
						  WHERE uac.id_user = '$id_user' AND ca.chapitre = '$chapter_id'
						  ORDER BY ca.numero_carte";
			
			$res_cards = $mysqli->query($sql_cards);
			
			if ($res_cards->num_rows > 0) {
				echo "<div class='row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3'>";
				
				while ($card = $res_cards->fetch_assoc()) {
					$id_carte = $card['id_carte'];
					$num_carte = $card['numero_carte'];
					$nom_carte = $card['nom_carte'];
					$rarete = $card['rarete'];
					$encre = $card['encre'];
					$artiste = $card['artiste'];
					$image_url = $card['image'];
					$quantite = $card['quantite'];
					$foil = $card['foil'];
					
					// Construct local image path
					$local_image_path = 'cartes/FR/'.$chapter_name.'/'.$num_carte.'.jpg';
					
					// Check if the local image exists, otherwise use the URL from database
					if (file_exists($local_image_path)) {
						$img_src = $local_image_path;
					} else {
						$img_src = $image_url;
					}
					
					echo "<div class='col'>";
					echo "<div class='card h-100'>";
					echo "<div class='position-relative'>";
					echo "<img src='" . htmlspecialchars($img_src) . "' class='card-img-top' alt='" . htmlspecialchars($nom_carte) . "'>";
					if ($quantite > 0) {
						echo "<span class='collection-badge'>" . $quantite . "</span>";
					}
					echo "</div>";
					echo "<div class='card-body'>";
					echo "<h6 class='card-title text-center'>" . htmlspecialchars($nom_carte) . "</h6>";
					if ($foil == 1) {
						echo "<p class='text-center'><small class='text-muted'>Foil</small></p>";
					}
					echo "<div class='d-grid gap-2'>";
					echo "<a href='detail_carte.php?id_carte=" . $id_carte . "' class='btn btn-sm btn-outline-primary'>Détails</a>";
					echo "</div>";
					echo "</div>";
					echo "</div>";
					echo "</div>";
				}
				
				echo "</div><hr>";
			} else {
				echo "<p>Aucune carte trouvée dans ce chapitre.</p><hr>";
			}
		}
	  } else {
		echo "<p>Vous n'avez aucune carte dans votre collection.</p>";
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