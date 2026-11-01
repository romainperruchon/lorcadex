<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();


if(isset($_POST['creer'])) {
		
	$email_user 	= $_POST['email_user'];
	$mdp_user 		= $_POST['mdp_user'];
					
	$sql = "SELECT email_user FROM users WHERE email_user='".$email_user."'";
	$resultat_user = $mysqli->query($sql);
	
	if (!filtremail($email_user)) {
		echo "<center><font color='red'>Erreur: Email incorrect</font></center><br /><br />";
	}	
	elseif ($resultat_user->num_rows != 0) {
		echo "<center><font color='red'>Erreur: Un compte a déjà été créé avec cet email</font></center><br /><br />";
	}
	else {
		if($_POST['creation']=="ok") {
							
			// Insertion user
			$mdp_user = md5($mdp_user);
			
			$insert_u = "INSERT INTO users (email_user, mdp_user) VALUES ('$email_user', '$mdp_user')";
			$res_u = $mysqli->query($insert_u);
			$id_user = $mysqli->insert_id;
			
			$_SESSION["id_user"] = $id_user;
			
			header("location:collection.php?creation=ok");
		}
	}
}
?>
	<html>
	<head>
		<title>Lorcadex - Inscription</title>
		
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	
	<body background="">
		
		<div class="row">
			<div class="col-12" align="center">
				<font color="blue" size=5 face="Verdana, Arial, Helvetica, sans-serif"><b>INSCRIPTION</b></font><br/><br/>
				<img class="mb-4" src="./img/logo.png" alt="" width="105" height="144">
			</div>
		</div>
		
		<div class="row">
			<div class="col-12" align="center">
				<form method="post" action="inscription.php">
					Entrez votre email:<br/>
					<input type="text" name="email_user" value="" size="20" maxlength="60">
					<br/>
					Entrez votre mot de passe:<br/>
					<input type="password" name="mdp_user" value="" size="20" maxlength="20">
					<br/>
					<input name="creation" type="hidden" value="ok">
					<input type="submit" name="creer" value="Cr&eacute;er" class='btn btn-success'>
					<br/><br/>
					</font>
				
					<table border="0">
					  
					</table>
				</form>
			</div>
		</div>
	
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	
	</body>
</html>