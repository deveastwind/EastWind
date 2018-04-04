<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add option for WPML
 *
 * @param array $fields
 * @param array $data
 *
 * @return array
 */
function um_admin_wpml_post_options( $fields, $data ) {
	global $post;

	if ( ! function_exists( 'icl_get_current_language' ) )
		return $fields;

	if ( empty( $post->post_type ) || $post->post_type != 'page' )
		return $fields;

	$fields[] = array(
		'id'    => '_um_wpml_user',
		'type'  => 'checkbox',
		'label' => __( 'This is a translation of UM profile page?', 'ultimate-member' ),
		'value' => ! empty( $data['_um_wpml_user'] ) ? $data['_um_wpml_user'] : 0
	);

	$fields[] = array(
		'id'    => '_um_wpml_account',
		'type'  => 'checkbox',
		'label' => __( 'This is a translation of UM account page?', 'ultimate-member' ),
		'value' => ! empty( $data['_um_wpml_account'] ) ? $data['_um_wpml_account'] : 0
	);

	return $fields;
}
add_filter( 'um_admin_access_settings_fields', 'um_admin_wpml_post_options', 10, 2 );


/**
 * Clear all users cache
 *
 * @param $action
 */
function um_admin_do_action__user_cache( $action ) {
	global $wpdb;
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		die();
	}

	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'um_cache_userdata_%'" );

	$url = add_query_arg( array( 'page' => 'ultimatemember', 'update' => 'cleared_cache' ), admin_url( 'admin.php' ) );
	exit( wp_redirect( $url ) );
}
add_action( 'um_admin_do_action__user_cache', 'um_admin_do_action__user_cache' );


/**
 * Purge temp uploads dir
 * @param $action
 */
function um_admin_do_action__purge_temp( $action ){
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		die();
	}

	UM()->files()->remove_dir( UM()->files()->upload_temp );

	$url = add_query_arg( array( 'page' => 'ultimatemember', 'update' => 'purged_temp' ), admin_url( 'admin.php' ) );
	exit( wp_redirect( $url ) );
}
add_action( 'um_admin_do_action__purge_temp', 'um_admin_do_action__purge_temp' );


/**
 * Duplicate form
 *
 * @param $action
 */
function um_admin_do_action__duplicate_form( $action ) {
	if ( ! is_admin() || ! current_user_can('manage_options') ) die();
	if ( ! isset( $_REQUEST['post_id'] ) || ! is_numeric( $_REQUEST['post_id'] ) ) die();

	$post_id = $_REQUEST['post_id'];

	$n = array(
		'post_type' 	  	=> 'um_form',
		'post_title'		=> sprintf( __( 'Duplicate of %s', 'ultimate-member' ), get_the_title( $post_id ) ),
		'post_status'		=> 'publish',
		'post_author'   	=> get_current_user_id(),
	);

	$n_id = wp_insert_post( $n );

	$n_fields = get_post_custom( $post_id );
	foreach ( $n_fields as $key => $value ) {

		if ( $key == '_um_custom_fields' ) {
			$the_value = unserialize( $value[0] );
		} else {
			$the_value = $value[0];
		}

		update_post_meta( $n_id, $key, $the_value );

	}

	delete_post_meta($n_id, '_um_core');

	$url = admin_url('edit.php?post_type=um_form');
	$url = add_query_arg('update','form_duplicated',$url);

	exit( wp_redirect( $url ) );

}
add_action( 'um_admin_do_action__duplicate_form', 'um_admin_do_action__duplicate_form' );


/**
 * Download a language remotely
 *
 * @param $action
 */
