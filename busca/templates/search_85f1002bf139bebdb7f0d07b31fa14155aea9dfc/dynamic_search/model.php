<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
?>

<div id="rh-app">
    <div class="rh-controls">
        <form style="position: relative;width: 100%;border: 1px solid #333;">
            <input name="rhSearch" placeholder="teste" style="display: inline-block;height: 40px;width: calc(100% - 45px);">
            <button style="width: auto;display: inline-block;float:  right;height:  40px;">testar</button>
        </form>

        <div class="rh-active-filters __rh-active-filters__"></div><!-- JS -->

        <div class="rh-filter rh-hidden" data-rh-filter="category" data-rh-label="Categoria">
            <span class="rh-filter-title">Categoria</span>
        </div><!-- JS -->

        <div class="rh-filter rh-hidden" data-rh-filter="price" data-rh-label="Preço">
            <span class="rh-filter-title">Preço</span>
        </div><!-- JS -->
        
        <div class="rh-filter rh-hidden" data-rh-filter="discount" data-rh-label="Desconto">
            <span class="rh-filter-title">Desconto</span>
        </div><!-- JS -->

        <div class="rh-filter rh-hidden" data-rh-filter="brand" data-rh-label="Fabricante"></div><!-- JS -->
    </div>

    <div class="rh-shelf-container">
        <div class="rh-shelf-controls">
            <div class="rh-order-by">
                <label>ORDENAR POR:</label>
                <select class="__rh-select-order__"><!-- JS -->
                    <option value="relevance" selected>Relevância</option>
                    <option value="discount">Desconto</option>
                    <option value="higherPrice">Maior Preço</option>
                    <option value="lowerPrice">Menor Preço</option>
                    <option value="new">Novidades</option>
                </select>
            </div>

            <div class="rh-itens-per-page">
                <label>ITENS POR PÁGINA:</label>
                <select class="__rh-select-pagination__"><!-- JS -->
                    <option selected>24</option>
                    <option>48</option>
                    <option>72</option>
                    <option>96</option>
                </select>
            </div>
        </div>
        <div class="rh-pagination">
            <div class="__rh-pagination__"></div><!-- JS -->
        </div>
        <div class="__rh-shelf__ rh-shelf"><!-- JS -->
            <!-- LOADER -->
            <div class="rh-loader-container">
                Carregando Prateleira...
                <div class="rh-loading-icon-bar"></div>
            </div>
            <!-- LOADER -->
        </div>
        <div class="rh-pagination">
            <div class="__rh-pagination__"></div><!-- JS -->
        </div>
    </div>
</div>