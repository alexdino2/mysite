<?php
//require_once('Connections/Algo.php'); 
include realpath(CGA_PLUGIN_BASEPATH . '/includes/functions3.php'); 

?>

<link type="text/css" href="/assets/css/pepper-grinder/jquery-ui-1.8.14.custom.css" rel="Stylesheet" />	
<script type="text/javascript" src="/assets/js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery-ui-1.8.14.custom.min.js"></script>
<link rel="stylesheet" href="/assets/css/style.css" type="text/css"/>


<?php


  //$profiles = $analytics->management_profiles->listManagementProfiles("~all", "~all");
  //$rows=$profiles->getItems();


?>

 

<form action="" method="post" id="MainInput" title="Goal Selection">

<?php
    
/*
    echo ("<select name='selectAcct' id='selectAcct'>");

    //loop through each profile and store in each dropdown
      foreach($rows as $r)
           {
               //echo ($r['id'] . " " . $r['name'] );
               echo ($r->getid() . " " . $r->getname() ); 
               //echo ("<option value=" . $r['id'] . " " . checkdefaultprofile($r['id']) . ">" . $r['name'] . "</option>");
               echo ("<option value=" . $r->getid() . " " . checkdefaultprofile($r->getid()) . ">" . $r->getname() . "</option>");
               //echo $result . ' (' . $result->getProfileId() . ")<br />";
           }

    echo "</select>";
  */
  //$acct_I_want = $segments->items->profileId;


    //get defaults
    if(!isset($startdt))
    {   
        if(isset($_SESSION['startdt']))
        {
            $startdt = date("m/d/Y", strtotime($_SESSION['startdt']));

        }   
        else
        {
            $startdt = date("m/d/Y", time() - 3000000);
        }
    }
    if(!isset($enddt))
    {   
        if(isset($_SESSION['enddt']))
        {
            $enddt = date("m/d/Y", strtotime($_SESSION['enddt']));

        }   
        else
        {
            $enddt = date("m/d/Y", time() - 86400);
        }
    }
    

    if(!isset($dim))
    {
        if(isset($_COOKIE['dim']))
        {
            $dim = $_COOKIE['dim'];
        }
        
    }

    if(!isset($acct))
    {
        if (isset($_COOKIE['acct']))
        {
            $acct = $_COOKIE['acct'];
        }
        else 
        {
            $acct = "";
        }
    //}

    } /*else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
    }*/
    
?>


    <br/>
        <p>Start Date: <input type="text" id="startdt" name="startdt" value=<?php echo date("m/d/Y", strtotime($startdt)); ?>></p>
        <p>End Date: <input type="text" id="enddt" name="enddt" value=<?php echo date("m/d/Y", strtotime($enddt)); ?>></p>

        <p>Goal: <input type="radio" id="goal" name="goal" value="goalCompletionsAll" <?php $sel = checkdefaultgoal("goalCompletionsAll"); echo $sel;?>/>All Goal Completions<br/>
            <input type="radio" id="goal" name="goal" value="goal1Completions" <?php $sel = checkdefaultgoal("goal1Completions"); echo $sel;?>/>Goal 1 Completions<br/>
            <input type="radio" id="goal" name="goal" value="goalStartsAll" <?php $sel = checkdefaultgoal("goalStartsAll"); echo $sel;?>/> All Goal Starts<br/>
            <input type="radio" id="goal" name="goal" value="pageloadtime" <?php $sel = checkdefaultgoal("pageloadtime"); echo $sel;?>/> Page Load Time<br/>   
            <input type="radio" id="goal" name="goal" value="timeonsite" <?php $sel = checkdefaultgoal("timeonsite"); echo $sel;?>/> Time on Site<br/>
            <input type="radio" id="goal" name="goal" value="bounces" <?php $sel = checkdefaultgoal("bounces"); echo $sel;?>/> Bounces<br/>
        </p>
        
  
    <input type="submit" name="submit" value="Submit"/>
</form>

<script>
	$(function() {
		$( "#startdt" ).datepicker({showWeek: true, firstDay: 1});
                $( "#enddt" ).datepicker({showWeek: true, firstDay: 1});
	});
</script>

<script>
    $(document).ready(function()
      { 
      $('#MainInput').formly({'theme':'Dark'});
      }
  );
</script>

