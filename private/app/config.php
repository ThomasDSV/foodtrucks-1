<?php

/*	----------------------------
	PARAMETRES CONNEXION BDD
----------------------------  */

// Adresse du serveur BdD
$host = "127.0.0.1";

// Nom d'utilisateur de la BdD
$user = "root";

// Mot de passe associé à l'utilisateur
$pass = "";

// Nom de la BdD sur laquelle on va travailler
$database = "foodtrucks";


/*	--------------
	CONSTANTES
--------------  */

// Execution mode
define("MODE", "dev"); // dev | prod

// Répertoires "views"
define("VIEWS_DIRECTORY", "../private/views/");
define("FUNCTIONS_DIRECTORY", "../private/functions/");
define("MODELS_DIRECTORY", "../private/models/");

// -- Regular Expressions
// Filter for controllers/autoload instruction
define("FUNCTIONS_FILES", "/^fnc-.*\.php$/i");

// Filter for models/autoload instruction
define("MODELS_FILES", "/^mdl-.*\.php$/i");

// Page par défaut (paramètre "page" non défini/rempli dans l'URL)
define("DEFAULT_PAGE", "home");

/*	----------------
	DEFAULT VARS
----------------  */

// $default_page = "home";