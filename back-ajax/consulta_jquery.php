<?php
session_start();

$simboloConsulta = filter_input(INPUT_POST, "symbol", FILTER_SANITIZE_SPECIAL_CHARS);

if (!empty($simboloConsulta) && isset($_SESSION["dados_ativos"][$simboloConsulta])) {
    // Converte para o formato "Y-m-d H:i:s" com hora completa
    $dataInicial = date("Y-m-d", strtotime($_SESSION["dados_ativos"][$simboloConsulta]["dt_inicial"]));
    $dataFinal = date("Y-m-d", strtotime($_SESSION["dados_ativos"][$simboloConsulta]["dt_final"]));

    echo $dataInicial . '--' . $dataFinal;
} else {
    echo "vazio";
}