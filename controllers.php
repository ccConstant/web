<?php
require_once 'modele/modele2.php';

function list_action($twig, $categorie){
  $template = $twig->load('products.twig');
  $product=new Products();
  $products=$product->get_products_by_cat($categorie);
  echo $template->render(array(
      'titre' => "Welcome ! ",
      'products' => $products,
  ));
}

/*function detail_action($cont,$twig, $id,$message=''){
  $ami = $cont->get_friend_by_id($id);
  $template = $twig->load('detail.twig.html');
  $titre="Détails";
  echo $template->render(array(
            'titre' => $titre,
            'ami' => $ami,
            'message' => $message
            ));
}

function suppr_action($cont, $id){
  return ($cont->delete_friend_by_id($id));
}

function patch_action($cont, $id, $naissance, $adresse, $ville){
  return ($cont->patch($id,$naissance,$adresse,$ville ));
}

function add_action($cont, $contact){
  return ($cont->add_friend($contact));
}*/
