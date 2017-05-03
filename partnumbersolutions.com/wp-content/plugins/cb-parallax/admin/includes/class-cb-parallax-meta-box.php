<?php

/**
 * Defines and displays the meta box.
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
class cb_parallax_meta_box {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The domain of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_version
	 */
	private $plugin_version;

	/**
	 * Whether the theme has a custom backround callback for 'wp_head' output.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    bool
	 */
	public $theme_has_callback = false;

	/**
	 * The reference to the image_options class.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var    object $image_options
	 */
	private $options;

	/**
	 * Maintains the allowed option values for the image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array $allowed_image_options
	 */
	public $allowed_image_options;

	/**
	 * Maintains the default image image_options.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array $default_image_options
	 */
	public $default_image_options;

	/**
	 * The array holding the names of the supported post types.
	 *
	 * @since    0.7.4
	 * @access   private
	 * @var      array $supported_post_types
	 */
	private $supported_post_types;

	/**
	 * Kicks off the meta box.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 * @param    array $upported_post_types
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version, $upported_post_types ) {

		$this->plugin_name          = $plugin_name;
		$this->plugin_domain        = $plugin_domain;
		$this->plugin_version       = $plugin_version;
		$this->supported_post_types = $upported_post_types;

		/* If the current user can't edit custom backgrounds, bail early. */
		if ( ! current_user_can( 'cb_parallax_edit' ) && ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$this->add_hooks();
		$this->retrieve_options();
		$this->load_dependencies();
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . "menu/includes/class-cb-parallax-options.php";
	}

	/**
	 * Retrieves the image_options.
	 *
	 * @since  0.6.0
	 * @access private
	 * @return void
	 */
	private function retrieve_options() {

		$this->options = new cb_parallax_options( $this->get_plugin_name(), $this->get_plugin_domain() );

		$this->allowed_image_options = $this->options->get_image_options_whitelist();

		$this->default_image_options = $this->options->get_default_image_options();
	}

	/**
	 * Register all necessary hooks for this part of the plugin to work with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	private function add_hooks() {

		/* Only load on the edit post screen. */
		add_action( 'load-post.php', array( $this, 'load_post' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post' ) );

		// Save meta data.
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Add actions for the edit post screen.
	 *
	 * @hooked_action
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function load_post() {

		$screen = get_current_screen();

		/* If the current theme doesn't support custom backgrounds, bail. */
		if ( ! current_theme_supports( 'custom-background' ) || ! post_type_supports( $screen->post_type, 'custom-background' ) ) {
			return;
		}

		/* Get the 'wp_head' callback. */
		$wp_head_callback = get_theme_support( 'custom-background', 'wp-head-callback' );

		/* Checks if the theme has set up a custom callback. */
		$this->theme_has_callback = empty( $wp_head_callback ) || '_custom_background_cb' === $wp_head_callback ? false : true;

		// Add the meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 5 );

		// Save meta data.
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  string $post_type
	 *
	 * @return void
	 */
	public function add_meta_box( $post_type ) {

		$screen = get_current_screen();

		if ( in_array( $screen->id, $this->supported_post_types ) ) {

			add_meta_box( $this->plugin_name . '-meta-box', __( 'cb Parallax', $this->plugin_domain ), array(
				&$this,
				'display_meta_box',
			), $post_type, 'side', 'core' );
		}
	}

	/**
	 * Displays the meta box.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  object $post
	 *
	 * @return void
	 */
	public function display_meta_box( $post ) {

		$image = null;
		$url = null;

		if( is_array( get_post_meta( $post->ID, 'cb_parallax', true ) ) ) {

			$post_meta = array_merge( $this->options->get_default_image_options(), get_post_meta( $post->ID, 'cb_parallax', true ) );
		} else {
			$post_meta = $this->options->get_default_image_options();
		}

		// Get the background image attachment ID.
		$attachment_id = isset( $post_meta['cb_parallax_attachment_id'] ) ? $post_meta['cb_parallax_attachment_id'] : false;

		// If an attachment ID was found, get the image source.
		if ( false !== $attachment_id ) {

			$image = wp_get_attachment_image_src( absint( $attachment_id ), 'full' );
			$url = isset( $image[0] ) ? $image[0] : '';
		}
		?>

		<!-- hidden fields. -->
		<?php wp_nonce_field( 'cb_parallax_nonce_field', 'cb_parallax_nonce' ); ?>
		<input type="hidden" name="cb_parallax_attachment_id" id="cb_parallax_attachment_id" value="<?php echo ! empty( $attachment_id ) ? esc_attr( $attachment_id ) : '' ?>" />
		<input type="hidden" name="cb_parallax_background_image_url_hidden" id="cb_parallax_background_image_url_hidden" value="<?php echo ! empty( $attachment_id ) ? esc_url( $url ) : '' ?>" />
		<!-- # hidden fields. -->

		<!-- background color. -->
		<!--<div>
			<input type="text" name="cb_parallax_background_color" id="cb_parallax_background_color" class="wp-color-picker cb-parallax-color-picker" value="<?php /*echo isset($post_meta['cb_parallax_background_color']) ? $post_meta['cb_parallax_background_color'] : ''; */?>" />
		</div>-->
		<!-- # background color. -->

		<div class="image-bg">

		<!-- media button. -->
		<div class="cb-parallax-remove-media-button-container">
			<a class="cb-parallax-remove-media" href="#"><i class="fa fa-times" aria-hidden="true"></i></a>
		</div>
		<!-- # media button. -->

		<!-- background image. -->
		<!--<div>-->
		<a class="cb-parallax-image-container" href="#">
			<a href="#" class="cb-parallax-media-url"><img id="cb_parallax_background_image_url" src="<?php echo esc_url( $url ); ?>" style="max-width: 254px; max-height: 159px; display: block;" /></a>
		</a>
		<!--</div>-->
		<!-- # background image. -->

		<!-- parallax checkbox -->
		<div class="cb-parallax-single-option-container cb-parallax-parallax-enabled-container" id="cb_parallax_parallax_enabled_container">
			<div>
				<label for="cb_parallax_parallax_enabled"
					   class="label-for-cb-parallax-switch"><?php echo __( 'Parallax', $this->plugin_domain ); ?></label>

				<label class="cb-parallax-switch">
					<input type="checkbox" id="cb_parallax_parallax_enabled" class="cb-parallax-switch-input cb_parallax_parallax_enabled"
						   name="cb_parallax_parallax_enabled" value="1"
						<?php checked( 1, isset( $post_meta['cb_parallax_parallax_enabled'] ) ? $post_meta['cb_parallax_parallax_enabled'] : 0, true ); ?>>
					<span class="cb-parallax-switch-label cb_parallax_parallax_enabled" data-on="On" data-off="Off"></span>
					<span class="cb-parallax-switch-handle"></span>
				</label>
			</div>
		</div>
		<!-- # parallax checkbox -->

		</div>

		<!-- parallax options -->
		<div class="cb-parallax-parallax-options-container">
			<div class="cb-parallax-single-option-container" id="cb_parallax_direction_container">
				<label for="cb_parallax_direction"><?php _e( 'Mode', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_direction" id="cb_parallax_direction"
						class="widefat cb_parallax_direction fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_direction'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_direction'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_vertical_scroll_direction_container">
				<label
					for="cb_parallax_vertical_scroll_direction"><?php _e( 'Vertical Scroll Direction', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_vertical_scroll_direction" id="cb_parallax_vertical_scroll_direction"
						class="widefat cb_parallax_vertical_scroll_direction fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_vertical_scroll_direction'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_vertical_scroll_direction'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_horizontal_scroll_direction_container">
				<label
					for="cb_parallax_horizontal_scroll_direction"><?php _e( 'Horizontal Scroll Direction', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_horizontal_scroll_direction" id="cb_parallax_horizontal_scroll_direction"
						class="widefat cb_parallax_horizontal_scroll_direction fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_horizontal_scroll_direction'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_horizontal_scroll_direction'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<!-- # parallax options. -->

		<!-- background image options -->
		<div class="cb-parallax-image-options-container">
			<div class="cb-parallax-single-option-container" id="cb_parallax_horizontal_alignment_container">
				<label for="cb_parallax_position_x"><?php _e( 'Horizontal Alignment', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_position_x" id="cb_parallax_position_x"
						class="widefat cb_parallax_position_x fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_position_x'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_position_x'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_vertical_alignment_container">
				<label for="cb_parallax_position_y"><?php _e( 'Vertical Alignment', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_position_y" id="cb_parallax_position_y"
						class="widefat cb_parallax_position_y fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_position_y'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_position_y'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_background_repeat_container">
				<label for="cb_parallax_background_repeat"><?php _e( 'Background Repeat', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_background_repeat" id="cb_parallax_background_repeat"
						class="widefat cb_parallax_background_repeat fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_background_repeat'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_background_repeat'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_background_attachment_container">
				<label for="cb_parallax_background_attachment"><?php _e( 'Background Attachment', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_background_attachment" id="cb_parallax_background_attachment"
						class="widefat cb_parallax_background_attachment fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_background_attachment'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_background_attachment'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<!-- # background image options. -->

		<!-- overlay options -->
		<div class="cb-parallax-overlay-options-container">
			<div class="cb-parallax-single-option-container" id="cb_parallax_overlay_image_container">
				<label for="cb_parallax_overlay_image"><?php _e( 'Overlay Pattern', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_overlay_image" id="cb_parallax_overlay_image"
						class="widefat cb_parallax_overlay_image fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_overlay_image'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_overlay_image'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_overlay_opacity_container">
				<label for="cb_parallax_overlay_opacity"><?php _e( 'Overlay Pattern Opacity', $this->plugin_domain ); ?></label>
				<select name="cb_parallax_overlay_opacity" id="cb_parallax_overlay_opacity"
						class="widefat cb_parallax_overlay_opacity fancy-select cb-parallax-fancy-select">
					<?php foreach ( $this->allowed_image_options['cb_parallax_overlay_opacity'] as $key => $value ) { ?>
						<option
							value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post_meta['cb_parallax_overlay_opacity'] ); ?> ><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="cb-parallax-single-option-container" id="cb_parallax_overlay_color_container">
				<input type="text" name="cb_parallax_overlay_color" id="cb_parallax_overlay_color" class="wp-color-picker cb-parallax-color-picker" value="<?php echo esc_attr( $post_meta['cb_parallax_overlay_color'] ); ?>" />
			</div>
		</div>
		<!-- # overlay options. -->
		<?php
	}

	/**
	 * Saves the data from the meta box.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return mixed
	 *
	 * @param  int    $post_id
	 * @param  object $post
	 */
	public function save_post( $post_id, $post ) {

		// Verify the nonce.
		if ( ! isset( $_POST['cb_parallax_nonce'] ) || ! wp_verify_nonce( $_POST['cb_parallax_nonce'], 'cb_parallax_nonce_field' ) ) {
			return;
		}
		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );
		// Check if the current user has permission to edit the post.
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}
		// Don't save if the post is only a revision.
		if ( 'revision' == $post->post_type ) {
			return;
		}

		$post_data = null;
		$options = null;
		$excluded_options = array(
			/*'cb_parallax_background_color',*/
			'cb_parallax_overlay_color',
			'cb_parallax_attachment_id',
			'cb_parallax_background_image_url'
		);
		$whitelist = array_merge( $this->options->get_image_options_whitelist(), $this->options->get_plugin_options_whitelist() );
		// Match the option keys against the POST data to retrieve the values
		foreach ( $this->options->get_all_option_keys() as $key ) {

			if ( isset( $_POST[ $key ] ) ) {

				$post_data[ $key ] = $_POST[ $key ];

				if ( ! in_array( $key, $excluded_options ) ) {

					if ( in_array( $post_data[ $key ], $whitelist[ $key ] ) ) {

						$options[ $key ] = $_POST[ $key ];
					}
				}
			}
		}

		// We retrieve these values "by hand" since there is no default value that could be used as a pattern to match against.
		//$colors['cb_parallax_background_color'] = isset( $_POST['cb_parallax_background_color'] ) ? $_POST['cb_parallax_background_color'] : null;
		$colors['cb_parallax_overlay_color']    = isset( $_POST['cb_parallax_overlay_color'] ) ? $_POST['cb_parallax_overlay_color'] : null;

		// Check the color values
		foreach ( $colors as $color_key => $color_value ) {

			if ( isset( $color_value ) && ! preg_match( '/^#[a-f0-9]{3,6}$/i', $color_value ) ) {

				$options[ $color_key ] = '';
			} else {
				$options[ $color_key ] = $color_value;
			}
		}
		// Add the attachment url.
		$options['cb_parallax_background_image_url'] = isset( $_POST['cb_parallax_background_image_url_hidden'] ) ? $_POST['cb_parallax_background_image_url_hidden'] : '';
		// Add the attachment id.
		$options['cb_parallax_attachment_id'] = isset( $_POST['cb_parallax_attachment_id'] ) ? $_POST['cb_parallax_attachment_id'] : '';

		// If an attachment is set...
		if ( $options['cb_parallax_attachment_id'] != '' ) {

			$is_custom_header = get_post_meta( $post->ID, '_wp_attachment_is_custom_background', true );

			// ...add the image to the pool of uploaded background images for this theme.
			if ( $is_custom_header !== get_stylesheet() ) {
				update_post_meta( $post_id, '_wp_attachment_is_custom_background', get_stylesheet() );
			}
		}

		// Save data.
		update_post_meta( $post_id, 'cb_parallax', $options );
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
