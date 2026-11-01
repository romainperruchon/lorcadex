<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

if (isset($_SESSION["id_user"])) {
	
	$id_user = $_SESSION["id_user"];
	
	if (isset($_POST['add_card']) && isset($_POST['id_card']) && $_POST['id_card'] != "") {
			
		$nb_card 	= $_POST['add_card'] + 1;
		$id_card	= $_POST['id_card'];
		
		// On vérifie si l'utilisateur possède déjà cette carte 
		$sql_v = "SELECT * FROM user_as_carte WHERE id_user='$id_user' AND id_carte='$id_card' AND foil='0'";
		$res_v = $mysqli->query($sql_v);
		
		if ($res_v->num_rows != 0) {			
			$sql_i = "UPDATE user_as_carte SET quantite=quantite+1 WHERE id_user='$id_user' AND id_carte='$id_card' AND foil='0'";			
		}
		else {
			
			$sql_i = "INSERT INTO user_as_carte (id_user, id_carte, quantite, foil) VALUES ('$id_user', '$id_card', '$nb_card', 0)";
		}
		$mysqli->query($sql_i);
	}
	
	if (isset($_POST['add_card_foil']) && isset($_POST['id_card']) && $_POST['id_card'] != "") {
		
		$nb_card 	= $_POST['add_card_foil'] + 1;
		$id_card	= $_POST['id_card'];
		
		// On vérifie si l'utilisateur possède déjà cette carte 
		$sql_v = "SELECT * FROM user_as_carte WHERE id_user='$id_user' AND id_carte='$id_card' AND foil='1'";
		$res_v = $mysqli->query($sql_v);
		
		if ($res_v->num_rows != 0) {
			$sql_i = "UPDATE user_as_carte SET quantite=quantite+1 WHERE id_user='$id_user' AND id_carte='$id_card' AND foil='1'";
		}
		else {
			$sql_i = "INSERT INTO user_as_carte (id_user, id_carte, quantite, foil) VALUES ('$id_user', '$id_card', '$nb_card', 1)";
		}
		$mysqli->query($sql_i);
	}
}
?>