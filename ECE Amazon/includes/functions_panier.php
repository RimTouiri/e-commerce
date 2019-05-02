<?php

function creePanier(){
   try{

      $db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
      $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // les noms de champs seront en caractères minuscules
      $db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); // les erreurs lanceront des exceptions
      $db->exec('SET NAMES utf8');           
   }

   catch(Exception $e){

      die('Veuillez vérifier la connexion à la base de données');

   }

   if (!isset($_SESSION['panier'])){
      $_SESSION['panier']=array();
      $_SESSION['panier']['nomArticle'] = array();
      $_SESSION['panier']['slugProduit'] = array();
      $_SESSION['panier']['quantiteArticle'] = array();
      $_SESSION['panier']['prixProduit'] = array();
      $_SESSION['panier']['verrou'] = false;
      $select = $db->query("SELECT tva FROM products");
      $data = $select->fetch(PDO::FETCH_OBJ);
      $_SESSION['panier']['tva'] = $data->tva;
   }
   return true;
}

function ajouterArticle($slugProduit,$quantiteArticle,$prixProduit){

   try{

      $db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
      $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // les noms de champs seront en caractères minuscules
      $db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); // les erreurs lanceront des exceptions
      $db->exec('SET NAMES utf8');            
   }

   catch(Exception $e){

      die('Veuillez vérifier la connexion à la base de données');

   }

   if (creePanier() && !estBloquer())
   {

      $s = $db->query("SELECT titre FROM products WHERE slug = '$slugProduit'");
      $r = $s->fetch(PDO::FETCH_OBJ);
      $nomArticle = $r->titre;

      $positionProduit = array_search($slugProduit,  $_SESSION['panier']['slugProduit']);

      if ($positionProduit !== false)
      {
         $_SESSION['panier']['quantiteArticle'][$positionProduit] += $quantiteArticle ;
      }
      else
      {  
         array_push( $_SESSION['panier']['nomArticle'],$nomArticle);
         array_push( $_SESSION['panier']['slugProduit'],$slugProduit);
         array_push( $_SESSION['panier']['quantiteArticle'],$quantiteArticle);
         array_push( $_SESSION['panier']['prixProduit'],$prixProduit);
      }
   }
   else{
   echo "Un problème est survenu veuillez contacter l'administrateur du site.";
   }
}

function modifNbreArticle($slugProduit,$quantiteArticle){
   //Si le panier éxiste
   if (creePanier() && !estBloquer())
   {
      //Si la quantité est positive on modifie sinon on supprime l'article
      if ($quantiteArticle > 0)
      {
         //Recharche du produit dans le panier
         $positionProduit = array_search($slugProduit,  $_SESSION['panier']['slugProduit']);

         if ($positionProduit !== false)
         {
            $_SESSION['panier']['quantiteArticle'][$positionProduit] = $quantiteArticle ;
         }
      }
      else{
      supprimerArticle($slugProduit);
      }
   }
   else{
   echo "Un problème est survenu veuillez contacter l'administrateur du site.";
   }
}

function supprimerArticle($slugProduit){
   var_dump($slugProduit);

   if (creePanier() && !estBloquer())
   {
      for($i = 0; $i < count($_SESSION['panier']['slugProduit']); $i++)
      {
         if ($_SESSION['panier']['slugProduit'][$i] == $slugProduit)
         {
            unset( $_SESSION['panier']['nomArticle'][$i]);
            unset( $_SESSION['panier']['slugProduit'][$i]);
            unset( $_SESSION['panier']['quantiteArticle'][$i]);
            unset( $_SESSION['panier']['prixProduit'][$i]);
         }

      }

   }else{
   echo "Un problème est survenu veuillez contacter l'administrateur du site.";
   }
}

function PrixGlobal(){
   $tot=0;
   for($i = 0; $i < count($_SESSION['panier']['slugProduit']); $i++)
   {
      $tot += $_SESSION['panier']['quantiteArticle'][$i] * $_SESSION['panier']['prixProduit'][$i];
   }
   return $tot;
}

function PrixGlobalTva(){

   $tot=0;
   for($i = 0; $i < count($_SESSION['panier']['slugProduit']); $i++)
   {
      $tot += $_SESSION['panier']['quantiteArticle'][$i] * $_SESSION['panier']['prixProduit'][$i];
   }
   return $tot + $tot*$_SESSION['panier']['tva']/100;
}

function supprPanier(){
   unset($_SESSION['panier']);
}

function estBloquer(){
   if (isset($_SESSION['panier']) && $_SESSION['panier']['verrou']){
       return true;
   }else{
       return false;
   }  
}

function compterArticles(){
   if (isset($_SESSION['panier'])){
       return count($_SESSION['panier']['slugProduit']);
   }else{
       return 0;
   }

}

function CalculFraisService(){

   try{
      $db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
      $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
      $db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); 
      $db->exec('SET NAMES utf8');            
   }

   catch(Exception $e){
      die('Veuillez vérifier la connexion à la base de données');
   }

   $frais_product = 0;
   $envoie = 0;

   for($i = 0; $i < compterArticles(); $i++){

      for($j = 0; $j < $_SESSION['panier']['quantiteArticle'][$i]; $j++){

         $slug = addslashes($_SESSION['panier']['slugProduit'][$i]);
         $s = $db->query("SELECT frais FROM products WHERE slug='$slug'");
         $r = $s->fetch(PDO::FETCH_OBJ);
         $frais = $r->frais;
         $frais_product += $frais;

      }
   }
   $s2 = $db->query("SELECT * FROM frais WHERE name <= '$frais_product' ORDER BY prix DESC");
   $r2 = $s2->fetch(PDO::FETCH_OBJ);
   $envoie = $r2->prix; 
   return $envoie;

}

?>