<?php
$product = array();

$stmt = mysqli_prepare($db, "SELECT id, name, price, image, description, terms FROM products WHERE slug = ?");
mysqli_stmt_bind_param($stmt, 's', $split[1]);
mysqli_stmt_execute($stmt);
$stmt->bind_result($product['id'], $product['name'], $product['price'], $product['image'], $product['description'], $product['terms']);
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

foreach($variations as $key => $variation){
	$stmt = mysqli_prepare($db, "SELECT name FROM product_variation_overrides WHERE id = ?");
	mysqli_stmt_bind_param($stmt, 'i', $variation['id']);
	mysqli_stmt_execute($stmt);
	$stmt->bind_result($override['name']);
	$stmt->fetch(); 
	$stmt->close(); 

	if(isset($override['name'])){
		$variations[$key]['name'] = $override['name'];
		unset($override);
	}
}


$pageTitle = $product['name'];

?>

<div class="center">

	<!-- / summery box \ -->
	<section class="summaryBox">
		<div class="mobiletopright">
			<p class="title"><?php echo $product['name']; ?></p>
			<div class="rank">
				<a href="#">
					<img src="images/star2.png" alt="">
				</a>
				<a href="#">
					<img src="images/star2.png" alt="">
				</a>
				<a href="#">
					<img src="images/star2.png" alt="">
				</a>
				<a href="#">
					<img src="images/star2.png" alt="">
				</a>
				<a href="#">
					<img src="images/star3.png" alt="">
				</a>
				<span>24 beoordelingen</span>
			</div>
		</div>
		<div class="left">
			<img src="<?php echo $product['image']; ?>" class="imghide" alt="">
			<img src="<?php echo $product['image']; ?>" class="imgshow" alt="">
			<p><?php echo $product['terms']; ?></p>
		</div>
		<div class="right">
			<form action="winkelwagen" method="post">
				<p class="title"><?php echo $product['name']; ?></p>
				<div class="rank">
					<a href="#">
						<img src="images/star2.png" alt="">
					</a>
					<a href="#">
						<img src="images/star2.png" alt="">
					</a>
					<a href="#">
						<img src="images/star2.png" alt="">
					</a>
					<a href="#">
						<img src="images/star2.png" alt="">
					</a>
					<a href="#">
						<img src="images/star3.png" alt="">
					</a>
					<span>24 beoordelingen</span>
				</div>
				<ul class="list" id="variations-list">
					<?php if(count($variations) < 4): ?>
						<?php foreach($variations as $variation): ?>
						<li data-variation-id="<?php echo $variation['id']; ?>">
							<a class="klikken" href="javascript:void(0)">
								<img src="<?php echo $product['image']; ?>" alt="">
								<?php
								$variationPriceSplit = explode('.', number_format($variation['price'], 2));
								?>
								<strong><?php echo ucfirst(str_replace($product['name'] . ' ', '', $variation['name'])); ?></strong> 
								<span><?php echo $variationPriceSplit[0]; ?>,</span> 
								<sup><?php echo $variationPriceSplit[1]; ?></sup>
							</a>
						</li>
						<?php endforeach; ?>
					<?php else: ?>
						<select id="product-variation-select" name="product-variation-id">
							<?php foreach($variations as $variation): ?>
								<option value="<?php echo $variation['id']; ?>"><?php echo ucfirst(str_replace($product['name'] . ' ', '', $variation['name'])); ?> - &euro; <?php echo number_format($variation['price'], 2, ',', falseR); ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</ul>
				<?php if(count($variations) < 4): ?>
				<input type="hidden" name="product-variation-id"/>
				<?php endif; ?>
				<input type="hidden" name="action" value="add"/>
				<span class="label">* Excl. &euro;4,95 verzendkosten</span> <button class="btn">Bestel direct</button>
				<br />
				<a class="forgot">Nu besteld is vandaag nog bezorgd</a>
			</form>
		</div>
	</section>
	<!-- \ summery box / -->

