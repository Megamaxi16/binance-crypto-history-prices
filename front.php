<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Título da Página</title>
</head>

<?php

$symbol = filter_input(INPUT_POST,"symbol", FILTER_SANITIZE_SPECIAL_CHARS);
$tempo_grafico = filter_input(INPUT_POST, "tempo_grafico", FILTER_SANITIZE_SPECIAL_CHARS);
$data_inicial = filter_input(INPUT_POST, "data_inicial", FILTER_SANITIZE_SPECIAL_CHARS);
$data_final = filter_input(INPUT_POST, "data_final", FILTER_SANITIZE_SPECIAL_CHARS);

if(!empty($symbol) && !empty($tempo_grafico) && !empty($data_inicial) && !empty($data_final))
?>



<style type="text/css">

</style>
<body>

<form method="post">

    <ul class="lista">
        <li>
            <label>Escolha a Cripto: </label>
                <select name="symbol" id="symbol">
                    <option value="BTCUSD">BTCUSD</option>
                    <option value="ETHUSD">ETHUSD</option>
                    <option value="SOLUSD">SOLUSD</option>
                    <option value="BNBUSD">BNBUSC</option>
                    <option value="LINKUSD">LINKUSD</option>
                </select>
            </label>
        </li>

        <li>
        <label>Escolha o tempo gráfico: </label>
            <select name="tempo_grafico" id="tempo_grafico">
                <option value="1m">Mensal</option>
                <option value="1d">Diário</option>
                <option value="4h">4 Horas</option>
                <option value="1h">1 Hora</option>
                <option value="30m">30 Minutos</option>
                <option value="15m">15 Minutos</option>
                <option value="5m">5 Minutos</option>
                <option value="1m">1 Minuto</option>
            </select>
        </li>
            <label>Data Inicial:</label>
            <input type="date" name="data_inicial" id="data_inicial" required>
        <li>

        </li>
            <label>Data Final:</label>
            <input type="date" name="data_final" id="data_final" required>
        <li>
        </li>
            <input type="submit" value="Enviar">
        <li>

    </ul>



</form>

</body>
</html>