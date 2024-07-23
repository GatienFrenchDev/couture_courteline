<?php

require_once "classes.php";
require_once "fonctions.php";

if (!isset($_GET["token"])) {
    http_response_code(403);
    header("Location: connexion.php");
    exit;
};

$token = getToken($_GET["token"]);

if (is_null($token)) {
    http_response_code(403);
    header("Location: connexion.php");
    exit;
}

if (!$token->isValide()) {
    http_response_code(403);
    header("Location: connexion.php");
    exit;
}

$utilisateur = $token->getUtilisateur();

if($utilisateur->getRole()=='admin'){
    http_response_code(403);
    header("Location: admin/accueil.php?token=".$token->getToken());
    exit;
}

if (isset($_GET["id_cours"]) && appartient($utilisateur->getIdUtilisateur(), $_GET["id_cours"])) {
    createAbsence($utilisateur->getIdUtilisateur(), $_GET["id_cours"]);
    $cours = getCours($_GET["id_cours"]);
    if ($cours->getDate()->getTimestamp() - time() >= 86400) {
        changeNbrRattrapage($utilisateur->getIdUtilisateur(), $utilisateur->getNbrRattrapage() + 1);
        $utilisateur->setRattrapage($utilisateur->getNbrRattrapage() + 1);
    }
}

$allcours = getAllCoursFromIdUtilisateur($utilisateur->getIdUtilisateur());
$bouton = "Prévenir mon abscence";
$page = "absences";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prochains Cours</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="min-h-screen">
        <?php
        require "components/navbar.php";
        require "components/cours.php";
        ?>
    </div>
    <?php
    require './components/footer.php';
    ?>

</body>

</html>