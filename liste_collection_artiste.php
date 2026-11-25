<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"])) {
	
	$id_user = $_SESSION["id_user"];
	
	// Une seule requête pour tous les artistes (au lieu de 4 par artiste) : cartes possédées dont foil,
	// carte affichée (la plus ancienne possédée) et total de cartes de l'artiste dans toute la base
	// Raretés 1 à 5 (commune à légendaire) : 2 versions (foil + non foil) ; autres raretés : 1 seule version
	$sql = "SELECT a.artiste, d.image, d.nom_carte, a.total_cards, a.foil_cards, a.owned_versions, t.total_artist_cards, t.total_versions
	        FROM (
	            SELECT c.artiste AS artiste,
	                   MIN(c.id_carte) AS id_carte,
	                   COUNT(*) AS total_cards,
	                   COUNT(CASE WHEN uac.foil = 1 THEN 1 END) AS foil_cards,
	                   COUNT(DISTINCT CASE WHEN uac.quantite > 0
	                       THEN (CASE WHEN c.rarete BETWEEN 1 AND 5 THEN CONCAT(c.id_carte, '-', uac.foil) ELSE CAST(c.id_carte AS CHAR) END)
	                   END) AS owned_versions
	            FROM cartes AS c
	            JOIN user_as_carte AS uac ON c.id_carte = uac.id_carte
	            WHERE uac.id_user = ? AND c.artiste IS NOT NULL AND c.artiste != ''
	            GROUP BY c.artiste
	        ) AS a
	        JOIN cartes AS d ON d.id_carte = a.id_carte
	        JOIN (
	            SELECT artiste, COUNT(*) AS total_artist_cards,
	                   SUM(CASE WHEN rarete BETWEEN 1 AND 5 THEN 2 ELSE 1 END) AS total_versions
	            FROM cartes
	            WHERE artiste IS NOT NULL AND artiste != ''
	            GROUP BY artiste
	        ) AS t ON t.artiste = a.artiste
	        ORDER BY a.artiste";
	
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("i", $id_user);
	$stmt->execute();
	$res_artists = $stmt->get_result();
	$artist_count = $res_artists->num_rows;
	
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
      
      .artist-card-img {
        height: 150px;
        object-fit: cover;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
      }
    </style>
  </head>
  <body>	
	<main class="container py-4">
	  <div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Collection par artiste 
		<?php
		echo "(" . $artist_count . " artiste" . ($artist_count > 1 ? "s" : "") . ")";
		?>
		</h1>
		<a href="dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a>
	  </div>
	  
	  <?php
	  if ($artist_count > 0) {
		echo "<div class='row g-3 mb-4'>";
		echo "<div class='col-12 col-md-6 col-lg-4'>";
		echo "<label for='rechercheArtiste' class='visually-hidden'>Rechercher un artiste</label>";
		echo "<input type='search' id='rechercheArtiste' class='form-control' placeholder='Rechercher un artiste...' autocomplete='off'>";
		echo "</div>";
		echo "<div class='col-12 col-md-6 col-lg-4'>";
		echo "<div class='input-group'>";
		echo "<label for='triArtistes' class='input-group-text'>Trier par</label>";
		echo "<select id='triArtistes' class='form-select'>";
		echo "<option value='nom-asc'>Nom (A → Z)</option>";
		echo "<option value='nom-desc'>Nom (Z → A)</option>";
		echo "<option value='total-asc'>Total (croissant)</option>";
		echo "<option value='total-desc'>Total (décroissant)</option>";
		echo "</select>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
		
		echo "<div id='listeArtistes' class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>";
		
		while ($artist = $res_artists->fetch_assoc()) {
			$artist_name = $artist['artiste'];
			$total_artist_cards = $artist['total_artist_cards'];
			$total_cards = $artist['total_cards'];
			$foil_cards = $artist['foil_cards'];
			$pourcentage = $artist['total_versions'] > 0 ? min(100, round($artist['owned_versions'] / $artist['total_versions'] * 100, 1)) : 0;
			$card_image = (string) $artist['image']; // image NULL en base : évite le "Deprecated" de htmlspecialchars() en PHP 8.1+
			$card_title = $artist['nom_carte'];
			
			echo "<div class='col' data-artiste='" . htmlspecialchars($artist_name, ENT_QUOTES) . "' data-total='" . (int) $total_artist_cards . "'>";
			echo "<a href='collection_artiste.php?artiste=" . urlencode($artist_name) . "' class='text-decoration-none'>";
			echo "<div class='card artist-card h-100 border-info'>";
			echo "<img src='" . htmlspecialchars($card_image) . "' class='artist-card-img' loading='lazy' alt='" . htmlspecialchars($card_title) . "'>";
			echo "<div class='card-body text-center'>";
			echo "<h5 class='card-title'>" . htmlspecialchars($artist_name) . "</h5>";
			echo "<p class='card-text'>Possédées : " . $total_cards . " cartes (Dont " . $foil_cards . " foil)</p>";
			echo "<p class='card-text'>Total : " . $total_artist_cards . " cartes</p>";
						echo "<div class='progress' role='progressbar' aria-valuenow='" . $pourcentage . "' aria-valuemin='0' aria-valuemax='100'>";
						echo "<div class='progress-bar' style='width: " . $pourcentage . "%'>" . $pourcentage . " %</div>";
						echo "</div>";
						echo "</div>";
			echo "</div>";
			echo "</a>";
			echo "</div>";
		}
		
		echo "</div>";
		echo "<p id='aucunResultat' class='d-none mt-3'>Aucun artiste ne correspond à votre recherche.</p>";
	  } else {
		echo "<p>Aucun artiste trouvé dans votre collection.</p>";
	  }
	  ?>
	</main>
    <script>
      const champRecherche = document.getElementById('rechercheArtiste');

      if (champRecherche) {
        // Insensible à la casse et aux accents : "cesar" trouve "César"
        const normaliser = (texte) => texte.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

        const filtrerArtistes = () => {
          const recherche = normaliser(champRecherche.value.trim());
          let nbVisibles = 0;

          document.querySelectorAll('[data-artiste]').forEach((colonne) => {
            const visible = normaliser(colonne.dataset.artiste).includes(recherche);
            colonne.classList.toggle('d-none', !visible);
            if (visible) {
              nbVisibles++;
            }
          });

          document.getElementById('aucunResultat').classList.toggle('d-none', nbVisibles > 0);
        };

        champRecherche.addEventListener('input', filtrerArtistes);
        filtrerArtistes();
      }

      const selectTri = document.getElementById('triArtistes');

      if (selectTri) {
        const grille = document.getElementById('listeArtistes');
        const colonnes = Array.from(grille.querySelectorAll('[data-artiste]'));
        // Le SQL renvoie déjà les artistes triés par nom : cet ordre sert de référence pour "Nom" et pour départager les égalités
        const rang = new Map(colonnes.map((colonne, index) => [colonne, index]));

        const trierArtistes = () => {
          const [critere, sens] = selectTri.value.split('-');
          const direction = sens === 'desc' ? -1 : 1;
          const valeur = (colonne) => (critere === 'total' ? Number(colonne.dataset.total) : rang.get(colonne));

          const tries = [...colonnes].sort((a, b) => direction * (valeur(a) - valeur(b)) || rang.get(a) - rang.get(b));
          grille.append(...tries);
        };

        selectTri.addEventListener('change', trierArtistes);
        trierArtistes();
      }
    </script>
  </body>
</html>
<?php 
}
else {
	header("location:login.php");
}
?>
