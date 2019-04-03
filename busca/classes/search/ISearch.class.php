<?php
namespace roihero\search;

/**
 * Interface guia para o uso dos search providos pela Roihero
 *  
 * @author moises
 */
interface ISearch {
    
    function setParametros($arrayRequest);
    
    function executar();
    
}
?>