<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://the-two.co/
 * @since      1.0.0
 *
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/includes
 * @author     Amit Rahav <amit.r.89@gmail.com>
 */
class Videopagetemplate {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Videopagetemplate_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * The array of templates that this plugin tracks.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $templates    All current page templates.
	 */
	protected $templates;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->templates = array();

		if ( defined( 'VIDEOPAGETEMPLATE_VERSION' ) ) {
			$this->version = VIDEOPAGETEMPLATE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'videopagetemplate';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Videopagetemplate_Loader. Orchestrates the hooks of the plugin.
	 * - Videopagetemplate_i18n. Defines internationalization functionality.
	 * - Videopagetemplate_Admin. Defines all hooks for the admin area.
	 * - Videopagetemplate_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-videopagetemplate-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-videopagetemplate-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-videopagetemplate-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-videopagetemplate-public.php';

		/**
		 * This file adds function to use globally in wp.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/videopagetemplate-global-helpers.php';

		/**
		 * This file register ACF fields
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/videopagetemplate-admin-acf.php';

		$this->loader = new Videopagetemplate_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Videopagetemplate_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Videopagetemplate_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Videopagetemplate_Admin( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// page template registration
		$this->loader->add_filter( 'theme_page_templates', $this, 'add_page_template' );
		$this->loader->add_filter( 'wp_insert_post_data', $this, 'register_project_templates' ); // Inject template into the page cache
		$this->loader->add_filter( 'template_include', $this, 'view_project_template' ); // determine if the page has our template assigned and return it's path
		$this->loader->add_filter( 'page_template_plugin_dir_path', $plugin_admin, 'page_template_path' ); // Page Template Path
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_project_templates' ); // Save page template
		
		// Add template to template array.
		$this->templates = array(
			'videopagetemplate-public-display.php' => 'Video Content Display',
		);

		// Add plugin settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_section' );

		// Handle single session
		if(get_option('restrict_one_session')){
			$this->loader->add_filter( 'login_redirect', $plugin_admin, 'auth_from_page_template_remove_sessions' ); // Remove all other sessions
		}

		// Handle role restrictions
		if(get_option('user_role_access') != "None" ){
			$this->loader->add_action( 'template_redirect', $plugin_admin, 'redirect_unregistered_or_wrong_role_users' ); // Redirect if not logged
		}
	}

	/**
	 * Adds our template to the page dropdown 
	 *
	 * @since    1.0.0
	 */
	public function add_page_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 * 
	 * @since    1.0.0
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page on display
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function view_project_template( $template ) {
		// Return the search template if we're searching (instead of the template for the first result)
		if ( is_search() ) {
			return $template;
		}

		// Get global post
		global $post;
		
		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)] ) ) {
			return $template;
		}

		// Allows filtering of file path
		$filepath = apply_filters( 'page_template_plugin_dir_path', plugin_dir_path( dirname(__FILE__) ) . 'public/partials/' );
		
		$file =  $filepath . get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Videopagetemplate_Public( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Videopagetemplate_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
