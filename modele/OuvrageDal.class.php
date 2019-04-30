<?php

//sollicite les services de la classe pdoDao
require_once ('PdoDao.class.php');

class OuvrageDal {
    /*
     * charge un tableau d'auteurs
     * @param $style : 0 == tableau assoc, 1 == objet
     * @return un objet de la classe PDOStatement
     */

    public static function loadOuvrages($style) {
        // instanciation d'un objet PdoDao
        $cnx = new PdoDao();
        $qry ="SELECT no_ouvrage as ID, "
          ."titre, "
          ."code_genre, "
          ."lib_genre, "
          ."auteur, "
          ."salle, "
          ."rayon, "
          ."dernier_pret, "
          ."disponibilite"
          ." FROM v_ouvrages "
          ."ORDER BY titre;";
        $tab = $cnx->getRows($qry, array(), $style);
        if (is_a($tab, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        // dans le cas où on attend un tableau d'objets
        if ($style == 1) {
            //retourne un tableau d'objets
            $res = array();
            foreach ($tab as $ligne) {
                $unGenre = new Genre($ligne->code_genre,$ligne->lib_genre);
                $unOuvrage = new Ouvrage(
                        $ligne->ID, $ligne->titre, $ligne->salle, $ligne->rayon, $unGenre
                );
                $unOuvrage->setListeNomsAuteurs($ligne->auteur);
                $unOuvrage->setDisponibilite($ligne->disponibilite);
                $unOuvrage->setDernierPret($ligne->dernier_pret);
                array_push($res, $unOuvrage); // identique ) $res[] = $unAuteur;
            }
            return $res;
        }
        return $tab;
    }


/**
     * charge un objet de la classe Ouvrage à partir de son code
     * @param  $id : le code de l'ouvrage
     * @return  un objet de la classe ouvrage
     */
    public static function loadOuvrageByID($id) {
        $cnx = new PdoDao();
        // requête
        $qry = 'SELECT no_ouvrage as ID, titre, acquisition, code_genre, lib_genre, salle, rayon, dernier_pret, disponibilite, auteur
        FROM v_ouvrages WHERE no_ouvrage = ?';
        $res = $cnx->getRows($qry, array($id), 1);
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        if (!empty($res)) {
            $res=$res[0];
            // l'ouvrage existe
            $unGenre = new Genre($res->code_genre, $res->lib_genre);
            $unOuvrage = new Ouvrage(
                $res->ID,
                $res->titre,
                $res->salle,
                $res->rayon,
                $unGenre,
                $res->acquisition
            );
            $unOuvrage->setListeNomsAuteurs($res->auteur);
            $unOuvrage->setDisponibilite($res->disponibilite);
            $unOuvrage->setDernierPret($res->dernier_pret);
            
            return $unOuvrage;
        } else {
            return NULL;
        }
    }

        /**
     * calcule le nombre d'ouvrages pour un genre
     * @param type $code : le code du genre
     * @return le nombre d'ouvrages du genre
     */
    public static function maxID($style) {
        $cnx = new PdoDao();
        $qry = 'SELECT MAX(id_auteur) FROM auteur';
        $res = $cnx->getRows($qry, array(), $style);
        return $res;

        //return $res[0];
    }

    /**
     * calcule le nombre d'ouvrages pour un genre
     * @param type $code : le code du genre
     * @return le nombre d'ouvrages du genre
     */
    public static function countOuvragesGenre($id) {
        $cnx = new PdoDao();
        $qry = 'SELECT COUNT(*) FROM ouvrage WHERE id_auteur = ?';
        $res = $cnx->getValue($qry, array($id));
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    public static function delAuteur($id) {
        $cnx = new PdoDao();
        $qry = 'DELETE FROM auteur WHERE id_auteur = ?';
        $res = $cnx->execSQL($qry, array($id));
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    /**
     * calcule le nombre d'auteurs liés à un genre
     * @param type $code : l'ID de l'ouvrage
     * @return le nombre d'auteurs pour un ouvrage
     */
    public static function countAuteursOuvrage($id) {
        $cnx = new PdoDao();
        $qry = 'SELECT COUNT(*) FROM auteur_ouvrage WHERE no_ouvrage = ?';
        $res = $cnx->getValue($qry, array($id));
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    /**
     * supprime un genre
     * @param int $id : l'id de l'auteur à supprimer
     * @return le nombre de lignes affectées
     */
    public static function delOuvrage($id) {
        $cnx = new PdoDao();
        $qry = 'DELETE FROM ouvrage WHERE no_ouvrage = ?';
        $res = $cnx->execSQL($qry, array($id));
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    /**
     * supprime un genre
     * @param   int $code : le code du genre à supprimer
     * @return le nombre de lignes affectées
     */
    public static function setOuvrage($unOuvrage) {
        $cnx = new PdoDao();
        $qry = 'UPDATE ouvrage SET titre = ?, salle = ?, rayon = ?, code_genre = ?, date_acquisition = ? WHERE no_ouvrage = ?';
        $res = $cnx->execSQL($qry, array(
            $unOuvrage->getTitre(),
            $unOuvrage->getSalle(),
            $unOuvrage->getRayon(),
            $unOuvrage->getLeGenre()->getCode(),
            $unOuvrage->getDateAcquisition()->format('Y_m_d'),
            $unOuvrage->getNum()
        ));
        
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    /**
     * ajoute un ouvrage
     * @param string $strTitre : le titre de l'ouvrage
     * @param string $strSalle : la salle de l'ouvrage
     * @param string $strRayon : le rayon de l'ouvrage
     * @param string $strGenre : le genre de l'ouvrage
     * @return le nombre de lignes affectées
     */
    public static function addOuvrage($strTitre, $intSalle, $strRayon, $strGenre, $strDate) {
        $cnx = new PdoDao();
        $qry = 'INSERT INTO ouvrage (titre, salle, rayon, code_genre, date_acquisition) VALUES (?,?,?,?,?)';
        $res = $cnx->execSQL($qry, array(// nb de lignes affectées
            $strTitre,
            $intSalle,
            $strRayon,
            $strGenre,
            $strDate
                )
        );
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    /**
     * Récupère l'ID du dernier ouvrage ajouté dans la base de données
     */
    public static  function getMaxId(){
        $cnx = new PdoDao();
        $qry = "SELECT MAX(no_ouvrage) FROM ouvrage";
        $intID = $cnx->getValue($qry, array());
        return $intID;
    }


}

?>
