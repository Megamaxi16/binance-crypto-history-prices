<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão se ainda não foi iniciada
}

header('Access-Control-Allow-Origin: *');
set_time_limit(14400);
require "functions.php";
require "conecta_banco.php";
require "iniciar.php";

$hoje = hoje();

//começa o front
include_once "head.php"?>
<title>Home</title>
<body>
<?php include "header.php"; ?>

<div class="container">

    <form method="post">

        <div class= "form-group">
            <label>Par de Criptomoeda</label>
            <select class="form-control" id="symbol" name="symbol" required>
                <option value="">Selecione...</option>
                <?php 
                    foreach($_SESSION["simbolos"] as $simbolo){
                        echo "<option value=".$simbolo.">".$simbolo."</option>";
                    }                
                ?>
            </select>
        </div>
        
            <!-- Add novas criptos -->
            <div class="d-flex justify-content-center">
            <a class="btn btn-primary" href="add_symbol.php">Adicione Novo Par</a>
            </div>

        <div class= "form-group">
            <label>Escolha o tempo gráfico: </label>
            <select name="tempo_grafico" class="form-control" id="tempo_grafico">
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

        <div class= "form-group">
            <label>Data Inicial:</label>
            <input type="date" name="data_inicial" id="data_inicial" class="form-control" required max="<?= $hoje ?>">
        </div>

        <div class= "form-group">
            <label>Data Final:</label>
            <input type="date" name="data_final" id="data_final" class="form-control" required max="<?= $hoje ?>">
        </div>


            <input type="submit" value="enviar" class="btn btn-primary" id="enviar">


    </form>
</div>

<div id="caixinha" style="background-color:aquamarine;">



</div>


<script>

    $(document).ready(function(){

        $('#enviar').on('click', function(e){
            e.preventDefault();

            let valoresArqueologo = {
                symbol: $('#symbol').val(),
                tempo_grafico: $('#tempo_grafico').val(),
                data_inicial: $('#data_inicial').val(),
                data_final: $('#data_final').val()
            };

            $.ajax({
                url: "back-ajax/gerador_jquery.php",
                type: "POST",
                data: valoresArqueologo,
                success: function(mensagem){
                    $('#caixinha').html(mensagem);

                },
                error: function(){
                    console.log("Vish, deu erro");
                }
            });

        })



        // verificar que simbolo já existe
        $('#symbol').on('change', function(){

            let simboloPesquisa = $('#symbol').val();

            //let url = "https://api.binance.com/api/v3/klines/?symbol="+ simboloPesquisa + "&interval=1d&startTime=0&limit=1"

            $.ajax({
                url: "back-ajax/gerador_datas_jquery.php",
                type: 'POST',
                data: {symbol: simboloPesquisa },
                success: function(primeiraData){

                    $('#data_inicial').attr('min', primeiraData);
                    $('#data_final').attr('min', primeiraData)

                },
                error: function(){
                    console.log("Vish, deu erro");
                }
            });
        });
    });

</script>
<?php include_once "footer.php"; ?>