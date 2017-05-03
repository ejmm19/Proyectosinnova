<?php

/**
 * The admin part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_admin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_version
	 */
	private $plugin_version;

	private $supported_post_types = array(
		'post',
		'page',
		'product',
		'portfolio',
		'books',
		'movies',
		'projects',
		'work'
	);

	/**
	 * Kicks off the admin part of the plugin.
	 *
	 * 1. Loads the dependencies the admin part relies on.
	 * 2. Defines post type support.
	 * 3. Defines the functionality for the custom backgrounds.
	 * 4. Checks if the theme supports custom backgrounds and the user's permission to interact with this plugin.
	 * 5. Defines the meta box.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 * @param    object $loader
	 * @param    string $meta_key
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;

		$this->load_dependencies();
		$this->define_post_type_support();
		$this->define_help_tab();
		$this->define_menu();

		$this->add_hooks();
	}

	/**
	 * Loads the initial files needed by the admin part of the plugin and assigns the loader object.
	 *
	 * The class responsible for orchestrating the actions and filters of the core plugin.
	 * The class responsible for the post type support.
	 * The class responsible for the localisation of scripts for the admin part.
	 * The class responsible for setting up the custom background.
	 * The class responsible for the help tab.
	 * The classes responsible for the meta box.
	 * The class responsible for the menu.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/includes/class-cb-parallax-post-type-support.php";

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/includes/class-cb-parallax-meta-box-localisation.php";

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/includes/class-cb-parallax-help-tab.php";

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/includes/class-cb-parallax-meta-box.php";

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/menu/class-cb-parallax-menu.php";
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 *
	 * @param    string $hook_suffix
	 *
	 * @return   void
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();

		if ( in_array( $screen->id, $this->supported_post_types ) ) {

			// Font Awesome.
			$fa_url = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css';
			$fa_cdn = wp_remote_get( $fa_url );
			if ( (int) wp_remote_retrieve_response_code( $fa_cdn ) !== 200 ) {
				$fa_url = plugin_dir_url( __FILE__ ) . '../../vendor/font-awesome/font-awesome.min.css';
			}
			wp_enqueue_style( 'inc-font-awesome', $fa_url, false );

			// Color picker.
			wp_enqueue_style( 'wp-color-picker' );

			// Admin part
			wp_enqueue_style(
				$this->plugin_name . '-admin-css',
				plugin_dir_url( __FILE__ ) . 'css/admin.css',
				array(),
				$this->plugin_version,
				'all'
			);
		}
	}

	/**
	 * Registers and enqueues the script for the admin part of the plugin.
	 *
	 * @hooked_action
	 *
	 * But first we check if we're in the right spot and
	 * if the current user owns the capability needed to interact with this plugin.
	 * If so, the script for the admin part gets registered and enqueued, and that script then gets localized.
	 * Finally, we enqueue the color picker.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 *
	 * @param    $hook_suffix
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( in_array( $screen->id, $this->supported_post_types ) ) {

			// Color picker.
			wp_enqueue_script( 'wp-color-picker' );

			// Media Frame.
			wp_enqueue_script( 'media-views' );

			// Media upload engine.
			wp_enqueue_media();

			// Fancy Select.
			wp_enqueue_script(
				$this->plugin_name . '-inc-fancy-select-js',
				plugin_dir_url( __FILE__ ) . '../vendor/fancy-select/fancySelect.js',
				array( 'jquery' ),
				$this->plugin_version,
				true
			);

			// Admin part.
			wp_enqueue_script(
				$this->plugin_name . '-admin-js',
				plugin_dir_url( __FILE__ ) . 'js/admin.js',
				array(
					'jquery',
					'wp-color-picker',
					'media-views',
					$this->plugin_name . '-inc-fancy-select-js',
				),
				$this->plugin_version,
				true
			);
		}

	}

	private function add_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'define_meta_box_localisation' ) );
		add_action( 'current_screen', array( $this, 'define_meta_box' ), 20 );

		add_action( 'upgrader_process_complete', array( $this, 'upgrade' ), 20 );
	}

	/**
	 * Calls the class responsible for any eventual upgrade-related functions.
	 *
	 * @hooked_action
	 *
	 * @since    0.6.0
	 * @access   public
	 */
	public function upgrade() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cb-parallax-upgrade.php';

		$upgrader = new cb_parallax_upgrade( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version() );

		$upgrader->run();
	}

	/**
	 * Defines the functionality for editing the custom background -
	 * if the current user has the capability to do so.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function check_cap() {

		if ( ! current_user_can( 'cb_parallax_edit' ) ) {
			return;
		}

		$this->define_meta_box();
	}

	/**
	 * Registers the action to execute on the object regarding the post type support.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function define_post_type_support() {

		$wp_support = new cb_parallax_post_type_support();

		add_action( 'init', array( $wp_support, 'add_post_type_support' ) );
	}

	/**
	 * Instanciates the class responsible for displaying the meta box.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function define_meta_box() {

		$meta_box = new cb_parallax_meta_box( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->supported_post_types );

		add_action( 'add_meta_boxes', array( $meta_box, 'add_meta_box' ) );
	}

	/**
	 * Instanciates the class responsible localizing the admin part of the plugin.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function define_meta_box_localisation() {

		$screen = get_current_screen();

		if ( in_array( $screen->id, $this->supported_post_types ) ) {

			$admin_localisation = new cb_parallax_meta_box_localisation( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version() );

			add_action( 'admin_enqueue_scripts', array( $admin_localisation, 'retrieve_image_options' ), 1 );
			add_action( 'admin_enqueue_scripts', array( $admin_localisation, 'localize_meta_box' ), 1000 );
			add_action( 'admin_enqueue_scripts', array( $admin_localisation, 'localize_media_frame' ), 1000 );
		}
	}

	/**
	 * Instanciates the class responsible for displaing the help tab.
	 *
	 * @since  0.1.0
	 * @see    admin/includes/class-cb-parallax-help-tab.php
	 * @access private
	 * @return void
	 */
	private function define_help_tab() {

		// Show up on all following post type's edit screens:
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'cb_parallax_settings_page' || isset( $_REQUEST['page'] ) || isset( $_REQUEST['post'] ) || isset( $_REQUEST['product'] ) && isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {

			$Help_Tab = new cb_parallax_help_tab( $this->get_plugin_domain() );

			add_action( 'in_admin_header', array( $Help_Tab, 'add_cb_parallax_help_tab' ), 20 );

			add_action( 'load-post.php', array( $Help_Tab, 'add_cb_parallax_help_tab' ), 10 );
			add_action( 'load-post-new.php', array( $Help_Tab, 'add_cb_parallax_help_tab' ), 11 );
			add_action( "load-{$GLOBALS['pagenow']}", array( $Help_Tab, 'add_cb_parallax_help_tab' ), 12 );
		}
	}

	/**
	 * Instanciates the class responsible for displaing the menu.
	 *
	 * @since  0.6.0
	 * @see    admin/includes/class-cb-parallax-help-tab.php
	 * @access private
	 * @return void
	 */
	private function define_menu() {

		$menu = new cb_parallax_menu( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version() );

		add_action( 'admin_enqueue_scripts', array( $menu, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $menu, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $menu, 'add_options_page' ), 20 );
		add_action( 'admin_menu', array( $menu, 'define_settings' ), 40 );
		add_filter( 'admin_body_class', array( $menu, 'add_body_class' ), 10, 1 );
	}

	/**
	 * Adds support, rating, and donation links to the plugin row meta on the plugins admin screen.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   array
	 *
	 * @param    array  $meta
	 * @param    string $file
	 */
	public function plugin_row_meta( $meta, $file ) {

		$plugin = plugin_basename( 'cb-parallax/cb-parallax.php' );

		if ( $file == $plugin ) {
			$meta[] = '<a href="https://wordpress.org/support/plugin/cb-parallax" target="_blank">' . __( 'Plugin support', $this->plugin_domain ) . '</a>';
			$meta[] = '<a href="https://wordpress.org/plugins/cb-parallax" target="_blank">' . __( 'Rate plugin', $this->plugin_domain ) . '</a>';
			$meta[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XLMMS7C62S76Q" target="_blank">' . __( 'Donate', $this->plugin_domain ) . '</a>';
		}

		return $meta;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string $plugin_name
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * The domain of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string $plugin_domain
	 */
	public function get_plugin_domain() {

		return $this->plugin_domain;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string $plugin_version
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}

}
