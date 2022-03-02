<div class="ofrusers-settings-wrap">
    <h2>
        <?php echo esc_html__( 'Only For Registered Users', 'only-registered-users' ); ?>
    </h2>
    <div class="ofrusers-settings-divider">

    </div>

    <form method="post" action="" class="ofrusers-settings-form">

        <?php wp_nonce_field( 'only-registered-users' )?>

        <table class="ofrusers form-table">
            <tr valign="top">
                <th scope="row">
                    <?php echo esc_html__( 'Default Membership Settings', 'only-registered-users' ); ?>
                </th>
                <td>
                    <label for="users_can_register">
                        <input name="users_can_register" type="checkbox" id="users_can_register" value="1"<?php checked( '1', get_option( 'users_can_register' ) );?> />
                        <?php echo esc_html__( 'Anyone can register', 'only-registered-users' ) ?>
                    </label>
                    <br />
                    <div class="ofrusers-settings-label-desc">
                        <?php echo esc_html__( 'This is a default WordPress option placed here for easy changing.', 'only-registered-users' ); ?>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php echo esc_html__( 'Allow Guest Access', 'only-registered-users' ); ?>
                </th>
                <td>
                    <label for="ofrusers_feeds">
                        <input name="ofrusers_feeds" type="checkbox" id="ofrusers_feeds" value="1"<?php checked( '1', !empty( $settings['ofrusers_feeds'] ) );?> />
                        <?php echo esc_html__( 'Allow access to your post and comment rss feeds (Warning: this will reveal all post contents to guests!)', 'only-registered-users' ); ?>
                    </label>
                    <br />
                    <label for="ofrusers_rest">
                        <input name="ofrusers_rest" type="checkbox" id="ofrusers_rest" value="1"<?php checked( '1', !empty( $settings['ofrusers_rest'] ) );?> />
                        <?php echo esc_html__( 'Allow access to your REST API\'s (Warning: this will reveal all post contents to guests!)', 'only-registered-users' ); ?>
                    </label>
                    <br/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php echo esc_html__( 'Login Form Message', 'only-registered-users' ); ?>
                </th>
                <td>
                    <label for="ofrusers_msg">
                        <textarea name="ofrusers_msg" id="ofrusers_msg" rows="5" col='5'><?php echo esc_html( $user_msg ); ?></textarea>
                    </label>
                    <br>
                    <div class="ofrusers-settings-label-desc">
                        <?php echo esc_html__( 'This message will be shown on the login form once user is redirected to login window', 'only-registered-users' ); ?>
                    </div>
                </td>
            </tr>
        </table>

        <p class="ofrusers submit">
            <?php submit_button();?>
            <input type="hidden" name="ofrusers_action" value="update" />
        </p>
    </form>
</div>