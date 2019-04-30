<?php

require_once 'modele/OuvrageDal.class.php';
require_once 'modele/GenreDal.class.php';
require_once 'include/_reference.lib.php';
require_once 'include/_forms.lib.php';
require_once 'include/_metier.lib.php';

if (!(isset($_REQUEST['action']))) {
    $action = 'listerOuvrages';
} else {
    $action = $_REQUEST['action'];
}

// variables pour al gestion des messages
$msg = '';      // message passé à la vue v_afficherMessage
$lien = '';     // message passé à la vue v_afficherErreurs
// variables pour la gestion des messages
$titrePage = 'Gestion des ouvrages';
// variables pour la gestion des erreurs
$tabErreurs = array();
$hasErrors = false;


switch ($action) {
    case'listerOuvrages' : {
            // récupérer les auteurs
            $lesOuvrages = OuvrageDal::loadOuvrages(1);
            // afficher le nombre d'auteurs
            $nbOuvrages = count($lesOuvrages);
            include ('vues/v_listerOuvrages.php');
        }
        break;

    case'ajouterOuvrage' : {
            //  initialisation des variables
            $strTitre = '';
            $intSalle = 1;
            $strRayon = '';
            $strGenre = '';
            $strDate= '';
            // traitement de l'option : saisie ou validation ?
            if (isset($_GET["option"])) {
                $option = htmlentities($_GET["option"]);
            } else {
                $option = 'saisirOuvrage';
            }
            switch ($option) {
                case 'saisirOuvrage' : {
                        $lesGenres = GenreDal::loadGenres(0);
                        include 'vues/v_ajouterOuvrage.php';
                    } break;
                case 'validerOuvrage' : {
                        if (isset($_POST["cmdValider"])) {
                            // récupération du libellé
                            if (!empty($_POST["txtTitre"])) {
                                $strTitre = ucfirst($_POST["txtTitre"]);
                            }
                            $intSalle = $_POST["rbnSalle"];
                            if (!empty($_POST["txtRayon"])) {
                                $strRayon = ucfirst($_POST["txtRayon"]);
                            }
                            $strGenre = $_POST["cbxGenres"];
                            if(!empty($_POST["txtRayon"])) {
                                $strDate = $_POST["txtDate"];
                            }
                            // test zones obligatoires
                            if (!empty($strTitre) and !empty($strRayon) and !empty($strDate)) {
                                //test de cohérence
                                // test de la date d'acquisition
                                $dateAcquisition = new DateTime($strDate);
                                $curDate = new DateTime(date('Y-m-d'));
                            }
                            if ($dateAcquisition > $curDate) {
                                $tabErreurs[] = 'La date d\'aquisition doit être antérieure ou égale à la date du jour';
                                $hasErrors = true;
                            }
                            // controle du rayon
                            if (!rayonValide($strRayon)) {
                                $tabErreurs[] = 'Le rayon n\'est pas valide, il doit comporter une lettre et un chiffre';
                                $hasErrors = true;
                            }
                        }
                        else {
                            if (empty($strTitre)) {
                                $tabErreurs[] = "Le titre doit être renseigné";
                            }
                            if (empty($strRayon)) {
                                $tabErreurs[] = "Le rayon doit être renseigné";
                            }
                            if (emtpy($strDate)) {
                                $tabErreurs[] = "La date d'aquisition doit être renseignée";
                            }
                            $hasErrors = true;
                        }
                        if (!$hasErrors) {
                            try{
                                $res = OuvrageDal::addOuvrage($strTitre,$intSalle, $strRayon, $strGenre, $strDate);

                                if ($res > 0) {
                                    $msg = '<span class="info">l\'ouvrage'.$strTitre.' a été ajouté</span>';
                                    $intID= OuvrageDal::getMaxId();
                                    $leOuvrage = OuvrageDal::loadOuvrageByID($intID);
                                    if($leOuvrage){
                                        include'vues/v_consulterOuvrage.php';
                                    }
                                    else {
                                        $tabErreurs[] = 'Cet ouvrage n\'existe pas !';
                                        $hasErrors = true;
                                    }
                                }
                                else {
                                    $tabErreurs[] = 'Une erreur s\'est produite dans l\'operation d\'ajout';
                                    $hasErrors = true;
                                }
                            }
                            catch (PDOException $e){
                                $tabErreurs[] = 'Une exception PDO a été levée';
                                $hasErrors = true;
                            }
                        }
                        else {
                            $msg = "L'opération d'ajout n'a pas pu etre menée à terme en raison des erreurs suivantes :";
                            $lien = '<a href="index.php?uc=gererOuvrages&action=ajouterOuvrage">Retour à la saisie</a>';
                            include 'vues/_v_afficherErreurs.php';
                        }
                    }
                    break;
            }
        }
        break;

    case'consulterOuvrage' : {
            // récupération du code
            $img = "";
            if (isset($_GET["id"])) {
                $id = intval(htmlentities($_GET["id"]));
                //appel de la méthode du modèle
                try {
                $leOuvrage = OuvrageDal::loadOuvrageByID($id);
                if ($leOuvrage == NULL) {
                    $tabErreurs[] = 'Cet ouvrage n\'existe pas !';
                    $hasErrors = true;
                    }
                } catch (PDOException $e){
                    $tabErreurs[] = $e->getMessage();
                    $hasErrors = true;
                }
            }
            else {
                //pas d'id dans l'url ni clic sur Valider : c'est anormal
                $tabErreurs[] = "Aucun ouvrage n'a été transmis pour consultation !";
                $hasErrors = true;
            }
            if ($hasErrors) {
                $msg="Une erreur s'est produite :";
                include 'vues/_v_afficherErreurs.php';
            } else {
                include 'vues/v_consulterOuvrage.php';
            }
        }
        break;

    case'supprimerOuvrage' : {
            // récupération du code
            if (isset($_GET["id"])) {
                $id = strtoupper(htmlentities($_GET["id"]));
                // appel de la méthode du modèle
                $leOuvrage = OuvrageDal::loadOuvrageByID($id);
                if ($leOuvrage == NULL) {
                    $tabErreurs[] = 'Cet ouvrage n\'existe pas !';
                    $hasErrors = true;
                } else {
                    // rechercher des ouvrages de ce genre
                    if (OuvrageDal::countOuvragesGenre($leOuvrage->getNum()) > 0) {
                        // il y a des ouvrages référencés, suppression impossible
                        $tabErreurs[] = 'Il existe des auteurs qui référencent cet ouvrage, suppression impossible !';
                        $hasErrors = true;
                    }
                }
            } else {
                //pas d'id dans l'url ni clic sur Valider : c'est anormal
                $tabErreurs[] = "Aucun identifiant d'ouvrage n'a été transmis pour suppression !";
                $hasErrors = true;
            }

            if (!$hasErrors) {
                $res = OuvrageDal::delOuvrage($leOuvrage->getNum());
                if ($res > 0) {
                    $msg = 'L\'Ouvrage ' .$leOuvrage->getNum(). ' a été supprimé';
                    include 'vues/_v_afficherMessage.php';
                    $lesOuvrages = OuvrageDal::loadOuvrages(1);
                    // afficher le nombre d'auteurs
                    $nbOuvrages = count($lesOuvrages);
                    include ('vues/v_listerOuvrages.php');
                } else {
                    $tabErreurs[] = "Cet ouvrage est lié à des auteurs, suppression impossible !";
                    $hasErrors = true;
                }
            }

            if ($hasErrors) {
                $msg = "L'opération de suppression n'a pas pu être menée à terme en raison des erreurs suivantes :";
                $lien = '<a href="index.php?uc=gererOuvrage">Retour à la saisie</a>';
                include 'vues/_v_afficherErreurs.php';
            }
        }
        break;

    case'modifierOuvrage' : {
            // initialisation des variables
            $strTitre = '';
            $strSalle = 1;
            $strRayon = '';
            $strGenre = '';
            $strDate = '';
            // traitement de l'option : saisie ou validation
            if (isset($_GET["option"])) {
                $option = htmlentities($_GET["option"]);
            } else {
                $option = 'saisirOuvrage';
            }
            // récupération de l'id/post ou en get
            if (isset($_GET["id"])) {
                $intID = intval(htmlentities($_GET["id"]));
                $leOuvrage = OuvrageDal::loadOuvrageByID($intID);
                if ($leOuvrage == NULL) {
                    $tabErreurs[] = 'Cet ouvrage n\'existe pas !';
                    $hasErrors = true;
                }
            } else {
                //pas d'id dans l'url ni clic sur Valider : c'est anormal
                $tabErreurs[] = "Aucun id d'ouvrage n'a été transmis pour modification !";
                $hasErrors = true;
            }
            // On ne rentre dans le switch que si
            // l'id est transmis et l'id de l'ouvrage existe
            if (!$hasErrors)
            switch ($option) {
                case 'saisirOuvrage' : {
                            // la fonction "afficherListe()" manipule un tableau associatif classique et non objet : style = 0
                            $lesGenres = GenreDal::loadGenres(0);
                            include('vues/v_modifierOuvrage.php');
                    } break;
                case 'validerOuvrage' : {
                    //si on a cliqué sur Valider
                    if (isset($_POST["cmdValider"])){
                        //memoriser les données pour les reafficher dans le formulaire
                        $intID = intval($_POST["txtID"]);
                        //récuperation des valeurs saisies
                        if (!empty($_POST["txtTitre"])) {
                            $strTitre = ucfirst($_POST["txtTitre"]);
                        }
                        $intSalle = $_POST["rbnSalle"];
                        if (!empty($_POST["txtRayon"])) {
                            $strRayon = ucfirst($_POST["txtRayon"]);
                        }
                        $strGenre = $_POST["cbxGenres"];
                        $leGenre = GenreDal::loadGenreByID($strGenre);
                        if (!empty($_POST["txtRayon"])){
                            $strDate = $_POST["txtDate"];
                        }

                        // Test des zones obligatoires
                        if (!empty($strTitre) and
                                !empty ($strRayon) and
                                !empty($strDate)) {
                            //tests de cohérence
                            //test de la date d'acquisition
                            $dateAcquisition = new DateTime($strDate);
                            $curDate = new DateTime(date('Y-m-d'));
                            if ($dateAcquisition > $curDate) {
                                //la date d'acquisition est posterieure à la date du jour
                                $tabErreurs[]= 'La date d\'acquisition doit etre antérieure ou égale à la date du jour';
                                $hasErrors = true;
                            }
                            //controle du rayon
                            if (!rayonValide($strRayon)) {
                                $tabErreurs[]= 'Le rayon n\'est pas valide, il doit comporter une lettre et un chiffre !';
                                $hasErrors = true;
                            }
                        }
                        else {
                            if (empty($strTitre)) {
                                $tabErreurs[]= 'Le titre doit etre renseigné';
                            }
                            if (empty($strRayon)) {
                                $tabErreurs[]= 'Le rayon doit etre renseigné';
                            }
                            if (empty($strDate)) {
                                $tabErreurs[]= 'La date d\'acquisition doit etre renseignée';
                            }
                            $hasErrors = true;
                        }
                        // Test si la couverture doit être changer
                        if(!(couvertureExiste($leOuvrage->getNum()))){
                            // Test $_FILES vide ou non pour le fichier
                            if ($_FILES['fichier']['error'] > 0){
                                $hasErrors = true;
                            }
                            else{

                                // $_FILES['nom du input']['name']     //Le nom original du fichier, comme sur le disque du visiteur (exemple : mon_icone.png).
                                // $_FILES['nom du input']['type']     //Le type du fichier. Par exemple, cela peut être « image/png ».
                                // $_FILES['nom du input']['size']     //La taille du fichier en octets.
                                // $_FILES['nom du input']['tmp_name'] //L'adresse vers le fichier uploadé dans le répertoire temporaire.
                                // $_FILES['nom du input']['error']    //Le code d'erreur, qui permet de savoir si le fichier a bien été uploadé.
                                // move_uploaded_file()                // Permet de deplacer le fichier

                                // Renomer le fichier
                                $_FILES['fichier']['name'] = $leOuvrage->getNum();
                                $name = $_FILES['fichier']['name'];
                                // Changer sa taille

                                // Le deplacer dans le bon dossier
                                $chemin = 'img/couvertures/'.$name.'.jpg';
                                if (!move_uploaded_file($_FILES['fichier']['tmp_name'], $chemin)) {
                                    $tabErreurs[] = 'Erreur lors du déplacement de l\'image';
                                }

                            }
                        }

                        if (!$hasErrors) {
                            $leOuvrage = new Ouvrage($intID, $strTitre, $intSalle, $strRayon, $leGenre, $dateAcquisition);
                            try {
                                // maj dans la bdb
                                $res = OuvrageDal::setOuvrage($leOuvrage);
                                if($res > 0) {
                                    $msg = '<span class="info">L\'ouvrage ' .$strTitre.' a été modifié</span>';
                                    // récuperation des valeurs dans la base
                                    $leOuvrage = OuvrageDal::loadOuvrageByID($intID);
                                    if($leOuvrage){
                                        include 'vues/v_consulterOuvrage.php';
                                    }
                                }
                                else{
                                    $tabErreurs[] = 'Une erreur s\' est produite lors de l\' operation de mise à jour !';
                                    $hasErrors = true;
                                }
                            }
                            catch (PDOException $e) {
                                $tabErreurs[] = 'Une exception a été levée';
                                $hasErrors = true;
                            }
                        }
                    }
                    else{
                        // pas d'id dans l'url ni clic sur Valider : c'est anormal
                        $tabErreurs[] = "Accès interdit";
                        $hasErrors = true;
                    }
                }
            }
            // affichage des erreurs
            if($hasErrors){
                $msg = "Une erreur s'est produite :";
                include 'vues/_v_afficherErreurs.php';
            }
    } break;

default : include 'vues/_v_home.php';
}
?>
