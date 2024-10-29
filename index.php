<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão se ainda não foi iniciada
}
set_time_limit(14400);
require_once "functions.php";
require_once "conecta_banco.php";
require "iniciar.php";
//require_once "arqueologo.php";


$symbol = filter_input(INPUT_POST,"symbol", FILTER_SANITIZE_SPECIAL_CHARS);
$tempo_grafico = filter_input(INPUT_POST, "tempo_grafico", FILTER_SANITIZE_SPECIAL_CHARS);
$data_inicial = filter_input(INPUT_POST, "data_inicial", FILTER_SANITIZE_SPECIAL_CHARS);
$data_final = filter_input(INPUT_POST, "data_final", FILTER_SANITIZE_SPECIAL_CHARS);

if(!empty($symbol) && !empty($tempo_grafico) && !empty($data_inicial) && !empty($data_final)){

    //transformo em timestamp e multiplico por mil para dar o timestamp em milissegundos, necessário para rodar a API
    $data_inicial = strtotime($data_inicial)*1000;
    $data_final = strtotime($data_final)*1000;

    //verifica que o usuário não confundiu data inicial e final
    if($data_final > $data_inicial){
    chamador($conexao, $symbol, $tempo_grafico, $data_inicial, $data_final);
    } else {
        echo "A data final deve ser depois da data incial";
    }
}

$hoje = hoje();

//começa o front
include_once "head.php"?>
<title>Home</title>
<body>
<?php include "header.php"; ?>

<div class="container">

<!-- 
-Puxar do banco quantos pares estão com algum registro e armazenar em session 
-Limi



-->




</div>
<script>

    $(document).ready(function(){

        $('#symbol').on('change', function(){

            let simboloPesquisa = $('#symbol').val();

            //let url = "https://api.binance.com/api/v3/klines/?symbol="+ simboloPesquisa + "&interval=1d&startTime=0&limit=1"

            $.ajax({
                url: "back-ajax/index_jquery.php",
                type: 'POST',
                data: {symbol: simboloPesquisa },
                success: function(primeiraData){

                    $('#data_inicial').attr('min', primeiraData);
                    $('#data_final').attr('min', primeiraData)

                },
                error: function(){
                    console.log("Vish, deu erro");
                }
            });
        });
    });

</script>
<?php include_once "footer.php"; ?>