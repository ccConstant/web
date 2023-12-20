<?php

/** Classe de gestion des produits servant de modèle
*   à notre application avec des méthodes de type CRUD
*/
class Admin {
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

	/** Récupère un login à partir de l'adresse mail */
	function get_admin_by_email($email)
	{
	  $sql="SELECT * from admin where username='$email'";
	  $data=self::$connexion->prepare($sql);
	  $data->execute();
	  return $data->fetchAll(PDO::FETCH_OBJ);
	}
}
