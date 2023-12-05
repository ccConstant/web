<?php

/** Classe de gestion des produits servant de modèle
*   à notre application avec des méthodes de type CRUD
*/
class Users {
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

	/** Récupére la liste des produits sous forme d'un tableau */
	function get_all_users()
	{
	  $sql="SELECT * from customers";
	  $data=self::$connexion->prepare($sql);
	  $data->execute();
	  return $data->fetchAll(PDO::FETCH_OBJ);
	}

	/** Récupére un user par id sous forme d'un tableau */
	function get_user_by_id($id)
	{
	  $sql="SELECT * from customers where id=$id";
	  $data=self::$connexion->prepare($sql);
	  $data->execute();
	  return $data->fetchAll(PDO::FETCH_OBJ);
	}

	/** Ajoute un user à la table customers */
	function add_user($data)
	{
	  $sql = "INSERT INTO users(id,forname, surname, add1, add2, add3, postcode, phone, email)
	  values (?,?,?,?,?,?,?,?,?)";
	  $stmt = self::$connexion->prepare($sql);
	  return $stmt->execute(array($data['id'], $data['forname'], $data['surname'], $data['add1'], $data['add2'], $data['add3'], $data['postcode'], $data['phone'], $data['email']));
	}
}
