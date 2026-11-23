<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"]) && isset($_GET['collection'])) {
	
	$id_user = $_SESSION["id_user"];
	$choix_collection = $_GET['collection'];
	
	// Check if we want to display all cards
	$display_all = ($choix_collection == "all");
	
	if ($display_all) {
		// For "all", we don't need to get chapter name or folder
		$nom_chapitre = "Toutes";
		$chapitre_folder = "";
	} else {
		$sql = "SELECT nom_chapitre FROM chapitres WHERE id_chapitre='$choix_collection'";
		$res = $mysqli->query($sql);
		$t = $res->fetch_assoc();
		
		$nom_chapitre = $t['nom_chapitre'];
		$chapitre_folder = "Chapitre ".$choix_collection;
	}
		
	// Nombre de cartes par rareté : total, puis possédées par l'utilisateur
	$filtre_chapitre = "";
	if (!$display_all) {
		$filtre_chapitre = " AND chapitre='".$mysqli->real_escape_string($choix_collection)."'";
	}
	
	$nb_cartes = array_fill(1, 8, 0);
	$sql = "SELECT rarete, COUNT(*) AS nb FROM cartes WHERE rarete BETWEEN 1 AND 8".$filtre_chapitre." GROUP BY rarete";
	$res = $mysqli->query($sql);
	while ($t = $res->fetch_assoc()) {
		$nb_cartes[(int)$t['rarete']] = (int)$t['nb'];
	}
	list($nb_co, $nb_unco, $nb_r, $nb_tr, $nb_leg, $nb_ench, $nb_epic, $nb_iconique) = array_values($nb_cartes);
	
	$nb_cartes_user = array_fill(1, 8, 0);
	$sql = "SELECT rarete, COUNT(DISTINCT user_as_carte.id_carte) AS nb FROM user_as_carte, cartes WHERE user_as_carte.id_carte = cartes.id_carte AND user_as_carte.id_user='$id_user' AND rarete BETWEEN 1 AND 8".$filtre_chapitre." GROUP BY rarete";
	$res = $mysqli->query($sql);
	while ($t = $res->fetch_assoc()) {
		$nb_cartes_user[(int)$t['rarete']] = (int)$t['nb'];
	}
	list($nb_co_user, $nb_unco_user, $nb_r_user, $nb_tr_user, $nb_leg_user, $nb_ench_user, $nb_epic_user, $nb_iconique_user) = array_values($nb_cartes_user);
	
	// Critères du panneau de filtres avancés
	// Critères numériques : code => array(libellé, colonne de la table cartes)
	$filtres_numeriques = array(
		"attaque" => array("Attaque", "force_carte"),
		"volonte" => array("Volonté", "volonte_carte"),
		"cout"    => array("Coût", "cout_carte"),
		"lore"    => array("Nombre de lore", "lore_carte")
	);
	$filtres_types = array(1 => "Personnage", 2 => "Action", 4 => "Chanson", 3 => "Objet", 5 => "Lieu");
	
	// Capacités : code => array(libellé, colonne booléenne de la table cartes ou null si elle n'existe pas)
	$filtres_capacites = array(
		"charge"        => array("Charge", "charge"),
		"offensif"      => array("Offensif", null),
		"melomane"      => array("Mélomane", null),
		"rempart"       => array("Rempart", "rempart"),
		"alter"         => array("Alter", null),
		"soutien"       => array("Soutien", "soutien"),
		"insaisissable" => array("Insaisissable", "inssaisissable"),
		"hors-atteinte" => array("Hors d'atteinte", "hors_atteinte"),
		"agilite"       => array("Agilité", null)
	);
	
	// Une carte possède une capacité quand son mot-clé ouvre un paragraphe de la description ("Rempart (...)").
	// Ailleurs dans le texte, il s'agit d'un effet qui accorde la capacité à d'autres personnages.
	$regex_capacites = array();
	foreach ($filtres_capacites as $code => $capacite) {
		$mot_cle = str_replace("'", "['’]", preg_quote($capacite[0], "/"));
		$regex_capacites[$code] = '/^[ \t]*'.$mot_cle.'(?!\p{L})/mu';
	}
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
		
		/* Réserve la place de la carte pour que le chargement différé (lazy) des images fonctionne */
		.image_carte img {
			aspect-ratio: auto 63 / 88;
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
	  
	  /* Sticky filter section styles */
	  .sticky-filters {
		position: fixed;
		top: 70px;
		width: 100%;
		z-index: 999;
		background-color: white;
		padding: 1rem 0;
		box-shadow: 0 2px 4px rgba(0,0,0,.1);
	  }
	  
	  /* Add padding to main content to prevent overlap */
	  .main-content {
		padding-top: 125px;
	  }
	  
	  /* Fixed footer styles */
	  .footer {
		position: fixed;
		bottom: 0;
		width: 100%;
		z-index: 1000;
		background-color: #f8f9fa;
		box-shadow: 0 -2px 4px rgba(0,0,0,.1);
	  }
	  
	  /* Add padding to main content to prevent overlap with footer */
	  .main-content {
		padding-bottom: 100px; /* Adjust based on footer height */
	  }
	  
	  /* Search bar styles */
	  .search-container {
		display: flex;
		align-items: center;
		margin-right: 15px;
	  }
	  
	  .search-input {
		width: 200px;
		padding: 5px 10px;
		border: 1px solid #ccc;
		border-radius: 4px;
		font-size: 14px;
		outline: none;
		transition: border-color 0.3s ease;
	  }
	  
	  .search-input:focus {
		border-color: #007bff;
		box-shadow: 0 0 0 2px rgba(0,123,255,.25);
	  }
	  
	  /* Cartes masquées par le panneau de filtres */
	  .hors-filtres {
		display: none !important;
	  }
	  
	  /* Panneau de filtres : s'ouvre par-dessus les cartes et défile si l'écran est trop petit */
	  .panneau-filtres {
		color: var(--bs-body-color);
		margin-top: 4px;
		padding-top: 8px;
		padding-bottom: 8px;
		border-top: 1px solid #dee2e6;
		max-height: calc(100vh - 240px);
		max-height: calc(100dvh - 240px);
		overflow-y: auto;
	  }
	  
	  /* Add padding-top for mobile view (row-cols-2) */
	  @media (max-width: 575.98px) {
		.row.row-cols-2 {
			padding-top: 60px;
		}
	  }
    </style>

    
  </head>
  <body>
  
<header data-bs-theme="dark" class="sticky-header">
  <div class="navbar-dark bg-dark shadow-sm">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="liste_collection.php"><img src="./img/logo.png" alt="" width="52" height="72"></a>
	  <a href="collection.php?collection=<?php echo $choix_collection; ?>"><strong><?php echo $nom_chapitre; ?></strong></a>
      
      <!-- Search bar added here -->
      <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Rechercher..." onkeyup="filterCards()">
      </div>
    </div>
  </div>
</header>

<main class="main-content">

  <section class="py-1 text-center sticky-filters">
    <div class="row">
	  <div class="col-5">
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
		
		<button type="button" id="btn_filtres" class="btn btn-sm btn-outline-secondary align-middle py-0 px-1" title="Filtrer les cartes" aria-controls="panneau_filtres" aria-expanded="false" onclick="basculer_filtres()">
		  <span class="material-icons align-middle">filter_list</span><span class="d-none d-sm-inline align-middle">Filtres</span><span id="nb_filtres_actifs" class="badge text-bg-primary ms-1 align-middle" hidden></span>
		</button>
	  </div>
      <div class="col-7">
		
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
		
		<img title='epiques' onclick="effacer_epiques()" id="rarete7" src='img/rarete_7.png' class="img-fluid" />
		<img title='epiques' onclick="afficher_epiques()" id="rarete7_disable" src='img/r7_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_epic_user."/".$nb_epic; ?>

		<img title='enchantees' onclick="effacer_enchantees()" id="rarete6" src='img/rarete_6.png' class="img-fluid" />
		<img title='enchantees' onclick="afficher_enchantees()" id="rarete6_disable" src='img/r6_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_ench_user."/".$nb_ench; ?>
		
		<img title='iconiques' onclick="effacer_iconiques()" id="rarete8" src='img/rarete_8.png' class="img-fluid" />
		<img title='iconiques' onclick="afficher_iconiques()" id="rarete8_disable" src='img/r8_disabled.png' class="img-fluid" style='display:none;' />
		<?php echo $nb_iconique_user."/".$nb_iconique; ?>
		
		<span class="material-icons" style='cursor:pointer;float:right' id='icon_afficher_min' onclick="afficher_min()">view_module</span>
		<span class="material-icons" style='cursor:pointer;float:right;display:none' id='icon_afficher_image' onclick="afficher_image()">image</span>
      </div>
    </div>
	
	<!-- Panneau dépliable de filtres avancés -->
	<div id="panneau_filtres" class="panneau-filtres container text-start" role="region" aria-label="Filtres des cartes" data-bs-theme="light" hidden>
	  <div class="row g-2">
		<?php foreach ($filtres_numeriques as $cle => $filtre) { ?>
		<div class="col-6 col-md-3">
		  <label class="form-label small fw-bold mb-1" for="filtre_<?php echo $cle; ?>_min"><?php echo $filtre[0]; ?></label>
		  <div class="input-group input-group-sm">
			<input type="number" min="0" inputmode="numeric" id="filtre_<?php echo $cle; ?>_min" class="form-control" placeholder="Min" aria-label="<?php echo $filtre[0]; ?> minimum" autocomplete="off">
			<input type="number" min="0" inputmode="numeric" id="filtre_<?php echo $cle; ?>_max" class="form-control" placeholder="Max" aria-label="<?php echo $filtre[0]; ?> maximum" autocomplete="off">
		  </div>
		</div>
		<?php } ?>
	  </div>
	  
	  <div class="mt-2">
		<div class="small fw-bold mb-1">Type</div>
		<?php foreach ($filtres_types as $code => $libelle) { ?>
		<input type="checkbox" class="btn-check" name="filtre_type" id="filtre_type_<?php echo $code; ?>" value="<?php echo $code; ?>" autocomplete="off">
		<label class="btn btn-sm btn-outline-primary mb-1" for="filtre_type_<?php echo $code; ?>"><?php echo $libelle; ?></label>
		<?php } ?>
	  </div>
	  
	  <div class="mt-2">
		<div class="small fw-bold mb-1">Capacité</div>
		<?php foreach ($filtres_capacites as $code => $capacite) { ?>
		<input type="checkbox" class="btn-check" name="filtre_capacite" id="filtre_capacite_<?php echo $code; ?>" value="<?php echo $code; ?>" autocomplete="off">
		<label class="btn btn-sm btn-outline-primary mb-1" for="filtre_capacite_<?php echo $code; ?>"><?php echo htmlspecialchars($capacite[0]); ?></label>
		<?php } ?>
		<div class="small">
		  <div class="form-check form-check-inline">
			<input class="form-check-input" type="radio" name="filtre_mode_capacite" id="filtre_mode_une" value="une" checked autocomplete="off">
			<label class="form-check-label" for="filtre_mode_une">Au moins une</label>
		  </div>
		  <div class="form-check form-check-inline">
			<input class="form-check-input" type="radio" name="filtre_mode_capacite" id="filtre_mode_toutes" value="toutes" autocomplete="off">
			<label class="form-check-label" for="filtre_mode_toutes">Toutes</label>
		  </div>
		</div>
	  </div>
	  
	  <div class="d-flex justify-content-between align-items-center mt-2">
		<span id="resultat_filtres" class="small text-body-secondary" aria-live="polite"></span>
		<button type="button" class="btn btn-sm btn-outline-secondary" onclick="reinitialiser_filtres()">Réinitialiser</button>
	  </div>
	</div>
  </section>

  <div class="album py-2 bg-body-tertiary">
    <div class="container">
	
      <div class="row row-cols-2 row-cols-sm-4 row-cols-md-6 g-2">
		<?php
		
		$choix_affichage = 1;
		
		if (isset($_POST['choix_affichage'])) {
			$choix_affichage = $_POST['choix_affichage'];
		}
		
		// Récupération de la liste des cartes
		$conditions = array();
		if (!$display_all) {
			$conditions[] = "chapitre='".$mysqli->real_escape_string($choix_collection)."'";
		}
		
		if ($choix_affichage == 2) {
			$conditions[] = "id_carte NOT IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND quantite > 0)";
		}
		else if ($choix_affichage == 3) {
			$conditions[] = "id_carte NOT IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND (quantite > 0 OR (quantite = 0 AND foil='0')))";
		}
		else if ($choix_affichage == 4) {
			// Les raretés 6, 7 et 8 n'ont pas de version foil : elles sont toujours affichées
			$conditions[] = "(rarete IN (6, 7, 8) OR id_carte NOT IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND quantite > 0 AND foil='1'))";
		}
		else if ($choix_affichage == 5) {
			$conditions[] = "id_carte IN (SELECT DISTINCT(id_carte) FROM user_as_carte WHERE id_user='$id_user' AND quantite > 0 )";
		}
		
		$sql = "SELECT id_carte, numero_carte, nom_carte, categorie_carte, rarete, encre, artiste, image, chapitre, cout_carte, force_carte, volonte_carte, lore_carte, type_carte, charge, rempart, soutien, inssaisissable, hors_atteinte, description FROM cartes";
		if (count($conditions) > 0) {
			$sql .= " WHERE ".implode(" AND ", $conditions);
		}
		if ($display_all) {
			$sql .= " ORDER BY chapitre, numero_carte + 0, numero_carte";
		}
		
		$res = $mysqli->query($sql);
		
		// Quantités possédées par l'utilisateur : une seule requête pour toutes les cartes
		$quantites = array();
		$res_quantites = $mysqli->query("SELECT id_carte, foil, quantite FROM user_as_carte WHERE id_user='$id_user'");
		while ($t = $res_quantites->fetch_assoc()) {
			if (!isset($quantites[$t['id_carte']][$t['foil']])) {
				$quantites[$t['id_carte']][$t['foil']] = $t['quantite'];
			}
		}
		
		while ($t_cartes = $res->fetch_assoc()) {
			
			$id_carte	= $t_cartes["id_carte"];
			$num_carte 	= $t_cartes["numero_carte"];
			$nom_carte	= $t_cartes["nom_carte"];
			$cat_carte	= $t_cartes["categorie_carte"];
			$rarete		= $t_cartes["rarete"];
			$encre		= $t_cartes["encre"];
			$artiste	= $t_cartes["artiste"];
			$image_url	= $t_cartes["image"];
			$chapitre_num = $t_cartes["chapitre"];
			
			// Données lues par le panneau de filtres (valeur vide quand la carte n'a pas la caractéristique)
			$attributs_filtres = "";
			foreach ($filtres_numeriques as $cle => $filtre) {
				$valeur = $t_cartes[$filtre[1]];
				$attributs_filtres .= ' data-'.$cle.'="'.($valeur === null ? '' : (int)$valeur).'"';
			}
			$attributs_filtres .= ' data-type="'.(int)$t_cartes["type_carte"].'"';
			
			$capacites_carte = array();
			foreach ($filtres_capacites as $code => $capacite) {
				$par_colonne = $capacite[1] !== null && !empty($t_cartes[$capacite[1]]);
				if ($par_colonne || preg_match($regex_capacites[$code], (string)$t_cartes["description"])) {
					$capacites_carte[] = $code;
				}
			}
			$attributs_filtres .= ' data-capacites="'.implode(' ', $capacites_carte).'"';
			
			// Nombre non foil et foil
			$nb_nf = $quantites[$id_carte][0] ?? 0;
			$nb_f = $quantites[$id_carte][1] ?? 0;
			
		?>
	  
        <div class="col <?php 
			// Ajout class rareté pour filtre par rareté
			if ($rarete == 1) { echo "commune "; } 
			elseif ($rarete == 2) { echo "unco "; } 
			elseif ($rarete == 3) { echo "rare "; } 
			elseif ($rarete == 4) { echo "trare "; } 
			elseif ($rarete == 5) { echo "legendaire "; } 
			elseif ($rarete == 6) { echo "enchantee "; }
			elseif ($rarete == 7) { echo "epic "; }
			elseif ($rarete == 8) { echo "iconique "; }
			
			// Ajout class rareté pour filtre par encre
			if ($encre == 1) { echo "ambre"; } 
			elseif ($encre == 2) { echo "amethyste"; } 
			elseif ($encre == 3) { echo "emeraude"; } 
			elseif ($encre == 4) { echo "rubis"; } 
			elseif ($encre == 5) { echo "saphir"; } 
			elseif ($encre == 6) { echo "acier"; }
		?>" id="card_<?php echo $id_carte; ?>" data-name="<?php echo strtolower($nom_carte); ?>" data-category="<?php echo strtolower($cat_carte); ?>" data-artist="<?php echo strtolower($artiste); ?>"<?php echo $attributs_filtres; ?>>
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
					<?php 
					if ($display_all) {
						echo "<div style ='border:1px solid black; background-color: lightblue;'>Ch".$chapitre_num." - ".$num_carte."</div>";
					} else {
						// For specific chapter, show only card number
						echo "<div style ='border:1px solid black; background-color: lightblue;'>".$num_carte."</div>";
					}
					?>
				</div>
				<?php 
				// Construct local image path
				if ($display_all) {
					$local_image_path = 'cartes/FR/Chapitre '.$chapitre_num.'/'.$num_carte.'.jpg';
				} else {
					$local_image_path = 'cartes/FR/'.$chapitre_folder.'/'.$num_carte.'.jpg';
				}
				
				// Check if the local image exists, otherwise use the URL from database
				if (file_exists($local_image_path)) {
					$image_src = $local_image_path;
				} else {
					$image_src = $image_url;
				}
				echo "<a href='".$image_src."' class='image_carte' data-toggle=\"lightbox\" data-gallery=\"chapitre1\" />";
				echo "\t<img src='".$image_src."' width='100%' height='100%' loading='lazy' decoding='async' id='img_card_".$id_carte."' class='img-fluid img-thumbnail";

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
				if ($rarete != 6 && $rarete != 7 && $rarete != 8) {
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
	$(document).on('click', '.btn-group input[type="submit"]', function(e) {
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
	$(document).on('contextmenu', '.btn-group input[type="submit"]', function(e) {
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
	
	// EPICS
	function effacer_epiques() {
		document.querySelectorAll('.epic').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete7_disable').style.display = '';
		document.getElementById('rarete7').style.display = 'none';
	}
	
	function afficher_epiques() {
		document.querySelectorAll('.epic').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete7_disable').style.display = 'none';
		document.getElementById('rarete7').style.display = '';
	}
	
	// ICONIQUES
	function effacer_iconiques() {
		document.querySelectorAll('.iconique').forEach(function(el) {
		   el.style.display = 'none';
		});
		
		document.getElementById('rarete8_disable').style.display = '';
		document.getElementById('rarete8').style.display = 'none';
	}
	
	function afficher_iconiques() {
		document.querySelectorAll('.iconique').forEach(function(el) {
		   el.style.display = '';
		});
		
		document.getElementById('rarete8_disable').style.display = 'none';
		document.getElementById('rarete8').style.display = '';
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
	
	// Search functionality
	function filterCards() {
		const input = document.getElementById('searchInput');
		const filter = input.value.toLowerCase();
		const cards = document.querySelectorAll('.col');
		
		cards.forEach(card => {
			// Get card details from data attributes
			const nom_carte = card.getAttribute('data-name') || '';
			const categorie_carte = card.getAttribute('data-category') || '';
			const artiste = card.getAttribute('data-artist') || '';
			
			// Check if the card matches the search criteria
			if (nom_carte.includes(filter) || categorie_carte.includes(filter) || artiste.includes(filter)) {
				card.style.display = '';
			} else {
				card.style.display = 'none';
			}
		});
	}
</script>

<script type="text/javascript">
	// Panneau de filtres avancés : attaque, volonté, coût, lore, type et capacités
	const FILTRES_NUMERIQUES = <?php echo json_encode(array_keys($filtres_numeriques)); ?>;
	
	function basculer_filtres() {
		const panneau = document.getElementById('panneau_filtres');
		panneau.hidden = !panneau.hidden;
		document.getElementById('btn_filtres').setAttribute('aria-expanded', String(!panneau.hidden));
	}
	
	function appliquer_filtres() {
		const panneau = document.getElementById('panneau_filtres');
		const cochees = (nom) => Array.from(panneau.querySelectorAll('input[name="' + nom + '"]:checked'), (champ) => champ.value);
		const saisie = (champ) => champ.value === '' ? null : Number(champ.value);
		
		// Un critère numérique est actif dès qu'un minimum ou un maximum est saisi
		const numeriques = [];
		FILTRES_NUMERIQUES.forEach((cle) => {
			const champ_min = document.getElementById('filtre_' + cle + '_min');
			const champ_max = document.getElementById('filtre_' + cle + '_max');
			const min = saisie(champ_min);
			const max = saisie(champ_max);
			const incoherent = min !== null && max !== null && min > max;
			champ_min.classList.toggle('border-danger', incoherent);
			champ_max.classList.toggle('border-danger', incoherent);
			if (min !== null || max !== null) {
				numeriques.push({cle: cle, min: min, max: max});
			}
		});
		const types = cochees('filtre_type');
		const capacites = cochees('filtre_capacite');
		const toutes_capacites = document.getElementById('filtre_mode_toutes').checked;
		
		const cartes = document.querySelectorAll('.col[id^="card_"]');
		let nb_affichees = 0;
		cartes.forEach((carte) => {
			// Les cartes sans la caractéristique (ex. attaque d'une Action) ne passent pas un critère numérique
			let visible = numeriques.every((filtre) => {
				const texte = carte.dataset[filtre.cle];
				if (texte === undefined || texte === '') {
					return false;
				}
				const valeur = Number(texte);
				return (filtre.min === null || valeur >= filtre.min) && (filtre.max === null || valeur <= filtre.max);
			});
			if (visible && types.length > 0) {
				visible = types.includes(carte.dataset.type);
			}
			if (visible && capacites.length > 0) {
				const possedees = (carte.dataset.capacites || '').split(' ');
				visible = toutes_capacites ? capacites.every((c) => possedees.includes(c)) : capacites.some((c) => possedees.includes(c));
			}
			carte.classList.toggle('hors-filtres', !visible);
			if (visible) {
				nb_affichees++;
			}
		});
		
		const nb_actifs = numeriques.length + (types.length > 0 ? 1 : 0) + (capacites.length > 0 ? 1 : 0);
		const badge = document.getElementById('nb_filtres_actifs');
		badge.textContent = nb_actifs;
		badge.hidden = nb_actifs === 0;
		document.getElementById('resultat_filtres').textContent = nb_affichees + ' / ' + cartes.length + ' cartes';
	}
	
	function reinitialiser_filtres() {
		const panneau = document.getElementById('panneau_filtres');
		panneau.querySelectorAll('input[type="number"]').forEach((champ) => { champ.value = ''; });
		panneau.querySelectorAll('input[type="checkbox"]').forEach((champ) => { champ.checked = false; });
		document.getElementById('filtre_mode_une').checked = true;
		appliquer_filtres();
	}
	
	document.getElementById('panneau_filtres').addEventListener('input', appliquer_filtres);
	appliquer_filtres();
</script>

  </body>
</html>
<?php 
}
else {
	header("location:login.php");
}
?>
