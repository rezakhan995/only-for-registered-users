<tr valign="top">
    <th scope="row">
        <?php echo esc_html__( 'Guest Access', 'only-for-registered-users' );?>
    </th>
    <td>
        <label for="ofrusers_feeds">
            <input name="ofrusers_feeds" type="checkbox" id="ofrusers_feeds" value="1"<?php checked( '1', !empty( $settings['ofrusers_feeds'] ) );?> />
            <?php echo esc_html__( 'Allow access to your post and comment feeds (Warning: this will reveal all post contents to guests!)', 'only-for-registered-users' );?>
        </label>
        <br />
        <label for="ofrusers_rest">
            <input name="ofrusers_rest" type="checkbox" id="ofrusers_rest" value="1"<?php checked( '1', !empty( $settings['ofrusers_rest'] ) );?> />
            <?php echo esc_html__( 'Allow access to your REST API\'s (Warning: this will reveal all post contents to guests!)', 'only-for-registered-users' );?>
        </label>
        <br/>
    </td>
</tr>