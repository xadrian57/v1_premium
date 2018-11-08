<?php
namespace roihero\widget;

/**
 * Interface guia para o uso dos widget providos pela Roihero
 *  
 * @author tiago
 */
interface IWidget {
    
    function setParametros($arrayRequest);
    
    function executar();
    
}
?>