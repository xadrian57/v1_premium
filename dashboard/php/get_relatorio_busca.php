<?php

include '/home/roihero/public_html/bd/conexao_bd_cadastro.php';
include '/home/roihero/public_html/bd/conexao_bd_dados.php';

require 'spreadsheets/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$id = mysqli_real_escape_string($conCad, $_GET['id']); // id cliente

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
$index = 2;

$sheet->setCellValue('A1', 'TERMO');
$sheet->setCellValue('B1', 'VEZES');


$sheet->getRowDimension('1')->setRowHeight(18); // seta altura da linha


$sheet->getStyle('A1')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A1')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A1')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('A1')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$sheet->getStyle('B1')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('B1')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('B1')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$sheet->getStyle('B1')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('0f9d58');
$sheet->getStyle('A1')->getFont()->getColor()->setARGB('ffffff');
$sheet->getStyle('A1')->getFont()->setSize(15);
$sheet->getStyle('A1')->getFont()->setBold(600);

$sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('B1')->getFill()->getStartColor()->setARGB('0f9d58');
$sheet->getStyle('B1')->getFont()->getColor()->setARGB('ffffff');
$sheet->getStyle('B1')->getFont()->setSize(15);
$sheet->getStyle('B1')->getFont()->setBold(600);

$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(30);

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


			$sheet->setCellValue('A'.$index, urldecode($result['termo']));
			$sheet->setCellValue('B'.$index, $result['vezes']);

			$sheet->getStyle('B'.$index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$index++;
		}
		
	}
}

$writer = new Xlsx($spreadsheet);
$arquivo = 'relatorio_busca_'.$cliente['nome'].'_'.date("d-m-Y").'.xlsx';
$writer->save($arquivo);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($arquivo));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($arquivo));
readfile($arquivo);
unlink($arquivo);
exit;

?>