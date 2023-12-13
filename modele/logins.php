<?php

/** Classe de gestion des produits servant de modèle
*   à notre application avec des méthodes de type CRUD
*/
class Logins {
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
	function add_login($data)
	{
	  $sql = "INSERT INTO logins(customer_id, username, password)
	  values (?,?,?)";
	  $stmt = self::$connexion->prepare($sql);
	  return $stmt->execute(array($data['customer_id'], $data['username'], $data['password']));
	}

	/** Récupère un login à partir de l'adresse mail */
	function get_login_by_email($email)
	{
	  $sql="SELECT * from logins where username='$email'";
	  $data=self::$connexion->prepare($sql);
	  $data->execute();
	  return $data->fetchAll(PDO::FETCH_OBJ);
	}
}
