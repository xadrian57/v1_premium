
<!DOCTYPE html>
<head>
	<meta charset = "UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />

</head>
<body>
	<header>
		<div class="rh_header">
			<h1 class="rh_header-title">Seja bem vindo à Roi Hero<br></h1>
									 <!----><h1 class="rh_clientname"> 
										 <?php 	session_name('premium');
												session_start(); 
												echo $_SESSION['nome'];
												session_write_close(); ?>,</h1>
			<h2 class="rh_header-text"><br><br>	infelizmente ainda não temos uma integração pronta com a sua plataforma.<br>
						Iremos entrar em contato com a sua plataforma para integrarmos a Roi Hero o mais rápido possível!</h2>						 
		</div>
	</header>
	<div class="row rh_init">
		<h2 class="rh_body-text">Em até 30 minutos</h1>
		<h3 class="rh_body-text">alguém da nossa equipe irá ligar para batermos um papo e conhecermos melhor a sua loja!</h3>
	</div>
	<div class="row rh_row-funcionalidades">
		<div class="col-md-4">
			<h2 class="rh_funcionalidade-title">Essas são algumas de<br>
				nossas funcionalidades!</h2>
			<div class="row">
				<div class="rh_funcionalidade">
					<h3 class ="rh_funcionalidade-name">Funcionalidade</h3>
					<h4 class="rh_funcionalidade-descricao">Lorem ipsum dolor sit amet, consectetur adipiscing elit,<br>
															sed do eiusmod tempor incididunt labore et dolore<br>
														  magna aliqua. Ut enim ad minim veniam, quis nostrud</h4>
				</div>
			</div>
			<div class="w-100">
				<div class="rh_funcionalidade">
					<h3 class ="rh_funcionalidade-name">Funcionalidade</h3>
					<h4 class="rh_funcionalidade-descricao">Lorem ipsum dolor sit amet, consectetur adipiscing elit,<br>
															sed do eiusmod tempor incididunt labore et dolore<br>
														  magna aliqua. Ut enim ad minim veniam, quis nostrud</h4>
				</div>
			</div>
			<div class="w-100">
				<div class="rh_funcionalidade">
					<h3 class ="rh_funcionalidade-name">Funcionalidade</h3>
					<h4 class="rh_funcionalidade-descricao">Lorem ipsum dolor sit amet, consectetur adipiscing elit,<br>
															sed do eiusmod tempor incididunt labore et dolore<br>
														  magna aliqua. Ut enim ad minim veniam, quis nostrud</h4>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<img class="rh_img-funcionalidades" src="img/funcionalidades.png" />
		</div>
	</div>
	<div class="row rh_row-help">
		<div class="col-md-6">
			<a href="#">
				<div class="rh_help-option">
					<img class="rh_img-icon" src="img/icon1.png" />
					<h3 class="rh_descricao">O prazo da integração completa pode demorar<br>
						cerca de 7 a 15 dias.Podendo ser adiantado<br>
						conforme for a demanda da plataforma.
					</h3>
				</div>
			</a>
		</div>
		<div class="col-md-6">
			<a href="#">
				<div class="rh_help-option">
					<img class="rh_img-icon" src="img/icon2.png" />
					<h3 class="rh_descricao">Caso queira ajudar, envie um ticket para<br>
						a sua plataforma informando a sua<br>
						necessidade de utilizar a Roi Hero.
					</h3>
				</div>
			</a>
		</div>
	</div>
	<footer>
		<div class="rh_footer">
				<h1 class="rh_footer-text">Assim que integrarmos com a sua plataforma, você também terá uma loja<br>
						com recomendações personalizadas, assim como estes sites!</h1>
		</div>
	</footer>
</body>
</html>