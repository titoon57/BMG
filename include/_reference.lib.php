<?php
/**
 *
 * BMG
 * © GroSoft
 *
 * References
 * Classes métier
 *
 *
 * @package 	default
 * @author 	dk
 * @version    	1.0
 */

/*
 *  ====================================================================
 *  Classe Genre : représente un genre d'ouvrage
 *  ====================================================================
*/

class Genre {
    private $_code;
    private $_libelle;

    /**
     * Constructeur
    */
    public function __construct(
            $p_code,
            $p_libelle
    ) {
        $this->setCode($p_code);
        $this->setLibelle($p_libelle);
    }

    /**
     * Accesseurs
    */
    public function getCode () {
        return $this->_code;
    }

    public function getLibelle () {
        return $this->_libelle;
    }

    /**
     * Mutateurs
    */
    public function setCode ($p_code) {
        $this->_code = $p_code;
    }

    public function setLibelle ($p_libelle) {
        $this->_libelle = $p_libelle;
    }

}



class Auteur {
    private $_id;
    private $_nom;
    private $_prenom;
    private $_alias;
    private $_notes;
    private $_anneeNaissance;
    private $_anneeDeces;
    private $_academicien;
    private $_nationalite;
    /**
     * Constructeur
    */
    public function __construct(
            $p_id,
            $p_nom,
            $p_prenom,
            $p_alias,
            $p_notes,
            $p_anneeNaissance,
            $p_anneeDeces,
            $p_academicien,
            $p_nationalite
    ) {
        $this->setId($p_id);
        $this->setNom($p_nom);
        $this->setPrenom($p_prenom);
        $this->setAlias($p_alias);
        $this->setNotes($p_notes);
        $this->setAnneeNaissance($p_anneeNaissance);
        $this->setAnneeDeces($p_anneeDeces);
        $this->setAcademicien($p_academicien);
        $this->setNationalite($p_nationalite);
    }

    /**
     * Accesseurs
    */
    public function getId () {
        return $this->_id;
    }

    public function getNom () {
        return $this->_nom;
    }

    public function getPrenom () {
        return $this->_prenom;
    }

    public function getAlias () {
        return $this->_alias;
    }

    public function getNotes () {
        return $this->_notes;
    }

    public function getAnneeNaissance () {
        return $this->_anneeNaissance;
    }

    public function getAnneeDeces () {
        return $this->_anneeDeces;
    }

    public function getAcademicien () {
        return $this->_academicien;
    }

    public function getNationalite () {
        return $this->_nationalite;
    }
    /**
     * Mutateurs
    */
    public function setId ($p_id) {
        $this->_id = $p_id;
    }

    public function setNom ($p_nom) {
        $this->_nom = $p_nom;
    }

    public function setPrenom ($p_prenom) {
        $this->_prenom = $p_prenom;
    }

    public function setAlias ($p_alias) {
        $this->_alias = $p_alias;
    }

      public function setNotes ($p_notes) {
        $this->_notes = $p_notes;
    }

    public function setAnneeNaissance ($p_anneeNaissance) {
        $this->_anneeNaissance = $p_anneeNaissance;
    }

    public function setAnneeDeces ($p_anneeDeces) {
        $this->_anneeDeces = $p_anneeDeces;
    }

    public function setAcademicien ($p_academicien) {
        $this->_academicien = $p_academicien;
    }

    public function setNationalite ($p_nationalite) {
        $this->_nationalite = $p_nationalite;
    }
}

/*
 *  ====================================================================
 *  Classe Ouvrage : représente un genre d'ouvrage
 *  ====================================================================
*/

class Ouvrage {
    private $_noOuvrage;
    private $_titre;
    private $_salle;
    private $_rayon;
    private $_leGenre;
    private $_dateAcquisition;
    private $_lesAuteurs;
    private $_dernierPret;
    private $_listeNomsAuteurs;
    private $_disponibilite;

    /**
     * Constructeur
    */
    public function __construct(
            $p_num,
            $p_titre,
            $p_salle,
            $p_rayon,
            $p_leGenre,
            $p_acquisition = null
    ) {
        $this->setNum($p_num);
        $this->setTitre($p_titre);
        $this->setSalle($p_salle);
        $this->setRayon($p_rayon);
        $this->setLeGenre($p_leGenre);
        $this->setDateAcquisition($p_acquisition);
        $this->_lesAuteurs = array();
    }

    /**
     * Accesseurs
    */
    public function getNum () {
        return $this->_Num;
    }

    public function getTitre () {
        return $this->_Titre;
    }

    public function getSalle () {
        return $this->_Salle;
    }

    public function getRayon () {
        return $this->_Rayon;
    }

    public function getLeGenre () {
        return $this->_leGenre;
    }

    public function getDateAcquisition () {
        return $this->_Acquisition;
    }

    public function getlesAuteurs () {
        return $this->_lesAuteurs;
    }

    public function getDernierPret () {
        return $this->_Dernier_pret;
    }

    public function getDisponibilite () {
        return $this->_Disponibilite;
    }

    public function getListeNomsAuteurs () {
        return $this->_ListeNomsAuteurs;
    }


    /**
     * Mutateurs
    */
    public function setNum ($p_num) {
        $this->_Num = $p_num;
    }

    public function setTitre ($p_titre) {
        $this->_Titre = $p_titre;
    }

    public function setSalle ($p_salle) {
        $this->_Salle = $p_salle;
    }

    public function setRayon ($p_rayon) {
        $this->_Rayon = $p_rayon;
    }

    public function setLeGenre ($p_leGenre) {
        $this->_leGenre = $p_leGenre;
    }

    public function setDateAcquisition ($p_acquisition) {
        $this->_Acquisition = $p_acquisition;
    }

    public function setlesAuteurs ($p_lesAuteurs) {
        $this->_lesAuteurs = $p_lesAuteurs;
    }

    public function setDernierPret ($p_dernier_pret) {
        $this->_Dernier_pret = $p_dernier_pret;
    }

    public function setDisponibilite ($p_disponibilite) {
        $this->_Disponibilite = $p_disponibilite;
    }

    public function setListeNomsAuteurs ($p_ListeNomsAuteurs) {
        $this->_ListeNomsAuteurs = $p_ListeNomsAuteurs;
    }
}
