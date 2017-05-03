<?php

/**
 * The class responsible for the admin menu.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.6.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/menu
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_menu {

	/**
	 * The name of the plugin.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The domain of the plugin.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The version number of the plugin.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @var      string $plugin_version
	 */
	private $plugin_version;

	/**
	 * The reference to the image_options class.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    object $image_options
	 */
	private $options;

	/**
	 * Assigns the required parameters, loads its dependencies and hooks the required actions.
	 *
	 * @since  0.6.0
	 *
	 * @param  string $plugin_name
	 * @param  string $plugin_domain
	 * @param  string $plugin_version
	 * @param  object $loader
	 *
	 * @return void
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;

		$this->init();
		$this->load_dependencies();
		$this->define_settings();
		$this->define_options();

		$this->add_hooks();
	}

	/**
	 * Loads it's dependencies.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies() {

		// The class responsible for all tasks concerning the settings api.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "menu/includes/class-cb-parallax-settings.php";

		// The class that maintains all data like default values and their meta data.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "menu/includes/class-cb-parallax-options.php";

		// The class responsible for localizing the admin script.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "menu/includes/class-cb-parallax-menu-localisation.php";

	}

	/**
	 * Enqueues styles and scripts.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function init() {

		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'cb_parallax_settings_page' ) {

			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Registers the st<les for the admin menu.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( isset( $hook_suffix ) && $hook_suffix === 'settings_page_cb_parallax_settings_page' ) {

			// Color picker.
			wp_enqueue_style( 'wp-color-picker' );

			// Menu
			wp_enqueue_style(
				$this->plugin_name . '-menu-css',
				plugin_dir_url( __FILE__ ) . 'css/menu.css',
				array(),
				$this->plugin_version,
				'all'
			);
		}
	}

	/**
	 * Registers the scripts for the admin menu.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {

		if ( isset( $hook_suffix ) && $hook_suffix === 'settings_page_cb_parallax_settings_page' ) {

			// Font Awesome.
			$fa_url = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css';
			$fa_cdn = wp_remote_get( $fa_url );
			if ( (int) wp_remote_retrieve_response_code( $fa_cdn ) !== 200 ) {
				$fa_url = plugin_dir_url( __FILE__ ) . '../../vendor/font-awesome/font-awesome.min.css';
			}
			wp_enqueue_style( 'inc-font-awesome', $fa_url, false );

			// Color picker.
			wp_enqueue_script( 'wp-color-picker' );

			// Media Frame.
			wp_enqueue_script( 'media-views' );

			// Media upload engine.
			wp_enqueue_media();

			// Fancy Select.
			wp_enqueue_script(
				$this->plugin_name . '-inc-fancy-select-js',
				plugin_dir_url( __FILE__ ) . '../../vendor/fancy-select/fancySelect.js',
				array( 'jquery' ),
				$this->plugin_version,
				true
			);

			// Menu.
			wp_enqueue_script(
				$this->plugin_name . '-menu-js',
				plugin_dir_url( __FILE__ ) . 'js/menu.js',
				array(
					'jquery',
					'wp-color-picker',
					'media-views',
					$this->plugin_name . '-inc-fancy-select-js',
				),
				$this->plugin_version,
				false
			);
		}
	}

	private function add_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'define_menu_localisation' ) );
	}

	/**
	 * Set a body class.
	 *
	 * @since  0.6.0
	 * @return string $classes
	 */
	public function add_body_class( $classes ) {

			$classes .= 'cb-parallax-settings-page';

		return $classes;
	}

	/**
	 * Initializes the components for the settings section.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @return void
	 */
	public function define_settings() {

		$settings = new cb_parallax_settings( $this->get_plugin_name(), $this->get_plugin_domain() );

		add_action( 'admin_init', array( $settings, 'register_settings' ), 1 );
		add_action( 'admin_init', array( $settings, 'initialize_settings' ), 10 );
	}

	/**
	 * Initializes the components for the image_options section.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @return void
	 */
	public function define_options() {

		$this->options = new cb_parallax_options( $this->get_plugin_name(), $this->get_plugin_domain() );
	}

	/**
	 * Instanciates the class responsible localizing the menu.
	 *
	 * @hooked_action
	 *
	 * @since    0.6.0
	 * @access   public
	 * @return   void
	 */
	public function define_menu_localisation( $hook_suffix ) {

		if ( isset( $hook_suffix ) && $hook_suffix === 'settings_page_cb_parallax_settings_page' ) {

			$menu_localisation = new cb_parallax_menu_localisation( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_options() );

			add_action( 'admin_enqueue_scripts', array( $menu_localisation, 'retrieve_image_options' ), 8 );
			add_action( 'admin_enqueue_scripts', array( $menu_localisation, 'localize_menu' ), 1000 );
			add_action( 'admin_enqueue_scripts', array( $menu_localisation, 'localize_media_frame' ), 1000 );
		}
	}

	/**
	 * Registers the settings page with WordPress.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @return void
	 */
	public function add_options_page() {

		add_options_page(
			'cbParallax',
			'cbParallax',
			'manage_options',
			'cb_parallax_settings_page',
			array( $this, 'menu_display' )
		);
	}

	/**
	 * Renders the page for the menu.
	 *
	 * @since  0.6.0
	 *
	 * @param  $active_tab
	 *
	 * @return void / echo
	 */
	public function menu_display() {

		?>

		<div class="wrap cb-parallax-admin-menu">
			<!-- error message fix -->
			<h2></h2>
			<!--<h2 class="cb-parallax-page-title"><?php /*echo __( 'cbParallax Settings', $this->plugin_domain ); */?></h2>-->

			<form id="cb_parallax_form" method="POST" action="options.php">

				<?php
				settings_fields( 'cb_parallax_options' );
				do_settings_sections( 'cb_parallax_settings_group' );

				submit_button();
				?>

			</form>

		</div>
		<?php
	}

	/**
	 * Retrieve the name of the plugin.
	 *
	 * @since     0.6.0
	 * @access    public
	 * @return    string $plugin_name
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * Retrieve the domain of the plugin.
	 *
	 * @since     0.6.0
	 * @access    public
	 * @return    string $plugin_domain
	 */
	public function get_plugin_domain() {

		return $this->plugin_domain;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.6.0
	 * @access    public
	 * @return    string $plugin_version
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}

	/**
	 * Retrieve the reference to the image_options class.
	 *
	 * @since     0.6.0
	 * @access    public
	 * @return    object $image_options
	 */
	public function get_options() {

		return $this->options;
	}

}
