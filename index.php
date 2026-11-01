<?php
session_start();
require_once("fonctions.php");

$mysqli = db_connexion();

function find_user_by_token(string $token, $mysqli)
{
    $tokens = parse_token($token);

    if (!$tokens) {
        return null;
    }
	
	$selector = $tokens[0];

    $sql = "SELECT users.id_user, pseudo_user
            FROM users
            INNER JOIN user_tokens ON user_id = users.id_user
            WHERE selector = '$selector' AND
                expiry > now()
            LIMIT 1";
	$res = $mysqli->query($sql);
	$t_user = $res->fetch_assoc();

    return $t_user;
}

function find_user_token_by_selector(string $selector, $mysqli) {

    $sql = "SELECT id, selector, hashed_validator, user_id, expiry
                FROM user_tokens
                WHERE selector = '$selector' AND
                    expiry >= now()
                LIMIT 1";

    $res = $mysqli->query($sql);

    return $res->fetch_assoc();
}

function token_is_valid(string $token, $mysqli) {
	
	[$selector, $validator] = parse_token($token);
	
	$tokens = find_user_token_by_selector($selector, $mysqli);
	
	if (!$tokens) {
		return false;
	}

	return password_verify($validator, $tokens['hashed_validator']);
}

function log_user_in(array $user): bool
{
    // prevent session fixation attack
    if (session_regenerate_id()) {
        // set email & id in the session
        $_SESSION['email'] = $user['email_user'];
        $_SESSION['id_user'] = $user['id_user'];
        return true;
    }

    return false;
}

function is_user_logged_in($mysqli) {

    // check the session
    if (isset($_SESSION['id_user'])) {
        return true;
    }

    // check the remember_me in cookie
    $token = $_COOKIE["remember_me"]; //filter_input(INPUT_COOKIE, 'remember_me', FILTER_SANITIZE_STRING);

    if ($token && token_is_valid($token, $mysqli)) {

        $user = find_user_by_token($token, $mysqli);

        if ($user) {
            return log_user_in($user);
        }
    }
    return false;
}

if (is_user_logged_in($mysqli)) {
	header("location:liste_collection.php");
}
else {
	header("location:login.php");
}
?>