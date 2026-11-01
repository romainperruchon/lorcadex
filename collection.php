<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"]) && isset($_GET['collection'])) {
	
	$id_user = $_SESSION["id_user"];
	$choix_collection = $_GET['collection'];
	
	$sql = "SELECT nom_chapitre FROM chapitres WHERE id_chapitre='$choix_collection'";
	$res = $mysqli->query($sql);
	$t = $res->fetch_assoc();
	
	$nom_chapitre = $t['nom_chapitre'];
		
	// Récupération du nombre de cartes par rareté
	$sql_r1 = "SELECT id_carte FROM cartes WHERE rarete='1' AND chapitre='$choix_collection'";
	$res_r1 = $mysqli->query($sql_r1);
	$nb_co = $res_r1->num_rows;
	
	$sql_r2 = "SELECT id_carte FROM cartes WHERE rarete='2' AND chapitre='$choix_collection'";
	$res_r2 = $mysqli->query($sql_r2);
	$nb_unco = $res_r2->num_rows;
	
	$sql_r3 = "SELECT id_carte FROM cartes WHERE rarete='3' AND chapitre='$choix_collection'";
	$res_r3 = $mysqli->query($sql_r3);
	$nb_r = $res_r3->num_rows;
	
	$sql_r4 = "SELECT id_carte FROM cartes WHERE rarete='4' AND chapitre='$choix_collection'";
	$res_r4 = $mysqli->query($sql_r4);
	$nb_tr = $res_r4->num_rows;
	
	$sql_r5 = "SELECT id_carte FROM cartes WHERE rarete='5' AND chapitre='$choix_collection'";
	$res_r5 = $mysqli->query($sql_r5);
	$nb_leg = $res_r5->num_rows;
	
	$sql_r6 = "SELECT id_carte FROM cartes WHERE rarete='6' AND chapitre='$choix_collection'";
	$res_r6 = $mysqli->query($sql_r6);
	$nb_ench = $res_r6->num_rows;
	
	// Récupération du nombre de cartes par rareté de l'utilisateur 
	$sql_r1_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete='1' AND chapitre='$choix_collection' GROUP BY user_as_carte.id_carte";
	$res_r1_user = $mysqli->query($sql_r1_user);
	$nb_co_user = $res_r1_user->num_rows;
	
	$sql_r2_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete='2' AND chapitre='$choix_collection' GROUP BY user_as_carte.id_carte";
	$res_r2_user = $mysqli->query($sql_r2_user);
	$nb_unco_user = $res_r2_user->num_rows;
	
	$sql_r3_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete='3' AND chapitre='$choix_collection' GROUP BY user_as_carte.id_carte";
	$res_r3_user = $mysqli->query($sql_r3_user);
	$nb_r_user = $res_r3_user->num_rows;
	
	$sql_r4_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete='4' AND chapitre='$choix_collection' GROUP BY user_as_carte.id_carte";
	$res_r4_user = $mysqli->query($sql_r4_user);
	$nb_tr_user = $res_r4_user->num_rows;
	
	$sql_r5_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete='5' AND chapitre='$choix_collection' GROUP BY user_as_carte.id_carte";
	$res_r5_user = $mysqli->query($sql_r5_user);
	$nb_leg_user = $res_r5_user->num_rows;
	
	$sql_r6_user = "SELECT user_as_carte.id_carte FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete='6' AND chapitre='$choix_collection' GROUP BY user_as_carte.id_carte";
	$res_r6_user = $mysqli->query($sql_r6_user);
	$nb_ench_user = $res_r6_user->num_rows;
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
	
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">

    <style>
		.img_card-gold {
			background-color: gold;
		}
		
		.img_card-green {
			background-color: green;
		}
		
		.img_card-orange {
			background-color: red;
		}
		
		.img_background-green {
			background-color: green;
		}
		
		.img_background-gold {
			background-color: gold;
		}
	
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
	  .hidden {display:none !important;}
	  
	  .numcard {
		padding-bottom: 14px; 
		padding-left: 32px;
	  }
	  
	  @media (min-width: 400px) {
		.numcard {
			padding-bottom: 16px; 
			padding-left: 38px;
		}
	  }
	  
	  @media (min-width:600px) {
		.numcard {
			padding-bottom: 14px; 
			padding-left: 32px;
		}
	  }
	  
	  @media (min-width:1000px) {
		.numcard {
			padding-bottom: 18px; 
			padding-left: 42px;
		}
	  }
	  
	  @media (min-width:1200px) {
		.numcard {
			padding-bottom: 21px; 
			padding-left: 52px;
		}
	  }
	  
	  @media (min-width:1600px) {
		.numcard {
			padding-bottom: 24px; 
			padding-left: 62px;
		}
	  }
	  
	  /* Sticky header styles */
	  .sticky-header {
		position: fixed;
		top: 0;
		width: 100%;
		z-index: 1000;
		background-color: #343a40; /* Match the navbar background */
		box-shadow: 0 2px 4px rgba(0,0,0,.1);
	  }
	  
	  /* Add padding to main content to prevent overlap */
	  .main-content {
		padding-top: 150px; /* Adjust based on header height */
	  }
    </style>

    
  </head>
  <body>
  
