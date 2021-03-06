<div id="content">
    <h2>Gestion des ouvrages</h2>
    <?php
    if (strlen($msg)>0) {
        echo '<span class="info">'.$msg.'</span>';
    }
    ?>
    <div id="object-list">          
        <div class="corps-form">
            <fieldset>
                <legend>Consulter un ouvrage</legend>                        
                <div id="breadcrumb">
                    <a href="index.php?uc=gererOuvrages&action=ajouterOuvrage">Ajouter</a>&nbsp;
                    <a href="index.php?uc=gererOuvrages&action=modifierOuvrage&option=saisirOuvrage&id=<?php echo $leOuvrage->getNum() ?>">Modifier</a>&nbsp;
                    <a href="index.php?uc=gererOuvrages&action=supprimerOuvrage&id=<?php echo $leOuvrage->getNum() ?>">Supprimer</a>
                </div>
                <table>
                    <tr>
                        <td class="h-entete">
                            Id :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getNum() ?>
                        </td>
                        <td class="right h-valeur" rowspan="8">
                            <?php echo couvertureOuvrage($leOuvrage->getNum(), $leOuvrage->getTitre()) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Titre :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getTitre() ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Auteur(s) :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getListeNomsAuteurs() ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Date d'acquisition :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getDateAcquisition() ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Genre :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getleGenre()->getLibelle() ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Salle et rayon :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getSalle().', '.$leOuvrage->getRayon() ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Dernier pret :
                        </td>
                        <td class="h-valeur">
                            <?php echo $leOuvrage->getDernierPret() ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-entete">
                            Disponibilié :
                        </td>
                        <td class="h-valeur">
                            <?php
                                if ($leOuvrage->getDisponibilite()=="D") {
                                    echo '<img src="img/dispo.png" alt=""/>';
                                }
                                else {
                                    echo '<img src="img/emprunte.png" alt="" />';
                                }
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>                    
        </div>
    </div>
</div>