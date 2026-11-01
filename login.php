<?php
session_start();

require_once("fonctions.php");

$mysqli = db_connexion();

if(isset ($_POST['email']) && isset ($_POST['password'])) {
	
	// recuperation des variables post
	$email 	= $_POST['email'];
	$mdp 	= $_POST['password'];
	
	// test champs vide
	if ( trim($email) == "" || trim($mdp) == "") { 
		echo "<center><font color='red'>Merci de remplir tous les champs</font><br />";
		echo "<a href=\"index.php\" class='btn btn-primary'>retour</a></center>";
	}
	else {
		if (ctype_digit($email) || strpos($email,'--') !== false) {
			echo "<center><font color='red'>Le mail ne respecte pas les normes de nommage ! Veuillez contacter l'admin ou un animateur sur le Discord LorcaNantes !</font><br />";
			echo "<a href=\"index.php\" class='btn btn-primary'>retour</a></center>";
		}
		else {
			
			// passage du mdp en md5
			$mdp = md5($mdp); 
					
			// 
			$sql = "SELECT id_user, mdp_user FROM users WHERE email_user='$email'";		
			$res = $mysqli->query($sql);
			$t_user = $res->fetch_assoc();
			
			$mdp_u = $t_user["mdp_user"];
					
			if ($mdp_u != null && $mdp_u != "") {
					
				if($mdp == $mdp_u){
							
					$_SESSION['email'] = $t_user['email_user'];
					$id_user = $_SESSION["id_user"] = $t_user["id_user"];
					
					[$selector, $validator, $token] = generate_tokens();

					// remove all existing token associated with the user id
					$sql = "DELETE FROM user_tokens WHERE user_id = '$id_user'";
					$mysqli->query($sql);

					// set expiration date
					$expired_seconds = time() + 60 * 60 * 24 * 30;

					// insert a token to the database
					$hash_validator = password_hash($validator, PASSWORD_DEFAULT);
					$expiry = date('Y-m-d H:i:s', $expired_seconds);

					$sql = "INSERT INTO user_tokens(user_id, selector, hashed_validator, expiry)
							VALUES('$id_user', '$selector', '$hash_validator', '$expiry')";
					$mysqli->query($sql);
					
					setcookie('remember_me', $token, $expired_seconds);
							
					header("location:liste_collection.php");
				}
				else {
					echo "<center><font color='red'>mot de passe incorrect<br />";
					echo "<a href=\"index.php\" class='btn btn-primary'>[ retour ]</a></center>";
				}
			}
			else {
				echo "<center><h1><font color='red'>Aucun compte rattaché à cet email n'a été trouvé</font></h1><br />";
				echo "<a href=\"index.php\" class='btn btn-primary'>[ retour ]</a></center>";
			}
		}
	}
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
    <title>Lorcadex - Login</title>
	
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">

	<link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<style>
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
    </style>
	
	<!-- Custom styles for this template -->
    <link href="login.css" rel="stylesheet">
  </head>
  <body>	
	<main class="form-signin w-100 m-auto">
	  <form action="login.php" method="post" name="login" id="login">
		<img class="mb-4" src="./img/logo.png" alt="" width="105" height="144">
		<h1 class="h3 mb-3 fw-normal">LOGIN</h1>

		<div class="form-floating">
		  <input type="email" class="form-control" id="email" name='email' placeholder="name@example.com">
		  <label for="email">Email address</label>
		</div>
		<div class="form-floating">
		  <input type="password" class="form-control" id="password" name='password' placeholder="Password">
		  <label for="password">Password</label>
		</div>

		<div class="form-check text-start my-3">
		  <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
		  <label class="form-check-label" for="flexCheckDefault">
			Remember me
		  </label>
		</div>
		<input type="submit" name="Submit" value="Se connecter">
		<a href="inscription.php">Pas encore inscrit ?</a>
		<p class="mt-5 mb-3 text-body-secondary">&copy; 2023</p>
	  </form>
	</main>
	
	<script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>