<?php
    // On inclut la connexion à la base
    //require_once('connect.php');
    // On écrit notre requête
    //$sql = 'SELECT * FROM `users`';
    // On prépare la requête
    //$query = $db->prepare($sql);
    // On exécute la requête
    //$query->execute();
    // On stocke le résultat dans un tableau associatif
    //$result = $query->fetchAll(PDO::FETCH_ASSOC);
    //require_once('close.php');
    ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Liste des utilisateurs</title>
    </head>
    <body>
        <h1>Liste des utilisateurs</h1>
        <table>
            <thead>
                <th>ID</th>
                <th>Login</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Rôle</th>
            </thead>
        <tbody>
            <!--php
            foreach($result as $user){
            ?>
            -->
                <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['login'] ?></td>
                <td><?= $user['firstname'] ?></td>
                <td><?= $user['lastname'] ?></td>
                <td><?= $user['role'] ?></td>
                <td><a href="details.php?id=<?= $user['id'] ?>">Voir</a> <a
                href="edit.php?id=<?= $user['id'] ?>">Modifier</a> <a href="delete.php?id=<?=
                $user['id'] ?>">Supprimer</a></td>
                </tr>
            <!--?php
            }
            ?>-->
        </tbody>
    </table>
    <a href="add.php">Ajouter</a>
    </body>
</html>
