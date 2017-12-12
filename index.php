<?php
/* 
Plugin Name: Flickr Image Slider
Plugin URI: http://pushpendra.net
Description: Shows the Flickr photostream.
Version: 1.0
Author: Pushpendra Singh
Author URI: http://pushpendra.net
*/
	add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );
	function register_plugin_styles() {
		wp_register_style( 'flikercss', plugins_url( 'smoothDivScroll.css' ) );
		wp_enqueue_style( 'flikercss' );
	}
	wp_register_script('swipebox', plugins_url('lightboxes/swipebox/js/jquery.swipebox.min.js', __FILE__),
			array('jquery'), 'v1.3.0.2', true);

	function add_js_css_fun() {
		wp_register_style( 'myCss', plugins_url('smoothDivScroll.css', __FILE__) );
		wp_register_script( 'myJqScript', plugins_url('jquery-ui-1.10.3.custom.min.js', __FILE__), array(), '1.0.0', true );
		wp_register_script( 'myScript', plugins_url('jquery.smoothdivscroll-1.3-min.js', __FILE__), array(), '1.0.0', true );
		wp_enqueue_style('myCss');
		wp_enqueue_script('myJqScript');
		wp_enqueue_script('myScript');

	}

	function vp_inline_script() { ?>
	<script type="text/javascript">
		// Initialize the plugin with no custom options
		$(document).ready(function () {
			// None of the options are set
			$("div#makeMeScrollable").smoothDivScroll({});
		});
	</script>
	<?php }
	add_action( 'wp_enqueue_scripts', 'add_js_css_fun' ); 
	add_action('wp_footer', 'vp_inline_script');

	$url = 'https://api.flickr.com/services/rest/?method=flickr.photos.search';
	$url.= '&api_key=4a7171987d8e3efebfdd1051e361de6b';
	$url.= '&user_id=136739934@N05';
	$url.= '&format=json';
	$url.= '&nojsoncallback=1';
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$output = curl_exec($ch);
	curl_close($ch);
	$response =  json_decode($output);
	$photo_array = $response->photos->photo;
	
	//trigger exception in a "try" block
	try {
		$photo_array = $response->photos->photo;
		if(!empty($photo_array))
		{
		  $flag=1;
		?>
		<div class="main-flickr-feeds-div">
		<h1>Flickr Feeds</h1>
		<ul id="list_flickr_feed" class="flickr-feed-list-view">
			<?php
				$flickrcontent ='';
				foreach($photo_array as $single_photo):
					$farm_id = $single_photo->farm;
					$owner_id = $single_photo->owner;
					$server_id = $single_photo->server;
					$photo_id = $single_photo->id;
					$secret_id = $single_photo->secret;
					$size = 'z';
					$title = $single_photo->title;
					$photo_url = 'http://farm'.$farm_id.'.staticflickr.com/'.$server_id.'/'.$photo_id.'_'.$secret_id.'_'.$size.'.'.'jpg';							
		
				$flickrcontent .= '<img title="'.$title.'" src="'.$photo_url.'" />';
			
				$flag++;
			endforeach;  
		 ?>
		 </ul>
		 </div>
			 
		 <?php
		}
		else
		{
		?>
		<div class="nopost"><h3>No Feed Found!</h3></div> 
		<?php
		}
	}
	catch(Exception $e) {
	  echo '<b>Message:</b> Invalid "Flickr App Id" or "Flickr User Id"';
	}
	function flickr_fun($atts){
	ob_start(); 

	?>
			
	 <div id="makeMeScrollable">
		<div class="scrollingHotSpotLeft"></div>
		<div class="scrollingHotSpotRight"></div>
		<div class="scrollWrapper">
			<div class="scrollableArea">
			<?php echo $flickrcontent; ?>
			</div>
		</div>
	</div>

	<?php 
	$content = ob_get_clean();
	return $content;
	}
	add_shortcode( 'my_flickr', 'flickr_fun' );
?>
