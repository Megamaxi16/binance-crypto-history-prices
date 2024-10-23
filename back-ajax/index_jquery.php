<?php
require_once "../functions.php";

$simboloData = filter_input(INPUT_POST, 'simboloPesquisa', FILTER_SANITIZE_SPECIAL_CHARS);

if(!empty($simboloData)){
    $comeco_url = "https://api.binance.com/api/v3/klines";

    $resultado = chama_api($comeco_url, $simboloData, "1d", 0, 1);

    echo $resultado[0];
}


?>