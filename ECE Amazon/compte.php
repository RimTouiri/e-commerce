<?php

require_once('includes/header.php');

?>
<br>
<center><h1>Mon compte</h1>
<br>
<h2>Informations</h2>
<br>
</center>
<?php
$user_id = $_SESSION['user_id'];
$select = $db->query("SELECT * FROM acheteur WHERE id = '$user_id'");

while($s = $select->fetch(PDO::FETCH_OBJ)){
	?>
<center>
	<h4>Pseudo : <?php echo $s->user; ?></h4>
    <h4>Nom : <?php echo $s->nom; ?></h4>
    <h4>Prénom : <?php echo $s->prenom; ?></h4>
    <h4>Adresse : <?php echo $s->adresse; ?></h4>
	<h4>Email : <?php echo $s->email; ?></h4>
	<h4>Password : <?php echo $s->mdp; ?></h4>
</center>
	<?php
}

?>
<br>
<center><a style="text-decoration: none; color:red;" href="deconnexion.php"><h4>Déconnexion</h4></a></center>
<br><br>

<?php
require_once('includes/footer.php');
?>