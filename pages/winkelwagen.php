<?php
$pageTitle = 'Winkelwagen';

if(isset($_POST['product-variation-id']) && $_POST['action'] == 'add'){
	$stmt = mysqli_prepare($db, "SELECT id FROM product_variations WHERE id = ?");
	mysqli_stmt_bind_param($stmt, 'i', $_POST['product-variation-id']);
	mysqli_stmt_execute($stmt);
	$stmt->bind_result($id);
	$stmt->fetch(); 
	$stmt->close(); 
	
	if(strlen($id) > 0){
		$_SESSION['cart'] = array();
		$_SESSION['cart'][0]['product-variation-id'] = $id;
		$_SESSION['cart'][0]['amount'] = 1;
	}
}

if(isset($_POST['action']) && $_POST['action'] == 'remove_card'){
	unset($_SESSION['cart'][0]['card']);
	die('removed');
}

if(isset($_POST['action']) && $_POST['action'] == 'remove_extra'){
	unset($_SESSION['cart'][0]['extra']);
	die('removed');
}

if(isset($_POST['action']) && $_POST['action'] == 'update_card'){
	if((bool)$_POST['add'] == false){
		unset($_SESSION['cart'][0]['card']);
	} else {
		$_SESSION['cart'][0]['card'] = array(
			'id' => $_POST['id'],
			'text' => $_POST['text']
		);
	}
	die('updated');
}

if(isset($_POST['action']) && $_POST['action'] == 'update_extra'){
	$_SESSION['cart'][0]['extra']['id'] = $_POST['id'];
	die('updated');
}


if(isset($_SESSION['cart']) && count($_SESSION['cart'][0]) > 0){
	$total = 0;
	$result = mysqli_query($db, "SELECT products.image, products.id as product_id, product_variations.name, product_variations.price, products.terms, product_variations.id FROM products, product_variations WHERE products.id = product_variations.product_id AND product_variations.id = {$_SESSION['cart'][0]['product-variation-id']}");
	$product = mysqli_fetch_assoc($result);
	
	$stmt = mysqli_prepare($db, "SELECT name FROM product_overrides WHERE id = ?");
	mysqli_stmt_bind_param($stmt, 'i', $product['product_id']);
	mysqli_stmt_execute($stmt);
	$stmt->bind_result($override['name']);
	$stmt->fetch(); 
	$stmt->close(); 

	if(isset($override['name'])){
		$product['name'] = $override['name'];
		unset($override);
	}
	
	$total += $product['price'];
	
	if(isset($_SESSION['cart'][0]['card'])){
		$result = mysqli_query($db, "SELECT name, price FROM cards WHERE id = {$_SESSION['cart'][0]['card']['id']}");
		$card = mysqli_fetch_assoc($result);
		$total += $card['price'];
	}
	
	if(isset($_SESSION['cart'][0]['extra']['id'])){
		$result = mysqli_query($db, "SELECT id, name, price FROM additional_products WHERE id = {$_SESSION['cart'][0]['extra']['id']}");
		$extra = mysqli_fetch_assoc($result);
		$total += $extra['price'];
	}
	
	$result = mysqli_query($db, "SELECT cards.id, cards.name, cards.price FROM cards, product_cards_mappings WHERE cards.id = product_cards_mappings.card_id AND product_cards_mappings.product_id = '{$product['product_id']}'");	
	$possibleCards = array();
	while($data = mysqli_fetch_assoc($result)){
		$possibleCards[] = $data;
	}
	
	$result = mysqli_query($db, "SELECT additional_products.id, additional_products.name, additional_products.price, additional_products.image FROM additional_products, additional_products_mappings WHERE additional_products.id = additional_products_mappings.additional_product_id AND additional_products_mappings.product_id = '{$product['product_id']}'");	
	$possibleExtras = array();
	while($data = mysqli_fetch_assoc($result)){
		$possibleExtras[] = $data;
	}
	// Shipping fee
	$total += 4.95;
}

?>




