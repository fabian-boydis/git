<?php
$category = array();

$stmt = mysqli_prepare($db, "SELECT id, name FROM categories WHERE slug = ?");
mysqli_stmt_bind_param($stmt, 's', $split[1]);
mysqli_stmt_execute($stmt);
$stmt->bind_result($category['id'], $category['name']);
$stmt->fetch(); 
$stmt->close(); 

if(strlen($category['name']) == 0){
	header('location: /notfound');
	exit();
}

$products = array();

$stmt = mysqli_prepare($db, "SELECT id, name, slug, image FROM products, product_categories_mappings WHERE product_categories_mappings.category_id = ? AND products.id = product_categories_mappings.product_id ORDER BY price ASC ");
$stmt->bind_param('i', $category['id']);
$stmt->execute();
$stmt->bind_result($id, $name, $slug, $image);
while($stmt->fetch()){
	$products[] = array(
		'id' => $id,
		'name' => $name,
		'image' => $image,
		'slug' => $slug
	);
}
$stmt->close(); 

foreach($products as $key => $value){
	
	$stmt2 = mysqli_prepare($db, "SELECT name FROM product_overrides WHERE id = ?");
	mysqli_stmt_bind_param($stmt2, 'i', $value['id']);
	mysqli_stmt_execute($stmt2);
	$stmt2->bind_result($override['name']);
	$stmt2->fetch(); 
	$stmt2->close(); 

	if(isset($override['name'])){
		$products[$key]['name'] = $override['name'];
		unset($override);
	}
	
	
	$result = mysqli_query($db, "SELECT price FROM product_variations WHERE product_id = '{$value['id']}' ORDER BY price ASC LIMIT 1");
	$data = mysqli_fetch_assoc($result);
	$products[$key]['price'] = $data['price'];
}

$pageTitle = $category['name'];
?>

<div class="center">


	<!-- / categoryCntr container \ -->
	<div id="categoryCntr">


		<!-- / rightCntr container \ -->
		<section id="rightCntr">

			<!-- / product box \ -->
			<section class="productBox threecolumn">
				<h3 class="title"><?php echo $category['name']; ?></h3>
				<div class="time">
					<p>Vandaag nog laten bezorgen? <span>U heeft nog 2 uur, 10 minuten en 43 seconden</span></p>
				</div>
				<ul>
					<li class="reviews-item hidemobile">
						<div class="shopingblock">
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
						</div>
					</li>

				
					<!--<li>
						<a href="#">
							<div class="img">
								<img src="images/productImg1.jpg" alt="">
							</div>
							<span class="tag">Populair!</span>

							<p class="title">Boeket Samantha</p>
							<p>
								Vanaf <span>14.</span> 
								<sup>95</sup>
							</p>
						</a>
						<!--<div class="popup">
							<div class="left">
								<a href="#">
									<div class="img">
										<img src="images/flower.png" alt="">
									</div>
									<p class="title">Boeket Samantha</p>
									<p>
										Vanaf <span>14.</span> 
										<sup>95</sup>
									</p>
								</a>
							</div>
							<div class="right">
								<div class="thumb">
									<ul>
										<li>
											<a href="#">
												<img src="images/small_img.png" alt="">
												<strong>Middel</strong> <span>14,</span> 
												<sup>95</sup>
											</a>
										</li>
										<li>
											<a href="#" class="active">
												<img src="images/small_img2.png" alt="">
												<strong>Middel</strong> <span>14,</span> 
												<sup>95</sup>
												<span class="arrow"></span>
											</a>
										</li>
										<li>
											<a href="#">
												<img src="images/small_img3.png" alt="">
												<strong>Middel</strong> <span>14,</span> 
												<sup>95</sup>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="clear"></div>
							<a href="#" class="btn">Bekijk en bestel</a>
						</div>-->
					<!--</li>-->
					<?php foreach($products as $product): ?>
					<li class="category-item" id="product<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>">
						<a href="product/<?php echo $product['slug']; ?>" >
							<div class="img">
								<img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
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
					</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<aside class="ourBox ourhome">
		<p class="title">Onze beloftes</p>
		<div class="block">
			<div class="shopingblock">
				<div class="cal1">
					<div class="figure">8.9</div>
					<span>Goed</span>
				</div>
				<div class="cal2">
					<strong class="title">Ella de Vries</strong>

					<p>“Heel erg mooi boeket en super snel bezorgd ook!”</p>
				</div>
				<div class="cal3">
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
			<aside class="reviewsBox">
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
                                <li>
                                    <p class="subTitle">Hanneke</p>
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
                                    <p class="intro">Opnieuw boven verwachting uitvoering van het verzoek bloemen te laten bezorgen van uit Baku naar Deventer. Niets op aan te merken<span>Haren, 15 juni</span></p>
                                </li>
                            </ul>
                            <a class="reviews" href="#">Schrijf zelf een beoordeling</a> <a class="reviewsall" href="#">Bekijk alle beoordelingen</a>
                        </aside>
                        <!-- \ reviews box / -->

                        <!-- / about box \ -->
                        <aside class="aboutBox">
                            <p class="title">Over BloemenLatenBezorgen.nl</p>
                            <p class="intro">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>
                            <p class="intro">
                                Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate
                                <br />
                                eget, arcu. In enim justo, rhoncus ut, imperdietas venenatis vitae, justo.
                            </p>
                            <p class="intro">Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi</p>
                        </aside>
			<!-- \ product box / -->

		</section>
		<!-- \ rightCntr container / -->

	</div>
	<!-- \ categoryCntr container / -->

</div>

