<?php

	header('Content-Type: application/json; charset=utf-8');
	include "../../bd/conexao_bd_dados.php";
	include "../../bd/conexao_bd_cadastro.php";

	//pega o id do cliente
	$idCli = mysqli_real_escape_string($conCad, $_POST['id']);
	$enable =  $_POST['enable'];
	
	if($enable) {
	    
	    //pega o site do cliente
	    $qSelectSiteCliente = "SELECT CLI_site FROM cliente WHERE CLI_id = $idCli";
	    $resultSelectSiteCliente =  mysqli_query($conCad, $qSelectSiteCliente);
	    $site = mysqli_fetch_array($resultSelectSiteCliente)['CLI_site'];
	    
	    $ch = curl_init();
	    $timeout = 0;
	    curl_setopt($ch, CURLOPT_URL, $site);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	    $conteudo = curl_exec ($ch);
	    
	    $varTrustvox = ".push(['_storeId', '";
	    $pos1 = strpos($conteudo, $varTrustvox);
	    
	    if($pos1) {
	        
	        $pos2 = strpos(substr($conteudo, $pos1), "']);");
	        
	        if($pos2) {
	            $idTrustvox = substr($conteudo, $pos1 + strlen($varTrustvox), $pos2 - strlen($varTrustvox));
	            
	            $qUpdateCliente = "UPDATE cliente SET CLI_id_tv = '$idTrustvox' WHERE CLI_id = $idCli";
	            $resultUpdateCliente = mysqli_query($conCad, $qUpdateCliente);
	            
	            //SELECT CLI
	            createTableTv($idCli, $conDados);
	            
	            // Recuperando os produtos do cliente
	            $sqlProdutos = 'SELECT XML_id FROM XML_' . $cli;
	            
	            $resultProds = mysqli_query( 
                                        $conDados,
                                        $sqlProdutos );
                
                // Se não trouxe resultado, vai para a próxima iteração
                if (mysqli_num_rows ( 
                                    $resultProds ) > 0) {
                
                    while ( $row = mysqli_fetch_array ( 
                                                        $resultProds ) ) {
                        // Paginação de 0 até 19
                        for($i = 1; $i <= 20; $i++)
                        {
                            //AVALIAÇÕES DOS PRODUTOS
                            $ch = curl_init();
                            $timeout = 0;
                            curl_setopt($ch, CURLOPT_URL, 'https://trustvox.com.br/widget/opinions?code=' . $row ['XML_id'] . '&store_id='.$idTrustvox.'&page='.$i);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.trustvox-v2+json'));
                            $json_av_prods = curl_exec ($ch);
                            curl_close($ch);
                            
                            $json_av_prods = json_decode($json_av_prods, true);
                            
                            if(!empty($json_av_prods['items']))
                            {
                                foreach($json_av_prods['items'] as $key => $value)
                                {
                                    //UPDATE/INSERT
                                    $user = trim($json_av_prods['items'][$key]['user']['name']);
                                    $opinion = 'null';
                                    
                                    if(array_key_exists('opinion', $json_av_prods['items'][$key])) {
                                        $opinion = trim($json_av_prods['items'][$key]['opinion']);
                                    }
                                    
                                    $update = "UPDATE tv_".$idCli."
                                               SET TV_opinion = '".$opinion."',
                                               TV_rate = ".$json_av_prods['items'][$key]['rate'].",
                                               TV_created_at = STR_TO_DATE('".$json_av_prods['items'][$key]['created_at']."', '%d/%m/%Y'),
                                               TV_user = '".$user."',
                                               dh_update = NOW()
                                               WHERE TV_id = '".$json_av_prods['items'][$key]['id'] . "'";
                                    $resultado = mysqli_query($conDados, $update);
                                    
                                    if(mysqli_affected_rows($conDados) < 1)
                                    {
                                        $insere = "INSERT INTO tv_".$idCli."(TV_opinion, TV_rate, TV_created_at, TV_user, TV_id, dh_insert)
                                       VALUES ('".$opinion."',
                                       '".$json_av_prods['items'][$key]['rate']."',
                                       STR_TO_DATE('".$json_av_prods['items'][$key]['created_at']."', '%d/%m/%Y'),
                                       '".$user."',
                                       '".$json_av_prods['items'][$key]['id']."',
                                       NOW())";
                                        $resultadoInsere = mysqli_query($conDados, $insere);
                                    }
                                }
                            }
                            else
                            {
                                //ARRAY VÁZIO
                                break;
                            }
                        }
        	        }
    	        }
                
                //AVALIAÇÕES DA LOJA
                $ch = curl_init();
                $timeout = 0;
                curl_setopt($ch, CURLOPT_URL, 'trustvox.com.br/store_reviews/store_reviews?store_id='.$idTrustvox);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.trustvox-v1+json'));
                $json_av_loja = curl_exec ($ch);
                curl_close($ch);
                
                $json_av_loja = json_decode($json_av_loja, true);
                
                if(!empty($json_av_loja['store_reviews']))
                {
                    $review = 1;
                    
                    foreach($json_av_loja['store_reviews'] as $key => $value)
                    {
                        $opinion = 'null';
                        
                        if(array_key_exists('text', $json_av_loja['store_reviews'][$key])) {
                            $opinion = trim($json_av_loja['store_reviews'][$key]['text']);
                        }
                        
                        //UPDATE/INSERT
                        $update = "UPDATE tv_".$idCli."
                                   SET TV_opinion = '".$opinion."',
                                   TV_rate = '".$json_av_loja['store_reviews'][$key]['rate']."',
                                   TV_created_at = DATE('".$json_av_loja['store_reviews'][$key]['date']."'),
                                   TV_user = '".trim($json_av_loja['store_reviews'][$key]['author'])."',
                                   dh_update = NOW()
                                   WHERE TV_id = 'rh_store_".$review . "'";
                        $resultado = mysqli_query($conDados, $update);
                        
                        if(mysqli_affected_rows($conDados) < 1)
                        {
                            $insere = "INSERT INTO tv_".$idCli."(TV_opinion, TV_rate, TV_created_at, TV_user, TV_id, dh_insert)
                                       VALUES ('".$opinion."',
                                               '".$json_av_loja['store_reviews'][$key]['rate']."',
                                               DATE('" . $json_av_loja['store_reviews'][$key]['date']."'),
                                               '".$json_av_loja['store_reviews'][$key]['author']."',
                                               'rh_store_".$review."',
                                               NOW())";
                            $resultadoInsere = mysqli_query($conDados, $insere);
                        }
                        
                        // Só salva 3 reviews da loja
                        if($review == 3)
                        {
                            break;
                        }
                        
                        $review++;
                    }
                }
                
                $sql = 'UPDATE config SET CONF_tv = 1 WHERE CONF_id_cli = ' . $idCli;
                mysqli_query($conCad, $sql);
	            
                showSuccess($idTrustvox,
                           'Parabéns, agora a Trustvox está integrada em suas recomendações da Roi Hero! Dê uma olhada nos temas que temos integrados com a Trustvox, além da inteligência de \"Melhor Recomendados\".');
	            
	        } else {
	            showError();
	        }
	    } else {
	        showError();
	    }
	    
	} else {
	    $sql = 'UPDATE config SET CONF_tv = 0 WHERE CONF_id_cli = ' . $idCli;
	    mysqli_query($conCad, $sql);
	    
	    $sql = 'DROP TABLE tv_' . $idCli;
	    mysqli_query($conDados, $sql);
	    
	    showSuccess(null, 'Trustvox DESATIVADA com as suas recomendações da Roi Hero!');
	}
	
	function createTableTv($id, $conn) {
	    $sql = 'CREATE TABLE IF NOT EXISTS `tv_' . $id . '` (
                 `id` int(11) auto_increment,
                 `TV_opinion` text,
                 `TV_rate` int(11) DEFAULT NULL,
                 `TV_created_at` date DEFAULT NULL,
                 `TV_user` varchar(100) NOT NULL,
                 `TV_id` varchar(64),
                 `dh_insert` datetime,
                 `dh_update` datetime,
                 primary key (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
	    
	    mysqli_query($conn, $sql);
	}

	function showError() {
	    echo '{"status":0,"errorMsg":"',
	    'Não foi possível achar a Trustvox em seu sistema, verifique se você realmente possui a ferramenta instalada em sua loja. Caso o problema persista, entre em contato com nosso suporte.',
	    '"}';
	}
	
	function showSuccess($idTrustvox, $msg) {
	    echo '{"status":1,"successMsg":"', $msg, '","idTrustvox":', ($idTrustvox ? $idTrustvox : '""'), '}';
	}
?>