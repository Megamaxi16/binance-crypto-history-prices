<?php
define('nome_servidor', 'localhost');
define('user','root');
define('senha','');
define('nome_banco','historico_cripto');

$conexao = mysqli_connect(nome_servidor, user, senha, nome_banco);

if(!$conexao){
die('deu erro, vc é burro'. mysqli_connect_error());
}
?>