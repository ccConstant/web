<?php

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
  $errors=array();
  if (!isset($post['email'])|| $post['email'] == ""){
    array_push($errors,"Vous devez entrer un email");
  }
  if (!isset($post['firstname']) || $post['firstname'] == "" ){
    array_push($errors,"Vous devez entrer un prénom");
  }
  if (!isset($post['lastname']) || $post['lastname'] == "" ){
    array_push($errors,"Vous devez entrer un nom");
  }
  if (!isset($post['address']) || $post['address'] == "" ){
    array_push($errors,"Vous devez entrer une adresse");
  }
  if (!isset($post['city']) || $post['city'] == "" ){
    array_push($errors,"Vous devez entrer une ville");
  }
  if (!isset($post['postalcode']) || $post['postalcode'] == "" ){
    array_push($errors,"Vous devez entrer un code postal");
  }
  if (!isset($post['phoneNumber']) || $post['phoneNumber'] == ""){
    array_push($errors,"Vous devez entrer un numéro de téléphone");
  }
  if (!isset($post['password']) || $post['password'] == ""){
    array_push($errors,"Vous devez entrer un mot de passe");
  }
  if (!isset($post['passwordconfirm']) || $post['passwordconfirm'] == ""){
    array_push($errors,"Vous devez confirmer votre mot de passe");
  }
  if ($post['password'] != $post['passwordconfirm']){
    array_push($errors,"Les mots de passe ne sont pas identiques");
  }
  if (count($errors) != 0){
    $template = $twig->load('subscribe.twig');
    echo $template->render(array(
        'titre' => "Inscription",
        'errors' => $errors,
        'data' => $post
    ));
  }


    //vérifier si les deux mots de passe sont identiques?
    //vérifier si le login n'existe pas déjà?
    //vérifier si le mail n'existe pas déjà?
    //si tout est ok, créer le compte et rediriger vers la page d'accueil en disant que c'est ok
}

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
