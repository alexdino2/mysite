<?php
/**
 * Font Awesome specific functions
 *
 * @package Total WordPress Themes
 * @subpackage Framework
 * @version 3.3.0
 */

/**
 * Array of Font Awesome Icons
 * Learn more: http://fortawesome.github.io/Font-Awesome/
 *
 * @since 1.0.0
 */
function wpex_get_awesome_icons( $return = 'all', $default = 'none' ) {

	// Add none to top of array
	$icons_array = array(
		'none' =>''
	);

	// Define return icons
	$return_icons = array();

	// Returns up arrows only
	if ( 'up_arrows' == $return ) {
		$return_icons = array('chevron-up','caret-up','angle-up','angle-double-up','long-arrow-up','arrow-circle-o-up','arrow-up','caret-square-o-up','level-up','sort-up','toggle-up');
		$return_icons = array_combine($return_icons, $return_icons);
	}

	// Returns all icons
	elseif ( 'all' == $return ) {
		$return_icons=array('500px'=>'500px','adjust'=>'adjust','adn'=>'adn','align-center'=>'align-center','align-justify'=>'align-justify','align-left'=>'align-left','align-right'=>'align-right','amazon'=>'amazon','ambulance'=>'ambulance','anchor'=>'anchor','android'=>'android','angellist'=>'angellist','angle-double-down'=>'angle-double-down','angle-double-left'=>'angle-double-left','angle-double-right'=>'angle-double-right','angle-double-up'=>'angle-double-up','angle-down'=>'angle-down','angle-left'=>'angle-left','angle-right'=>'angle-right','angle-up'=>'angle-up','apple'=>'apple','archive'=>'archive','area-chart'=>'area-chart','arrow-circle-down'=>'arrow-circle-down','arrow-circle-left'=>'arrow-circle-left','arrow-circle-o-down'=>'arrow-circle-o-down','arrow-circle-o-left'=>'arrow-circle-o-left','arrow-circle-o-right'=>'arrow-circle-o-right','arrow-circle-o-up'=>'arrow-circle-o-up','arrow-circle-right'=>'arrow-circle-right','arrow-circle-up'=>'arrow-circle-up','arrow-down'=>'arrow-down','arrow-left'=>'arrow-left','arrow-right'=>'arrow-right','arrow-up'=>'arrow-up','arrows'=>'arrows','arrows-alt'=>'arrows-alt','arrows-h'=>'arrows-h','arrows-v'=>'arrows-v','asterisk'=>'asterisk','at'=>'at','automobile'=>'automobile','backward'=>'backward','balance-scale'=>'balance-scale','ban'=>'ban','bar-chart-o'=>'bar-chart-o','barcode'=>'barcode','battery-0'=>'battery-0','battery-1'=>'battery-1','battery-2'=>'battery-2','battery-3'=>'battery-3','battery-4'=>'battery-4','beer'=>'beer','behance'=>'behance','behance-square'=>'behance-square','bell'=>'bell','bell-o'=>'bell-o','bell-slash'=>'bell-slash','bell-slash-o'=>'bell-slash-o','bicycle'=>'bicycle','binoculars'=>'binoculars','birthday-cake'=>'birthday-cake','bitbucket'=>'bitbucket','bitbucket-square'=>'bitbucket-square','bitcoin'=>'bitcoin','black-tie'=>'black-tie','bluetooth'=>'bluetooth','bluetooth-b'=>'bluetooth-b','bold'=>'bold','bomb'=>'bomb','book'=>'book','bookmark'=>'bookmark','bookmark-o'=>'bookmark-o','briefcase'=>'briefcase','bug'=>'bug','building'=>'building','building-o'=>'building-o','bullhorn'=>'bullhorn','bullseye'=>'bullseye','bus'=>'bus','buysellads'=>'buysellads','cab'=>'cab','calculator'=>'calculator','calendar'=>'calendar','calendar-check-o'=>'calendar-check-o','calendar-minus-o'=>'calendar-minus-o','calendar-o'=>'calendar-o','calendar-plus-o'=>'calendar-plus-o','calendar-times-o'=>'calendar-times-o','camera'=>'camera','camera-retro'=>'camera-retro','caret-down'=>'caret-down','caret-left'=>'caret-left','caret-right'=>'caret-right','caret-up'=>'caret-up','cart-arrow-down'=>'cart-arrow-down','cart-plus'=>'cart-plus','cc'=>'cc','cc-amex'=>'cc-amex','cc-diners-club'=>'cc-diners-club','cc-discover'=>'cc-discover','cc-jcb'=>'cc-jcb','cc-mastercard'=>'cc-mastercard','cc-paypal'=>'cc-paypal','cc-stripe'=>'cc-stripe','cc-visa'=>'cc-visa','certificate'=>'certificate','chain'=>'chain','check'=>'check','check-circle'=>'check-circle','check-circle-o'=>'check-circle-o','check-square'=>'check-square','check-square-o'=>'check-square-o','chevron-circle-down'=>'chevron-circle-down','chevron-circle-left'=>'chevron-circle-left','chevron-circle-right'=>'chevron-circle-right','chevron-circle-up'=>'chevron-circle-up','chevron-down'=>'chevron-down','chevron-left'=>'chevron-left','chevron-right'=>'chevron-right','chevron-up'=>'chevron-up','child'=>'child','chrome'=>'chrome','circle'=>'circle','circle-o'=>'circle-o','circle-o-notch'=>'circle-o-notch','circle-thin'=>'circle-thin','clock-o'=>'clock-o','clone'=>'clone','cloud'=>'cloud','cloud-download'=>'cloud-download','cloud-upload'=>'cloud-upload','cny'=>'cny','code'=>'code','code-fork'=>'code-fork','codepen'=>'codepen','codiepie'=>'codiepie','coffee'=>'coffee','columns'=>'columns','comment'=>'comment','comment-o'=>'comment-o','commenting'=>'commenting','commenting-o'=>'commenting-o','comments'=>'comments','comments-o'=>'comments-o','compass'=>'compass','compress'=>'compress','connectdevelop'=>'connectdevelop','contao'=>'contao','copy'=>'copy','copyright'=>'copyright','creative-commons'=>'creative-commons','credit-card'=>'credit-card','credit-card-alt'=>'credit-card-alt','crop'=>'crop','crosshairs'=>'crosshairs','css3'=>'css3','cube'=>'cube','cubes'=>'cubes','cut'=>'cut','cutlery'=>'cutlery','dashboard'=>'dashboard','dashcube'=>'dashcube','database'=>'database','dedent'=>'dedent','delicious'=>'delicious','desktop'=>'desktop','deviantart'=>'deviantart','diamond'=>'diamond','digg'=>'digg','dollar'=>'dollar','dot-circle-o'=>'dot-circle-o','download'=>'download','dribbble'=>'dribbble','dropbox'=>'dropbox','drupal'=>'drupal','edge'=>'edge','edit'=>'edit','eject'=>'eject','ellipsis-h'=>'ellipsis-h','ellipsis-v'=>'ellipsis-v','envelope'=>'envelope','envelope-o'=>'envelope-o','envelope-square'=>'envelope-square','eraser'=>'eraser','euro'=>'euro','exchange'=>'exchange','exclamation'=>'exclamation','exclamation-circle'=>'exclamation-circle','expand'=>'expand','expeditedssl'=>'expeditedssl','external-link'=>'external-link','external-link-square'=>'external-link-square','eye'=>'eye','eye-slash'=>'eye-slash','eyedropper'=>'eyedropper','facebook-f'=>'facebook-f','facebook-official'=>'facebook-official','facebook-square'=>'facebook-square','fast-backward'=>'fast-backward','fast-forward'=>'fast-forward','fax'=>'fax','feed'=>'feed','female'=>'female','fighter-jet'=>'fighter-jet','file'=>'file','file-code-o'=>'file-code-o','file-excel-o'=>'file-excel-o','file-movie-o'=>'file-movie-o','file-o'=>'file-o','file-pdf-o'=>'file-pdf-o','file-photo-o'=>'file-photo-o','file-powerpoint-o'=>'file-powerpoint-o','file-sound-o'=>'file-sound-o','file-text'=>'file-text','file-text-o'=>'file-text-o','file-word-o'=>'file-word-o','file-zip-o'=>'file-zip-o','film'=>'film','filter'=>'filter','fire'=>'fire','fire-extinguisher'=>'fire-extinguisher','firefox'=>'firefox','flag'=>'flag','flag-checkered'=>'flag-checkered','flag-o'=>'flag-o','flash'=>'flash','flask'=>'flask','flickr'=>'flickr','folder'=>'folder','folder-o'=>'folder-o','folder-open'=>'folder-open','folder-open-o'=>'folder-open-o','font'=>'font','fonticons'=>'fonticons','fort-awesome'=>'fort-awesome','forumbee'=>'forumbee','forward'=>'forward','foursquare'=>'foursquare','frown-o'=>'frown-o','gamepad'=>'gamepad','gbp'=>'gbp','ge'=>'ge','gear'=>'gear','gears'=>'gears','genderless'=>'genderless','get-pocket'=>'get-pocket','gg'=>'gg','gg-circle'=>'gg-circle','gift'=>'gift','git'=>'git','git-square'=>'git-square','github'=>'github','github-alt'=>'github-alt','github-square'=>'github-square','gittip'=>'gittip','globe'=>'globe','google'=>'google','google-plus'=>'google-plus','google-plus-square'=>'google-plus-square','google-wallet'=>'google-wallet','group'=>'group','h-square'=>'h-square','hand-grab-o'=>'hand-grab-o','hand-lizard-o'=>'hand-lizard-o','hand-o-down'=>'hand-o-down','hand-o-left'=>'hand-o-left','hand-o-right'=>'hand-o-right','hand-o-up'=>'hand-o-up','hand-peace-o'=>'hand-peace-o','hand-pointer-o'=>'hand-pointer-o','hand-scissors-o'=>'hand-scissors-o','hand-spock-o'=>'hand-spock-o','hand-stop-o'=>'hand-stop-o','hashtag'=>'hashtag','hdd-o'=>'hdd-o','header'=>'header','headphones'=>'headphones','heart'=>'heart','heart-o'=>'heart-o','heartbeat'=>'heartbeat','history'=>'history','home'=>'home','hospital-o'=>'hospital-o','hotel'=>'hotel','hourglass'=>'hourglass','hourglass-1'=>'hourglass-1','hourglass-2'=>'hourglass-2','hourglass-3'=>'hourglass-3','hourglass-o'=>'hourglass-o','houzz'=>'houzz','html5'=>'html5','i-cursor'=>'i-cursor','inbox'=>'inbox','indent'=>'indent','industry'=>'industry','info'=>'info','info-circle'=>'info-circle','instagram'=>'instagram','institution'=>'institution','internet-explorer'=>'internet-explorer','intersex'=>'intersex','ioxhost'=>'ioxhost','italic'=>'italic','joomla'=>'joomla','jsfiddle'=>'jsfiddle','key'=>'key','keyboard-o'=>'keyboard-o','language'=>'language','laptop'=>'laptop','lastfm'=>'lastfm','lastfm-square'=>'lastfm-square','leaf'=>'leaf','leanpub'=>'leanpub','legal'=>'legal','lemon-o'=>'lemon-o','level-down'=>'level-down','level-up'=>'level-up','lg{font-size'=>'lg{font-size','life-bouy'=>'life-bouy','lightbulb-o'=>'lightbulb-o','line-chart'=>'line-chart','linkedin'=>'linkedin','linkedin-square'=>'linkedin-square','linux'=>'linux','list'=>'list','list-alt'=>'list-alt','list-ol'=>'list-ol','list-ul'=>'list-ul','location-arrow'=>'location-arrow','lock'=>'lock','long-arrow-down'=>'long-arrow-down','long-arrow-left'=>'long-arrow-left','long-arrow-right'=>'long-arrow-right','long-arrow-up'=>'long-arrow-up','magic'=>'magic','magnet'=>'magnet','mail-forward'=>'mail-forward','mail-reply'=>'mail-reply','mail-reply-all'=>'mail-reply-all','male'=>'male','map'=>'map','map-marker'=>'map-marker','map-o'=>'map-o','map-pin'=>'map-pin','map-signs'=>'map-signs','mars'=>'mars','mars-double'=>'mars-double','mars-stroke'=>'mars-stroke','mars-stroke-h'=>'mars-stroke-h','mars-stroke-v'=>'mars-stroke-v','maxcdn'=>'maxcdn','meanpath'=>'meanpath','medium'=>'medium','medkit'=>'medkit','meh-o'=>'meh-o','mercury'=>'mercury','microphone'=>'microphone','microphone-slash'=>'microphone-slash','minus'=>'minus','minus-circle'=>'minus-circle','minus-square'=>'minus-square','minus-square-o'=>'minus-square-o','mixcloud'=>'mixcloud','mobile-phone'=>'mobile-phone','modx'=>'modx','money'=>'money','moon-o'=>'moon-o','mortar-board'=>'mortar-board','motorcycle'=>'motorcycle','mouse-pointer'=>'mouse-pointer','music'=>'music','navicon'=>'navicon','neuter'=>'neuter','newspaper-o'=>'newspaper-o','object-group'=>'object-group','object-ungroup'=>'object-ungroup','odnoklassniki'=>'odnoklassniki','odnoklassniki-square'=>'odnoklassniki-square','opencart'=>'opencart','openid'=>'openid','opera'=>'opera','optin-monster'=>'optin-monster','pagelines'=>'pagelines','paint-brush'=>'paint-brush','paperclip'=>'paperclip','paragraph'=>'paragraph','paste'=>'paste','pause'=>'pause','pause-circle'=>'pause-circle','pause-circle-o'=>'pause-circle-o','paw'=>'paw','paypal'=>'paypal','pencil'=>'pencil','pencil-square'=>'pencil-square','percent'=>'percent','phone'=>'phone','phone-square'=>'phone-square','photo'=>'photo','pie-chart'=>'pie-chart','pied-piper'=>'pied-piper','pied-piper-alt'=>'pied-piper-alt','pinterest'=>'pinterest','pinterest-p'=>'pinterest-p','pinterest-square'=>'pinterest-square','plane'=>'plane','play'=>'play','play-circle'=>'play-circle','play-circle-o'=>'play-circle-o','plug'=>'plug','plus'=>'plus','plus-circle'=>'plus-circle','plus-square'=>'plus-square','plus-square-o'=>'plus-square-o','power-off'=>'power-off','print'=>'print','product-hunt'=>'product-hunt','puzzle-piece'=>'puzzle-piece','qq'=>'qq','qrcode'=>'qrcode','question'=>'question','question-circle'=>'question-circle','quote-left'=>'quote-left','quote-right'=>'quote-right','ra'=>'ra','random'=>'random','recycle'=>'recycle','reddit'=>'reddit','reddit-alien'=>'reddit-alien','reddit-square'=>'reddit-square','refresh'=>'refresh','registered'=>'registered','remove'=>'remove','renren'=>'renren','retweet'=>'retweet','road'=>'road','rocket'=>'rocket','rotate-left'=>'rotate-left','rotate-right'=>'rotate-right','rss-square'=>'rss-square','ruble'=>'ruble','rupee'=>'rupee','safari'=>'safari','save'=>'save','scribd'=>'scribd','search'=>'search','search-minus'=>'search-minus','search-plus'=>'search-plus','sellsy'=>'sellsy','send'=>'send','send-o'=>'send-o','server'=>'server','share-alt'=>'share-alt','share-alt-square'=>'share-alt-square','share-square'=>'share-square','share-square-o'=>'share-square-o','shekel'=>'shekel','shield'=>'shield','ship'=>'ship','shirtsinbulk'=>'shirtsinbulk','shopping-bag'=>'shopping-bag','shopping-basket'=>'shopping-basket','shopping-cart'=>'shopping-cart','sign-in'=>'sign-in','sign-out'=>'sign-out','signal'=>'signal','simplybuilt'=>'simplybuilt','sitemap'=>'sitemap','skyatlas'=>'skyatlas','skype'=>'skype','slack'=>'slack','sliders'=>'sliders','slideshare'=>'slideshare','smile-o'=>'smile-o','soccer-ball-o'=>'soccer-ball-o','sort-alpha-asc'=>'sort-alpha-asc','sort-alpha-desc'=>'sort-alpha-desc','sort-amount-asc'=>'sort-amount-asc','sort-amount-desc'=>'sort-amount-desc','sort-down'=>'sort-down','sort-numeric-asc'=>'sort-numeric-asc','sort-numeric-desc'=>'sort-numeric-desc','sort-up'=>'sort-up','soundcloud'=>'soundcloud','space-shuttle'=>'space-shuttle','spinner'=>'spinner','spoon'=>'spoon','spotify'=>'spotify','square'=>'square','square-o'=>'square-o','stack-exchange'=>'stack-exchange','stack-overflow'=>'stack-overflow','star'=>'star','star-half'=>'star-half','star-half-empty'=>'star-half-empty','star-o'=>'star-o','steam'=>'steam','steam-square'=>'steam-square','step-backward'=>'step-backward','step-forward'=>'step-forward','stethoscope'=>'stethoscope','sticky-note'=>'sticky-note','sticky-note-o'=>'sticky-note-o','stop'=>'stop','stop-circle'=>'stop-circle','stop-circle-o'=>'stop-circle-o','street-view'=>'street-view','strikethrough'=>'strikethrough','stumbleupon'=>'stumbleupon','stumbleupon-circle'=>'stumbleupon-circle','subscript'=>'subscript','subway'=>'subway','suitcase'=>'suitcase','sun-o'=>'sun-o','superscript'=>'superscript','table'=>'table','tablet'=>'tablet','tag'=>'tag','tags'=>'tags','tasks'=>'tasks','tencent-weibo'=>'tencent-weibo','terminal'=>'terminal','text-height'=>'text-height','text-width'=>'text-width','th'=>'th','th-large'=>'th-large','th-list'=>'th-list','thumb-tack'=>'thumb-tack','thumbs-down'=>'thumbs-down','thumbs-o-down'=>'thumbs-o-down','thumbs-o-up'=>'thumbs-o-up','thumbs-up'=>'thumbs-up','ticket'=>'ticket','times-circle'=>'times-circle','times-circle-o'=>'times-circle-o','tint'=>'tint','toggle-down'=>'toggle-down','toggle-left'=>'toggle-left','toggle-off'=>'toggle-off','toggle-on'=>'toggle-on','toggle-right'=>'toggle-right','toggle-up'=>'toggle-up','trademark'=>'trademark','train'=>'train','transgender-alt'=>'transgender-alt','trash'=>'trash','trash-o'=>'trash-o','tree'=>'tree','trello'=>'trello','tripadvisor'=>'tripadvisor','trophy'=>'trophy','truck'=>'truck','tty'=>'tty','tumblr'=>'tumblr','tumblr-square'=>'tumblr-square','turkish-lira'=>'turkish-lira','tv'=>'tv','twitch'=>'twitch','twitter'=>'twitter','twitter-square'=>'twitter-square','umbrella'=>'umbrella','underline'=>'underline','unlink'=>'unlink','unlock'=>'unlock','unlock-alt'=>'unlock-alt','unsorted'=>'unsorted','upload'=>'upload','usb'=>'usb','user'=>'user','user-md'=>'user-md','user-plus'=>'user-plus','user-secret'=>'user-secret','user-times'=>'user-times','venus'=>'venus','venus-double'=>'venus-double','venus-mars'=>'venus-mars','viacoin'=>'viacoin','video-camera'=>'video-camera','vimeo'=>'vimeo','vimeo-square'=>'vimeo-square','vine'=>'vine','vk'=>'vk','volume-down'=>'volume-down','volume-off'=>'volume-off','volume-up'=>'volume-up','warning'=>'warning','wechat'=>'wechat','weibo'=>'weibo','whatsapp'=>'whatsapp','wheelchair'=>'wheelchair','wifi'=>'wifi','wikipedia-w'=>'wikipedia-w','windows'=>'windows','won'=>'won','wordpress'=>'wordpress','wrench'=>'wrench','xing'=>'xing','xing-square'=>'xing-square','y-combinator-square'=>'y-combinator-square','yahoo'=>'yahoo','yc'=>'yc','yelp'=>'yelp','youtube'=>'youtube','youtube-play'=>'youtube-play','youtube-square'=>'youtube-square');
	}
	return apply_filters( 'wpex_get_awesome_icons', array_merge( $icons_array, $return_icons ) );
}

