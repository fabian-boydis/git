<?php

if(!isset($_SESSION['beheer'])){
	header('location: beheer');
	exit();
}

if(isset($_POST['product'])){
	foreach($_POST['product'] as $k => $p){
		if(!isset($p['enabled'])){
			mysqli_query($db, "DELETE FROM product_overrides WHERE id = '{$k}'");
		} else {
			$result = mysqli_query($db, "SELECT id FROM product_overrides WHERE id = '{$k}'");
			if(mysqli_num_rows($result) > 0){
				$st2 = mysqli_prepare($db, "UPDATE product_overrides SET name = ? WHERE id = ?");
				mysqli_stmt_bind_param($st2, 'si', $p['name'], $k);
				mysqli_stmt_execute($st2);
				mysqli_stmt_close($st2);
			} else {
				$st2 = mysqli_prepare($db, "INSERT INTO product_overrides (id, name) VALUES (?, ?)");
				mysqli_stmt_bind_param($st2, 'is', $k, $p['name']);
				mysqli_stmt_execute($st2);
				mysqli_stmt_close($st2);
			}
		}
	}
}

$products = array();
$stmt = mysqli_prepare($db, "SELECT id, name FROM products");
$stmt->execute();
$stmt->bind_result($id, $name);
while($stmt->fetch()){
	$products[] = array(
		'id' => $id,
		'name' => $name
	);
}
$stmt->close(); 

$stmt = mysqli_prepare($db, "SELECT id, name FROM product_overrides");
$stmt->execute();
$stmt->bind_result($id, $name);
while($stmt->fetch()){
	$overrides[] = $id;
	$overrideProducts[$id] = $name;
}
$stmt->close(); 
?>
<div class="center">
	<div id="funnel">
		<form method="POST" action="">
			<table>
			<?php foreach($products as $key => $value): ?>
			<tr>
				<td>
					<input type="checkbox" name="product[<?php echo $value['id']; ?>][enabled]" value="1" <?php if(in_array($value['id'], $overrides)){ echo 'checked'; } ?>/>
				</td>
				
				<td>
					<?php echo $value['name']; ?>
				</td>
				
				<td>
					<input type="text" name="product[<?php echo $value['id']; ?>][name]" value="<?php if(isset($overrideProducts[$value['id']])){ echo $overrideProducts[$value['id']]; } ?>"/>
				</td>
				
				<td>
					<a href="beheer-product-variations-overrides?pid=<?php echo $value['id']; ?>">Bewerk variaties</a>
				</td>
			</tr>
			<?php endforeach; ?>
			</table>
			<button type="submit">Opslaan</button>
		</form>
	</div>
</div>