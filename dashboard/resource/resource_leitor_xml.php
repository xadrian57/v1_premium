<?php

//CONEXÃO BD Dados
include '../../bd/conexao_bd_dados.php';

//CONEXÃO BD CADASTRO
include '../../bd/conexao_bd_cadastro.php';

//CONEXÃO BD BUSCA
include '../../bd/conexao_bd_busca.php';

// FONETIZAR
include '../../../pt_metaphone/portuguese_metaphone.php';

mysqli_set_charset($conDados, 'utf8');

header('Content-Type: text/html; charset=utf-8');

//DEFINIR SESSAO
session_name('premium');
session_start();
session_write_close();

//ESCONDER WARNING
error_reporting(0);
ini_set('display_errors', FALSE);


$id = $_SESSION['id'];
//$id = '001';
$url = urldecode(mysqli_escape_string($conCad, $_POST['url']));

$jsonTags1 = $_POST['tags'];
$jsonTags2 = $_POST['tags_secundarias'];

$selectAPIOrXML = "SELECT CONF_api_vtex, CONF_busca_be FROM config WHERE CONF_id_cli = ". $id;
$resultAPIOrXML = mysqli_query($conCad, $selectAPIOrXML);

$arrayAPIOrXML = mysqli_fetch_array($resultAPIOrXML);

if($arrayAPIOrXML['CONF_api_vtex'] == 1)
{
    include 'resource_api_vtex.php';
    echo API_VTEX($id, $url, $conCad, $conDados);
    exit();
}


$jsonTagsConc = [];

foreach ($jsonTags1 as $key => $value)
{
    if(!empty($jsonTags2[$key]))
    {
        $jsonTagsConc[$key] = mysqli_escape_string($conCad, $value).",".mysqli_escape_string($conCad, $jsonTags2[$key]);
    }
    else
    {
        $jsonTagsConc[$key] = mysqli_escape_string($conCad, $value);
    }
}

