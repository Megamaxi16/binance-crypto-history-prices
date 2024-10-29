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
        $resultado = chama_api($url_base, $simbolo, $intervalo, $timestamp_inicio_chamada, $numero_de_velas);

        if(isset($resultado['code'])){
            $condicao_parada = 1;
            return "<br> Caiu no 'code': ".$resultado['code']."<br>";
            break;
        }
        
        if($timestamp_inicio_chamada >= $timestamp_final){
            $condicao_parada = 1;
            //echo "<br>Timestamp Final:".$timestamp_final."<br>";
            echo "Inserido com sucesso TOTA";
            break;
        }

        switch ($metodo){
            case 1:

                //chama a função mais rapida em tempo de resposta
                $timestamp_inicio_chamada = insere_banco_tempo($conexao, $resultado, $tabela, $simbolo, $incremento, $timestamp_final);

            break;

            case 2:
        //chama a função mais segura
            break;

            case 3:
        //chama uma terceira função que não pensei ainda
            break;
        }


    }
    $fim_exec = microtime(true);
    $tempo_exec = $fim_exec - $comeco_exec;
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
        [5] = Volume total (to armazenando isso????)
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
?>