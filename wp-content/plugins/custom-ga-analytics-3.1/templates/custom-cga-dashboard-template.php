<?php
/*
 * Template:       Custom Google Analytics Dashboard Template
*/

$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
$gAuth = new Google_Auth();
$gAnalytics = new Google_Analytics();
If(isset($_SESSION['CGA_sessionData'])){
	$accessData = json_decode($_SESSION['CGA_sessionData']['token']);
        $user_id = $mem->get('userid');
	if(!$gAuth->isAccessTokenExpired()){
		$gaAccounts = $gAnalytics->getAccounts();
                $mem->set($user_id.'accts',$gaAccounts);
	}else{
		wp_redirect(get_bloginfo('url').'/ga-login');
	}
}else{
	wp_redirect(get_bloginfo('url').'/ga-login');
}
get_header();
?>
	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content clr">

				<?php wpex_hook_content_top(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wpex_get_template_part( 'page_single_blocks' ); ?>

				<?php endwhile; ?>

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->



<?php
$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

if ( is_user_logged_in() ) {
        // Current user is logged in,
        // so let's get current user info
        global $current_user;
        $current_user = wp_get_current_user();
        // User ID
        $user_id = $current_user->ID;
        $mem->set('userid',$user_id);
}
                
if(!empty($gaAccounts)){
	$cgaDashSetting = get_option('cga_dashSetting');
	if($cgaDashSetting != '')
		$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting));
	if(!empty($cgaDashSetting)){
		//$siteMetaPageID = $cgaDashSetting['setting']['siteMetaPage'];
                $goalCyclePageID = $cgaDashSetting['setting']['goalCyclePage']; //change to redirect to the goal cycle
		//$siteMetaPageURL = get_permalink($siteMetaPageID);
                $goalCyclePageURL = get_permalink($goalCyclePageID); //change to redirect to the goal cycle
	}
	//echo 'Select the account you would like to analyze. You can go back here to analyze multiple accounts.</br></br>';
        echo '<form action="" method="GET" class="account_form">
		Select the domain that you would like to analyze';
		echo '<select name="account">
			<option value="">Select Account</option>';
		foreach($gaAccounts as $item){
			if($_GET['account'] == $item['id'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			echo '<option value="'.$item['id'].'" '.$selected.'>'.$item['name'].' - '.$item['id'].'</option>';
		}
		echo '</select>';
		echo '<input type="submit" value="submit">
	</form>';
	if(isset($_GET['account']) && $_GET['account'] != ''){
		$days = 7;
		if(isset($_GET['startDate']) && $_GET['startDate'] != '') {
			//$days =  floor((strtotime($_GET['endDate'])-strtotime($_GET['startDate']))/(60*60*24));
                        //$startdate = date_format($_GET['startDate'],'Y-m-d');
                        $startdate = date('Y-m-d', strtotime($_GET['startDate']));
                        //$enddate = date_format($_GET['endDate'],'Y-m-d');
                        //$enddate = date('Y-m-d', strtotime($_GET['endDate']));
                }
                elseif (($mem->get($user_id.'startdate')) && $_GET['account'] != ''){
                        $startdate = $mem->get($user_id.'startdate');
                }
                else {
                        $startdate = date('Y-m-d', strtotime('-8 days'));
                        $mem->set($user_id.'startdate',$startdate);
                }
                if(isset($_GET['endDate']) && $_GET['endDate'] != ''){
			//$days =  floor((strtotime($_GET['endDate'])-strtotime($_GET['startDate']))/(60*60*24));
                        //$startdate = date_format($_GET['startDate'],'Y-m-d');
                        //$startdate = date('Y-m-d', strtotime($_GET['startDate']));
                        //$enddate = date_format($_GET['endDate'],'Y-m-d');
                        $enddate = date('Y-m-d', strtotime($_GET['endDate']));
                }
                elseif (($mem->get($user_id.'enddate')) && $_GET['account'] != ''){
                        $enddate = $mem->get($user_id.'enddate');
                }
                else {
                        $enddate = date('Y-m-d', strtotime('-1 days'));
                        $mem->set($user_id.'enddate',$enddate);
                }
		$gaPropereties = $gAnalytics->getListProperties($_GET['account'], $days);
                //print_r($gaPropereties);
                $mem->set($user_id.'sessions',$gaPropereties[0]['metaData']['sessions']);
                //$mem->set($user_id.'startdate',$startdate);
                //$mem->set($user_id.'enddate',$enddate);
                $mem->quit();
                
		//echo "<pre>"; print_r($gaPropereties); echo "</pre>";
		if(!empty($gaPropereties)){?>
			<div class="filters">
				<span><?php echo $startdate.' - '.$enddate;
                                    //if((isset($_GET['startDate']) && $_GET['startDate'] != '') && (isset($_GET['endDate']) && $_GET['endDate'] != '')){
						//echo date('Y-m-d', strtotime($_GET['startDate'])).' - '.date('Y-m-d', strtotime($_GET['endDate']));
					//}else{
						//echo date('Y-m-d', strtotime('-30 days')).' - '.date('Y-m-d');
					//} ?>  &#9660;</span>
				<div class="filterBlock">
					<form action="" method="GET">
						<input type="hidden" name="account" value="<?php echo $_GET['account']; ?>">
                                                <input type="text" id="startDate" name="startDate" placeholder="Start Date" value="<?php echo $startdate//echo date('Y-m-d', strtotime($_GET['startDate'])); ?>">
						<input type="text" id="endDate" name="endDate" placeholder="End Date" value="<?php echo $enddate; ?>">
						<input type="submit" value="Apply">
					</form>
				</div>
			</div>
			<table id="gaSiteMetaData" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Site</th>
						<th>Sessions</th>
						<th>Avg. Session Duration</th>
						<th>Bounce Rate</th>
						<th>Goal Conversion Rate</th>
					</tr>
				</thead>
				<?php foreach($gaPropereties as $item){ ?>
					<tr>
						<td><a href="<?php echo $goalCyclePageURL.'?account='.$_GET['account'].'&property='.$item['id'].'&propertyname='.$item['name'].'&startDate='.$startdate.'&endDate='.$enddate //changed url from $siteMetaPageURL ?>"><?php echo $item['name'].' - '.$item['id'] ?></a></td>
						<td><?php echo $item['metaData']['sessions']; ?></td>
						<td><?php echo $item['metaData']['sessionDuration']; ?></td>
						<td><?php echo $item['metaData']['bounceRate']; ?></td>
						<td><?php echo $item['metaData']['goalConversionRate']; ?></td>
					</tr>
				<?php } ?>
			</table>
		<?php }
	}
}
?>
			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->
        
<?php
get_footer();
