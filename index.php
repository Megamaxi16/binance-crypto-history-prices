<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão se ainda não foi iniciada
}
set_time_limit(14400);
require_once "functions.php";
require_once "conecta_banco.php";
require "iniciar.php";

$hoje = hoje();

//começa o front
include_once "head.php"?>
<title>Home</title>
<body>
<?php include "header.php"; ?>

<div class="conteiner-flex">
    <h1 class="titulo"> Criptomoedas Registradas: </h1>
</div>


<div class="container">
    <div class="row g-3 d-flex"> 
        
        <!--<div class="col-md-4 justify-content-center conteiner-pai-cripto ">
            <div class="conteiner-criptos">
                <h3>Criptomoeda</h3>
                <p>Primeira Vela: </p>
                <p>Ultima Vela: </p>
                <p>Total de Registros: </p>
            </div>    
        </div>-->

        <?php
        if(isset($_SESSION["dados_ativos"]) && !empty($_SESSION["dados_ativos"])){
        foreach ($_SESSION["dados_ativos"] as $cripto_ativa => $dados){?>

            
            <div class="col-md-4 justify-content-center conteiner-pai-cripto ">
                <div class="conteiner-criptos">
                    <h3><?= $dados["simbolo"] ?></h3>
                    <p>Primeira Vela: <?=$dados["dt_inicial"]?></p>
                    <p>Ultima Vela: <?=$dados["dt_final"]?></p>
                    <p>Total de Registros: <?=$dados["total_registros"]?></p>
                </div>    
            </div>



        <?php }
        }
        
        else{
            echo '<h3>Nenhuma criptomoeda encontrada ainda, clique <a href="gerador.php">aqui</a> para adicionar sua primeira</h3>';
        }

        ?>

    </div>
</div>


<script>

    $(document).ready(function(){

        $('#symbol').on('change', function(){

            let simboloPesquisa = $('#symbol').val();

            //let url = "https://api.binance.com/api/v3/klines/?symbol="+ simboloPesquisa + "&interval=1d&startTime=0&limit=1"

            $.ajax({
                url: "back-ajax/index_jquery.php",
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
<?php include_once "footer.php"; 
session_destroy();?>