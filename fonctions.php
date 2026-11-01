<?php

//%%%%%%%%%%%%%%%%%%//
// FONCTION DE DB
//%%%%%%%%%%%%%%%%%%//
function db_connexion() //CONNEXION A LA DB
{
	$connexion = mysqli_connect("localhost","root","","lorcadex");
	
	if (!$connexion) {
		echo "Désolé, connexion à la bdd impossible"; exit(); 
	}
	
	// Pour les soucis d'encodage
	$connexion->query("SET NAMES UTF8");
	
	return $connexion;
}


//%%%%%%%%%%%%%%%%%%%%%%%%%//
// FONCTIONS DIVERSES
//%%%%%%%%%%%%%%%%%%%%%%%%%//

function get_remote_address()
{
	return $_SERVER['REMOTE_ADDR'];
}

function get_date($timestamp) {
	$monthes = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
								'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	$days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
	$date_first = $days[date('w', $timestamp)];
	$date_second = $monthes[date('n', $timestamp)];
	echo $date_first.' '.date('d', $timestamp).' '.$date_second.' '.date('Y', $timestamp).' à '.date('H:i:s', $timestamp); 
}

//%%%%%%%%%%%%%%%%%%%%%%%%%//
// FONCTIONS TOKEN
//%%%%%%%%%%%%%%%%%%%%%%%%%//

function generate_tokens(): array
{
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));

    return [$selector, $validator, $selector . ':' . $validator];
}

function parse_token(string $token): ?array
{
    $parts = explode(':', $token);

    if ($parts && count($parts) == 2) {
        return [$parts[0], $parts[1]];
    }
    return null;
}
  
//**********************************//
// FONCTIONS DE FILTRE CHAINE VALIDE
//**********************************//
function filtre($chaine,$lenghtMin,$lenghtMax)
{
	$lenghtBefore = strlen($chaine);
	
	$caracteres = array(
	"," => "error",
	";" => "error",
	":" => "error",
	"!" => "error",
	"@" => "error",
	"|" => "error",
	"=" => "error",
	"+" => "error",
	"/" => "error",
	"*" => "error",
	"#" => "error",
	"'" => "error",
	"&" => "error",
	);
	$chaine = strtr($chaine,$caracteres);	
	$chaine = trim(htmlentities($chaine) );
	$lenghtAfter = strlen($chaine);
	
	return !(($lenghtBefore < $lenghtAfter) || ($lenghtAfter < $lenghtMin) || ($lenghtBefore > $lenghtMax));
}

//**************************//
// FONCTIONS DE FILTRE MAIL
//**************************//
function filtremail($chaine)
{
	return( strpos($chaine,"@") > 0 && strrpos($chaine,".") > strpos($chaine,"@") );
}

//************************************//
// FONCTIONS AFFICHAGE DANS UN TABLEAU
//************************************//
function afficheResultat($resultat)
{
	if (mysql_num_rows($resultat) == 0) 
	{
		echo "<B><FONT color='black'>Aucun résultat trouvé</B></FONT>"; 
		exit();
	}
	$cols = 4;
	echo "<div align=\"center\"><TABLE BORDER=1>";
	$nbAttr = mysql_num_fields($resultat);
	$rows = $nbAttr / $cols;

	echo "<TR>";
	for ($i=0; $i < $nbAttr; $i++) 
		echo "<TH>" . mysql_field_name($resultat, $i) . "</TH>\n";
	echo "</TR>";

	while($tabAttr = mysql_fetch_row($resultat))
	{
		echo "<TR>";
		for($i=0; $i<$nbAttr; $i++) 
			echo "<TD>" . $tabAttr[$i] . "</TD>";
		echo "</TR>";
	}
	echo "</TABLE></div>";
}

//***************************************//
// FONCTIONS DE CREATION DE JAUGE
//***************************************//

function get_color_pv ($pv, $pv_max){ 
	$pourc = $pv * 100 / $pv_max;
	if ($pourc < 25)
		$color = "#FF0000"; //rouge
	elseif ($pourc < 50)
		$color = "#ff9900"; //orange
	elseif ($pourc < 75)
		$color = "#FFFF00"; //jaune
	else 
		$color = "#00FF00"; //vert
	echo "<font color=\"$color\">$pv</font>";
}

function pourc_jauge ($p, $p_max) {
	$pourc = $p * 100 / $p_max;
	
	return $pourc;
}