if(!empty($id))
{
    if(!empty($jsonTags1)) {
        
        // INSERT NO BD
        $update = "UPDATE customXML SET CXML_item = '".$jsonTagsConc['Tag do Produto']."', CXML_id_prod = '".$jsonTagsConc['ID Produto']."', CXML_titulo = '".$jsonTagsConc['Nome Produto']."', CXML_descricao = '".$jsonTagsConc['Descrição do Produto']."', CXML_valor = '".$jsonTagsConc['Preço Normal']."', CXML_valor_promo = '".$jsonTagsConc['Preço Promocional']."', CXML_n_parc = '".$jsonTagsConc['Quantidade de parcelas']."', CXML_parc = '".$jsonTagsConc['Valor das parcelas']."', CXML_link = '".$jsonTagsConc['Link Produto']."', CXML_link_imagem1 = '".$jsonTagsConc['Foto Produto']."', CXML_link_imagem2 = '".$jsonTagsConc['Foto Produto secundária']."', CXML_sku = '".$jsonTagsConc['ID do SKU']."', CXML_categoria = '".$jsonTagsConc['Categoria']."', CXML_brand = '".$jsonTagsConc['Marca do produto']."', CXML_estoque = '".$jsonTagsConc['Disponibilidade em estoque']."', CXML_custom1 = '".$jsonTagsConc['Custom 1']."', CXML_custom2 = '".$jsonTagsConc['Custom 2']."', CXML_custom3 = '".$jsonTagsConc['Custom 3']."', CXML_custom4 = '".$jsonTagsConc['Custom 4']."', CXML_custom5 = '".$jsonTagsConc['Custom 5']."' WHERE CXML_id_cliente = '$id'";
        $resultado = mysqli_query($conCad, $update);
        
        if(mysqli_affected_rows($conCad) < 1)
        {
            $insere = "INSERT INTO customXML(CXML_id_cliente, CXML_item, CXML_id_prod, CXML_titulo, CXML_descricao, CXML_valor, CXML_valor_promo, CXML_n_parc, CXML_parc, CXML_link, CXML_link_imagem1, CXML_link_imagem2, CXML_sku, CXML_categoria, CXML_brand, CXML_estoque, CXML_custom1, CXML_custom2, CXML_custom3, CXML_custom4, CXML_custom5) VALUES ('$id','".$jsonTagsConc['Tag do Produto']."', '".$jsonTagsConc['ID Produto']."', '".$jsonTagsConc['Nome Produto']."', '".$jsonTagsConc['Descrição do Produto']."', '".$jsonTagsConc['Preço Normal']."', '".$jsonTagsConc['Preço Promocional']."', '".$jsonTagsConc['Quantidade de parcelas']."', '".$jsonTagsConc['Valor das parcelas']."', '".$jsonTagsConc['Link Produto']."', '".$jsonTagsConc['Foto Produto']."', '".$jsonTagsConc['Foto Produto secundária']."', '".$jsonTagsConc['ID do SKU']."', '".$jsonTagsConc['Categoria']."', '".$jsonTagsConc['Marca do produto']."', '".$jsonTagsConc['Disponibilidade em estoque']."', '".$jsonTagsConc['Custom 1']."', '".$jsonTagsConc['Custom 2']."', '".$jsonTagsConc['Custom 3']."', '".$jsonTagsConc['Custom 4']."', '".$jsonTagsConc['Custom 5']."')";
            $resultadoInsere = mysqli_query($conCad, $insere);
        }
    }

    $selectCXML = "SELECT
                    CXML_item,
                    CXML_id_prod,
                    CXML_titulo,
                    CXML_descricao,
                    CXML_valor,
                    CXML_valor_promo,   
                    CXML_n_parc,        
                    CXML_parc,          
                    CXML_link,          
                    CXML_link_imagem1,  
                    CXML_link_imagem2,  
                    CXML_sku,           
                    CXML_categoria,
                    CXML_brand,
                    CXML_estoque,
                    CXML_nome_custom1,
                    CXML_custom1,
                    CXML_nome_custom2,
                    CXML_custom2,
                    CXML_nome_custom3,
                    CXML_custom3,
                    CXML_nome_custom4,
                    CXML_custom4,
                    CXML_nome_custom5,
                    CXML_custom5
                FROM
                    customXML
                WHERE
                    CXML_id_cliente = '$id'";
    $resultadoCXML = mysqli_query($conCad, $selectCXML);

    $arrayCXML = mysqli_fetch_array($resultadoCXML);
    
    if(($url =='') || ($url == NULL))
    {
        $queryXML = "SELECT CONF_XML as URL_XML, CONF_num_pag_XML FROM config WHERE CONF_id_cli = '$id'";
        $resultadoXML = mysqli_query($conCad, $queryXML);
        $array = mysqli_fetch_array($resultadoXML);
        $url = $array['URL_XML'];
        $nPage = $array['CONF_num_pag_XML'];
    }
    else
    {
        $queryXML = "SELECT CONF_num_pag_XML FROM config WHERE CONF_id_cli = '$id'";
        $resultadoXML = mysqli_query($conCad, $queryXML);
        $array = mysqli_fetch_array($resultadoXML);
        $nPage = $array['CONF_num_pag_XML'];
    }
    
    $time = time();

    //$idProdAux = '0';

    $posts = [];

    $arrayIdsProd = [];
    $arrayCustom = [];
    $arrayEstoqueProd = [];

    $buscaBackEnd = $arrayAPIOrXML['CONF_busca_be'];

    $selectPlat = "SELECT CLI_id_plataforma FROM cliente WHERE CLI_id = ". $id;
    $resultPlat = mysqli_query($conCad, $selectPlat);

    $arrayPlat = mysqli_fetch_array($resultPlat);
    
    for($page = 1; $page <= $nPage; $page++)
    {
        if($nPage > 1)
        {
            if($arrayPlat['CLI_id_plataforma'] == 19)
            {
                $stringPage = "&pag=";
            }
            else
            {
                $stringPage = "?page=";
            }

            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $url.''.$stringPage.''.$page);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $conteudo = curl_exec ($ch);
        }
        else
        {
            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $conteudo = curl_exec ($ch);            
        }

        $len = strlen($conteudo);
            
        if (!($len < 18 || strcmp(substr($conteudo,0,2),"\x1f\x8b")))
        {
            $conteudo = gzdecode($conteudo);
        }
        
        curl_close($ch);
    
        $xml = new XMLReader();
        $xml->xml($conteudo);
        
        while ($xml->read())
        {
            switch ($xml->nodeType)
            {
                case (XMLReader::ELEMENT):
                    if ($xml->localName == $arrayCXML['CXML_item'])
                    {
                        $node = $xml->expand();
                        $dom = new DomDocument();
                        $n = $dom->importNode($node,true);
                        $dom->appendChild($n);
                        $item = simplexml_import_dom($n);
                        
                        $titulo = customXML($arrayCXML['CXML_titulo'], $item);

                        $titulo = limpaTitulo($titulo);

                        $fonetizado = fonetizar($titulo);

                        $descricao = customXML($arrayCXML['CXML_descricao'], $item);                    
                        
                        // Id do produto
                        $idprod = customXML($arrayCXML['CXML_id_prod'], $item);

                        if($id == 1165)
                        {   
                            $posId = strpos($idprod, '-');
                            if($posId > 0)
                                $idprod = substr($idprod, 0, $pos);
                        }
                        
                        if(empty($idprod)) {
                            $idprod = customXML('g:item_group_id', $item);
                        }
                        
                        $link = limpaLink(customXML($arrayCXML['CXML_link'], $item));
                        
                        $sku = customXML($arrayCXML['CXML_sku'], $item);
                        
                        $price = limpaPreco(customXML($arrayCXML['CXML_valor'], $item));
                        
                        $sale_price = limpaPreco(customXML($arrayCXML['CXML_valor_promo'], $item));
                        
                        $availability = customXML($arrayCXML['CXML_estoque'], $item);
                        
                        $type = ajustaCategoria(customXML($arrayCXML['CXML_categoria'], $item));
                        
                        $image_link = customXML($arrayCXML['CXML_link_imagem1'], $item);

                        $image_link = ajusteImagemCliente($image_link, $id);

                        $brand = customXML($arrayCXML['CXML_brand'], $item);

                        $custom1 = customXML($arrayCXML['CXML_custom1'], $item);
                        $custom2 = customXML($arrayCXML['CXML_custom2'], $item);
                        $custom3 = customXML($arrayCXML['CXML_custom3'], $item);
                        $custom4 = customXML($arrayCXML['CXML_custom4'], $item);
                        $custom5 = customXML($arrayCXML['CXML_custom5'], $item);
                        
                        $months = customXML($arrayCXML['CXML_n_parc'], $item);
                        $amount = limpaPreco(customXML($arrayCXML['CXML_parc'], $item));
                        
                        $SALE_PRICE = stringToFloat($sale_price);
                        $PRICE = stringToFloat($price);
                        $AMOUNT = stringToFloat($amount);
                        
                        $desconto = calculaDesconto($PRICE, $SALE_PRICE);
                        
                        $availability = geraEstoque($availability, $id);

                        if($id == 1743)
                        {
                            if($custom1 == 'top' || $custom1 == 'bottom')
                            {
                                $availability = 0;
                            }
                        }
                        
                        if($idprod != "" && $idprod != null)
                        {
                            if(!$checkCreateXML)
                            {
                                createXML($id, $conDados);
                                $checkCreateXML = true;
                            }

                            $availability = verificaProd($arrayIdsProd, $arrayEstoqueProd, $idprod, $availability);
                            
                            $arrayIdsProd[] = $idprod;
                            $arrayEstoqueProd[] = $availability;
                            if($id == 116)
                            {
                                $arrayCustom[$idprod][] = $custom5;
                            }
                            else
                            {
                                $arrayCustom[$idprod][0] = $custom5;
                            }

                            $update ="UPDATE XML_".$id." SET XML_time='$time', XML_titulo='" . htmlspecialchars($titulo) . "', XML_descricao = '$descricao', XML_titulo_upper=UPPER('" .  $titulo . "'), XML_sku='$sku', XML_price = '$PRICE', XML_sale_price = '$SALE_PRICE', XML_desconto = '$desconto', XML_availability = '$availability', XML_link = '$link',XML_type ='" . htmlspecialchars($type) . "',XML_type_upper=UPPER('" .  $type . "'),  XML_image_link = '$image_link', XML_vparcela = '$AMOUNT', XML_nparcelas = '$months', XML_brand = '$brand', XML_custom1 = '$custom1', XML_custom2 = '$custom2', XML_custom3 = '$custom3', XML_custom4 = '$custom4', XML_custom5 = '$custom5' WHERE XML_id = '$idprod'";
                            $resultado = mysqli_query($conDados, $update);
                            
                            if(mysqli_affected_rows($conDados) < 1)
                            {
                                $insere=("INSERT INTO XML_".$id." (XML_descricao, XML_time, XML_time_insert, XML_titulo, XML_titulo_upper, XML_id, XML_sku, XML_price, XML_sale_price, XML_desconto, XML_availability, XML_link, XML_type, XML_type_upper, XML_image_link, XML_vparcela, XML_nparcelas, XML_brand, XML_custom1, XML_custom2, XML_custom3, XML_custom4, XML_custom5) VALUES ('$descricao', '$time', '$time', '" . htmlspecialchars($titulo) . "',UPPER('" .  $titulo . "'), '$idprod', '$sku', '$PRICE', '$SALE_PRICE','$desconto', '$availability', '$link','" . htmlspecialchars($type) . "',UPPER('" .  $type . "'),'$image_link', '$AMOUNT', '$months', '$brand', '$custom1', '$custom2', '$custom3', '$custom4', '$custom5')");
                                $resultadoInsere = mysqli_query($conDados, $insere);

                                if($buscaBackEnd)
                                {  
                                    $insereBusca=("INSERT INTO BUSCA_".$id." (titulo, titulo_fonetico, id, custom_2, custom_1) VALUES (UPPER('" .  $titulo . "'), '$fonetizado', '$idprod', '$custom4', '". implode(',', $arrayCustom[$idprod]) ."')");
                                    $resultadoInsereBusca = mysqli_query($conBusca, $insereBusca);
                                }
                            }
                            else
                            {
                                if($buscaBackEnd)
                                {  
                                    $updateBusca ="UPDATE BUSCA_".$id." SET titulo = UPPER('" .  $titulo . "'), titulo_fonetico = '$fonetizado', custom_1 = '". implode(',', $arrayCustom[$idprod]) ."', custom_2 = '$custom4' WHERE id = '$idprod'";
                                    $resultadoBusca = mysqli_query($conBusca, $updateBusca);

                                    if(mysqli_affected_rows($conBusca) < 1)
                                    {
                                        $insereBusca=("INSERT INTO BUSCA_".$id." (titulo, titulo_fonetico, id, custom_2, custom_1) VALUES (UPPER('" .  $titulo . "'), '$fonetizado', '$idprod', '$custom4', '". implode(',', $arrayCustom[$idprod]) ."')");
                                        $resultadoInsereBusca = mysqli_query($conBusca, $insereBusca);
                                    }
                                }
                            }

                            $select = "SELECT XML_click_7 FROM XML_".$id." WHERE XML_id = '$idprod'";
                            $querySelect = mysqli_query($conDados, $select);
                            
                            $arraySelect = mysqli_fetch_array($querySelect);

                            $posJSON = atualizaProdJSON($posts, $idprod);

                            if($posJSON != false)
                            {
                                $posts[$posJSON] = array('id'=> strval($idprod), 'sku'=> $sku, 'title'=> urlencode($titulo), 'in_stock'=> $availability, 
                                'price'=> $PRICE, 'sale_price'=> $SALE_PRICE, 'link'=> urlencode($link), 'link_image'=> urlencode($image_link), 
                                'type'=> urlencode($type), 'amount'=> $AMOUNT, 'months'=> $months, 'venda' => $arraySelect['XML_click_7'],
                                'desconto' => $desconto, 'productReference' => $custom5);
                            }
                            else
                            {
                                $posts[] = array('id'=> strval($idprod), 'sku'=> $sku, 'title'=> urlencode($titulo), 'in_stock'=> $availability, 
                                'price'=> $PRICE, 'sale_price'=> $SALE_PRICE, 'link'=> urlencode($link), 'link_image'=> urlencode($image_link), 
                                'type'=> urlencode($type), 'amount'=> $AMOUNT, 'months'=> $months, 'venda' => $arraySelect['XML_click_7'],
                                'desconto' => $desconto, 'productReference' => $custom5);
                            }

                            if($buscaBackEnd)
                            {                                
                                $updateBusca ="UPDATE BUSCA_".$id." SET click = '". $arraySelect['XML_click_7'] ."' WHERE id = '$idprod'";
                                $resultadoBusca = mysqli_query($conBusca, $updateBusca);
                            }
                        }
                        else
                        {
                            echo '3';
                            //exit();
                        }
                    }
            }
        } //fim do while
    }
    
    
    if($insere OR $update)
    {
        $atualiza = "UPDATE config SET CONF_XML = '$url', CONF_at_xml = CURRENT_TIMESTAMP() WHERE CONF_id_cli = '$id'";
        $insert3 = mysqli_query($conCad, $atualiza);
        
        $updatestats ="UPDATE XML_".$id." SET XML_availability = 0 WHERE XML_time != '$time' OR XML_time IS NULL";
        $resultadostats = mysqli_query($conDados, $updatestats);

        notificaXML($id, $conCad);

        geraJSON($id, $posts);
        
        echo '1';
        exit();
    }
    else
    {
        echo '0';
        exit();
    }
}
else
{
    echo '2';
    exit();
}



