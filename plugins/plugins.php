<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
$current_url = '?page=5x5-tools&tab='. $tab;

echo '<a href="'. $current_url. '&check_plugin_updates=1' .'" class="button button-primary">Check Plugin Updates</a>';

if( isset($_GET['update_plugin']) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $plugin = $_GET['update_plugin'];
    $upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'plugin' ) ) );
    $upgrader->upgrade( $plugin );
}

if( isset($_GET['check_plugin_updates'] ) ) {
    $update_plugins = get_site_transient( 'update_plugins' );

    $have_updates = false;

    $plugins = get_plugins();
    foreach( $plugins as $plugin => $plugin_data ) {
        if( strpos( $plugin, 'ff-' ) === false ) continue;

        echo '<p>Checking '. $plugin .'</p>';

        $data = ff_check_plugin_update( $plugin, $plugin_data );
        if( $data['have_update'] ) {
            
            $update_plugins->response[$plugin] = $data['res'];
            $have_updates = true;

            echo '<p><a href="'. $current_url. '&update_plugin='. $plugin .'" class="button button-primary">Update '. $plugin .'</a></p>';

        }
    }

    if( $have_updates ) {
        set_site_transient( 'update_plugins', $update_plugins, 10000 );
    } else {

        echo '<p>No updates</p>';

    }
}

function ff_check_plugin_update( $plugin, $plugin_data ) {

    $data = [
        'have_update' => false,
        'res' => '',
    ];
    
    $remote = wp_remote_get( 'https://devlibrary2021.wpengine.com/wp-json/ff/v1/plugin-update/'. $plugin, array(
        'timeout' => 10,
        'headers' => array(
            'Accept' => 'application/json'
        ))
    );

    if( is_wp_error( $remote ) || !isset( $remote[ 'response' ][ 'code' ] ) ) {
        return $data;
    }
    
    if ( $remote ) {
        
        $remote = json_decode( $remote[ 'body' ] );

        if ( $remote && version_compare( $plugin_data['Version'], $remote->version, '<' ) ) {

            $data['have_update'] = true;

            $res = new stdClass();
            $res->slug = $remote->slug;
            $res->plugin = plugin_basename( __FILE__ );
            $res->new_version = $remote->version;
            $res->package = $remote->download_url;
            $data['res'] = $res;
        }
    }
    
    return $data;  
}