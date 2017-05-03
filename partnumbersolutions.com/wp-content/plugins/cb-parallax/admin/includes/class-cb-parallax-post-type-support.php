<?php

/**
 * Enables the custom background support for the given post types.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_paraallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_post_type_support {

	/**
	 * The available post types.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $post_types
	 */
	private $post_types = array( 'post', 'page', 'product', 'portfolio', 'books', 'movies', 'projects', 'work' );

	/**
	 * The feature we want the post types to work with.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $feature
	 */
	private $feature = 'custom-background';

	/**
	 * The array holding a set of default values for the custom background feature.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $defaults
	 */
	private $defaults;

	private function set_defaults() {

		$this->defaults = array(
			'default-color'          => '',
			'default-image'          => '',
			'wp-head-callback'       => '_custom_background_cb',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
		);
	}

	/**
	 * @since    0.1.0
	 * @access   public
	 * @return   mixed | void
	 */
	public function __construct() {

		$this->set_defaults();
	}

	/**
	 * Add post type support for the given post types with the defined feature.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function add_post_type_support() {

		foreach ( $this->post_types as $post_type ) {

			add_post_type_support( $post_type, $this->feature );
		}
	}

}