// FUNÇÕES AUXILIARES

function createXML($id, $conDados)
{
    $criaXML = ("CREATE TABLE IF NOT EXISTS XML_".$id." (
            XML_titulo VARCHAR(256),
            XML_titulo_upper varchar(256),
            XML_id VARCHAR(256) NOT NULL,
            XML_sku VARCHAR(256) DEFAULT NULL,
            XML_price DECIMAL(16,2) NOT NULL,
            XML_sale_price DECIMAL(16,2) NOT NULL,
            XML_desconto INT(3) NOT NULL,
            XML_availability boolean NOT NULL,
            XML_link VARCHAR(1024),
            XML_type VARCHAR(256),
            XML_type_upper varchar(256),
            XML_image_link VARCHAR(1024),
            XML_image_link2 VARCHAR(1024),
            XML_descricao VARCHAR(1024),
            XML_brand VARCHAR(256),
            XML_nparcelas int (3) DEFAULT '0',
            XML_vparcela DECIMAL(16,2) DEFAULT '0',
            XML_time int(11) DEFAULT NULL,
            XML_time_insert int(11) DEFAULT NULL,
            XML_custom_nome1 VARCHAR(256),
            XML_custom1 VARCHAR(256),
            XML_custom_nome2 VARCHAR(256),
            XML_custom2 VARCHAR(256),
            XML_custom_nome3 VARCHAR(256),
            XML_custom3 VARCHAR(256),
            XML_custom_nome4 VARCHAR(256),
            XML_custom4 VARCHAR(256),
            XML_custom_nome5 VARCHAR(256),
            XML_custom5 VARCHAR(256),
            xml_navegou_complementar VARCHAR(1000),
            xml_carrinho_complementar VARCHAR(1000),
            xml_compra_complementar VARCHAR(1000),
            XML_click_7 int(16) DEFAULT 0,
            XML_click_3 int(16) DEFAULT 0,
            XML_click_1 int(16) DEFAULT 0,
            XML_venda_7 int(16) DEFAULT 0,
            XML_venda_3 int(16) DEFAULT 0,
            XML_venda_1 int(16) DEFAULT 0,
            FULLTEXT(XML_link,XML_type,XML_titulo,XML_sku),
            INDEX(XML_titulo_upper, XML_type_upper, XML_click_7, XML_click_3, XML_click_1, XML_availability, XML_venda_7, XML_venda_3, XML_venda_1, XML_time_insert),
            PRIMARY KEY (XML_id)
            )
        ");
    mysqli_query($conDados, $criaXML);
    
    
    $criaRWID = ("CREATE TABLE IF NOT EXISTS RWID_".$id." (
            RWID_id int(10) NOT NULL  AUTO_INCREMENT,
            RWID_id_wid VARCHAR(255) NOT NULL,
            RWID_evento int(1) NOT NULL,
            RWID_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            RWID_id_prod VARCHAR(1000) NOT NULL,
            RWID_quant VARCHAR(255) NOT NULL,
            RWID_valor VARCHAR(255) NOT NULL,
            PRIMARY KEY (RWID_id)
            )
        ");
    mysqli_query($conDados, $criaRWID);
    
    $criaVIEW = ("CREATE TABLE IF NOT EXISTS VIEW_".$id." (
            VIEW_id int(10) NOT NULL AUTO_INCREMENT,
            VIEW_id_wid VARCHAR(255) NOT NULL,
            VIEW_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (VIEW_id)
            )
        ");
    mysqli_query($conDados, $criaVIEW);
    
    $criaRGER = ("CREATE TABLE IF NOT EXISTS RGER_".$id." (
            RGER_id int(10) NOT NULL  AUTO_INCREMENT,
            RGER_evento int(1) NOT NULL,
            RGER_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            RGER_id_prod VARCHAR(1000) NOT NULL,
            RGER_quant VARCHAR(255) NOT NULL,
            RGER_valor VARCHAR(255) NOT NULL,
            FULLTEXT(RGER_id_prod),
            PRIMARY KEY(RGER_id)
            )
        ");
    mysqli_query($conDados, $criaRGER);
}

