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

include 'classes/inteligencia/IInteligencia.class.php';
include 'classes/inteligencia/AbstractInteligencia.class.php';
//include 'classes/inteligencia/AbstractInteligenciaComposta.class.php';
include 'classes/inteligencia/FactoryInteligencia.class.php';

//include 'classes/inteligencia/CarrinhoComplementarInteligencia.class.php';
include 'classes/inteligencia/LiquidacaoInteligencia.class.php';
include 'classes/inteligencia/MaisClicadosInteligencia.class.php';
include 'classes/inteligencia/SimilarCategoriaInteligencia.class.php';
include 'classes/inteligencia/SimilarMarcaInteligencia.class.php';
?>