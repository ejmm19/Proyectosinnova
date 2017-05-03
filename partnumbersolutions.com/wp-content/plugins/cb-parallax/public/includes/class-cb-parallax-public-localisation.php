<?php

/**
 * This class is responsible for localizing the public part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 *  License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_public_localisation {

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

	/**
	 * The array holding the meta data.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      array $image_options
	 */
	public $image_options;

	/**
	 * The array holding the meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $image_data
	 */
	private $image_data;

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
	 * @since  0.1.0
	 * @access public
	 * @var    array $image_options_whitelist
	 */
	public $image_options_whitelist;

	/**
	 * Maintains the default image image_options
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array $default_image_options
	 */
	public $default_image_options;

	/**
	 * Kicks off localisation of the public part of the plugin.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 * @param    string $meta_key
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;
		$this->image_options  = array();

		$this->add_hooks();
		$this->load_dependencies();
		$this->retrieve_options();
	}

	/**
	 * Register all necessary hooks for this part of the plugin to work with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function add_hooks() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'retrieve_image_data' ), 12 );
		add_action( 'template_redirect', array( &$this, 'retrieve_image_options' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'localize_frontend' ), 1000 );
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
	}

	/**
	 * Retrieves the image options.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function retrieve_options() {

		$this->options = new cb_parallax_options( $this->get_plugin_name(), $this->get_plugin_domain() );

		$this->image_options_whitelist = $this->options->get_image_options_whitelist();

		$this->default_image_options = $this->options->get_default_image_options();
	}

	/**
	 * Retrieves the image meta data.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   mixed
	 */
	public function retrieve_image_data() {

		// Retrieve the image options
		$options = $this->fetch_options();

		// If we have no related post meta data, we don't do anything here.
		if ( false == $options || '' == $options ) {
			return array();
		}

		$image_attributes = null;
		$attachment_id    = isset( $options['cb_parallax_attachment_id'] ) ? $options['cb_parallax_attachment_id'] : '';

		// If an attachment ID was found, get the image height.
		if ( ! empty( $attachment_id ) ) {
			$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' );

			$this->image_data['imageWidth']  = $image_attributes[1];
			$this->image_data['imageHeight'] = $image_attributes[2];
		} else {

			$this->image_data['imageWidth']  = 0;
			$this->image_data['imageHeight'] = 0;
		}

		// Add this value to the image options array.
		$this->image_options['canParallax'] = $this->can_parallax();

	}

	/**
	 * Retrieves the post meta data.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function retrieve_image_options() {

		// Retrieve the image options.
		$image_options = $this->options->fetch_options();

		// If we have no related post meta data, we don't do anything here.
		if ( false == $image_options || '' == $image_options ) {
			return;
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
		$default_options  = array_merge($this->options->get_default_image_options(), $this->options->get_default_plugin_options() );
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
		// Add the background image url
		$options['backgroundImageUrl'] = isset( $image_options['cb_parallax_background_image_url'] ) ? $image_options['cb_parallax_background_image_url'] : '';

		$options['parallaxEnabled'] = isset( $image_options['cb_parallax_parallax_enabled'] ) ? $image_options['cb_parallax_parallax_enabled'] : '0';

		$overlay_image = $options['overlayImage'];
		$options['overlayImage'] = '' != $overlay_image ? $this->convert_overlay_image_name( $overlay_image ) : '';

		// If an attachment ID was found, get the image source.
		if ( false !== $attachment_id ) {

			$image = wp_get_attachment_image_src( absint( $attachment_id ), 'full' );
			$options['backgroundImageUrl'] = isset( $image[0] ) ? $image[0] : '';
		}

		// Translates parameters into the default locale to propperly serve the script.
		if ( 'en_US' != get_locale() ) {

			$options = $this->translate_to_default_locale( $options );
		}

		$this->image_options = $options;
	}

	/**
	 * Retrieves the plugin option values.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @return   array $plugin_options
	 */
	private function retrieve_plugin_options() {

		// Retrieve the image options.
		$image_options = get_option( 'cb_parallax_options' );

		$post_data        = null;
		$plugin_options          = null;
		$pattern          = '/cb_parallax_/';
		$excluded_options = array(
			'cb_parallax_parallax_enabled'
		);
		$default_options  = $this->options->get_default_plugin_options();
		// Match the option keys against the image option values, filtered trough the options-whitelist
		foreach ( $default_options as $option_key => $value ) {

			if ( ! in_array( $option_key, $excluded_options ) ) {

				// Remove the prefix
				$key = preg_replace( $pattern, '', $option_key );
				// Prepare the option key for the script
				$key = lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $key ) ) ) );

				if ( isset( $image_options[ $option_key ] ) ) {

					$plugin_options[ $key ] = $image_options[ $option_key ];
				} else {
					$plugin_options[ $key ] = '0';
				}
			}

		}

		return $plugin_options;
	}

	/**
	 * Localizes the public part of the plugin.
	 *
	 * @hooked_action
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function localize_frontend() {

		if( $this->has_background_image() ) {

			wp_localize_script(
				$this->plugin_name . '-public-js',
				'cbParallax',
				array_merge(
					$this->image_data,
					$this->image_options,
					$this->get_none_string(),
					$this->get_overlay_image_path(),
					array( 'pluginOptions' => $this->get_plugin_options() ),
					$this->retrieve_plugin_options()
				)
			);
		} else {

			wp_localize_script(
				$this->plugin_name . '-public-js',
				'cbParallax',
				array_merge(
					$this->get_none_string(),
					array( 'pluginOptions' => $this->get_plugin_options() ),
					$this->retrieve_plugin_options()
				)
			);
		}
	}

	/**
	 * Retrieves the path to the folder containing the overlay images.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   array
	 */
	private function get_overlay_image_path() {

		$path = site_url() . '/wp-content/plugins/cb-parallax/public/images/overlays/';

		return array( 'overlayPath' => $path );
	}

	/**
	 * Retrieves the options either global or those that are on a per-post-basis.
	 *
	 * @since    0.2.5
	 * @access   private
	 *
	 * @param object $post
	 *
	 * @return mixed / void
	 */
	private function fetch_options() {

		return $this->options->fetch_options();
	}


	private function can_parallax() {

		// Determines weather parallax is possible or not.
		if ( $this->image_data['imageWidth'] >= 1920 && $this->image_data['imageHeight'] >= 1200 ) {

			return true;
		} else {

			return false;
		}
	}


	private function convert_overlay_image_name( $input ) {

		$name = null;

		if ( preg_match( '/\s/', $input ) ) {
			// Remove whitespace and capitalize.
			$name = preg_replace( '/ /', '-', $input );
			$name     = implode( ' ', array_map( 'strtolower', explode( ' ', $name ) ) );
		} else {

			$name = strtolower($input);
		}

		return $name . '.png';
	}

	/**
	 * Helper function, that translates "non-default-locale strings" into strings of the default locale,
	 * to propperly serve the script.
	 *
	 * @since  0.1.0
	 * @access private
	 *
	 * @param  $post_meta
	 *
	 * @return array
	 */
	private function translate_to_default_locale( $post_meta ) {

		$output = array();

		foreach ( $post_meta as $option => $value ) {

			switch ( $option ) {

				// Custom background image_options.
				case( $option === 'backgroundRepeat' );

					if ( isset( $value ) && $value == __( 'no-repeat', $this->plugin_domain ) ) {

						$output[ $option ] = 'no-repeat';
					} else if ( isset( $value ) && $value == __( 'repeat', $this->plugin_domain ) ) {

						$output[ $option ] = 'repeat';
					} else if ( isset( $value ) && $value == __( 'horizontal', $this->plugin_domain ) ) {

						$output[ $option ] = 'horizontal';
					} else if ( isset( $value ) && $value == __( 'vertical', $this->plugin_domain ) ) {

						$output[ $option ] = 'vertical';
					} else {
						$output[ $option ] = $value;
					}

					break;

				case( $option === 'verticalAlignment' || $option === 'positionY' );

					if ( isset( $value ) && $value == __( 'top', $this->plugin_domain ) ) {

						$output[ $option ] = 'top';
					} else if ( isset( $value ) && $value == __( 'center', $this->plugin_domain ) ) {

						$output[ $option ] = 'center';
					} else if ( isset( $value ) && $value == __( 'bottom', $this->plugin_domain ) ) {

						$output[ $option ] = 'bottom';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'horizontalAlignment' || $option === 'positionX' );

					if ( isset( $value ) && $value == __( 'left', $this->plugin_domain ) ) {

						$output[ $option ] = 'left';
					} else if ( isset( $value ) && $value == __( 'center', $this->plugin_domain ) ) {

						$output[ $option ] = 'center';
					} else if ( isset( $value ) && $value == __( 'right', $this->plugin_domain ) ) {

						$output[ $option ] = 'right';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'backgroundAttachment' );

					if ( isset( $value ) && $value == __( 'fixed', $this->plugin_domain ) ) {

						$output[ $option ] = 'fixed';
					} else if ( isset( $value ) && $value == __( 'scroll', $this->plugin_domain ) ) {

						$output[ $option ] = 'scroll';
					} else {

						$output[ $option ] = $value;
					}
					break;

				// Parallax background image_options.
				case( $option === 'direction' );

					if ( isset( $value ) && $value == __( 'vertical', $this->plugin_domain ) ) {

						$output[ $option ] = 'vertical';
					} else if ( isset( $value ) && $value == __( 'horizontal', $this->plugin_domain ) ) {

						$output[ $option ] = 'horizontal';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'verticalScrollDirection' );

					if ( isset( $value ) && $value == __( 'to top', $this->plugin_domain ) ) {

						$output[ $option ] = 'to top';
					} else if ( isset( $value ) && $value == __( 'to bottom', $this->plugin_domain ) ) {

						$output[ $option ] = 'to bottom';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'horizontalScrollDirection' );

					if ( isset( $value ) && $value == __( 'to the left', $this->plugin_domain ) ) {

						$output[ $option ] = 'to the left';
					} else if ( isset( $value ) && $value == __( 'to the right', $this->plugin_domain ) ) {

						$output[ $option ] = 'to the right';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'overlayImage' );

					if ( isset( $value ) && $value == __( 'none', $this->plugin_domain ) ) {

						$output[ $option ] = 'none';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'overlayOpacity' );

					if ( isset( $value ) && $value == __( 'default', $this->plugin_domain ) ) {

						$output[ $option ] = 'default';
					} else {
						$output[ $option ] = $value;
					}
					break;

				default:
					$output[ $option ] = $value;
			}
		}

		return apply_filters( 'translate_to_default_locale', $output, $post_meta );
	}

	/**
	 * Localizes the text on the color picker.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return array
	 */
	private function get_none_string() {

		return array(
			'noneString' => 'none'
		);
	}

	/**
	 * Retrieves the option.
	 *
	 * @since    0.2.1
	 * @access   private
	 * @return   array
	 */
	private function get_plugin_options() {

		$options             = get_option( 'cb_parallax_options' );
		$global              = isset( $options['cb_parallax_global'] ) ? $options['cb_parallax_global'] : false;
		$allow_override      = isset( $options['cb_parallax_allow_override'] ) ? $options['cb_parallax_allow_override'] : false;
		$scrolling_preserved = isset( $options['cb_parallax_preserve_scrolling'] ) ? $options['cb_parallax_preserve_scrolling'] : false;
		$disabled_on_mobile  = isset( $options['cb_parallax_disable_on_mobile'] ) ? $options['cb_parallax_disable_on_mobile'] : false;

		return array(
			'isGlobal'             => $global,
			'doesAllowOverride'    => $allow_override,
			'isScrollingPreserved' => $scrolling_preserved,
			'isDisabledOnMobile'   => $disabled_on_mobile
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

	/**
	 * Determines if there is a background image related to the requested post (type).
	 *
	 * @since    0.6.0
	 * @access   private
	 * @return   bool
	 */
	private function has_background_image() {

		global $post;

		$image_options              = get_option( 'cb_parallax_options' );
		$post_meta                  = isset( $post ) ? get_post_meta( $post->ID, 'cb_parallax', true ) : false;
		$menu_options_attachment_id = isset( $image_options['cb_parallax_attachment_id'] ) ? $image_options['cb_parallax_attachment_id'] : false;
		$post_meta_attachment_id    = isset( $post_meta['cb_parallax_attachment_id'] ) ? $post_meta['cb_parallax_attachment_id'] : false;

		$image_source = $this->determine_image_source();

		if ( 'global' == $image_source ) {

			if ( false !== $menu_options_attachment_id ) {

				return true;
			} else {

				return false;
			}
		} else {

			if ( false !== $post_meta_attachment_id ) {

				return true;
			} else {

				return false;
			}
		}
	}

	/**
	 * Determines the image source.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @return   string $source_type
	 */
	private function determine_image_source() {

		global $post;

		$post_meta      = isset( $post ) ? get_post_meta( $post->ID, 'cb_parallax', true ) : false;
		$post_has_image = isset( $post_meta['cb_parallax_attachment_id'] ) ? $post_meta['cb_parallax_attachment_id'] : false;

		$options        = get_option( 'cb_parallax_options' );
		$is_global      = isset( $options['cb_parallax_global'] ) ? $options['cb_parallax_global'] : false;
		$allow_override = isset( $options['cb_parallax_allow_override'] ) ? $options['cb_parallax_allow_override'] : false;
		$attachment_id  = isset( $options['cb_parallax_attachment_id'] ) ? $options['cb_parallax_attachment_id'] : '';

		$source_type = null;

		if ( ! $is_global || $is_global && $attachment_id == '' ) {
			$source_type = 'per_post';

		} else if ( $is_global && $allow_override && $post_has_image ) {
			$source_type = 'per_post';
		} else {
			$source_type = 'global';
		}

		return $source_type;

	}

}
