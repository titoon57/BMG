<?php
require_once "include/API/_GoogleBooks.php";

if (!(isset($_REQUEST['action']))) {
    $action = 'saisirRecherche';
} else {
    $action = $_REQUEST['action'];
}
$titrePage = 'Rechercher un ouvrage';

$tabErreurs = array();
$hasErrors = false;

$msg=''; 
$lien='';

// API 
$books = new GoogleBooks('AIzaSyAjv74bLlZ-BlRuBtgQS9kjQaKOVXSIvAU');

switch ($action) {
    case 'saisirRecherche' : {
        //$theme = $books->getVolumes('harry');
        include 'vues/v_rechercheOuvrage.php';
    }
    break;

    case 'validerRecherche' : {
        include 'vues/v_listeRecherches.php';
    }
    break;
} 

?>
