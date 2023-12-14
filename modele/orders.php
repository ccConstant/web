<?php

/** Classe de gestion des produits servant de modèle
*   à notre application avec des méthodes de type CRUD
*/
class Orders {
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

	/** Ajoute un produit à la table contacts */
	function add_order($data, $registered=true)
	{
	  $sql = "INSERT INTO orders(customer_id, registered, delivery_add_id, payment_type, date, status, session, total)
	  values (?,?,?,?,?,?,?,?)";
	  $stmt = self::$connexion->prepare($sql);
	  return $stmt->execute(array($data['customer_id'], $registered,$data['delivery_add_id'],$data['payment_type'], $data['date'], $data['status'], $data['session'], $data['total']));
		
	}
}