function notificaXML($id, $conCad){
    $qNotXMLCadastrado = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status) VALUES ($id, 'XML cadastrado com sucesso', 'Seu XML foi cadastrado com sucesso. Agora você já pode aproveitar todas as vantagens da ROI Hero no seu site!', CURRENT_DATE(), 1)";
    $resultNotXMLCadastrado = mysqli_query($conCad, $qNotXMLCadastrado);
}

function customXML($custom, $item)
{
    $resp = "";
    
    if($custom != "" && $custom != null)
    {
        if(count(explode(',', $custom)) > 1)
        {
            $cArray = explode(',', $custom);
            
            if(strpos($cArray[0], ':') == 1 && strpos($cArray[1], ':') == 1)
            {
                $customArray1 = explode(':', $cArray[0]);
                $customArray2 = explode(':', $cArray[1]);
                
                $resp = $item->children($customArray1[0], TRUE)->{$customArray1[1]}->children($customArray2[0], TRUE)->{$customArray2[1]};
            }
            else if(strpos($cArray[0], ':') == 1 && strpos($cArray[1], ':') != 1)
            {
                $customArray1 = explode(':', $cArray[0]);
                
                $resp = $item->children($customArray1[0], TRUE)->{$customArray1[1]}->$cArray[1];
            }
            else if(strpos($cArray[0], ':') != 1 && strpos($cArray[1], ':') == 1)
            {
                $customArray2 = explode(':', $cArray[1]);
                
                $resp = $item->{$cArray[0]}->children($customArray2[0], TRUE)->{$customArray2[1]};
            }
            else
            {
                $resp = $item->{$cArray[0]}->{$cArray[1]};
            }
        }
        else
        {
            if(strpos($custom, ':') == 1)
            {
                $customArray = explode(':', $custom);
                
                $resp = $item->children($customArray[0], TRUE)->{$customArray[1]};
            }
            else
            {
                $resp = $item -> $custom;
            }
        }
    }
    
    return trim($resp);
}

