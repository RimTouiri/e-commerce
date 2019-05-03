<?php

function creePanier(){
   try{

      $db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
      $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
      $db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); 
      $db->exec('SET NAMES utf8');           
   }catch(Exception $e){

      die('Vérifier votre connexion à la base de données');

   }

   if (!isset($_SESSION['panier'])){
      $_SESSION['panier']=array();
      $_SESSION['panier']['nomArticle'] = array();
      $_SESSION['panier']['slugArticle'] = array();
      $_SESSION['panier']['quantiteArticle'] = array();
      $_SESSION['panier']['prixArticle'] = array();
      $_SESSION['panier']['verrou'] = false;
      $select = $db->query("SELECT tva FROM products");
      $data = $select->fetch(PDO::FETCH_OBJ);
      $_SESSION['panier']['tva'] = $data->tva;
   }
   return true;
}

function supprPanier(){
   unset($_SESSION['panier']);
}

function ajouterArticle($slugArticle,$quantiteArticle,$prixArticle){

   try{

      $db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
      $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); 
      $db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); 
      $db->exec('SET NAMES utf8'); 
       
   }catch(Exception $e){

      die('Vérifier votre connexion à la base de données');

   }

   if (creePanier() && !Verrouillage()){  //Si panier éxiste
      $s1 = $db->query("SELECT titre FROM products WHERE slug = '$slugArticle'");
      $r1 = $s1->fetch(PDO::FETCH_OBJ);
      $nomArticle = $r1->titre;

      $positionArticle = array_search($slugArticle,  $_SESSION['panier']['slugArticle']);  //Recherche de l'article dans le panier

       if ($positionArticle !== false){
          $_SESSION['panier']['quantiteArticle'][$positionArticle]+=$quantiteArticle;
      }else{  
         array_push( $_SESSION['panier']['nomArticle'],$nomArticle);
         array_push( $_SESSION['panier']['slugArticle'],$slugArticle);
         array_push( $_SESSION['panier']['quantiteArticle'],$quantiteArticle);
         array_push( $_SESSION['panier']['prixArticle'],$prixArticle);
      }
   }else{
       echo "<h2>Erreur! Contacter l'administrateur du site.</h2>";
   }
}

function modifNbreArticle($slugArticle,$quantiteArticle){
   
   if (creePanier() && !Verrouillage()){//Si panier éxiste
       
      if ($quantiteArticle > 0){ //Si quantité positive: modifie sinon: supprime l'article
         
         $positionArticle = array_search($slugArticle,  $_SESSION['panier']['slugArticle']); //Recherche de l'article dans le panier

         if ($positionArticle !== false){
             
            $_SESSION['panier']['quantiteArticle'][$positionArticle] = $quantiteArticle ;
         }
      }else{
            supprimerArticle($slugArticle);
      }
   }else{
        echo "<h2>Erreur! Contacter l'administrateur du site.</h2>";
   }
}

function supprimerArticle($slugArticle){ 
   var_dump($slugArticle);

   if (creePanier() && !Verrouillage()){  //si panier existe
      for($i = 0; $i < count($_SESSION['panier']['slugArticle']); $i++){
         if ($_SESSION['panier']['slugArticle'][$i] == $slugArticle){
           
            unset( $_SESSION['panier']['nomArticle'][$i]);
            unset( $_SESSION['panier']['slugArticle'][$i]);
            unset( $_SESSION['panier']['quantiteArticle'][$i]);
            unset( $_SESSION['panier']['prixArticle'][$i]);
         }

      }

   }else{
   echo "<h2>Erreur! Contacter l'administrateur du site.</h2>";
   }
}

function PrixGlobal(){
   $tot=0;
   for($i = 0; $i<count($_SESSION['panier']['slugArticle']); $i++)
   {
      $tot += $_SESSION['panier']['quantiteArticle'][$i] * $_SESSION['panier']['prixArticle'][$i];
   }
   return $tot;
}

function Verrouillage(){
   if (isset($_SESSION['panier']) && $_SESSION['panier']['verrou']){
       return true;
   }else{
       return false;
   }  
}

function PrixGlobalTva(){

   $tot=0;
   for($i = 0; $i < count($_SESSION['panier']['slugArticle']); $i++)
   {
      $tot += $_SESSION['panier']['quantiteArticle'][$i] * $_SESSION['panier']['prixArticle'][$i];
   }
   return $tot + $tot*$_SESSION['panier']['tva']/100;
}

function compterArticles(){
   if(isset($_SESSION['panier'])){
       return count($_SESSION['panier']['slugArticle']);
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

    $envoie = 0;
    $frais_article = 0;

   for($i = 0; $i < compterArticles(); $i++){
      for($j = 0; $j < $_SESSION['panier']['quantiteArticle'][$i]; $j++){

         $slug = addslashes($_SESSION['panier']['slugArticle'][$i]);
         $s1 = $db->query("SELECT frais FROM products WHERE slug='$slug'");
         $r1 = $s1->fetch(PDO::FETCH_OBJ);
         $frais = $r1->frais;
         $frais_article += $frais;

      }
   }
   $s2 = $db->query("SELECT * FROM frais WHERE name <= '$frais_article' ORDER BY prix DESC");
   $r2 = $s2->fetch(PDO::FETCH_OBJ);
   $envoie = $r2->prix; 
   return $envoie;

}

?>