<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://the-two.co/
 * @since      1.0.0
 *
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/admin
 * @author     Amit Rahav <amit.r.89@gmail.com>
 */
class Videopagetemplate_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Videopagetemplate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Videopagetemplate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/videopagetemplate-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Videopagetemplate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Videopagetemplate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/videopagetemplate-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Save correct page template meta on a post save
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function save_project_templates($post_id){
		$meta_data = get_post_meta($post_id);

	}

	/**
	 * Determine path of page template 
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function page_template_path($path){
		return $path;
	}

	/**
	 * Adding plugin settings to general section page 
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function settings_section(){
		add_settings_section(  
			'vpt_settings_section', // Section ID 
			'Video Page Template Plugin Settings', // Section Title
			array($this, 'section_options_callback'), // Callback
			'general' // What Page?  This makes the section show up on the General Settings Page
		);
	
		add_settings_field( // Option 1
			'user_role_access', // Option ID
			'Users role enabled access', // Label
			array($this, 'role_options'), // !important - This is where the args go!
			'general', // Page it will be displayed (General Settings)
			'vpt_settings_section', // Name of our section
			array( // The $args
				'user_role_access' // Should match Option ID
			)  
		); 
	
		add_settings_field( // Option 2
			'restrict_one_session', // Option ID
			'Single session restriction', // Label
			array($this, 'boolean_option'), // !important - This is where the args go!
			'general', // Page it will be displayed
			'vpt_settings_section', // Name of our section (General Settings)
			array( // The $args
				'restrict_one_session', // Should match Option ID
			)  
		); 
	
		register_setting('general','user_role_access', 'esc_attr');
		register_setting('general','restrict_one_session', 'esc_attr');
	}

	/**
	 * Text for settings section 
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function section_options_callback() { // Section Callback
		echo '<p>Some option for Video Page Template plugin</p>';  
	}
	
	/**
	 * Textbox fields 
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function boolean_option($args) { 
		$option = get_option($args[0]);
		$lower_name = strtolower($args[0]);

		echo '<input type="checkbox" id="'. $lower_name .'" name="'. $lower_name .'" value="'. $args[0] .'" '. checked($lower_name, $option, 0) .'>';
		echo '<label for="'. $lower_name .'">Restrict access to the template page for only one session at a time</label><br>';
	}


	/**
	 * User roles fields
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function role_options($args) { 
		global $wp_roles;
		$option = get_option($args[0]);
		$roles = $wp_roles->roles; 

		$html = '<select name="' . $args[0] .'">';
		// First option - default None
		$html .= '<option value="None" '. selected($option ,"none", 0) . ' >None</option>';
		foreach ($roles as $role) {
			$name = strtolower(str_replace(" ", "_", $role['name']));
			$selected = selected($option ,$name, 0);
			$html .= '<option value="' . $name . '"  '. $selected . ' >' . $role['name'] . '</option>';
		}
		$html .= '</select>';

		echo $html;

	}

	/**
	 * Handle redirect on the page template for wrong role or logged out users
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function redirect_unregistered_or_wrong_role_users()
	{
		if(!is_user_logged_in() && is_page_template('videopagetemplate-public-display.php')){
			$redirect = add_query_arg( 'redirected_from_restricted_page_template', get_permalink( $post->ID ), wp_login_url() );
			wp_safe_redirect( $redirect, 401, 'videopagetemplate' );
			exit;
		}
	}


	public function auth_from_page_template_remove_sessions($redirect_to){
		
		if( isset( $_REQUEST['redirected_from_restricted_page_template']) ) {

			// get all sessions for user
			$sessions  = WP_Session_Tokens::get_instance( get_current_user_id() );
			// get current session
			$token = wp_get_session_token();
			// destroy everything since we'll be logging in shortly
			$sessions->destroy_others( $token  );

			return $_REQUEST['redirected_from_restricted_page_template'];

		}else{
			return $redirect_to;
		} 

	}
	

}
