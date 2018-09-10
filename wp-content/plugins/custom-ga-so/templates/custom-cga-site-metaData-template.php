<?php
/*
 * Template: Custom Google Analytics Site MetaData Template
*/

//require_once realpath(CGA_PLUGIN_BASEPATH . '/includes/declaregapi.php');
require_once realpath(CGA_PLUGIN_BASEPATH . '/includes/gapi.class.php');
require_once realpath(CGA_PLUGIN_BASEPATH . '/tools/src/Google/autoload.php');

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
$gAuth = new Google_Auth();
$gAnalytics = new Google_Analytics();
If(isset($_SESSION['CGA_sessionData'])){
	$accessData = json_decode($_SESSION['CGA_sessionData']['token']);
	if(!$gAuth->isAccessTokenExpired()){
		$gaAccounts = $gAnalytics->getAccounts();
	}else{
		wp_redirect(get_bloginfo('url').'/ga-login');
	}
}else{
	wp_redirect(get_bloginfo('url').'/ga-login');
}

if(isset($_POST['DeleteFilter']))
{
    //deletefilter($_POST["filterkey"],$filter_a); could not get function to delete other values after completion

    $filterkey = $_POST["filterkey"];
    unset($filter_a[$filterkey]);
    $filter_a=array_values($filter_a);
    $filter="";
    foreach ($filter_a as $key => $value)
    {
        $filter .= $value . "&&";
    }
    $filter = substr($filter,0,strlen($filter)-2);
    runstdset($filter);
    $_SESSION['filter'] = serialize($filter_a);

}

if(isset($_POST['submit']))
{

    $client->setUseObjects(true);

    $startdatetime =  time();
    $currdatetime = date("Y-m-d H:i:s", $startdatetime);
    $acct = $_POST["selectAcct"];
    //echo $acct;
    //$dim = $_POST["selectDim"];
    $startdt = date("Y-m-d", strtotime($_POST["startdt"]));
    $enddt = date("Y-m-d", strtotime($_POST["enddt"]));
    $denset = 'Visits';
    $goal = $_POST["goal"];

    $gapidenset = "get" . $denset;
    $gapigoal = "get" . $goal;

    define('ga_profile_id',$acct);


    //$filter =$goal.">4";
    //unset($filter_a);
    //$filter_a[0]=$goal.">4";
    $filter="";

    //print_r($filter_a);

    if(isset($filter_a))
    {

        foreach ($filter_a as $key => $value)
        {
            $filter .= $value . "&&";
        }
        $filter = substr($filter,0,strlen($filter)-2);
    }

    else
    {
            $filter_a="";
    }
    //$ga->requestReportData(ga_profile_id,array($dim),array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);

    $_SESSION['acct'] = $acct;
    $_SESSION['startdt'] = $startdt;
    $_SESSION['enddt'] = $enddt;
    $_SESSION['filter'] = serialize($filter_a);

    mysqli_query($Algo,"truncate tbl_dim1_dim2_den_num");

    //start to get all major dim data


    $arr = array('date', 'source', 'medium', 'deviceCategory','mobileDeviceModel','campaign','landingPagePath','sessionCount','socialNetwork','browser','continent','region','flashVersion','operatingSystem','operatingSystemVersion','networkDomain','networkLocation','screenResolution','javaEnabled','language','yearWeek','searchUsed','userGender','userAgeBracket','interestOtherCategory','interestAffinityCategory','interestInMarketCategory','channelGrouping');

    foreach ($arr as $whichdim2) {
        $dimset[1]= $whichdim2;
        $gapidimset1 = "get" . $dimset[1];

        foreach ($arr as $whichdim1) {
            //$whichdim = $value;
            $dimset[0]=$whichdim1;
            $gapidimset0 = "get" . $dimset[0];

            $ga->requestReportData($acct,$dimset,array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);
            $middatetime =  time();

            foreach($ga->getResults() as $result)
            {
                    mysqli_query($Algo,"insert into tbl_dim1_dim2_den_num(whichdim1,dim1,whichdim2,dim2,den,num) VALUES ('" . $whichdim1. "','" . $result->$gapidimset0(). "','" . $whichdim2. "','" . $result->$gapidimset1(). "','" . $result->$gapidenset() . "','" . $result->$gapigoal() . "')");
            }
        }
    }

    echo "<br/>Done!</br>";
    //time();
    $enddatetime = time();
    $midduration = $middatetime - $startdatetime;
    $duration = $enddatetime - $startdatetime;
    mysqli_query($Algo,"insert into tbl_code_run_log(typeid,DateTime,goal,denset,dim,acct,filter,stdate,enddate,midduration,duration) VALUES ('6','" . $currdatetime . "','" . $goal . "','" . $denset . "','" . $dim . "','" . $acct . "','" . $filter ."','". $startdt ."','". $enddt ."','" . $midduration ."','" . $duration . "')");

} 

get_header();?>

<div id="content-wrap" class="container clr">

<?php

if(!empty($gaAccounts)){
	if(isset($_GET['account']) && $_GET['account'] != '' && isset($_GET['property']) && $_GET['property'] != '' ){
		$gAnalytics->getPropertyViews($_GET['account'], $_GET['property'], $_GET['days']);
	}
}



if(isset($filter_a))
{
    //print filter table
    echo "==Filters==";
    echo "<form id='frmFilters' method='post'>";
    echo "<table>";
    $i=0;
    $filter="";
    $filter_a;
    foreach ($filter_a as $key => $value)
    {
        echo "<tr><td>".$key."</td><td>".$value."</td><td><input type='hidden' id='filterkey' name='filterkey' value='".$key."'/><input type='submit' name='DeleteFilter' value='Delete'/>"."</td></tr>";          
        $filter .= $value . "&&";
    }
    echo "</table>";
    $filter = substr($filter,0,strlen($filter)-2);

    echo "</form>"; 
}

echo "cool <br/>";

//include realpath(CGA_PLUGIN_BASEPATH . '/includes/goalselectionform.php');

?>
</div><!-- .container -->
<?php 
get_footer();
?>
