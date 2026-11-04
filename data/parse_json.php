<?php
session_start();
require_once("../fonctions.php");

$mysqli = db_connexion();


// transforme la couleur en entier correspondant
function couleur_to_int($couleur) {
	
	$int_couleur = 1;
	
	if ($couleur == "Ambre") {
		$int_couleur = 1;
	}
	else if ($couleur == "Améthyste") {
		$int_couleur = 2;
	}
	else if ($couleur == "Émeraude") {
		$int_couleur = 3;
	}
	else if ($couleur == "Rubis") {
		$int_couleur = 4;
	}
	else if ($couleur == "Saphir") {
		$int_couleur = 5;
	}
	else if ($couleur == "Acier") {
		$int_couleur = 6;
	}
	
	return $int_couleur;
}

// transforme le type en entier correspondant
function type_to_int($type, $subtype) {
	
	$int_type = 1;
	
	if ($type == "Personnage") {
		$int_type = 1;
	}
	else if ($type == "Action") {
		if ($subtype == "Chanson") {
			$int_type = 4;
		}
		else {
			$int_type = 2;
		}
	}
	else if ($type == "Objet") {
		$int_type = 3;
	}
	else if ($type == "Lieu"){
		$int_type = 5;
	}
	
	return $int_type;
}

// transforme la rarete en entier correspondant
function rarete_to_int($rarete) {
	
	$int_rarete = 1;
	
	if ($rarete == "Commune") {
		$int_rarete = 1;
	}
	else if ($rarete == "Inhabituelle") {
		$int_rarete = 2;
	}
	else if ($rarete == "Rare") {
		$int_rarete = 3;
	}
	else if ($rarete == "Très Rare") {
		$int_rarete = 4;
	}
	else if ($rarete == "Légendaire") {
		$int_rarete = 5;
	}
	else if ($rarete == "Enchantée"){
		$int_rarete = 6;
	}
	else if ($rarete == "Épique") {
		$int_rarete = 7;
	}
	else if ($rarete == "Iconique") {
		$int_rarete = 8;
	}
	
	return $int_rarete;
}

// Récupérer le numéro de chapitre depuis l'URL
$chapter = isset($_GET['chapter']) ? $_GET['chapter'] : 14;

// Construire le nom du fichier JSON
$filename = 'setdata.' . $chapter . '.json';

// Vérifier si le fichier existe
if (!file_exists($filename)) {
    // Essayer de télécharger depuis le site distant
    $url = 'https://lorcanajson.org/files/current/fr/sets/setdata.' . $chapter . '.json';
    
    // Télécharger le contenu
    $content = @file_get_contents($url);
    
    if ($content !== false) {
        // Sauvegarder le fichier localement
        file_put_contents($filename, $content);
    } else {
        die("Erreur: Impossible de récupérer le fichier $filename depuis $url");
    }
} else {
    // Le fichier existe déjà localement
    $content = file_get_contents($filename);
}

$json = json_decode($content, true);


// Pour chaque carte dans le JSON
foreach($json['cards'] as $carte) {
	
	$rarity = $carte['rarity'];
	
	if ($rarity != "Spécial" && !isset($carte['promoGrouping'])) {
	
		$chapitre	= $chapter;
		$num 		= $carte['number'];
		$artist		= addslashes($carte['artistsText']);
		
		$color = $carte['color'];
		$tab_color = explode("-", $color);
		
		$couleur1	= couleur_to_int($tab_color[0]);
		if (count($tab_color) > 1) {
			$couleur2	= couleur_to_int($tab_color[1]);
		}
		else {
			$couleur2 = 'NULL';
		}
		
		$cout		= $carte['cost'];
		
		if (isset($carte['version'])) {
			$nom		= addslashes($carte['name'])."\n".addslashes($carte['version']);
		}
		else{
			$nom		= addslashes($carte['name']);
		}
		
		$image_rb	= $carte['images']['full'];
		$encrable	= $carte['inkwell']?1:0;
		
		if (isset($carte['lore'])) {
			$lore 		= $carte['lore'];
		}
		else {
			$lore = 'NULL';
		}
		
		if (isset($carte['strength'])) {
			$force		= $carte['strength'];
		}
		else {
			$force = 'NULL';
		}
		
		if (isset($carte['willpower'])) {
			$volonte	= $carte['willpower'];
		}
		else {
			$volonte = 'NULL';
		}
		
		$rarete		= rarete_to_int($rarity);
		if (isset($carte['subtypesText'])) {
			$ssType		= $carte['subtypesText'];
		}
		else {
			$ssType = "";
		}
		
		$type		= type_to_int($carte['type'], $ssType);
		
		if (isset($carte['keywordAbilities'])) {
			$tab_abilites = $carte['keywordAbilities'];
			$insaisissable 	= in_array("Insaisissable", $tab_abilites)?1:0;
			$rempart 		= in_array("Rempart", $tab_abilites)?1:0;
			$soutien 		= in_array("Soutien", $tab_abilites)?1:0;
			$charge			= in_array("Charge", $tab_abilites)?1:0;
			$hors_atteinte	= in_array("Hors d'atteinte", $tab_abilites)?1:0;
			$combattant		= in_array("Combattant", $tab_abilites)?1:0;
			$boost			= in_array("Boost", $tab_abilites)?1:0;
		}
		else {
			$insaisissable 	= 0;
			$rempart 		= 0;
			$soutien 		= 0;
			$charge			= 0;
			$hors_atteinte	= 0;
			$combattant		= 0;
			$boost			= 0;
		}
		
		$image = $carte['images']['full'];
		
		// Vérifier si la carte existe déjà
		$check_sql = "SELECT id_carte FROM cartes WHERE numero_carte = '$num' AND chapitre = $chapitre";
		$result = $mysqli->query($check_sql);
		
		if ($result && $result->num_rows > 0) {
			// La carte existe, on la met à jour
			$update_sql = "UPDATE cartes SET 
				ordre = $num,
				nom_carte = '$nom',
				categorie_carte = '$ssType',
				cout_carte = $cout,
				force_carte = $force,
				volonte_carte = $volonte,
				lore_carte = $lore,
				type_carte = '$type',
				encre = $couleur1,
				encre2 = $couleur2,
				rarete = $rarete,
				encrable = $encrable,
				inssaisissable = $insaisissable,
				rempart = $rempart,
				soutien = $soutien,
				hors_atteinte = $hors_atteinte,
				charge = $charge,
				combattant = $combattant,
				boost = $boost,
				image = '$image',
				artiste = '$artist'
				WHERE numero_carte = '$num' AND chapitre = $chapitre";
			
			$mysqli->query($update_sql);
		} else {
			// La carte n'existe pas, on l'insère
			$insert_sql = "INSERT INTO cartes (numero_carte, ordre, nom_carte, categorie_carte, cout_carte, force_carte, volonte_carte, lore_carte, type_carte, encre, encre2, rarete, encrable, inssaisissable, rempart, soutien, hors_atteinte, charge, combattant, boost, image, artiste, chapitre) 
			VALUES ($num, $num, '$nom', '$ssType', $cout, $force, $volonte, $lore, '$type', $couleur1, $couleur2, $rarete, $encrable, $insaisissable, $rempart, $soutien, $hors_atteinte, $charge, $combattant, $boost, '$image', '$artist', $chapitre)";
			
			$mysqli->query($insert_sql);
		}
	}
	
}

?>