<?php
require_once "functions.php";
require_once "conecta_banco.php";

//Começo do front da página
include_once "head.php";?>

<title>Adicionar Par de Critptomoedas</title>
<body>
<?php include "header.php";?>

<form method="post">
    <div class="row d-flex justify-content-center">
        <div class= "form-group col-sm-4 mt-5">
            <label><h2>Digite o par</h2></label>
            <input type="text" class="form-control" id="symbol_add">
            <div class="d-flex justify-content-center">
                <input class="btn btn-primary mt-3" id="verificar_botao" type="submit" value="Verificar Símbolo">
            </div>
        </div>
    </div>
</form>
<div class="d-flex justify-content-center" id="result">

</div>

<script>

    //aqui alguém pode manipular um while e bloquear o ip do usuário
    $(document).ready(function(){

        $('#verificar_botao').on('click', function(e){
            e.preventDefault();//faz com que não recarregue a página
            
            let simbolo = $('#symbol_add').val();
            console.log("símbolo capturado com sucesso");

            $.ajax({
                url: "back-ajax/add_symbol_query.php",
                type:'POST',
                data: {symbol_add: simbolo},
                success: function(response) {
                    //exibição do resultado retornado pelos echos do php
                    //vou retornar as classes prontas também
                    $('#result').html(response);
                },
                error: function(){
                    console.log("Não foi possível encontrar este par de criptomoedas")
                }
            });
        });
    });
</script>
<?php include_once "footer.php"; ?>