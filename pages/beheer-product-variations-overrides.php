<?php

if(!isset($_SESSION['beheer'])){
	header('location: beheer');
	exit();
}

if(!isset($_GET['pid']) || strlen($_GET['pid']) < 1){
	header('location: beheer-product-overrides.php');
	exit();
}

if(isset($_POST['variation'])){
	foreach($_POST['variation'] as $k => $p){
		if(!isset($p['enabled'])){
			mysqli_query($db, "DELETE FROM product_variation_overrides WHERE id = '{$k}'");
		} else {
			$result = mysqli_query($db, "SELECT id FROM product_variation_overrides WHERE id = '{$k}'");
			if(mysqli_num_rows($result) > 0){
				$st2 = mysqli_prepare($db, "UPDATE product_variation_overrides SET name = ? WHERE id = ?");
				mysqli_stmt_bind_param($st2, 'si', $p['name'], $k);
				mysqli_stmt_execute($st2);
				mysqli_stmt_close($st2);
			} else {
				$st2 = mysqli_prepare($db, "INSERT INTO product_variation_overrides (id, name) VALUES (?, ?)");
				mysqli_stmt_bind_param($st2, 'is', $k, $p['name']);
				mysqli_stmt_execute($st2);
				mysqli_stmt_close($st2);
			}
		}
	}
}

$variations = array();
$stmt = mysqli_prepare($db, "SELECT id, name FROM product_variations WHERE product_id = '{$_GET['pid']}'");
$stmt->execute();
$stmt->bind_result($id, $name);
while($stmt->fetch()){
	$variations[] = array(
		'id' => $id,
		'name' => $name
	);
}
$stmt->close(); 

$stmt = mysqli_prepare($db, "SELECT id, name FROM product_variation_overrides");
$stmt->execute();
$stmt->bind_result($id, $name);
while($stmt->fetch()){
	$overrides[] = $id;
	$overrideVariations[$id] = $name;
}
$stmt->close(); 
?>

<div class="center">
	<div id="funnel">
		<form method="POST" action="">
			<table>
			<?php foreach($variations as $key => $value): ?>
			<tr>
				<td>
					<input type="checkbox" name="variation[<?php echo $value['id']; ?>][enabled]" value="1" <?php if(in_array($value['id'], $overrides)){ echo 'checked'; } ?>/>
				</td>
				
				<td>
					<?php echo $value['name']; ?>
				</td>
				
				<td>
					<input type="text" name="variation[<?php echo $value['id']; ?>][name]" value="<?php if(isset($overrideVariations[$value['id']])){ echo $overrideVariations[$value['id']]; } ?>"/>
				</td>
				
			</tr>
			<?php endforeach; ?>
			</table>
			<button type="submit">Opslaan</button>
		</form>
	</div>
</div>