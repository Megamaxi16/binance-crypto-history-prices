<?php
session_start();
header('Access-Control-Allow-Origin: *');
set_time_limit(14400);
require "functions.php";
require "conecta_banco.php";
require "iniciar.php";
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


//começa o front
include_once "head.php"?>
<title>Home</title>
<body>
<?php include "header.php"; ?>

<div class="container">

    <form method="post">

        <div class= "form-group">
            <label>Par de Criptomoeda</label>
            <select class="form-control" id="symbol">
                <?php 
                    foreach($_SESSION["simbolos"] as $simbolo){
                        echo "<option value=".$simbolo.">".$simbolo."</option>";
                    }                
                ?>
            </select>
        </div>
        
            <!-- Add novas criptos -->
            <div class="d-flex justify-content-center">
            <a class="btn btn-primary" href="add_symbol.php">Adicione Novo Par</a>
            </div>

        <div class= "form-group">
            <label>Escolha o tempo gráfico: </label>
            <select name="tempo_grafico" class="form-control" id="tempo_grafico">
                <!--<option value="1mes">Mensal</option>-->
                <option value="1d">Diário</option>
                <option value="4h">4 Horas</option>
                <option value="1h">1 Hora</option>
                <option value="30m">30 Minutos</option>
                <option value="15m">15 Minutos</option>
                <option value="5m">5 Minutos</option>
                <option value="1m">1 Minuto</option>
            </select>
        </div>

        <div class= "form-group">
            <label>Data Inicial:</label>
            <input type="date" name="data_inicial" id="data_inicial" class="form-control" required>
        </div>

        <div class= "form-group">
            <label>Data Final:</label>
            <input type="date" name="data_final" id="data_final" class="form-control" required>
        </div>


            <input type="submit" value="Enviar" class="btn btn-primary">






    </form>
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

                    console.log ("oi: "+primeiraData);
                    $('#data_inicial').attr('min', primeiraData);

                },
                error: function(){
                    console.log("Vish, deu erro");
                }
            });
        });
    });

</script>
<?php include_once "footer.php"; ?>