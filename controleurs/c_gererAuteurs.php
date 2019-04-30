<?php
require_once 'modele/AuteurDal.class.php';
require_once 'include/_reference.lib.php';

if(!isset($_REQUEST["action"])){
    $action = "listerAuteurs";
}
else{
    $action = $_REQUEST["action"];
}

$titrePage = 'Gestion des auteurs';

$tabErreurs = array();
$hasErrors = false;

// variables pour la gestion des messages
$msg=''; //msg passé à v_afficherMessages
$lien=''; //msg passé à v_afficherErreurs


// initialisation des variables
$strId = 0;
$strNom = '';
$strPrenom = '';
$strAlias = '' ;
$strNotes = '' ;

switch($action){
    case 'listerAuteurs' : {
        $lesAuteurs = AuteurDal::loadAuteurs(1);
        $nbAuteurs = count($lesAuteurs);
        include("vues/v_listerAuteurs.php");
    }
    break;

    case 'consulterAuteur' : {
            // récuperation du code passé dans l'URL
        if (isset($_GET["id"])){
            $strId = strtoupper(htmlentities($_GET["id"]));
            // appel de la méthode du modèle
            $lAuteur = AuteurDal::loadAuteurByID($strId);
            if($lAuteur == NULL){
                $tabErreurs[] = 'Cet auteur n\'existe pas !';
                $hasErrors = true;
            }
        }
        else {
            // ^pas d'id dans l'url ni clic sur Valider : c'est Anormal
            $tabErreurs[] = "Aucun auteur n'a été transmis pour consultation !";
            $hasErrors = true;
        }

        if ($hasErrors){
            include 'vues/_v_afficherErreurs.php';
        }
        else{
            include 'vues/v_consulterAuteur.php';
        }
    }
    break;

    case 'ajouterAuteur' : {
        if(isset($_GET["option"])){
            $option = htmlentities($_GET["option"]);
        }
        else {
            $option = "saisirAuteur";
        }
        switch($option) {
            case 'saisirAuteur' : {
                include 'vues/v_ajouterAuteur.php';
            }
            break;
            case 'validerAuteur' : {
                if(isset($_POST["cmdValider"])){
                    if(!empty($_POST["txtNom"])){
                        $strNom = ucfirst(htmlentities($_POST["txtNom"]));
                    }

                    if(!empty($_POST["txtPrenom"])){
                        $strPrenom = ucfirst(htmlentities($_POST["txtPrenom"]));
                    }

                    if(!empty($_POST["txtAlias"])){
                        $strAlias = ucfirst(htmlentities($_POST["txtAlias"]));
                    }
                    else{
                        $strAlias = NULL;
                    }

                    if(!empty($_POST["txtNotes"])){
                        $strNotes = ucfirst(htmlentities($_POST["txtNotes"]));
                    }
                    else{
                        $strNotes = NULL;
                    }
                    // test zones obligatoires
                    if(!empty($strNom) && !empty($strPrenom)){
                        $doublon = AuteurDal::loadAuteurByID($strId);
                        if($doublon != NULL){
                            $tabErreurs[] = 'Il existe déjà un Auteur avec cet id !';
                            $hasErrors = true;
                        }
                    }
                    else{
                        if(empty($strNom)) {
                            $tabErreurs[] = "Le nom doit être renseigné ! ";
                        }
                        if(empty($strPrenom)) {
                            $tabErreurs[] = "Le prenom doit être renseigné ! ";
                        }
                        $hasErrors = true;
                    }
                    if (!$hasErrors){
                        $res = AuteurDal::addAuteur($strNom, $strPrenom, $strAlias, $strNotes);
                        if($res > 0) {
                            $msg = 'L\'Auteur '
                                    .$strNom.' '
                                    .$strPrenom.' a été ajouté';
                            include 'vues/_v_afficherMessage.php';
                            //include 'vues/v_consulterAuteur.php';
                        }
                        else {
                            $tabErreurs[] = 'Une erreur s\'est produite dans l\'operation d\'ajout!';
                            $hasErrors = true;
                        }
                    }
                }
            }
        }
    }
    break;

    case 'modifierAuteur' : {
        // initialisation des variables
        $tabErreurs = array();
        $hasErrors = false;
        $strNom = "";
        // creer l'objet Auteur
        if (isset($_REQUEST["id"])){
            $strId = strtoupper(htmlentities($_REQUEST["id"]));
            // appel de la méthode du modèle
            $lAuteur = AuteurDal::loadAuteurByID($strId);
            if($lAuteur == NULL){
                $tabErreurs[] = 'Cet auteur n\'existe pas !';
                $hasErrors = true;
            }
        }
        else {
            // pas d'id dans l'url ni clic sur Valider : c'est Anormal
            $tabErreurs[] = "Aucun auteur n'a été transmis pour validation !";
            $hasErrors = true;
        }
        //traitement de l'option : saisie ou validation ?
        if (isset($_GET["option"])) {
            $option = htmlentities($_GET["option"]);
        } else {
            $option = 'saisirAuteur';
        }
        switch ($option){
            case'saisirAuteur' : {
                if(!$hasErrors){
                    // affichage de la vue de modification
                    // l'objet Auteur $lAuteur est connu
                    include ("vues/v_modifierAuteur.php");
                } else {
                    $msg = "L'opération de modification n'a pas pu être mené";
                    include 'vues/_v_afficherErreurs.php';
                }
            } break;
            case'validerAuteur' : {
                if(!$hasErrors){
                    if(isset($_POST["cmdValider"])){
                        if (!empty($_POST["txtNom"])) {
                            $strNom = ucfirst(htmlentities($_POST["txtNom"]));
                        }
                        else {
                           $tabErreurs[] = "Le nom doit être renseigné !";
                           $hasErrors = true;
                        }

                        if (!empty($_POST["txtPrenom"])) {
                            $strPrenom = ucfirst(htmlentities($_POST["txtPrenom"]));
                        }
                        else {
                           $strPrenom = NULL;
                        }

                        if (!empty($_POST["txtAlias"])) {
                            $strAlias = ucfirst(htmlentities($_POST["txtAlias"]));
                        }
                        else {
                           $strAlias = NULL;
                        }

                        if (!empty($_POST["txtNotes"])) {
                            $strNotes = ucfirst(htmlentities($_POST["txtNotes"]));
                        }
                        else {
                           $strNotes = NULL;
                        }


                        if (!$hasErrors) {
                            // mise à jour de la base données
                            $lAuteur->setNom($strNom);
                            $lAuteur->setPrenom($strPrenom);
                            $lAuteur->setAlias($strAlias);
                            $lAuteur->setNotes($strNotes);
                            $res = AuteurDal::setAuteur($lAuteur);
                            if($res > 0) {
                                $msg = 'L\'auteur '
                                        .$lAuteur->getNom().'-'
                                        .$lAuteur->getPrenom().'a été modifié';
                                include 'vues/_v_afficherMessage.php';
                                include 'vues/v_consulterAuteur.php';
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

    case 'supprimerAuteur' : {
        if (isset($_GET["id"])){
            $strId = strtoupper(htmlentities($_GET["id"]));
            // appel de la méthode du modèle
            $lAuteur = AuteurDal::loadAuteurByID($strId);
            if($lAuteur == NULL){
                $tabErreurs[] = 'Cet auteur n\'existe pas !';
                $hasErrors = true;
            }
            else {
                // recherche des ouvrages de ce genre
                if (AuteurDal::countOuvragesAuteur($lAuteur->getId()) > 0){
                $tabErreurs[] = "Il existe des auteurs qui référencent cet auteur, suppression impossible !";
                $hasErrors = true;
                }
            }
        }
        else {
            // pas d'id dans l'url ni clic sur Valider : c'est anormal
            $tabErreurs[] = "Aucun auteur n'a été transmis pour la suppression !";
            $hasErrors = true;
        }
        if (!$hasErrors){
            $res = AuteurDal::delAuteur($lAuteur->getId()); // $res contient le nombre de lignes
            if ($res > 0){
                $msg = 'L\'auteur '
                        . $lAuteur->getId() . 'a été supprimé';
                include 'vues/_v_afficherMessage.php';
                // affichage de la liste des auteurs
                $lesAuteurs = AuteurDal::loadAuteurs(1);
                // afficher le nombre de auteurs
                $nbAuteurs = count($lesAuteurs);
                include 'vues/v_listerAuteurs.php';
            } else {
                $tabErreurs[] = "Une erreur s\'est produite dans l\'opération de suppression !";
                $hasErrors = true;
            }
        }
        if ($hasErrors){
            $msg = "L'opération de suppression n'a pas pu être menée à terme en raison des erreurs suivantes :";
            $lien = '<a href="index.php?uc=gererAuteurs">Retour à la saisie</a>';
            include 'vues/_v_afficherErreurs.php';
        }
    }
    break;
}
