<?php

function checkdefault($dimrow)
{
    global $dim;
    if ($dimrow == $dim){
            $result = "selected";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function checkdefaultprofile($profileid)
{
    global $acct;
    if ($profileid == $acct){
            $result = "selected";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function checkdefaultgoal($goalrow)
{
    global $goal;
    if ($goalrow == $goal){
            $result = "checked";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function checkdefaultdenset($densetrow)
{
    global $denset;
    if ($densetrow == $denset){
            $result = "checked";
    }
            else
    {
            //return '';
            $result = "";

    }
    return $result;
}

function getfilter($var){
	$filter_a = array();
        if (isset($_SESSION[$var])){
                    return $_SESSION[$var];
                    $filter_a = unserialize($_SESSION['filter']);
        }
        else
        {
                if (isset($_COOKIE[$var])){
                        $var = $_COOKIE[$var];
                        if($var='filter'){
                        $filter_a = unserialize($_COOKIE['filter']);
                        }
                }
                else {
                        $var = ""; //need to change
                        //$acct = $_POST["selectAcct"];
                }
                
        }
    
}

function getvar($var)
{

    if(!isset($var))
    {
            
        if (isset($_SESSION[$var])){
                    $var = $_SESSION[$var];
        }
        else
        {
                if (isset($_COOKIE[$var])){
                        $var = $_COOKIE[$var];
                }
                else {
                        $var = ""; //need to change
                        //$acct = $_POST["selectAcct"];
                }
                
        }
    }
    return $var;
}

function setvar($var)
{
	if($var='filter'){
		$filter_a = array();
	}
    if(!isset($var))
    {
            
        if (isset($_SESSION[$var])){
                    $acct = $_SESSION[$var];
                    if($var='filter'){
                    	$filter_a = unserialize($_SESSION['filter']); 
                    }
        }
        else
        {
                if (isset($_COOKIE[$var])){
                        $acct = $_COOKIE[$var];
                        if($var='filter'){
                        	$filter_a = unserialize($_COOKIE['filter']);
                        }
                }
                else {
                        $acct = ""; //need to change
                        //$acct = $_POST["selectAcct"];
                }
                
        }
    }
}

function progressBar($percentage) {
	print "<div id=\"progress-bar\" class=\"all-rounded\">\n";
	print "<div id=\"progress-bar-percentage\" class=\"all-rounded\" style=\"width: $percentage%\">";
		if ($percentage > 5) {print "$percentage%";} else {print "<div class=\"spacer\">&nbsp;</div>";}
	print "</div></div>";
}

function runstdset($filter)
{
        $hostname_Algo = "mysql.destinoanalytics.com:3306";
        $database_Algo = "dinolytics_algo";
        $username_Algo = "alexdino1";
        $password_Algo = "Madison8";

        //$Algo = mysql_pconnect($hostname_Algo, $username_Algo, $password_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
        $Algo = mysqli_connect($hostname_Algo, $username_Algo, $password_Algo, $database_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
        
        $startdatetime =  time();
        $currdatetime = date("Y-m-d H:i:s", $startdatetime);

        //need to modify filter so that it keeps adding filters
        //need to hide additional submit area
        global $acct;
        $acct=getvar($acct);

        global $dim;
        $dim=getvar($dim);
        echo $dim;
        
        global $whichdim;
        $whichdim=getvar($whichdim);
        echo $whichdim;
        
        global $goal;
        $goal = getvar($goal);

        global $startdt;
        $startdt = getvar($startdt);
        global $enddt;
        $enddt = getvar($enddt);

        global $denset;
        $gapidenset = "get" . $denset;
        global $gapigoal;
        $gapigoal = "get" . $goal;

        //$startdt = "2011-01-01";
        //$enddt = "2011-05-30";

        define('ga_profile_id',$acct);

        global $ga;
        $ga->requestReportData(ga_profile_id,array($dim),array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);
        $middatetime =  time();

        /*
        $base = "d:\wamp\www\Destino\JAVA\extract_dim_den_num.bat";
        $param = $base . " " . $acct . " " .$dim . " " . $startdt . " " . $enddt;
        setcookie('dim',$dim,false,"/",false); //works with localhost only
        setcookie('acct',$acct,false,"/",false); //works with localhost only
        setcookie('filter',$filter,false,"/",false); //works with localhost only
        */

        // Open Database Connection 
        //mysqli_connect("destsvc.chourz5tgohk.us-east-1.rds.amazonaws.com:3306", "alexdino1", "Madison8") or die(mysql_error()); 
        //mysqli_select_db("algo") or die(mysql_error()); 
        mysqli_query($Algo,"truncate tbl_dim_den_num");

        foreach($ga->getResults() as $result){
                mysqli_query($Algo,"insert into tbl_dim_den_num(whichdim,dim,den,num) VALUES ('" . $whichdim. "','" . $result. "','" . $result->$gapidenset() . "','" . $result->$gapigoal() . "')");
        }

        $enddatetime = time();
        $midduration = $middatetime - $startdatetime;
        $duration = $enddatetime - $startdatetime;
        mysqli_query($Algo,"insert into tbl_code_run_log(typeid,DateTime,goal,dim,acct,filter,midduration,duration) VALUES ('2','" . $currdatetime . "','" . $goal . "','" . $dim . "','" . $acct . "','" . $filter."','" . $midduration ."','" . $duration . "')");
        return true;
}
//could not get this function to work - intended to cycle dimensions
function appenddimset($ga,$Algo,$acct,$whichdim,$denset,$goal,$filter,$startdt,$enddt)
{

    $ga->requestReportData($acct,array($whichdim),array($denset,$goal),'-' . $goal,$filter,$startdt,$enddt);
    $middatetime =  time();

    foreach($ga->getResults() as $result)
        {
            mysqli_query($Algo,"insert into tbl_dim_den_num(whichdim,dim,den,num) VALUES ('" . $whichdim. "','" . $result. "','" . $result->$gapidenset() . "','" . $result->$gapigoal() . "')");
        }

}

//version 3 - converted mysql to mysqli
//version 4 - added function to cycle through dims
?>