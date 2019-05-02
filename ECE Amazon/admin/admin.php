<?php

	session_start();
?>

<link href="../style/bootstrap.css" type="text/css" rel="stylesheet"/>

<center><h1>Bienvenue, <?php echo $_SESSION['user']; ?></h1></center>
<br>

<div>
    <center><h2>Gestion des Produits</h2>
    <a href="?action=add">Ajouter un produit</a>
    <br>
    <a href="?action=modifyanddelete">Modifier / Supprimer un produit</a></center>
</div>
<br>
<div>
    <center><h2>Gestion des Catégories</h2>
    <a href="?action=add_categorie">Ajouter une categorie</a>
    <br>
    <a href="?action=modifyanddelete_categorie">Modifier / Supprimer une categorie</a>
    <br><br></center>
</div>
<div>
    <center><h2>Gestion des Vendeurs</h2>
    <a href="?action=add_vendeur">Ajouter un vendeur</a>
    <br>
    <a href="?action=modifyanddelete_vendeur">Modifier / Supprimer un vendeur</a>
    </center>
</div>
<br>
<center><h2>Gestion des Frais de services et de la TVA</h2>
<a href="?action=options">Options : Frais de service/TVA</a></center>
    <br><br>

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
		$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // les noms de champs seront en caractères minuscules
		$db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); // les erreurs lanceront des exceptions
		$db->exec('SET NAMES utf8');				
	}

	catch(Exception $e){

		die('Une erreur est survenue');

	}

	if(isset($_SESSION['user'])){

		if(isset($_GET['action'])){

			if($_GET['action']=='add'){

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

							echo'Veuillez rentrer une image ayant pour extension : png, jpg ou jpeg';

						}else{

							$image_size = getimagesize($img_tmp);

							if($image_size['mime']=='image/jpeg'){

								$image_src = imagecreatefromjpeg($img_tmp);

							}else if($image_size['mime']=='image/png'){

								$image_src = imagecreatefrompng($img_tmp);

							}else{

								$image_src = false;
								echo'Veuillez rentrer une image valide';

							}

							if($image_src!==false){

								$image_width=200;

								if($image_size[0]==$image_width){

									$image_finale = $image_src;

								}else{

									$new_width[0]=$image_width;

									$new_height[1] = 200;

									$image_finale = imagecreatetruecolor($new_width[0],$new_height[1]);

									imagecopyresampled($image_finale,$image_src,0,0,0,0,$new_width[0],$new_height[1],$image_size[0],$image_size[1]);

								}

								imagejpeg($image_finale,'../imgs/'.$slug.'.jpg');

							}

						}

					}else{

						echo'Veuillez rentrer une image';

					}

					if($titre&&$descr&&$prix&&$nbitem){

						$categorie=$_POST['categorie'];

						$frais=$_POST['frais'];

						$select = $db->query("SELECT prix FROM frais WHERE name='$frais'");

						$s = $select->fetch(PDO::FETCH_OBJ);

						$envoie = $s->prix;

						$old_prix = $prix;

						$prix_final = $old_prix + $envoie;

						$select=$db->query("SELECT tva FROM products");

						$s1=$select->fetch(PDO::FETCH_OBJ);

						if($s1){

							$tva = $s1->tva;

						}else{
							$tva = 20;
						}

						$prix_final_1 = $prix_final+$prix_final*$tva/100;

						$insert = $db->query("INSERT INTO products (titre,slug,descr,prix,categorie,frais,envoie,tva,prix_final,nbitem) VALUES('$titre','$slug','$descr','$prix','$categorie','$frais','$envoie','$tva','$prix_final_1','$nbitem')");

						header('Location: ../categories.php?categorie='.$categorie);

					}else{

						echo'Veuillez remplir tous les champs';

					}

				}

			?>

				<form action="" method="post" enctype="multipart/form-data">
				<h3>Titre du produit :</h3><input type="text" name="titre"/>
				<h3>Description du produit :</h3><textarea name="descr"></textarea>
				<h3>Prix :</h3><input type="text" name="prix"/><br><br>
				<h3>Image :</h3>
				<input type="file" name="img"/><br><br>
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
				<h3>Stock : </h3><input type="text" name="nbitem"/><br><br>
				<input type="submit" name="submit"/>
				</form>

			<?php

			}else if($_GET['action']=='modifyanddelete'){

				$select = $db->prepare("SELECT * FROM products");
				$select->execute();

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					echo $s->titre;
					?>
					<a href="?action=modify&amp;id=<?php echo $s->id; ?>">Modifier</a>
					<a href="?action=delete&amp;id=<?php echo $s->id; ?>">X</a><br><br>
					<?php

				}

			}else if($_GET['action']=='modify'){

				$id=$_GET['id'];

				$select = $db->prepare("SELECT * FROM products WHERE id=$id");
				$select->execute();

				$data = $select->fetch(PDO::FETCH_OBJ);

				?>

				<form action="" method="post">
				<h3>Titre du produit :</h3><input value="<?php echo $data->titre; ?>" type="text" name="titre"/>
				<h3>Description du produit :</h3><textarea name="descr"><?php echo $data->descr; ?></textarea>
				<h3>Prix</h3><input value="<?php echo $data->prix; ?>" type="text" name="prix"/>
				<h3>Stock : </h3><input type="text" value="<?php echo $data->nbitem; ?>"name="nbitem"/><br><br>
				<input type="submit" name="submit" value="Modifier"/>
				</form>

				<?php

				if(isset($_POST['submit'])){

					$nbitem = $_POST['nbitem'];
					$titre=$_POST['titre'];
					$descr=$_POST['descr'];
					$prix=$_POST['prix'];

					$update = $db->prepare("UPDATE products SET titre='$titre',descr='$descr',prix='$prix',nbitem='$nbitem' WHERE id=$id");
					$update->execute();

					header('Location: admin.php?action=modifyanddelete');

				}

			}else if($_GET['action']=='delete'){

				$id=$_GET['id'];
				$delete = $db->prepare("DELETE FROM products WHERE id=$id");
				$delete->execute();
				header('Location: admin.php?action=modifyanddelete');

			}else if($_GET['action']=='add_categorie'){

				if(isset($_POST['submit'])){

					$name = addslashes($_POST['name']);
					$slug = sluge($name);

					if($name){

						$insert = $db->prepare("INSERT INTO categorie (name,slug) VALUES('$name','$slug')");
						$insert->execute();


					}else{

						echo'Veuillez remplir tous les champs';

					}

				}

				?>

				<form action="" method="post">
				<h3>Titre de la categorie : </h3><input type="text" name="name"/><br><br>
				<input type="submit" name="submit" value="Ajouter" />
				</form>

				<?php


			}else if($_GET['action']=='modifyanddelete_categorie'){

				$select = $db->prepare("SELECT * FROM categorie");
				$select->execute();

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					echo $s->name;
					?>
					<a href="?action=modify_categorie&amp;id=<?php echo $s->id; ?>">Modifier</a>
					<a href="?action=delete_categorie&amp;id=<?php echo $s->id; ?>">X</a><br><br>
					<?php

				}

			}else if($_GET['action']=='modify_categorie'){

				$id=$_GET['id'];

				$select = $db->prepare("SELECT * FROM categorie WHERE id=$id");
				$select->execute();

				$data = $select->fetch(PDO::FETCH_OBJ);

				?>

				<form action="" method="post">
				<h3>Titre de la categorie :</h3><input value="<?php echo $data->name; ?>" type="text" name="name"/><br>
				<input type="submit" name="submit" value="Modifier"/>
				</form>

				<?php

				if(isset($_POST['submit'])){

					$name=$_POST['name'];

					$select = $db->query("SELECT name FROM categorie WHERE id='$id'");

					$result = $select->fetch(PDO::FETCH_OBJ);

					$update = $db->prepare("UPDATE categorie SET name='$name' WHERE id=$id");
					$update->execute();

					$id = $_GET['id'];
				
					$update = $db->query("UPDATE products SET categorie='$name' WHERE categorie='$result->name'");
					
					header('Location: admin.php?action=modifyanddelete_categorie');
				}

			}else if($_GET['action']=='delete_categorie'){

				$id=$_GET['id'];
				$delete = $db->prepare("DELETE FROM categorie WHERE id=$id");
				$delete->execute();

				header('Location: admin.php?action=modifyanddelete_categorie');

			}else if($_GET['action']=='options'){

				?>

				<h3>Possibilités de Frais de service :</h3>

				<?php

				$select = $db->query("SELECT * FROM frais");

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					?>

					<form action="" method="post">
					<input type="text" name="frais" value="<?php echo $s->name;?>"/><a href="?action=modify_frais&amp;name=<?php echo $s->name; ?>">  Modifier</a>
					</form>

					<?php

				}

				$select=$db->query("SELECT tva FROM products");

				$s = $select->fetch(PDO::FETCH_OBJ);

				if(!$s){
					$show_tva = 20;
				}else{
					$show_tva = $s->tva;
				}

				if(isset($_POST['submit2'])){

					$tva=$_POST['tva'];

					if($tva){

						$update = $db->query("UPDATE products SET tva=$tva");
						header("Refresh:0");

					}

				}

				?>
				<h3>tva : </h3>

				<form action="" method="post"/>
				<input type="text" name="tva" value="<?= $show_tva; ?>"/>
				<input type="submit" name="submit2" value="Modifier"/>
				</form>

				<?php


			}else if($_GET['action']=='modify_frais'){

				$old_frais = $_GET['name'];
				$select = $db->query("SELECT * FROM frais WHERE name=$old_frais");
				$s = $select->fetch(PDO::FETCH_OBJ);

				if(isset($_POST['submit'])){

					$frais=$_POST['frais'];
					$prix=$_POST['prix'];

					if($frais&&$prix){

						$update = $db->query("UPDATE frais SET name='$frais', prix='$prix' WHERE name=$old_frais");
						header("Refresh:0");

					}

				}

				?>

				<h3>Options</h3>

				<form action="" method="post">
				<h3>Poids (plus de) : </h3><input type="text" name="frais" value="<?php echo $_GET['name']; ?>"/><br>
				<h3>Correspond à </h3><input type="text" name="prix" value="<?php echo $s->prix; ?>"/> <h3>Euros</h3>
				<input type="submit" name="submit" value="Modifier"/>
				</form>

				<?php


			}else{

				die('Une erreur s\'est produite.');

			}

		}else{



		}

	}else{

		header('Location: ../index.php');

	}
?>