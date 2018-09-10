<?php
require('./wp-load.php');
get_header();
?>

<?php
 
/*
 * Following code will get single product details
 * A product is identified by product id (pid)
 */


// array for JSON response
$response = array();
 
// include db connect class
//require_once __DIR__ . '/db_connect.php';
 
// connecting to db
require_once('Connections/Algo.php');
 
// get all products from products table
$result = mysqli_query($Algo,"SELECT * FROM vMasterPages") or die(mysql_error());
 
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    //$response["products"] = array();
    $response = array();
 
    while ($row = mysqli_fetch_array($result)) {
        // temp user array
        $product = array();
        $product["key"] = $row["pagePath"];
        $product["region"] = $row["pagePathLevel1"];
        $product["subregion"] = $row["pagePathLevel2"];
        $product["value"] = $row["sessions"];
 
        // push single product into final response array
        array_push($response, $product);
    }
    // success
    //$response["success"] = 1;
 
    // echoing JSON response
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No products found";
 
    // echo no users JSON
    echo json_encode($response);
}

    
?>


<?php get_footer(); ?>