<?php
try{
 // Connexion à la bdd
 $db = new PDO('mysql:host=localhost;dbname=crud', 'root','');
 $db->exec('SET NAMES "UTF8"');
} catch (PDOException $e){
 echo 'Erreur : '. $e->getMessage();
 die();
}