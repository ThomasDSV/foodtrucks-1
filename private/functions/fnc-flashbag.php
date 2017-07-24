<?php
// GESTION DES FLASHBAGS

function setFlashbag($state, $message) {

	if (!isset($_SESSION['flashbag'])) {
		$_SESSION['flashbag'] = [];
	}

	array_push($_SESSION['flashbag'], [
		"state" => $state,
		"message" => $message
	]);
}

function getFlashbag() {

	if (!empty($_SESSION['flashbag'])) {

		// Afficher le flashbag
		foreach ($_SESSION['flashbag'] as $value) { ?>
			<div class="alert alert-<?= $value['state']; ?>">
			<?= $value['message']; ?>
			</div> <?php
		}

		// Suppression du message de flashbag
		unset($_SESSION['flashbag']);
	}
}