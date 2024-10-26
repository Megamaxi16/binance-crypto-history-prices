<?php

/*Erros a serem consertados:
    -Tá inserindo no banco bem mais do que a data final
    -Tratar melhorar as possibilidades de uso por parte do usuário
    -Isso vai funcionar mas vai ser meia boca
    -Tratar quanto de incremento vai ter
    -Trocar o switch case de lugar
    -Otimizar Uso de memória
*/
//https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1m&startTime=165531956000&limit=50

include "conecta_banco.php";
include "functions.php";

comecar_insercao($conexao, "BTCUSDT", "1d", /*1502928000000*/ 1652046466339, (time()*1000) - 1000*60*60*24);

function comecar_insercao($conexao, $simbolo, $intervalo, $timestamp_inicio_chamada, $timestamp_final){

    $url_base = "https://api.binance.com/api/v3/klines";
    
    $tabela = "tempo_".$intervalo; 

    $numero_de_velas= 1000;

    //inicio tempo de resposta
    $comeco_exec = microtime(true);
    
    //variável que escolhe o método de envio de dados
    $metodo = 1;

    $condicao_parada = 0;;

    while($condicao_parada == 0){
        echo "olá";
        $resultado = chama_api($url_base, $simbolo, $intervalo, $timestamp_inicio_chamada, $numero_de_velas);

        if(isset($resultado['code'])){
            $condicao_parada = 1;
            break;
        }


        switch ($metodo){
            case 1:
        //chama a função mais rapida em tempo de resposta

                $timestamp_inicio_chamada = insere_banco_tempo($conexao, $resultado, $tabela, $simbolo);

            break;

            case 2:
        //chama a função mais segura
            break;

            case 3:
        //chama uma terceira função que não pensei ainda
            break;
        }

    }

}




function insere_banco_tempo($conexao, $dados, $tabela, $simbolo){

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

        $query_gigantesca = $query_acc."('".gmdate('Y-m-d H:i:s',intval($linha[0])/1000)."', ".$linha[1].", ".$linha[2].", ".$linha[3].", ".$linha[4].", ".$linha[5].", '".$simbolo."'), ";

        $ultimo_timestamp_parcial = $linha[0];
    }


    if(!empty($query_gigantesca)){
        $query_oficial = "insert into ".$tabela."(horario_abertura, preco_abertura, high, low, preco_fechamento, volume, symbol) values ".$query_gigantesca;

        $query_final = rtrim($query_oficial, ", ");

        if(mysqli_query($conexao, $query_final)){
        echo "<br><h3>Inserido com sucesso 2</h3><br>";
        return  $ultimo_timestamp_parcial;
        }
        else{
            echo "<br><h3>Deu Merda</h3><br>";
            return $ultimo_timestamp_parcial;
        }
    }
    else{
        echo "Erro na geração da query";
    }


}
?>