<?php

// Liste des pages du site

$router = [
//	nom_route          => ["page",                   "accès restreint ?"];

	// Page d'accueil
	"home"             => ["home.php",             false],

	// Foodtrucks
	"foodtrucks"       => ["foodtrucks.php",       false],
	"foodtruck"        => ["foodtruck.php",        false],
	"add-foodtruck"    => ["add-foodtruck.php",    true ],
	"edit-foodtruck"   => ["edit-foodtruck.php",   true ],
	"delete-foodtruck" => ["delete-foodtruck.php", true ],

	// Contact
	"contact"          => ["contact.php",          false],

	// Utilisateurs
	"profile"          => ["profile.php",          true ],
	"settings"         => ["settings.php",         true ],
	"register"         => ["register.php",         false],
	"login"            => ["login.php",            false],
	"lostpwd"          => ["lostpwd.php",          false],
	"renewpwd"         => ["renewpwd.php",         false],
	"logout"           => [null,                   true ],

	// Page d'erreur
	"404"              => ["404.php",              false]
];