<?php
require_once 'modele/GenreDal.class.php';
require_once 'include/_reference.lib.php';

if(!isset($_REQUEST["action"])){
    $action = "listerGenres";
}
else{
    $action = $_REQUEST["action"];
}

// variables pour la gestion des messages
$titrePage = 'Gestion des genres';
// variables pour la gestion des erreurs
$tabErreurs = array();
$hasErrors = false;
// variables pour la gestion des messages
$msg = ''; // message passé ç la vue _v_afficherMessage
$lien = ''; // message passé ç la vue _v_afficherErreurs

// initialisation des variables
$strCode = '';
$strLibelle = '';

switch($action){
    case 'listerGenres' : {
            $lesGenres = GenreDal::loadGenres(1);
            $nbGenres = count($lesGenres);
        include 'vues/v_listerGenres.php';
    }
    break;

    case 'consulterGenre' : {
            // récuperation du code passé dans l'URL
        if (isset($_GET["id"])){
            $strCode = strtoupper(htmlentities($_GET["id"]));
            // appel de la méthode du modèle
            $leGenre = GenreDal::loadGenreByID($strCode);
            if($leGenre == NULL){
                $tabErreurs[] = 'Ce genre n\'existe pas !';
                $hasErrors = true;
            }
        }
        else {
            // ^pas d'id dans l'url ni clic sur Valider : c'est Anormal
            $tabErreurs[] = "Aucun genre n'a été transmis pour consultation !";
            $hasErrors = true;
        }

        if ($hasErrors){
            include 'vues/_v_afficherErreurs.php';
        }
        else{
            include 'vues/v_consulterGenre.php';
        }
    }
    break;

    case 'ajouterGenre' : {
    //traitement de l'option : saisie ou validation ?
    if (isset($_GET["option"])) {
        $option = htmlentities($_GET["option"]);
    } else {
        $option = 'saisirGenre';
    }

    switch ($option) {
        case 'saisirGenre' : {
                include 'vues/v_ajouterGenre.php';
            }
            break;
        case 'validerGenre' :{
             if (isset($_POST["cmdValider"])) {
        // récupération du libellé
        if (!empty($_POST["txtLibelle"])) {
            $strLibelle = ucfirst(htmlentities($_POST["txtLibelle"]));
        }
        if (!empty($_POST["txtCode"])) {
            $strCode = strtoupper(htmlentities($_POST["txtCode"]));
        }
        // test zones obligatoires
        if (!empty($strCode) and !empty($strLibelle)) {
            // les zones obligatoires sont présentes
            // tests de cohérence
            // contrôle d'existence d'un genre avec le même code
            $doublon = GenreDal::loadGenreByID($strCode);
            if ($doublon != NULL) {
                 // signaler l'erreur
                $tabErreurs[] = 'Il existe déjà un genre avec ce code !';
                $hasErrors = true;
            }
        }
        else {
            if (empty($strCode)) {
                $tabErreurs[] = "Le code doit être renseigné !";
            }
            if (empty($strLibelle)) {
                $tabErreurs[] = "Le libellé doit être renseigné !";
            }
            $hasErrors = true;
        }
                if (!$hasErrors){
                    $res = GenreDal::addGenre($strCode, $strLibelle);
                    if($res > 0) {
                        $msg = 'Le genre'
                                .$strCode.'-'
                                .$strLibelle.'a été ajouté';
                        $leGenre = GenreDal::loadGenreByID($strCode);
                        include 'vues/_v_afficherMessage.php';
                        $leGenre = new Genre($strCode, $strLibelle);
                        include 'vues/v_consulterGenre.php';
                    }
                    else {
                        $tabErreurs[] = 'Une erreur s\'est produite dans l\'operation d\'ajout!';
                        $hasErrors = true;
                        }
                    }
                    if ($hasErrors){
                        $msg = "L'opération d'ajout n'a pas pu être menée à terme en raison des erreurs suivantes :";
                        $lien = '<a href="index.php?uc=gererGenres&action=ajouterGenre">Retour à la saisie</a>';
                        include 'vues/_v_afficherErreurs.php';
                    }
                }
            } break;
        }
        break;
    }
    break;


    case 'modifierGenre' : {
        // initialisation des variables
        $tabErreurs = array();
        $hasErrors = false;
        $strLibelle = '';
        // creer l'objet Genre
        if (isset($_REQUEST["id"])){
            $strCode = strtoupper(htmlentities($_REQUEST["id"]));
            // appel de la méthode du modèle
            $leGenre = GenreDal::loadGenreByID($strCode);
            if($leGenre == NULL){
                $tabErreurs[] = 'Ce genre n\'existe pas !';
                $hasErrors = true;
            }
        }
        else {
            // pas d'id dans l'url ni clic sur Valider : c'est Anormal
            $tabErreurs[] = "Aucun genre n'a été transmis pour validation !";
            $hasErrors = true;
        }
        //traitement de l'option : saisie ou validation ?
        if (isset($_GET["option"])) {
            $option = htmlentities($_GET["option"]);
        } else {
            $option = 'saisirGenre';
        }
        switch ($option){
            case'saisirGenre' : {
                if(!$hasErrors){
                    // affichage de la vue de modification
                    // l'objet Genre $leGenre est connu
                    include ("vues/v_modifierGenre.php");
                } else {
                    $msg = "L'opération de modification n'a pas pu être mené";
                    include 'vues/_v_afficherErreurs.php';
                }
            } break;
            case'validerGenre' : {
                if(!$hasErrors){
                    if(isset($_POST["cmdValider"])){
                        if (!empty($_POST["txtLibelle"])) {
                            $strLibelle = ucfirst(htmlentities($_POST["txtLibelle"]));
                        }
                        else {
                           $tabErreurs[] = "Le libelle doit être renseigné !";
                           $hasErrors = true;
                        }
                        if (!$hasErrors) {
                            // mise à jour de la base données
                            $leGenre->setLibelle($strLibelle);
                            $res = GenreDal::setGenre($leGenre);
                            if($res > 0) {
                                $msg = 'Le genre '
                                        .$leGenre->getCode().'-'
                                        .$leGenre->getLibelle().'a été ajouté';
                                include 'vues/_v_afficherMessage.php';
                                include 'vues/v_consulterGenre.php';
                            } else {
                                $tabErreurs[] = "Une erreur s'est produite dans l'opération de modification";
                                $hasErrors = true;
                            }
                        }
                    }
                }
                if($hasErrors){
                    $msg = "L'opération de modification n'a pas pu être menée à terme";
                    include 'vues/_v_afficherErreurs.php';
                }
            }
        }
        break;
    }
    break;

    case 'supprimerGenre' : {
    if (isset($_GET["id"])){
            $strCode = strtoupper(htmlentities($_GET["id"]));
            // appel de la méthode du modèle
            $leGenre = GenreDal::loadGenreByID($strCode);
            if($leGenre == NULL){
                $tabErreurs[] = 'Ce genre n\'existe pas !';
                $hasErrors = true;
            }
            else {
                // recherche des ouvrages de ce genre
                if (GenreDal::countOuvragesGenre($leGenre->getCode()) > 0){
                $tabErreurs[] = "Il existe des ouvrages qui référencent ce genre, suppression impossible !";
                $hasErrors = true;
                }
            }
        }
        else {
            // pas d'id dans l'url ni clic sur Valider : c'est anormal
            $tabErreurs[] = "Aucun genre n'a été transmis pour la suppression !";
            $hasErrors = true;
        }
        if (!$hasErrors){
            $res = GenreDal::delGenre($leGenre->getCode()); // $res contient le nombre de lignes
            if ($res > 0){
                $msg = 'Le genre '
                        . $leGenre->getCode() . 'a été supprimé';
                include 'vues/_v_afficherMessage.php';
                // affichage de la liste des genres
                $lesGenres = GenreDal::loadGenres(1);
                // afficher le nombre de genres
                $nbGenres = count($lesGenres);
                include 'vues/v_listerGenres.php';
            } else {
                $tabErreurs[] = "Une erreur s\'est produite dans l\'opération de suppression !";
                $hasErrors = true;
            }
        }
        if ($hasErrors){
            $msg = "L'opération de suppression n'a pas pu être menée à terme en raison des erreurs suivantes :";
            $lien = '<a href="index.php?uc=gererGenres">Retour à la saisie</a>';
            include 'vues/_v_afficherErreurs.php';
        }
    }
    break;

    default : include 'vues/_v_home.php';
}
