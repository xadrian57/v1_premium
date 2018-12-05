<?php
$isTagflagAvailable = true;
$msgTagflag = '';

if($_SESSION['idPlan'] == 42) {
    
    $clientId = $_SESSION['id'];
    
    $msgTagflag = 'Para utilizar o tagflag, verifique o(s) seguinte(s) item(ns):<br/>';
    
    // 1 - Pixel Instalado => Verifica se existe inserção recente (nas ultimas 3 horas) alguma inserção na tabela RGER_
    $sql = 'SHOW TABLES LIKE \'RGER_' . $clientId . '\'';
    $result = mysqli_query($conDados, $sql);
    $row = mysqli_fetch_array($result);
    
    if(count($row)) {
        $sql = 'SELECT COUNT(*) total FROM RGER_' . $clientId . ' WHERE RGER_data > DATE_SUB(NOW(), INTERVAL 90 MINUTE)';
        $result = mysqli_query($conDados, $sql);
        $row = mysqli_fetch_array($result);
        
        if($row && $row['total'] == 0) {
            $msgTagflag .= '- Instale o Pixel.<br/>';
            $isTagflagAvailable = false;
        }
    } else {
        $msgTagflag .= '- Instale o Pixel.<br/>';
        $isTagflagAvailable = false;
    }
    
    
    
    // 2 - XML cadastrado => Verifica se existe a tabela XML_ e ela NÃO deve estar vazia
    $sql = 'SHOW TABLES LIKE \'XML_' . $clientId . '\'';
    $result = mysqli_query($conDados, $sql);
    $row = mysqli_fetch_array($result);
    
    if(count($row)) {
        $sql = 'SELECT COUNT(*) total FROM XML_' . $clientId;
        $result = mysqli_query($conDados, $sql);
        $row = mysqli_fetch_array($result);
        
        if($row && $row['total'] == 0) {
            $msgTagflag .= '- Cadastre o XML.<br/>';
            $isTagflagAvailable = false;
        }
    } else {
        $msgTagflag .= '- Cadastre o XML.<br/>';
        $isTagflagAvailable = false;
    }
    
    
    // 3 - CSS não-vazio e com cor => falar com Moisés ou com Eliabe sobre essa verificação :D
    // O Eliabe disse que não existe essa de com cor. Todos os css já tem cor.
    // Então a validação, é apenas da existencia do arquivo e se ele tem conteúdo.
    $cssFile = '../../widget/css/rh_' . sha1($clientId). '.css';
    
    // Verificando se o arquivo existe
    if(!file_exists($cssFile)) {
        
        $msgTagflag .= '- O layout dos blocos não aparenta estar correto, entre em contato com o suporte.<br/>';
        $isTagflagAvailable = false;
        
    } else if (filesize($cssFile) == 0) {
        
        $msgTagflag .= '- O layout dos blocos não aparenta estar correto, entre em contato com o suporte.<br/>';
        $isTagflagAvailable = false;
    }
}

?>

<link rel="stylesheet" type="text/css" href="assets/css/pages/my_recomendations/my_recomendations.css"/>

<div class="app-content content container-fluid">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-xs-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="breadcrumb-wrapper col-xs-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="widget_maker">Recomendações</a>
							</li>
							<li class="breadcrumb-item"><a href="minhas_recomendacoes">Minhas Recomendações</a>
							</li>
						</ol>
					</div>
				</div>
				<h3 class="content-header-title mb-0">Minhas Recomendações</h3>
			</div>
		</div>
		<div class="content-body"><!-- LISTA WIDGETS -->
			<section id="basic-listgroup">
				<div class="row match-height">
