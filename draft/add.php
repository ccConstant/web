<?php
    $sql = "INSERT INTO `users` (`login`, `password`, `firstname`,`lastname`,
    `description`, `role`, `enabled`) VALUES (:login, :password,
    :firstname, :lastname, :description, :role, :enabled);";
    $query = $db->prepare($sql);
    $query->bindValue(':login', $login, PDO::PARAM_STR);
    $query->bindValue(':lastname', $lastname, PDO::PARAM_STR);
    $query->bindValue(':role', $role, PDO::PARAM_INT);
    $query->execute();