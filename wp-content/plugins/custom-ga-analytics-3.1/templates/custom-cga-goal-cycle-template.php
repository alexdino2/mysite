<?php
/*
 * Template:       Custom Google Analytics Goal Cycle Template
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

//echo '<a href="http://siteoptimus.com/site-optimus-dashboard/#">Analyze different domain</a></br></br>';
    
if(!empty($gaAccounts)){
	$cgaDashSetting = get_option('cga_dashSetting');
	if($cgaDashSetting != '')
		$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting));
	if(!empty($cgaDashSetting)){
		$siteMetaPageID = $cgaDashSetting['setting']['goalCycleMetaPage'];
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
            $properties = $gAnalytics->getListGoals($_GET['account'],'2018-01-01','2018-03-14');
            //print_r($properties);
            $days = 30;
		if((isset($_GET['startDate']) && $_GET['startDate'] != '') && (isset($_GET['endDate']) && $_GET['endDate'] != '')){
		$startdate=$_GET['startDate'];
                $enddate=$_GET['endDate'];
                    $days =  floor((strtotime($_GET['endDate'])-strtotime($_GET['startDate']))/(60*60*24));
		}
		$gaPropereties = $gAnalytics->getListGoals($_GET['account'], $startdate, $enddate);
		//echo "<pre>"; print_r($gaPropereties); echo "</pre>";
		//$gaPropereties = $gaPropereties['modelData']['items'];
		//echo $gaPropereties['profileId'];
		//echo "<pre>"; print_r($gaPropereties[2]['metaData']['modelData']['items'][0]['name']); echo "</pre>";
		
		//$itemarray = $gaPropereties[2]['metaData']['modelData']['items'];

		if(!empty($gaPropereties)){?>
			<div class="filters">
				<span><?php if((isset($_GET['startDate']) && $_GET['startDate'] != '') && (isset($_GET['endDate']) && $_GET['endDate'] != '')){
						echo date('Y-m-d', strtotime($_GET['startDate'])).' - '.date('Y-m-d', strtotime($_GET['endDate']));
					}else{
						echo date('Y-m-d', strtotime('-30 days')).' - '.date('Y-m-d');
					} ?>  &#9660;</span>
				<div class="filterBlock">
					<form action="" method="GET">
						<input type="hidden" name="account" value="<?php echo $_GET['account']; ?>">
						<input type="text" id="startDate" name="startDate" placeholder="Start Date" value="<?php echo date('Y-m-d', strtotime($_GET['startDate'])); ?>">
						<input type="text" id="endDate" name="endDate" placeholder="End Date" value="<?php echo date('Y-m-d', strtotime($_GET['endDate'])); ?>">
						<input type="submit" value="Apply">
					</form>
				</div>
			</div>
			<table id="gaSiteGoalData" class="display dataTable no-footer" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Site</th>
						<th>Goal Sessions</th>
						<th>Goal Completions</th>
						<th>Goal Conversion Rate</th>
						<th>Goal Value</th>
					</tr>
				</thead>
				<?php 
				$i=0;
				foreach($gaPropereties as $item){ 
					
				?>
					<tr role="row" class="odd">
						<td class="sorting_1"><a href="<?php echo $siteMetaPageURL.'?account='.$_GET['account'].'&property='.$item['id'].'&propertyname='.$item['name'].'&days='.$days.'&profileId='.$item['profileID'] ?>"><?php echo $item['name'].' - '.$_GET['account']; ?></a></td>
						<td>&nbsp;<?php echo $item['metaResult']['sessions']; ?></td>
						<td>&nbsp;<?php //echo $item['metaData']['sessions']; ?></td>
						<td>&nbsp;<?php //echo $item['metaData']['goalConversionRate']; ?></td>
						<td>&nbsp;<?php //echo '$'.$item['value']; ?></td>
					</tr>
					<?php 
					$itemArray = $item['metaData']['modelData']['items'];
					
					$j = 1;
					$k = 0;
					$totalGoalCompletion = 0;
					foreach($itemArray as $data){ 
					$id = $data['id'];
					
					$totalGoalCompletion = $totalGoalCompletion + $item['goalData'][$k]['totalsForAllResults']['ga:goal'.$id.'Completions'];
					$session = $item['goalData'][$k]['totalsForAllResults']['ga:sessions'];
                                        $mem->set('goal'.$id.'completes',$totalGoalCompletion);
                                        $mem->set('totalsessions',$session);
					?>
					<tr>
						<td ><a href="<?php echo $siteMetaPageURL.'?account='.$_GET['account'].'&property='.$item['id'].'&propertyname='.$item['name'].'&days='.$days.'&profileId='.$item['profileID'].'&goalID='.$id.'&startDate='.date('Y-m-d', strtotime($_GET['startDate'])).'&goalcompletes='.$item['goalData'][$k]['totalsForAllResults']['ga:goal'.$id.'Completions'].'&sessions='.$item['metaResult']['sessions'].'&endDate='.date('Y-m-d', strtotime($_GET['endDate'])) ?>"> <?php echo $data['name'].'- goal '.$j; ?></a></td>
						<td > <?php //echo $item['goalData'][$k]['totalsForAllResults']['ga:sessions']; ?></td>
						<td > <?php echo $item['goalData'][$k]['totalsForAllResults']['ga:goal'.$id.'Completions']; ?></td>
						<td > <?php echo round($item['goalData'][$k]['totalsForAllResults']['ga:goal'.$id.'Completions']/$item['goalData'][$k]['totalsForAllResults']['ga:sessions']*100,1); ?>%</td>
						<td > <?php echo '$'.$data['value']; ?></td>
					</tr>
				<?php	
					$j++; $k++;	
					}
				?>
					<tr role="row" class="odd">
						<td>&nbsp;</td>
						<td>&nbsp;<?php //echo $item['metaData']['sessions']; ?></td>
						<td>
							<a href="<?php echo $siteMetaPageURL.'?account='.$_GET['account'].'&property='.$item['id'].'&propertyname='.$item['name'].'&days='.$days.'&profileId='.$item['profileID'].'&allGoalComplete='.$totalGoalCompletion.'&session='.$session.'&startDate='.date('Y-m-d', strtotime($_GET['startDate'])).'&endDate='.date('Y-m-d', strtotime($_GET['endDate'])) ?>">
								<?php echo 'All Goal Completions : '.$totalGoalCompletion ?>
							</a>
						</td>
						<td>&nbsp;<?php //echo $item['metaData']['goalConversionRate']; ?></td>
						<td>&nbsp;<?php //echo '$'.$item['value']; ?></td>
					</tr>
				<?php 
				$i++;
				} ?>
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
$mem->quit();
get_footer();
