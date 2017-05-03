<?php

/**
 * The class that maintains the default image_options and the related meta data.
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
class cb_parallax_options {

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
	 * Returns the (image) image_options and their meta data.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return array $image_options
	 */
	private function image_options() {

		$options = array(
			'cb_parallax_background_image_url'        => array(
				'option_key'     => 'cb_parallax_background_image_url',
				'name'           => __( 'Background Image', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => ''/*__( 'Choose a background image.', $this->plugin_domain )*/,
				'default_value'  => '',
				'input_type'     => 'media',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			),
			/*'cb_parallax_background_color'            => array(
				'option_key'     => 'cb_parallax_background_color',
				'name'           => __( 'Background Color', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Set a background color. Best to be used with partially transparent images.', $this->plugin_domain ),
				'default_value'  => '',
				'input_type'     => 'color',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			),*/
			'cb_parallax_background_repeat'           => array(
				'option_key'     => 'cb_parallax_background_repeat',
				'name'           => __( 'Repeat', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Set repeat property.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_background_repeat']['no-repeat'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_background_repeat'],
			),
			'cb_parallax_position_x'                  => array(
				'option_key'     => 'cb_parallax_position_x',
				'name'           => __( 'Position X', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Set the horizontal position of the background image.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_position_x']['center'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_position_x'],
			),
			'cb_parallax_position_y'                  => array(
				'option_key'     => 'cb_parallax_position_y',
				'name'           => __( 'Position Y', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Set the vertical position of the background image.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_position_y']['center'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_position_y'],
			),
			'cb_parallax_background_attachment'       => array(
				'option_key'     => 'cb_parallax_background_attachment',
				'name'           => __( 'Attachment', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Set the attachment.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_background_attachment']['fixed'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_background_attachment'],
			),
			'cb_parallax_direction'                   => array(
				'option_key'     => 'cb_parallax_direction',
				'name'           => __( 'Direction', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Choose horizontal or vertical parallax direction.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_direction']['vertical'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_direction'],
			),
			'cb_parallax_vertical_scroll_direction'   => array(
				'option_key'     => 'cb_parallax_vertical_scroll_direction',
				'name'           => __( 'Vertical Scroll Direction', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Choose whether the image should move up- or downwards on page scroll.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_vertical_scroll_direction']['top'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_vertical_scroll_direction'],
			),
			'cb_parallax_horizontal_scroll_direction' => array(
				'option_key'     => 'cb_parallax_horizontal_scroll_direction',
				'name'           => __( 'Horizontal Scroll Direction', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Choose whether the image should move to the left or to the right on page scroll.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_horizontal_scroll_direction']['left'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_horizontal_scroll_direction']
			),
			'cb_parallax_horizontal_alignment'        => array(
				'option_key'     => 'cb_parallax_horizontal_alignment',
				'name'           => __( 'Horizontal Alignment', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Align the image horizontally.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_horizontal_alignment']['center'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_horizontal_alignment'],
			),
			'cb_parallax_vertical_alignment'          => array(
				'option_key'     => 'cb_parallax_vertical_alignment',
				'name'           => __( 'Vertical Alignment', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Align the image vertically.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_vertical_alignment']['center'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_vertical_alignment']
			),
			'cb_parallax_overlay_image'               => array(
				'option_key'     => 'cb_parallax_overlay_image',
				'name'           => __( 'Overlay Image', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Select an overlay image if you like.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_overlay_image']['none'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_overlay_image']
			),
			'cb_parallax_overlay_opacity'             => array(
				'option_key'     => 'cb_parallax_overlay_opacity',
				'name'           => __( 'Overlay Opacity', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Set overlay opacity.', $this->plugin_domain ),
				'default_value'  => $this->image_options_whitelist['cb_parallax_overlay_opacity']['default'],
				'input_type'     => 'select',
				'notice_level'   => 'none',
				'select_values'  => $this->image_options_whitelist['cb_parallax_overlay_opacity']
			),
			'cb_parallax_overlay_color'               => array(
				'option_key'     => 'cb_parallax_overlay_color',
				'name'           => __( 'Overlay Color', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Select overlay color.', $this->plugin_domain ),
				'default_value'  => '',
				'input_type'     => 'color',
				'notice_level'   => 'none',
				'select_values'  => 'none'
			),
		);

		return $options;
	}

	/**
	 * Returns the plugin image_options and their meta data.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return array $plugin_options
	 */
	private function plugin_options() {

		$plugin_options = array(

			'cb_parallax_parallax_enabled'   => array(
				'option_key'     => 'cb_parallax_parallax_enabled',
				'name'           => __( 'Parallax Enabled', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Enable parallax', $this->plugin_domain ),
				'default_value'  => false,
				'input_type'     => 'checkbox',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			),
			'cb_parallax_global'             => array(
				'option_key'     => 'cb_parallax_global',
				'name'           => __( 'Global', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Enable this feature to apply this image and it\'s settings to all supported post types.', $this->plugin_domain ),
				'default_value'  => false,
				'input_type'     => 'checkbox',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			),
			'cb_parallax_allow_override'     => array(
				'option_key'     => 'cb_parallax_allow_override',
				'name'           => __( 'Allow Override', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Individual settings on a per post -/ per page basis will be used instead of this image and it\'s settings. On pages where no background image is defined, the above image will be displayed, if one is defined.', $this->plugin_domain ),
				'default_value'  => true,
				'input_type'     => 'checkbox',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			),
			'cb_parallax_preserve_scrolling' => array(
				'option_key'     => 'cb_parallax_preserve_scrolling',
				'name'           => __( 'Preserve Scrolling', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'With this option activated, the scroll behaviour will be the same on all pages, even if there is no parallax background image defined.', $this->plugin_domain ),
				'default_value'  => true,
				'input_type'     => 'checkbox',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			),
			'cb_parallax_disable_on_mobile'  => array(
				'option_key'     => 'cb_parallax_disable_on_mobile',
				'name'           => __( 'Disable On Mobile Devices', $this->plugin_domain ),
				'callback'       => 'render_settings_field_callback',
				'settings_group' => 'background-image',
				'description'    => __( 'Disable parallax-effect on mobile devices. This may be useful <i>if</i> you encounter performance issues.', $this->plugin_domain ),
				'default_value'  => false,
				'input_type'     => 'checkbox',
				'notice_level'   => 'none',
				'select_values'  => 'none',
			)
		);

		return $plugin_options;
	}

	/**
	 * Maintains the allowed image image_options.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @return   array $image_options_whitelist
	 */
	private function load_image_options_whitelist() {

		// Image image_options for a static background image.
		$image_options_whitelist['cb_parallax_position_x'] = array(
			'left'   => __( 'left', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'right'  => __( 'right', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_position_y'] = array(
			'top'    => __( 'top', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'bottom' => __( 'bottom', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_background_attachment'] = array(
			'fixed'  => __( 'fixed', $this->plugin_domain ),
			'scroll' => __( 'scroll', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_background_repeat'] = array(
			'no-repeat' => __( 'no-repeat', $this->plugin_domain ),
			'repeat'    => __( 'repeat', $this->plugin_domain ),
			'repeat-x'  => __( 'horizontal', $this->plugin_domain ),
			'repeat-y'  => __( 'vertical', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_direction'] = array(
			'vertical'   => __( 'vertical', $this->plugin_domain ),
			'horizontal' => __( 'horizontal', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_vertical_scroll_direction'] = array(
			'top'    => __( 'to top', $this->plugin_domain ),
			'bottom' => __( 'to bottom', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_horizontal_scroll_direction'] = array(
			'left'  => __( 'to the left', $this->plugin_domain ),
			'right' => __( 'to the right', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_horizontal_alignment'] = array(
			'left'   => __( 'left', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'right'  => __( 'right', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_vertical_alignment'] = array(
			'top'    => __( 'top', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'bottom' => __( 'bottom', $this->plugin_domain ),
		);

		$image_options_whitelist['cb_parallax_overlay_image'] = array_merge( array( 'none' => __( 'none', $this->plugin_domain ) ), $this->convert_overlay_image_names( $this->retrieve_overlay_image_names() ) );

		$image_options_whitelist['cb_parallax_overlay_opacity'] = array(
			'default' => __( 'default', $this->plugin_domain ),
			'0.1'     => '0.1',
			'0.2'     => '0.2',
			'0.3'     => '0.3',
			'0.4'     => '0.4',
			'0.5'     => '0.5',
			'0.6'     => '0.6',
			'0.7'     => '0.7',
			'0.8'     => '0.8',
			'0.9'     => '0.9',
		);

		$image_options_whitelist['cb_parallax_overlay_color'] = '';

		return $image_options_whitelist;
	}

	/**
	 * Maintains the allowed plugin image_options.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return array $plugin_options
	 */
	private function load_plugin_options_whitelist() {

		// Image image_options for a static background image.
		$plugin_options['cb_parallax_parallax_enabled'] = array(
			'off' => false,
			'on'  => true,
		);

		$plugin_options['cb_parallax_global'] = array(
			'off' => false,
			'on'  => true,
		);

		$plugin_options['cb_parallax_allow_override'] = array(
			'off' => false,
			'on'  => true,
		);

		$plugin_options['cb_parallax_preserve_scrolling'] = array(
			'off' => false,
			'on'  => true,
		);

		$plugin_options['cb_parallax_disable_on_mobile'] = array(
			'off' => false,
			'on'  => true,
		);

		return $plugin_options;
	}

	/**
	 * Assigns the required parameters to its instance and loads the allowed option values.
	 *
	 * @param array $keys
	 */
	public function __construct( $plugin_name, $plugin_domain ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_domain = $plugin_domain;

		//$this->add_dummy_data();
		$this->image_options_whitelist  = $this->load_image_options_whitelist();
		$this->plugin_options_whitelist = $this->load_plugin_options_whitelist();
	}

	/**
	 * The reference to the image_options class.
	 *
	 * @since  0.6.0
	 * @access public
	 * @return array $image_options_whitelist
	 */
	public function get_image_options_whitelist() {

		return $this->image_options_whitelist;
	}

	/**
	 * Retrieves the image_options.
	 *
	 * @since  0.6.0
	 * @access public
	 * @return array $plugin_options_whitelist
	 */
	public function get_plugin_options_whitelist() {

		return $this->plugin_options_whitelist;
	}

	/**
	 * Processes the plugin image_options array and returns the default option values.
	 *
	 * @since  0.6.0
	 * @access private
	 *
	 * @return array  $default_options
	 */
	public function get_default_image_options() {

		$default_options = array();

		$options = $this->image_options();

		foreach ( $options as $option_key => $args ) {

			$default_options[ $option_key ] = $args['default_value'];
		}

		return $default_options;
	}

	/**
	 * Processes the plugin image_options array and returns the default plugin-option values.
	 *
	 * @since  0.6.0
	 * @access private
	 *
	 * @return array  $default_plugin_options
	 */
	public function get_default_plugin_options() {

		$default_plugin_options = array();

		$options = $this->plugin_options();

		foreach ( $options as $option_key => $args ) {

			$default_plugin_options[ $option_key ] = $args['default_value'];
		}

		return $default_plugin_options;
	}

	/**
	 * Retrieves the option keys.
	 *
	 * @since  0.6.0
	 *
	 * @return array  $keys
	 */
	public function get_all_option_keys() {

		$option_keys = array_merge( $this->get_default_image_options(), $this->get_default_plugin_options() );
		$keys = array();

		foreach($option_keys as $name => $option_key ) {
			$keys[] = $name;
		}

		return $keys;
	}

	/**
	 * Extracts the necessary meta data from the requested image_options array.
	 *
	 * @param  string $settings_pack
	 *
	 * @return array $args
	 */
	public function get_all_options_arguments() {

		$options = array_merge( $this->image_options(), $this->plugin_options() );

		$args = array();

		foreach ( $options as $option_key => $arguments ) {

			$args[ $option_key ] = array(
				'option_key'     => $arguments['option_key'],
				'name'           => $arguments['name'],
				'settings_group' => $arguments['settings_group'],
				'description'    => $arguments['description'],
				'input_type'     => $arguments['input_type'],
				'select_values'  => $arguments['select_values'],
			);
		}

		return $args;
	}

	/**
	 * Returns the meta data necessary for rendering the requested settings section heading.
	 *
	 * @since  0.6.0
	 *
	 * @param  string $section
	 *
	 * @return mixed
	 */
	public function get_section_heading( $section ) {

		$background_image_heading = array(
			'option_key'     => 'cb_parallax_image_options',
			'name'           => __( 'Background Image', $this->plugin_domain ),
			'settings_group' => 'background-image',
			'description'    => __( 'Customize the background image image_options.', $this->plugin_domain ),
			'callback'       => 'background_image_settings_section_callback',
			'class'          => 'icon icon-equalizer',
		);

		$plugin_heading = array(
			'option_key'     => 'cb_parallax_parallax_options',
			'name'           => __( 'General Settings', $this->plugin_domain ),
			'settings_group' => 'plugin',
			'description'    => __( 'General settings.', $this->plugin_domain ),
			'callback'       => 'plugin_settings_section_callback',
			'class'          => 'icon icon-equalizer',
		);

		switch ( $section ) {

			case( 'background-image' == $section );
				$heading = $background_image_heading;
				break;

			case( 'plugin' == $section );
				$heading = $plugin_heading;
				break;
			default:
				return false;
		}

		return $heading;
	}

	/**
	 * Retrieves the options either global or those that are on a per-post-basis.
	 *
	 * @since    0.2.5
	 * @access   public
	 *
	 * @param object $post
	 *
	 * @return mixed
	 */
	public function fetch_options() {

		global $post;
		$options = null;

		// Here we need to check if $post is an object. If not, we bail.
		if ( ( ! is_object( $post ) || null === $post ) && false == get_option( 'page_for_posts' ) ) {
			return;
		}

		// Determines if we get the image data from the post meta or from the image_options array.
		$source_type = $this->determine_image_source();

		if ( $source_type == 'per_post' ) {

			$page_for_posts = get_option( 'page_for_posts' );

			if ( ( get_post_type( $post ) == 'product' || 'portfolio' || 'post' || 'page' ) && false == ( ! is_front_page() && is_home() ) ) {

				$options =  get_post_meta( $post->ID, 'cb_parallax', true );
			} else if ( false != $page_for_posts ) {

				$options = get_post_meta( $page_for_posts, 'cb_parallax', true );
			} else {

				$options = get_post_meta( $post->ID, 'cb_parallax', true );
			}

			if( is_array( $options ) ) {

				$options = array_merge( $this->get_default_image_options(), $options );
			}

			return $options;

		} else {

			return get_option( 'cb_parallax_options' );
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


	private function retrieve_overlay_image_names() {

		$path = plugin_dir_path(__FILE__) . '../../../public/images/overlays/';
		$excluded = array( '.', '..', '.DS_Store' );
		$names_list = null;

		if ( $handle = opendir( $path ) ) {

			while ( false !== ( $entry = readdir( $handle ) ) ) {

				if ( ! in_array( $entry, $excluded ) ) {

					$names_list[] = strip_tags( $entry );
				}
			}

			closedir( $handle );

			return $names_list;
		} else {

			return false;
		}

	}


	private function convert_overlay_image_names( $input ) {

		$output = array();

		foreach ( $input as $option_key => $value ) {

			// Remove the file extension
			$name = preg_replace( array( '/.png/', '/-/' ), array( '', ' ' ), $value );

			if ( preg_match( '/\s/', $name ) ) {
				// Remove whitespace and capitalize.
				$name = implode( ' ', array_map( 'ucfirst', explode( ' ', $name ) ) );
				$output[] = $name;
			} else {

				$output[] = ucfirst($name);
			}

		}

		return $output;
	}

}
