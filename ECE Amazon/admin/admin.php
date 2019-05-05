<?php

	session_start();
?>

<link href="../style/bootstrap.css" type="text/css" rel="stylesheet"/>

<center><h1>Bienvenue, <?php echo $_SESSION['user']; ?></h1></center>
<br>

<div>
    <center><h2>Gestion des Produits</h2>
    <a href="?action=ajouter">Ajouter un produit</a>
    <br>
    <a href="?action=modifieretsuppr">Modifier / Supprimer un produit</a></center>
</div>
<br>

<div>
    <center><h2>Gestion des Vendeurs</h2>
    <a href="?action=ajouter_vendeur">Ajouter un vendeur</a>
    <br>
    <a href="?action=supprimer_vendeur">Supprimer un vendeur</a>
    </center>
</div>
<br>
<center><h2>Gestion des Frais de services et de la TVA</h2>
<a href="?action=option">Options : Frais de service/TVA</a></center>
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
		$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); 
		$db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION); 
		$db->exec('SET NAMES utf8');
        
	}catch(Exception $e){
		die('Une erreur est survenue');
	}

	if(isset($_SESSION['user'])){

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

								imagejpeg($image_finale,'../imgs/'.$slug.'.jpg');

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

						header('Location: ../categories.php?categorie='.$categorie);

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

					header('Location: admin.php?action=modifieretsuppr');

				}

			}else if($_GET['action']=='suppr'){

				$id=$_GET['id'];
				$delete = $db->prepare("DELETE FROM products WHERE id=$id");
				$delete->execute();
				header('Location: admin.php?action=modifieretsuppr');

			}else if($_GET['action']=='ajouter_categorie'){

				if(isset($_POST['submit'])){

					$name = addslashes($_POST['name']);
					$slug = sluge($name);

					if($name){

						$insert = $db->prepare("INSERT INTO categorie (name,slug) VALUES('$name','$slug')");
						$insert->execute();


					}else{

						echo'<h2 style="color:red;">Champs incomplets!<h2>';

					}

				}

				?>

				<center><form action="" method="post">
				<h3>Nom de la categorie : </h3><input type="text" name="name"><br><br>
				<input type="submit" name="submit" value="Ajouter" >
                </form></center>

				<?php


			}else if($_GET['action']=='modifieretsuppr_categorie'){

				$select = $db->prepare("SELECT * FROM categorie");
				$select->execute();

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					echo $s->name;
					?>
					<a href="?action=modifier_categorie&amp;id=<?php echo $s->id; ?>">Modifier</a>
					<a href="?action=suppr_categorie&amp;id=<?php echo $s->id; ?>">X</a><br><br>
					<?php

				}

			}else if($_GET['action']=='modifier_categorie'){

				$id=$_GET['id'];

				$select = $db->prepare("SELECT * FROM categorie WHERE id=$id");
				$select->execute();

				$data = $select->fetch(PDO::FETCH_OBJ);

				?>

				<center><form action="" method="post">
				<h3>Nom de la categorie à modifier :</h3><input value="<?php echo $data->name; ?>" type="text" name="name"><br>
				<input type="submit" name="submit" value="Modifier">
                </form></center>

				<?php

				if(isset($_POST['submit'])){

					$name=$_POST['name'];

					$select = $db->query("SELECT name FROM categorie WHERE id='$id'");

					$result = $select->fetch(PDO::FETCH_OBJ);

					$update = $db->prepare("UPDATE categorie SET name='$name' WHERE id=$id");
					$update->execute();

					$id = $_GET['id'];
				
					$update = $db->query("UPDATE products SET categorie='$name' WHERE categorie='$result->name'");
					
					header('Location: admin.php?action=modifieretsuppr_categorie');
				}

			}else if($_GET['action']=='suppr_categorie'){

				$id=$_GET['id'];
				$delete = $db->prepare("DELETE FROM categorie WHERE id=$id");
				$delete->execute();

				header('Location: admin.php?action=modifieretsuppr_categorie');

			}else if($_GET['action']=='ajouter_vendeur'){

				if(isset($_POST['submit'])){

                    $vendeur =  addslashes($_POST['vendeur']);
                    $email = addslashes($_POST['email']);
                    $nom = addslashes($_POST['nom']);
                    $prenom = addslashes($_POST['prenom']);
                    $adresse = addslashes($_POST['adresse']);
                    $mdp = addslashes($_POST['mdp']);

					if($vendeur && $email && $nom && $prenom && $adresse && $mdp){

						$insert = $db->prepare("INSERT INTO vendeur(vendeur,email,nom,prenom,adresse,mdp) VALUES('$vendeur','$email','$nom','$prenom','$adresse','$mdp')");
						$insert->execute();


					}else{

						echo'<h2 style="color:red;">Champs incomplets!<h2>';

					}

				}

				?>

				<center><form action="" method="post">
				<h3>Pseudo Vendeur : </h3><input type="text" name="vendeur">
                <h3>Email Vendeur : </h3><input type="text" name="email">
                <h3>Nom Vendeur : </h3><input type="text" name="nom">
                <h3>Prenom Vendeur : </h3><input type="text" name="prenom">
                <h3>Adresse Vendeur : </h3><input type="text" name="adresse">
                <h3>Mot-de-Passe Vendeur : </h3><input type="text" name="mdp">
                <br><br>
				<input type="submit" name="submit" value="Ajouter" >
                </form></center>

				<?php


			}else if($_GET['action']=='supprimer_vendeur'){

				$select = $db->prepare("SELECT * FROM vendeur");
				$select->execute();

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					echo $s->vendeur;
					?>
					<a href="?action=suppr_vendeur&amp;id=<?php echo $s->id; ?>">X</a><br><br>
					<?php

				}

			}else if($_GET['action']=='suppr_vendeur'){

				$id=$_GET['id'];
				$delete = $db->prepare("DELETE FROM vendeur WHERE id=$id");
				$delete->execute();

				header('Location: admin.php?action=modifieretsuppr_vendeur');

			}else if($_GET['action']=='option'){

				?>

                <center><h3>Possibilités de Frais de service :</h3></center>

				<?php

				$select = $db->query("SELECT * FROM frais");

				while($s=$select->fetch(PDO::FETCH_OBJ)){

					?>

					<center><form action="" method="post">
					<input type="text" name="frais" value="<?php echo $s->name;?>"><a href="?action=modifier_frais&amp;name=<?php echo $s->name; ?>"> Modifier</a>
                    </form></center>

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
                <center>
				<h3>TVA : </h3>

				<form action="" method="post">
				<input type="text" name="tva" value="<?= $show_tva; ?>">
				<input type="submit" name="submit2" value="Modifier">
                </form></center>

				<?php


			}else if($_GET['action']=='modifier_frais'){

				$ancien_frais = $_GET['name'];
				$select = $db->query("SELECT * FROM frais WHERE name=$ancien_frais");
				$s = $select->fetch(PDO::FETCH_OBJ);

				if(isset($_POST['submit'])){

					$frais=$_POST['frais'];
					$prix=$_POST['prix'];

					if($prix && $frais){

						$update = $db->query("UPDATE frais SET name='$frais', prix='$prix' WHERE name=$ancien_frais");
						header("Refresh:0");
					}
				}

				?>
                <center><h3>Options : Frais de service</h3>

				<form action="" method="post">
				<h3>Id frais de Service :</h3><input type="text" name="frais" value="<?php echo $_GET['name']; ?>"><br>
				<h3>Prix du service :</h3><input type="text" name="prix" value="<?php echo $s->prix; ?>"> <h3>en Euros</h3>
				<input type="submit" name="submit" value="Modifier">
                </form></center>

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