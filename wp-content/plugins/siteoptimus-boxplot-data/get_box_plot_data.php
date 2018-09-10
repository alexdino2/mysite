<?php 
/**
 * Plugin Name: Site Optimus Get Boxplot Data
 * Plugin URI: http://siteoptimus.com
 * Description: This plugin adds some boxplot Data to Site Optimus
 * Version: 1.0.0
 * Author: Alex Destino
 * Author URI: http://alexdestino.com
 * License: GPL2
 */

$response = array();

/* 
// connecting to db
$hostname_Algo = "wordpress.cdc9etng7jk2.us-east-1.rds.amazonaws.com:3306";
$database_Algo = "wordpress";
$username_Algo = "alexdino1";
$password_Algo = "Madison8";
*/

global $wpdb;

//$Algo = mysql_pconnect($hostname_Algo, $username_Algo, $password_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
//$Algo = mysqli_connect($hostname_Algo, $username_Algo, $password_Algo, $database_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
 
$sql="SELECT  dim2,
        max(c_rate) as max,
        SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                GROUP_CONCAT(                 -- 1) make a sorted list of values
                    f.c_rate
                    ORDER BY f.c_rate
                    SEPARATOR ','
                )
            ,   ','                           -- 2) cut at the comma
            ,   75/100 * COUNT(*) + 1         --    at the position beyond the 90% portion
            )
        ,   ','                               -- 3) cut at the comma
        ,   -1                                --    right after the desired list entry
        )                 AS `75th Percentile`,
        SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                GROUP_CONCAT(                 -- 1) make a sorted list of values
                    f.c_rate
                    ORDER BY f.c_rate
                    SEPARATOR ','
                )
            ,   ','                           -- 2) cut at the comma
            ,   50/100 * COUNT(*) + 1         --    at the position beyond the 90% portion
            )
        ,   ','                               -- 3) cut at the comma
        ,   -1                                --    right after the desired list entry
        )                 AS `50th Percentile`,
        SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                GROUP_CONCAT(                 -- 1) make a sorted list of values
                    f.c_rate
                    ORDER BY f.c_rate
                    SEPARATOR ','
                )
            ,   ','                           -- 2) cut at the comma
            ,   25/100 * COUNT(*) + 1         --    at the position beyond the 90% portion
            )
        ,   ','                               -- 3) cut at the comma
        ,   -1                                --    right after the desired list entry
        )                 AS `25th Percentile`,
        min(c_rate) as min
FROM    (select dim2, den, num, num/den as c_rate from tbl_dim1_dim2_den_num where den>0) AS f
GROUP BY dim2";

$so_result=$wpdb->get_results($sql) or die(mysql_error());

// get summary data from 2 dim table
//$result = mysqli_query($Algo,"SELECT num/den as c_rate FROM tbl_dim1_dim2_den_num") or die(mysql_error());
//$result = mysqli_query($Algo,$sql) or die(mysql_error());
//print_r($result);
//$records = mysqli_fetch_all ($result, MYSQLI_ASSOC);
//echo json_encode($records );

/*
// check for empty result
if (num_rows($so_result)>0) {
    // looping through all results
    $so_response = array();
 
    foreach($so_result as $so_row) {
        // temp user array
        $fivenum = array();
        $fivenum["x"] = $so_row["dim2"];
        $fivenum["low"] = $so_row["min"];
        $fivenum["q1"] = $so_row["25th Percentile"];
        $fivenum["median"] = $so_row["50th Percentile"];
        $fivenum["q3"] = $so_row["75th Percentile"];
        $fivenum["high"] = $so_row["max"];
 
        // push single product into final response array
        array_push($so_response, $fivenum);
    }
    // success
    //$so_response["success"] = 1;
 
    // echoing JSON response
    //echo json_encode($so_response, JSON_NUMERIC_CHECK);
} else {
    // no products found
    $so_response["success"] = 0;
    $so_response["message"] = "No records found";
 
    // echo no users JSON
    //echo json_encode($so_response);
}
*/
    
?>