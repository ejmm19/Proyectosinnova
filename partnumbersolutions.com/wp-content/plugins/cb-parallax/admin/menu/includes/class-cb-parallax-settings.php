<?php

/**
 * The class that deals with the settings api.
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
class cb_parallax_settings {

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
	 * The array containing the default image_options.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    array $default_image_options
	 */
	private $default_image_options;

	/**
	 * The array containing the default plugin image_options.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    array $default_plugin_options
	 */
	private $default_plugin_options;

	/**
	 * The reference to the image_options class.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    object $image_options
	 */
	private $options;

	/**
	 * The reference to the validation class.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    object $validation
	 */
	private $validation;

	/**
	 * Retrieves the default image_options and sets them.
	 *
	 * @since  0.6.0
	 * @uses   get_default_image_options()
	 * @see    admin/menu/includes/class-cb-parallax-image_options.php
	 * @access private
	 * @return void
	 */
	private function set_default_image_options() {

		$this->default_image_options = $this->options->get_default_image_options();
	}

	/**
	 * Retrieves the default plugin image_options and sets them.
	 *
	 * @since  0.6.0
	 * @uses   get_default_plugin_options()
	 * @see    admin/menu/includes/class-cb-parallax-image_options.php
	 * @access private
	 * @return void
	 */
	private function set_default_plugin_options() {

		$this->default_plugin_options = $this->options->get_default_plugin_options();
	}

	/**
	 * Kicks off the settings class.
	 *
	 * @since 0.6.0
	 *
	 * @param string $plugin_name
	 * @param string $plugin_domain
	 */
	public function __construct( $plugin_name, $plugin_domain ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_domain = $plugin_domain;

		$this->load_dependencies();
		$this->set_default_image_options();
		$this->set_default_plugin_options();
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

		// The class that holds all plugin-related data.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/class-cb-parallax-options.php";

		// The class responsible for the validation tasks.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "includes/class-cb-parallax-validation.php";

		$this->options    = new cb_parallax_options( $this->get_plugin_name(), $this->get_plugin_domain() );
		$this->validation = new cb_parallax_validation( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_options() );
	}

	/**
	 * Adds the actions to be executed with WordPress:
	 * - Registers the settings group and the validation-callback with WordPress
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function add_hooks() {

		add_action( 'admin_init', array( &$this, 'register_settings' ), 1 );
		add_action( 'admin_init', array( &$this, 'initialize_settings' ), 10 );
	}

	/**
	 * Registers the settings group and the validation-callback with WordPress.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @return void
	 */
	public function register_settings() {

		register_setting( 'cb_parallax_options', 'cb_parallax_options', array( &$this, 'run_validation' ) );
	}

	/**
	 * Kicks off the validation process.
	 *
	 * @since  0.6.0
	 * @return $output
	 */
	public function run_validation( $input ) {

		$attachment_id = isset( $_POST['cb_parallax_attachment_id'] ) ? $_POST['cb_parallax_attachment_id'] : false;
		$image_url 	   = isset( $_POST['cb_parallax_background_image_url_hidden'] ) ? $_POST['cb_parallax_background_image_url_hidden'] : false;

		$input['cb_parallax_attachment_id']        = $attachment_id;
		$input['cb_parallax_background_image_url'] = $image_url;

		// Makes sure the data gets validated only once.
		if( true == get_transient( 'cb_parallax_already_validated_transient' ) ) {

			delete_transient( 'cb_parallax_already_validated_transient' );

			// Translate the strings if necessary.
			if ( get_locale() !== 'en_US' ) {

				$input = $this->translate_to_custom_locale( $input );
			}

			return $input;
		} else {

			$output = $this->validation->run( $input );

			return $output;
		}
	}

	/**
	 * Registers the sections, their headings and settings fields with WordPress.
	 *
	 * @hooked_action
	 *
	 * @since  0.6.0
	 * @uses   get_section_heading()
	 * @uses   get_all_options_arguments()
	 * @see    admin/menu/includes/class-cb-parallax-image_options.php
	 * @return void
	 */
	public function initialize_settings() {

		$this->add_settings_section( $this->options->get_section_heading( 'background-image' ) );
		$this->add_settings_field( $this->options->get_all_options_arguments() );
	}

	/**
	 * Registers the settings sections with WordPress.
	 *
	 * @since  0.6.0
	 *
	 * @param  array $args
	 *
	 * @return void
	 */
	private function add_settings_section( $args ) {

		add_settings_section(
			'cb_parallax_settings_section',
			$args['name'],
			array( &$this, $args['callback'] ),
			'cb_parallax_settings_group'
		);
	}

	/**
	 * Registers the settings fields with WordPress.
	 *
	 * @since  0.6.0
	 *
	 * @param  array $settings_fields
	 *
	 * @return void
	 */
	private function add_settings_field( $settings_fields ) {

		foreach ( $settings_fields as $option_key => $args ) {

			add_settings_field(
				$option_key,
				$args['name'],
				array( &$this, 'render_settings_field_callback' ),
				'cb_parallax_settings_group',
				'cb_parallax_settings_section',
				array(
					'option_key'    => $option_key,
					'name' 			=> $args['name'],
					'description'   => $args['description'],
					'input_type'    => $args['input_type'],
					'select_values' => $args['select_values'],
					'default_value' => $this->get_default_value( $option_key )
				)
			);
		}
	}

	/**
	 * Calls the corresponding callback function that renders the section field.
	 *
	 * @since  0.6.0
	 *
	 * @param  array $args
	 *
	 * @return void
	 */
	public function render_settings_field_callback( $args ) {

		switch ( $args['input_type'] ) {

			case( $args['input_type'] == 'checkbox' );

				$this->echo_checkbox_field( $args );
				break;

			case( $args['input_type'] == 'text' );

				$this->echo_text_field( $args );
				break;

			case( $args['input_type'] == 'color' );

				$this->echo_color_picker_field( $args );
				break;

			case( $args['input_type'] == 'select' );

				$this->echo_select_field( $args );
				break;

			case( $args['input_type'] == 'media' );

				$this->echo_media_field( $args );
				break;
		}
	}

	/**
	 * Renders a settings field with a checkbox.
	 *
	 * @since 0.6.0
	 *
	 * @param $args
	 *
	 * @return void echo
	 */
	public function echo_checkbox_field( $args ) {

		$options = get_option( 'cb_parallax_options' );

		$option_key = $args['option_key'];
		$description = $args['description'];
		$value = isset( $options[ $option_key ] ) ? $options[ $option_key ] : false;

		$html = '<label class="cb-parallax-switch label-for-cb-parallax-switch" title="' . $description . '">';
		$html .= '<input type="checkbox" id="' . $option_key . '" class="cb-parallax-switch-input cb-parallax-input-checkbox" name="' . 'cb_parallax_options' . '[' . $option_key . ']" value="1" ' . checked( 1, isset( $value ) ? $value : 0, false ) . '></input>';
		$html .= '<span class="cb-parallax-switch-label" data-on="On" data-off="Off"></span>';
		$html .= '<span class="cb-parallax-switch-handle"></span>';
		$html .= '</label>';

		echo $html;
	}

	/**
	 * Renders a settings field with a text field.
	 *
	 * @since 0.6.0
	 *
	 * @param $args
	 *
	 * @return void echo
	 */
	public function echo_text_field( $args ) {

		$options = get_option( 'cb_parallax_options' );

		$option_key = $args['option_key'];
		$description = $args['description'];
		$value = isset( $options[ $option_key ] ) ? $options[ $option_key ] : $args['default_value'];

		$html = '<p class="cb-parallax-input-container">';
		$html .= '<input type="text" id="' . $option_key . '" class="cb-parallax-input-text" title="' . $description . '" name="' . 'cb_parallax_options' . '[' . $option_key . ']" Placeholder="' . $this->default_image_options[ $option_key ] . '" value="' . $value . '"></input>';
		$html .= '</p>';

		echo $html;
	}

	/**
	 * Renders a settings field with a media field.
	 *
	 * @since 0.6.0
	 * @param $args
	 * @return void echo
	 */
	public function echo_media_field( $args ) {

		$options = get_option( 'cb_parallax_options' );
		$option_key = $args['option_key'];
		$description = $args['description'];
		$value = isset( $options[ $option_key ] ) ? $options[ $option_key ] : $args['default_value'];

		// Get the attachment id.
		$attachment_id = isset( $options['cb_parallax_attachment_id'] ) ? $options['cb_parallax_attachment_id'] : false;

		// Get image meta
		if ( false !== $attachment_id ) {
			$image = wp_get_attachment_image_src( absint( $attachment_id ), 'full' );
		}
		// Get the image URL.
		$url = isset( $image[0] ) ? $image[0] : '';

		// hidden fields.
		wp_nonce_field( 'cb_parallax_nonce_field', 'cb_parallax_nonce' );
		$html = '<input type="hidden" name="cb_parallax_attachment_id" id="cb_parallax_attachment_id" value="' . ( ! empty( $attachment_id ) ? esc_attr( $attachment_id ) : "" ) . '" />';
		$html .= '<input type="hidden" name="cb_parallax_background_image_url_hidden" id="cb_parallax_background_image_url_hidden" value="' . ( ! empty( $attachment_id ) ? esc_attr( $url ) : "" ) . '" />';

		// "Remove image" button.
		$html .= '<div class="cb-parallax-remove-media-button-container"><a class="cb-parallax-remove-media" href="#"><i class="fa fa-times" aria-hidden="true"></i></a></div>';

		// background image.
		$html .= '<div class="cb-parallax-image-container"><a href="#" class="cb-parallax-media-url"><img id="cb_parallax_background_image_url" title="' . $description . '" class="cb_parallax_background_image" src="' . esc_url( $value ) . '" style="max-width: 100%; max-height: 200px; display: block;" /></a></div>';

		echo $html;
	}

	/**
	 * Renders a settings field with a color picker.
	 *
	 * @since 0.6.0
	 *
	 * @param $args
	 *
	 * @return void echo
	 */
	public function echo_color_picker_field( $args ) {

		$options = get_option( 'cb_parallax_options' );

		$option_key = $args['option_key'];
		$description = $args['description'];
		$value = isset( $options[ $option_key ] ) ? $options[ $option_key ] : $args['default_value'];

		$html = '<p class="cb-parallax-input-container">';
		$html .= '<input type="text" id="' . $option_key . '" title="' . $description . '" name="' . 'cb_parallax_options' . '[' . $option_key . ']" Placeholder="' . $this->default_image_options[ $option_key ] . '" value="' . $value . '" class="' . $option_key . ' cb-parallax-color-picker cb-parallax-input-color-picker"></input>';
		$html .= '</p>';

		echo $html;
	}

	/**
	 * Renders a settings field with a select dropdown.
	 *
	 * @since  0.6.0
	 * @uses   translate_to_custom_locale()
	 *
	 * @param  $args
	 *
	 * @return void echo
	 */
	public function echo_select_field( $args ) {

		if ( get_locale() !== 'en_US' ) {

			$options = get_option( 'cb_parallax_options' );
			//$image_options = $this->translate_to_custom_locale( $image_options );
		} else {

			$options = get_option( 'cb_parallax_options' );
		}

		$option_key    = $args['option_key'];
		$description = $args['description'];
		$select_values = $args['select_values'];
		$value = isset( $options[ $option_key ] ) ? $options[ $option_key ] : $args['default_value'];

		$html = '<p class="cb-parallax-input-container" title="' . $description . '">';
		$html .= '<select name="' . 'cb_parallax_options' . '[' . $option_key . ']" class="floating-element fancy-select cb-parallax-fancy-select cb-parallax-input-select" id="' . $option_key . '">';
		foreach ( $select_values as $select_value ) {

			$html .= '<option value="' . $select_value . '"' . selected( $value, $select_value, false ) . '>' . $select_value . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';

		echo $html;
	}

	/**
	 * Translation helper function for some select box values.
	 * Since Nicescroll makes use of strings as parameters - and it does only "speak" English -
	 * this function translates the values that were stored in the default locale into strings of the current locale.
	 * This way, the localisation feature remains fully functional.
	 *
	 * @since  0.6.0
	 * @access private
	 * @see    admin/menu/includes/class-cb-parallax-validation.php | translate_to_default_locale()
	 * @return array $output
	 */
	private function translate_to_custom_locale( $input ) {

		$output = array();

		foreach ( $input as $option => $value ) {

			switch ( $option ) {

				// Custom background image_options.
				case( $option === 'cb_parallax_background_repeat' );

					if ( isset( $value ) && $value == 'no-repeat' ) {

						$output[ $option ] = __( 'no-repeat', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'repeat' ) {

						$output[ $option ] = __( 'repeat', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'horizontal' ) {

						$output[ $option ] = __( 'horizontal', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'vertical' ) {

						$output[ $option ] = __( 'vertical', $this->plugin_domain );
					} else {
						$output[ $option ] = $value;
					}

					break;

				case( $option === 'cb_parallax_vertical_alignment' || $option === 'cb_parallax_position_y' );

					if ( isset( $value ) && $value == 'top' ) {

						$output[ $option ] = __( 'top', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'center' ) {

						$output[ $option ] = __( 'center', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'bottom' ) {

						$output[ $option ] = __( 'bottom', $this->plugin_domain );
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_horizontal_alignment' || $option === 'cb_parallax_position_x' );

					if ( isset( $value ) && $value == 'left' ) {

						$output[ $option ] = __( 'left', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'center' ) {

						$output[ $option ] = __( 'center', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'right' ) {

						$output[ $option ] = __( 'right', $this->plugin_domain );
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_background_attachment' );

					if ( isset( $value ) && $value == 'fixed' ) {

						$output[ $option ] = __( 'fixed', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'scroll' ) {

						$output[ $option ] = __( 'scroll', $this->plugin_domain );
					} else {

						$output[ $option ] = $value;
					}
					break;

				// Parallax background image_options.
				case( $option === 'cb_parallax_direction' );

					if ( isset( $value ) && $value == 'vertical' ) {

						$output[ $option ] = __( 'vertical', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'horizontal' ) {

						$output[ $option ] = __( 'horizontal', $this->plugin_domain );
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_vertical_scroll_direction' );

					if ( isset( $value ) && $value == 'to top' ) {

						$output[ $option ] = __( 'to top', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'to bottom' ) {

						$output[ $option ] = __( 'to bottom', $this->plugin_domain );
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_horizontal_scroll_direction' );

					if ( isset( $value ) && $value == 'to the left' ) {

						$output[ $option ] = __( 'to the left', $this->plugin_domain );
					} else if ( isset( $value ) && $value == 'to the right' ) {

						$output[ $option ] = __( 'to the right', $this->plugin_domain );
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_overlay_image' );

					if ( isset( $value ) && $value == 'none' ) {

						$output[ $option ] = __( 'none', $this->plugin_domain );
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'cb_parallax_overlay_opacity' );

					if ( isset( $value ) && $value == 'default' ) {

						$output[ $option ] = __( 'default', $this->plugin_domain );
					} else {
						$output[ $option ] = $value;
					}
					break;

				default:
					$output[ $option ] = $value;
			}
		}

		return apply_filters( 'translate_to_default_locale', $output, $input );
	}

	/**
	 * Renders the description for the "image settings section".
	 *
	 * @since 0.6.0
	 * @return void
	 */
	public function background_image_settings_section_callback() {}

	/**
	 * Renders the description for the "plugin settings section".
	 *
	 * @since 0.6.0
	 * @return void
	 */
	public function plugin_settings_section_callback() {}

	/**
	 * Retrieves the default value for each option.
	 *
	 * @since 0.6.0
	 * @access private
	 *
	 * @return string / mixed
	 */
	private function get_default_value( $option_key ) {

		$default_options = array_merge( $this->default_image_options, $this->default_plugin_options );

		return $default_options[ $option_key ];
	}

	/**
	 * Retrieves the reference to the image_options class.
	 *
	 * @since  0.6.0
	 * @return object $image_options
	 */
	public function get_plugin_options() {

		return $this->options;
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
