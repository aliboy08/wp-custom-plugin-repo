<?php
add_action( 'admin_menu', function(){
    add_menu_page(  __( '5x5 Tools', 'fivebyfive' ), '5x5 Tools', 'manage_options', '5x5-tools', 'admin_page_5x5_tools', 'dashicons-admin-generic', 100 ); 
} );

function admin_page_5x5_tools(){
    
    if( !in_array( 'administrator', wp_get_current_user()->roles ) ) {
        echo '<p>Access Denied</p>';
        return;
    }
    
    $tabs = [];

    $tabs[] = [
        'label' => 'Plugins',
        'slug' => 'plugins',
        'file' => 'plugins/plugins.php',
    ];

    $tabs = apply_filters( 'ff_tools_tabs', $tabs );
    
    $default_tab = $tabs[0]['slug'];
    $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
    ?>
    
    <div class="wrap">
    
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <nav class="nav-tab-wrapper">
            <?php
            foreach( $tabs as $tab_item ) {
                $active = ( $tab == $tab_item['slug'] ) ? ' nav-tab-active' : '';
                echo '<a href="?page=5x5-tools&tab='. $tab_item['slug'] .'" class="nav-tab '. $active .'">'. $tab_item['label'] .'</a>';
            }
            ?>
        </nav>

        <div class="tab-content">
        <?php 
        foreach( $tabs as $tab_item ) {
            if( $tab_item['slug'] == $tab ) {
                echo '<h2>'. $tab_item['label'] .'</h2>';
                include $tab_item['file'];
                break;
            }
        }
        ?>
        </div>
        
    </div>
    <?php
}