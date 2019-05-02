<?php 

require_once('includes/header.php');


if(!isset($_SESSION['user_id'])){

	if(isset($_POST['submit'])){

		$email = $_POST['email'];
		$mdp = $_POST['mdp'];

		if($email && $mdp){
			$select = $db->query("SELECT id FROM acheteur WHERE email='$email' AND mdp='$mdp' ");
			if($select->fetchColumn()){
				$select = $db->query("SELECT * FROM acheteur WHERE email='$email'");
				$result = $select->fetch(PDO::FETCH_OBJ);
				$_SESSION['user_id'] = $result->id;
				$_SESSION['user_name'] = $result->user;
				$_SESSION['user_email'] = $result->email;
				$_SESSION['user_password'] = $result->mdp;
				header('Location: compte.php');
			}else{
				echo '<br/><h3 style="color:red;">Mauvais identifiants.</h3>';
			}
		}else{
			echo '<br/><h3 style="color:red;">Veuillez remplir tous les champs.</h3>';
		}

	}

	?>
	<br>
	<center><h1>Connexion</h1>
    <br>

	<form action="" method="POST">
		<h4>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type="email" name="email"/></h4>
		<h4>Mot-de-passe : <input type="password" name="mdp"/></h4>
		<input type="submit" name="submit"/>
	</form>
        <br>
	<a href="inscription.php">Inscription</a>
	<br>
</center>
<br><br><br><br><br><br><br><br><br><br>
<?php

}else{
	header('Location:compte.php');
}

require_once('includes/footer.php');

?>