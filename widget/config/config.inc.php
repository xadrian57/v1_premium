<?php
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'classes/widget/IWidget.class.php';
include 'classes/widget/AbstractWidget.class.php';
include 'classes/widget/Widget.class.php';

include 'classes/util/Util.class.php';
include 'classes/inteligencia/IInteligencia.class.php';
include 'classes/inteligencia/AbstractInteligencia.class.php';
include 'classes/inteligencia/AbstractInteligenciaComposta.class.php';
include 'classes/inteligencia/FactoryInteligencia.class.php';

require_once 'classes/inteligencia/BaixouDePrecoInteligencia.class.php';
require_once 'classes/inteligencia/CarrinhoComplementarInteligencia.class.php';
require_once 'classes/inteligencia/ColecaoInteligencia.class.php';
require_once 'classes/inteligencia/CompreJuntoInteligencia.class.php';
require_once 'classes/inteligencia/ItensComplementaresInteligencia.class.php';
require_once 'classes/inteligencia/LiquidacaoInteligencia.class.php';
require_once 'classes/inteligencia/MaisClicadosInteligencia.class.php';
require_once 'classes/inteligencia/MaisVendidosCategoriaInteligencia.class.php';
require_once 'classes/inteligencia/MaisVendidosCategoriaManualInteligencia.class.php';
require_once 'classes/inteligencia/MaisVendidosInteligencia.class.php';
require_once 'classes/inteligencia/ManualInteligencia.class.php';
require_once 'classes/inteligencia/MelhoresAvaliadosESimilaresInteligencia.class.php';
require_once 'classes/inteligencia/MelhoresAvaliadosInteligencia.class.php';
require_once 'classes/inteligencia/RemarketingNavegacaoInteligencia.class.php';
require_once 'classes/inteligencia/NovidadesLojaInteligencia.class.php';
require_once 'classes/inteligencia/OverlayDeSaidaInteligencia.class.php';
require_once 'classes/inteligencia/ProdutoSimilarInteligencia.class.php';
require_once 'classes/inteligencia/RemarketingComplementarInteligencia.class.php';
require_once 'classes/inteligencia/RemarketingOnSiteDinamicoInteligencia.class.php';
require_once 'classes/inteligencia/RemarketingOnSiteInteligencia.class.php';
require_once 'classes/inteligencia/RodapeDeProdutosInteligencia.class.php';
require_once 'classes/inteligencia/ProdutosRelacionadosInteligencia.class.php';
require_once 'classes/inteligencia/SmartHomeInteligencia.class.php';
require_once 'classes/inteligencia/CompreJuntoComplementarInteligencia.class.php';
require_once 'classes/inteligencia/PalavraChaveInteligencia.class.php';
require_once 'classes/inteligencia/MaisVendidosMarcaManualInteligencia.class.php';
require_once 'classes/inteligencia/SimilarPorParametrosInteligencia.class.php';
require_once 'classes/inteligencia/NovidadeMarcaManualInteligencia.class.php';
require_once 'classes/inteligencia/LojaLateralInteligencia.class.php';

?>