function limpaTitulo($titulo)
{
    $titulo = str_replace('\'',' ',$titulo);

    return $titulo;
}

function limpaLink($link)
{
    $pos = strpos($link, '?');
    
    if($pos !== false)
    {
        $link = substr($link, 0, $pos);
    }
    
    return $link;
}

function limpaPreco($price)
{
    $price = str_replace("R$", '', $price);
    $price = str_replace("BRL", '', $price);
    
    $price  = trim($price);
    
    if(strlen($price) > 3) {
        if(substr($price, -3, 1) == ',') {
            
            // Removendo os pontos do preço
            $price = str_replace('.', '', $price);
            // Substituindo a virgula por ponto
            $price = str_replace(',', '.', $price);
            
        } else if(substr($price, -3, 1) == '.') {
            $price = str_replace(",", '', $price);
        }
    }
    
    return $price;
}

function ajustaCategoria($type)
{
    $type = str_replace(" > ", " - ", $type);
    $type = str_replace(">", " - ", $type);
    
    return $type;
}

function fonetizar($titulo)
{
    $arrayPalavras = explode(' ', $titulo);

    $arrayPalavrasAux = [];
    
    for($i=0; $i < count($arrayPalavras); $i++)
    {
        $arrayPalavrasAux[] = portuguese_metaphone($arrayPalavras[$i]);
    }
    
    return implode($arrayPalavrasAux, ' ');
}