/*
// Used to generate the icons list from the css file
$iconclasses=array();
$faless = file_get_contents(WPEX_CSS_DIR_URI .'lib/font-awesome.min.css');
preg_match_all('/\.fa-.*?:before\s*?\{\s*?content/',$faless,$matches);
foreach($matches[0] as $match) {
	$match = explode(':',$match);
	$iconclasses[] = preg_replace('/^\./','',$match[0]);
}
sort($iconclasses);
foreach ( $iconclasses as $iconclass ) {
	$icon = preg_replace( '/fa-/', '', $iconclass );
	echo "'". $icon ."'=>'". $icon ."',";
}*/

/**
 * Array of Font Icons for meta options
 *
 * @since 1.0.0
 */
function wpex_get_meta_awesome_icons() {
	$awesome_icons = wpex_get_awesome_icons();
	$return_array = array();
	foreach ( $awesome_icons as $awesome_icon ) {
		$return_array[] = array(
			'name'  => $awesome_icon,
			'value' => $awesome_icon
		);
	}
	return $return_array;
}

/**
 * Font Awesome icons corresponding to post formats
 *
 * @since 1.4.0
 */
function wpex_post_format_icon() {

	// Default icon style
	$icon = 'fa fa-file-text-o';

	// Get post format
	$format = get_post_format();

	// Video
	if ( 'video' == $format ) {
		$icon = 'fa fa-video-camera';
	} elseif ( 'audio' == $format ) {
		$icon = 'fa fa-music';
	} elseif ( 'gallery' == $format ) {
		$icon = 'fa fa-file-photo-o';
	} elseif ( 'quote' == $format ) {
		$icon = 'fa fa-quote-left';
	}

	// Apply filters for child theme editing
	$icon = apply_filters( 'wpex_post_format_icon', $icon );

	// Return correct font awesome classname
	echo $icon;
}

