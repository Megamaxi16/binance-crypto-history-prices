<?php
//Hello World
//https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1m&startTime=165531956000&limit=50

//require "conecta_banco.php";


//função que chama tudo, antes não era uma função mas agora é. Transformei em função para deixar o index mais focado em frontend, to com preguiça de deixar bunitinho.
function chamador($conexao, $simbolo, $intervalo, $timestamp_inicial, $timestamp_final){

$url_base = "https://api.binance.com/api/v3/klines";

//tratar: intervalo, e timestamps


echo $simbolo."<br>"; //= "BTCUSDT"; // Par BTC/USDT pegar via post
echo $intervalo."<br>"; //= "1d";    // Intervalo de tempo diário -> pegar via post
echo $timestamp_inicial."<br>"; //= strtotime('2017-01-01') * 1000; // Converter para milissegundos - pegar via post
echo $timestamp_final."<br>"; //= strtotime('2024-09-14') * 1000;   // Converter para milissegundos -> pegar via post
echo $numero_de_velas= 10;


//pega o timestamp de milésimo do início para calcular o tempo de resposta
$comeco_exec = microtime(true);

historico_temporal($conexao, $url_base, $simbolo, $intervalo, $timestamp_inicial, $timestamp_final, $numero_de_velas);

$fim_exec_parc = microtime(true);
$tempo_execucao = floatval($fim_exec_parc) - floatval($comeco_exec);
echo "<br> Tempo de execução: ". $tempo_execucao;

}

function historico_temporal($conexao, $url_base, $simbolo, $intervalo, $timestamp_inicial, $timestamp_final, $numero_de_velas){

    //pega as velas baseado no intervalo mandado
    $resultado = chama_api($url_base, $simbolo, $intervalo, $timestamp_inicial, $numero_de_velas);

    //o escopo pode ser confuso por causa da recursividade
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

        //chama a função que prepara a query
        $query_funcao = valores($query_funcao, gmdate('Y-m-d H:i:s', $vela[0]/1000), $vela[1], $vela[2], $vela[3], $vela[4], $vela[5], "grafico_diario");    

        //Armazenando o ultimo momento para fazer o próximo pedido
        $momento_final = $vela[0];
        
        echo "<br>";
    }

    //insere no banco com query gigante preparada
    insere_banco_memoria($conexao, $query_funcao, "grafico_diario");

    echo "<br> Momento Atual requisição: ". gmdate('d / m / Y H:i:s',$momento_final/1000)."<br>Timestamp Atual da Requisição:".$momento_final."<br><br>";

    //manda esperar 10 segundos para chamar a função de forma recursiva
    if($momento_final/1000 <= $timestamp_final){
        sleep(1);
        //$proximo_momento_inicial = strtotime("+1 day",$momento_final);
        //historico_temporal($conexao, $url_base, $simbolo, $intervalo, $proximo_momento_inicial, $numero_de_velas);
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

    $query_final = "insert into ".$tempo_grafico."(horario_abertura, preco_abertura, high, low, preco_fechamento, volume) values ".$query;


    $query_final = rtrim($query_final, ", ");

    if(mysqli_execute_query($conexao, $query_final)){
        echo "<br><h3>Inserido com sucesso 2</h3><br>";
    }

}
?>