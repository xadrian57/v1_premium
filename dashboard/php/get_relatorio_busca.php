<?php
include '../../bd/conexao_bd_dados.php';
include '../../bd/conexao_bd_cadastro.php';

$id = mysqli_escape_string($conCad, $_GET['id']);

$select = 'SELECT CLI_nome FROM cliente WHERE CLI_id = '.$id;
$query = mysqli_query($conCad, $select);
$result = mysqli_fetch_assoc($query);

$cliente = array('nome' => $result['CLI_nome'], 'id' => $id);

$select = '
	SELECT 
		count(id) as vezes,
		tx_busca as termo
	FROM 
		`BUSCA_'.$cliente['id'].'`
	WHERE
		dh_data BETWEEN SUBDATE(CURRENT_DATE,30) AND CURRENT_DATE
	GROUP BY termo
	ORDER BY vezes DESC
	';
$data = [];
$query = mysqli_query($conDados, $select);
if (mysqli_error($conDados)) {
    // echo 'O cliente '.$cliente['nome'].'('.$cliente['id'].') não possui nenhuma instância na tabela.';
}
else {
	$r = [];
	while ($result = mysqli_fetch_assoc($query)) {
		if (mysqli_num_rows($query) == 0) {
			// echo 'O cliente '.$cliente['nome'].'('.$cliente['id'].') não possui nenhum termo que foi pesquisados nos últimos 30 dias.';
		} else {
			//echo 'O cliente '.$cliente['nome'].'('.$cliente['id'].') possui termos que foram pesquisados nos últimos 30 dias,';
			
			array_push($data, 
				array(
					'termo' => $result['termo'],
					'vezes' => $result['vezes']
				)
			);
		}
		
	}
}

$arquivo = 'relatorio_busca_'.$cliente['nome'].'_'.date("d-m-Y").'.xls';

header("Content-Disposition: attachment; filename=\"".$arquivo."\"");
header("Content-Type: application/vnd.ms-excel;");
header("Pragma: no-cache");
header("Charset: utf-8");
header("Expires: 0");

// Criamos uma tabela HTML com o formato da planilha
$html = '';
$html .= '<table>';
$html .= '<tr>';
$html .= '<td colspan="2">RELATÓRIO DE TERMOS DA BUSCA</tr>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td><b>TERMO</b></td>';
$html .= '<td><b>VEZES</b></td>';
$html .= '</tr>';

foreach ($data as $item) {
	$html .= '<tr>';
	$html .= '<td>'.urldecode($item['termo']).'</td>';
	$html .= '<td>'.$item['vezes'].'</td>';
	$html .= '</tr>';
}

$html .= '</table>';

// Envia o conteúdo do arquivo
echo $html;
exit;

?>