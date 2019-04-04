<?php
namespace roihero\search\inteligencia;

interface IInteligencia {
    
    /**
     * Este é o método principal do processamento da Inteligência.
     * Cada inteligência implementada, possuirá um comportamento único, só dela.
     */
    function processar();
}
?>