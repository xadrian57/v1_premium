<?php 
	$email = urldecode($_GET['e']);
?>

<script type="text/javascript">
	localStorage.setItem('_trh','<?= $email ?>');
	window.location.href = "/confirmacao";
</script>