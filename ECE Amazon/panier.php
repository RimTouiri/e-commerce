<!--sources paypal : https://developer.paypal.com/demo/checkout/#/pattern/client
https://developer.paypal.com/docs/api/payments/v1/#payment
https://jcrozier.developpez.com/articles/web/panier/-->


<?php
require_once('includes/header.php');
?>

<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

<?php

    require_once('includes/functions_panier.php');

    $prixtot = 0;
    $erreur = false;
    $action = (isset($_POST['action'])?$_POST['action']:(isset($_GET['action'])?$_GET['action']:null));

    if($action!==null){

        if(!in_array($action, array('ajouter','supprimer','refresh')))

            $erreur = true;

            $m = (isset($_POST['l'])?$_POST['l']:(isset($_GET['l'])?$_GET['l']:null));
            $b = (isset($_POST['q'])?$_POST['q']:(isset($_GET['q'])?$_GET['q']:null));
            $f = (isset($_POST['p'])?$_POST['p']:(isset($_GET['p'])?$_GET['p']:null));

            $m = preg_replace('#\v#', '', $m);
            $f = floatval($f);

            if(is_array($b)){

                $NbreArticle= array();
                $i = 0;

                foreach($b as $contenu){
                    $NbreArticle[$i++] = intval($contenu);
                }
            }else{
                $b = intval($b);
            }
    }

if(!$erreur){

	switch($action){

		Case "ajouter":
		ajouterArticle($m,$b,$f);
		break;

		Case "supprimer":
		supprimerArticle($m);
		break;

		Case "refresh":
		for($i = 0;$i<count($NbreArticle);$i++){
			modifNbreArticle($_SESSION['panier']['slugArticle'][$i], round($NbreArticle[$i]));
		}
		break;

		Default:
		break;
	}
}

?>

<center>
    <br>
    <form method="post" action="">
	<table width="1000">
		<tr>
            <td colspan="4"><center><h2>Votre panier</h2></center></td>
		</tr>
        
		<tr>
            <td><h5>Nom Article :</h5></td>
            <td><h5>Prix à l'unité :</h5></td>
            <td><h5>Quantité :</h5></td>
            <td><h5>TVA :</h5></td>
            <td><h5>Supprimer Article :</h5></td>
		</tr>
        
        <?php
            //Suppression du Panier
			if(isset($_GET['deletepanier']) && $_GET['deletepanier'] == true){
				supprPanier();
			}
        
            //Création du Panier
			if(creePanier()){
			$nbProduits = count($_SESSION['panier']['nomArticle']);

			if($nbProduits <= 0){

				echo'<br><p style="font-size:30px; color:Red;">Panier vide!</p>';

			}else{
				$tot = PrixGlobal();
				$envoie = CalculFraisService();
                $tottva = PrixGlobalTVA();
				$prixtot = $tottva + $envoie;

				for($i = 0; $i<$nbProduits; $i++){
        
            ?>

					<tr>

						<td>
                            <br><?php echo $_SESSION['panier']['nomArticle'][$i]; ?>
                        </td>
						<td>
                            <br><?php echo $_SESSION['panier']['prixArticle'][$i];?>
                        </td>
						<td>
                            <br><input name="q[]" value="<?php echo $_SESSION['panier']['quantiteArticle'][$i]; ?>" size="5"/>
                        </td>
						<td>
                            <br><?php echo $_SESSION['panier']['tva']." %"; ?>
                        </td>
						<td>
                            <br><a href="panier.php?action=supprimer&amp;l=<?php echo $_SESSION['panier']['slugArticle'][$i]; ?>">X</a>
                        </td>

					</tr>
        
            <?php 
                } 
            ?>
					<tr>

						<td colspan="2">
                            <br><br>
							<p>Total : <?php echo $tot." €"; ?></p><br>
							<p>Total avec TVA : <?php echo $tottva." €"; ?></p>
							<p>Frais de service : <?php echo $envoie." €"; ?></p>
							<?php 
                                if(isset($_SESSION['user_id'])){ 
                            ?>
                                    <div id="paypal-button"></div>
                            <?php }else{?>
                                    <h4 style="color:red;">Vous devez vous connecter pour passer une Commande :
                                    <a style="color:green;" href="connexion.php">Connexion</a></h4>
                                    <br>
                            <?php } ?>
						</td>
					</tr>
        
					<tr>
						<td colspan="4">
                            <center>
                                <input style="color:white; background-color:black;" type="submit" value="refresh"/>
                                <input type="hidden" name="action" value="refresh"/>
                            </center>
                            <center>
                                <a style="color:red; text-decoration:none;" href="?deletepanier=true"><h4>Suppression du panier</h4></a>
                            </center>
						</td>
					</tr>

            <?php


			}

		}

        ?>
	</table>
    </form>
</center>

<script>
	paypal.Button.render({

	    env: 'sandbox',//sandbox|production, test: sandbox, pour de vrai: production

	    client: {
	        sandbox:    'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R',
	        production: '<insert production client id>' //replacer par le notre si pour de vrai
	    },

	    style: {
            layout: 'vertical',  // horizontal | vertical
            size:   'large',     // medium | large | responsive
            shape:  'pill',      // pill | rect
            color:  'blue'       // gold | blue | silver | black
        },

	    // Show the buyer a 'Pay Now' button in the checkout flow
	    commit: true,

	    // payment() is called when the button is clicked
	    payment: function(data, actions) {

	        // Make a call to the REST api to create the payment
	        return actions.payment.create({
	            payment: {
	                transactions: [
	                    {
	                        amount: { total: <?= $prixtot ?>, currency: 'EUR' }
	                    }
	                ]
	            },
	        });
	    },

	    onAuthorize: function(data, actions) { // onAuthorize() is called when the buyer approves the payment

	        return actions.payment.get().then(function(data) {

                // Récupération des informations de transactions : pas necessaire
                console.log(data);

                var shipping = data.payer.payer_info.shipping_address;

                var name = shipping.recipient_name;
                var rue = shipping.line1;
                var pays_code = shipping.pays_code;
                var ville = shipping.ville;
                var date = '<?= date("Y/m/d") ?>';
                var id_trans = data.id;
                var prix = data.transactions[0].amount.total;
                var code_devise = 'EUR';

                $.post(
         			"process.php",
         			{
         				name : name,
         				rue: rue,
         				ville: ville,
         				pays_code : pays_code,
         				date: date,
         				id_trans: id_trans,
         				prix: prix,
         				code_devise: code_devise,
         			}
				);

                //Redirection vers le site après le paiement
                return actions.payment.execute().then(function() {
                	$(location).attr("href", '<?= "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI'])."/success.php"; ?>');
            	});
            });
	    },

	}, '#paypal-button');
</script>
<?php

require_once('includes/footer.php');

?>