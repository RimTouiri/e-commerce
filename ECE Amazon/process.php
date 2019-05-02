<?php

session_start();
require_once('includes/functions_panier.php');

try{
	$db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
	$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); //noms de champs en caractères minuscules
	$db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); //erreurs lancent exceptions					
}

catch(Exception $e){

	die('la connexion à la base de donnée a échoué!');

}

$name = $_POST['name'];
$rue = $_POST['rue'];
$ville = $_POST['ville'];
$pays_code = $_POST['pays_code'];
$date = $_POST['date'];
$prix = $_POST['prix'];
$id_trans = $_POST['id_trans'];
$user_id = $_SESSION['user_id'];
$code_devise = $_POST['code_devise'];

$db->query("INSERT INTO transactions (name, rue, ville, pays, date, id_trans, amount, code_devise, user_id) VALUES('$name', '$rue', '$ville', '$pays_code', '$date', '$id_trans', '$prix', '$code_devises', '$user_id')");

for($i = 0; $i<count($_SESSION['panier']['nomArticle']); $i++){
    
	$produit = $_SESSION['panier']['nomArticle'][$i];
	$quantite_article = $_SESSION['panier']['quantiteArticle'][$i];
	$insert = $db->query("INSERT INTO products_transactions (produit, quantite_article, id_trans) VALUES('$produit','$quantite_article', '$id_trans')");
	$select = $db->query("SELECT * FROM products WHERE titre='$produit'");
	$r = $select->fetch(PDO::FETCH_OBJ);
	$nbitem = $r->nbitem;
	$nbitem = $nbitem-$quantite_article;
	$update = $db->query("UPDATE products SET nbitem='$nbitem' WHERE titre='$produit'");
}

supprPanier();

?>