<?php class Google_Auth {
 
    protected $loader;
 
    protected $plugin_slug;
 
    protected $version;
 
    public function __construct() {
        require_once realpath(CGA_PLUGIN_BASEPATH . '/tools/src/Google/autoload.php');
		$this->client_id = '503203966289-a9i272p88upaodnspruelks6kr1s41dt.apps.googleusercontent.com';
		$this->client_secret = 'AgEpYPu2PPGUBSlO_rG2tMhh';
		$this->redirect_uri = 'http://demo.getyoursolutions.com/custom_ga/ga-login/';
		$this->homeUrl = 'http://demo.getyoursolutions.com/custom_ga/ga-login/';
		$this->gClient = new Google_Client();
		$this->gClient->setClientId($this->client_id);
		$this->gClient->setClientSecret($this->client_secret);
		$this->gClient->setRedirectUri($this->redirect_uri);
		$this->gClient->setScopes(array(
			'https://www.googleapis.com/auth/userinfo.email',
			'https://www.googleapis.com/auth/userinfo.profile',
			'https://www.googleapis.com/auth/analytics',
			'https://www.googleapis.com/auth/analytics.edit',
			'https://www.googleapis.com/auth/analytics.readonly',
			'https://www.googleapis.com/auth/analytics.provision'
		));
    }
    
    public function authenticate() {
		
    }
    
    public function auth_url() {
		return $this->gClient->createAuthUrl();
    }
    
    public function check_access_code() {
		if(isset($_REQUEST['code']) && $_REQUEST['code'] != ''){
			if (session_status() == PHP_SESSION_NONE) {
				@session_start();
			}
			$this->gClient->authenticate($_REQUEST['code']);
			$_SESSION['CGA_sessionData']['token'] = $this->gClient->getAccessToken();
			$cgaDashSetting = get_option('cga_dashSetting');
			$user_ID = get_current_user_id();
			$analyticAccess = get_user_meta($user_ID, 'ga_userAccess', true);
			$analyticTimeData = array();
			if($analyticAccess != ''){
				$analyticTimeData = unserialize($analyticAccess);
				$analyticTimeData[] = time();
				update_user_meta($user_ID, 'ga_userAccess', serialize($analyticTimeData));
			}else{
				$analyticTimeData[] = time();
				update_user_meta($user_ID, 'ga_userAccess', serialize($analyticTimeData));
			}
			if($cgaDashSetting != '')
				$cgaDashSetting = maybe_unserialize(base64_decode($cgaDashSetting));
			$dashBoardURL = get_permalink($cgaDashSetting['setting']['dashboardPage']); 
			echo '<script>window.location.href="'.$dashBoardURL.'"</script>';
			wp_redirect($dashBoardURL);
			exit();
		}
    }
    
    public function isAccessTokenExpired() {
		If(isset($_SESSION['CGA_sessionData'])){
			$accessData = json_decode($_SESSION['CGA_sessionData']['token']);
			$expiresOn = $accessData->created + $accessData->expires_in;
			if($expiresOn > time())
				return false;
			else
				return true;
		}else{
			return true;
		}
    }
    
    public function getClientObject() {
		return $this->gClient;
    }
}
