<?php 

function adminConsult($twig, $isAdmin, $connected, $order){
    
    $orders=$order->get_orders();
    
    $template = $twig->load('navbar.twig');
    echo $template->render(array(
        'connected' => $connected,
        'admin' => $isAdmin,
    ));
    $template = $twig->load('adminConsult.twig');
    echo $template->render(array(
        'orders' => $orders,
    ));
}