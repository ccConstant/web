<?php
require("connect.php");

/** Classe de gestion des produits servant de modèle
*   à notre application avec des méthodes de type CRUD
*/
class Products {
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
	function get_all_products()
	{
	  $sql="SELECT * from products";
	  $data=self::$connexion->prepare($sql);
	  $data->execute();
	  return $data->fetchAll(PDO::FETCH_OBJ);
	}

	/** Ajoute un produit à la table contacts */
	function add_product($data)
	{
	  $sql = "INSERT INTO produits(id,cat_id, name, description, image, price, quantity)
	  values (?,?,?,?,?)";
	  $stmt = self::$connexion->prepare($sql);
	  return $stmt->execute(array($data['id'],
		$data['cat_id'], $data['name'],$data['description'],$data['image'], $data['price'], $data['quantity']));
	}

	/** Récupére un contact à partir de son ID */
	/*function get_friend_by_id($id)
	{
	  $sql="SELECT * from contacts where ID=:id";
	  $stmt=self::$connexion->prepare($sql);
	  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	  $stmt->execute();
	  return $stmt->fetch(PDO::FETCH_OBJ);
	 }*/

	/** Efface un contact à partir de son ID */
	/*function delete_friend_by_id($id)
	  {
	  	$sql="Delete from contacts where ID=:id";
	  	$stmt=self::$connexion->prepare($sql);
	  	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	  	return $stmt->execute();
	  }*/


	/** Met jour d'une personne avec sa date de naissance son adresse et sa ville */
	/*function patch($id, $naissance, $adresse, $ville)
	{
	 	$sql = "UPDATE `contacts` SET `NAISSANCE` = :naissance, `ADRESSE` = :adresse, `VILLE` = :ville
	 	WHERE `contacts`.`ID` = :id";
	 	$stmt = self::$connexion->prepare($sql);
	 	$stmt->bindParam(':naissance', $naissance);
         $stmt->bindParam(':adresse', $adresse);
	 	$stmt->bindParam(':ville', $ville);
	 	$stmt->bindParam(':id', $id);
	 	return $stmt->execute();
	}*/

    /** Met à jour d'une personne avec son nom, son prénom,
     *  sa date de naissance et sa ville */
    /*function update($id, $nom, $prenom, $naissance, $ville)
	{
	 	 $sql = "UPDATE `contacts `SET `NOM` = :nom,
                SET `PRENOM` = :prenom,
                SET `NAISSANCE` = :naissance,
                SET `VILLE` = :ville
	 	        WHERE `contacts`.`ID` = :id";
	 	 $stmt = self::$connexion->prepare($sql);
     $stmt->bindParam(':nom', $nom);
     $stmt->bindParam(':prenom', $prenom);
	 	 $stmt->bindParam(':naissance', $naissance);
	 	 $stmt->bindParam(':ville', $ville);
	 	 $stmt->bindParam(':id', $id);
	 	 return $stmt->execute();
	}*/
}