function ajusteImagemCliente($image_link, $id)
{
    if($id == 598)
    {
        $array = explode('/', $image_link);

        $array[6] = '180_'.$array[6];

        return implode($array, '/');
    }
    else
    {
        return $image_link;
    }
}

function stringToFloat($price)
{
    $pos1 = strpos($price, '.');
    $pos2 = strpos($price, ',');
    
    if(($pos1 !== FALSE && $pos2 !== FALSE)||($pos1 === FALSE && $pos2 !== FALSE))
    {
        
        $explodePrice = explode('.', $price);
        $price = implode('', $explodePrice);
        
        $arrayprice = explode(',',$price);
        $price_string = $arrayprice[0].'.'.$arrayprice[1];
    }
    else
    {
        $price_string = $price_string = $price;
    }
    
    $PRICE = floatval ($price_string);
    
    return $PRICE;
}

function calculaDesconto($PRICE, $SALE_PRICE)
{
    if($PRICE > 0)
    {
        $desconto = 100 - (($SALE_PRICE * 100) / $PRICE);
        $desconto = round($desconto,0);
    }
    else
        $desconto = 0;
        
        return $desconto;
}

function geraEstoque($availability, $idCli)
{
    //if(strcasecmp($availability,'in stock') != 0)
    //    $availability = 0;
    //    else
    //        $availability = 1;          
           

    if((strtolower(trim($availability)) != 'em estoque') && (strtolower(trim($availability)) != 'in stock') && (trim($availability) != '1') && (trim($availability) != 'disponível') && ($idCli != '756') && ($idCli != '859'))
    {
        $availability = 0;
    }
    else
    {
        $availability = 1;
    }

    return $availability;
}

function verificaProd($arrayIdsProd, $arrayEstoqueProd, $idProd, $estoqueProd)
{
    for($i=0; $i < count($arrayIdsProd); $i++)
    {
        if($arrayIdsProd[$i] == $idProd)
        {
            if($arrayEstoqueProd[$i] == 1)
            {
                return 1;
            }
        }
    }

    return $estoqueProd;
}

function atualizaProdJSON($posts, $idprod)
{
    for($i=0; $i < count($posts); $i++)
    {
        if($posts[$i]['id'] == $idprod)
        {
            return $i;
        }
    }

    return false;
}

function geraJSON($id, $posts)
{
    $json_data = json_encode($posts);
    file_put_contents('../../JSON/JSON_'.sha1($id).'.json', $json_data);
}

?>