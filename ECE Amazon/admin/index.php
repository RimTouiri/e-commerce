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

		if($mdp && $user){
			if($mdp==$mdp_admin && $user==$user_admin){ // connection admin
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

<center><h1>Connexion Administrateur</h1></center>
<br>
<br>
<div>
<center><form action="" method="POST">
    <h3>Utilisateur :</h3>
    <input type="text" name="user"/>
    <br><br>
    <h3>Mot-de-passe :</h3>
    <input type="password" name="mdp"/>
    <br><br>
    <input style="color:white; background-color:black;" type="submit" name="submit"/>
</form></center>
</div>
<br><br><br><br><br><br><br><br><br>

<?php
require_once('../includes/footer.php');
?>