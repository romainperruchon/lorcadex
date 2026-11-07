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
			$description = $t_carte["description"];
			
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
<html lang="fr" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Détail de la carte">
    <meta name="author" content="Romain PERRUCHON">
    <title>Lorcadex - Détail de la carte</title>

    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        .card-image {
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .rarete-badge {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
        }
        .encre-icon {
            width: 24px;
            height: 24px;
        }
        .quantity-control {
            min-width: 80px;
        }
        .card-detail-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
    </style>
  </head>
  <body>
  
<header data-bs-theme="dark">
  <div class="navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a href="collection.php<?php echo "?collection=$chapitre"; ?>" class="navbar-brand d-flex align-items-center">
        <img src="./img/logo.png" alt="" width="52" height="72"><strong><?php echo htmlspecialchars($nom_chapitre); ?></strong>
      </a>
    </div>
  </div>
</header>

<main class="py-4">
	<div class="container card-detail-container">
		<div class="row justify-content-center mb-4">
			<div class="col-md-10 text-center">
				<h1 class="display-6 fw-bold text-primary"><?php echo htmlspecialchars($nom_carte_princ); ?></h1>
				<?php if (!empty($nom_carte_sec)): ?>
					<h2 class="display-7 fw-bold text-secondary"><?php echo htmlspecialchars($nom_carte_sec); ?></h2>
				<?php endif; ?>
			</div>
		</div>

		<div class="row g-4">
			<!-- Image de la carte -->
			<div class="col-lg-6">
				<div class="card stats-card h-100">
					<div class="card-body text-center p-4">
						<?php 
						// Vérification si l'image locale existe
						$image_path = "img/cartes/" . $num_carte . ".jpg";
						if (file_exists($image_path)) {
							echo '<img src="' . $image_path . '" class="card-image img-fluid" alt="Image de la carte ' . htmlspecialchars($nom_carte_princ) . '">';
						} else {
							// Si l'image locale n'existe pas, afficher l'image depuis l'URL
							$image_url = $t_carte["image"];
							if (!empty($image_url)) {
								echo '<img src="' . htmlspecialchars($image_url) . '" class="card-image img-fluid" alt="Image de la carte ' . htmlspecialchars($nom_carte_princ) . '">';
							} else {
								// Si aucune image n'est disponible, afficher un placeholder
								echo '<img src="https://via.placeholder.com/300" class="card-image img-fluid" alt="Image de la carte ' . htmlspecialchars($nom_carte_princ) . '">';
							}
						}
						?>
					</div>
				</div>
			</div>

			<!-- Informations sur la carte -->
			<div class="col-lg-6">
				<div class="card stats-card h-100">
					<div class="card-body">
						<h3 class="card-title">Informations</h3>
						
						<div class="row mb-3">
							<div class="col-3 fw-bold">Chapitre:</div>
							<div class="col-3"><?php echo htmlspecialchars($nom_chapitre); ?></div>
							<div class="col-3 fw-bold">Numéro:</div>
							<div class="col-3"><?php echo htmlspecialchars($num_carte); ?></div>
						</div>
						
						<div class="row mb-3">
							<div class="col-4 fw-bold">Catégorie:</div>
							<div class="col-8"><?php echo htmlspecialchars($cat_carte); ?></div>
						</div>
						
						<div class="row mb-3">
							<div class="col-4 fw-bold">Rareté:</div>
							<div class="col-8">
								<?php
								// Définir les libellés de rareté et leurs images par numéro
								$rarete_labels = [
									1 => ["libelle" => "Commune", "image" => "rarete_1.png"],
									2 => ["libelle" => "Unco", "image" => "rarete_2.png"],
									3 => ["libelle" => "Rare", "image" => "rarete_3.png"],
									4 => ["libelle" => "Très Rare", "image" => "rarete_4.png"],
									5 => ["libelle" => "Légendaire", "image" => "rarete_5.png"],
									6 => ["libelle" => "Enchantée", "image" => "rarete_6.png"],
									7 => ["libelle" => "Epic", "image" => "rarete_7.png"],
									8 => ["libelle" => "Iconique", "image" => "rarete_8.png"]
								];
								
								$rarete_data = isset($rarete_labels[$rarete]) ? $rarete_labels[$rarete] : $rarete_labels[1];
								$rarete_libelle = $rarete_data['libelle'];
								$rarete_image = $rarete_data['image'];
								?>
								<span class="badge rarete-badge bg-success"><img src='img/<?php echo $rarete_image; ?>' class='encre-icon' alt='<?php echo htmlspecialchars($rarete_libelle); ?>'> <?php echo htmlspecialchars($rarete_libelle); ?></span>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-4 fw-bold">Encre:</div>
							<div class="col-8">
								<img src='img/encre<?php echo $encre; ?>.png' class='encre-icon' alt='Encre <?php echo $encre; ?>'>
								<span class="ms-2">
									<?php
									// Définir les libellés d'encre
									$encre_labels = [
										1 => "Ambre",
										2 => "Améthyste", 
										3 => "Emeraude",
										4 => "Rubis",
										5 => "Saphir",
										6 => "Acier"
									];
									$encre_label = isset($encre_labels[$encre]) ? $encre_labels[$encre] : "Inconnue";
									echo htmlspecialchars($encre_label);
									?>
								</span>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-4 fw-bold">Description:</div>
							<div class="col-8"><?php 
                                // Rechercher les occurrences de tous les mots-clés et les mettre en gras
                                $description_affichee = htmlspecialchars($description);
                                
                                // Mots-clés avec nombres
                                $patterns = [
                                    '/Alter\s+(\d+)/i' => '<strong>Alter $1</strong>',
                                    '/À l\'unisson\s+(\d+)/i' => '<strong>À l\'unisson $1</strong>',
                                    '/Offensif \+\s*(\d+)/i' => '<strong>Offensif +$1</strong>',
									'/Mélomane \s*(\d+)/i' => '<strong>Mélomane $1</strong>'
                                ];
                                
                                // Mots-clés simples
                                $simple_patterns = [
									'Rempart' => '<strong>Rempart</strong>',
									'Mélomane' => '<strong>Mélomane</strong>',
                                    'Soutien' => '<strong>Soutien</strong>',
                                    'Appel de l\'aventure' => '<strong>Appel de l\'aventure</strong>',
                                    'Insaisissable' => '<strong>Insaisissable</strong>',
                                    'Charge' => '<strong>Charge</strong>',
                                    'Résistance' => '<strong>Résistance</strong>',
                                    'Agilité' => '<strong>Agilité</strong>',
                                    'Hors d\'atteinte' => '<strong>Hors d\'atteinte</strong>'
                                ];
                                
                                // Appliquer les patterns avec nombres
                                foreach ($patterns as $pattern => $replacement) {
                                    $description_affichee = preg_replace($pattern, $replacement, $description_affichee);
                                }
                                
                                // Appliquer les patterns simples
                                foreach ($simple_patterns as $keyword => $replacement) {
                                    $description_affichee = preg_replace('/' . preg_quote($keyword, '/') . '/i', $replacement, $description_affichee);
                                }
                                
                                echo nl2br($description_affichee); 
                            ?></div>
						</div>
						
						<div class="row mb-3">
							<div class="col-4 fw-bold">Artiste:</div>
							<div class="col-8"><?php echo htmlspecialchars($t_carte["artiste"]); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Contrôles de quantité -->
		<div class="row mt-4">
			<div class="col-12">
				<div class="card stats-card">
					<div class="card-body">
						<h3 class="card-title">Ma Collection</h3>
						
						<form method="post" action="">
							<div class="row align-items-center g-3">
								<div class="col-md-3">
									<label class="form-label fw-bold">Carte Normale:</label>
								</div>
								<div class="col-md-2">
									<button type="submit" name="nb_norm_moins" value="1" class="btn btn-outline-danger quantity-control">
										<i class="bi bi-dash"></i>
									</button>
								</div>
								<div class="col-md-2 text-center">
									<span class="fw-bold fs-4"><?php echo $nb_nf; ?></span>
								</div>
								<div class="col-md-2">
									<button type="submit" name="nb_norm_plus" value="1" class="btn btn-outline-success quantity-control">
										<i class="bi bi-plus"></i>
									</button>
								</div>
							</div>
							
							<div class="row align-items-center g-3 mt-2">
								<div class="col-md-3">
									<label class="form-label fw-bold">Carte Foil:</label>
								</div>
								<div class="col-md-2">
									<button type="submit" name="nb_foil_moins" value="1" class="btn btn-outline-danger quantity-control">
										<i class="bi bi-dash"></i>
									</button>
								</div>
								<div class="col-md-2 text-center">
									<span class="fw-bold fs-4"><?php echo $nb_f; ?></span>
								</div>
								<div class="col-md-2">
									<button type="submit" name="nb_foil_plus" value="1" class="btn btn-outline-success quantity-control">
										<i class="bi bi-plus"></i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>

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