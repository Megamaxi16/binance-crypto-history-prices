<?php
    if(!isset($_SESSION["simbolos"])){
    pega_simbolos($conexao);
    }
    
    if(!isset($_SESSION["simbolos"])){
        pega_simbolos_ativos($conexao);
    }

    

?>