<div class="wrap">
	<h2><?php echo 'Google Analytics Settings'; ?></h2>
	<hr>
	<br class="clear" />
	
</div>
<div id="poststuff" class="gadwp">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">
			<div class="settings-wrapper">
				<div class="inside">
					<?php $message = '';
					if(isset($_POST['cgaDash_save']) && $_POST['cgaDash_save'] != ''){
						update_option('cga_dashSetting', base64_encode(serialize($_POST['cgaData']))); 
						$message = 'Settings saved successfully';
					}
					$cgaDashSetting = get_option('cga_dashSetting');
					if($cgaDashSetting != '')
						$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting)); ?>
					<p style="color:green;"><?php echo $message; ?></p>
					<form action="" method="POST" style="width:50%;">
						<?php $pages = get_posts('post_type=page&post_status=publish&posts_per_page=-1&order=ASC&orderby=name'); ?>
						<h3>Google API project credentials:</h3>
						<table class="gadwp-settings-options">
							<tr>
								<td>Client ID: </td>
								<td><input type="text" name="cgaData[setting][clientID]" value="<?php echo $cgaDashSetting['setting']['clientID']; ?>" style="width:100%;"></td>
							</tr>
							<tr>
								<td>Client Secret: </td>
								<td><input type="text" name="cgaData[setting][clientSecret]" value="<?php echo $cgaDashSetting['setting']['clientSecret']; ?>" style="width:100%;"></td>
							</tr>
						</table>
						<hr>
						<table class="gadwp-settings-options">
							<tr>
								<td>GA login Page: </td>
								<td>
									<select name="cgaData[setting][loginPage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['loginPage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td>GA Dashboard Page: </td>
								<td>
									<select name="cgaData[setting][dashboardPage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['dashboardPage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td>GA Goal Cycle Page: </td>
								<td>
									<select name="cgaData[setting][goalCyclePage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['goalCyclePage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td>GA Site MetaData Page: </td>
								<td>
									<select name="cgaData[setting][siteMetaPage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['siteMetaPage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td>GA Source MetaData Page: </td>
								<td>
									<select name="cgaData[setting][goalCycleMetaPage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['goalCycleMetaPage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td>GA Cycle Table Page: </td>
								<td>
									<select name="cgaData[setting][cycleTablePage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['cycleTablePage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>       
                                                        <tr>
								<td>GA Cycle Table Page: </td>
								<td>
									<select name="cgaData[setting][oppvizPage]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['oppvizPage'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>        
                                                        <tr>
								<td>GA Network Graph Page: </td>
								<td>
									<select name="cgaData[setting][networkGraph]">
										<option value="">Select Page</option>
										<?php foreach($pages as $page){
											if($cgaDashSetting['setting']['networkGraph'] == $page->ID){
												$selected = 'selected="selected"';
											}else{
												$selected = '';
											} ?>
											<option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>   
							<tr>
								<td>
									<input class="button button-primary button-large" type="submit" value="Save Settings" name="cgaDash_save">
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
			

