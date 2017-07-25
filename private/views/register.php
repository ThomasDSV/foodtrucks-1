<?php

// Tableau des mois
$month = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

// Définition des variables par défaut
$login       = null;
$password    = null;
$firstname   = null;
$lastname    = null;
$gender      = null;
$birth_day   = null;
$birth_month = null;
$birth_year  = null;

// Cas où l'utilisateur envoie le formulaire (méthode POST)
// Contrôle du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $save = true;

    // Recupérer les données de $_POST
    $token       = isset($_POST['token'])          ? $_POST['token']          : null;
    $login       = isset($_POST['login'])          ? $_POST['login']          : null;
    $password    = isset($_POST['password'])       ? $_POST['password']       : null;
    $firstname   = isset($_POST['firstname'])      ? $_POST['firstname']      : null;
    $lastname    = isset($_POST['lastname'])       ? $_POST['lastname']       : null;
    $gender      = isset($_POST['gender'])         ? $_POST['gender']         : null;
    $birth_day   = isset($_POST['birth']['day'])   ? $_POST['birth']['day']   : null;
    $birth_month = isset($_POST['birth']['month']) ? $_POST['birth']['month'] : null;
    $birth_year  = isset($_POST['birth']['year'])  ? $_POST['birth']['year']  : null;


    // Contrôler l'intégrité du token
    if ($_SESSION['token'] !== $token) {
        $save = false;
        setFlashbag("danger", "Le token est invalide.");
    }

    // - Contrôle de l'adresse email
    // --
    // -> ne doit pas être vide
    if (empty($login)) {
        $save = false;
        setFlashbag("danger", "Veuillez saisir une adresse mail.");
    }
    // -> doit avoir la syntaxe d'une adresse email valide
    else if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $save = false;
        setFlashbag("danger", "Veuillez saisir une adresse mail valide.");
    }

    // - Contrôle du mot de passe
    // --
    // -> doit contenir au moins 8 caractères
    if (strlen($password) < 8) {
        $save = false;
        setFlashbag("danger", "Le mot de passe doit faire au minimum huit caractères.");
    }
    // -> doit contenir au plus 16 caractères
    else if (strlen($password) > 16) {
        $save = false;
        setFlashbag("danger", "Le mot de passe doit faire au maximum seize caractères.");
    }
    // -> doit avoir au moins un caractère de type numérique
    else if (!preg_match("/[0-9]/", $password)) {
        $save = false;
        setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère numérique.");
    }
    // -> doit avoir au moins un caractère en majuscule
    else if (!preg_match("/[A-Z]/", $password)) {
        $save = false;
        setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère majuscule.");
    }
    // -> doit avoir au moins un caractère spécial (#@!=+-_)
    else if (!preg_match("/(#|@|!|=|\+|-|_)/", $password)) {
        $save = false;
        setFlashbag("danger", "Le mot de passe doit contenir au moins un caractère spécial. (#@!=+-_)");
    }
    // On crypte le mot de passe
    else {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // - Contrôle du prénom
    // --
    // -> doit être une chaine alphabétique
    if (!preg_match("/[a-z]/i", $firstname)) {
        $save = false;
        setFlashbag("danger", "Vous avez un prénom bizarre.");
    }
    // -> peut contenir un tiret
    else if (!preg_match("/(-)?/", $firstname)) {
        $save = false;
        setFlashbag("danger", "Le prénom ne peut contenir qu'un seul tiret du six.");
    }
    // -> ne doit pas posséder de caractère numérique
    else if (preg_match("/[0-9]/", $firstname)) {
        $save = false;
        setFlashbag("danger", "Le prénom ne peut pas contenir de caractère numérique.");
    }

    // - Contrôle du nom de famille
    // --
    // -> doit être une chaine alphabétique
    if (!preg_match("/[a-z]/i", $firstname)) {
        $save = false;
        setFlashbag("danger", "Vous avez un prénom bizarre.");
    }
    // -> peut contenir un tiret
    else if (!preg_match("/(-)?/", $firstname)) {
        $save = false;
        setFlashbag("danger", "Le prénom ne peut contenir qu'un seul tiret du six.");
    }
    // -> ne doit pas posséder de caractère numérique
    else if (preg_match("/[0-9]/", $firstname)) {
        $save = false;
        setFlashbag("danger", "Le prénom ne peut pas contenir de caractère numérique.");
    }

    // - Contrôle de la date de naissance
    // --
    // -> doit etre une date valide
    if (!(is_numeric($birth_day) && is_numeric($birth_month) && is_numeric($birth_year))) {
        $save = false;
        setFlashbag("danger", "La date saisie n'est pas valide.");
    }

    else if (!checkdate($birth_month, $birth_day, $birth_year)) {
        $save = false;
        setFlashbag("danger", "La date saisie n'est pas valide.");
    }

    else {
        $birthday = $birth_year."-".$birth_month."-".$birth_day;
    }
    
    // -> doit être supérieur à 13 ans au moment de l'inscription
    if (isset($birthday)) {
        $tz  = new DateTimeZone('Europe/Brussels');
        $age = DateTime::createFromFormat('Y-m-d', $birthday, $tz)
            ->diff(new DateTime('now', $tz))
            ->y;

        if ($age < 13) {
            $save = false;
            setFlashbag("danger", "Vous devez avoir au moins treize pour pouvoir vous inscrire.");
        }
    }

    // - Contrôle le genre
    // --
    // -> Le champ doit possèder une valeur (M ou F)
    if ($gender !== "F" && $gender !== "M" && $gender !== "T" && $gender !== "A") {
        $save = false;
        setFlashbag("danger", "Le genre choisi n'est pas valide.");
    }

    // - Contrôle des conditions d'utilisation du service
    // --
    // -> La checkbox doit etre cochée.
    if (!isset($_POST["acceptTerms"])) {
        $save = false;
        setFlashbag("danger", "Vous devez accepter les conditions d'utilisation du service.");
    }

    // - Contrôle l'existence de l'utilisateur dans la BDD
    // -> L'adresse email ne doit pas être présente dans la BDD (table users)
    $query = $pdo->query("SELECT email FROM users;");
    $users = $query->fetchAll();
    if (in_array($login, $users)) {
        $save = false;
        setFlashbag("danger", "L'adresse mail saisi est déjà associé à un compte existant.");
    }

    // On enregistre l'utilisateur dans la BDD
    if ($save) {

        // Enregistre l'utilisateur
        $idUser = addUser(array(
            "firstname" => $firstname,
            "lastname"  => $lastname,
            "login"     => $login,
            "password"  => $password,
            "gender"    => $gender,
            "birthday"  => $birthday
        ));

        // Identification de l'utilisateur
        $_SESSION['user'] = [
            "id"        => $idUser,
            "firstname" => $firstname,
            "lastname"  => $lastname,
            "email"     => $login,
            "roles"     => $default_users_roles
        ];

        // Flashbag Success
        setFlashbag("success", "Vous avez bien été enregistré. Votre id est le ".$idUser.".");

        // Destruction du token
        unset($_SESSION['token']);

        // Redirection de l'utilisateur
        header("location: index.php?page=profile");
        exit;
    }


}

