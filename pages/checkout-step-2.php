<?php
$pageTitle = 'Afrekenen';

if(!isset($_SESSION['cart'][0]) || count($_SESSION['cart'][0]) < 1){
	header('location: winkelwagen');
	exit();
}

if(isset($_GET['action']) && $_GET['action'] == 'get_address'){
	$deliveryWs = new SoapClient('https://webservice.topbloemen.nl/soap/delivery/?wsdl', array(
		'login' => $webserviceUsername,
		'password' => $webservicePassword
	));
	
	$result = $deliveryWs->getAddress($_GET['country'], $_GET['postcode'], $_GET['housenumber']);
	header("Content-Type: application/json", true);
	echo json_encode($result);
	die();
}

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
					<li class="active step2">
						<a href="checkout-step-2"><span class="count">2</span><span class="text">Adresgegevens</span></a>
					</li>
					<li class="step3">
						<a href="javascript:void(0)"><span class="count">3</span><span class="text">Afronden</span></a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
			<!-- \ step box / -->
		
			<!-- / specialorder box \ -->
			<div class="specialorderBox">
				<form method="POST" action="javascript:void(0)" id="checkoutAddressDetails">
					<h3>Verzender gegevens</h3>
					<fieldset>
						<div>
							<label>Naam</label>
							<input type="text" name="sender[name]" placeholder="Naam" required/>
						</div>
						<div>
							<label>Postcode + huisnummer</label>
							<input type="text" name="sender[postcode]" placeholder="Postcode" required/>
							<input type="text" name="sender[number]" placeholder="Huisnummer" required/>
							<input type="hidden" name="sender[address]"/>
							<input type="hidden" name="sender[city]"/>
						</div>
						<div>
							<span id="sender-address-1"></span><br />
							<span id="sender-address-2"></span>
						</div>
						<div>
							<label>Email</label>
							<input type="email" name="sender[email]" placeholder="Naam" required/>
						</div>
						<div>
							<label>Telefoonnummer</label>
							<input type="text" name="sender[phone]" placeholder="Telefoonnummer" required/>
						</div>
						<div class="clear"></div>

					</fieldset>

					<h3>Ontvanger gegevens</h3>
					<fieldset>
						<div>
							<label>Naam</label>
							<input type="text" name="recipient[name]" placeholder="Naam" required/>
						</div>
						<div>
							<label>Postcode + huisnummer</label>
							<input type="text" name="recipient[postcode]" placeholder="Postcode" required/>
							<input type="text" name="recipient[number]" placeholder="Huisnummer" required/>
							<input type="hidden" name="recipient[address]"/>
							<input type="hidden" name="recipient[city]"/>
						</div>
						<div>
							<span id="recipient-address-1"></span><br />
							<span id="recipient-address-2"></span>
						</div>
						<div>
							<label>Telefoonnummer</label>
							<input type="text" name="recipient[phone]" placeholder="Telefoonnummer" required/>
						</div>
						<div class="clear"></div>

					</fieldset>
					<a class="addrssBtn" href="javascript:void(0)" id="cartToAddress">Afronden</a>
					<button type="submit" id="hiddenSubmitForValidation" style="display: none;"></button>
				</form>
			</div>
			<!-- \ specialorder box / -->

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
