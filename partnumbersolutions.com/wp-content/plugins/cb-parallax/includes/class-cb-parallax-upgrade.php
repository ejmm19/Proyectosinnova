<?php

/**
 * Executes function such as migrating image_options and refactoring existing meta_keys.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.6.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_upgrade {

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
	 * Maintains the allowed option values.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var    array $image_options_whitelist
	 */
	public $image_options_whitelist;

	/**
	 * Maintains the default image image_options
	 *
	 * @since  0.6.0
	 * @access public
	 * @var    array $default_image_options
	 */
	public $default_image_options;

	/**
	 * Maintains the default plugin image_options
	 *
	 * @since  0.6.0
	 * @access public
	 * @var    array $default_plugin_options
	 */
	public $default_plugin_options;

	/**
	 * 1. Defines the plugin's name, domain and version, and loads its basic dependencies.
	 * 2. Instanciates and assigns the loader.
	 * 3. Loads the language files.
	 * 4. Loads the admin part of the plugin.
	 * 5. Loads the public part of the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_domain = $plugin_domain;
		$this->plugin_version = $plugin_version;

		$this->load_dependencies();
		$this->retrieve_options();
	}

	/**
	 * Calls the function that upgrades the database.
	 *
	 * @hooked_action
	 *
	 * @since    0.6.0
	 * @return   void
	 * @access   public
	 */
	public function run() {

		$this->upgrade_postmeta();
		$this->migrate_options();
	}

	/**
	 * Loads it's dependencies.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies() {
		// The class that maintains all data like default values and their meta data.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/menu/includes/class-cb-parallax-options.php";
	}

	/**
	 * Sets the allowed image_options
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function retrieve_options() {

		$this->options = new cb_parallax_options( $this->get_plugin_name(), $this->get_plugin_domain() );

		$this->image_options_whitelist = $this->options->get_image_options_whitelist();

		$this->default_image_options = $this->options->get_default_image_options();

		$this->default_plugin_options = $this->options->get_default_plugin_options();
	}

	/**
	 * Calls the function that upgrades the database.
	 **
	 * @since    0.6.0
	 * @return   void
	 * @access   private
	 */
	private function upgrade_postmeta() {

		global $wpdb;

		$querystr = "
		    SELECT $wpdb->postmeta.post_id
		    FROM $wpdb->postmeta
		    WHERE $wpdb->postmeta.meta_key = 'cb_parallax'
		    ORDER BY $wpdb->postmeta.post_id ASC
		";
		$postmeta = $wpdb->get_results( $querystr, OBJECT );


		if( ! empty( $postmeta ) ) {

			foreach ( $postmeta as $i => $post ) {

				$pattern = '/cb_parallax_/';
				// Get the post meta data related to this plugin.
				$input = get_post_meta( $post->post_id, 'cb_parallax', true );

				// Retrieves the first key of the options array.
				$first_key = key( $input );

				// Here we check for a persistent meta_key value. If this one is not yet prefixed with "cb_parallax_",
				// we know we have to refactor this image_options array in order to upgrade the entries.
				if ( false == preg_match( $pattern, $first_key ) ) {

					// Retrieve the post meta data with refactored meta_keys.
					$output = $this->refactor_option_keys( $input );

					// Get the image name if there is one stored.
					$name = isset( $output['cb_parallax_overlay_image'] ) ? $output['cb_parallax_overlay_image'] : false;
					if( false !== $name ) {
						// Remane the overlay image.
						$output['cb_parallax_overlay_image'] = $this->refactor_initial_overlay_image_name( $name );
					}

					// Delete the post meta opition
					delete_post_meta( $post->post_id, 'cb_parallax' );
					// Save the newly created array to th edatabase.
					update_post_meta( $post->post_id, 'cb_parallax', $output );
				}
			}
		}

	}

	/**
	 * Moves the plugin image_options to the new image_options array
	 **
	 * @since    0.6.0
	 * @return   void
	 * @access   private
	 */
	private function migrate_options() {

		// The deprecated image_options array
		$input  = get_option( 'cb_parallax' );
		$output = null;

		// If there are image_options stored in the deprecated image_options array
		if( false !== $input && is_array( $input ) ) {

			$output = $this->migrate( $input );
			// Here we save the now prefixed image_options back to the new image_options array.
			update_option( 'cb_parallax_options', $output );
			// Clean up unneeded option entry.
			delete_option( 'cb_parallax' );
		}

	}

	/**
	 * Renames the option keys.
	 *
	 * @param array $input
	 *
	 * @since    0.6.0
	 * @return   array $updated_meta_values
	 * @access   private
	 */
	private function refactor_option_keys( $input ) {

		$pattern = '/cb_parallax_/';
		$prefix = 'cb_parallax_';
		$output = null;
		// Prefix the option keys.
		foreach( $input as $key => $value) {
			// ...if necessary.
			if( ! preg_match( $pattern, $key ) ) {
				$output[ $prefix . $key ] = $value;
			}
		}

		// Correct the option key for the background image url manually.
		if( ! array_key_exists( 'cb_parallax_background_image_url', $input ) ) {

			$output['cb_parallax_background_image_url'] = $output['cb_parallax_background_image'];
			unset( $output['cb_parallax_background_image'] );
		}

		return $this->add_default_image_options( $output );
	}

	/**
	 * Renames the option keys.
	 *
	 * @param array $input
	 *
	 * @since    0.6.0
	 * @return   mixed
	 * @access   private
	 */
	private function refactor_initial_overlay_image_name( $name ) {

		// The old overlay image names.
		$old_image_names = array(
			'none' => __( 'none', $this->plugin_domain ),
			'01'   => '01.png',
			'02'   => '02.png',
			'03'   => '03.png',
			'04'   => '04.png',
			'05'   => '05.png',
			'06'   => '06.png',
			'07'   => '07.png',
			'08'   => '08.png',
			'09'   => '09.png',
		);

		$new_image_names = array(
			'none' => __( 'none', $this->plugin_domain ),
			'01'   => '01',
			'02'   => '02',
			'03'   => '03',
			'04'   => '04',
			'05'   => '05',
			'06'   => '06',
			'07'   => '07',
			'08'   => '08',
			'09'   => '09',
		);

		// Get the key.
		$key = array_search( $name, $old_image_names );
		// Set the new overlay image name.
		$name = $new_image_names[$key];

		return $name;
	}

	/**
	 * Adds default image settings to the image_options array.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return array
	 */
	private function add_default_image_options( $output ) {

		return array_merge( $this->default_plugin_options, $output );
	}

	/**
	 * Moves the stored image_options into a new image_options array.
	 **
	 * @since    0.6.0
	 * @access   private
	 */
	private function migrate( $input ) {

		$new_array = null;
		$prefix = 'cb_parallax_';

		foreach($input as $key => $value ) {

			$new_array[$prefix . $key] = $value;
		}

		return $this->add_default_plugin_options( $new_array );
	}

	/**
	 * Merge default image_options into the image_options-array.
	 *
	 * @param $output
	 *
	 * @since  0.6.0
	 * @access private
	 * @return array $output
	 */
	private function add_default_plugin_options( $output ) {

		return array_merge( $this->default_plugin_options, $output );
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

}
