<?php

/**
 * This class is responsible for localizing the admin part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_meta_box_localisation {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
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
	 * The array holding the "meta data" of the image.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $image_options
	 */
	private $image_options;

	/**
	 * Kicks off localisation of the admin part.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;

		$this->add_hooks();
		$this->load_dependencies();
	}

	/**
	 * Register all necessary hooks for this part of the plugin to work with WordPress, if the user has admin rights.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	private function add_hooks() {

		if ( is_admin() ) {

			add_action( 'admin_enqueue_scripts', array( &$this, 'retrieve_image_options' ), 11 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'localize_meta_box' ), 1000 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'localize_media_frame' ), 1000 );
		}
	}

	/**
	 * Loads it's dependencies.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies() {

		// The class responsible for all tasks concerning the image_options.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "../admin/menu/includes/class-cb-parallax-options.php";

		$this->options = new cb_parallax_options( $this->get_plugin_name(), $this->get_plugin_domain() );
	}

	/**
	 * Retrieves the source, width and and height of the custom background image,
	 * as well as the possible directions and the actually selected direction ( for "mode").
	 * This is used to control display of the elements inside the meta box.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function retrieve_image_options() {

		$image_options = get_post_meta( get_the_ID(), 'cb_parallax', true );
		$image = null;

		// If we have no related post meta data, we don't do anything here.
		if ( false == $image_options || '' == $image_options ) {

			$image_options = $this->options->get_default_image_options();
		}

		$post_data        = null;
		$options          = null;
		$pattern          = '/cb_parallax_/';
		$excluded_options = array(
			/*'cb_parallax_background_color',*/
			'cb_parallax_overlay_color',
			'cb_parallax_attachment_id',
			'cb_parallax_background_image_url'
		);
		$whitelist        = array_merge( $this->options->get_image_options_whitelist(), $this->options->get_plugin_options_whitelist() );
		$default_options  = array_merge( $this->options->get_default_image_options(), $this->options->get_default_plugin_options() );
		// Match the option keys against the image option values, filtered trough the options-whitelist
		foreach ( $default_options as $option_key => $value ) {

			if ( ! in_array( $option_key, $excluded_options ) ) {

				if ( isset( $image_options[ $option_key ] ) ) {

					// Remove the prefix
					$key = preg_replace( $pattern, '', $option_key );
					// Prepare the option key for the script
					$key = lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $key ) ) ) );

					if ( in_array( $image_options[ $option_key ], $whitelist[ $option_key ] ) ) {

						$options[ $key ] = $image_options[ $option_key ];
					} else {
						$options[ $key ] = $default_options[ $option_key ];
					}
				}
			}
		}

		// The following values have no defaults, so we check them "by hand":
		// We retrieve these values "by hand" since there is no default value that could be used as a pattern to match against.
		//$colors['backgroundColor'] = isset( $image_options['cb_parallax_background_color'] ) ? $image_options['cb_parallax_background_color'] : '';
		$colors['overlayColor']    = isset( $image_options['cb_parallax_overlay_color'] ) ? $image_options['cb_parallax_overlay_color'] : '';

		// Check the color values
		foreach ( $colors as $color_key => $color_value ) {

			if ( isset( $color_value ) && ! preg_match( '/^#[a-f0-9]{3,6}$/i', $color_value ) ) {

				$options[ $color_key ] = '';
			} else {
				$options[ $color_key ] = $color_value;
			}
		}
		// Add the attachment id.
		$attachment_id = $options['attachmentId'] = isset( $image_options['cb_parallax_attachment_id'] ) ? $image_options['cb_parallax_attachment_id'] : '';
		// If an attachment ID was found, get the image source.
		if ( false !== $attachment_id ) {

			$image = wp_get_attachment_image_src( absint( $attachment_id ), 'full' );
		}

		// Set the url.
		$options['backgroundImageUrl'] = isset( $image[0] ) ? $image[0] : '';
		// Set the image dimensions.
		$options['attachmentWidth']  = isset( $image[1] ) ? $image[1] : '';
		$options['attachmentHeight'] = isset( $image[2] ) ? $image[2] : '';

		// Below we retrieve localized strings - the actual value and the allowed options for that value to check against inside the script.
		$direction = ! empty( $post_meta['cb_parallax_direction'] ) ? $post_meta['cb_parallax_direction'] : false;
		if ( false !== $direction && $direction === __( 'vertical', $this->plugin_domain ) ) {

			$options['actualDirection'] = __( 'vertical', $this->plugin_domain );
		} else {

			$options['actualDirection'] = __( 'horizontal', $this->plugin_domain );
		}

		//
		//$options['canParallax'] = $this->can_parallax( $image );

		/*if ( 'en_US' != get_locale() ) {
			// Translates parameters into the default locale to propperly serve the script.
			$options = $this->translate_to_default_locale( $options );
		}*/

		$this->image_options = $options;
	}

	/**
	 * Retrieves - so far German - strings to localize some css-pseudo-selectors. They are not implemented as localizeable strings,
	 * since there may be issues with translated words for "on" and "off" regarding the limited space on the switch.
	 * I'll leave it like that for now.
	 *
	 * @since  0.1.0
	 * @see    admin/css/switch.css
	 * @see    admin/js/admin.js
	 * @access private
	 * @return array $labels
	 */
	private function get_switches_texts() {

		$locale = $this->get_locale();

		switch ( $locale ) {

			case( $locale == 'de_DE' );

				$labels = array(
					'locale'       => $locale,
					'switchesText' => array( 'On' => 'Ein', 'Off' => 'Aus' )
				);
				break;

			default:

				$labels = array(
					'locale'       => 'default',
					'switchesText' => array( 'On' => 'On', 'Off' => 'Off' )
				);
		}

		return $labels;
	}

	/**
	 * Localizes the text on the color picker.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return array
	 */
	private function get_background_color_text() {

		return array(
			'backgroundColorText' => __( 'Background Color', $this->plugin_domain ),
			'overlayColorText'    => __( 'Overlay Color', $this->plugin_domain ),
			'noneString'          => __( 'none', $this->plugin_domain ),
			'verticalString'      => __( 'vertical', $this->plugin_domain )
		);
	}

	/**
	 * Retrieves the locale of the WordPress installation.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return string
	 */
	private function get_locale() {

		return get_locale();
	}

	/**
	 * Localizes the meta box.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function localize_meta_box() {

		wp_localize_script(
			$this->plugin_name . '-admin-js',
			'cbParallax',
			array_merge(
				$this->get_switches_texts(),
				$this->get_background_color_text(),
				$this->image_options
			)
		);
	}

	/**
	 * Localizes the "media frame".
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function localize_media_frame() {

		wp_localize_script(
			$this->plugin_name . '-admin-js',
			'cbParallaxMediaFrame',
			array(
				'title'  => __( 'Set Background Image', $this->plugin_domain ),
				'button' => __( 'Set background image', $this->plugin_domain )
			)
		);
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

}
