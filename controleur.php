<?php

session_start();
require_once 'modele/modele2.php';
include 'vendor/autoload.php';
// le dossier ou on trouve les templates
$loader = new Twig\Loader\FilesystemLoader('vue');
// initialiser l'environement Twig
$twig = new Twig\Environment($loader);

include 'controllers.php';
// on lit une action en parametre
// par defaut, 'list'
$action = $_GET['action'] ?? 'welcome';
$categorie = $_GET['categorie'] ?? 0 ;
$message = "";
$product=new Products();
switch ($action) {
    case "welcome":
        $template = $twig->load('welcome.twig');
        echo $template->render(array(
            'titre' => "Welcome ! ",
        ));
        break;
    case "list": 
        echo "coucou";
        list_action($twig, $categorie, $product);
        break;
    case "detail":
        detail_action($product,$twig, $_GET['id']);
        break;
    case "addToCart":
        if(!isset($_SESSION['cart'])){
            $_SESSION['cart'] = array();
        }
        if(!isset($_SESSION['cart'][$_GET['id']])){
            $_SESSION['cart'][$_GET['id']]=1;
        }else{
            $_SESSION['cart'][$_GET['id']]+=1;
        }
        echo "Produit ajouté au panier !";
        break;
    case "cartconsult":
        foreach ($_SESSION['cart'] as $key => $value) {            
            $products[$key] = $product->get_product_by_id($key);
        }
        var_dump($products[13]);

        $template = $twig->load('cart.twig');
        echo $template->render(array(
            'titre' => "Panier",
            'cart' => $products
        ));
        break;
    /*case "suppr":
       if (suppr_action($cont, $_GET['id']))
            $message = "Contact supprimé avec succès !";
       else $message = "Pb de suppression !";
       list_action($cont,$twig,$message);
       break;
    case "patch":
       if (!empty($_GET['id']) and !empty($_GET['naissance']) and !empty($_GET['adresse'])
        and !empty($_GET['ville'])) {
           $res = patch_action($cont, $_GET['id'], $_GET['naissance'], $_GET['adresse'], $_GET['ville']);
       }
         if (!empty($res))
            $message = "Contact modifié avec succès!";
         else
            $message = "Pb de modification";
        list_action($cont,$twig,$message);
        break;
	  case "add":
      if (add_action($cont, $_GET))
		       $message = "Le contact ".$_GET['nom']." ajouté avec succès !";
	    else $message = "Pb d'ajout lors de l'ajout du contact !";
      list_action($cont,$twig,$message);
      break;*/
    default:
    $template = $twig->load('welcome.twig');
}

//header("refresh:4;url=controleur.php");
