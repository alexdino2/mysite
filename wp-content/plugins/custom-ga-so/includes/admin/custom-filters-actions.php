<?php
/**
* Add custom columns in user listing page
*/
function cga_custom_user_columns( $column ) {
	unset($column['posts']);
    $column['ga_grant'] = 'GA grant';
    $column['posts'] = 'Posts';
	return $column;
}
add_filter( 'manage_users_columns', 'cga_custom_user_columns' );

/**
* Add custom columns data in user listing page
*/
function cga_custom_user_columns_data( $val, $column_name, $user_id ) {
	global $wpdb;
    $user = get_userdata( $user_id );
    switch ($column_name) {
        case 'ga_grant' :
            $analyticAccess = get_user_meta($user_id, 'ga_userAccess', true);
			if(!empty($analyticAccess)){
				return 'Y';
			}else{
				return 'N';
			}
            break;
        default:
    }
    return $return;
}
add_filter( 'manage_users_custom_column', 'cga_custom_user_columns_data', 10, 3 );
