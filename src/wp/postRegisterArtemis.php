<?php
/*
Template Name: postRegisterArtemis
*/
/*
	$args = array(
	    'role'          =>  'member',
	    'meta_key'      =>  'account_status',
	    'meta_value'    =>  'approved'
	);

	foreach ($users as $user) {
	   echo '<pre>';
	   print_r( $user );
	   echo '</pre>';
*/
	$users = get_users( array( 'fields' => array( 'ID' ) ) );
	foreach($users as $user){
		echo '<pre>';
        print_r(get_user_meta ( $user->ID));
  		echo '</pre>';      
    }    
?>