<div class="center">
	<div id="funnel">

		<!-- / funnel left container \ -->
		<section id="funnelleftCntr">

			<!-- / step box \ -->
			<div class="stepBox">
				<ul>
					<li class="active step1">
						<a href="winkelwagen"><span class="count">1</span><span class="text">Uw bestelling</span></a>
					</li>
					<li class="step2">
						<a href="checkout-step-2"><span class="count">2</span><span class="text">Adresgegevens</span></a>
					</li>
					<li class="step3">
						<a href="javascript:void(0)"><span class="count">3</span><span class="text">Afronden</span></a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
			<!-- \ step box / -->
		
		
			<?php if(isset($_SESSION['cart']) && count($_SESSION['cart'][0]) > 0): ?>
			<!-- / stepdetail box \ -->
			<div class="stepdetailBox">
				<div class="left">
					<img src="<?php echo $product['image']; ?>" alt="">
				</div>
				<div class="right">
					<ul>
						<li>
							<img src="<?php echo $product['image']; ?>" class="showmobileimg" alt="">
							<span class="text"><?php echo $product['name']; ?></span> <span>&euro;<?php echo number_format($product['price'], 2, ',', '.'); ?></span>
						</li>
						<?php if(isset($_SESSION['cart'][0]['card']) && count($_SESSION['cart'][0]['card']) > 0): ?>
						<li><?php echo $card['name']; ?><span>&euro;<?php echo number_format($card['price'], 2, ',', '.'); ?></span><a class="crossBtn" href="javascript:void(0)" id="removeCardButton"></a></li>
						<?php endif; ?>
						<?php if(isset($_SESSION['cart'][0]['extra']) && count($_SESSION['cart'][0]['extra']) > 0): ?>
						<li><?php echo $extra['name']; ?><span>&euro;<?php echo number_format($extra['price'], 2, ',', '.'); ?></span><a class="crossBtn" href="javascript:void(0)" id="removeExtraButton"></a></li>
						<?php endif; ?>
						<li>Bezorgkosten<span>&euro;4,95</span></li>
						<li>
							<strong>Totaal</strong> 
							<span><strong>&euro;<?php echo number_format($total, 2, ',', '.'); ?></strong></span>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
			<!-- \ stepdetail box / -->

			<!-- / addcard box \ -->
			<div class="addcardBox">
				<form>
					<fieldset>
						<div class="wrap2">
							<span class="checkbox">
								<input id="check1" type="checkbox" name="card[add]" value="1" <?php if(isset($_SESSION['cart'][0]['card']) && count($_SESSION['cart'][0]['card']) > 0){ echo 'checked'; } ?>>
								<label for="check1">Kaartje toevoegen</label>
							</span>
							<select name="card[id]" id="card-select">
								<?php foreach($possibleCards as $card): ?>
								<option value="<?php echo $card['id']; ?>" <?php if(isset($_SESSION['cart'][0]['card']) && $card['id'] == $_SESSION['cart'][0]['card']['id']){ echo 'selected'; } ?>><?php echo $card['name']; ?> &euro; <?php echo number_format($card['price'], 2, ',', '.'); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="card">
							<p>Hier komt uw tekst te staan</p>
						</div>
						<div class="clear"></div>
						<div class="clear"></div>
						<label>Tekst voor op het kaartje</label>
						<textarea name="card[text]" placeholder="Typ hier uw tekst. Vergeet niet de afzender te vermelden."><?php if(isset($_SESSION['cart'][0]['card']['text'])){ echo $_SESSION['cart'][0]['card']['text']; } ?></textarea>
						<span id="card-chars-left">150 karakters over</span>
					</fieldset>
				</form>
				<div class="clear"></div>
			</div>
			<!-- \ addcard box / -->

			<!-- / specialorder box \ -->
			<div class="specialorderBox" id="aExtra">
				<?php if (count($possibleExtras) > 0): ?>
				<h4>Uw bestelling speciaal maken met iets extra's?</h4>
				<ul id="extras-list">
					<?php foreach($possibleExtras as $e): ?>
					<li>
						<div class="block">
							<a href="javascript:void(0)" <?php if(isset($extra) && $e['id'] == $extra['id']) { echo 'class="active"'; } ?>data-extra-id="<?php echo $e['id']; ?>">
								<img src="<?php echo $e['image']; ?>" alt="">
								<p class="title"><?php echo $e['name']; ?></p>
								<p>
									<?php 
									$eSplit = explode('.', number_format($e['price'], 2));
									?>
									<span><?php echo $eSplit[0]; ?>,</span> 
									<sup><?php echo $eSplit[1]; ?></sup>
								</p>
								<div class="btn"></div>
								<div class="rightbtn"></div>
							</a>
						</div>
					</li>
					<?php endforeach; ?>
					<li style="list-style: none; display: inline">
						<div class="clear"></div>
					</li>
				</ul>
				<?php endif; ?>
				<a class="addrssBtn" href="javascript:void(0)" id="cartToAddress">Volgende: Adresgegevens</a>

				<div class="clear"></div>
			</div>
			<!-- \ specialorder box / -->
			<?php else: ?>
			<span>U heeft nog geen bloemen om te laten bezorgen.</span>
			<?php endif; ?>
		</section>
		<!-- \ funnel left container / -->

		<!-- / funnel right container \ -->
		<section id="funnelrightCntr">

			<!-- / querry box \ -->
			<div class="queryBox">
				<div class="left">
					<img src="images/img8.png" alt="">
				</div>
				<div class="right">
					<p class="title">Vragen tijdens het bestellen?</p>
					<p>Neem gerust contact op met onze klantenservice.</p>
					<p class="cal">088 110 8000</p>
					<p class="services">Klantenservice open</p>
				</div>
				<div class="clear"></div>
			</div>
			<!-- \ querry box / -->

			<!-- / shopingblock box \ -->
			<div class="shopingblockBox">
				<div class="left">
					<div class="figure">8.9</div>
					<span>Goed</span>
				</div>
				<div class="right">
					<strong class="title">Ella de Vries</strong>

					<p>&rdquo;Heel erg mooi boeket en super snel bezorgd ook!&rdquo;</p>
				</div>
				<div class="clear"></div>
				<img src="images/logo1.png" alt="">
				<div class="rank">
					<ul>
						<li>
							<a href="#">
								<img src="images/star.png" alt="">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="images/star.png" alt="">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="images/star.png" alt="">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="images/star.png" alt="">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="images/star1.png" alt="">
							</a>
						</li>
					</ul>
					<div class="clear"></div>
					<p>Uit 3267 beoordelingen</p>
				</div>
				<div class="clear"></div>
			</div>
			<!-- \ shopingblock box / -->

		</section>
		<!-- \ funnel right container / -->

		<div class="clear"></div>
	</div>
</div>
<div class="clear"></div>