/**
 * List of standard fonts
 *
 * @since Total 1.0.0
 */
function wpex_standard_fonts() {
	return apply_filters( 'wpex_standard_fonts_array', array(
		'Arial, Helvetica, sans-serif',
		'Arial Black, Gadget, sans-serif',
		'Bookman Old Style, serif',
		'Comic Sans MS, cursive',
		'Courier, monospace',
		'Georgia, serif',
		'Garamond, serif',
		'Impact, Charcoal, sans-serif',
		'Lucida Console, Monaco, monospace',
		'Lucida Sans Unicode, Lucida Grande, sans-serif',
		'MS Sans Serif, Geneva, sans-serif',
		'MS Serif, New York, sans-serif',
		'Palatino Linotype, Book Antiqua, Palatino, serif',
		'Tahoma, Geneva, sans-serif',
		'Times New Roman, Times, serif',
		'Trebuchet MS, Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif',
		'Paratina Linotype',
		'Trebuchet MS',
	) );
}

/**
 * List of All GooGle fonts
 *
 * @since 1.6.0
 */
function wpex_google_fonts_array() {
	return apply_filters( 'wpex_google_fonts_array', array('ABeeZee', 'Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Adamina', 'Advent Pro', 'Aguafina Script', 'Akronim', 'Aladin', 'Aldrich', 'Alef', 'Alegreya', 'Alegreya SC', 'Alegreya Sans', 'Alegreya Sans SC', 'Alex Brush', 'Alfa Slab One', 'Alice', 'Alike', 'Alike Angular', 'Allan', 'Allerta', 'Allerta Stencil', 'Allura', 'Almendra', 'Almendra Display', 'Almendra SC', 'Amarante', 'Amaranth', 'Amatic SC', 'Amethysta', 'Anaheim', 'Andada', 'Andika', 'Angkor', 'Annie Use Your Telescope', 'Anonymous Pro', 'Antic', 'Antic Didone', 'Antic Slab', 'Anton', 'Arapey', 'Arbutus', 'Arbutus Slab', 'Architects Daughter', 'Archivo Black', 'Archivo Narrow', 'Arimo', 'Arizonia', 'Armata', 'Artifika', 'Arvo', 'Asap', 'Asset', 'Astloch', 'Asul', 'Atomic Age', 'Aubrey', 'Audiowide', 'Autour One', 'Average', 'Average Sans', 'Averia Gruesa Libre', 'Averia Libre', 'Averia Sans Libre', 'Averia Serif Libre', 'Bad Script', 'Balthazar', 'Bangers', 'Basic', 'Battambang', 'Baumans', 'Bayon', 'Belgrano', 'Belleza', 'BenchNine', 'Bentham', 'Berkshire Swash', 'Bevan', 'Bigelow Rules', 'Bigshot One', 'Bilbo', 'Bilbo Swash Caps', 'Bitter', 'Black Ops One', 'Bokor', 'Bonbon', 'Boogaloo', 'Bowlby One', 'Bowlby One SC', 'Brawler', 'Bree Serif', 'Bubblegum Sans', 'Bubbler One', 'Buda', 'Buenard', 'Butcherman', 'Butterfly Kids', 'Cabin', 'Cabin Condensed', 'Cabin Sketch', 'Caesar Dressing', 'Cagliostro', 'Calligraffitti', 'Cambo', 'Candal', 'Cantarell', 'Cantata One', 'Cantora One', 'Capriola', 'Cardo', 'Carme', 'Carrois Gothic', 'Carrois Gothic SC', 'Carter One', 'Caudex', 'Cedarville Cursive', 'Ceviche One', 'Changa One', 'Chango', 'Chau Philomene One', 'Chela One', 'Chelsea Market', 'Chenla', 'Cherry Cream Soda', 'Cherry Swash', 'Chewy', 'Chicle', 'Chivo', 'Cinzel', 'Cinzel Decorative', 'Clicker Script', 'Coda', 'Coda Caption', 'Codystar', 'Combo', 'Comfortaa', 'Coming Soon', 'Concert One', 'Condiment', 'Content', 'Contrail One', 'Convergence', 'Cookie', 'Copse', 'Corben', 'Courgette', 'Cousine', 'Coustard', 'Covered By Your Grace', 'Crafty Girls', 'Creepster', 'Crete Round', 'Crimson Text', 'Croissant One', 'Crushed', 'Cuprum', 'Cutive', 'Cutive Mono', 'Damion', 'Dancing Script', 'Dangrek', 'Dawning of a New Day', 'Days One', 'Delius', 'Delius Swash Caps', 'Delius Unicase', 'Della Respira', 'Denk One', 'Devonshire', 'Didact Gothic', 'Diplomata', 'Diplomata SC', 'Domine', 'Donegal One', 'Doppio One', 'Dorsa', 'Dosis', 'Dr Sugiyama', 'Droid Sans', 'Droid Sans Mono', 'Droid Serif', 'Duru Sans', 'Dynalight', 'EB Garamond', 'Eagle Lake', 'Eater', 'Economica', 'Ek Mukta', 'Electrolize', 'Elsie', 'Elsie Swash Caps', 'Emblema One', 'Emilys Candy', 'Engagement', 'Englebert', 'Enriqueta', 'Erica One', 'Esteban', 'Euphoria Script', 'Ewert', 'Exo', 'Exo 2', 'Expletus Sans', 'Fanwood Text', 'Fascinate', 'Fascinate Inline', 'Faster One', 'Fasthand', 'Fauna One', 'Federant', 'Federo', 'Felipa', 'Fenix', 'Finger Paint', 'Fira Mono', 'Fira Sans', 'Fjalla One', 'Fjord One', 'Flamenco', 'Flavors', 'Fondamento', 'Fontdiner Swanky', 'Forum', 'Francois One', 'Freckle Face', 'Fredericka the Great', 'Fredoka One', 'Freehand', 'Fresca', 'Frijole', 'Fruktur', 'Fugaz One', 'GFS Didot', 'GFS Neohellenic', 'Gabriela', 'Gafata', 'Galdeano', 'Galindo', 'Gentium Basic', 'Gentium Book Basic', 'Geo', 'Geostar', 'Geostar Fill', 'Germania One', 'Gilda Display', 'Give You Glory', 'Glass Antiqua', 'Glegoo', 'Gloria Hallelujah', 'Goblin One', 'Gochi Hand', 'Gorditas', 'Goudy Bookletter 1911', 'Graduate', 'Grand Hotel', 'Gravitas One', 'Great Vibes', 'Griffy', 'Gruppo', 'Gudea', 'Habibi', 'Halant', 'Hammersmith One', 'Hanalei', 'Hanalei Fill', 'Handlee', 'Hanuman', 'Happy Monkey', 'Headland One', 'Henny Penny', 'Herr Von Muellerhoff', 'Hind', 'Holtwood One SC', 'Homemade Apple', 'Homenaje', 'IM Fell DW Pica', 'IM Fell DW Pica SC', 'IM Fell Double Pica', 'IM Fell Double Pica SC', 'IM Fell English', 'IM Fell English SC', 'IM Fell French Canon', 'IM Fell French Canon SC', 'IM Fell Great Primer', 'IM Fell Great Primer SC', 'Iceberg', 'Iceland', 'Imprima', 'Inconsolata', 'Inder', 'Indie Flower', 'Inika', 'Irish Grover', 'Istok Web', 'Italiana', 'Italianno', 'Jacques Francois', 'Jacques Francois Shadow', 'Jim Nightshade', 'Jockey One', 'Jolly Lodger', 'Josefin Sans', 'Josefin Slab', 'Joti One', 'Judson', 'Julee', 'Julius Sans One', 'Junge', 'Jura', 'Just Another Hand', 'Just Me Again Down Here', 'Kalam', 'Kameron', 'Kantumruy', 'Karla', 'Karma', 'Kaushan Script', 'Kavoon', 'Kdam Thmor', 'Keania One', 'Kelly Slab', 'Kenia', 'Khand', 'Khmer', 'Kite One', 'Knewave', 'Kotta One', 'Koulen', 'Kranky', 'Kreon', 'Kristi', 'Krona One', 'La Belle Aurore', 'Laila', 'Lancelot', 'Lato', 'League Script', 'Leckerli One', 'Ledger', 'Lekton', 'Lemon', 'Libre Baskerville', 'Life Savers', 'Lilita One', 'Lily Script One', 'Limelight', 'Linden Hill', 'Lobster', 'Lobster Two', 'Londrina Outline', 'Londrina Shadow', 'Londrina Sketch', 'Londrina Solid', 'Lora', 'Love Ya Like A Sister', 'Loved by the King', 'Lovers Quarrel', 'Luckiest Guy', 'Lusitana', 'Lustria', 'Macondo', 'Macondo Swash Caps', 'Magra', 'Maiden Orange', 'Mako', 'Marcellus', 'Marcellus SC', 'Marck Script', 'Margarine', 'Marko One', 'Marmelad', 'Marvel', 'Mate', 'Mate SC', 'Maven Pro', 'McLaren', 'Meddon', 'MedievalSharp', 'Medula One', 'Megrim', 'Meie Script', 'Merienda', 'Merienda One', 'Merriweather', 'Merriweather Sans', 'Metal', 'Metal Mania', 'Metamorphous', 'Metrophobic', 'Michroma', 'Milonga', 'Miltonian', 'Miltonian Tattoo', 'Miniver', 'Miss Fajardose', 'Modern Antiqua', 'Molengo', 'Molle', 'Monda', 'Monofett', 'Monoton', 'Monsieur La Doulaise', 'Montaga', 'Montez', 'Montserrat', 'Montserrat Alternates', 'Montserrat Subrayada', 'Moul', 'Moulpali', 'Mountains of Christmas', 'Mouse Memoirs', 'Mr Bedfort', 'Mr Dafoe', 'Mr De Haviland', 'Mrs Saint Delafield', 'Mrs Sheppards', 'Muli', 'Mystery Quest', 'Neucha', 'Neuton', 'New Rocker', 'News Cycle', 'Niconne', 'Nixie One', 'Nobile', 'Nokora', 'Norican', 'Nosifer', 'Nothing You Could Do', 'Noticia Text', 'Noto Sans', 'Noto Serif', 'Nova Cut', 'Nova Flat', 'Nova Mono', 'Nova Oval', 'Nova Round', 'Nova Script', 'Nova Slim', 'Nova Square', 'Numans', 'Nunito', 'Odor Mean Chey', 'Offside', 'Old Standard TT', 'Oldenburg', 'Oleo Script', 'Oleo Script Swash Caps', 'Open Sans', 'Open Sans Condensed', 'Oranienbaum', 'Orbitron', 'Oregano', 'Orienta', 'Original Surfer', 'Oswald', 'Over the Rainbow', 'Overlock', 'Overlock SC', 'Ovo', 'Oxygen', 'Oxygen Mono', 'PT Mono', 'PT Sans', 'PT Sans Caption', 'PT Sans Narrow', 'PT Serif', 'PT Serif Caption', 'Pacifico', 'Paprika', 'Parisienne', 'Passero One', 'Passion One', 'Pathway Gothic One', 'Patrick Hand', 'Patrick Hand SC', 'Patua One', 'Paytone One', 'Peralta', 'Permanent Marker', 'Petit Formal Script', 'Petrona', 'Philosopher', 'Piedra', 'Pinyon Script', 'Pirata One', 'Plaster', 'Play', 'Playball', 'Playfair Display', 'Playfair Display SC', 'Podkova', 'Poiret One', 'Poller One', 'Poly', 'Pompiere', 'Pontano Sans', 'Port Lligat Sans', 'Port Lligat Slab', 'Prata', 'Preahvihear', 'Press Start 2P', 'Princess Sofia', 'Prociono', 'Prosto One', 'Puritan', 'Purple Purse', 'Quando', 'Quantico', 'Quattrocento', 'Quattrocento Sans', 'Questrial', 'Quicksand', 'Quintessential', 'Qwigley', 'Racing Sans One', 'Radley', 'Rajdhani', 'Raleway', 'Raleway Dots', 'Rambla', 'Rammetto One', 'Ranchers', 'Rancho', 'Rationale', 'Redressed', 'Reenie Beanie', 'Revalia', 'Ribeye', 'Ribeye Marrow', 'Righteous', 'Risque', 'Roboto', 'Roboto Condensed', 'Roboto Slab', 'Roboto Mono', 'Rochester', 'Rock Salt', 'Rokkitt', 'Romanesco', 'Ropa Sans', 'Rosario', 'Rosarivo', 'Rouge Script', 'Rozha One', 'Rubik Mono One', 'Rubik One', 'Ruda', 'Rufina', 'Ruge Boogie', 'Ruluko', 'Rum Raisin', 'Ruslan Display', 'Russo One', 'Ruthie', 'Rye', 'Sacramento', 'Sail', 'Salsa', 'Sanchez', 'Sancreek', 'Sansita One', 'Sarina', 'Sarpanch', 'Satisfy', 'Scada', 'Schoolbell', 'Seaweed Script', 'Sevillana', 'Seymour One', 'Shadows Into Light', 'Shadows Into Light Two', 'Shanti', 'Share', 'Share Tech', 'Share Tech Mono', 'Shojumaru', 'Short Stack', 'Siemreap', 'Sigmar One', 'Signika', 'Signika Negative', 'Simonetta', 'Sintony', 'Sirin Stencil', 'Six Caps', 'Skranji', 'Slabo 13px', 'Slabo 27px', 'Slackey', 'Smokum', 'Smythe', 'Sniglet', 'Snippet', 'Snowburst One', 'Sofadi One', 'Sofia', 'Sonsie One', 'Sorts Mill Goudy', 'Source Code Pro', 'Source Sans Pro', 'Source Serif Pro', 'Special Elite', 'Spicy Rice', 'Spinnaker', 'Spirax', 'Squada One', 'Stalemate', 'Stalinist One', 'Stardos Stencil', 'Stint Ultra Condensed', 'Stint Ultra Expanded', 'Stoke', 'Strait', 'Sue Ellen Francisco', 'Sunshiney', 'Supermercado One', 'Suwannaphum', 'Swanky and Moo Moo', 'Syncopate', 'Tangerine', 'Taprom', 'Tauri', 'Teko', 'Telex', 'Tenor Sans', 'Text Me One', 'The Girl Next Door', 'Tienne', 'Tinos', 'Titan One', 'Titillium Web', 'Trade Winds', 'Trocchi', 'Trochut', 'Trykker', 'Tulpen One', 'Ubuntu', 'Ubuntu Condensed', 'Ubuntu Mono', 'Ultra', 'Uncial Antiqua', 'Underdog', 'Unica One', 'UnifrakturCook', 'UnifrakturMaguntia', 'Unkempt', 'Unlock', 'Unna', 'VT323', 'Vampiro One', 'Varela', 'Varela Round', 'Vast Shadow', 'Vesper Libre', 'Vibur', 'Vidaloka', 'Viga', 'Voces', 'Volkhov', 'Vollkorn', 'Voltaire', 'Waiting for the Sunrise', 'Wallpoet', 'Walter Turncoat', 'Warnes', 'Wellfleet', 'Wendy One', 'Wire One', 'Yanone Kaffeesatz', 'Yellowtail', 'Yeseva One', 'Yesteryear', 'Zeyada') );
}

