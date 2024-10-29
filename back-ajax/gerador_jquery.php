<?php
require_once ("../arqueologo.php");

$symbol = filter_input(INPUT_POST, 'symbol', FILTER_SANITIZE_SPECIAL_CHARS);
$tempo_grafico = filter_input(INPUT_POST, 'tempo_grafico', FILTER_SANITIZE_SPECIAL_CHARS);
$data_inicial = filter_input(INPUT_POST, 'data_inicial', FILTER_SANITIZE_SPECIAL_CHARS);
$data_final = filter_input(INPUT_POST, 'data_final', FILTER_SANITIZE_SPECIAL_CHARS);


if(!empty($symbol) && !empty($tempo_grafico) && !empty($data_inicial) && !empty($data_final)){

    //transformo em timestamp e multiplico por mil para dar o timestamp em milissegundos, necessário para rodar a API
    $data_inicial = strtotime($data_inicial)*1000;
    $data_final = strtotime($data_final)*1000;

    //verifica que o usuário não confundiu data inicial e final
    if($data_final > $data_inicial){
        $mensagemFinal = comecar_insercao($conexao, $symbol, $tempo_grafico, $data_inicial, $data_final);
        echo $mensagemFinal;
    }
    
    else {
        echo "A data final deve ser depois da data inicial";
    }
}

else{
    echo "tem coisa que não passou no if";
}

?>