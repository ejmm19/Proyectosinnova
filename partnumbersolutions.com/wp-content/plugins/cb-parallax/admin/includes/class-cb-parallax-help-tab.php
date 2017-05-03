<?php

/**
 * The class responsible for creating and displaying the help tab on edit screens for posts, pages and products.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_help_tab {

	/**
	 * The name of the domain.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The array containing the title and the content of the help tab.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    array $tabs
	 */
	private $tabs;

	/**
	 * Sets the content of the help tab.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return void
	 */
	private function set_tab() {

		$this->tabs = array( __( 'HELP', $this->plugin_domain ) => array( 'title' => __( 'cbParallax help', $this->plugin_domain ) ) );
	}

	/**
	 * Sets the tab.
	 * Determines if we're on an edit screen for a post, page or a product,
	 * and if so, it hooks the action to load the help tab.
	 *
	 * @param  $plugin_domain
	 *
	 * @return mixed | void
	 */
	public function __construct( $plugin_domain ) {

		$this->plugin_domain = $plugin_domain;

		$this->set_tab();
		$this->initialize();
	}

	/**
	 * Initializes the help tab.
	 *
	 * @since  0.1.0
	 * @return mixed | void
	 */
	private function initialize() {

			add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'add_cb_parallax_help_tab' ), 15 );
	}

	/**
	 * Adds the contents of the help tab to the current screen.
	 *
	 * @hooked_action
	 *
	 * @since  0.1.0
	 * @return mixed | void
	 */
	public function add_cb_parallax_help_tab() {

		foreach ( $this->tabs as $id => $data ) {

			$title = __( $data['title'], $this->plugin_domain );

			get_current_screen()->add_help_tab( array(
				'id'      => $id,
				'title'   => __( $title, $this->plugin_domain ),
				'content' => $this->display_content_callback(),
			) );
		}
	}

	/**
	 * The callback function containing the content of the help tab.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return string  $html
	 */
	private function display_content_callback() {

		$html = '<p>' . __( "This plugin enables you to have a fullscreen background image with a parallax effect with any image that meets the minimum dimensional requirements ( 1920 x 1200px for vertical parallax, wider for horizontal parallax).", $this->plugin_domain ) . '</p>';

		$html .= '<p>' . __( "You can choose an overlay image and define it's settings.", $this->plugin_domain ) . '</p>';

		$html .= '<p>' . __( "The indicated directions are meant to be met while scrolling down the content.", $this->plugin_domain ) . '</p>';

		$html .= '<p>' . __( "Enjoy!", $this->plugin_domain ) . '</p>';

		return $html;
	}

}
