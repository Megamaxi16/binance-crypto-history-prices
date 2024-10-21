<?php
session_start();

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
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Crypto Archaeologist</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" sizes="16x16" href="images/bitcoin_logo_white_transparent_16x16.png">
    <link rel="icon" sizes="32x32" href="images/bitcoin_logo_white_transparent_32x32.png">
</head>

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
                    /*<option value="BTCUSDT">Bitcoin/USDT</option>
                    <option value="ETHUSDT">Ethereum/USDT</option>
                    <option value="SOLUSDT">Solana/USDT</option>
                    <option value="BNBUSDT">Binance/USDT</option>
                    <option value="LINKUSDT">Chainlink/USDT</option>*/

                ?>
            </select>
        </div>
        
            <!-- <label>Escolha a Cripto: </label>
                <select name="symbol" id="symbol">
                    <option value="BTCUSDT">Bitcoin/USDT</option>
                    <option value="ETHUSDT">Ethereum/USDT</option>
                    <option value="SOLUSDT">Solana/USDT</option>
                    <option value="BNBUSDT">BinanceUSDT</option>
                    <option value="LINKUSDT">Chainlink/USDT</option>
                </select>
            </label> -->


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

<script src="js\jquery-3.7.1.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>