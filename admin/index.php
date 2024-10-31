<?php
/* Admin Screens */

defined( 'ABSPATH' ) || exit;
// require_once('pwa_settings.php');
/* Add Admin Menu */
function OrendaAdmin() {
	add_menu_page( 'OrendaWidgets', 'OrendaWidgets', 'manage_options', 'orenda-admin', 'addOrendaAdmin' );
}
add_action( 'admin_menu', 'OrendaAdmin' );

/* Plugin Admin HTML and Styling */
function addOrendaAdmin() {
	?>
	<div class="wrap orenda-admin-settings">
	<img class="orenda-admin-logo" src=<?php echo ORENDA_ELEMENTOR_PLUGIN_URL.'/assets/img/logo.png' ?> >
	<div class="orenda-admin-head" >
		<h1>No Code Embedded<br>Financial Solutions</h1>
		<div class="orenda-admin-intro">Create new revenue streams by building your own Fintech with our easy drag and drop no code, embedded financial products from one platform.</div>
	</div>
	<div class="orenda-admin-email">Contact Us: <a href="mailto:cs@orenda.finance">cs@orenda.finance</a></div>
    <div class="orenda-admin-version"><small><?PHP echo "Plugin version: ".ORENDA_ELEMENTOR_PLUGIN_VERSION ?></small></div>

		<form action="options.php" method="post">
        <?php 
        settings_fields( 'dbi_orenda_plugin_prefs' );
        do_settings_sections( 'dbi_orenda_plugin_widgets' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    </div> 
    
	<?php
}

/* Admin Menu App Preferences */
function dbi_reg_orenda_settings() {
    register_setting( 'dbi_orenda_plugin_prefs', 'dbi_orenda_plugin_prefs', 'dbi_plugin_orenda_validate' );
    add_settings_section( 'api_settings', 'Program configuration', 'dbi_plugin_orenda_section_text', 'dbi_orenda_plugin_widgets' );
	add_settings_field( 'dbi_plugin_setting_orenda_widget_css', 'Widget styling', 'dbi_plugin_setting_orenda_widget_css', 'dbi_orenda_plugin_widgets', 'api_settings' );
	add_settings_field( 'dbi_plugin_setting_orenda_widget_stage', 'Widget stage', 'dbi_plugin_setting_orenda_widget_stage', 'dbi_orenda_plugin_widgets', 'api_settings' );
    add_settings_field( 'dbi_plugin_setting_orenda_core_stage', 'Core stage', 'dbi_plugin_setting_orenda_core_stage', 'dbi_orenda_plugin_widgets', 'api_settings' );
    add_settings_field( 'dbi_plugin_setting_orenda_widget_sandbox', 'Widget Sandbox active', 'dbi_plugin_setting_orenda_widget_sandbox', 'dbi_orenda_plugin_widgets', 'api_settings' );
    add_settings_field( 'dbi_plugin_setting_orenda_core_card', 'Card', 'dbi_plugin_setting_orenda_core_card', 'dbi_orenda_plugin_widgets', 'api_settings' );
    add_settings_field( 'dbi_plugin_setting_orenda_widget_auth', 'AUTH', 'dbi_plugin_setting_orenda_widget_auth', 'dbi_orenda_plugin_widgets', 'api_settings' );
    add_settings_field( 'dbi_plugin_setting_orenda_intercom', 'Intercom client code', 'dbi_plugin_setting_orenda_intercom', 'dbi_orenda_plugin_widgets', 'api_settings' );
    // add_settings_field( 'dbi_plugin_setting_orenda_pwa', 'PWA Active', 'dbi_plugin_setting_orenda_pwa', 'dbi_orenda_plugin_widgets', 'api_settings' );
}
add_action( 'admin_init', 'dbi_reg_orenda_settings' );

function dbi_plugin_orenda_section_text() {
    echo '<p>Orenda widget preferences. <a href="http://orenda.finance">More info</a></p>';
}

function dbi_plugin_setting_orenda_widget_css() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    echo "<input id='dbi_plugin_setting_orenda_widget_css' name='dbi_orenda_plugin_prefs[widget_css]' type='text' value='" . esc_attr( $options['widget_css'] ) . "' />";
}

function dbi_plugin_setting_orenda_widget_stage() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    echo "<input id='dbi_plugin_setting_orenda_widget_stage' name='dbi_orenda_plugin_prefs[widget_stage]' type='text' value='" . esc_attr( $options['widget_stage'] ) . "' />";
}

function dbi_plugin_setting_orenda_core_card() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    echo "<input id='dbi_plugin_setting_orenda_core_card' name='dbi_orenda_plugin_prefs[widget_card]' type='text' value='" . esc_attr( $options['widget_card'] ) . "' />";
}

function dbi_plugin_setting_orenda_widget_sandbox() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    $optionTrue = ($options['widget_sandbox'] == 'true') ? 'selected' : '';
    $optionFalse = ($options['widget_sandbox'] !== 'true') ? 'selected' : '';
    echo "<select id='dbi_plugin_setting_orenda_widget_sandbox' name='dbi_orenda_plugin_prefs[widget_sandbox]'><option value='true' ". esc_attr($optionTrue) ." >YES</option><option value='false' ". esc_attr($optionFalse) .">NO</option></select>";
}

function dbi_plugin_setting_orenda_core_stage() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    echo "<input id='dbi_plugin_setting_orenda_core_stage' name='dbi_orenda_plugin_prefs[core_stage]' type='text' value='" . esc_attr( $options['core_stage'] ) . "' />";
}

function dbi_plugin_setting_orenda_widget_auth() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    echo "<input id='dbi_plugin_setting_orenda_widget_sandbox' name='dbi_orenda_plugin_prefs[widget_auth]' type='text' value='" . esc_attr( $options['widget_auth'] ) . "' />";
}

function dbi_plugin_setting_orenda_intercom() {
    $options = get_option( 'dbi_orenda_plugin_prefs' );
    echo "<input id='dbi_plugin_setting_orenda_intercom' name='dbi_orenda_plugin_prefs[intercom]' type='text' value='" . esc_attr( $options['intercom'] ) . "' />";
}

function dbi_plugin_orenda_validate( $input ) {
    $newinput['widget_css'] = trim( $input['widget_css'] );
    $newinput['widget_auth'] = trim( $input['widget_auth'] );
    $newinput['intercom'] = trim( $input['intercom'] );
    $newinput['widget_card'] = trim( $input['widget_card'] );
    $newinput['widget_sandbox'] = trim( $input['widget_sandbox'] );
    if ( $newinput['widget_sandbox'] !== "false" ) {
        $newinput['widget_sandbox'] = 'true';
    }

    $newinput['widget_stage'] = trim( $input['widget_stage'] );
    if ( $newinput['widget_stage'] !== "dev" ) {
        $newinput['widget_stage'] = '';
    }

    $newinput['core_stage'] = trim( $input['core_stage'] );
    if ( $newinput['core_stage'] !== "dev" ) {
        $newinput['core_stage'] = '';
    }
   
    return $newinput;
}

?>