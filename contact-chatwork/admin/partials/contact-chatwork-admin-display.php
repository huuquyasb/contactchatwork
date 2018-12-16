<?php
// create custom plugin settings menu
add_action('admin_menu', 'contact_chatwork_plugin_create_menu');

function contact_chatwork_plugin_create_menu() {

	//create new top-level menu
	add_menu_page('Contact Chatwork', 'Contact Chatwork', 'administrator', __FILE__, 'my_cool_plugin_settings_page' ,  'dashicons-awards', 90 );

	//call register settings function
	add_action( 'admin_init', 'register_my_cool_plugin_settings' );
}

function register_my_cool_plugin_settings() {
	//register our settings
	register_setting( 'contact-chatwork-plugin-settings-group', 'sw_api_token' );
	register_setting( 'contact-chatwork-plugin-settings-group', 'sw_room_id' );
	register_setting( 'contact-chatwork-plugin-settings-group', 'option_etc' );
}

function my_cool_plugin_settings_page() {
?>
<div class="wrap">
<h1>Setting contact chatwork</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'contact-chatwork-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'contact-chatwork-plugin-settings-group' ); ?>
    <table class="form-table  form-field form-required" >
        <tr valign="top">
        <th scope="row"><a target="_blank" href="https://www.chatwork.com/service/packages/chatwork/subpackages/api/token.php">Api token</a></th>
        <td>
        <input type="text" maxlength="100" name="sw_api_token" value="<?php echo esc_attr( get_option('sw_api_token') ); ?>" />
        
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">RoomId</th>
        <td>
        <input type="text" maxlength="60" name="sw_room_id" value="<?php echo esc_attr( get_option('sw_room_id') ); ?>" /></td>
        </tr>
        <a target="_blank" href="http://download.chatwork.com/ChatWork_API_Documentation.pdf">More Api</a>
    </table>
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>