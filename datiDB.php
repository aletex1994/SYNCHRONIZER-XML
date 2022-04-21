<?php

//COLLEGAMENTO AL DB

$host='localhost';
$user='root';
$psw='qwedcvbnm';
$db='woocomm';

$link=new mysqli($host,$user,$psw,$db);

if (mysqli_connect_errno()){
    die (mysqli_connect_error());
}


?>