<?php
// TODO Remover esse if, quando liberar o Tagflag para uso geral
if($_SESSION['idPlan'] == 42) {
?>
					<div class="col-lg-12 col-md-12">
						<div class="borderdiv card">
							<div class="card-header">
								<h1 class="card-title">TAG FLAG</h4><div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<div class="col-lg-6 col-md-6" height="500px">
									<h3 style="color:#416d94 ; font-size: 30px ; font-weight: 600">HORAS DE TRABALHO 
										<p style=" font-size: 30px; color:#416d94">REDUZIDAS EM SEGUNDOS!</h5></p>
										<p style="font-size: 18px">O Tag Flag é uma ferramenta que faz com que você insira todos seus blocos de recomendações em sua loja com um clique, é fácil, simples e rápido de usar, e o mais importante: sem precisar saber nada de programação! 
										</p>
										<p style="font-size: 18px">Assista o vídeo ao lado e aprenda a usar o Tag Flag!</p>
					<?php if ($isTagflagAvailable){?>
									<a class="btn btn-success" href="tagflag" target="_blank">
										Começar Já!
									</a>
					<?php } else { ?>
					                <p class="red"><?php echo $msgTagflag; ?></p>
					<?php } ?>
									</div>
									<div class="col-lg-6 col-md-6">
										<center>
											<iframe width="480px" height="270px" src="https://www.youtube.com/embed/PTxN7wiaVdk" frameborder="0" allowfullscreen></iframe>
										</center>										
									</div>
								</div>
							</div>
						</div>
					</div>
<?php
// TODO Remover essa linha, quando liberar o Tagflag para uso geral
}
?>
					<!-- BUSCA -->
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Busca</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsBusca">
									</ul>
								</div>
							</div>
						</div>
					</div>
					
					<!-- OVERLAYSS -->
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">OVERLAYS</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsBasicos">
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- BLOCOS HOME -->
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">RECOMENDAÇÕES DA HOME</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsHome">
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- BLOCOS PAGINA PRODUTO -->
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">RECOMENDAÇÕES DA PÁGINA DE PRODUTO</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsProduto">
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row match-height">
					<!-- BLOCOS PAGINA CATEGORIA(STARTUP NÃO TEM) -->
					<div id="coluna-widgets-categoria" class="col-lg-12 col-md-12" style="display: none;">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">RECOMENDAÇÕES DA PÁGINA DE CATEGORIA</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>

							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsCategoria">
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- BLOCOS PAGINA BUSCA VAZIA -->
					<!-- <div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">RECOMENDAÇÕES DA PÁGINA DE BUSCA VAZIA</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsBusca">
									</ul>
								</div>
							</div>
						</div>
					</div> -->
				</div>

				<div class="row match-height">
					<!-- BLOCOS PAGINA CARRINHO(APENAS PREMIUM) -->
					<div id="coluna-widgets-carrinho" class="col-lg-12 col-md-12" style="display: none;">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">RECOMENDAÇÕES DA PÁGINA DE CARRINHO</h4>
								<a class="heading-elements-toggle"><i class="fa fa-ellipsis font-medium-3"></i></a>
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-body collapse in">
								<div class="card-block">
									<ul class="list-group" id="widgetsCarrinho">
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<!-- Modal Editar Widget-->
<div id="modalEditarWidget" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title adic" id="titulo-modal-edit"><i class="ft ft-edit"></i>&nbsp;&nbsp;Editar Bloco de Recomendação</h4>
				<span class="primary page-info" id="spec-inteligencia-modal-edit"></span>
            </div>

            <div class="modal-body">
                <div id="campos-wid-edit" class="row">
                	<div class="col-md-12">
                		<div class="form-group">
	                    	<label>Nome</label>
	                    	<div class="rh-input-icon-right">
	                    		<input id="nomeWidget" name="nome" class="form-control" type="text">
                                <abbr title="Esse nome serve para identificação do seu bloco aqui dentro do Dashboard apenas, ele não irá aparecer na sua loja" class="info-abbr">
                                    <i class="icon-info"></i>
                                </abbr>
	                    	</div>
	                    </div>
	                </div>
                	<div class="col-md-12">
	                    <div class="form-group">
	                    	<label>Título Promocional</label>
	                    	<div class="rh-input-icon-right">
		                    	<input id="tituloWidget" name="titulo" class="form-control" type="text">
                                <abbr title="Esse é o título do bloco que irá aparecer na sua loja" class="info-abbr">
                                    <i class="icon-info"></i>
                                </abbr>
	                        </div>
	                    </div>
	                </div>
                	<div class="col-md-12">
	                    <div class="form-group">
	                    	<label>UTM</label>
	                    	<div class="rh-input-icon-right">
	                    		<input id="utmWidget" name="utm" class="form-control" type="text">
                                <abbr title="Link por onde você pode fazer a analise de fluxo de dados via Google Analytics" class="info-abbr">
                                    <i class="icon-info"></i>
                                </abbr>
	                        </div>	                    	
	                    </div>
	                </div>
                	<div id="container-configuracoes" class="col-md-12">
	                </div>
                	<!-- 
                	OPCAO DE ALTERAR PÁGINA DESABILITADA
                	<div class="col-md-12">
	                    <div class="form-group">
	                    	<label>Página</label>
	                    	<select id="paginaWidget" name="pagina" class="form-control">
	                    		<option value="1">Home</option>
	                    		<option value="2">Produto</option>
	                    		<option value="3">Busca</option>
	                    		<option value="4">Categoria</option>
	                    		<option value="5">Carrinho</option>
	                    	</select>
	                    </div>
	                </div> 
	            	-->
	            	<div id="widedit-opcoes-adicionais" class="col-md-12"></div>
                </div>
            </div>

            <div class="modal-footer">
            	<span class="rh-id-wid primary pull-left">ID: <span id="rhIdWid"></span></span>
				<button type="button" class="btn btn-outline-warning" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
				<button id="btn-salva-wid" type="button" class="btn btn-outline-primary"><i class="fa fa-check"></i> Salvar Alterações</button>
			 </div>
        </div>
    </div>
</div> 

<!-- MODAL CONFIRMAR EXCLUSAO -->
<div class="modal fade text-xs-left" id="modalConfirmaExclusao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" style="display: none;" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger white">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel10">Basic Modal</h4>
			</div>
			<div class="modal-body">
				<h5>Tem certeze de que deseja excluir esse bloco de recomendação</h5>
				
				<p>Para confirmar a exclusão do seu bloco, pressione o botão confirmar.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar <i class="fa fa-times"></i></button>
				<button id="btnConfirmaExclusao" type="button" class="btn btn-outline-danger">Confirmar <i class="fa fa-check"></i></button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="assets/js/pages/minhas_recomendacoes/widget_edit.js"></script>
<script>
	

	var rhPhoto = function (el) {
		$(el).click( function(){
			var path = this.dataset.target;

			var modal = document.createElement('div');
			modal.id = 'modalViewPhoto';
			modal.classList = 'modal';

			modal.innerHTML = `
				<div class="modal-dialog modal-sm">
					<div class="modal-content text-xs-center">
						<div class="modal-header text-xs-left">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
							<h4 class="modal-title"><i class="ft-image"></i> Imagem</h4>							
						</div>
						<div class="modal-body">
							<img src="${path}">
						</div>
					</div>
				</div>
			`;

			$(modal).insertBefore($('body')[0].firstChild);
			
			$('#modalViewPhoto').modal();		
			$('#modalEditarWidget').modal('hide');

			$('#modalViewPhoto').on('hidden.bs.modal', function () {
				$('#modalEditarWidget').modal('show');
			});
		});
	}	
</script>

