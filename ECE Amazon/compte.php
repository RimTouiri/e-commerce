<?php

require_once('includes/header.php');

?>
<h1>Mon compte</h1>
<h2>Mes informations</h2>
<?php
$user_id = $_SESSION['user_id'];
$select = $db->query("SELECT * FROM acheteur WHERE id = '$user_id'");

while($s = $select->fetch(PDO::FETCH_OBJ)){
	?>
	<h4>Pseudo : <?php echo $s->user; ?></h4>
    <h4>Nom : <?php echo $s->nom; ?></h4>
    <h4>Prénom : <?php echo $s->prenom; ?></h4>
    <h4>Adresse : <?php echo $s->adresse; ?></h4>
	<h4>Email : <?php echo $s->email; ?></h4>
	<h4>Password : <?php echo $s->mdp; ?></h4>
	<?php
}

?>
<h2>Mes transactions</h2>
<?php
$select = $db->query("SELECT * FROM transactions WHERE user_id = '$user_id'");

while($s = $select->fetch(PDO::FETCH_OBJ)){

	$id_trans = $s->id_trans;
	$select2 = $db->query("SELECT * FROM products_transactions WHERE id_trans='$id_trans'");

	?>

	<h4>Nom et prénom : <?php echo $s->name; ?></h4>
	<h4>Rue : <?php echo $s->rue; ?></h4>
	<h4>Ville : <?php echo $s->ville; ?></h4>
	<h4>Pays : <?php echo $s->pays; ?></h4>
	<h4>Date de transaction : <?php echo $s->date; ?></h4>
	<h4>ID de transaction : <?php echo $s->id_trans; ?></h4>
	<h4>Prix total : <?php echo $s->amount; ?></h4>
	<h4>Produits : </h4>
	<?php while($r = $select2->fetch(PDO::FETCH_OBJ)){?> 
	<h5>Nom : <?php echo $r->produit; ?></h5>
	<h5>Quantité : <?php echo $r->quantite_article; ?></h5>
	<?php } ?>
	<h4>Devise utilisée : <?php echo $s->code_devise; ?></h4>
	
	<?php
}
?>
<a href="deconnexion.php">Se déconnecter</a>
<?php

require_once('includes/footer.php');

?>