function um_admin_do_action__um_language_downloader( $action ) {
	if ( !is_admin() || !current_user_can('manage_options') ) die();

	$locale = get_option('WPLANG');
	if ( !$locale ) return;
	if ( !isset( UM()->available_languages[$locale] ) ) return;

	$path = UM()->files()->upload_basedir;
	$path = str_replace('/uploads/ultimatemember','',$path);
	$path = $path . '/languages/plugins/';
	$path = str_replace('//','/',$path);

	$remote = 'https://ultimatemember.com/wp-content/languages/plugins/ultimatemember-' . $locale . '.po';
	$remote2 = 'https://ultimatemember.com/wp-content/languages/plugins/ultimatemember-' . $locale . '.mo';

	$remote_tmp = download_url( $remote, $timeout = 300 );
	copy( $remote_tmp, $path . 'ultimatemember-' . $locale . '.po' );
	unlink( $remote_tmp );

	$remote2_tmp = download_url( $remote2, $timeout = 300 );
	copy( $remote2_tmp, $path . 'ultimatemember-' . $locale . '.mo' );
	unlink( $remote2_tmp );

	$url = remove_query_arg('um_adm_action', UM()->permalinks()->get_current_url() );
	$url = add_query_arg('update','language_updated',$url);
	exit( wp_redirect($url) );

}
add_action( 'um_admin_do_action__um_language_downloader', 'um_admin_do_action__um_language_downloader' );


/**
 * Action to hide notices in admin
 *
 * @param $action
 */
function um_admin_do_action__hide_notice( $action ) {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		die();
	}

	update_option( $action, 1 );
	exit( wp_redirect( remove_query_arg( 'um_adm_action' ) ) );
}
add_action( 'um_admin_do_action__um_hide_locale_notice', 'um_admin_do_action__hide_notice' );
add_action( 'um_admin_do_action__um_can_register_notice', 'um_admin_do_action__hide_notice' );
add_action( 'um_admin_do_action__um_hide_exif_notice', 'um_admin_do_action__hide_notice' );


/**
 * Various user actions
 *
 * @param $action
 */
function um_admin_do_action__user_action( $action ) {
	if ( !is_admin() || !current_user_can( 'edit_users' ) ) die();
	if ( !isset( $_REQUEST['sub'] ) ) die();
	if ( !isset($_REQUEST['user_id']) ) die();

	um_fetch_user( $_REQUEST['user_id'] );

	$subaction = $_REQUEST['sub'];

	/**
	 * UM hook
	 *
	 * @type action
	 * @title um_admin_user_action_hook
	 * @description Action on bulk user subaction
	 * @input_vars
	 * [{"var":"$subaction","type":"string","desc":"Bulk Subaction"}]
	 * @change_log
	 * ["Since: 2.0"]
	 * @usage add_action( 'um_admin_user_action_hook', 'function_name', 10, 1 );
	 * @example
	 * <?php
	 * add_action( 'um_admin_user_action_hook', 'my_admin_user_action', 10, 1 );
	 * function my_admin_user_action( $subaction ) {
	 *     // your code here
	 * }
	 * ?>
	 */
	do_action( "um_admin_user_action_hook", $subaction );
	/**
	 * UM hook
	 *
	 * @type action
	 * @title um_admin_user_action_{$subaction}_hook
	 * @description Action on bulk user subaction
	 * @change_log
	 * ["Since: 2.0"]
	 * @usage add_action( 'um_admin_user_action_{$subaction}_hook', 'function_name', 10 );
	 * @example
	 * <?php
	 * add_action( 'um_admin_user_action_{$subaction}_hook', 'my_admin_user_action', 10 );
	 * function my_admin_user_action() {
	 *     // your code here
	 * }
	 * ?>
	 */
	do_action( "um_admin_user_action_{$subaction}_hook" );

	um_reset_user();

	wp_redirect( add_query_arg( 'update', 'user_updated', admin_url('?page=ultimatemember') ) );
	exit;

}
add_action( 'um_admin_do_action__user_action', 'um_admin_do_action__user_action' );


/**
 * Add any custom links to plugin page
 *
 * @param array $links
 *
 * @return array
 */
function ultimatemember_plugin_links( $links ) {
	$more_links[] = '<a href="http://docs.ultimatemember.com/">' . __( 'Docs', 'ultimate-member' ) . '</a>';
	$more_links[] = '<a href="'.admin_url().'admin.php?page=um_options">' . __( 'Settings', 'ultimate-member' ) . '</a>';

	$links = $more_links + $links;
	return $links;
}
$prefix = is_network_admin() ? 'network_admin_' : '';
add_filter( "{$prefix}plugin_action_links_" . um_plugin, 'ultimatemember_plugin_links' );