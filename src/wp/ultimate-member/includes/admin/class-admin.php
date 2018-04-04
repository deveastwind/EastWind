<?php
namespace um\admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'um\admin\Admin' ) ) {


	/**
	 * Class Admin
	 * @package um\admin
	 */
	class Admin {


		/**
		 * @var string
		 */
		var $templates_path;


		/**
		 * Admin constructor.
		 */
		function __construct() {
			$this->templates_path = um_path . 'includes/admin/templates/';

			add_action( 'admin_init', array( &$this, 'admin_init' ), 0 );
			add_action( 'admin_notices', array( $this, 'check_wrong_install_folder' ), 3 );
		}


		/**
		 * Check if current page load UM post type
		 *
		 *
		 * @return bool
		 */
		function is_plugin_post_type() {
			if ( isset( $_REQUEST['post_type'] ) ) {
				$post_type = $_REQUEST['post_type'];
				if ( in_array( $post_type, array( 'um_form','um_directory' ) ) ) {
					return true;
				}
			} elseif ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {
				$post_type = get_post_type();
				if ( in_array( $post_type, array( 'um_form', 'um_directory' ) ) ) {
					return true;
				}
			}

			return false;
		}


		/**
		 * Check if plugin is installed with correct folder
		 */
		function check_wrong_install_folder() {
			$invalid_folder = false;

			$slug_array = explode( '/', um_plugin );
			if ( $slug_array[0] != 'ultimate-member' )
				$invalid_folder = true;

			if ( $invalid_folder ) { ?>

				<div class="error">
					<p>
						<?php printf( __( 'You have installed <strong>%s</strong> with wrong folder name. Correct folder name is <strong>"ultimate-member"</strong>.', 'ultimate-member' ), ultimatemember_plugin_name ) ?>
					</p>
				</div>

			<?php }
		}


		/**
		 * Init admin action/filters + request handlers
		 */
		function admin_init() {
			require_once 'core/um-admin-actions-user.php';
			require_once 'core/um-admin-actions-modal.php';
			require_once 'core/um-admin-actions.php';

			require_once 'core/um-admin-filters-fields.php';

			if ( is_admin() && current_user_can( 'manage_options' ) && ! empty( $_REQUEST['um_adm_action'] ) ) {
				/**
				 * UM hook
				 *
				 * @type action
				 * @title um_admin_do_action__
				 * @description Make some action on custom admin action
				 * @input_vars
				 * [{"var":"$action","type":"string","desc":"Admin Action"}]
				 * @change_log
				 * ["Since: 2.0"]
				 * @usage add_action( 'um_admin_do_action__', 'function_name', 10, 1 );
				 * @example
				 * <?php
				 * add_action( 'um_admin_do_action__', 'my_admin_do_action', 10, 1 );
				 * function my_admin_do_action( $action ) {
				 *     // your code here
				 * }
				 * ?>
				 */
				do_action( "um_admin_do_action__", $_REQUEST['um_adm_action'] );
				/**
				 * UM hook
				 *
				 * @type action
				 * @title um_admin_do_action__{$action}
				 * @description Make some action on custom admin $action
				 * @input_vars
				 * [{"var":"$action","type":"string","desc":"Admin Action"}]
				 * @change_log
				 * ["Since: 2.0"]
				 * @usage add_action( 'um_admin_do_action__{$action}', 'function_name', 10, 1 );
				 * @example
				 * <?php
				 * add_action( 'um_admin_do_action__{$action}', 'my_admin_do_action', 10, 1 );
				 * function my_admin_do_action( $action ) {
				 *     // your code here
				 * }
				 * ?>
				 */
				do_action( "um_admin_do_action__{$_REQUEST['um_adm_action']}", $_REQUEST['um_adm_action'] );
			}
		}

	}
}