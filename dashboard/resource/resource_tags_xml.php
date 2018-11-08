<?php

	header('Content-Type: text/html; charset=utf-8');
    
	//ESCONDER WARNING
    error_reporting(0);
    ini_set('display_errors', FALSE);

	$url = urldecode($_POST['url']);

	$ch = curl_init();
    $timeout = 0;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $conteudo = curl_exec ($ch);

    $len = strlen($conteudo);

    if (!($len < 18 || strcmp(substr($conteudo,0,2),"\x1f\x8b"))) 
    {
        $conteudo = gzdecode($conteudo);
    }

    curl_close($ch);

	$doc = new DOMDocument();
	$doc->loadXML($conteudo);

	$xpath = new DOMXpath( $doc );
	$nodes = $xpath->query( '//*' );

	$nodeNames = array();
	foreach( $nodes as $node )
	{
	    $nodeNames[] = $node->nodeName;
	}

	if(count($nodeNames) > 0)
	{
		echo json_encode( array_unique($nodeNames), true );
	}
	else
	{
		echo '0';
	}

?>