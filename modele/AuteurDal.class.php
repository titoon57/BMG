<?php
// sollicite services de la classe PdoDal
require_once ('PdoDao.class.php');

class AuteurDal {

    /*
    * charge un tableau
    * @param $style : 0 == tableau assoc, 1 == objet
    * @return un objet de la classe PDOStatement
    */
    public static function loadAuteurs($style){
        // instanciation d'un objet PdoDao
        $cnx = new PdoDao();
        $qry = "SELECT * FROM auteur";
        $tab = $cnx->getRows($qry, array(), $style);
        if (is_a($tab,'PDOException')){
            return PDO_EXCEPTION_VALUE;
        }
        // dans le cas où on attend un tableau d'objets
        if ($style == 1){
            // retourner un tableau d'objets
            $res = array();
            foreach($tab as $ligne){
                $unAuteur = new Auteur(
                    $ligne->id_auteur,
                    $ligne->nom_auteur,
                    $ligne->prenom_auteur,
                    $ligne->alias,
                    $ligne->notes,
                    $ligne->annee_naissance,
                    $ligne->annee_deces,
                    $ligne->academicien,
                    $ligne->id_nationalite
                    );
                array_push($res, $unAuteur); // identique à res[] = $unAuteur
            }
            return $res;
        }
        return $tab;
    }

    /*
    * charge un tableau
    * @param $style : 0 == tableau assoc, 1 == objet
    * @return un objet de la classe PDOStatement
    */
    public static function loadAuteursByNationalite($style, $id){
        // instanciation d'un objet PdoDao
        $cnx = new PdoDao();
        $qry = "SELECT * FROM auteur WHERE id_nationalite = ?";
        $tab = $cnx->getRows($qry, array($id), $style);
        if (is_a($tab,'PDOException')){
            return PDO_EXCEPTION_VALUE;
        }

        // dans le cas où on attend un tableau d'objets
        if ($style == 1){
            // retourner un tableau d'objets
            $res = array();
            foreach($tab as $ligne){
                $unAuteur = new Auteur(
                    $ligne->id_auteur,
                    $ligne->nom_auteur,
                    $ligne->prenom_auteur,
                    $ligne->alias,
                    $ligne->notes
                    );
                array_push($res, $unAuteur); // identique à res[] = $unAuteur
            }
            return $res;
        }
        return $tab;
    }

    /**
     * charge un objet de la classe Auteur à partir de son code
     * @param  $id : le code du genre
     * @return  un objet de la classe Genre
     */
    public static function loadAuteurByID($id) {
        $cnx = new PdoDao();
        // requête
        $qry = 'SELECT id_auteur, nom_auteur, prenom_auteur, alias, notes,  FROM auteur WHERE id_auteur = ?';
        $res = $cnx->getRows($qry, array($id), 1);
        if (is_a($res, 'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        if (!empty($res)) {
            // le genre existe
            $id = $res[0]->id_auteur;
            $nom = $res[0]->nom_auteur;
            $prenom = $res[0]->prenom_auteur;
            $alias = $res[0]->alias;
            $notes = $res[0]->notes;
            return new Auteur($id, $nom, $prenom, $alias, $notes);
        } else {
            return NULL;
        }
    }

    /**
     * ajoute un Auteur
     * @param   string  $nom
     * @param   string  $prenom
     * @param   string  $alias
     * @param   string  $notes
     * @return  nombre de lignes affectées
     */
     public static function addAuteur($nom, $prenom, $alias, $notes ) {
          $cnx = new PdoDao();
          $qry = 'INSERT INTO auteur (nom_auteur, prenom_auteur, alias, notes) VALUES (?,?,?,?)';
          $res = $cnx->execSQL($qry, array(// nb de lignes affectées
              $nom,
              $prenom,
              $alias,
              $notes
                  )
          );
          if (is_a($res, 'PDOException')) {
              return PDO_EXCEPTION_VALUE;
          }
          return $res;
      }

    /**
    * calcule le nombre d'ouvrages pour un genre
    * @param type $code : le code du genre
    * @return le nombre d'ouvrages du genre
    */
    public static function countOuvragesAuteur($id){
        $cnx = new PdoDao();
        $qry = 'SELECT COUNT(*) FROM ouvrage WHERE id_auteur = ?';
        $res = $cnx->getValue($qry,array($id));
        if (is_a($res,'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    /**
     * supprime un genre
     * @param   int $code : le code du genre à supprimer
     * @return le nombre de lignes affectées
    */
    public static function delAuteur($id) {
        $cnx = new PdoDao();
        $qry = 'DELETE FROM auteur WHERE id_auteur = ?';
        $res = $cnx->execSQL($qry,array($id));
        if (is_a($res,'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

    public static function setAuteur($unAuteur) {
        $cnx = new PdoDao();
        $qry = 'UPDATE auteur SET nom_auteur = ?, prenom_auteur = ?, alias = ?, notes = ? WHERE id_auteur = ?';
        $res = $cnx->execSQL($qry,array(
            $unAuteur->getNom(),
            $unAuteur->getPrenom(),
            $unAuteur->getAlias(),
            $unAuteur->getNotes(),
            $unAuteur->getId()
         ));
        if (is_a($res,'PDOException')) {
            return PDO_EXCEPTION_VALUE;
        }
        return $res;
    }

}


?>
