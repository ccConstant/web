<?php

session_start();
require("modele/connect.php");
require_once 'modele/products.php';
require_once 'modele/users.php';
require_once 'modele/logins.php';
require_once 'modele/orders.php';
require_once 'modele/admin.php';
require_once 'modele/delivery_adresses.php';
include 'vendor/autoload.php';
// le dossier ou on trouve les templates
$loader = new Twig\Loader\FilesystemLoader('vue');
// initialiser l'environement Twig
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());


include 'productController.php';
include 'userController.php';
include 'orderController.php';
include 'adminController.php';
include 'FacturePDF.php';

// on lit une action en parametre
// par defaut, 'list'
$action = $_GET['action'] ?? 'welcome';
$categorie = $_GET['categorie'] ?? 0 ;
$message = "";
$product=new Products();
$login=new Logins();
$user=new Users();
$order=new Orders();
$admin=new Admin();
$deliveryAdresses=new Delivery_adresses();
$connected=false ;
$isAdmin=false;
if (isset($_SESSION['user']) && $_SESSION['user']!=null){   
    $connected=true;
}
if (isset($_SESSION['admin']) && $_SESSION['admin']!=null){   
    $isAdmin=true;
}
switch ($action) {
    case "welcome":
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
            'admin' => $isAdmin,
        ));
        $template = $twig->load('welcome.twig');
        echo $template->render(array(
            'titre' => "Welcome ! ",
        ));
        break;
    case "list": 
        list_action($twig, $categorie, $product, $connected, $isAdmin);
        break;
    case "detail":
        detail_action($product,$twig, $_GET['id'], $connected, $isAdmin);
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
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
            'admin' => $isAdmin,
        ));
        $template = $twig->load('example.twig');
        echo $template->render(array(
            'message' => "Produit ajouté au panier ! ",
        ));
        break;
    case "cartconsult": 
        cartConsult($twig, $product, $connected, $isAdmin);
        break;
    case "subscribe" : 
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
            'admin' => $isAdmin,
        ));
        $template = $twig->load('subscribe.twig');
        echo $template->render(array(
        ));
        break;
    case "createAccount":
        $connected=createAccount($twig, $_POST, $user, $login);
        break;
    case "buy": 
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
            'admin' => $isAdmin,
        ));
        if (isset($_SESSION['admin']) && $_SESSION['admin']!=null){
            $template = $twig->load('cart.twig');
            echo $template->render(array(
                'message' => "Vous ne pouvez pas commander en tant qu'admin ! ",
            ));
        }else{
            $count=0;
            if($_SESSION['cart']!=null){
                foreach($_SESSION['cart'] as $key => $value){
                    if($value==0){
                        $count++;
                    }
                }
                if ($count!=count($_SESSION['cart'])){
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
                }else{
                    $template = $twig->load('welcome.twig');
                    echo $template->render(array(
                    ));
                }
            }else{
                $template = $twig->load('welcome.twig');
                echo $template->render(array(
                ));
            }
            
        }
        break;
    case "payment":
        payment($twig, $_POST, $user, $connected, $deliveryAdresses, $isAdmin);
        break;
    case "login":
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
            'admin' => $isAdmin,
        ));
        $template = $twig->load('login.twig');
        echo $template->render(array(
        ));
        break;
    case "deleteFromCart" : 
        $_SESSION['cart'][$_GET['id']]=0;
        cartConsult($twig, $product, $connected, $isAdmin);
        break;
    case "connectUser":
        connectUser($twig, $_POST, $login, $connected, $user, $admin, $isAdmin);
        //connecter le user 
        //rediriger vers la page d'accueil
        break ; 
    case "disconnect":
        unset($_SESSION['user']);
        unset($_SESSION['admin']);
        unset($_SESSION['cart']);
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => false,
            'admin' => $isAdmin,
        ));
        $template = $twig->load('welcome.twig');
        echo $template->render(array(
            'titre' => "Welcome ! ",
        ));

        break;
    case "addQuantity":
        $product_item=$product->get_product_by_id($_GET['id']);
        //convert string to int
        $quantity=intval($product_item[0]->quantity);
        $cart=intval($_SESSION['cart'][$_GET['id']]);
        if($quantity>$cart){
            $_SESSION['cart'][$_GET['id']]+=1;
        }
        header("Location: controleur.php?action=cartconsult");
        break;
    case "removeQuantity":
        $_SESSION['cart'][$_GET['id']]-=1;
        header("Location: controleur.php?action=cartconsult");
        break;

    case "order" : 
        order($twig, $order, $product, $_POST, $connected, $isAdmin);
        break;
    case "adminConsult":
        adminConsult($twig, $isAdmin, $connected, $order);
        break;
    case "pdf":
        pdf($product, $user);
        break;
    case "validateOrder":
        validateOrder($twig, $order, $isAdmin, $connected);
        break;
    default:
    $template = $twig->load('welcome.twig');
}

//header("refresh:4;url=controleur.php");