</div>
 <div class="clear"></div>
                    <div class="center">
                        <div id="ourCntr">
                            <div id="left">

                                <!-- / about box \ -->
                                <aside class="ourBox">
                                    <p class="title">Onze beloftes</p>
                                    <div class="block">
                                        <div class="shopingblock">
                                            <div class="cal3 mobile">
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
                                            </div>
                                            <div class="cal1">
                                                <div class="figure">8.9</div>
                                                <span>Goed</span>
                                            </div>
                                            <div class="cal2">
                                                <strong class="title">Ella de Vries</strong>

                                                <p>&rdquo;Heel erg mooi boeket en super snel bezorgd ook!&rdquo;</p>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="cal3 mobileDuplicate">
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
                                            </div>
                                        </div>
                                        <div class="link">
                                            <ul>
                                                <li>Voor 13:00 uur besteld, vandaag bezorgd</li>
                                                <li class="icon5">7 dagen versgarantie</li>
                                                <li class="icon6">Veilig betalen en achteraf betalen mogelijk</li>
                                            </ul>
                                        </div>
                                    </div>
                                </aside>
                                <aside class="ourBox">
                                    <p class="title">Omschrijving</p>
                                    <div class="block">
                                        <p class="intro">Een bonte verzameling van vrolijk gekleurde bloemen vormen dit sprankelend geheel. Laat vrolijke bloemen bezorgen met dit boeket!</p>
                                    </div>
                                </aside>
                                <!-- \ about box / -->

                            </div>
                            <div id="right">

                                <!-- / reviews box \ -->
                                <aside class="reviewsBox reviewRight">
                                    <p class="title">89 Beoordelingen van klanten</p>
                                    <ul>
                                        <li>
                                            <p class="subTitle">
                                                Harold 
                                                <span class="rank">
                                                    <a href="#">
                                                        <img src="images/star2.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <img src="images/star2.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <img src="images/star2.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <img src="images/star2.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <img src="images/star3.png" alt="">
                                                    </a>
                                                </span>
                                            </p>
                                            <p class="intro">Top bestelling/aflevering en resultaat meer hoef ik niet toe te voegen!<span>Wormerveer, 17 juni</span></p>
                                        </li>
                                        <li>
                                            <p class="subTitle">Dominique van Straaten</p>
                                            <div class="rank">
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star3.png" alt="">
                                                </a>
                                            </div>
                                            <p></p>
                                            <p class="intro">Heb door jullie een boeket bij mijn moeder laten bezorgen voor Moederdag op zaterdag . Complimenten voor het mooie boeket, zij is er heel blij mee. Bedankt voor de goede service.<span>Wormerveer, 13 juni</span></p>
                                        </li>
                                        <li>
                                            <p class="subTitle">Lieke van der Veer</p>
                                            <div class="rank">
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star2.png" alt="">
                                                </a>
                                                <a href="#">
                                                    <img src="images/star3.png" alt="">
                                                </a>
                                            </div>
                                            <p></p>
                                            <p class="intro">Mooi boeket, dezelfde dag nog bezorgd. Vandaag ga ik zaterdag uitproberen. Ben benieuwd!<span>Doetinchem, 12 juni</span></p>
                                        </li>
                                    </ul>
                                    <a class="reviews" href="#">Schrijf zelf een beoordeling</a> <a class="reviewsall" href="#">Bekijk alle beoordelingen</a>
                                </aside>
                                <!-- \ reviews box / -->

                            </div>
                        </div>

                        <!-- / mobile summery box \ -->
                        <div class="mobilesummaryBox">
                            <p class="title">Dit wordt hem!</p>
                            <div class="inner">
                                <div class="left">
                                    <img src="images/img9.png" alt="">
                                </div>
                                <div class="right">
                                    <p class="subtitle">Boeket Joyce</p>
                                    <div class="rank">
                                        <a href="#">
                                            <img src="images/star2.png" alt="">
                                        </a>
                                        <a href="#">
                                            <img src="images/star2.png" alt="">
                                        </a>
                                        <a href="#">
                                            <img src="images/star2.png" alt="">
                                        </a>
                                        <a href="#">
                                            <img src="images/star2.png" alt="">
                                        </a>
                                        <a href="#">
                                            <img src="images/star3.png" alt="">
                                        </a>
                                    </div>
                                    <strong>
                                        Vanaf <span>14.</span> 
                                        <sup>95</sup>
                                    </strong>
                                </div>
                                <div class="clear"></div>
                                <a href="#" class="btn">Bestel direct</a>
                                <br />
                                <a href="#" class="forgot">Nu besteld is vandaag nog bezorgd</a>
                            </div>
                        </div>
                        <!-- \ mobile summery box / -->

                        <!-- / product box \ -->
                        <section class="productBox column">
                            <h3 class="title">Andere interessante boeketten</h3>
                            <ul>
                                <li>
                                    <a href="#">
                                        <div class="img">
                                            <img src="images/productImg1.jpg" alt="">
                                        </div>
                                        <p class="title">Boeket Samantha</p>
                                        <p>
                                            Vanaf <span>14.</span> 
                                            <sup>95</sup>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="img">
                                            <img src="images/productImg1.jpg" alt="">
                                        </div>
                                        <p class="title">Boeket Samantha</p>
                                        <p>
                                            Vanaf <span>14.</span> 
                                            <sup>95</sup>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="img">
                                            <img src="images/productImg1.jpg" alt="">
                                        </div>
                                        <p class="title">Boeket Samantha</p>
                                        <p>
                                            Vanaf <span>14.</span> 
                                            <sup>95</sup>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="img">
                                            <img src="images/productImg1.jpg" alt="">
                                        </div>
                                        <p class="title">Boeket Samantha</p>
                                        <p>
                                            Vanaf <span>14.</span> 
                                            <sup>95</sup>
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </section>
                        <!-- \ product box / -->

                    </div>
                    <div class="clear"></div>
                </div>
