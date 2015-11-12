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
	$total += $product['price'];
	
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


<?php 
die(); 
?>