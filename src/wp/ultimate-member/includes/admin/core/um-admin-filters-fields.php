<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Modify field args just before it is saved into form
 *
 * @param $array
 *
 * @return mixed
 */
function um_admin_pre_save_field_to_form( $array ){
	unset( $array['conditions'] );
	if ( isset($array['conditional_field']) && !empty( $array['conditional_action'] ) && !empty( $array['conditional_operator'] ) ) {
		$array['conditional_value'] = ! empty( $array['conditional_value'] ) ? $array['conditional_value'] : '';
		$array['conditions'][] = array( $array['conditional_action'], $array['conditional_field'], $array['conditional_operator'], $array['conditional_value'] );
	}

	if ( isset($array['conditional_field1']) && !empty( $array['conditional_action1'] ) && !empty( $array['conditional_operator1'] ) ) {
		$array['conditional_value1'] = ! empty( $array['conditional_value1'] ) ? $array['conditional_value1'] : '';
		$array['conditions'][] = array( $array['conditional_action1'], $array['conditional_field1'], $array['conditional_operator1'], $array['conditional_value1'] );
	}

	if ( isset($array['conditional_field2']) && !empty( $array['conditional_action2'] ) && !empty( $array['conditional_operator2'] ) ) {
		$array['conditional_value2'] = ! empty( $array['conditional_value2'] ) ? $array['conditional_value2'] : '';
		$array['conditions'][] = array( $array['conditional_action2'], $array['conditional_field2'], $array['conditional_operator2'], $array['conditional_value2'] );
	}

	if ( isset($array['conditional_field3']) && !empty( $array['conditional_action3'] ) && !empty( $array['conditional_operator3'] ) ) {
		$array['conditional_value3'] = ! empty( $array['conditional_value3'] ) ? $array['conditional_value3'] : '';
		$array['conditions'][] = array( $array['conditional_action3'], $array['conditional_field3'], $array['conditional_operator3'], $array['conditional_value3'] );
	}

	if ( isset($array['conditional_field4']) && !empty( $array['conditional_action4'] ) && !empty( $array['conditional_operator4'] ) ) {
		$array['conditional_value4'] = ! empty( $array['conditional_value4'] ) ? $array['conditional_value4'] : '';
		$array['conditions'][] = array( $array['conditional_action4'], $array['conditional_field4'], $array['conditional_operator4'], $array['conditional_value4'] );
	}

	return $array;
}
add_filter('um_admin_pre_save_field_to_form', 'um_admin_pre_save_field_to_form', 1 );


/**
 * Some fields may require extra fields before saving
 *
 * @param $array
 *
 * @return mixed
 */
function um_admin_pre_save_fields_hook( $array ) {
	extract( $array );

	/**
	 * UM hook
	 *
	 * @type filter
	 * @title um_fields_without_metakey
	 * @description Field Types without meta key
	 * @input_vars
	 * [{"var":"$types","type":"array","desc":"Field Types"}]
	 * @change_log
	 * ["Since: 2.0"]
	 * @usage add_filter( 'um_fields_without_metakey', 'function_name', 10, 1 );
	 * @example
	 * <?php
	 * add_filter( 'um_fields_without_metakey', 'my_fields_without_metakey', 10, 1 );
	 * function my_fields_without_metakey( $types ) {
	 *     // your code here
	 *     return $types;
	 * }
	 * ?>
	 */
	$fields_without_metakey = apply_filters( 'um_fields_without_metakey', array(
		'block',
		'shortcode',
		'spacing',
		'divider',
		'group'
	) );

	$fields = UM()->query()->get_attr('custom_fields', $form_id);
	$count = 1;
	if ( isset( $fields ) && !empty( $fields) ) $count = count($fields)+1;

	// set unique meta key
	if ( in_array( $field_type, $fields_without_metakey ) && !isset($array['post']['_metakey']) ) {
		$array['post']['_metakey'] = "um_{$field_type}_{$form_id}_{$count}";
	}

	// set position
	if ( !isset( $array['post']['_position'] ) ) {
		$array['post']['_position'] = $count;
	}

	return $array;
}
add_filter('um_admin_pre_save_fields_hook', 'um_admin_pre_save_fields_hook', 1 );


/**
 * Apply a filter to handle errors for field updating in backend
 *
 * @param $errors
 * @param $array
 *
 * @return mixed
 */
function um_admin_field_update_error_handling( $errors, $array ){
	extract( $array );

	$field_attr = UM()->builtin()->get_core_field_attrs( $field_type );

		if ( isset( $field_attr['validate'] ) ) {

			$validate = $field_attr['validate'];
			foreach ( $validate as $post_input => $arr ) {

				$mode = $arr['mode'];

				switch ( $mode ) {

					case 'numeric':
						if ( !empty( $array['post'][$post_input] ) && !is_numeric( $array['post'][$post_input] ) ){
							$errors[$post_input] = $validate[$post_input]['error'];
						}
						break;

					case 'unique':
						if ( !isset( $array['post']['edit_mode'] ) ) {
							if ( UM()->builtin()->unique_field_err( $array['post'][$post_input] ) ) {
								$errors[$post_input] = UM()->builtin()->unique_field_err( $array['post'][$post_input] );
							}
						}
						break;

					case 'required':
						if (  $array['post'][$post_input] == '' )
							$errors[$post_input] = $validate[$post_input]['error'];
						break;

					case 'range-start':
						if ( UM()->builtin()->date_range_start_err( $array['post'][$post_input] ) && $array['post']['_range'] == 'date_range' )
							$errors[$post_input] = UM()->builtin()->date_range_start_err( $array['post'][$post_input] );
							break;

					case 'range-end':
						if ( UM()->builtin()->date_range_end_err( $array['post'][$post_input], $array['post']['_range_start'] ) && $array['post']['_range'] == 'date_range' )
							$errors[$post_input] = UM()->builtin()->date_range_end_err( $array['post'][$post_input], $array['post']['_range_start'] );
							break;

				}

			}

		}

		return $errors;

	}
add_filter('um_admin_field_update_error_handling', 'um_admin_field_update_error_handling', 1, 2 );


/**
 * Filter validation types on loop
 *
 * @param $break
 * @param $key
 * @param $form_id
 * @param $field_array
 *
 * @return bool
 */
function um_builtin_validation_types_continue_loop( $break, $key, $form_id, $field_array ) {

	// show unique username validation only for user_login field
	if ( isset( $field_array['metakey'] ) && $field_array['metakey'] == 'user_login' && $key !== 'unique_username' ) {
		return false;
	}

	return $break;
}
add_filter( 'um_builtin_validation_types_continue_loop', 'um_builtin_validation_types_continue_loop', 1, 4 );


/**
 * @param $hide
 *
 * @return bool
 */
function um_hide_metabox_restrict_content_shop( $hide ) {
	if ( function_exists( 'wc_get_page_id' ) && ! empty( $_GET['post'] ) &&
	     $_GET['post'] == wc_get_page_id( 'shop' ) ) {
		return true;
	}

	return $hide;
}
add_filter( 'um_restrict_content_hide_metabox', 'um_hide_metabox_restrict_content_shop', 10, 1 );