function affiche_jauge ($p, $p_max){ 
	$pourc = pourc_jauge($p, $p_max);
	
	if ($pourc < 20)
		$color = "#FF0000"; //rouge
	elseif ($pourc < 50)
		$color = "#ff9900"; //orange
	elseif ($pourc < 80)
		$color = "#FFFF00"; //jaune
	else 
		$color = "#00FF00"; //vert
	
	echo '<table border=1 width=100% >';
	echo '	<tr style="height:5px;">';
	echo '		<td style="padding:0">';
	echo '			<table align="center" width=100% border=0>';
	echo "				<tr>";
	if ($pourc > 0) {
		echo "					<td width=\"$pourc%\" height=5 bgcolor=\"$color\" style='padding:0'></td>";
	}
	echo "					<td width=\"100-$pourc%\"bgcolor='#ffffff' height=10 style='padding:0'></td>";
	echo "				</tr>";
	echo "			</table>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
}

function affiche_jauge_inverse ($p, $p_max){ 
	$pourc = $p * 100 / $p_max;
	if ($pourc < 25)
		$color = "#00FF00"; //vert
	elseif ($pourc < 50)
		$color = "#ffff00"; //jaune
	elseif ($pourc < 75)
		$color = "#ff9900"; //orange
	else 
		$color = "#FF0000"; //rouge
	echo '<table border=1 width=202 height=8 cellpadding=1 cellspacing=0 bgcolor=0 bordercolor="black">';
		echo '<tr><td>';
				echo '<table align="center" width=100% height=6 border=0 cellpadding=0 cellspacing=0>';
					if ($pourc > 0)
						echo "<tr><td width=\"$pourc%\" height=6 bgcolor=\"$color\"><font size=1>&nbsp;</font></td>";
						echo "<td width=\"100-$pourc%\"bgcolor='#ffffff' height=6></td>";
					echo "</tr>";
				echo "</table>";
		echo "</td></tr>";
	echo "</table>";
	return $pourc;
}
// autres

// Fonction qui récupére l'adresse IP de l'utilisateur
function realip() {
   if (isSet($_SERVER)) {
    if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
     $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
     $realip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
     $realip = $_SERVER["REMOTE_ADDR"];
    }

   } else {
    if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
     $realip = getenv( 'HTTP_X_FORWARDED_FOR' );
    } elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
     $realip = getenv( 'HTTP_CLIENT_IP' );
    } else {
     $realip = getenv( 'REMOTE_ADDR' );
    }
   }
   return $realip;
}

/**
  * Fonction qui vérifie si un perso est admin
  * @param $id_perso	: l'identifiant du perso
  * @return Bool		: Si oui ou non le perso est admin
  */
function admin_perso($mysqli, $id_perso){
	$sql = "SELECT admin_perso FROM joueur, perso WHERE id_perso='$id_perso' AND perso.idJoueur_perso  = joueur.id_joueur";
	$res = $mysqli->query($sql);
	$t_admin = $res->fetch_assoc();
	
	return $t_admin["admin_perso"];
}


/**
  * Fonction qui retourne la configuration de disponibilité du jeu
  * @return Bool	: Si oui ou non le jeu est disponible
  */
function config_dispo_jeu($mysqli){
	$sql = "SELECT valeur_config FROM config_jeu WHERE code_config='disponible'";
	$res = $mysqli->query($sql);
	$t_dispo = $res->fetch_assoc();
	
	return $t_dispo["valeur_config"];
}

function genererChaineAleatoire($taille) {
	// Initialisation des caractères utilisables
    $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

	$password = "";
	
    for($i=0;$i<$taille;$i++) {
        $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
    }
		
    return $password;
}

function envoi_mail_mdp($mysqli, $mail, $new_mdp, $code_activation) {

	// Headers mail
	$headers ='From: "Nord VS Sud"<nordvssud@no-reply.fr>'."\n";
	$headers .='Reply-To: nordvssud@no-reply.fr'."\n";
	$headers .='Content-Type: text/plain; charset="utf-8"'."\n";
	$headers .='Content-Transfer-Encoding: 8bit';
	
	// Destinataire du mail
	$destinataire = $mail;
	
	// Titre du mail
	$titre = 'Mot de passe perdu';
	
	$lien_activation = "http://loka.zd.fr/nvs/mdp_perdu.php?code=".$code_activation;
	
	// Contenu du mail
	$message = "Votre nouveau mot de passe est $new_mdp \r\nAfin d'activer ce nouveau mot de passe, veuillez cliquer sur ce lien : $lien_activation \r\nLe nouveau mot de passe ne sera actif qu'après activation. \r\nSi vous n'avez pas demandé à réinitailiser votre mot de passe, veuillez ne pas prendre en compte ce mail.";
	
	// Envoie du mail
	mail($destinataire, $titre, $message, $headers);
}
?>