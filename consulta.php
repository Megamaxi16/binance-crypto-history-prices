<?php
session_start();
require_once "functions.php";
require_once "conecta_banco.php";
require_once "iniciar.php";

//Começo do front da página
include_once "head.php";?>

<title>Consultar Histórico de Preços</title>
<body>
<?php include "header.php";?>

<div class="container">
    <form method="post">
        <div class="row d-flex justify-content-center">

            <div class= "form-group col-sm-4 mt-5">
                <label><h5>Escolha o Par de Criptomoedas</h5></label>
                <select class="form-control" id="query_symbol" name="query_symbol" required>
                    <option value="">Selecione...</option>
                    <?php 
                    if(isset($_SESSION["dados_ativos"]) && !empty($_SESSION["dados_ativos"])){
                        foreach ($_SESSION["dados_ativos"] as $cripto_ativa => $dados){

                            echo "<option value=".$dados["simbolo"].">".$dados["simbolo"]."</option>";
                        }
                    }
    
                    else{
                        echo '<option value="">Nenhuma moeda cadastrada</option>"';
                    }?>
                </select>
            </div>

            <div class= "form-group col-sm-4 mt-5">
            <label><h5>Escolha o tempo gráfico</h5></label>
            <select name="tempo_grafico" class="form-control" id="tempo_grafico_consulta">
                <!--<option value="1mes">Mensal</option>-->
                <option value="1d">Diário</option>
                <option value="4h">4 Horas</option>
                <option value="1h">1 Hora</option>
                <option value="30m">30 Minutos</option>
                <option value="15m">15 Minutos</option>
                <option value="5m">5 Minutos</option>
                <option value="1m">1 Minuto</option>
            </select>
            </div>

        </div>




            <div class="row d-flex justify-content-center">
                <div class= "form-group col-sm-4">
                    <label><h5>Data Inicial</h5></label>
                    <input type="date" name="data_inicial_consulta" id="data_inicial_consulta" class="form-control" required min="" max="">
                </div>

                <div class= "form-group col-sm-4">
                    <label><h5>Data Final</h5></label>
                    <input type="date" name="data_final_consulta" id="data_final_consulta" class="form-control" required min="" max="">
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <input type="submit" value="Consultar" class="btn btn-primary" id="consultar">
            </div>            
    </form>
</div>



<!-- Tabela de preços -->

    <div class="row-md d-flex justify-content-center mt-5">
        <div class="col-md-10" id="resultado_tabela">
                         


        </div>
    </div>
    <div class="empty_space"></div>


<script>

    //aqui alguém pode manipular um while e bloquear o ip do usuário
    $(document).ready(function(){

    $('#consultar').on('click', function(e){
        e.preventDefault();

        let valoresConsulta = {
            query_symbol: $('#query_symbol').val(),
            tempo_grafico_consulta: $('#tempo_grafico_consulta').val(),
            data_inicial_consulta: $('#data_inicial_consulta').val(),
            data_final_consulta: $('#data_final_consulta').val()
        };

        $.ajax({
            url: "back-ajax/consulta_tabela_jquery.php",
            type: "POST",
            data: valoresConsulta,
            success: function(resultado_pesquisa){
                console.log("sucesso");
                $('#resultado_tabela').html(resultado_pesquisa);

            },
            error: function(){
                console.log("Vish, deu erro");
            }
        });

    })


        // colocar data minima ou maxima
        $('#query_symbol').on('change', function(){

            let simboloConsulta = $('#query_symbol').val();

            $.ajax({
                    url: "back-ajax/consulta_jquery.php",
                    type: 'POST',
                    data: {symbol: simboloConsulta },
                    success: function(datas){

                        if(datas != 'vazio'){
                            console.log();
                            datas = datas.split('--');

                            console.log(datas[0]);
                            console.log(datas[1]);


                            $('#data_inicial_consulta').attr('min', datas[0]);
                            $('#data_inicial_consulta').attr('max', datas[1]);
                            $('#data_final_consulta').attr('min', datas[0]);
                            $('#data_final_consulta').attr('max', datas[1]);
                        }
                    },
                    error: function(){
                        console.log("Vish, deu erro");
                    }
                });
        });

});
</script>

<?php include_once "footer.php"; ?>