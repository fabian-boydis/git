<?php

if(!isset($_SESSION['beheer'])){
	header('location: beheer');
	exit();
}

if(isset($_POST['populair'])){
	mysqli_query($db, "DELETE FROM home_products");
	foreach($_POST['populair'] as $id){
		mysqli_query($db, "INSERT INTO home_products (id) VALUES ('{$id}')");
	}
}

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

$stmt = mysqli_prepare($db, "SELECT id FROM home_products");
$stmt->execute();
$stmt->bind_result($id);
while($stmt->fetch()){
	$homeProducts[] = $id;
}
$stmt->close(); 
?>
<div class="center">
	<div id="funnel">
		<form method="POST" action="">
			<?php foreach($products as $key => $value): ?>
			<input type="checkbox" name="populair[]" value="<?php echo $value['id']; ?>" <?php if(in_array($value['id'], $homeProducts)){ echo 'checked'; } ?>/> <?php echo $value['name']; ?><br />
			<?php endforeach; ?>
			
			<button type="submit">Opslaan</button>
		</form>
	</div>
</div>