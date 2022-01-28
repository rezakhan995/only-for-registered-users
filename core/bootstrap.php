<?php

class Ofrusers_Bootstrap {

    public $exclusions = [];

    // Class initialization
    function __construct() {
        // Register our hooks
        add_action( 'wp', [ $this, 'redirect_to_login_or_not' ] );
        add_action( 'rest_api_init', [ $this, 'redirect_to_login_or_not' ] );

        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        add_action( 'init', [ $this, 'login_form_custom_msg' ] );

        if ( isset( $_POST['ofrusers_action'] ) && 'update' == $_POST['ofrusers_action'] ) {
            add_action( 'init', [ $this, 'handle_settings' ] );
        }

    }

    // Depending on conditions, check if we need to run an authentication check
    public function redirect_to_login_or_not() {

		// If the user is logged in, then abort
        if ( current_user_can( 'read' ) ) {
            return;
        }

        $settings = get_option( 'only-for-registered-users' );

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
        if ( in_array( basename( $_SERVER['PHP_SELF'] ), apply_filters( 'only-for-registered-users_exclusions', $this->exclusions ) ) ) {
            return;
        }

        // passed all the checking, so redirect to the login form
        auth_redirect();
    }

    // Register the plugin's settings page
    public function admin_menu() {
        add_options_page(
            esc_html__( 'Only Registered Users', 'only-for-registered-users' ),
            esc_html__( 'Only Registered Users', 'only-for-registered-users' ),
            'manage_options',
            'only-for-registered-users',
            [ $this, 'options_page_view' ]
        );
    }

    // Show the settings page
    public function options_page_view() {
        $settings = get_option( 'only-for-registered-users' );

        $default_user_msg   = esc_html__( 'Only registered and logged in users are allowed to view the content you are trying to access. Please log in first.', 'only-for-registered-users' );
        $user_msg           = empty($settings['ofrusers_msg']) ? $default_user_msg: $settings['ofrusers_msg'];
        ?>
		<div class="ofrusers wrap">
			<h2>
                <?php echo esc_html__( 'Only For Registered Users', 'only-for-registered-users' );?>
            </h2>

			<form method="post" action="">

				<?php wp_nonce_field( 'only-for-registered-users' )?>

				<table class="ofrusers form-table">
					<tr valign="top">
						<th scope="row">
                            <?php echo esc_html__( 'Default Membership Settings', 'only-for-registered-users' );?>
                        </th>
						<td>
							<label for="users_can_register">
								<input name="users_can_register" type="checkbox" id="users_can_register" value="1"<?php checked( '1', get_option( 'users_can_register' ) );?> />
								<?php echo esc_html__( 'Anyone can register', 'only-for-registered-users' )?>
							</label>
                            <br />
							<?php echo esc_html__( 'This is a default WordPress option placed here for easy changing.', 'only-for-registered-users' );?>
						</td>
					</tr>
					<tr valign="top">
                        <th scope="row">
                            <?php echo esc_html__( 'Allow Guest Access', 'only-for-registered-users' );?>
                        </th>
                        <td>
                            <label for="ofrusers_feeds">
                                <input name="ofrusers_feeds" type="checkbox" id="ofrusers_feeds" value="1"<?php checked( '1', !empty( $settings['ofrusers_feeds'] ) );?> />
                                <?php echo esc_html__( 'Allow access to your post and comment rss feeds (Warning: this will reveal all post contents to guests!)', 'only-for-registered-users' );?>
                            </label>
                            <br />
                            <label for="ofrusers_rest">
                                <input name="ofrusers_rest" type="checkbox" id="ofrusers_rest" value="1"<?php checked( '1', !empty( $settings['ofrusers_rest'] ) );?> />
                                <?php echo esc_html__( 'Allow access to your REST API\'s (Warning: this will reveal all post contents to guests!)', 'only-for-registered-users' );?>
                            </label>
                            <br/>
                        </td>
                    </tr>
					<tr valign="top">
						<th scope="row">
                            <?php echo esc_html__( 'Login Form Message', 'only-for-registered-users' );?>
                        </th>
						<td>
							<label for="ofrusers_msg">
								<textarea name="ofrusers_msg" id="ofrusers_msg" rows="5" col='5'><?php echo esc_html( $user_msg );?></textarea>
								<br>
                                <?php echo esc_html__( 'This message will be shown on the login form once user is redirected to login window', 'only-for-registered-users' );?>
							</label>
						</td>
					</tr>
				</table>

				<p class="ofrusers submit">
					<?php submit_button();?>
					<input type="hidden" name="ofrusers_action" value="update" />
				</p>
			</form>
		</div>
		<?php
	}

	// Just a pretty message for users
    public function login_form_custom_msg() {

		// Don't show the error message if anything else is going on (registration, etc.)
        if ( 'wp-login.php' != basename( $_SERVER['PHP_SELF'] ) || !empty( $_POST ) || ( !empty( $_GET ) && empty( $_GET['redirect_to'] ) ) ) {
            return;
        }

        $settings           = get_option( 'only-for-registered-users' );
        $default_user_msg   = esc_html__( 'Only registered and logged in users are allowed to view the content you are trying to access. Please log in first.', 'only-for-registered-users' );
        $user_msg           = empty($settings['ofrusers_msg']) ? $default_user_msg : $settings['ofrusers_msg'];

        global $error;
        $error = $user_msg;
    }

    // Update settings submitted from the settings page
    public function handle_settings() {

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'No direct access!!!' ) );
        }

        check_admin_referer( 'only-for-registered-users' );

        $settings = [
            'ofrusers_feeds' => ( !empty( $_POST['ofrusers_feeds'] ) ) ? 1 : 0,
            'ofrusers_rest'  => ( !empty( $_POST['ofrusers_rest'] ) ) ? 1 : 0,
            'ofrusers_msg'   => ( !empty( $_POST['ofrusers_msg'] ) ) ? $_POST['ofrusers_msg'] : '',
        ];

        update_option( 'only-for-registered-users', $settings );

        update_option( 'users_can_register', ( !empty( $_POST['users_can_register'] ) ) ? 1 : 0 );

        wp_redirect( add_query_arg( 'updated', 'true' ) );

        exit();
    }

}

new Ofrusers_Bootstrap();