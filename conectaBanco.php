<?php
define('HOST','localhost');
define('USER','root');
define('PASS','');
define('DBNAME','agenda');
define('PORT','3306');


try{
    $conexao = new pdo('mysql: host=' . HOST . '; port=' . PORT. '; dbname=' . DBNAME, USER, PASS);
}catch(PDOException $exception){
    echo "Erro ao conectar ao banco: " . $exception->getMessage();
}
?>