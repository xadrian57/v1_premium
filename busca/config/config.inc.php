<?php
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

//FONETIZAR
include '../../pt_metaphone/portuguese_metaphone.php';

include 'classes/search/ISearch.class.php';
include 'classes/search/AbstractSearch.class.php';
include 'classes/search/Search.class.php';

include 'classes/util/Util.class.php';
?>