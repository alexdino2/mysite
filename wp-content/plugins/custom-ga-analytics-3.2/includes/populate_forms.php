<?php


//print_r($gaAccounts);
$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);


$user_id = $mem->get('userid');
$accts = $mem->get($user_id.'accts');
global $acct_names;
$acct_names= array_column($accts, 'name','id');
$selacct = $mem->get($user_id.'selacct');
$properties = $mem->get($user_id.'properties');
//$acct_ids= array_column($gaAccounts, 'id');
//print_r($acct_names);

add_filter( 'gform_pre_render_9', 'populate_accts');
add_filter( 'gform_pre_validation_9', 'populate_accts');
add_filter( 'gform_pre_submission_filter_9', 'populate_accts');
add_filter( 'gform_admin_pre_render_9', 'populate_accts');
add_filter( 'gform_chained_selects_input_choices_9_7_1', 'populate_accts', 10, 5 );
function populate_accts( $form ) {

    foreach ( $form['fields'] as &$field ) {

        if ( $field->id != 1 ) {
            continue;
        }

        // you can add additional parameters here to alter the posts that are retrieved
        // more info: http://codex.wordpress.org/Template_Tags/get_posts
        //$posts = $acct_names;

        $choices = array();

        global $acct_names;

        foreach ( $acct_names as $key => $value ) {
            //var_dump($acct_names);
            //echo 'can you see me';
            //$choices[] = array( 'text' => $post['name'], 'value' => $post['id'] );
            //$choices[] = array( 'text' => $post->name, 'value' => $post->id );
            $choices[] = array( 'text' => $value, 'value' => $key);
        }

        if ($field->id == 1){
            $field->choices = $choices;
        }

        

    }

    return $form;
}

// populates the "Property" drop down of our Chained Select
add_filter( 'gform_chained_selects_input_choices_9_7_2', 'populate_properties', 10, 5 );
function populate_properties( $input_choices, $form_id, $field, $input_id, $chain_value ) {
 
    //$api_key = '9du7r286jng2zjjaf8antxur'; // signup for your own Edmunds API key here: http://edmunds.mashery.com/member/register
    $choices = array();
 
    $selected_acct = $chain_value[ "{$field->id}.1" ];
    if( ! $selected_acct ) {
        return $input_choices;
    }
 
    //$gaPropereties = $gAnalytics->getListGoals($_GET['account']);
    $properties = $gAnalytics->analytics->management_webproperties->listManagementWebproperties($selected_acct);
    //$gaPropereties = $gAnalytics->getListGoals({$selected_make}, $startdate, $enddate);
    print_r($properties);
    //$response = wp_remote_get( "https://api.edmunds.com/api/vehicle/v2/{$selected_make}/models?view=basic&fmt=json&api_key={$api_key}" );
    //if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
        //return $input_choices;
    //}
 
    //$models = json_decode( wp_remote_retrieve_body( $response ) )->models;
    
    
    foreach( $properties as $property ) {
        $choices[] = array(
            'text'       => $property->name,
            'value'      => $property->niceName,
            'isSelected' => false
        );
    }
 
    return $choices;
}