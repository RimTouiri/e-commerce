<?php

	session_start();

	try{
		$db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
		$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // les noms de champs seront en caractères minuscules
		$db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); // les erreurs lanceront des exceptions
		$db->exec('SET NAMES utf8');				
	}
    catch(Exception $e){

		die('La connexion à la base de donnée a échoué');

	}
?>

<html>
	<head>
        <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
        <link rel="icon" href="../img/favicon.ico" type="image/x-icon"> <!-- affichage favicon -->
        <link href="style/styles.css" type="text/css" rel="stylesheet"/>
		<link href="style/bootstrap.css" type="text/css" rel="stylesheet"/>
        <meta charset="utf8">
	</head>
    
	<header>
        <a class="admin" href="admin/index.php"><img width="30px" src="img/admin.png"></a>
        <br>
        <br>
        <center><h1>ECE Amazon</h1></center>
        <br>
        <br>

		<ul class="menu">
			<li><a href="index.php">Accueil</a></li>
			<li><a href="categories.php">Catégories</a></li>
            <li><a href="ventes_flash">Ventes Flash</a></li>
			<li><a href="panier.php">Panier</a></li>
			
            <?php 
            if(!isset($_SESSION['user_id'])){
            ?>
            
			<li><a href="inscription.php">Inscription</a></li>
			<li><a href="connexion.php">Connexion</a></li>
			
            <?php 
            }else{ 
            ?>
            
			<li><a href="compte.php">Votre Compte</a></li>
			
            <?php 
            } 
            ?>
            
            <li><a href="vendeur.php">Vendre</a></li>
            <li><a href="contact.php">Contact</a></li>
		</ul>
	</header>
</html>