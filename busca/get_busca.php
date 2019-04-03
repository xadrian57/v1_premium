<?php
include 'config/config.inc.php';

use roihero\search\Search;

$search = new Search();
$search->setParametros($_GET);
$search->executar();
?>