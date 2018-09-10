<?php
class Braintree_Admin_Pages{

	
	/**
	 * Display the tutorials page. 
	 */
	public static function showTutorialsView(){
		WC_Braintree_Admin::getAdminHeader();
		self::tutorialsHeader();
		self::merchantAccounts();
		self::apiKeys();
		self::subscriptions();
		self::webhooks();
	}
	
	public static function tutorialsHeader(){
		?>
		<div class="div--tutorialHeader">
		  <ul>
		    <li><a tutorial-container="merchant_accounts" href="#"><?php echo __('Merchant Account ID', 'braintree')?></a></li>
		  	<li><a tutorial-container="api_keys" href="#"><?php echo __('API Keys', 'braintree')?></a></li>
		  	<li><a tutorial-container="subscriptions" href="#"><?php echo __('Subscriptions', 'braintree')?></a></li>
		  	<li><a tutorial-container="webhooks" href="#"><?php echo __('Webhooks', 'braintree')?></a></li>
		  </ul>
		</div>
		<?php
	}
	
	public static function merchantAccounts(){
		?>
		<div id="merchant_accounts" class="braintree-explanation-container display">
		  <div class="div--title">
		    <h2><?php echo __('Merchant Account ID Configuration', 'braintree')?></h2>
		  </div>
		  <div class="explanation">
		    <div><strong><?php echo __('Merchant Account ID: ', 'braintree')?></strong>
		      <?php echo __('The Merchant Account ID is used during transactions to determine the settlement currency.
		      		Within the <a href="'.admin_url().'admin.php?page=braintree-woocommerce-settings'.'">WooCommerce Settings</a> page, you
		      		can add all of the Merchant Accounts that are associated with your Braintree Account. ', 'braintree')?>
		    </div>
		    <div class="explanation">
		      <?php echo __('To find your Merchant Accounts, login to your <a target="_blank" href="https://braintreegateway.com/login">Braintree Sandbox</a> or <a target="_blank" href="https://sandbox.braintreegateway.com/login">Braintree Production</a> account. 
		      		Once logged in, Click the <strong>Settings</strong> link, then <strong>Processing</strong>. At the bottom of the page, you will find your Merchant Accounts. ', 'braintree')?>
		    </div>
		    <div>
		      <p><?php echo __('Login and click the Processing link located under the Settings menu item.', 'braintree')?></p>
		      <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/settings-processing.png'?>"/></div>
		    </div>
		    <div>
		      <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/merchant-accounts.png'?>"/></div>
		    </div>
		     <div>
		      <p><?php echo __('Navigate to the <a href="'.admin_url().'admin.php?page=braintree-woocommerce-settings'.'">WooCommerce Settings</a> page and add the your merchant accounts. Be sure and associate
		      		your merchant account with the currency that it is assigned to in Braintree.', 'braintree')?></p>
		      <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/merchant-accounts-assign.png'?>"/></div>
		    </div>
		  </div>
		</div>
		<?php 
	}
	
	public static function apiKeys(){
		?>
		<div id="api_keys" class="braintree-explanation-container">
	      <div class="div--title">
			<h2><?php echo __('API Keys Configuration', 'braintree')?></h2>
			  </div>
				 <div class="explanation">
				    <div><strong><?php echo __('API Keys: ', 'braintree')?></strong>
				      <?php echo __('The API Keys are used to communicate securely with Braintree from your Wordpress site. In order for the plugin to send and receive data to and from Braintree, it is required that you add your API keys.
				      		The Merchant ID identifies your merchant account during each request and the Public and Private Key are used for authentication.', 'braintree')?>
				    </div>
				 </div>
				 <div class="explanation">
				      <?php echo __('To access your API Keys, login to your <a target="_blank" href="https://braintreegateway.com/login">Braintree Sandbox</a> or <a target="_blank" href="https://sandbox.braintreegateway.com/login">Braintree Production</a> account. Hover over the 
				      		<strong>Account</strong> link and click <strong>My User</strong>. Scroll to the bottom of the page and click <strong>View Authorizations</strong>. If you have not generated your API keys, do so now. Copy and paste the 
				      		Merchant ID, Private Key, and Public Key into the field located on the <a href="'.admin_url().'admin.php?page=braintree-payment-settings'.'"></a>', 'braintree')?>
				 </div>
				<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/account-myuser.png'?>"/></div>
				<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/api-keys.png'?>"/></div>
				<div>
				  <p><?php echo __('Copy and paste the API keys from Braintree into the settings located on the <a href="'.admin_url().'admin.php?page=braintree-payment-settings'.'">API Settings</a> page.', 'braintree')?></p>
				  <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/api-keys-setup.png'?>"/></div>
				</div>
		</div>
		<?php 
	}
	
	public static function subscriptions(){
		?>
		<div id="subscriptions" class="braintree-explanation-container">
		  <div class="div--title">
			 <h2><?php echo __('Subscription Configuration', 'braintree')?></h2>
		  </div>
		  <div class="explanation">
		    <div><strong><?php echo __('Subscriptions: ', 'braintree')?></strong>
				      <?php echo __('Subscriptions can be configured in two ways. If you want to use the standard WooCommerce Subscriptions funcionality, simply select that option on the 
				      		<a href="'.admin_url().'admin.php?page=braintree-subscriptions-page'.'">Subscriptions Page</a>. If however, you want to take advantage of Braintree\'s automated subscription functionality,
				      		there are several steps that you must perform such as creating a planId and associating it with your subscription product.', 'braintree')?>
			</div>
	      </div>
		  <div class="explanation">
				      <?php echo __('To configure Braintree Subscriptions, login to your <a target="_blank" href="https://braintreegateway.com/login">Braintree Sandbox</a> or <a target="_blank" href="https://sandbox.braintreegateway.com/login">Braintree Production</a> account. 
				      		Once logged in, Click the <strong>Plans</strong> link. To add a plan click <strong>New</strong>.', 'braintree')?>
		  </div>
		  <div>
		  	<p><?php echo __('Login to your Braintree Sandbox or Production account and navigate to the Plans link on the left hand side.', 'braintree')?></p>
		  	<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/plans.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Click the Plans link and then click Add New to create a new Plan. Give your plan a name, Id, and description.', 'braintree')?></p>
		  	<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/planId.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Assign the Plan currency, billing period, and whether or not the subscription starts immediately.', 'braintree')?></p>
		  	<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/planId2.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Set the end date if there is one and configure any add ons that you want the plan to have. An example of an add on would be a one time fee
		    		to be charged when the subscription is created to serve as a signup fee.', 'braintree')?></p>
		  	<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/planId3.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Once you have saved the Plan, navigate to your wordpress site and click on the subscription product you want to edit. 
		    		If you have configured your API keys correctly, the planId\'s associated with your merchant account will appear. Assign the plan that you want and
		    		save the product. To test, simply purchase the subscription and verify that it is created in the Braintree environment.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/subscriptions-assign-plan.png'?>"/></div>
		  </div>
		   <div>
		    <p><?php echo __('You don\'t have to have to have the WooCommerce Subscriptions plugin to charge for subscriptions. This plugin allows you to convert a regular WooCommerce product into a subscription. Select the 
		    		product that you want to charge as a subscription then select "Sell As Subscription" and select the plan Id.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/subscription-braintree-only.png'?>"/></div>
		  </div>
		</div>
		<?php 
	}
	
	public static function webhooks(){
		?>
		<div id="webhooks" class="braintree-explanation-container">
		  <div class="div--title">
			 <h2><?php echo __('Webhook Configuration', 'braintree')?></h2>
		  </div>
		  <div class="explanation">
		    <div><strong><?php echo __('Webhooks: ', 'braintree')?></strong>
		    <?php echo __('Braintree has the ability to send notices to your wordpress site when certain events occur. For example, you can tell Braintree to send you messages
		    		anytime a Braintree subscription payment fails. This allows you to handle the failed payment method in an automated way.', 'braintree')?>
		  </div>
		  <div>
		  	<p><?php echo __('Login to your Braintree Ssandbox or Production account and navigate to the Webooks page by clicking <strong>Webhooks</strong> located under the <strong>Settings</strong>
		  			menu item.', 'braintree')?></p>
		  	<div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/webhooks.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Click the <strong>Create new webhook</strong> button.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/webhook-create.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Paste this url <textarea>'.WC_Braintree_Admin::getWebhooksUrl().'</textarea> into the Destination URL field. Switch http for https if you have an SSL cert enabled. 
		    		Assign all of the notification types that you want Braintree to trigger. Save the webhook.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/webhook-create2.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('Enable the webhooks option on the <a href="'.admin_url().'admin.php?page=braintree-webhooks-page'.'">Webhook Settings</a> page and enable all the notification types that 
		    		you want to accept from Braintree.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/webhook-create3.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('From within Braintree, navigate to the Webhooks page. Under the actions header, there will be a link next to your URL that says <strong>Check URL</strong>. Click this link to 
		    		test your webhook integration. If you have configured everything correctly, you will receive a 200 response code.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/webhook-create4.png'?>"/></div>
		  </div>
		  <div>
		    <p><?php echo __('You can verify that the notification sent from Braintree was received by checking the <a href="'.admin_url().'admin.php?page=braintree-debug-log'.'">Debug Log</a>.', 'braintree')?></p>
		    <div class="explanation-img"><img src="<?php echo WC_BRAINTREE_ASSETS.'images/tutorials/debug-log.png'?>"/></div>
		  </div>
		<?php 
	}
}