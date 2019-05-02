<?php 
require_once('includes/header.php');

if(!isset($_SESSION['user_id'])){
	if(isset($_POST['submit'])){

        $mdp = $_POST['mdp'];
		$email = $_POST['email'];

		if($mdp && $email){
			$select = $db->query("SELECT id FROM acheteur WHERE email='$email' AND mdp='$mdp' ");
            
			if($select->fetchColumn()){
                
				$select = $db->query("SELECT * FROM acheteur WHERE email='$email'");
				$result = $select->fetch(PDO::FETCH_OBJ);
				$_SESSION['user_id'] = $result->id;
                $_SESSION['user_email'] = $result->email;
                $_SESSION['user_password'] = $result->mdp;
				$_SESSION['user_name'] = $result->user;
				
				header('Location: compte.php');
			}else{
				echo '<br><center><h2 style="color:red;">Mauvais identifiants !</h2></center>';
			}
		}else{
			echo '<br><center><h2 style="color:red;">Champs incomplets !</h2></center>';
		}

	}
?>
	<br>
	<center><h1>Connexion</h1>
    <br>

	<form action="" method="POST">
		<h4>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type="email" name="email"/></h4>
		<h4>Mot-de-passe : <input type="password" name="mdp"/></h4>
		<input style="color:white; background-color:black;" type="submit" name="submit"/>
	</form>
        <br>
        <a style="text-decoration:none; color:green;" href="inscription.php"><h4>Inscription</h4></a>
	<br>
</center>
<br><br><br><br><br><br><br><br>
<?php

}else{
	header('Location:compte.php');
}

require_once('includes/footer.php');

?>