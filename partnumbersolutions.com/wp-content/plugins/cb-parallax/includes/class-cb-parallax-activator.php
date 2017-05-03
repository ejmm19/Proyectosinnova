<?php

/**
 * Fired during plugin activation.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_activator {

	/**
	 * The variable that holds the name of the capability which is necessary
	 * to interact with this plugin.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      string $capability
	 */
	private $capability;

	public function __construct() {

		$this->capability = 'cb_parallax_edit';
	}

	/**
	 * Fired during activation of the plugin.
	 * Adds the capability to edit custom backgrounds to the administrator role.
	 *
	 * @since    0.1.0
	 * @access   static
	 * @return   void
	 */
	public function activate() {

		// Gets the administrator role.
		$role = get_role( 'administrator' );

		// If the acting user has admin rights, the capability gets added.
		if ( ! empty( $role ) ) {
			$role->add_cap( $this->capability );
		}
	}
}
