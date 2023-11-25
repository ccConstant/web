<?php
    session_start();
    // On inclut la connexion à la base
    require_once('connect.php');
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $id = strip_tags($_GET['id']);
        // On écrit notre requête
        $sql = 'SELECT * FROM `users` WHERE `id`=:id';
        // On prépare la requête
        $query = $db->prepare($sql);
        // On attache les valeurs
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        // On exécute la requête
        $query->execute();
        // On stocke le résultat dans un tableau associatif
        $user = $query->fetch();
        if(!$user){
            header('Location: index.php');
        }
    }else{
        header('Location: index.php');
    }
    require_once('close.php');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Liste des produits</title>
        </head>
        <body>
            <h1>Détails pour l’utilisateur <?= $user['login'] ?></h1>
            <p>ID : <?= $user['id'] ?></p>
            <p>Produit : <?= $user['login'] ?></p>
            <p>Prix : <?= $user['firstname'] ?></p>
            <p>Nombre : <?= $user['lastname'] ?></p>
            <p>Nombre : <?= $user['role'] ?></p>
            <p><a href="edit.php?id=<?= $user['id'] ?>">Modifier</a> <a
            href="delete.php?id=<?= $user['id'] ?>">Supprimer</a></p>
        </body>
    </html>