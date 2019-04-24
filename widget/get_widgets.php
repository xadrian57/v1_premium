<?php
/*
 * Olá amigo desenvolvedor.
 * Caso, você receba a missão de desenvolver uma nova inteligência.
 * Siga os passos abaixo:
 * 
 * - Criar um nova classe [Nome da inteligência]Inteligencia.class.php na pasta classes/inteligencia
 * - Adicionar o require_once dessa nova inteligência no arquivo config/config.inc.php
 * - Na classe FactoryInteligencia, adicione uma nova constant para representar sua nova inteligencia
 * - Ainda na classe Factory, insira a criação da sua inteligência no método getInteligencia
 * - Implementar o método processar() na nova classe de inteligência.
 * - Adicionar a UTM no arquivo 'AbstractInteligencia.class.php'
 * E TESTE EXAUSTIVAMENTE
 * 
 * Mais sobre as inteligências e widgets no arquivo
 * https://docs.google.com/spreadsheets/d/1LMg8rgYQPOPPy1bprVrv7aqYhI4HIe2Y0WBNHZlTzbc/edit?usp=drive_web&ouid=109671985796093627964
 */

include 'config/config.inc.php';

use roihero\widget\Widget;

$widget = new Widget();
$widget->setParametros($_GET);
$widget->executar();
?>