<?php
session_start();
require_once "../conecta_banco.php";


$symbol = filter_input(INPUT_POST, 'query_symbol', FILTER_SANITIZE_SPECIAL_CHARS);
$tempo_grafico_consulta = filter_input(INPUT_POST, 'tempo_grafico_consulta', FILTER_SANITIZE_SPECIAL_CHARS);
$data_inicial_consulta = filter_input(INPUT_POST, 'data_inicial_consulta', FILTER_SANITIZE_SPECIAL_CHARS);
$data_final_consulta = filter_input(INPUT_POST, 'data_final_consulta', FILTER_SANITIZE_SPECIAL_CHARS);

if(!empty($symbol) && !empty($tempo_grafico_consulta) && !empty($data_inicial_consulta) && !empty($data_final_consulta)){

    $tabela = mysqli_real_escape_string($conexao, "tempo_".$tempo_grafico_consulta);

    $queryConsulta = "select * from $tabela where symbol = ? and horario_abertura between ? and ?";

    $stmt = mysqli_prepare($conexao, $queryConsulta);

    if(!$stmt){
        die("<br>Deu Merda: " .mysqli_error($conexao)."<br>");
    }

    mysqli_stmt_bind_param($stmt, "sss", $symbol, $data_inicial_consulta, $data_final_consulta);

    if(mysqli_stmt_execute($stmt)){
        $resultado = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($resultado) > 0){

            echo '<table class="table table-bordered table-striped text-center align-middle">
            <thead>
                <tr>
                    <th colspan="6" class="thead-dark">'.$symbol.'</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <th>Data</th>
                    <th>Preço Abertura</th>
                    <th>Preço Fechamento</th>
                    <th>High</th>
                    <th>Low</th>
                    <th>Volume</th>
                </tr>';

            while($linha = mysqli_fetch_assoc($resultado)){
                echo '<tr>
                <th scope="row">'.$linha["horario_abertura"].'</th>
                <td>'.$linha["preco_abertura"].'</td>
                <td>'.$linha["preco_fechamento"].'</td>
                <td>'.$linha["high"].'</td>
                <td>'.$linha["low"].'</td>
                <td>'.$linha["volume"].'</td>
                </tr>';
            }
            echo '  </tbody>
                    </table>';
        }
    
    
    mysqli_stmt_close($stmt);
    }
    else {
        echo "mensagem de erro da derrota";
    }
}

?>