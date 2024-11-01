<?php
require_once "../conecta_banco.php";
require_once "../functions.php";

// Desativa o relatório de exceções do MySQLi para usar o tratamento manual de erros
mysqli_report(MYSQLI_REPORT_OFF);

$simbolo_cru = filter_input(INPUT_POST, "symbol_add", FILTER_SANITIZE_SPECIAL_CHARS);
if(!empty($simbolo_cru)){

    //formata string de acordo com a documentação da binance
    $simbolo_cru = str_replace(['/', '-', ':', '_', ' '], '', $simbolo_cru);
    $simbolo_formatado = strtoupper($simbolo_cru);

    //verifica se o símbolo está presente na api
    if(count(chama_api("https://api.binance.com/api/v3/klines", $simbolo_formatado, "1d", 0, 1)) == 1){

        $query_add_simbolo = "insert into symbols(symbol) values ('".$simbolo_formatado."')";

        //inserir no banco
        if(mysqli_query($conexao, $query_add_simbolo)){
            echo '<div class="bg-success text-white d-flex justify-content-center align-items-center" style="width: 300px; height: 50px;">
            Par '.$simbolo_formatado.' inserido com sucesso !
            </div>';
            die;
        }
        else{
        
            if(mysqli_errno($conexao) == 1062){
                echo '<div class="bg-warning text-white d-flex justify-content-center align-items-center" style="width: 300px; height: 50px;">
                Par de criptomoedas já regitrado          
                </div>';
                die;
            }

            else{
                echo '<div class="bg-danger text-white d-flex justify-content-center align-items-center" style="width: 300px; height: 50px;">
                '.mysqli_error($conexao).'            
                </div>';
            }
        }
    }
    else{
        echo '<div class="bg-warning text-white d-flex justify-content-center align-items-center" style="width: 300px; height: 50px;">
        Par de moedas errado ou não presente na binance       
        </div>';
    }
    atualiza_session($conexao);
}
?>