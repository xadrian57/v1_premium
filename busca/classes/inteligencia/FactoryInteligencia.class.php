<?php
namespace roihero\search\inteligencia;

/**
 * Classe auxiliar para a criação das instâncias de Inteligência.
 * 
 * @author moises
 */
class FactoryInteligencia {
   
    /**
     * Construtor privado para que não seja criada intância dessa classe.
     */
    private function __construct() {
    }
    
    public static function getInteligencia($nameInteligencia) {
        $inteligencia = null;
        
        switch ($nameInteligencia) {
            case 'TOP_VIEWS':
                
                $inteligencia = new MaisClicadosInteligencia();
                break;

            case 'SALE_OFF':
                
                $inteligencia = new LiquidacaoInteligencia();
                break;

            // case 'COMP':
                
            //     $inteligencia = new LiquidacaoInteligencia();
            //     break;

            case 'SIM_CAT':
                
                $inteligencia = new SimilarCategoriaInteligencia();
                break;

            case 'SIM_BRAND':
                
                $inteligencia = new SimilarMarcaInteligencia();
                break;
                     
        }
        
        return $inteligencia;
    }
}
?>