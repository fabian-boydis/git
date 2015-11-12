<?php
// Wachtwoord
$auth = 'Cu3cPaEK';

if($_POST['auth'] == $auth){
	$_SESSION['beheer'] = true;
}

if(isset($_SESSION['beheer'])):
?>

<div class="spacer">
<h1>Welkom</h1><br />
<a href="beheer-populaire-producten">Populaire producten</a><br />
<a href="beheer-product-overrides">Product naam overschrijven</a><br />	
</div>
<?php else: ?>
<form method="POST" action="" class="spacer">
	<input type="password" name="auth" placeholder="Wachtwoord" required/>
	<button type="submit">Inloggen</button>
</form>
<?php endif; ?>