<header data-bs-theme="dark" class="sticky-header">
  <div class="navbar-dark bg-dark shadow-sm">
    <div class="container d-flex align-items-center">
      <a href="liste_collection.php"><img src="./img/logo.png" alt="" width="52" height="72"></a>
	  <a href="collection.php?collection=<?php echo $choix_collection; ?>"><strong><?php echo $nom_chapitre; ?></strong></a>
    </div>
  </div>
</header>

<main class="main-content">

  <section class="py-1 text-center container">
    <div class="row">
	  <div class="col-6">
		<img title='ambres' onclick="effacer_ambres()" id="encre1" src='img/encre1.png' class="img-fluid" />
		<img title='ambres' onclick="afficher_ambres()" id="encre1_disable" src='img/e1 - disabled.png' class="img-fluid" style='display:none;' />
	  
		<img title='amethystes' onclick="effacer_amethystes()" id="encre2" src='img/encre2.png' class="img-fluid" />
		<img title='amethystes' onclick="afficher_amethystes()" id="encre2_disable" src='img/e2 - disabled.png' class="img-fluid" style='display:none;' />

		<img title='emeraudes' onclick="effacer_emeraudes()" id="encre3" src='img/encre3.png' class="img-fluid" />
		<img title='emeraudes' onclick="afficher_emeraudes()" id="encre3_disable" src='img/e3 - disabled.png' class="img-fluid" style='display:none;' />

		<img title='rubis' onclick="effacer_rubis()" id="encre4" src='img/encre4.png' class="img-fluid" />
		<img title='rubis' onclick="afficher_rubis()" id="encre4_disable" src='img/e4 - disabled.png' class="img-fluid" style='display:none;' />
		
		<img title='saphirs' onclick="effacer_saphirs()" id="encre5" src='img/encre5.png' class="img-fluid" />
		<img title='saphirs' onclick="afficher_saphirs()" id="encre5_disable" src='img/e5 - disabled.png' class="img-fluid" style='display:none;' />
		
		<img title='aciers' onclick="effacer_aciers()" id="encre6" src='img/encre6.png' class="img-fluid" />
		<img title='aciers' onclick="afficher_aciers()" id="encre6_disable" src='img/e6 - disabled.png' class="img-fluid" style='display:none;' />
	  </div>
      <div class="col-6">
		
		<img title='communes' onclick="effacer_communes()" id="rarete1" src='img/rarete_1.png' class="img-fluid" />
		<img title='communes' onclick="afficher_communes()" id="rarete1_disable" src='img/r1_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_co_user."/".$nb_co; ?>
		
		<img title='uncos' onclick="effacer_uncos()" id="rarete2" src='img/rarete_2.png' class="img-fluid" />
		<img title='uncos' onclick="afficher_uncos()" id="rarete2_disable" src='img/r2_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_unco_user."/".$nb_unco; ?>
		
		<img title='rares' onclick="effacer_rares()" id="rarete3" src='img/rarete_3.png' class="img-fluid" />
		<img title='rares' onclick="afficher_rares()" id="rarete3_disable" src='img/r3_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_r_user."/".$nb_r; ?>
		
		<img title='trares' onclick="effacer_trares()" id="rarete4" src='img/rarete_4.png' class="img-fluid" />
		<img title='trares' onclick="afficher_trares()" id="rarete4_disable" src='img/r4_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_tr_user."/".$nb_tr; ?>
		
		<img title='legendaires' onclick="effacer_legendaires()" id="rarete5" src='img/rarete_5.png' class="img-fluid" />
		<img title='legendaires' onclick="afficher_legendaires()" id="rarete5_disable" src='img/r5_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_leg_user."/".$nb_leg; ?>
		
		<img title='enchantees' onclick="effacer_enchantees()" id="rarete6" src='img/rarete_6.png' class="img-fluid" />
		<img title='enchantees' onclick="afficher_enchantees()" id="rarete6_disable" src='img/r6_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_ench_user."/".$nb_ench; ?>
		
		<span class="material-icons" style='cursor:pointer;float:right' id='icon_afficher_min' onclick="afficher_min()">view_module</span>
		<span class="material-icons" style='cursor:pointer;float:right;display:none' id='icon_afficher_image' onclick="afficher_image()">image</span>
      </div>
    </div>
  </section>

  <div class="album py-2 bg-body-tertiary">
    <div class="container">
	
      <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 g-2">
		<?php
		
		$choix_affichage = 1;
		
		if (isset($_POST['choix_affichage'])) {
			$choix_affichage = $_POST['choix_affichage'];
		}
		
		$sql = "SELECT * FROM cartes WHERE chapitre='$choix_collection'";
		
		if ($choix_affichage == 2) {
			$sql .= " AND id_carte NOT IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND quantite > 0)";
		}
		else if ($choix_affichage == 3) {
			$sql .= " AND id_carte NOT IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND (quantite > 0 OR (quantite = 0 AND foil='0')))";
		}
		else if ($choix_affichage == 4) {
			$sql .= " AND id_carte NOT IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND quantite > 0 AND foil='1' AND rarete != 6 AND rarete != 7 AND rarete != 8)";
		}
		else if ($choix_affichage == 5) {
			$sql .= " AND id_carte IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND quantite > 0 )";
		}
		
		// Récupération de la liste des cartes du chapitre 1
		$res = $mysqli->query($sql);
		
		while ($t_cartes = $res->fetch_assoc()) {
			
			$id_carte	= $t_cartes["id_carte"];
			$num_carte 	= $t_cartes["numero_carte"];
			$nom_carte	= $t_cartes["nom_carte"];
			$cat_carte	= $t_cartes["categorie_carte"];
			$rarete		= $t_cartes["rarete"];
			$encre		= $t_cartes["encre"];
			$image_url	= $t_cartes["image"];
			
			// Nombre non foil
			$sql_nb_nf = "SELECT quantite FROM user_as_carte WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='0'";
			$res_nb_nf = $mysqli->query($sql_nb_nf);
			if($t = $res_nb_nf->fetch_assoc()) {
				$nb_nf = $t['quantite'];
			}
			else {
				$nb_nf = 0;
			}
			
			// Nombre foil
			$sql_nb_f = "SELECT quantite FROM user_as_carte WHERE id_user='$id_user' AND id_carte='$id_carte' AND foil='1'";
			$res_nb_f = $mysqli->query($sql_nb_f);
			if($t = $res_nb_f->fetch_assoc()) {
				$nb_f = $t['quantite'];
			}
			else {
				$nb_f = 0;
			}
			
		?>
	  
        <div class="col <?php 
			// Ajout class rareté pour filtre par rareté
			if ($rarete == 1) { echo "commune "; } 
			elseif ($rarete == 2) { echo "unco "; } 
			elseif ($rarete == 3) { echo "rare "; } 
			elseif ($rarete == 4) { echo "trare "; } 
			elseif ($rarete == 5) { echo "legendaire "; } 
			elseif ($rarete == 6) { echo "enchantee "; }
			
			// Ajout class rareté pour filtre par encre
			if ($encre == 1) { echo "ambre"; } 
			elseif ($encre == 2) { echo "amethyste"; } 
			elseif ($encre == 3) { echo "emeraude"; } 
			elseif ($encre == 4) { echo "rubis"; } 
			elseif ($encre == 5) { echo "saphir"; } 
			elseif ($encre == 6) { echo "acier"; }
		?>">
		<?php
        echo "<div id='img_background_".$id_carte."' class='card shadow-sm";
		if ($nb_nf + $nb_f >= 4) {
			if ($nb_nf >= 1 && $nb_f >= 1 && $nb_nf + $nb_f >= 6) {
				// PlaySet + Master Set
				echo " img_background-gold";
			}
			else {
				// PlaySet
				echo " img_background-green";
			}
		}
		echo "'>";
		?>
			<div id="id_carte_<?php echo $id_carte; ?>">
				<div style="text-align: center; width: 70%;font-weight: bold;" class="numcard">
					<?php echo "<div style ='border:1px solid black; background-color: lightblue;'>".$num_carte."</div>"; ?>
				</div>
				<?php 
				// Construct local image path
				$local_image_path = 'cartes/FR/'.$nom_chapitre.'/'.$num_carte.'.jpg';
				
				// Check if the local image exists, otherwise use the URL from database
				if (file_exists($local_image_path)) {
					$image_src = $local_image_path;
				} else {
					$image_src = $image_url;
				}
				echo "<a href='".$local_image_path."' class='image_carte' data-toggle=\"lightbox\" data-gallery=\"chapitre1\" />";
				echo "\t<img src='".$image_src."' width='100%' height='100%' id='img_card_".$id_carte."' class='img-fluid img-thumbnail";

				if ($nb_nf > 0 || $nb_f > 0) {
					if ($nb_nf > 0 && $nb_f > 0) {
						echo " img_card-gold";
					}
					else {
						echo " img_card-green";
					}
				}
				else {
					echo " img_card-orange";
				}
				echo "' />"; 
				echo "</a>";
				?>
			</div>
            <div class="card-body" >
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
				  <form method='POST' onsubmit="return sendData(<?php echo $id_carte.",".$nb_nf; ?>);">
					<input type='submit' id='add_card_<?php echo $id_carte; ?>' name='add_card_<?php echo $id_carte; ?>' value='<?php echo $nb_nf; ?>' />
				  </form>
				<?php 
				if ($rarete != 6) {
				?>
				  <form method='POST' onsubmit="return sendDataFoil(<?php echo $id_carte.",".$nb_f; ?>);">
					<input type='submit' id='add_card_foil_<?php echo $id_carte; ?>' name='add_card_foil_<?php echo $id_carte; ?>' value='<?php echo $nb_f; ?>' />
				  </form>
				<?php 
				}
				?>
                </div>
				<a href="detail_carte.php?id_carte=<?php echo $id_carte; ?>" class="btn btn-primary float-right ml-2" role="button">Détail</a>
              </div>
            </div>
          </div>
        </div>
		
		<?php
		}
		?>
      </div>
    </div>
	
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	
  </div>
  
  
  
  <footer class="footer mt-auto py-3 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <form method='post' action='collection.php?collection=<?php echo $choix_collection; ?>'>
            <select class="form-select form-select-sm" name='choix_affichage' onchange="this.form.submit()">
              <option value='1' <?php if ($choix_affichage == 1) { echo "selected"; } ?>>Toutes les cartes</option>
              <option value='2' <?php if ($choix_affichage == 2) { echo "selected"; } ?>>Toutes les manquantes</option>
              <option value='3' <?php if ($choix_affichage == 3) { echo "selected"; } ?>>Manquantes normales</option>
              <option value='4' <?php if ($choix_affichage == 4) { echo "selected"; } ?>>Manquantes foils</option>
              <option value='5' <?php if ($choix_affichage == 5) { echo "selected"; } ?>>Ma collection</option>
            </select>
          </form>
        </div>
        <div class="col-md-3"></div>
      </div>
    </div>
  </footer>

