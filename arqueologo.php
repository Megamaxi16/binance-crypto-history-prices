<?php

/*Erros a serem consertados:
    -Tá inserindo no banco bem mais do que a data final
    -Tratar melhorar as possibilidades de uso por parte do usuário
    -Isso vai funcionar mas vai ser meia boca
    -Tratar quanto de incremento vai ter
    -Trocar o switch case de lugar ok
    -Otimizar Uso de memória ok
*/
//https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1m&startTime=165531956000&limit=50

include_once "conecta_banco.php";
include_once "functions.php";

//comecar_insercao($conexao, "BTCUSDT", "1d", 1502928000000 /*1652046466339*/, (time() - 60*60*24)*1000);

function comecar_insercao($conexao, $simbolo, $intervalo, $timestamp_inicio_chamada, $timestamp_final){

    $url_base = "https://api.binance.com/api/v3/klines";
    
    $tabela = "tempo_".$intervalo; 

    $numero_de_velas= 1000;

    //inicio tempo de resposta
    $comeco_exec = microtime(true);

    //tratando o incremento para ele ser dinamico
    $incremento = null;
    switch($intervalo){
        case "1d":
            $incremento = 60*60*24*1000;
            break;
        case "4h":
            $incremento = 60*60*4*1000;
            break;
        case "1h":
            $incremento = 60*60*1000;
            break;
        case "30m":
            $incremento = 60*30*1000;
            break;
        case "15m":
            $incremento = 60*15*1000;
            break;
        case "5m":
            $incremento = 60*5*1000;
            break;
        case "1m":
            $incremento = 60*1000;
            break;
    }
    
    //variável que escolhe o método de envio de dados
    $metodo = 1;

    $condicao_parada = 0;

    while($condicao_parada == 0){

        if(isset($resultado['code'])){
            $condicao_parada = 1;
            return "<br> Caiu no 'code': ".$resultado['code']."<br>";
            break;
        }

        $resultado = chama_api($url_base, $simbolo, $intervalo, $timestamp_inicio_chamada, $numero_de_velas);


        
        if($timestamp_inicio_chamada >= $timestamp_final){
            $condicao_parada = 1;
            //echo "<br>Timestamp Final:".$timestamp_final."<br>";
            //echo "Inserido com sucesso";
            break;
        }

        switch ($metodo){
            case 1:

                //chama a função mais rapida em tempo de resposta - APENAS 5% MAIS RÁPIDA
                $timestamp_inicio_chamada = insere_banco_tempo($conexao, $resultado, $tabela, $simbolo, $incremento, $timestamp_final);

            break;

            case 2:

                //chama a função mais segura
                $timestamp_inicio_chamada = insere_banco_seguro($conexao, $resultado, $tabela, $simbolo, $incremento, $timestamp_final);


            break;
        }


    }
    $fim_exec = microtime(true);
    $tempo_exec = $fim_exec - $comeco_exec;
    atualiza_session($conexao);
    return "<br>Inserido com sucesso!<br><br>tempo de execução:". $tempo_exec;
}

//esta dentro de um while
function insere_banco_tempo($conexao, $dados, $tabela, $simbolo, $incremento, $tempo_final){ 
    $ultimo_timestamp_parcial = null;

    //tratar a parte final da query
    $query_gigantesca="";
    foreach($dados as $linha){
        $query_acc = $query_gigantesca;
        /*
        [0] = timestamp de abertura
        [1] = Preço de abertura
        [2] = Preço Máximo no intervalo
        [3] = Preço Mínimo no intervalo
        [4] = Preço de Fechamento
        [5] = Volume total
        */

        //parar concatenação quando ficar igual ou maior que o tempo final
        if($ultimo_timestamp_parcial >= $tempo_final){
            break;
        }

        $query_gigantesca = $query_acc."('".gmdate('Y-m-d H:i:s',intval($linha[0])/1000)."', ".$linha[1].", ".$linha[2].", ".$linha[3].", ".$linha[4].", ".$linha[5].", '".$simbolo."'), ";


        //milissegundos
        $ultimo_timestamp_parcial = $linha[0];
        //echo "<br> Timestamp preparo: ". $ultimo_timestamp_parcial;
    }


    if(!empty($query_gigantesca)){
        $query_oficial = "insert into ".$tabela."(horario_abertura, preco_abertura, high, low, preco_fechamento, volume, symbol) values ".$query_gigantesca;

        $query_final = rtrim($query_oficial, ", ");

        if(mysqli_query($conexao, $query_final)){
        //echo "<br><h3>Inserido com sucesso 2</h3><br>";
        return  $ultimo_timestamp_parcial + $incremento;
        }
        else{
            //echo "<br><h3>Deu Merda</h3><br>";
            return $ultimo_timestamp_parcial + $incremento;
        }
    }
    else{
        return "Erro na geração da query";
        die;
    }


}


//------------------------------------------------------------------------------------------//

//esta dentro de um while
function insere_banco_seguro($conexao, $dados, $tabela, $simbolo, $incremento, $tempo_final){ 

    // Construir a query inicial
$query = "INSERT INTO ".$tabela." (horario_abertura, preco_abertura, high, low, preco_fechamento, volume, symbol) VALUES ";

$values = []; // Armazenará cada linha como uma string formatada para a query
$types = ""; // Armazenará os tipos de cada valor a ser bindado
$params = []; // Armazenará os valores a serem inseridos

$ultimo_timestamp_parcial = null;

foreach ($dados as $linha) {
    if ($ultimo_timestamp_parcial >= $tempo_final) {
        break;
    }

    // Formata cada linha para ser incluída na query
    $values[] = "(?, ?, ?, ?, ?, ?, ?)"; //tipo um push

    // Adiciona os tipos de parâmetros de cada linha (sdddddss)
    $types .= "sddddds";

    // Adiciona os valores correspondentes para bind
    $params[] = gmdate('Y-m-d H:i:s', intval($linha[0]) / 1000);
    $params[] = $linha[1];
    $params[] = $linha[2];
    $params[] = $linha[3];
    $params[] = $linha[4];
    $params[] = $linha[5];
    $params[] = $simbolo;

    $ultimo_timestamp_parcial = $linha[0];
}

// Concatena os valores na query
$query = $query .implode(", ", $values);

// Prepara a consulta completa
$stmt = mysqli_prepare($conexao, $query);

if (!$stmt) {
    die("Erro ao preparar statement: " . mysqli_error($conexao));
}

// Usa call_user_func_array para bindar os valores
mysqli_stmt_bind_param($stmt, $types, ...$params);

// Executa a query com todas as linhas em uma única chamada
if (!mysqli_stmt_execute($stmt)) {
    echo "Erro ao inserir dados: " . mysqli_stmt_error($stmt);
    $sucesso = 0;
} else {
    echo "Dados inseridos com sucesso!";
    $sucesso = 1;
}


if($sucesso == 1){
    return  $ultimo_timestamp_parcial + $incremento;
}

}

?>