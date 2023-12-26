<?php

function order($twig, $order, $product, $post, $connected, $isAdmin){
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
    if (isset($_SESSION['orderAdress']) && $_SESSION['orderAdress']!=null){
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
      $order=$order->add_order($infos, $registered);

      $lastOrder=array([
        'customer_id' => $infos['customer_id'],
        'delivery_add_id' => $infos['delivery_add_id'],
        'payment_type' => $infos['payment_type'],
        'date' => $infos['date'],
        'total' => $infos['total'],
        'products' => $_SESSION['cart'],
        'order_id' => $order,
      ]);

      $_SESSION['lastOrder']=$lastOrder;
      $_SESSION['cart']=null;
      $_SESSION['orderAdress']=null;
      $template = $twig->load('navbar.twig');
      echo $template->render(array(
          'connected' => $connected,
          'admin' => $isAdmin,
      ));
      $template = $twig->load('done.twig');
      echo $template->render(array(
          'connected' => true,
        ));
  }