</main>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js" crossorigin="anonymous"></script>
<script type="text/javascript">
	// Fonction pour supprimer une carte normale
	function remove_card(id_card, remove_card) {
		$.ajax({
			type: 'post',
			url: 'delete.php',
			data: {
			  id_card:id_card,
			  remove_card:remove_card
			},
			success: function (response) {
				
				// Vérifier si l'élément existe avant de le modifier
				var element = document.getElementById('add_card_'+id_card);
				element.value = +element.value - 1;
				
				// Mise à jour des classes CSS
				var element_foil = document.getElementById('add_card_foil_'+id_card);
				var element_nfoil = document.getElementById('add_card_'+id_card);
				
				if (element_foil) {
					var value_nb_foil = +element_foil.value;
					if (value_nb_foil > 0) {
						var imgElement = document.getElementById('img_card_'+id_card);
						if (imgElement) {
							imgElement.classList.remove("img_card-green");
							imgElement.classList.add("img_card-gold");
						}
					}
				}
				
				if (element_nfoil) {
					var value_nb_nfoil = +element_nfoil.value;
					if (element_foil) {
						var value_nb_foil = +element_foil.value;
						if (value_nb_foil + value_nb_nfoil >= 4) {
							var bgElement = document.getElementById('img_background_'+id_card);
							if (bgElement) {
								if (value_nb_foil >= 1 && value_nb_nfoil >= 1 && value_nb_nfoil + value_nb_foil >= 6) {
									bgElement.classList.remove("img_background-green");
									bgElement.classList.add("img_background-gold");
								}
								else {
									bgElement.classList.add("img_background-green");
								}
							}
						}
					} else {
						// Si pas de foil, vérifier juste les cartes normales
						if (value_nb_nfoil >= 4) {
							var bgElement = document.getElementById('img_background_'+id_card);
							if (bgElement) {
								bgElement.classList.add("img_background-green");
							}
						}
					}
				}
			}
		});
		return false;
	}
	
	// Fonction pour supprimer une carte foil
	function remove_card_foil(id_card, remove_card_foil) {
		$.ajax({
			type: 'post',
			url: 'delete.php',
			data: {
			  id_card:id_card,
			  remove_card_foil:remove_card_foil
			},
			success: function (response) {
				// Vérifier si l'élément existe avant de le modifier
				var element = document.getElementById('add_card_foil_'+id_card);
				if (element) {
					element.value = +element.value - 1;
				}
				
				// Mise à jour des classes CSS
				var element_foil = document.getElementById('add_card_foil_'+id_card);
				var element_nfoil = document.getElementById('add_card_'+id_card);
				
				if (element_nfoil) {
					var value_nb_nfoil = +element_nfoil.value;
					if (value_nb_nfoil > 0) {
						var imgElement = document.getElementById('img_card_'+id_card);
						if (imgElement) {
							imgElement.classList.remove("img_card-green");
							imgElement.classList.add("img_card-gold");
						}
					}
				}
				
				if (element_foil) {
					var value_nb_foil = +element_foil.value;
					if (element_nfoil) {
						var value_nb_nfoil = +element_nfoil.value;
						if (value_nb_foil + value_nb_nfoil >= 4) {
							var bgElement = document.getElementById('img_background_'+id_card);
							if (bgElement) {
								if (value_nb_foil >= 1 && value_nb_nfoil >= 1 && value_nb_nfoil + value_nb_foil >= 6) {
									bgElement.classList.remove("img_background-green");
									bgElement.classList.add("img_background-gold");
								}
								else {
									bgElement.classList.add("img_background-green");
								}
							}
						}
					} else {
						// Si pas de nfoil, vérifier juste les cartes foil
						if (value_nb_foil >= 4) {
							var bgElement = document.getElementById('img_background_'+id_card);
							if (bgElement) {
								bgElement.classList.add("img_background-green");
							}
						}
					}
				}
			}
		});
		return false;
	}
	function sendData(id_card, add_card) {
		
		$.ajax({
			type: 'post',
			url: 'insert.php',
			data: {
			  id_card:id_card,
			  add_card:add_card
			},
			success: function (response) {
				document.getElementById('add_card_'+id_card).value = +document.getElementById('add_card_'+id_card).value + 1;
				
				value_nb_foil = +document.getElementById('add_card_foil_'+id_card).value;
				value_nb_nfoil = +document.getElementById('add_card_'+id_card).value;
				
				if (value_nb_foil > 0) {
					var element = document.getElementById('img_card_'+id_card);
					element.classList.remove("img_card-green");
					element.classList.add("img_card-gold");
				}
				
				if (value_nb_foil + value_nb_nfoil >= 4) {			
					var element = document.getElementById('img_background_'+id_card);
					
					if (value_nb_foil >= 1 && value_nb_nfoil >= 1 && value_nb_nfoil + value_nb_foil >= 6) {
						element.classList.remove("img_background-green");
						element.classList.add("img_background-gold");
					}
					else {
						element.classList.add("img_background-green");
					}
				}
			}
		});
		return false;
	}
	
	function sendDataFoil(id_card, add_card_foil) {
	
		$.ajax({
			type: 'post',
			url: 'insert.php',
			data: {
			  id_card:id_card,
			  add_card_foil:add_card_foil
			},
			success: function (response) {
				document.getElementById('add_card_foil_'+id_card).value = +document.getElementById('add_card_foil_'+id_card).value + 1;
			  
				value_nb_foil = +document.getElementById('add_card_foil_'+id_card).value;
				value_nb_nfoil = +document.getElementById('add_card_'+id_card).value;
			  
				if (value_nb_nfoil > 0) {
					var element = document.getElementById('img_card_'+id_card);
					element.classList.remove("img_card-green");
					element.classList.add("img_card-gold");
				}
				
				if (value_nb_foil + value_nb_nfoil >= 4) {
					var element = document.getElementById('img_background_'+id_card);
					
					if (value_nb_foil >= 1 && value_nb_nfoil >= 1 && value_nb_nfoil + value_nb_foil >= 6) {
						element.classList.remove("img_background-green");
						element.classList.add("img_background-gold");
					}
					else {
						element.classList.add("img_background-green");
					}
				}
			}
		});
			
		return false;
	}

	// Gestion des clics sur les boutons d'ajout/suppression
	$('.btn-group').on('click', 'input[type="submit"]', function(e) {
		// Vérifier si Shift est enfoncé
		if (e.shiftKey) {
			var elementId = $(this).attr('id');
			var id_card;
			
			// Extraction de l'ID de la carte de manière robuste
			if (elementId.includes('add_card_foil_')) {
				// Pour les cartes foil : id="add_card_foil_123"
				id_card = elementId.split('add_card_foil_')[1];
			} else if (elementId.includes('add_card_')) {
				// Pour les cartes normales : id="add_card_123"  
				id_card = elementId.split('add_card_')[1];
			}
			
			var current_value = parseInt($(this).val());
			
			// Si on a une valeur > 0, on supprime une carte
			if (current_value > 0 && id_card) {
				if (elementId.includes('add_card_foil_')) {
					// Suppression de carte foil
					remove_card_foil(id_card, current_value);
				} else {
					// Suppression de carte normale
					remove_card(id_card, current_value);
				}
			}
			e.preventDefault();
		}
	});
	
	// Gestion du clic droit (contextmenu) pour suppression
	$('.btn-group').on('contextmenu', 'input[type="submit"]', function(e) {
		e.preventDefault();
		var elementId = $(this).attr('id');
		var id_card;
		
		// Extraction de l'ID de la carte de manière robuste
		if (elementId.includes('add_card_foil_')) {
			// Pour les cartes foil : id="add_card_foil_123"
			id_card = elementId.split('add_card_foil_')[1];
		} else if (elementId.includes('add_card_')) {
			// Pour les cartes normales : id="add_card_123"  
			id_card = elementId.split('add_card_')[1];
		}
		
		var current_value = parseInt($(this).val());
		
		// Si on a une valeur > 0, on supprime une carte
		if (current_value > 0 && id_card) {
			if (elementId.includes('add_card_foil_')) {
				// Suppression de carte foil
				remove_card_foil(id_card, current_value);
			} else {
				// Suppression de carte normale
				remove_card(id_card, current_value);
			}
		}
	});
	
	function afficher_min() {
		document.querySelectorAll('.image_carte').forEach(function(el) {
		   el.style.display = 'none';
		});		
		
		document.getElementById('icon_afficher_min').style.display = 'none';
		document.getElementById('icon_afficher_image').style.display = '';
	}
	
	function afficher_image() {
		document.querySelectorAll('.image_carte').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('icon_afficher_min').style.display = '';
		document.getElementById('icon_afficher_image').style.display = 'none';
	}
	
	// COMMUNES
	function effacer_communes() {
		document.querySelectorAll('.commune').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete1_disable').style.display = '';
		document.getElementById('rarete1').style.display = 'none';
	}
	
	function afficher_communes() {
		document.querySelectorAll('.commune').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete1_disable').style.display = 'none';
		document.getElementById('rarete1').style.display = '';
	}
	
	// UNCOS
	function effacer_uncos() {
		document.querySelectorAll('.unco').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete2_disable').style.display = '';
		document.getElementById('rarete2').style.display = 'none';
	}
	
	function afficher_uncos() {
		document.querySelectorAll('.unco').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete2_disable').style.display = 'none';
		document.getElementById('rarete2').style.display = '';
	}
	
	// RARES
	function effacer_rares() {
		document.querySelectorAll('.rare').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete3_disable').style.display = '';
		document.getElementById('rarete3').style.display = 'none';
	}
	
	function afficher_rares() {
		document.querySelectorAll('.rare').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete3_disable').style.display = 'none';
		document.getElementById('rarete3').style.display = '';
	}
	
	// TRES RARES
	function effacer_trares() {
		document.querySelectorAll('.trare').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete4_disable').style.display = '';
		document.getElementById('rarete4').style.display = 'none';
	}
	
	function afficher_trares() {
		document.querySelectorAll('.trare').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete4_disable').style.display = 'none';
		document.getElementById('rarete4').style.display = '';
	}
	
	// LEGENDAIRES
	function effacer_legendaires() {
		document.querySelectorAll('.legendaire').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete5_disable').style.display = '';
		document.getElementById('rarete5').style.display = 'none';
	}
	
	function afficher_legendaires() {
		document.querySelectorAll('.legendaire').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete5_disable').style.display = 'none';
		document.getElementById('rarete5').style.display = '';
	}
	
	// ENCHANTEES
	function effacer_enchantees() {
		document.querySelectorAll('.enchantee').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete6_disable').style.display = '';
		document.getElementById('rarete6').style.display = 'none';
	}
	
	function afficher_enchantees() {
		document.querySelectorAll('.enchantee').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete6_disable').style.display = 'none';
		document.getElementById('rarete6').style.display = '';
	}
	
	// AMBRE
	function effacer_ambres() {
		document.querySelectorAll('.ambre').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('encre1_disable').style.display = '';
		document.getElementById('encre1').style.display = 'none';
	}
	
	function afficher_ambres() {
		document.querySelectorAll('.ambre').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('encre1_disable').style.display = 'none';
		document.getElementById('encre1').style.display = '';
	}
	
	// AMETHYSTE
	function effacer_amethystes() {
		document.querySelectorAll('.amethyste').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('encre2_disable').style.display = '';
		document.getElementById('encre2').style.display = 'none';
	}
	
	function afficher_amethystes() {
		document.querySelectorAll('.amethyste').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('encre2_disable').style.display = 'none';
		document.getElementById('encre2').style.display = '';
	}
	
	// EMERAUDE
	function effacer_emeraudes() {
		document.querySelectorAll('.emeraude').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('encre3_disable').style.display = '';
		document.getElementById('encre3').style.display = 'none';
	}
	
	function afficher_emeraudes() {
		document.querySelectorAll('.emeraude').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('encre3_disable').style.display = 'none';
		document.getElementById('encre3').style.display = '';
	}
	
	// RUBIS
	function effacer_rubis() {
		document.querySelectorAll('.rubis').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('encre4_disable').style.display = '';
		document.getElementById('encre4').style.display = 'none';
	}
	
	function afficher_rubis() {
		document.querySelectorAll('.rubis').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('encre4_disable').style.display = 'none';
		document.getElementById('encre4').style.display = '';
	}

	// SAPHIR
	function effacer_saphirs() {
		document.querySelectorAll('.saphir').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('encre5_disable').style.display = '';
		document.getElementById('encre5').style.display = 'none';
	}
	
	function afficher_saphirs() {
		document.querySelectorAll('.saphir').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('encre5_disable').style.display = 'none';
		document.getElementById('encre5').style.display = '';
	}
	
	// ACIER
	function effacer_aciers() {
		document.querySelectorAll('.acier').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('encre6_disable').style.display = '';
		document.getElementById('encre6').style.display = 'none';
	}
	
	function afficher_aciers() {
		document.querySelectorAll('.acier').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('encre6_disable').style.display = 'none';
		document.getElementById('encre6').style.display = '';
	}

	$(document).ready(function ($) {
		// delegate calls to data-toggle="lightbox"
		$(document).on('click', '[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', function(event) {
			event.preventDefault();
			return $(this).ekkoLightbox({
				onShown: function() {
					if (window.console) {
						return console.log('Checking our the events huh?');
					}
				},
				onNavigate: function(direction, itemIndex) {
					if (window.console) {
						return console.log('Navigating '+direction+'. Current item: '+itemIndex);
					}
				}
			});
		});

		// navigateTo
		$(document).on('click', '[data-toggle="lightbox"][data-gallery="navigateTo"]', function(event) {
			event.preventDefault();

			return $(this).ekkoLightbox({
				onShown: function() {

					this.modal().on('click', '.modal-footer a', function(e) {

						e.preventDefault();
						this.navigateTo(2);

					}.bind(this));

				}
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
