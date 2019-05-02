<?php 

require_once('includes/header.php');

if(!isset($_SESSION['user_id'])){

	if(isset($_POST['submit'])){

		$user = $_POST['user'];
		$email = $_POST['email'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
		$mdp = $_POST['mdp'];
		$remdp = $_POST['remdp'];

		if($user && $email && $nom && $prenom && $adresse && $mdp && $remdp){
			
            if($mdp==$remdp){
				
                $db->query("INSERT INTO acheteur (user, email, nom, prenom, adresse, mdp) VALUES('$user','$email','$nom','$prenom','$adresse','$mdp')");
				echo '<br><h3 style="color:green;">Compte créer, <a href="connexion.php">connecter</a> vous.</h3>';
                
			}else{
				echo '<br><h2 style="color:red;">Mot de passe incorrect!</h2>';
			}
		}else{
			echo '<br><h2 style="color:red;">Champs imcomplets!</h2>';
		}
	}
    
?>

	<br>
    <center><h1>Inscription</h1></center>
    <br>

	<center><form action="" method="POST">
		<h4>Pseudo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="user"/></h4>
		<h4>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="email" name="email"/></h4>
        <h4>Nom &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="nom"/></h4>
        <h4>Prenom &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="prenom"/></h4>
        <h4>Adresse &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="text" name="adresse"/></h4>
		<h4>Mot-de-passe &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
            <input type="password" name="mdp"/></h4>
		<h4>Mot-de-passe(vérif) :
            <input type="password" name="remdp"/></h4>
		
        <input style="color:white; background-color:black;"type="submit" name="submit"/>
    </form></center>

	<a href="connexion.php">Connexion</a>
	<br/>
<?php

}else{
	header('Location:compte.php');
}
?>

<?php
require_once('includes/footer.php');

?>