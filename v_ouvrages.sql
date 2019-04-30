/*
Fonction f_auteurs_ouvrage :
Le ou les auteurs d'un ouvrage passé en argument
*/
delimiter ;
DROP FUNCTION IF EXISTS f_auteurs_ouvrage;

delimiter $
CREATE function f_auteurs_ouvrage (
	p_id	int
)
RETURNS varchar(255)
READS SQL DATA
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE v_auteur varchar (128);    
    DECLARE v_result varchar (255);
    DECLARE v_temp varchar (255);
    DECLARE c_auteurs CURSOR FOR
		SELECT 
			IF (
				ISNULL(alias) OR LENGTH(alias) = 0, 
				IF (
					ISNULL(prenom_auteur) OR LENGTH(prenom_auteur) = 0,
					nom_auteur,
					CONCAT(prenom_auteur,' ',nom_auteur)
				)			
				,
				alias				
			) AS nom
        FROM auteur a
        INNER JOIN auteur_ouvrage ao ON a.id_auteur = ao.id_auteur
        WHERE no_ouvrage = p_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    SET v_auteur := '';
    SET v_temp := '';
  	OPEN c_auteurs;
	read_loop: LOOP
		FETCH c_auteurs INTO v_auteur;
        IF done THEN
			LEAVE read_loop;
		END IF;
		SELECT CONCAT(v_temp,v_auteur,', ') INTO v_temp; 
	END LOOP;
	CLOSE c_auteurs;
    SELECT SUBSTR(RTRIM(v_temp),1,LENGTH(v_temp) - 2) INTO v_result;
	RETURN v_result;
END
$

/*
Vue « v_ouvrages » : 
toutes les informations relatives à l’ouvrage 
(avec le code et le libellé du genre, 
le nom et le prénom de l’auteur) 
ainsi que la date du dernier prêt 
*/
delimiter ;
CREATE OR REPLACE VIEW v_ouvrages AS
SELECT
	o.no_ouvrage,
	titre,
	salle,
	rayon,
	o.code_genre,
	lib_genre,
	DATE_FORMAT(
		date_acquisition,'%Y-%m-%d'
	) AS acquisition,
	IF(LENGTH(f_auteurs_ouvrage(o.no_ouvrage)) = 0, 'Indéterminé', f_auteurs_ouvrage(o.no_ouvrage)) AS auteur,
	DATE_FORMAT(
		MAX(date_emp),
        '%Y-%m-%d'
	) AS dernier_pret,
    IF (
		EXISTS (
			SELECT no_ouvrage
            FROM v_prets_en_cours vpec
            WHERE o.no_ouvrage = vpec.no_ouvrage
		),
        'E',
        'D'
	) AS disponibilite            
FROM
	ouvrage o
INNER JOIN 
	genre g
ON 
	o.code_genre = g.code_genre
LEFT OUTER JOIN 
	pret p
ON 
	o.no_ouvrage = p.no_ouvrage
GROUP BY
	o.no_ouvrage,
	titre,
	salle,
	rayon,
	o.code_genre,
	lib_genre,
	acquisition,
    auteur
;