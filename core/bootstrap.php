<?php

class Ofrusers_Bootstrap {

    public $exclusions = [];

    // Class initialization
    function __construct() {
        //register all styles and scripts
        add_action( 'admin_enqueue_scripts', [$this, 'js_css_admin'] );

        // Register our hooks
        add_action( 'wp', [ $this, 'redirect_to_login_or_not' ] );
        
        add_action( 'rest_api_init', [ $this, 'redirect_to_login_or_not' ] );

        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        add_action( 'init', [ $this, 'login_form_custom_msg' ] );

        $post_arr = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

        if ( isset( $post_arr['ofrusers_action'] ) && 'update' == $post_arr['ofrusers_action'] ) {
            add_action( 'init', [ $this, 'handle_settings' ] );
        }

    }


    public function js_css_admin() {

        // get screen id
        $screen    = get_current_screen();
        $screen_id = $screen->id;

        $allowed_screen_ids = [
            'settings_page_only-registered-users',
        ];

        if( in_array($screen_id, $allowed_screen_ids) ){
            wp_enqueue_style( 'ofrusers-admin', \Ofrusers::assets_url() . 'css/admin.css', [], \Ofrusers::version(), 'all' );
        }
    }

    // Depending on conditions, check if we need to run an authentication check
    public function redirect_to_login_or_not() {

		// If the user is logged in, then abort
        if ( current_user_can( 'read' ) ) {
            return;
        }

        $settings = get_option( 'only-registered-users' );

		// Feeds
        if ( !empty( $settings['ofrusers_feeds'] ) && is_feed() ) {
            return;
        }

        // Rest
        $is_rest = defined( 'REST_REQUEST' );

        if ( !empty( $settings['ofrusers_rest'] ) && $is_rest ) {
            return;
        }

        // An array of pages that will be EXCLUDED from restriction
        $this->exclusions = [
            'wp-login.php',
            'wp-register.php',
            'wp-cron.php', // Just incase
            'wp-trackback.php',
            'wp-app.php',
            'xmlrpc.php',
        ];

		// If the current page name is in the exclusion array, then just abort
        if ( in_array( basename( $_SERVER['PHP_SELF'] ), apply_filters( 'only-registered-users_exclusions', $this->exclusions ) ) ) {
            return;
        }

        // passed all the checking, so redirect to the login form
        auth_redirect();
    }

    // Register the plugin's settings page
    public function admin_menu() {
        add_options_page(
            esc_html__( 'Only Registered Users', 'only-registered-users' ),
            esc_html__( 'Only Registered Users', 'only-registered-users' ),
            'manage_options',
            'only-registered-users',
            [ $this, 'options_page_view' ]
        );
    }

    // Show the settings page
    public function options_page_view() {
        $settings = get_option( 'only-registered-users' );

        $default_user_msg   = esc_html__( 'Only registered and logged in users are allowed to view the content you are trying to access. Please log in first.', 'only-registered-users' );
        $user_msg           = empty($settings['ofrusers_msg']) ? $default_user_msg: $settings['ofrusers_msg'];
        
        $settings_template  = Ofrusers::plugin_dir() . 'templates/settings.php';
        if( file_exists($settings_template) ){
            include $settings_template;
        }
	}

	// Just a pretty message for users
    public function login_form_custom_msg() {

		// Don't show the error message if anything else is going on (registration, etc.)
        if ( 'wp-login.php' != basename( $_SERVER['PHP_SELF'] ) || !empty( $_POST ) || ( !empty( $_GET ) && empty( $_GET['redirect_to'] ) ) ) {
            return;
        }

        $settings           = get_option( 'only-registered-users' );
        $default_user_msg   = esc_html__( 'Only registered and logged in users are allowed to view the content you are trying to access. Please log in first.', 'only-registered-users' );
        $user_msg           = empty($settings['ofrusers_msg']) ? $default_user_msg : $settings['ofrusers_msg'];

        global $error;
        $error = $user_msg;
    }

    // Update settings submitted from the settings page
    public function handle_settings() {

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'No direct access!!!' ) );
        }

        $post_arr = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

        check_admin_referer( 'only-registered-users' );

        $settings = [
            'ofrusers_feeds' => ( !empty( $post_arr['ofrusers_feeds'] ) ) ? 1 : 0,
            'ofrusers_rest'  => ( !empty( $post_arr['ofrusers_rest'] ) ) ? 1 : 0,
            'ofrusers_msg'   => ( !empty( $post_arr['ofrusers_msg'] ) ) ? $post_arr['ofrusers_msg'] : '',
        ];

        update_option( 'only-registered-users', $settings );

        update_option( 'users_can_register', ( !empty( $post_arr['users_can_register'] ) ) ? 1 : 0 );

        wp_redirect( add_query_arg( 'updated', 'true' ) );

        exit();
    }

}

new Ofrusers_Bootstrap();