<?php
$product = array();

$stmt = mysqli_prepare($db, "SELECT id, slug, name, price, image, description, terms FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $split[1]);
mysqli_stmt_execute($stmt);
$stmt->bind_result($product['id'], $product['slug'], $product['name'], $product['price'], $product['image'], $product['description'], $product['terms']);
$stmt->fetch(); 
$stmt->close(); 

if(strlen($product['name']) == 0){
	header('location: /notfound');
	exit();
}

$stmt = mysqli_prepare($db, "SELECT name FROM product_overrides WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $product['id']);
mysqli_stmt_execute($stmt);
$stmt->bind_result($override['name']);
$stmt->fetch(); 
$stmt->close(); 

if(isset($override['name'])){
	$product['name'] = $override['name'];
	unset($override);
}

$variations = array();

$stmt = mysqli_prepare($db, "SELECT id, price, name FROM product_variations WHERE product_id = ? ORDER BY price ASC ");
$stmt->bind_param('i', $product['id']);
$stmt->execute();
$stmt->bind_result($id, $price, $name);
while($stmt->fetch()){
	$variations[] = array(
		'id' => $id,
		'price' => $price,
		'name' => $name
	);
}
$stmt->close();

$maten = array();
$maten[1] = 'Standaard';
$maten[2] = 'Groot';
$maten[3] = 'XL';
 
?>

<div class="popup">
	<a href="#" class="closeit">&nbsp;</a>
	<div class="left">
		<a href="product/<?php echo $product['slug']; ?>">
			<div class="img">
				<img src="<?php echo $product['image']; ?>" alt="">
			</div>
			<p class="title"><?php echo $product['name']; ?></p>
			<p>
				<?php
				$priceSplit = explode('.', number_format($product['price'], 2));
				?>
				Vanaf <span><?php echo $priceSplit[0]; ?>,</span> 
				<sup><?php echo $priceSplit[1]; ?></sup>
			</p>
		</a>
	</div>
	<div class="right">
		<div class="thumb">
			<ul>
				<?php $variationNo = 1; ?>
				<?php foreach($variations as $variation): ?>
				<li data-variation-id="<?php echo $variation['id']; ?>">
					<a href="product/<?php echo $product['slug']; ?>?size=<?php echo $variationNo ?>">
						<!--<img src="<?php echo $product['image']; ?>" alt="">-->
						<?php
						$variationPriceSplit = explode('.', number_format($variation['price'], 2));
						?>
						<strong><?php if(count($variations) == 3) { echo $maten[$variationNo]; } else { echo ucfirst(str_replace($product['name'] . ' ', '', $variation['name'])); } ?></strong> 
						<span><?php echo $variationPriceSplit[0]; ?>,</span> 
						<sup><?php echo $variationPriceSplit[1]; ?></sup>
					</a>
				</li>
				<?php $variationNo++; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
	<a href="product/<?php echo $product['slug']; ?>" class="btn">Bekijk en bestel</a>
</div>

<?php
mysqli_close($db);
die()
?>