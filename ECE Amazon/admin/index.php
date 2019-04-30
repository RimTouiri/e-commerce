<?php

	session_start();

	$user_admin='Admin';         //nom utilisateur admin
	$mdp_admin='1234';  //mdp utilisateur admin

	if($_SESSION['user']){
		header('Location: admin.php');
	}

	if(isset($_POST['submit'])){ //détection du click sur bouton 

		$user = $_POST['user']; //permet d'écrire $user a la place de $_POST['user']
		$mdp = $_POST['mdp'];

		if($user && $mdp){
			if($user==$user_admin && $mdp==$mdp_admin){ // connection admin
				$_SESSION['user']=$user;	
				header('Location: admin.php');
			}else{
				echo'Mauvais Identifiants';
			}
		}else{

			echo'Champs incomplets !';
		}
	}


?>
<link href="../style/bootstrap.css" type="text/css" rel="stylesheet"/>
<link href="../style/styles.css" type="text/css" rel="stylesheet"/>
<style>
    background:

</style>
<center><h1>Administration - Connexion</h1></center>
<br>
<br>
<center><form action="" method="POST">
    <h3>Utilisateur :</h3>
    <input type="text" name="user"/>
    <br><br>
    <h3>Mot-de-passe :</h3>
    <input type="password" name="mdp"/>
    <br><br>
    <input style="color:white; background-color:black;" type="submit" name="submit"/>
</form></center>

<?php
require_once('../includes/footer.php');

?>