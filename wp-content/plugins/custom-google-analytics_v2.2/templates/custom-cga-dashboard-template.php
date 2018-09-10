<?php
/*
 * Template:       Custom Google Analytics Dashboard Template
*/



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
get_header();

if(!empty($gaAccounts)){
	$cgaDashSetting = get_option('cga_dashSetting');
	if($cgaDashSetting != '')
		$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting));
	if(!empty($cgaDashSetting)){
		$siteMetaPageID = $cgaDashSetting['setting']['siteMetaPage'];
		$siteMetaPageURL = get_permalink($siteMetaPageID);
	}
	echo '<form action="" method="GET" class="account_form">
		Choose Account';
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
		$days = 30;
		if((isset($_GET['startDate']) && $_GET['startDate'] != '') && (isset($_GET['endDate']) && $_GET['endDate'] != '')){
			$days =  floor((strtotime($_GET['endDate'])-strtotime($_GET['startDate']))/(60*60*24));
		}
		$gaPropereties = $gAnalytics->getListProperties($_GET['account'], $days);
		//echo "<pre>"; print_r($gaPropereties); echo "</pre>";
		if(!empty($gaPropereties)){?>
			<div class="filters">
				<span><?php if((isset($_GET['startDate']) && $_GET['startDate'] != '') && (isset($_GET['endDate']) && $_GET['endDate'] != '')){
						echo date('F d, Y', strtotime($_GET['startDate'])).' - '.date('F d, Y', strtotime($_GET['endDate']));
					}else{
						echo date('F d, Y', strtotime('-30 days')).' - '.date('F d, Y');
					} ?>  &#9660;</span>
				<div class="filterBlock">
					<form action="" method="GET">
						<input type="hidden" name="account" value="<?php echo $_GET['account']; ?>">
						<input type="text" id="startDate" name="startDate" placeholder="Start Date" value="<?php echo $_GET['startDate']; ?>">
						<input type="text" id="endDate" name="endDate" placeholder="End Date" value="<?php echo $_GET['endDate']; ?>">
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
						<td><a href="<?php echo $siteMetaPageURL.'?account='.$_GET['account'].'&property='.$item['id'].'&propertyname='.$item['name'].'&days='.$days ?>"><?php echo $item['name'].' - '.$item['id'] ?></a></td>
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
get_footer();
