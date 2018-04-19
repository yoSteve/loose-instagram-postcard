<?php
/*
*
* Loose Instagram Postcard Settings Page
* 
*/

// NOTE: to use these options in a template:
//		 $options = get_option('loose_postcard_options'); // $options is an Array
//		 echo $options['client_id']; // access array using unique identifier

function build_loose_options_page() { ?>
	<div id="theme-options-wrap">
		<div class="icon32" id="icon-tools"> <br /> </div>
		<form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields('loose_postcard_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
		</form>
	</div>
<?php }

add_action('admin_init', 'register_and_build_loose_fields');

function register_and_build_loose_fields() {

	register_setting('loose_postcard_options', 'loose_postcard_options', 'validate_loose_setting');

	add_settings_section('loose_postcard_settings', 'Loose Instagram Postcard Settings', 'section_loose_postcard', __FILE__);

	function section_loose_postcard() {
		echo '<p>Instagram requires some info from you before we can get this plugin rolling.
		The good people over at SlickRemix.com have a step-by-step on how to get the info we need: <a href="https://www.slickremix.com/docs/how-to-create-instagram-access-token/" target="_blank"> https://www.slickremix.com/docs/how-to-create-instagram-access-token/</a></p>';
	}

	add_settings_field('client_id', 'Instagram Client ID', 'client_id', __FILE__, 'loose_postcard_settings');

	add_settings_field('client_secret', 'Instagram Client Secret', 'client_secret', __FILE__, 'loose_postcard_settings');

	add_settings_field('access_token', 'Instagram Access Token', 'access_token', __FILE__, 'loose_postcard_settings');

	// add_settings_field('display_num', 'Number of Posts to Display (Max 20)', 'display_num', __FILE__, 'loose_postcard_settings');

}

function validate_loose_setting($loose_postcard_options) {
	return $loose_postcard_options;
}

function client_id() {
	$options = get_option('loose_postcard_options');  echo "<input name='loose_postcard_options[client_id]' type='text' value='{$options['client_id']}' />";
}

function client_secret() {
	$options = get_option('loose_postcard_options');  echo "<input name='loose_postcard_options[client_secret]' type='text' value='{$options['client_secret']}' />";
}

function access_token() {
	$options = get_option('loose_postcard_options');  echo "<input name='loose_postcard_options[access_token]' type='text' value='{$options['access_token']}' />";
}

add_action('admin_menu', 'create_loose_postcard_options_page');
function create_loose_postcard_options_page() {  add_options_page('CLoose Instagram Postcard Settings', 'Loose Postcard Settings', 'administrator', __FILE__, 'build_loose_options_page');}

/////////////////////////////////// Widgetize this Plugin /////////////////////////////////////

class loose_postcard_plugin extends WP_Widget {

	// constructor
	function loose_postcard_plugin() {
		parent::WP_Widget( false, $name = __('Loose Postcard Widget', 'wp_widget_plugin') );
	}

	// widget form creation
	function form($instance) {
		// Check values
		if( $instance) {
		     $display_num		  = esc_attr($instance['display_num']);
		} else {
		     $display_num 		= '';
		} ?>
			<p>
				<label for="<?php echo $this->get_field_id('display_num'); ?>"><?php _e('How Many Posts to Display? (Max 20)', 'wp_widget_plugin'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('display_num'); ?>" name="<?php echo $this->get_field_name('display_num'); ?>" type="text" value="<?php echo $display_num; ?>" />
			</p>
	<?php	}

	// widget update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
      // Fields
      $instance['display_num'] = strip_tags($new_instance['display_num']);
    return $instance;
	}

	// widget display
	function widget($args, $instance) {
		extract( $args );
   // these are the widget options
   $display_num = apply_filters('widget_title', $instance['display_num']);
   echo $before_widget;
   // Display the widget
   echo '<div class="loosePostcard target" data-display-num="' . $display_num . '"></div>';
   echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("loose_postcard_plugin");'));

/////////////////////////////////// Prepare Options for use in Javascript and Enqueue Script  /////////////////////////////////////

$loose_options_object = get_option('loose_postcard_options');

$loose_settings_array = array(
	'client_id'			=>	$loose_options_object['client_id'],
	'client_secret'	=>	$loose_options_object['client_secret'],
	'access_token'	=>	$loose_options_object['access_token'],
);

wp_enqueue_script( 'loose_postcard_js', plugin_dir_url( __FILE__)  . '/loose_postcard.js', array('jquery') );
wp_localize_script('loose_postcard_js', 'loose_php_vars', $loose_settings_array);
// enqueue the basic stylesheet
wp_enqueue_style( 'loose_postcard_css', plugin_dir_url( __FILE__)  . '/loose_postcard.css' );


?>
