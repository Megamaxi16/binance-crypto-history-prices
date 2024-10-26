<?php

require "functions.php";


    $comeco_url = "https://api.binance.com/api/v3/klines";

    $resultado = chama_api($comeco_url, "BTCUSDTasd", "1d", 0, 1);

    if(!is_array($resultado)){
        echo "Não é array";
    }

    else{
        //echo $resultado[0][0];
    echo date("Y-m-d", $resultado[0][0]/1000);
    }


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sobre</title>
    <link rel="icon" sizes="16x16" href="images/bitcoin_logo_white_transparent_16x16.png">
    <link rel="icon" sizes="32x32" href="images/bitcoin_logo_white_transparent_32x32.png">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include "header.php"; ?>



<script src="js\jquery-3.7.1.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>