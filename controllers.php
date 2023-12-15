<?php

define('LMAX_EMAIL', 150); //longueur du champ dans la base de données
define('LMAX_FIRSTNAME', 50); //longueur du champ dans la base de données
define('LMAX_LASTNAME', 50); //longueur du champ dans la base de données
define('LMAX_ADR', 50); //longueur du champ dans la base de données
define('LMAX_CP', 10); //longueur du champ dans la base de données
define('LMAX_TEL', 10); 
define('LMAX_VILLE', 50); //longueur du champ dans la base de données
define('LMAX_PAYS', 50); //longueur du champ dans la base de données
define('LMAX_PASSWORD', 40); //longueur du champ dans la base de données

require_once 'modele/products.php';

function list_action($twig, $categorie, $product, $connected){
  $products=$product->get_products_by_cat($categorie);
  $lien=array();
  foreach ($products as $key => $value) {
    $lien[$value->id]="./productimages/".$products[$key]->image;
  }
  $template = $twig->load('navbar.twig');
  echo $template->render(array(
      'connected' => $connected,
  ));
  $template = $twig->load('products.twig');
  echo $template->render(array(
      'titre' => "Welcome ! ",
      'products' => $products,
      'lien' => $lien,
  ));
}

function detail_action($product,$twig, $id){
  $template = $twig->load('navbar.twig');
  echo $template->render(array(
      'connected' => $connected,
  ));
  $template = $twig->load('detail.twig');
  $p=$product->get_product_by_id($id);
  $titre="Détails";

  echo $template->render(array(
            'titre' => $titre,
            'product' => $p,
            'lien' => "./productimages/".$p[0]->image,
            ));
}

function cartConsult($twig, $product, $connected){
  $quantite=array();
  $products=array();
  $total=0; 
  $sousTotal=array();
  $lien=array();
  if (isset($_SESSION['cart'])){
    foreach ($_SESSION['cart'] as $key => $value) {
      if ($value != 0) {
        $product_data=$product->get_product_by_id($key);
        $total+=$value*$product_data[0]->price;
        $products[$key]=$product_data;
        $quantite[$key] = $value;
        $lien[$key]="./productimages/".$product_data[0]->image;
        $sousTotal[$key]=$value*$product_data[0]->price;
      }
    }
  }
  $template = $twig->load('navbar.twig');
  echo $template->render(array(
      'connected' => $connected,
  ));
  $template = $twig->load('cart.twig');
  echo $template->render(array(
      'titre' => "Panier",
      'cart' => $products,
      'quantite' => $quantite,
      'lien' => $lien,
      'total' => $total,
      'sousTotal' => $sousTotal,

  ));
}

function createAccount($twig, $post, $user, $login){
  $erreurs=verif_entree($post);
  $res=$user->get_user_by_email($post['mail']);
  if (count($res)!=0){
    $erreurs[] = 'Cette adresse mail est déjà utilisée.';
  }

  if (count($erreurs) != 0){
    $template = $twig->load('navbar.twig');
    echo $template->render(array(
        'connected' => $connected,
    ));
    $template = $twig->load('subscribe.twig');
    echo $template->render(array(
        'titre' => "Inscription",
        'errors' => $erreurs,
        'data' => $post
    ));
  }else{
    $password=password_hash($post['password'], PASSWORD_DEFAULT);
    $post['password']=$password;
    $_SESSION['user']=$user->add_user($post);
    $login->add_login(array('customer_id' => $_SESSION['user'], 'username' => $post['mail'], 'password' => $password));
    $user=$user->get_user_by_id($_SESSION['user']);
    $template = $twig->load('navbar.twig');
    echo $template->render(array(
        'connected' => true,
    ));
    $template = $twig->load('example.twig');
    $title="Bienvenu(e) ".$user[0]->forname. "!";
    echo $template->render(array(
        'title' => $title,
        'message' => 'Votre compte a bien été créé ! ',
      ));
    return true;
  }
  return false;
}

