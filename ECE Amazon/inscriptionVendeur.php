<?php

require_once('includes/header.php');

?>


<?php

if(!isset($_SESSION['vendeur_id'])){

	if(isset($_POST['submit'])){

		$vendeur = $_POST['vendeur'];
		$email = $_POST['email'];
        $adresse = $_POST['adresse'];
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
		$mdp = $_POST['mdp'];
		$remdp = $_POST['remdp'];

		if($vendeur && $email && $nom && $prenom && $adresse && $mdp && $remdp){
			
            if($mdp==$remdp){
                $db->query("INSERT INTO vendeur (vendeur, email, nom, prenom, adresse, mdp) VALUES('$vendeur','$email','$nom','$prenom','$adresse','$mdp')");
				echo '<br><h3 style="color:green;">Compte créer, <a href="connexionVendeur.php">connecter</a> vous.</h3>';
			}else{
				echo '<br><h2 style="color:red;">Mot de passe incorrect!</h2>';
			}
		}else{
			echo '<br><h2 style="color:red;">Champs imcomplets!</h2>';
		}
	} 
?>

	<br>
    <center><h1>Inscription - Vendeur</h1></center>
    <br>

	<center><form action="" method="POST">
		<h4>Pseudo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="vendeur"/></h4>
		<h4>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="email" name="email"/></h4>
        <h4>Prenom &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="prenom"/></h4>
        <h4>Nom &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="nom"/></h4>
        <h4>Adresse &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="adresse"/></h4>
		<h4>Mot-de-passe &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="password" name="mdp"/></h4>
		<h4>Mot-de-passe(vérif) :
            <input type="password" name="remdp"/></h4>
		
        <input style="color:white; background-color:black;"type="submit" name="submit"/>
    </form>

<a style="text-decoration:none; color:green;" href="connexionVendeur.php"><h4>Connexion</h4></a></center>
	<br>
<?php

}else{
	header('Location:compteVendeur.php');
}
?>

<?php
require_once('includes/footer.php');
?>