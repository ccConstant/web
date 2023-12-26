<?php

require_once 'modele/products.php';


function list_action($twig, $categorie, $product, $connected, $isAdmin){
  $products=$product->get_products_by_cat($categorie);
  $lien=array();
  foreach ($products as $key => $value) {
    $lien[$value->id]="./productimages/".$products[$key]->image;
  }
  $template = $twig->load('navbar.twig');
  echo $template->render(array(
      'connected' => $connected,
      'admin' => $isAdmin,
  ));
  $template = $twig->load('products.twig');
  echo $template->render(array(
      'titre' => "Welcome ! ",
      'products' => $products,
      'lien' => $lien,
  ));
}

function detail_action($product,$twig, $id, $connected, $isAdmin){
  $template = $twig->load('navbar.twig');
  echo $template->render(array(
      'connected' => $connected,
      'admin' => $isAdmin,
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


function cartConsult($twig, $product, $connected, $isAdmin){
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
      'admin' => $isAdmin,
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