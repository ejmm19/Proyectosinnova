<?php

/**
 * The class responsible for sanitizing and validating the user inputs.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.6.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/menu/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_validation {

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
	 * The reference to the image_options class.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    object $image_options
	 */
	private $options;

	/**
	 * Maintains the whitelist for the image options.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var    array $image_options_whitelist
	 */
	public $image_options_whitelist;

	/**
	 * Maintains the whitelist for the plugin options.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var    array $plugin_options_whitelist
	 */
	public $plugin_options_whitelist;

	/**
	 * Maintains the default image image options
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array $default_image_options
	 */
	public $default_image_options;

	/**
	 * Assigns the required parameters to its instance.
	 *
	 * @since 0.6.0
	 *
	 * @param string $plugin_name
	 * @param string $plugin_domain
	 * @param object $options
	 */
	public function __construct( $plugin_name, $plugin_domain ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_domain = $plugin_domain;

		$this->load_dependencies();
		$this->retrieve_options();
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/class-cb-parallax-options.php";
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

		$this->plugin_options_whitelist = $this->options->get_plugin_options_whitelist();

		$this->default_image_options = $this->options->get_default_image_options();
	}

	/**
	 * Kicks off sanitisation and validation - if there's any input given.
	 *
	 * @since  0.6.0
	 *
	 * @param  array $input
	 *
	 * @return array $output
	 */
	public function run( $input ) {

		$input  = $this->sanitize( $input );
		return $this->validate( $input );
	}

	/**
	 * Sanitizes the input.
	 *
	 * @since  0.6.0
	 *
	 * @param  array $input
	 *
	 * @return array $output
	 */
	private function sanitize( $input ) {

		$output = array();

		foreach ( $input as $key => $value ) {

			if ( isset ( $input[ $key ] ) ) {
				$output[ $key ] = strip_tags( stripslashes( $value ) );
			}
		}

		return apply_filters( 'sanitize', $output, $input );
	}

	/**
	 * Validates the input.
	 *
	 * since  0.6.0
	 * @uses   get_default_image_options()
	 * @see    admin/menu/includes/class-cb-parallax-image_options.php
	 * @uses   translate_to_default_locale()
	 *
	 * @param  array $input
	 *
	 * @return array $output
	 */
	private function validate( $input ) {

		$defaults      = $this->options->get_default_image_options();
		//$notice_levels = $this->image_options->get_notice_levels();
		$output        = array();
		//$errors        = array();

		// Translate the strings if necessary.
		if ( get_locale() !== 'en_US' ) {

			$input = $this->translate_to_default_locale( $input );
		}

		$i = 0;
		foreach ( $input as $option_key => $value ) {

			switch ( $option_key ) {

				case ( $option_key === 'cb_parallax_attachment_id' );

					if ( false == is_int( absint( $value ) ) ) {

						$value = ''/*$defaults[ $option_key ]*/;
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursorcolor_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_background_image_url' ); // @todo check for url

					if ( true == filter_var( 'example.com', FILTER_VALIDATE_URL ) ) {

						$value                 = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursorcolor_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_background_color' || $option_key === 'cb_parallax_overlay_color' );

					if ( $value !== '' ) {

						if ( isset( $value ) && ! preg_match( '/^#[a-f0-9]{3,6}$/i', $value ) ) {

							$value                 = '';
							/*$errors[ $option_key ] = array(
								'option_key'   => $option_key,
								'name'         => ucfirst( $option_key ),
								'index'        => $i,
								'notice_level' => $notice_levels[ $option_key ],
								'message'      => $this->cursoropacitymin_error_message(),
							);*/
						}
					}

					break;

				case ( $option_key === 'cb_parallax_position_x' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array($value, $whitelist_options ) ) {

						$value                 = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_position_y' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_background_attachment' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_direction' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_vertical_scroll_direction' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case( $option_key === 'cb_parallax_horizontal_scroll_direction' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_horizontal_alignment' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case( $option_key === 'cb_parallax_vertical_alignment' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case ( $option_key === 'cb_parallax_overlay_image' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case( $option_key === 'cb_parallax_overlay_opacity' );

					$whitelist_options = $this->image_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case( $option_key === 'cb_parallax_parallax_enabled' );

					$whitelist_options = $this->plugin_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;

				case( $option_key === 'cb_parallax_global' );

					$whitelist_options = $this->plugin_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;
				case( $option_key === 'cb_parallax_allow_override' );

					$whitelist_options = $this->plugin_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;
				case( $option_key === 'cb_parallax_preserve_scrolling' );

					$whitelist_options = $this->plugin_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;
				case( $option_key === 'cb_parallax_disable_on_mobile' );

					$whitelist_options = $this->plugin_options_whitelist[ $option_key ];

					if ( ! in_array( $value, $whitelist_options ) ) {

						$value = $defaults[ $option_key ];
						/*$errors[ $option_key ] = array(
							'option_key'   => $option_key,
							'name'         => ucfirst( $option_key ),
							'index'        => $i,
							'notice_level' => $notice_levels[ $option_key ],
							'message'      => $this->cursoropacitymax_error_message(),
						);*/
					}

					break;
			}
			// The array holding the processed values.
			$output[ $option_key ] = $value;
			$i ++;
		}

		// Fill unset image_options with "false".
		foreach ( $defaults as $key => $value ) {

			$output[ $key ] = isset( $output[ $key ] ) ? $output[ $key ] : false;
		}

		/*if ( get_locale() !== 'en_US' ) {

			$output = $this->translate_to_default_locale( $output );
		}*/

		// If there were errors and transients were created, we create one more containing the ids of the previously created ones.
		/*if ( isset( $errors ) && ( ! empty( $errors ) ) ) {

			set_transient( 'cb_parallax_validation_transient', $errors, 60 );
		}*/

		set_transient( 'cb_parallax_already_validated_transient', true, 60 );

		return apply_filters( 'validate', $output, $input );
	}

	/**
	 * Helper function, that translates "non-default-locale strings" into strings of the default locale.
	 * This task is necessary, since Nicescroll needs some strings as parameters and they have to be served in English.
	 * With this step, localisation remains fully functional.
	 *
	 * @since  0.6.0
	 * @access private
	 * @see    admin/menu/includes/class-cb-parallax-settings.php | translate_to_custom_locale()
	 *
	 * @param  $input
	 *
	 * @return mixed
	 */
	private function translate_to_default_locale( $input ) {

		$output = array();

		foreach ( $input as $option => $value ) {

			switch ( $option ) {

				// Custom background image_options.
				case( $option === 'cb_parallax_background_repeat' );

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

				case( $option === 'cb_parallax_vertical_alignment' || $option === 'cb_parallax_position_y' );

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

				case( $option === 'cb_parallax_horizontal_alignment' || $option === 'cb_parallax_position_x' );

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

				case( $option === 'cb_parallax_background_attachment' );

					if ( isset( $value ) && $value == __( 'fixed', $this->plugin_domain ) ) {

						$output[ $option ] = 'fixed';
					} else if ( isset( $value ) && $value == __( 'scroll', $this->plugin_domain ) ) {

						$output[ $option ] = 'scroll';
					} else {

						$output[ $option ] = $value;
					}
					break;

				// Parallax background image_options.
				case( $option === 'cb_parallax_direction' );

					if ( isset( $value ) && $value == __( 'vertical', $this->plugin_domain ) ) {

						$output[ $option ] = 'vertical';
					} else if ( isset( $value ) && $value == __( 'horizontal', $this->plugin_domain ) ) {

						$output[ $option ] = 'horizontal';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_vertical_scroll_direction' );

					if ( isset( $value ) && $value == __( 'to top', $this->plugin_domain ) ) {

						$output[ $option ] = 'to top';
					} else if ( isset( $value ) && $value == __( 'to bottom', $this->plugin_domain ) ) {

						$output[ $option ] = 'to bottom';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_horizontal_scroll_direction' );

					if ( isset( $value ) && $value == __( 'to the left', $this->plugin_domain ) ) {

						$output[ $option ] = 'to the left';
					} else if ( isset( $value ) && $value == __( 'to the right', $this->plugin_domain ) ) {

						$output[ $option ] = 'to the right';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_overlay_image' );

					if ( isset( $value ) && $value == __( 'none', $this->plugin_domain ) ) {

						$output[ $option ] = 'none';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_overlay_opacity' );

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

		return apply_filters( 'translate_to_default_locale', $output );
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
