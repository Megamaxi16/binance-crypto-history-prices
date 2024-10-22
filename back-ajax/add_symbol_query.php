<?php
require_once "../conecta_banco.php";
$simbolo_cru = filter_input(INPUT_POST, "symbol_add", FILTER_SANITIZE_SPECIAL_CHARS);
if(!empty($simbolo_cru)){

    //formata string de acordo com a documentação da binance
    $simbolo_cru = str_replace(['/', '-', ':', '_', ' '], '', $simbolo_cru);
    $simbolo_formatado = strtoupper($simbolo_cru);

    //inserir no banco
    $query_add_simbolo = "insert into symbols(symbol) values ('".$simbolo_formatado."')";
    if(mysqli_query($conexao, $query_add_simbolo)){
        echo '<div class="bg-success text-white d-flex justify-content-center align-items-center" style="width: 300px; height: 200px;">
        Par '.$simbolo_formatado.' inserido com sucesso !
        </div>';
        die;
    }
    else{
        echo "Erro ao inserir símbolo: ". mysqli_error($conexao);
        die;
    }
}
?>