<?php

require "functions.php";


    $comeco_url = "https://api.binance.com/api/v3/klines";

    $resultado = chama_api($comeco_url, "BTCUSDTasd", "1d", 0, 1);

    if(!is_array($resultado)){
        echo "Não é array";
    }

    else{
        //echo $resultado[0][0];
    echo date("Y-m-d", $resultado[0][0]/1000);
    }


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sobre</title>
    <link rel="icon" sizes="16x16" href="images/bitcoin_logo_white_transparent_16x16.png">
    <link rel="icon" sizes="32x32" href="images/bitcoin_logo_white_transparent_32x32.png">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include "header.php"; ?>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script src="js\jquery-3.7.1.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>