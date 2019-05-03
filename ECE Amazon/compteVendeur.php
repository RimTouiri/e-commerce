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
$vendeur_id = $_SESSION['vendeur_id'];
$select = $db->query("SELECT * FROM vendeur WHERE id = '$vendeur_id'");

while($s = $select->fetch(PDO::FETCH_OBJ)){
	?>
<center>
	<h4>Pseudo : <?php echo $s->vendeur; ?></h4>
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
<br>

<div>
    <center><h2>Gestion de mes Produits</h2>
    <a href="?action=ajouter">Ajouter un produit</a>
    <br>
    <a href="?action=modifieretsuppr">Modifier / Supprimer un produit</a></center>
</div>
<br>

<?php

	function sluge($ecrit){
		$ecrit=preg_replace('~[^\pL\d]+~u', '-', $ecrit);
		$ecrit=iconv('utf-8', 'us-ascii//TRANSLIT', $ecrit);
		$ecrit=preg_replace('~[^-\w]+~', '', $ecrit);
		$ecrit=trim($ecrit, '-');
		$ecrit=preg_replace('~-+~', '-', $ecrit);
		$ecrit=strtolower($ecrit);

		if (empty($ecrit)) {
		  return 'n-a';
		}

  		return $ecrit;
	}

	try{

		$db = new PDO('mysql:host=127.0.0.1;dbname=ece_amazon', 'root','');
		$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); 
		$db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); 
		$db->exec('SET NAMES utf8');
        
	}catch(Exception $e){
		die('Une erreur est survenue');
	}

		if(isset($_GET['action'])){

			if($_GET['action']=='ajouter'){

				if(isset($_POST['submit'])){

					$nbitem= $_POST['nbitem'];
					$titre= addslashes($_POST['titre']);
					$slug = sluge($titre);
					$descr= addslashes($_POST['descr']);
					$prix=$_POST['prix'];

					$img = $_FILES['img']['name'];

					$img_tmp = $_FILES['img']['tmp_name'];

					if(!empty($img_tmp)){

						$image = explode('.',$img);

						$image_ext = end($image);

						if(in_array(strtolower($image_ext),array('png','jpg','jpeg'))===false){

							echo'<h2 style="color:red;">Veuillez rentrer une image du type : png, jpg ou jpeg</h2>';

						}else{

							$image_taille = getimagesize($img_tmp);

							if($image_taille['mime']=='image/jpeg'){

								$image_src = imagecreatefromjpeg($img_tmp);

							}else if($image_taille['mime']=='image/png'){

								$image_src = imagecreatefrompng($img_tmp);

							}else{

								$image_src = false;
								echo'<h2 style="color:red;>Veuillez rentrer une image valide</h2>';

							}

							if($image_src!==false){

								$image_largeur=200;

								if($image_taille[0]==$image_largeur){

									$image_finale = $image_src;

								}else{

									$nvelle_largeur[0]=$image_largeur;

									$nvelle_hauteur[1] = 200;

									$image_finale = imagecreatetruecolor($nvelle_largeur[0],$nvelle_hauteur[1]);

									imagecopyresampled($image_finale,$image_src,0,0,0,0,$nvelle_largeur[0],$nvelle_hauteur[1],$image_taille[0],$image_taille[1]);

								}

								imagejpeg($image_finale,'imgs/'.$slug.'.jpg');

							}

						}

					}else{

						echo'<h2 style="color:red;>Veuillez ajouter une image</h2>';

					}

					if($titre && $descr && $nbitem && $prix){

						$categorie=$_POST['categorie'];

						$frais=$_POST['frais'];

						$select = $db->query("SELECT prix FROM frais WHERE name='$frais'");

						$s = $select->fetch(PDO::FETCH_OBJ);

						$envoie = $s->prix;

						$ancien_prix = $prix;

						$prix_final = $ancien_prix + $envoie;

						$select=$db->query("SELECT tva FROM products");

						$s1=$select->fetch(PDO::FETCH_OBJ);

						if($s1){

							$tva = $s1->tva;

						}else{
							$tva = 20;
						}

						$prix_final_1 = $prix_final+$prix_final*$tva/100;

						$insert = $db->query("INSERT INTO products (titre,slug,descr,prix,categorie,frais,envoie,tva,prix_final,nbitem) VALUES('$titre','$slug','$descr','$prix','$categorie','$frais','$envoie','$tva','$prix_final_1','$nbitem')");

						header('Location: categories.php?categorie='.$categorie);

					}else{

						echo'<h2 style="color:red;">Champs incomplets!<h2>';

					}

				}

			?>

				<center><form action="" method="post" enctype="multipart/form-data">
				<h3>Titre du produit :</h3><input type="text" name="titre">
				<h3>Description du produit :</h3><textarea name="descr"></textarea>
				<h3>Prix :</h3><input type="text" name="prix"><br><br>
				<h3>Image :</h3>
				<input type="file" name="img"><br><br>
				<h3>Categorie :</h3><select name="categorie">

				<?php $select=$db->query("SELECT * FROM categorie");

					while($s = $select->fetch(PDO::FETCH_OBJ)){

						?>

						<option><?php echo $s->name; ?></option>

						<?php

					}

				 ?>

				</select>
                <br><br>
				<h3>Frais de service (0=0€, 1=5€, 2=10€, 3=15€, 4=20€) :</h3><select name="frais">-->
				<?php 

					$select=$db->query("SELECT * FROM frais");

					while($s = $select->fetch(PDO::FETCH_OBJ)){

						?>

						<option><?php echo $s->name; ?></option>

						<?php

					}

				 ?>
				</select><br><br>
				<h3>Stock : </h3><input type="text" name="nbitem"><br><br>
				<input type="submit" name="submit">
                </form></center>

			<?php

			}else if($_GET['action']=='modifieretsuppr'){

				$select = $db->prepare("SELECT * FROM products");
				$select->execute();

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					echo $s->titre;
					?>
					<a href="?action=modifier&amp;id=<?php echo $s->id; ?>">Modifier</a>
					<a href="?action=suppr&amp;id=<?php echo $s->id; ?>">X</a>
                    <br><br>
					<?php

				}

			}else if($_GET['action']=='modifier'){

				$id=$_GET['id'];

				$select = $db->prepare("SELECT * FROM products WHERE id=$id");
				$select->execute();

				$data = $select->fetch(PDO::FETCH_OBJ);

				?>

				<center><form action="" method="post">
				<h3>Titre du produit :</h3><input value="<?php echo $data->titre; ?>" type="text" name="titre">
				<h3>Description du produit :</h3><textarea name="descr"><?php echo $data->descr; ?></textarea>
				<h3>Prix</h3><input value="<?php echo $data->prix; ?>" type="text" name="prix">
				<h3>Stock : </h3><input type="text" value="<?php echo $data->nbitem; ?>"name="nbitem"><br><br>
				<input type="submit" name="submit" value="Modifier">
                </form></center>

				<?php

				if(isset($_POST['submit'])){

					$nbitem = $_POST['nbitem'];
					$titre=$_POST['titre'];
					$descr=$_POST['descr'];
					$prix=$_POST['prix'];

					$update = $db->prepare("UPDATE products SET titre='$titre',descr='$descr',prix='$prix',nbitem='$nbitem' WHERE id=$id");
					$update->execute();

					header('Location: compteVendeur.php?action=modifieretsuppr');

				}

			}else if($_GET['action']=='suppr'){

				$id=$_GET['id'];
				$delete = $db->prepare("DELETE FROM products WHERE id=$id");
				$delete->execute();
				header('Location: compteVendeur.php?action=modifieretsuppr');

			}else{

				die('Une erreur s\'est produite.');

			}

		}else{
            
		}

require_once('includes/footer.php');
?>