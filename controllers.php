<?php

require_once 'modele/products.php';

function list_action($twig, $categorie, $product){
  $template = $twig->load('products.twig');
  $products=$product->get_products_by_cat($categorie);
  echo $template->render(array(
      'titre' => "Welcome ! ",
      'products' => $products,
  ));
}

function detail_action($product,$twig, $id){
  $template = $twig->load('detail.twig');
  $p=$product->get_product_by_id($id);
  $titre="Détails";
  echo $template->render(array(
            'titre' => $titre,
            'product' => $p,
            'lien' => "productimages/".$p[0]->image,
            ));
}

function cartConsult($twig, $product){
  foreach ($_SESSION['cart'] as $key => $value) {  
    $products[$key]=$product->get_product_by_id($key) ; 
    $quantite[$key] = $value;
  }
  $template = $twig->load('cart.twig');
  echo $template->render(array(
      'titre' => "Panier",
      'cart' => $products,
      'quantite' => $quantite

  ));
}

function createAccount($twig){

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
