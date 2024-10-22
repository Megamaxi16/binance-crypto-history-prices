<?php
require_once "functions.php";
require_once "conecta_banco.php";
head("Adicionar Par de Cripto");
include "header.php";

$simbolo_cru = filter_input(INPUT_POST, "symbol_add", FILTER_SANITIZE_SPECIAL_CHARS);


if(!empty($simbolo_cru)){

    //formata string de acordo com a documentação da binance
    $simbolo_cru = str_replace(['/', '-', ':', '_', ' '], '', $simbolo_cru);
    $simbolo_formatado = strtoupper($simbolo_cru);

    //inserir no banco
    $query_add_simbolo = "insert into symbols(symbol) values ('".$simbolo_formatado."')";
    if(mysqli_query($conexao, $query_add_simbolo)){
        echo '<div class="bg-success text-white d-flex justify-content-center align-items-center" style="width: 300px; height: 200px;">
        Par '.$simbolo_formatado.' inserido com sucesso !
        </div>';
    }
    else{
        echo "Erro ao inserir símbolo: ". mysqli_error($conexao);
    }
}



?>

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

        $('verificar_botao').on('click', function(){

            let simbolo = $('symbol_add').val;

            $.ajax({
                url: 'add_symbol.php',
                type:'POST',
                data: {symbol_add: simbolo},
                sucess: function(response) {

                    //exibição do resultado retornado pelos echos do php
                    //vou retornar as classes prontas também
                    $('#result').html(response);
                },
                error: function(){
                    alert("Não foi possível encontrar este par de criptomoedas")
                }

            });

        });

    });

</script>

<?php footer(); ?>