<?php
	require_once('includes/header.php');

	if(isset($_GET['show'])){

		$product = htmlentities($_GET['show']);

		$select=$db->prepare("SELECT * FROM products WHERE slug='$product'");
		$select->execute();	

		$s = $select->fetch(PDO::FETCH_OBJ);

		$descr = $s->descr;

		$descr_finale=wordwrap($descr,100,'<br>', false);

		?>

		<br>
        <div style="text-align:center;">
            <img src="imgs/<?php echo $s->slug; ?>.jpg">
            <h1><?php echo $s->titre; ?></h1>
            <h4><?php echo $descr_finale; ?></h4>
            <h4>En Stock : <?php echo $s->nbitem; ?></h4>
            <?php 
                if ($s->nbitem>0){ 
            ?>
                    <a href="panier.php?action=ajouter&amp;l=<?php echo $s->slug; ?>&amp;q=1&amp;p=<?php echo $s->prix; ?>">Ajouter dans mon Panier</a>
            <?php 
                }else{
                    echo'<h4 style="color:red;">Le stock épuisé !</h4>';
                } 
            ?>
		</div>
        <br>

    <?php 

	}else{

	if(isset($_GET['categorie'])){

		$categorie_slug=$_GET['categorie'];
        
		$select = $db->query("SELECT name FROM categorie WHERE slug='$categorie_slug'");
		$resultat = $select->fetch(PDO::FETCH_OBJ);
        
		$categorie = addslashes($resultat->name);
        
		$select = $db->prepare("SELECT * FROM products WHERE categorie='$categorie'");
		$select->execute();

		while($s=$select->fetch(PDO::FETCH_OBJ)){

			$lenght=75;
			$descr = $s->descr;
			$new_descr=substr($descr,0,$lenght)."...";
			$descr_finale=wordwrap($new_descr,50,'<br>', false);

    ?>
            <div style="text-align:center;">
			<br>
			<a href="?show=<?php echo $s->slug; ?>"><img src="imgs/<?php echo $s->slug; ?>.jpg"></a>
			<a href="?show=<?php echo $s->slug; ?>"><h2><?php echo $s->titre;?></h2></a>

			<h5><?php echo $descr_finale; ?></h5>
			<h4><?php echo $s->prix; ?> €</h4>
			<h5>En Stock : <?php echo $s->nbitem; ?></h5>
    <?php 
            if ($s->nbitem>0){ ?><a href="panier.php?action=ajouter&amp;l=<?php echo $s->slug; ?>&amp;q=1&amp;p=<?php echo $s->prix; ?>">Ajouter dans mon Panier</a><?php 
            }else{
                echo'<h4 style="color:red;">Le stock épuisé !</h4>';
            }
    ?>
            
        </div>
			

    <?php

		}
    ?>

	<br><br><br><br>

	<?php

	}else{
    ?>

		<br>
        <center><h1>Catégories :</h1></center>
        <br>
		
        <?php
	       $select = $db->query("SELECT * FROM categorie");
	       while($s = $select->fetch(PDO::FETCH_OBJ)){
		?>
        <div class="categorie">
            <center><a class="categorie" href="?categorie=<?php echo $s->slug;?>"><li><h2><?php echo $s->name ?></h2></li></a></center>
        </div>
		<?php
	   }

    }

}
	require_once('includes/footer.php');
?>