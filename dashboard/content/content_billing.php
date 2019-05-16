<?php 
	include '../../bd/conexao_bd_cadastro.php';
	$id_cli = $_SESSION["id"];

	$info = [];

	// select plano
	$select = "SELECT PLAN_valor as valor_plano FROM plano WHERE PLAN_id_cli = $id_cli";
	$q = mysqli_query($conCad, $select);
	$result = mysqli_fetch_assoc($q);
	$info = array_merge($info, $result);

	// select smart recomendation
	$select = "SELECT WID_cpa as smart_recomendation_cpa FROM widget WHERE WID_id_cli = $id_cli AND WID_formato = 1 OR WID_formato = 3 GROUP BY WID_id_cli";
	$q = mysqli_query($conCad, $select);	
	$result = mysqli_fetch_assoc($q);
	$info = array_merge($info, $result);

	// select smart recovery
	$select = "SELECT WID_cpa as smart_recovery_cpa FROM widget WHERE WID_id_cli = $id_cli AND WID_formato = 45 OR WID_formato = 46 OR WID_formato = 44 GROUP BY WID_id_cli";
	$q = mysqli_query($conCad, $select);
	$result = mysqli_fetch_assoc($q);
	$info = array_merge($info, $result);

	// select smart search
	$select = "SELECT WID_cpa as smart_search_cpa FROM widget WHERE WID_id_cli = $id_cli AND WID_formato = 7 GROUP BY WID_id_cli";
	$q = mysqli_query($conCad, $select);	
	$result = mysqli_fetch_assoc($q);
	$info = array_merge($info, $result);

	$info['valor_plano'] = 'R$ ' . number_format($info['valor_plano'], 2, ',', '.');

	// mostrar tabela ou n
	$display = $info['smart_recomendation_cpa'] == 0 && $info['smart_search_cpa'] == 0 && $info['smart_recovery_cpa'] == 0 ? 'none': 'block';

?>

<div class="app-content content container-fluid">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-8 col-xs-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="breadcrumb-wrapper col-xs-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="overview">Home</a></li>
							<li class="breadcrumb-item"><a href="configuracoes">Configurações</a></li>
						</ol>
					</div>
				</div>
				<h3 class="content-header-title mb-0">CONFIGURAÇÕES</h3>
			</div>
		</div>

		<div class="row match-height">
			<!-- VÍDEO PIXEL -->
			<div class="col-xl-6 col-md-12 col-sm-12">
				<div class="card" style="">
					<div class="card-body">
						<div class="card-block">
							<h4 class="card-title">PLANO ATUAL</h4>
						</div>
						<div class="card-block pt-0">
							<table class="table" style="display:<?= $display ?>">
								<thead>
									<tr>
										<th><h4><strong>Produto</strong></h4></th>
										<th><h4><strong>Preço</strong></h4></th>
									</tr>
								</thead>
								<tbody>
									<?php if ($info['smart_recomendation_cpa'] > 0) { ?> 
									<tr>
										<td><h4>Smart Recomendation</h4></td>
										<td><h4><?= $info['smart_recomendation_cpa'] ?>%</h4></td>
									</tr>
									<?php } ?>
									
									<?php if ($info['smart_search_cpa'] > 0) { ?> 
									<tr>
										<td><h4>Smart Search</h4></td>
										<td><h4><?= $info['smart_search_cpa'] ?>%</h4></td>
									</tr>
									<?php } ?>

									<?php if ($info['smart_recovery_cpa'] > 0) { ?> 
									<tr>
										<td><h4>Smart Recovery</h4></td>
										<td><h4><?= $info['smart_recovery_cpa'] ?>%</h4></td>
									</tr>
									<?php } ?>
									
								</tbody>
							</table>

							<table class="table mt-2">
								<tbody>
									<tr>
										<td><h4><strong>Total Fixo</strong></h4></td>
										<td><h4><?= $info['valor_plano'] ?></h4></td>
									</tr>
									<tr style="display: <?= $display ?>">
										<td><h4><strong>Total Dinâmico</strong></h4></td>
										<td><h4>5% CPA</h4></td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