/**
 * Enqueues a Google Font
 *
 * @since 2.1.0
 */
function wpex_enqueue_google_font( $font) {

	// Get list of all Google Fonts
	$google_fonts = wpex_google_fonts_array();

	// Make sure font is in our list of fonts
	if ( ! $google_fonts || ! in_array( $font, $google_fonts ) ) {
		return;
	}

	// Sanitize handle
	$handle = trim( $font );
	$handle = strtolower( $handle );
	$handle = str_replace( ' ', '-', $handle );

	// Sanitize font name
	$font = trim( $font );
	$font = str_replace( ' ', '+', $font );

	// Subset
	$subset = wpex_get_mod( 'google_font_subsets', 'latin' );
	$subset = $subset ? $subset : 'latin';
	$subset = '&amp;subset='. $subset;

	// Weights
	$weights = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
	$weights = apply_filters( 'wpex_google_font_enqueue_weights', $weights, $font );
	$italics = apply_filters( 'wpex_google_font_enqueue_italics', true );

	// Main URL
	$url = wpex_get_google_fonts_url() .'/css?family='. str_replace(' ', '%20', $font ) .':';

	// Add weights to URL
	if ( ! empty( $weights ) ) {
		$url .= implode( ',' , $weights );
		$italic_weights = array();
		if ( $italics ) {
			foreach ( $weights as $weight ) {
				$italic_weights[] = $weight .'italic';
			}
			$url .= implode( ',' , $italic_weights );
		}
	}

	// Add subset to URL
	$url .= $subset;

	// Enqueue style
	wp_enqueue_style( 'wpex-google-font-'. $handle, $url, false, false, 'all' );

}