<?php 

$pageTitle = 'Afrekenen';

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

if(isset($_SESSION['cart'][0]['card'])){
	$result = mysqli_query($db, "SELECT name, price FROM cards WHERE id = {$_SESSION['cart'][0]['card']['id']}");
	$card = mysqli_fetch_assoc($result);
}

if(isset($_SESSION['cart'][0]['extra']['id'])){
	$result = mysqli_query($db, "SELECT id, name, price FROM additional_products WHERE id = {$_SESSION['cart'][0]['extra']['id']}");
	$extra = mysqli_fetch_assoc($result);
}

$deliveryWs = new SoapClient('https://webservice.topbloemen.nl/soap/delivery/?wsdl', array(
	'login' => $webserviceUsername,
	'password' => $webservicePassword
));

$dates = $deliveryWs->getDates($product['product_id'], 'NL');

if(isset($_POST['action']) && $_POST['action'] == 'to_gateway'){
	$orderWs = new SoapClient('https://webservice.topbloemen.nl/soap/order/?wsdl', array(
		'login' => $webserviceUsername,
		'password' => $webservicePassword
	));	
	
	
	// Set correct gateway before redirecting
	$currXml = str_replace('<paymentMethod>' . $_SESSION['checkout']['method'] . '</paymentMethod>',  '<paymentMethod>' . $_POST['method'] . '</paymentMethod>', $_SESSION['checkout']['xml']);
	
	// Set correct delivery date
	$currXml = str_replace('<date>' . $_SESSION['checkout']['date'] . '</date>',  '<date>' . $_POST['date'] . '</date>', $currXml);
	
	$result = $orderWs->update($currXml, $_SESSION['checkout']['orderId']);
	
	$paymentWs = new SoapClient('https://webservice.topbloemen.nl/soap/payment/?wsdl', array(
		'login' => $webserviceUsername,
		'password' => $webservicePassword
	));
		
	$result = $paymentWs->getUrl($_SESSION['checkout']['orderId'], str_replace('step-3', 'return', 'https://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '?status'));
	echo $result;
	die();
}

// Create order otherwise return error via json
if(isset($_POST['sender'])){
	$orderWs = new SoapClient('https://webservice.topbloemen.nl/soap/order/?wsdl', array(
		'login' => $webserviceUsername,
		'password' => $webservicePassword
	));

	$paymentWs = new SoapClient('https://webservice.topbloemen.nl/soap/payment/?wsdl', array(
		'login' => $webserviceUsername,
		'password' => $webservicePassword
	));
	
	
	$xml = '<?xml version="1.0"?>
<order>
	<ip>' . $_SERVER['REMOTE_ADDR'] . '</ip>
	<sender>
		<name>' . $_POST['sender']['name'] . '</name>
		<address>' . $_POST['sender']['address'] . '</address>
		<postcode>' . $_POST['sender']['postcode'] . '</postcode>
		<city>' . $_POST['sender']['city'] . '</city>
		<country>NL</country>
		<phone>' . $_POST['sender']['phone'] . '</phone>
		<emailAddress>' . $_POST['sender']['email'] . '</emailAddress>
	</sender>
	<recipient>
		<name>' . $_POST['recipient']['name'] . '</name>
		<address>' . $_POST['recipient']['address'] . '</address>
		<postcode>' . $_POST['recipient']['postcode'] . '</postcode>
		<city>' . $_POST['recipient']['city'] . '</city>
		<country>NL</country>
		<phone>' . $_POST['recipient']['phone'] . '</phone>
	</recipient>
	<delivery>
		<date>' . $dates[0]->date . '</date>
	</delivery>
	<product>
		<id>' . $product['product_id'] . '</id>
		<variationId>' . $product['id'] . '</variationId>
		<amount>1</amount>
	</product>';
	
if(isset($_SESSION['cart'][0]['card'])){
	$xml .= '	<card>
		<id>' . $_SESSION['cart'][0]['card']['id'] . '</id>
		<amount>1</amount>
		<text>' . $_SESSION['cart'][0]['card']['text'] . '</text>
	</card>';
}

if(isset($_SESSION['cart'][0]['extra']['id'])){
	$xml .= '	<additionalProduct>
		<id>' . $_SESSION['cart'][0]['extra']['id'] . '</id>
		<amount>1</amount>
	</additionalProduct>';
}

$xml .= '<paymentMethod>ideal</paymentMethod>
	</order>';
	
	$result = $orderWs->create($xml);
	
	$methods = $paymentWs->getMethods($xml, $result->orderId);

	if($result->status == 'success'){
		$_SESSION['checkout']['orderId'] = $result->orderId;
		$_SESSION['checkout']['methods'] = $methods;
		$_SESSION['checkout']['xml'] = $xml;
		$_SESSION['checkout']['method'] = 'ideal';
		$_SESSION['checkout']['date'] = $dates[0]->date;
		echo json_encode(array('status' => 'success', 'message' => $result->error->message));
	} else {
		echo json_encode(array('status' => 'error', 'message' => $result->error->message));
	}
	
	header("Content-Type: application/json", true);
	die();
}

if(!isset($_SESSION['checkout']['orderId'])){
	header('location: winkelwagen');
	exit();
}

$total = 0;
$result = mysqli_query($db, "SELECT products.image, products.id as product_id, product_variations.name, product_variations.price, products.terms, product_variations.id FROM products, product_variations WHERE products.id = product_variations.product_id AND product_variations.id = {$_SESSION['cart'][0]['product-variation-id']}");
$product = mysqli_fetch_assoc($result);
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

// Shipping fee
$total += 4.95;

?>


<div class="center">
	<div id="funnel">

		<!-- / funnel left container \ -->
		<section id="funnelleftCntr">

			<!-- / step box \ -->
			<div class="stepBox">
				<ul>
					<li class="step1">
						<a href="winkelwagen"><span class="count">1</span><span class="text">Uw bestelling</span></a>
					</li>
					<li class="step2">
						<a href="checkout-step-2"><span class="count">2</span><span class="text">Adresgegevens</span></a>
					</li>
					<li class="active step3">
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
						<li><?php echo $card['name']; ?><span>&euro;<?php echo number_format($card['price'], 2, ',', '.'); ?></span></li>
						<?php endif; ?>
						<?php if(isset($_SESSION['cart'][0]['extra']) && count($_SESSION['cart'][0]['extra']) > 0): ?>
						<li><?php echo $extra['name']; ?><span>&euro;<?php echo number_format($extra['price'], 2, ',', '.'); ?></span></li>
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

			<!-- / specialorder box \ -->
			<div class="specialorderBox">
				<div>
					<label for="deliveryDate">Gewenste bezorgdatum</label>
					<select id="deliveryDate">
						<?php foreach ($dates as $date): ?>
							<option value="<?php echo $date->date; ?>"><?php echo date('d-m-Y', strtotime($date->date)); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			
				<?php foreach($_SESSION['checkout']['methods'] as $method): ?>
				<a class="addrssBtn checkout-gateway-button" href="javascript:void(0)" data-method="<?php echo $method->code; ?>"><?php echo $method->name; ?></a>
				<?php endforeach; ?>

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