// Cas où l'utilisateur arrive sur la page sans envoyer le formulaire (méthode GET)
else {
    // Definition du token
    $_SESSION['token'] = getToken();
}
?>

<div class="page-header">
    <h2>Inscription</h2>
</div>

<div class="row">
    <div class="col-md-4 col-md-offset-4">

        <?php getFlashbag(); ?>

        <form method="post">

            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

            <div class="form-group">
                <label for="login">Identifiant (adresse email)</label>
                <input  class="form-control" type="text" id="login" name="login" value="<?php echo $login; ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input  class="form-control" type="password" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input  class="form-control" type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>">
            </div>

            <div class="form-group">
                <label for="lastname">Nom de famille</label>
                <input  class="form-control" type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>">
            </div>

            <div class="form-group">
                <label>Genre</label>
                <label><input type="radio" name="gender" value="F" <?php echo $gender == "F" ? "checked" : null; ?>> Féminin </label>
                <label><input type="radio" name="gender" value="M" <?php echo $gender == "M" ? "checked" : null; ?>> Masculin </label>
                <label><input type="radio" name="gender" value="T" <?php echo $gender == "T" ? "checked" : null; ?>> Trans </label>
                <label><input type="radio" name="gender" value="A" <?php echo $gender == "A" ? "checked" : null; ?>> Alien </label>
            </div>

            <div class="form-group">
                <label for="birthday">Date de naissance</label>
                <div class="row">
                    <div class="col-md-4">
                        <select  class="form-control" id="birthday" name="birth[day]">
                            <option value="">Jour</option>
                            <?php for($i=1; $i<=31; $i++): ?>
                                <option value="<?= str_pad($i, 2, 0, STR_PAD_LEFT); ?>"><?php
                                    echo str_pad($i, 2, 0, STR_PAD_LEFT);
                                ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select  class="form-control" name="birth[month]">
                            <option value="">Mois</option>
                            <?php for($i=0; $i<12; $i++): ?>
                                <option value="<?= str_pad(($i+1), 2, 0, STR_PAD_LEFT); ?>"><?php echo $month[$i]; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select  class="form-control" name="birth[year]">
                            <option value="">Années</option>
                            <?php for($i=date('Y'); $i>date('Y')-100; $i--): ?>
                                <option value="<?= $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label>
                    <input type="checkbox" name="acceptTerms">
                    J'accepte les conditions d'utilisation du service.
                </label>
            </div>

            <br/>
            <button type="submit" class="btn btn-info btn-block">Valider</button>
        </form>

        <p class="text-center">
            <a href="index.php?page=login">J'ai déjà un compte</a>
        </p>

    </div>
</div>