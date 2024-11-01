<?php
require_once "../functions.php";

$simboloData = filter_input(INPUT_POST, 'symbol', FILTER_SANITIZE_SPECIAL_CHARS);


if(!empty($simboloData)){
    $comeco_url = "https://api.binance.com/api/v3/klines";

    $resultado = chama_api($comeco_url, $simboloData, "1d", 0, 1);
    //echo $resultado[0][0];
    echo date("Y-m-d", $resultado[0][0]/1000);
}

unset($simboloData);
unset($comeco_url);
unset($resultado);


?>