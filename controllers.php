<?php

require_once 'modele/modele2.php';

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
