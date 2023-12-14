<?php

/** Classe de gestion des produits servant de modèle
*   à notre application avec des méthodes de type CRUD
*/
class Delivery_adresses {
	/** Objet contenant la connexion pdo à la BD */
	private static $connexion;

	/** Constructeur établissant la connexion */
	function __construct()
	{
		$dsn="mysql:dbname=".BASE.";host=".SERVER;
		try{
				self::$connexion=new PDO($dsn,USER,PASSWD);
		}
		catch(PDOException $e){
		printf("Échec de la connexion : %s\n", $e->getMessage());
		$this->connexion = NULL;
		}
	}

	/** Ajoute un user à la table customers */
	function add_deliveryAdresses($data)
	{
        $sql = "INSERT INTO delivery_addresses(firstname, lastname, add1, add2, city, postcode, phone, email)
        values (?,?,?,?,?,?,?,?)";
	    $stmt = self::$connexion->prepare($sql);
	    $stmt->execute(array($data['firstname'], $data['lastname'], $data['addr'], $data['addr2'], $data['city'], $data['postcode'], $data['phone'], $data['email']));
		return self::$connexion->lastInsertId();
	}

    /** Récupère une delivery adresse grâce à l'id */

    function get_deliveryAdresses_by_id($id)
    {
        $sql="SELECT * from delivery_addresses where id=$id";
        $data=self::$connexion->prepare($sql);
        $data->execute();
        return $data->fetchAll(PDO::FETCH_OBJ);
    }

}
