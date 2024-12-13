<?php 
$host       = "localhost";
$user       = "root";
$pass       = "";
$db         = "db_hospital2";

$koneksi    = mysqli_connect($host,$user,$pass,$db);
if($koneksi){
    
    //echo("TERKONEKSI");
}//else echo ("Gagal terkoneksi");
?>