function verif_mail($post){
 $erreurs=array();
 // vérification du format de l'adresse email
 $email = trim($post['mail']);
 if (empty($email)) {
     $erreurs[] = 'L\'adresse mail ne doit pas être vide.';
 }else {
     if (mb_strlen($email, 'UTF-8') > LMAX_EMAIL){
         $erreurs[] = 'L\'adresse mail ne peut pas dépasser '.LMAX_EMAIL.' caractères.';
       }
       // la validation faite par le navigateur en utilisant le type email pour l'élément HTML input
       // est moins forte que celle faite ci-dessous avec la fonction filter_var()
       // Exemple : 'l@i' passe la validation faite par le navigateur et ne passe pas
       // celle faite ci-dessous
       if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
           $erreurs[] = 'L\'adresse mail n\'est pas valide.';
       }
   }
   return $erreurs;
}

function verif_mdp($passe1){
  $erreurs=array();
  // vérification des mots de passe
  if (empty($passe1)) {
      $erreurs[] = 'Le mot de passe ne doit pas être vide.';
  }else{
    if (mb_strlen($passe1, 'UTF-8') > LMAX_PASSWORD){
        $erreurs[] = 'Le mot de passe ne peut pas dépasser ' . LMAX_PASSWORD . ' caractères.';
    }else{
      $noTags = strip_tags($passe1);
       if ($noTags != $passe1){
           $erreurs[] = 'Le mots de passe ne peut pas contenir de code HTML.';
      }
    }
  }
  return $erreurs;
}


