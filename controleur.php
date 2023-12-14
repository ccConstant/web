<?php

session_start();
require("modele/connect.php");
require_once 'modele/products.php';
require_once 'modele/users.php';
require_once 'modele/logins.php';
require_once 'modele/orders.php';
require_once 'modele/delivery_adresses.php';
include 'vendor/autoload.php';
// le dossier ou on trouve les templates
$loader = new Twig\Loader\FilesystemLoader('vue');
// initialiser l'environement Twig
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());


include 'controllers.php';
// on lit une action en parametre
// par defaut, 'list'
$action = $_GET['action'] ?? 'welcome';
$categorie = $_GET['categorie'] ?? 0 ;
$message = "";
$product=new Products();
$login=new Logins();
$user=new Users();
$order=new Orders();
$deliveryAdresses=new Delivery_adresses();
$connected=false ;
if (isset($_SESSION['user']) && $_SESSION['user']!=null){   
    $connected=true;
}
var_dump($connected);
switch ($action) {
    case "welcome":
        $template = $twig->load('welcome.twig');
        echo $template->render(array(
            'titre' => "Welcome ! ",
            'connected' => $connected,
        ));
        break;
    case "list": 
        list_action($twig, $categorie, $product, $connected);
        break;
    case "detail":
        detail_action($product,$twig, $_GET['id'], $connected);
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
        $template = $twig->load('example.twig');
        echo $template->render(array(
            'message' => "Produit ajouté au panier ! ",
            'connected' => $connected,
        ));
        break;
    case "cartconsult": 
        cartConsult($twig, $product, $connected);
        break;
    case "subscribe" : 
        $template = $twig->load('subscribe.twig');
        echo $template->render(array(
        ));
        break;
    case "createAccount":
        $connected=createAccount($twig, $_POST, $user, $login);
        break;
    case "buy": 
        if($_SESSION['cart']!=null){
            foreach($_SESSION['cart'] as $key => $value){
                if($value!=0){
                    if ($connected){
                        $user=$user->get_user_by_id($_SESSION['user']);
                        $template = $twig->load('buyConnected.twig');
                        echo $template->render(array(
                            'data' => $user[0],
                            
                        ));
                    }else{
                        
                        $template = $twig->load('buyNotConnected.twig');
                        echo $template->render(array(
                        ));
                    }
                }
            }
        }else{
            $template = $twig->load('welcome.twig');
            echo $template->render(array(
            ));
        }
        break;
    case "payment":
        payment($twig, $_POST, $user, $connected, $deliveryAdresses);
        break;
    case "login":
        $template = $twig->load('login.twig');
        echo $template->render(array(
        ));
        break;
    case "deleteFromCart" : 
        $_SESSION['cart'][$_GET['id']]=0;
        cartConsult($twig, $product, $connected);
        break;
    case "connectUser":
        connectUser($twig, $_POST, $login, $connected, $user);
        //connecter le user 
        //rediriger vers la page d'accueil
        break ; 
    case "disconnect":
        unset($_SESSION['user']);
        unset($_SESSION['cart']);
        $template = $twig->load('welcome.twig');
        echo $template->render(array(
            'titre' => "Welcome ! ",
            'connected' => false,
        ));

        break;
    case "addQuantity":
        $_SESSION['cart'][$_GET['id']]+=1;
        header("Location: controleur.php?action=cartconsult");
        //cartConsult($twig, $product, $connected);
        break;
    case "removeQuantity":
        $_SESSION['cart'][$_GET['id']]-=1;
        header("Location: controleur.php?action=cartconsult");
        break;

    case "order" : 
        order($twig, $order, $product);
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
