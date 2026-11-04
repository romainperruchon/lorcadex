<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"]) && isset($_GET['artiste'])) {
	
	$id_user = $_SESSION["id_user"];
	$artiste = $_GET['artiste'];
	
?>
<!doctype html>
<html lang="fr" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lorcadex - Collection par artiste</title>

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
		<h1>Cartes de l'artiste : <?php echo htmlspecialchars($artiste); ?></h1>
		<a href="liste_collection_artiste.php" class="btn btn-secondary">Retour aux artistes</a>
	  </div>
	  
	  <?php
	  // Get all cards from this artist (regardless of user ownership)
	  $sql = "SELECT ca.* 
	          FROM cartes AS ca 
	          WHERE ca.artiste = '$artiste'
	          ORDER BY ca.chapitre, ca.numero_carte";
	  
	  $res_cards = $mysqli->query($sql);
	  
	  if ($res_cards->num_rows > 0) {
		echo "<div class='row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3'>";
		
		while ($card = $res_cards->fetch_assoc()) {
			$id_carte = $card['id_carte'];
			$num_carte = $card['numero_carte'];
			$nom_carte = $card['nom_carte'];
			$rarete = $card['rarete'];
			$encre = $card['encre'];
			$image_url = $card['image'];
			
			// Get the chapter name for this card
			$sql_chapter = "SELECT nom_chapitre FROM chapitres WHERE id_chapitre = " . $card['chapitre'];
			$res_chapter = $mysqli->query($sql_chapter);
			$chapter_data = $res_chapter->fetch_assoc();
			$chapter_name = $chapter_data['nom_chapitre'];
			$chapter_folder = "Chapitre ".$card['chapitre'];
			
			// Construct local image path
			$local_image_path = 'cartes/FR/'.$chapter_folder.'/'.$num_carte.'.jpg';
			
			// Check if the local image exists, otherwise use the URL from database
			if (file_exists($local_image_path)) {
				$img_src = $local_image_path;
			} else {
				$img_src = $image_url;
			}
			
			// Check if user owns this card
			$quantite = 0;
			$foil = 0;
			
			$sql_user_card = "SELECT quantite, foil FROM user_as_carte WHERE id_user = '$id_user' AND id_carte = '$id_carte'";
			$res_user_card = $mysqli->query($sql_user_card);
			
			if ($res_user_card->num_rows > 0) {
				$user_card = $res_user_card->fetch_assoc();
				$quantite = $user_card['quantite'];
				$foil = $user_card['foil'];
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
		
		echo "</div>";
	  } else {
		echo "<p>Aucune carte trouvée pour cet artiste.</p>";
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