function verif_entree($post, $verifMdp=true){
  $erreurs=array();

  // vérification du prénom
  $firstname = trim($post['firstname']);

  if (empty($firstname)) {
      $erreurs[] = 'Le prénom doit être renseigné.';
  }
  else {
      if (mb_strlen($firstname, 'UTF-8') > LMAX_FIRSTNAME){
          $erreurs[] = 'Le prénom ne peut pas dépasser ' . LMAX_FIRSTNAME . ' caractères.';
      }
      $noTags = strip_tags($firstname);
      if ($noTags != $firstname){
          $erreurs[] = 'Le prénom ne peut pas contenir de code HTML.';
      }
      else {
          mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
          if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $firstname)){
              $erreurs[] = 'Le prénom contient des caractères non autorisés';
          }
      }
  }


  // vérification du nom
  $lastname = trim($post['lastname']);

  if (empty($lastname)) {
      $erreurs[] = 'Le nom doit être renseigné.';
  }
  else {
      if (mb_strlen($lastname, 'UTF-8') > LMAX_LASTNAME){
          $erreurs[] = 'Le nom ne peut pas dépasser ' . LMAX_FIRSTNAME . ' caractères.';
      }
      $noTags = strip_tags($lastname);
      if ($noTags != $lastname){
          $erreurs[] = 'Le nom ne peut pas contenir de code HTML.';
      }
      else {
          mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
          if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $lastname)){
              $erreurs[] = 'Le nom contient des caractères non autorisés';
          }
      }
  }

  $erreurs=array_merge($erreurs, verif_mail($post));

     // vérification de phone number
     $phone = trim($post['phoneNumber']);

     if (empty($phone)) {
         $erreurs[] = 'Le numéro de téléphone doit être renseigné.';
     }
     else {
         if (mb_strlen($phone, 'UTF-8') > LMAX_TEL){
             $erreurs[] = 'Le numéro de téléphone ne peut pas dépasser ' . LMAX_TEL . ' caractères.';
         }
         $noTags = strip_tags($phone);
         if ($noTags != $phone){
             $erreurs[] = 'Le numéro de téléphone ne peut pas contenir de code HTML.';
         }else{
          mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
            if( !mb_ereg_match('^[0-9]{10}$', $phone)){
                $erreurs[] = 'Le numéro de téléphone doit contenir 10 chiffres.';
            }
         }
     }

    array_merge($erreurs, verif_addr($post));
    
    // vérification des mots de passe

  if ($verifMdp){
    $passe1 = trim($post['password']);
    $passe2 = trim($post['passwordconfirm']);
      $erreurs=array_merge($erreurs, verif_mdp($passe1));
      if ($passe1 !== $passe2) {
          $erreurs[] = 'Les mots de passe doivent être identiques.';
      }
  }
    return $erreurs;
  }

  function verif_addr($post ){
    $erreurs=array();


    // vérification de ADDR1
    $addr1 = trim($post['addr']);

    if (empty($addr1)) {
        $erreurs[] = 'L\'adresse doit être renseignée.';
    }
    else {
        if (mb_strlen($addr1, 'UTF-8') > LMAX_ADR){
            $erreurs[] = 'L\'adresse ne peut pas dépasser ' . LMAX_ADR . ' caractères.';
        }
        $noTags = strip_tags($addr1);
        if ($noTags != $addr1){
            $erreurs[] = 'L\'adresse ne peut pas contenir de code HTML.';
        }
    }


    // vérification de ADDR2
    $addr2 = trim($post['addr2']);

    if (!empty($addr2)) {
      if (mb_strlen($addr2, 'UTF-8') > LMAX_ADR){
          $erreurs[] = 'Le complément d\'adresse ne peut pas dépasser ' . LMAX_ADR . ' caractères.';
      }
      $noTags = strip_tags($addr2);
      if ($noTags != $addr2){
          $erreurs[] = 'Le complément d\'adresse ne peut pas contenir de code HTML.';
      }
    }

      // vérification de ville

      $ville = trim($post['city']);
      if (empty($ville)) {
          $erreurs[] = 'La ville doit être renseignée.';
      }else{
        if (mb_strlen($ville, 'UTF-8') > LMAX_VILLE){
            $erreurs[] = 'La ville ne peut pas dépasser ' . LMAX_VILLE . ' caractères.';
        }
        $noTags = strip_tags($ville);
        if ($noTags != $ville){
            $erreurs[] = 'La ville ne peut pas contenir de code HTML.';
        }
      }


    // vérification de cp
    $cp = trim($post['postcode']);

    if (empty($cp)) {
        $erreurs[] = 'Le code postal doit être renseigné.';
    }
    else {
        if (mb_strlen($cp, 'UTF-8') > LMAX_CP){
            $erreurs[] = 'Le code postal ne peut pas dépasser ' . LMAX_CP . ' caractères.';
        }
        $noTags = strip_tags($cp);
        if ($noTags != $cp){
            $erreurs[] = 'Le code postal ne peut pas contenir de code HTML.';
        }else{
          mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
            if( !mb_ereg_match('^[0-9]{5}$', $cp)){
                $erreurs[] = 'Le code postal doit contenir 5 chiffres.';
            }
        }
    }
    return $erreurs;
  }


  function connectUser($twig, $post, $login, $connected, $user){
    $erreurs=verif_mail($post);
    $erreurs=array_merge($erreurs, verif_mdp($post['password']));
    if (count($erreurs) != 0){
      $template = $twig->load('navbar.twig');
      echo $template->render(array(
          'connected' => $connected,
      ));
      $template = $twig->load('login.twig');
      echo $template->render(array(
          'titre' => "Connexion",
          'errors' => $erreurs,
          'data' => $post
      ));
    }else{
      $login=$login->get_login_by_email($post['mail']);
      if (count($login) == 0){
        $erreurs[] = 'L\'adresse mail n\'existe pas.';
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
        ));
        $template = $twig->load('login.twig');
        echo $template->render(array(
            'titre' => "Connexion",
            'errors' => $erreurs,
            'data' => $post
        ));
      }else{
        if (password_verify($post['password'], $login[0]->password)){
          $_SESSION['user']=$login[0]->customer_id;
          $template = $twig->load('navbar.twig');
          echo $template->render(array(
              'connected' => true,
          ));
          $template = $twig->load('example.twig');
          $user_=$user->get_user_by_id($_SESSION['user']);
          $title="Bienvenu(e) ".$user_[0]->forname. "!";
          echo $template->render(array(
              'title' => $title,
              'message' => 'Vous êtes connecté ! ',
            ));
          return true;
        }else{
          $erreurs[] = 'Le mot de passe est incorrect.';
          $template = $twig->load('navbar.twig');
          echo $template->render(array(
              'connected' => $connected,
          ));
          $template = $twig->load('login.twig');
          echo $template->render(array(
              'titre' => "Connexion",
              'errors' => $erreurs,
              'data' => $post
          ));
        }
      }

    }
  }

  function payment($twig, $post, $user, $connected, $deliveryAdresses){
    
    //cas non co
    if (!$connected){
      $erreurs=verif_entree($post, false);
      $res=$user->get_user_by_email($post['mail']);
      if (count($res)!=0){
        $erreurs[] = 'Cette adresse mail est déjà utilisée.';
      }
    
      if (count($erreurs) != 0){
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => $connected,
        ));
        $template = $twig->load('buyNotConnected.twig');
        echo $template->render(array(
            'errors' => $erreurs,
            'data' => $post
        ));

        
      }else{

        $id=$user->add_user($post, 0);
        $_SESSION['userTemp']=$id;
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => false,
        ));
        $template = $twig->load('payment.twig');
        echo $template->render(array(

          ));
      }

    //cas co 
    }else{
      $user=$user->get_user_by_id($_SESSION['user']);
      if (isset($post['new']) && $post['new']==1){
        //cas où on choisit l'adresse préremplie
        $template = $twig->load('navbar.twig');
        echo $template->render(array(
            'connected' => true,
        ));
        $template = $twig->load('payment.twig');
        echo $template->render(array(
          ));
      }else{
        //cas où on saisit une nouvelle adresse 
        $erreurs=verif_addr($post);
        //si erreurs
        if (count($erreurs) != 0){
          $template = $twig->load('navbar.twig');
          echo $template->render(array(
              'connected' => $connected,
          ));
          $template = $twig->load('buyConnected.twig');
          echo $template->render(array(
              'errors' => $erreurs,
              'data' => $user[0],
              'data2' => $post
          ));
        //si pas d'erreurs
         //on créé une nouvelle delevery adress et on l'ajoute à la session
        }else{
          $newAdress=array();
          $newAdress['firstname']=$user[0]->forname;
          $newAdress['lastname']=$user[0]->surname;
          $newAdress['city']=$post['city'];
          $newAdress['addr']=$post['addr'];
          $newAdress['addr2']=$post['addr2'];
          $newAdress['postcode']=$post['postcode'];
          $newAdress['phone']=$user[0]->phone;
          $newAdress['email']=$user[0]->email;
          $id=$deliveryAdresses->add_deliveryAdresses($newAdress);
          $_SESSION['orderAdress']=$id;
          $template = $twig->load('navbar.twig');
          echo $template->render(array(
              'connected' => true,
          ));
          $template = $twig->load('payment.twig');
          echo $template->render(array(
            ));

        }
      }
    }
  }
    

        //cas co 




    //cas co 
    //if il choisit l'adresse actuelle


    //if il choisit une nouvelle adresse

    //verif de la nouvelle adresse 
    //si pas d'erreurs 
    //ajouter l'adresse à la base de données
    //rediriger vers la page de paiement




  function order($twig, $order, $product, $post){
    $infos=array();
    $infos['delivery_add_id']=null;
    $infos['date']=date("Y-m-d H:i:s");
    if (isset($post['new']) && $post['new'] =="1"){
      $infos['payment_type']="paypal"; 
    }else{
      $infos['payment_type']="cheque"; 
    }
    $infos['status']=2;
    $infos['session']=0;
    $registered=0;
    $total=0; 
    foreach ($_SESSION['cart'] as $key => $value) {
      if ($value != 0) {
        $product_data=$product->get_product_by_id($key);
        $quantity=$product_data[0]->quantity-$value; 
        $product->set_quantity($key, $quantity);
        $total+=$value*$product_data[0]->price;
      }
    }
    if ($_SESSION['orderAdress']!=null){
      $infos['delivery_add_id']=$_SESSION['orderAdress'];
    }
      if (isset($_SESSION['user']) && $_SESSION['user']!=null){
        $infos['session']=session_id();
        $infos['customer_id']=$_SESSION['user'];
        $registered=1;
      }else{
        $infos['customer_id']=$_SESSION['userTemp'];

      }

      $infos['total']=$total;
      $_SESSION['cart']=null;
      $_SESSION['orderAdress']=null;
      $order=$order->add_order($infos, $registered);
      $template = $twig->load('done.twig');
      echo $template->render(array(
          'connected' => true,
        ));
  }
