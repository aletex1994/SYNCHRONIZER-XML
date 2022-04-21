<?php

//COLLEGAMENTO AL DB

$host='your_db';
$user='tex1994';
$psw='yourpsw';
$db='DATABASE';

$link=new mysqli($host,$user,$psw,$db);

if (mysqli_connect_errno()){
    die (mysqli_connect_error());
}


?>
