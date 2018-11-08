<?php
namespace roihero\widget\inteligencia;

/**
 * Classe auxiliar para a criação das instâncias de Inteligência.
 * 
 * @author tiago
 */
class FactoryInteligencia {
    
    const MAIS_CLICADOS                               = 1;
    const MAIS_VENDIDOS                               = 2;
    const MAIS_VENDIDOS_CATEGORIA                     = 3;
    const REMARKETING_ON_SITE                         = 4;
    const PRODUTO_SIMILAR                             = 5;
    const LIQUIDACAO                                  = 6;
    const COLECAO                                     = 7;
    const COMPRE_JUNTO                                = 8;
    const OFERTA_LIMITADA_MANUAL                      = 9;
    const REMARKETING_ON_SITE_DINAMICO                = 10;
    const CARRINHO_COMPLEMENTAR                       = 11;
    const ITENS_COMPLEMENTAR                          = 12;
    const OVERLAY_DE_SAIDA                            = 13;
    const BAIXOU_DE_PRECO                             = 14;
    const NOVIDADES_DA_LOJA                           = 15;
    const GEO_LOCALIZACAO                             = 16;
    const TOP_TRENDS_GOOGLE_FACEBOOK                  = 17;
    const MELHORES_AVALIADOS_E_SIMILARES_PRODUTO      = 18;
    const VITRINE_DESCOBERTAS                         = 19;
    const MELHORES_AVALIADOS                          = 20;
    const RECEM_AVALIADOS                             = 21;
    const REMARKETING_COMPLEMENTAR                    = 23;
    const MAIS_VENDIDOS_CATEGORIA_MANUAL              = 24;
    const PALAVRA_CHAVE                               = 25;
    // 22 é a busca, ainda não sabemos se será uma inteligência
    const PRODUTOS_RELACIONADOS                       = 34;
    const REMARKETING_NAVEGACAO                       = 35;    
    const SMART_HOME                                  = 36;
    const COMPRE_JUNTO_COMPLEMENTAR                   = 37;
    const MAIS_VENDIDOS_MARCA_MANUAL                  = 38;
    const SIMILAR_POR_PARAMETROS                      = 39; 
    const NOVIDADE_MARCA_MANUAL                       = 40;  

    
    /**
     * Construtor privado para que não seja criada intância dessa classe.
     */
    private function __construct() {
    }
    
    public static function getInteligencia($widInteligencia, $widget, $widgetProps, $widgetConfig) {
        $inteligencia = null;
        
        switch ($widInteligencia) {
            case self::MAIS_CLICADOS:
                
                $inteligencia = new MaisClicadosInteligencia();
                break;
                
            case self::MAIS_VENDIDOS:
                
                $inteligencia = new MaisVendidosInteligencia();
                break;
                
            case self::MAIS_VENDIDOS_CATEGORIA:
                
                $inteligencia = new MaisVendidosCategoriaInteligencia();
                break;
                
            case self::REMARKETING_ON_SITE:
                
                $inteligencia = new RemarketingOnSiteInteligencia();
                break;
                
            case self::PRODUTO_SIMILAR:
                
                $inteligencia = new ProdutoSimilarInteligencia();
                break;
                
            case self::LIQUIDACAO:
                
                $inteligencia = new LiquidacaoInteligencia();
                break;
                
            case self::COLECAO:
                
                $inteligencia = new ColecaoInteligencia();
                break;
                
            case self::COMPRE_JUNTO:
                
                $inteligencia = new CompreJuntoInteligencia();
                break;
                
            case self::OFERTA_LIMITADA_MANUAL:
                
                $inteligencia = new ManualInteligencia();
                break;
                
            case self::REMARKETING_ON_SITE_DINAMICO:
                
                $inteligencia = new RemarketingOnSiteDinamicoInteligencia();
                break;
                
            case self::CARRINHO_COMPLEMENTAR:
                
                $inteligencia = new CarrinhoComplementarInteligencia();
                break;
                
            case self::ITENS_COMPLEMENTAR:
                
                $inteligencia = new ItensComplementaresInteligencia();
                break;
                
            case self::OVERLAY_DE_SAIDA:
                
                if($widget->getOverlayNaoExibido()) {
                    $inteligencia = new OverlayDeSaidaInteligencia();
                }
                
                break;
                
            case self::BAIXOU_DE_PRECO:
                
                $inteligencia = new BaixouDePrecoInteligencia();
                break;
                
            // 15
            case self::NOVIDADES_DA_LOJA:
                
                $inteligencia = new NovidadesLojaInteligencia();
                break;
                
            // 20
            case self::MELHORES_AVALIADOS:
                
                $inteligencia = new MelhoresAvaliadosInteligencia();
                break;
                
            // 23
            case self::REMARKETING_COMPLEMENTAR:
                
                $inteligencia = new RemarketingComplementarInteligencia();
                break;
                
            case self::MAIS_VENDIDOS_CATEGORIA_MANUAL:
                
                $inteligencia = new MaisVendidosCategoriaManualInteligencia();
                break;

            case self::PRODUTOS_RELACIONADOS:
                
                $inteligencia = new ProdutosRelacionadosInteligencia();
                break;
                
            case self::REMARKETING_NAVEGACAO:
                
                $inteligencia = new RemarketingNavegacaoInteligencia();
                break;
                
            case self::SMART_HOME:
                
                $inteligencia = new SmartHomeInteligencia();
                break;

            case self::COMPRE_JUNTO_COMPLEMENTAR:
                
                $inteligencia = new CompreJuntoComplementarInteligencia();
                break; 

            case self::PALAVRA_CHAVE:
                
                $inteligencia = new PalavraChaveInteligencia();
                break;   

            case self::MAIS_VENDIDOS_MARCA_MANUAL:
                
                $inteligencia = new MaisVendidosMarcaManualInteligencia();
                break;

            case self::SIMILAR_POR_PARAMETROS:
                
                $inteligencia = new SimilarPorParametrosInteligencia();
                break;

            case self::NOVIDADE_MARCA_MANUAL:
                
                $inteligencia = new NovidadeMarcaManualInteligencia();
                break;              
        }
        
        // Se populada corretamente, então seta os valores
        if($inteligencia) {
            $inteligencia->setWidget($widget);
            $inteligencia->setWidgetProps($widgetProps);
            $inteligencia->setWidgetConfig($widgetConfig);
        }
        
        return $inteligencia;
    }
}
?>