<?php
	session_name('premium');
	session_start();
	
	unset ($_SESSION['email']);
	unset ($_SESSION['senha']);
	unset ($_SESSION['id']);
	unset ($_SESSION['idPlan']);
	unset ($_SESSION['idPlataforma']);
	unset ($_SESSION["currency"]);

	header ("Location: ../login");
	
	session_destroy();
?>