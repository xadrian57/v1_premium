/*
ESTE SCRIPT MOSTRA OS FIELDS CORRETOS A SEREM PREENCHIDOS COM BASE NA OPÇÃO DO CLIENTE
*/



/*
SELECIONA CPF OU CNPJ
*/

$("#selecionacpfcnpj").change(function () {
    var selected_option = $('#selecionacpfcnpj').val();
    if (selected_option === '1') {
        $('#selecionacpf').attr('pk','1').show();
    }
    if (selected_option != '1') {
        $("#selecionacpf").removeAttr('pk').hide();
    }
}),

$("#selecionacpfcnpj").change(function () {
    var selected_option = $('#selecionacpfcnpj').val();
    if (selected_option === '2') {
        $('#selecionacnpj').attr('pk','1').show();
    }
    if (selected_option != '2') {
        $("#selecionacnpj").removeAttr('pk').hide();
    }
}),


/*
SELECIONA CPF OU CNPJ
*/

$("#selecionatelefone").change(function () {
    var selected_option = $('#selecionatelefone').val();
    if (selected_option === '1') {
        $('#selecionacelular').attr('pk','1').show();
    }
    if (selected_option != '1') {
        $("#selecionacelular").removeAttr('pk').hide();
    }
}),

$("#selecionatelefone").change(function () {
    var selected_option = $('#selecionatelefone').val();
    if (selected_option === '2') {
        $('#selecionafixo').attr('pk','1').show();
    }
    if (selected_option != '2') {
        $("#selecionafixo").removeAttr('pk').hide();
    }
})