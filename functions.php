<?php
/*Erros a serem consertados:
    -Tá inserindo no banco bem mais do que a data final
    -Tratar melhorar as possibilidades de uso indo por parte do usuário
    -Isso vai funcionar mas vai ser meia boca

*/
//https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1m&startTime=165531956000&limit=50

//require "conecta_banco.php";


//função que chama tudo, antes não era uma função mas agora é. Transformei em função para deixar o index mais focado em frontend, to com preguiça de deixar bonitinho.
function chamador($conexao, $simbolo, $intervalo, $timestamp_inicial, $timestamp_final){
echo "Timestamp final: ".$timestamp_final."<br>";
$url_base = "https://api.binance.com/api/v3/klines";

$numero_de_velas= 1000;

//pega o timestamp de milésimo do início para calcular o tempo de resposta
$comeco_exec = microtime(true);

historico_temporal($conexao, $url_base, $simbolo, $intervalo, $timestamp_inicial, $timestamp_final, $numero_de_velas);

$fim_exec_parc = microtime(true);
$tempo_execucao = floatval($fim_exec_parc) - floatval($comeco_exec);
echo "<br> Tempo de execução: ". $tempo_execucao;

}

function historico_temporal($conexao, $url_base, $simbolo, $intervalo, $timestamp_inicial, $timestamp_final, $numero_de_velas){

    //debug do $timestamp_final
    echo "Timestamp final: ".$timestamp_final."<br>";


    //pega as velas baseado no intervalo mandado
    $resultado = chama_api($url_base, $simbolo, $intervalo, $timestamp_inicial, $numero_de_velas);

    //o escopo pode ser confuso por causa da recursividade mas eu declarei fora do while
    $query_funcao = "";

    foreach($resultado as $vela){
        
        /*
        echo "Momento: ".gmdate('d / m / Y H:i:s', $vela[0]/1000)."<br>";
        echo "Momento timestamp: ".$vela[0]."<br>";
        echo "Preço de Abertura: ".$vela[1]."<br>";
        echo "Preço Máximo: ".$vela[2]."<br>";
        echo "Preço Mínimo: ".$vela[3]."<br>";
        echo "Preço de Fechamento: ".$vela[4]."<br>";
        echo "Volume: ".$vela[5]."<br>";
        */

        $teste_timestamp = intval($vela[0])/1000;

        //chama a função que prepara a query -> o nome está gráfico_diário pois não tratei a variável ainda, é basicamente o nome da tabela do banco
        $query_funcao = valores($query_funcao, gmdate('Y-m-d H:i:s', $teste_timestamp), $vela[1], $vela[2], $vela[3], $vela[4], $vela[5]);

        //Armazenando o ultimo momento em milissegundos para a próxima requisição
        $momento_atual = $vela[0];


    }

    //insere no banco com query gigante preparada
    insere_banco_memoria($conexao, $query_funcao, "grafico_diario");

    echo "<br> Momento Atual requisição: ". gmdate('d / m / Y H:i:s',$momento_atual/1000)."<br>Timestamp Atual da Requisição:".$momento_atual."<br><br>";

    echo $momento_atual ."<=".$timestamp_final;

    //manda esperar 1 segundo para chamar a função de forma recursiva

    if($momento_atual <= $timestamp_final){
        sleep(1);
        
        $proximo_momento_inicial = $momento_atual/1000 + (60*60*24);
        $proximo_momento_inicial = $proximo_momento_inicial*1000;

        //troquei $momento_atual por $timestamp_final (penultimo parametro) se der merda reverter
        historico_temporal($conexao, $url_base, $simbolo, $intervalo, $proximo_momento_inicial, $timestamp_final, $numero_de_velas);
    }


}



function chama_api($url_base, $simbolo, $intervalo, $timestamp_inicial, $numero_de_velas){

    $url = $url_base ."?symbol=".$simbolo."&interval=".$intervalo."&startTime=".$timestamp_inicial."&limit=".$numero_de_velas;

    $sessao_curl = curl_init();

    //seta metodo: url e já seta a url tbm
    curl_setopt($sessao_curl, CURLOPT_URL, $url);

    //faz com que retorne a resposta como string
    curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);

    //resultado vem em json, o true no final é pra transformar em array
    return json_decode(curl_exec($sessao_curl), true);

}



function insere_banco($conexao, $momento, $preco_abertura, $preco_max, $preco_min, $preco_fech, $volume, $tempo_grafico){
    //$tempo grafico também é o nome da tabela / (?,?,....) igual como era em C, você deixa para colocar as variáveis dps.
    $query = "insert into ".$tempo_grafico."(horario_abertura, preco_abertura, high, low, preco_fechamento, volume) values 
    (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexao, $query);
    
    if(!$stmt){
        die("<br>Deu Merda: " .mysqli_error($conexao)."<br>");
    }

    //Aqui você atribui as variaveis ao $stmt
    mysqli_stmt_bind_param($stmt, "sddddd", $momento, $preco_abertura, $preco_max, $preco_min, $preco_fech, $volume);

    if(mysqli_stmt_execute($stmt)){
        echo "Inserido com Sucesso no banco!<br>";
    }
    else{
        echo "<br>Deu errado no momento:".$momento."<br>";
    }

}

//formata a querye com todos os valores
function valores($query_antiga, $momento, $preco_abertura, $preco_max, $preco_min, $preco_fech, $volume){

    $query_total = $query_antiga."('".$momento."', ".$preco_abertura.", ".$preco_max.", ".$preco_min.", ".$preco_fech.", ".$volume."), ";

    return $query_total;
}


function insere_banco_memoria($conexao, $query, $tempo_grafico){

    //verifico se query está vazio para não fazer com que dê erro no final
    if(!empty($query)){
        $query_final = "insert into ".$tempo_grafico."(horario_abertura, preco_abertura, high, low, preco_fechamento, volume) values ".$query;


        $query_final = rtrim($query_final, ", ");

        //echo ($query_final);

        if(mysqli_query($conexao, $query_final)){
            echo "<br><h3>Inserido com sucesso 2</h3><br>";
        }
    }
}
?>