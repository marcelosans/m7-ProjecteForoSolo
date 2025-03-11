<?php
 $cadena_connexio = 'mysql:dbname=m7_uf1;host=localhost:3335';
 $usuari = 'root';
 $passwd = '';
 try{
    //Ens connectem a la BDs
    $db = new PDO($cadena_connexio, $usuari, $passwd);
    //Tallem la connexió a la BDs
}catch(PDOException $e){
    echo 'Error amb la BDs: ' . $e->getMessage() . '<br>';
}


?>