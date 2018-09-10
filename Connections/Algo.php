<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"

//Need some type of timeout function
//$hostname_Algo = "localhost";
//$hostname_Algo = "destsvc.chourz5tgohk.us-east-1.rds.amazonaws.com:3306";
$hostname_Algo = "mysql.destinoanalytics.com:3306";
$database_Algo = "dinolytics_algo";
$username_Algo = "alexdino1";
$password_Algo = "Madison8";

//$Algo = mysql_pconnect($hostname_Algo, $username_Algo, $password_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
$Algo = mysqli_connect($hostname_Algo, $username_Algo, $password_Algo, $database_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
?>