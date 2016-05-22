# WordPress-Custom-Settings-Page-API
A simple API for building custom settings page for WordPress themes and plugins.


## Code Sample

```
add_action( 'admin_menu', 'register_general_settings_page', 1 );

function register_general_settings_page() {
    add_menu_page(
        __( 'Pendy WP' ),
        'Pendy WP',
        'manage_options',
        'hello-config',
        'general_settings_page_function'
    );
}

function general_settings_page_function() {
    $args = array(
        array(
            'section_title'    => __( 'MailChimp Addon', 'pp_mc' ),
            'mc_license_key'   => array(
                'type'        => 'text',
                'label'       => __( 'License Key', 'pp_mc' ),
                'description' => __( 'Enter Your Purchase License Key to Receive Update.', 'pp_mc' ),
            ),
            'mc_activate'      => array(
                'type'        => 'checkbox',
                'label'       => __( 'Activate Addon', 'pp_mc' ),
                'description' => __( 'Check to Activate MailChimp Integration.', 'pp_mc' ),
            ),
            'mc_activate_sync' => array(
                'type'           => 'checkbox',
                'checkbox_label' => __( 'Activate Sync', 'pp_mc' ),
                'label'          => __( 'Activate Email Sync', 'pp_mc' ),
                'description'    => __( 'Check to enable Syncing of WordPress users to MailChimp', 'pp_mc' ),
            ),
        ),
    );


    $instance = new W3Guy\Custom_Settings_Page_Api( $args, 'pendywp', 'Goat' );
    $instance->main_content( $args );
    $instance->option_name( 'pendywp' );
    $instance->sidebar( array(
        array(
            'section_title' => 'Documentation',
            'content'       => '',
        ),
        array(
            'section_title' => 'Help / Support',
            'content'       => '',
        ),
    ) );
    $instance->tab( array(
        array( 'url' => admin_url( 'admin.php?page=hello-config&g=h' ), 'label' => 'Settings' ),
        array( 'url' => admin_url( 'admin.php?page=hello-config' ), 'label' => 'World' ),
    ) );

    $instance->build();
}
```
