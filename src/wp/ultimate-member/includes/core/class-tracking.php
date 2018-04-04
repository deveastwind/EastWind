<?php
namespace um\core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'um\core\Tracking' ) ) {


	/**
	 * Class Tracking
	 * @package um\core
	 */
	class Tracking {


		/**
		 * @var
		 */
		private $data;


		/**
		 * Tracking constructor.
		 */
		public function __construct() {

			$this->schedule_send();

			add_action( 'admin_notices', array( $this, 'admin_notices' ), 10 );

			add_action( 'um_admin_do_action__opt_into_tracking', array( $this, 'um_admin_do_action__opt_into_tracking' ) );
			add_action( 'um_admin_do_action__opt_out_of_tracking', array( $this, 'um_admin_do_action__opt_out_of_tracking' ) );
		}


		/**
		 * Opt-in tracking
		 *
		 * @param $action
		 */
		function um_admin_do_action__opt_into_tracking( $action ) {
			UM()->options()->update( 'um_allow_tracking', 1 );
			update_option( 'um_tracking_notice', 1 );

			$this->send_checkin(true);

			exit( wp_redirect( remove_query_arg('um_adm_action') ) );
		}


		/**
		 * Opt-out of tracking
		 *
		 * @param $action
		 */
		function um_admin_do_action__opt_out_of_tracking( $action ) {
			UM()->options()->update( 'um_allow_tracking', 0 );
			update_option('um_tracking_notice', 1 );

			exit( wp_redirect( remove_query_arg('um_adm_action') ) );
		}


		/**
		 * Setup info array
		 *
		 */
		private function setup_data() {
			$data = array();

			// Retrieve current theme info
			if ( get_bloginfo( 'version' ) < '3.4' ) {
				$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
				$theme      = $theme_data['Name'];
				$theme_ver  = $theme_data['Version'];
			} else {
				$theme_data = wp_get_theme();
				$theme      = $theme_data->Name;
				$theme_ver  = $theme_data->Version;
			}

			$data['url'] = home_url();
			$data['theme'] = $theme;
			$data['theme_version'] = $theme_ver;
			$data['wp_version'] = get_bloginfo( 'version' );
			$data['version'] = ultimatemember_version;

			// Retrieve current plugin information
			if( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$plugins        = array_keys( get_plugins() );
			$active_plugins = get_option( 'active_plugins', array() );

			foreach ( $plugins as $key => $plugin ) {
				if ( in_array( $plugin, $active_plugins ) ) {
					// Remove active plugins from list so we can show active and inactive separately
					unset( $plugins[ $key ] );
				}
			}

			$data['active_plugins']   = $active_plugins;
			$data['inactive_plugins'] = $plugins;
			$data['language'] = get_bloginfo('language');
			$data['multisite'] = ( is_multisite() ) ? 1 : 0;

			UM()->setup()->install_basics();

			$data['email'] = get_option( 'admin_email' );
			$data['unique_sitekey'] = get_option( '__ultimatemember_sitekey' );

			$this->data = $data;

		}


		/**
		 * Check if tracking is allowed
		 *
		 * @return int
		 */
		private function tracking_allowed() {
			if ( ! UM()->options()->get( 'allow_tracking' ) )
				return 0;
			return 1;
		}


		/**
		 * Get last send time
		 *
		 * @return mixed|void
		 */
		private function get_last_send() {
			return get_option( 'um_tracking_last_send' );
		}


		/**
		 * Send a report
		 *
		 * @param bool $override
		 */
		public function send_checkin( $override = false ) {

			if( ! $this->tracking_allowed() && ! $override )
				return;

			// Send a maximum of once per period
			$last_send = $this->get_last_send();
			if( $last_send && $last_send > strtotime( '-1 day' ) )
				return;

			$this->setup_data();

			if ( !get_option('__ultimatemember_coupon_sent') ) {
				$this->data['send_discount'] = 1;
			} else {
				$this->data['send_discount'] = 0;
			}

			$request = wp_remote_post( 'https://ultimatemember.com/?um_action=checkin', array(
				'method'      => 'POST',
				'timeout'     => 20,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'body'        => $this->data,
				'user-agent'  => 'UM/' . ultimatemember_version . '; ' . get_bloginfo( 'url' ),
			) );

			update_option( 'um_tracking_last_send', time() );
			update_option( '__ultimatemember_coupon_sent', 1 );
		}


		/**
		 * Run a scheduled report
		 */
		private function schedule_send() {
			add_action( 'um_daily_scheduled_events', array( $this, 'send_checkin' ) );
		}


		/**
		 * Show admin notices
		 */
		public function admin_notices() {

			if( ! current_user_can( 'manage_options' ) )
				return;

			$hide_notice = get_option('um_tracking_notice');

			if ( $hide_notice )
				return;

			$optin_url  =  esc_url( add_query_arg( 'um_adm_action', 'opt_into_tracking' ) );
			$optout_url =  esc_url( add_query_arg( 'um_adm_action', 'opt_out_of_tracking' ) );

			echo '<div class="updated um-admin-notice"><p>';

			printf( __( 'Thanks for installing <strong>%s</strong>! The core plugin is free but we also sell extensions which allow us to continue developing and supporting the plugin full time. If you subscribe to our mailing list (no spam) we will email you a 20%% discount code which you can use to purchase the <a href="%s" target="_blank">extensions bundle</a>.', 'ultimate-member' ), ultimatemember_plugin_name, 'https://ultimatemember.com/core-extensions-bundle/' );

			echo '</p>';

			echo '<p><a href="' . esc_url( $optin_url ) . '" class="button button-primary">' . __( 'Subscribe to mailing list', 'ultimate-member' ) . '</a>';
			echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="button-secondary">' . __( 'No thanks', 'ultimate-member' ) . '</a></p></div>';

		}

	}
}