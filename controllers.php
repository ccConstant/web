<?php

define('LMAX_EMAIL', 150); //longueur du champ dans la base de données
define('LMAX_FIRSTNAME', 50); //longueur du champ dans la base de données
define('LMAX_LASTNAME', 50); //longueur du champ dans la base de données
define('LMAX_ADR', 50); //longueur du champ dans la base de données
define('LMAX_CP', 10); //longueur du champ dans la base de données
define('LMAX_TEL', 20); //longueur du champ dans la base de données
define('LMAX_VILLE', 50); //longueur du champ dans la base de données
define('LMAX_PAYS', 50); //longueur du champ dans la base de données
define('LMAX_PASSWORD', 40); //longueur du champ dans la base de données

require_once 'modele/products.php';

function list_action($twig, $categorie, $product){
  $template = $twig->load('products.twig');
  $products=$product->get_products_by_cat($categorie);
  foreach ($products as $key => $value) {
    $lien[$value->id]="./productimages/".$products[$key]->image;
  }
  echo $template->render(array(
      'titre' => "Welcome ! ",
      'products' => $products,
      'lien' => $lien,
  ));
}

function detail_action($product,$twig, $id){
  $template = $twig->load('detail.twig');
  $p=$product->get_product_by_id($id);
  $titre="Détails";
  echo $template->render(array(
            'titre' => $titre,
            'product' => $p,
            'lien' => "./productimages/".$p[0]->image,
            ));
}

function cartConsult($twig, $product){
  $quantite=array();
  $products=array();
  if (isset($_SESSION['cart'])){
    foreach ($_SESSION['cart'] as $key => $value) {  
      $products[$key]=$product->get_product_by_id($key) ; 
      $quantite[$key] = $value;
    }
  }
  $template = $twig->load('cart.twig');
  echo $template->render(array(
      'titre' => "Panier",
      'cart' => $products,
      'quantite' => $quantite

  ));
}

function createAccount($twig, $post){
  $erreurs=array();
  // vérification du format de l'adresse email
  $email = trim($_POST['mail']);
  var_dump(empty($email));
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

    // vérification des mots de passe
    $passe1 = trim($_POST['password']);
    $passe2 = trim($_POST['passwordconfirm']);
    if (empty($passe1) || empty($passe2)) {
        $erreurs[] = 'Les mots de passe ne doivent pas être vides.';
    }else{
      if (mb_strlen($passe1, 'UTF-8') > LMAX_PASSWORD){
          $erreurs[] = 'Le mot de passe ne peut pas dépasser ' . LMAX_PASSWORD . ' caractères.';
      }else{
        if ($passe1 !== $passe2) {
            $erreurs[] = 'Les mots de passe doivent être identiques.';
        }
      }
    }

    // vérification du prénom
    $firstname = trim($_POST['firstname']);

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
    $lastname = trim($_POST['lastname']);

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

    // vérification de ADDR1
    $addr1 = trim($_POST['addr']);

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
    $addr2 = trim($_POST['addr2']);

    if (!empty($addr2)) {
      if (mb_strlen($addr2, 'UTF-8') > LMAX_ADR){
          $erreurs[] = 'Le complément d\'adresse ne peut pas dépasser ' . LMAX_ADR . ' caractères.';
      }
      $noTags = strip_tags($addr2);
      if ($noTags != $addr2){
          $erreurs[] = 'Le complément d\'adresse ne peut pas contenir de code HTML.';
      }
    }

    // vérification de ADDR3
    $addr3 = trim($_POST['addr3']);

    if (!empty($addr3)) {
        if (mb_strlen($addr3, 'UTF-8') > LMAX_ADR){
            $erreurs[] = 'Le deuxième complément d\'adresse ne peut pas dépasser ' . LMAX_ADR . ' caractères.';
        }
        $noTags = strip_tags($addr3);
        if ($noTags != $addr3){
            $erreurs[] = 'Le deuxième complément d\'adresse ne peut pas contenir de code HTML.';
        }
    }


     // vérification de cp
     $cp = trim($_POST['postcode']);

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
         }
     }
  

    // pas d'erreurs ==> enregistrement de l'utilisateur
    /*$nomprenom = em_bd_proteger_entree($bd, $nomprenom);

    $passe1 = password_hash($passe1, PASSWORD_DEFAULT);
    $passe1 = em_bd_proteger_entree($bd, $passe1);

  


    $sql = "INSERT INTO clients(cliNomPrenom, cliEmail, cliDateNaissance, cliPassword, cliAdresse, cliCP, cliVille, cliPays)
            VALUES ('$nomprenom', '$email', $aaaammjj, '$passe1', '', 0, '', '')";

    mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);

    // mémorisation de l'ID dans une variable de session
    // cette variable de session permet de savoir si le client est authentifié
    $_SESSION['id'] = mysqli_insert_id($bd);

    // libération des ressources
    mysqli_close($bd);

    // redirection vers la page précédente
    if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
      header("Location: {$_SERVER['HTTP_REFERER']}");
    }else{
      header("Location: ../index.php");
    }
    exit();*/


  /*if (!isset($post['address']) || $post['address'] == "" ){
    array_push($erreurs,"Vous devez entrer une adresse");
  }
  if (!isset($post['city']) || $post['city'] == "" ){
    array_push($erreurs,"Vous devez entrer une ville");
  }
  if (!isset($post['postalcode']) || $post['postalcode'] == "" ){
    array_push($erreurs,"Vous devez entrer un code postal");
  }
  if (!isset($post['phoneNumber']) || $post['phoneNumber'] == ""){
    array_push($erreurs,"Vous devez entrer un numéro de téléphone");
  }*/
  if (count($erreurs) != 0){
    $template = $twig->load('subscribe.twig');
    echo $template->render(array(
        'titre' => "Inscription",
        'errors' => $erreurs,
        'data' => $post
    ));
  }
}



    //vérifier si les deux mots de passe sont identiques?
    //vérifier si le login n'existe pas déjà?
    //vérifier si le mail n'existe pas déjà?
    //si tout est ok, créer le compte et rediriger vers la page d'accueil en disant que c'est ok

/*
function suppr_action($cont, $id){
  return ($cont->delete_friend_by_id($id));
}

function patch_action($cont, $id, $naissance, $adresse, $ville){
  return ($cont->patch($id,$naissance,$adresse,$ville ));
}

function add_action($cont, $contact){
  return ($cont->add_friend($contact));
}*/
