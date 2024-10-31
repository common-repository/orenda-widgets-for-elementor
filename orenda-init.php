<?php

/**
 * Plugin Name: Orenda Widgets for Elementor
 * Description: Explore Our No Code Widget Library. Say goodbye to sourcing technical expertise to integrate your financial services. With Orenda’s no code, plug n play approach to embedded finance with our complex API functions already conveniently built-in, you eliminate the need for developers. Each widget is self contained in the platform which means you can launch your financial products in minutes by using Orenda's pre-built templates or integrated into your own web and mobile applications.
 * Plugin URI:  https://www.orenda.finance/widget-library
 * Version:     1.2.2
 * Author:      Orenda Finance
 * Author URI:  https://orenda.finance
 * Text Domain: orenda-elementor-widget
 *
 * Elementor tested up to: 3.5.6
 * Elementor Pro tested up to: 3.5.6
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('ORENDA_ELEMENTOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ORENDA_ELEMENTOR_PLUGIN_VERSION', "1.2.2");
/**
 * Register Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */

function importWidgetsEditor()
{
	require_once("lib/update-widgets.php");
	updateWidgets(false);
}

function importWidgetsCron(){
	require_once("lib/update-widgets.php");
	updateWidgets();
}

add_action('elementor/editor/init', 'importWidgetsEditor');
add_action('init', 'importWidgetsCron');

function import_cron_activation()
{
	if (!wp_next_scheduled('import_cron')) {
		wp_schedule_event(time(), 'hourly', 'import_cron');
	}
	add_action('import_cron', 'importWidgetsCron');
}

add_action("init", "import_cron_activation");

function register_orenda_widgets($widgets_manager)
{
	require_once("lib/getwidgets.php");

	$active_widgets = get_option("active_widgets");

	if (empty($active_widgets)) {
		updateWidgets();
		$active_widgets = get_option("active_widgets");
	}

	$active_widgets = explode(", ", $active_widgets);

	foreach ($active_widgets as $name) {
		if (!empty($name)) {
			require_once(__DIR__ . '/widgets/' . $name . '.php');
			$classname = "Elementor_orenda_" . $name;
			$widgets_manager->register(new $classname);
		}
	}
}
add_action('elementor/widgets/register', 'register_orenda_widgets');

function add_orenda_elementor_widget_categories($orenda_elements_manager)
{
	$orenda_elements_manager->add_category(
		'orenda-cards-category',
		[
			'title' => esc_html__('Orenda Cards', 'plugin-name'),
			'icon' => 'fa fa-plug',
		]
	);
	$orenda_elements_manager->add_category(
		'orenda-bank-category',
		[
			'title' => esc_html__('Orenda Banking', 'plugin-name'),
			'icon' => 'fa fa-plug',
		]
	);
	$orenda_elements_manager->add_category(
		'orenda-other-category',
		[
			'title' => esc_html__('Orenda Other', 'plugin-name'),
			'icon' => 'fa fa-plug',
		]
	);
}
add_action('elementor/elements/categories_registered', 'add_orenda_elementor_widget_categories');


/* Activate Orenda */
add_action('init', 'OrendaHeaderIncludes');
add_action('wp_body_open', 'OrendaWrapperStart');
add_action('wp_footer', 'OrendaWrapperEnd');
add_action('elementor/frontend/after_register_scripts', 'OrendaFooteerIncludes');

function OrendaFooteerIncludes()
{
	wp_register_script('orenda-popup-script', ORENDA_ELEMENTOR_PLUGIN_URL . 'assets/js/popup.js', ['jquery'], null, true);
	wp_enqueue_script('orenda-popup-script');
}

function OrendaWrapperStart()
{
	echo '<div id="dm">';
}
function OrendaWrapperEnd()
{
	$auth = orenda_getAuth();
	echo '</div><div id="Widgets" authName="' . esc_attr($auth) . '" appId="dm"></div>';
}

function OrendaFrontendHeaderIncludes()
{
	wp_register_script("orenda-addtarget-script", ORENDA_ELEMENTOR_PLUGIN_URL . 'assets/js/addtarget.js');
	wp_enqueue_script("orenda-addtarget-script");
}

add_action("wp_head", "OrendaFrontendHeaderIncludes");

function OrendaAdminHeaderIncludes()
{
	wp_register_style("tab-content-style",  ORENDA_ELEMENTOR_PLUGIN_URL . "assets/css/tabcontent.css", [], null, false);
	wp_enqueue_style("tab-content-style");

	wp_register_script("tab-content-script", ORENDA_ELEMENTOR_PLUGIN_URL . 'assets/js/tabcontent.js');
	wp_enqueue_script("tab-content-script");
}

add_action("admin_init", "OrendaAdminHeaderIncludes");

function OrendaHeaderIncludes()
{

	add_option("active_widgets", "");
	$core = orenda_isStageCore();
	$intercom = orenda_getIntercom();
	$stage = orenda_getStage();
	$options = get_option('dbi_orenda_plugin_prefs');
	$pwa_options = get_option('dbi_orenda_pwa_prefs');
	if ($pwa_options['pwa'] == "true" && !is_user_logged_in()) {

		$pwa_colour = esc_attr($pwa_options['pwa_colour']);
		$pwa_name = esc_attr($pwa_options['app_name']);
		$image_path = esc_attr($pwa_options['image_path']);
		$pwa_name_short = explode(" ", $pwa_name)[0];
		$pwa_static_manifest = $pwa_options['pwa_static_manifest'];
		$manifest_screenshots = $pwa_options['manifest_screenshots'];

		if (empty($image_path)) {
			$image_path = "members_orenda_finance";
		}

		$data_to_pass = array(
			"icon512" => "https://assets.orenda.finance/" . str_replace(".", "_", $image_path) . "/logo512_default.png",
			"name" => $pwa_name,
			"shortName" => $pwa_name_short,
			"themeColor" => esc_attr($pwa_colour),
			"manifestScreenshots" => $manifest_screenshots,
		);

		wp_register_script('orenda-enable-pwa', 'https://progressier.com/client/script.js?id=t6PB1ocNyU80mkVZ58J3', [], null, false);

		if ($pwa_static_manifest == 'true') {
			echo '<link rel="manifest" href="https://progressier.com/client/progressier.json?id=t6PB1ocNyU80mkVZ58J3"><script id="orenda-enable-pwa" defer="defer" src="https://progressier.com/client/script.js?id=t6PB1ocNyU80mkVZ58J3" type="text/javascript"></script>';
		}

		wp_localize_script('orenda-enable-pwa', 'progressierAppRuntimeSettings', $data_to_pass);

		wp_enqueue_script('orenda-enable-pwa');
	}
	wp_register_script('orenda-enable-sandbox', ORENDA_ELEMENTOR_PLUGIN_URL . 'assets/js/sandbox.js', [], null, false);
	(orenda_isSandbox() == "false" && !is_user_logged_in()) ? '' : wp_enqueue_script('orenda-enable-sandbox');

	wp_register_script('orenda-core-function', esc_url('https://frontend' . esc_attr($core) . '.orenda.finance'), [], null, false);
	wp_enqueue_script('orenda-core-function');

	wp_register_script('orenda-intercom-script', ORENDA_ELEMENTOR_PLUGIN_URL . 'assets/js/intercom.js', ['orenda-core-function',], null, false);
	wp_enqueue_script('orenda-intercom-script');

	wp_localize_script('orenda-intercom-script', 'orendaIntercomCode', esc_attr($intercom));

	wp_register_style('orenda-core-head-styles', esc_url('https://frontend' . esc_attr($stage) . '.orenda.finance/index.css'), [], null, false);
	wp_enqueue_style('orenda-core-head-styles');

	wp_register_style('orenda-core-foot-styles', esc_url($options['widget_css']), [], null, false);
	wp_enqueue_style('orenda-core-foot-styles');

	add_filter('script_loader_tag', 'orenda_defer_scripts', 10, 3);

	function orenda_defer_scripts($tag, $handle, $src)
	{
		// The handles of the enqueued scripts we want to defer
		$defer_scripts = array(
			'orenda-enable-pwa', 'orenda-core-function',
		);

		if (in_array($handle, $defer_scripts)) {
			return '<script id="' . $handle . '" defer="defer" src="' . $src . '" type="text/javascript"></script>' . "\n";
		}

		return $tag;
	}
}

function orenda_getStage()
{
	$settings = get_option('dbi_orenda_plugin_prefs');
	$value = esc_attr($settings['widget_stage']);
	return ($value == "dev") ? "-" . $value : '';
}

function orenda_isStageCore()
{
	$settings = get_option('dbi_orenda_plugin_prefs');
	$value = esc_attr($settings['core_stage']);
	return ($value == "dev") ? "-" . $value : '';
}

function orenda_isSandbox()
{
	$settings = get_option('dbi_orenda_plugin_prefs');
	$value = esc_attr($settings['widget_sandbox']);
	return  $value;
}

function orenda_getAuth()
{
	$settings = get_option('dbi_orenda_plugin_prefs');
	$value = esc_attr($settings['widget_auth']);
	return (!$value == "") ? $value : '';
}

function orenda_getIntercom()
{
	$settings = get_option('dbi_orenda_plugin_prefs');
	$value = esc_attr($settings['intercom']);
	return (!$value == "") ? $value : 'e5ni5di4';;
}

function orenda_getCard()
{
	$settings = get_option('dbi_orenda_plugin_prefs');
	$value = esc_attr($settings['widget_card']);
	$stage = esc_attr($settings['core_stage']);
	$default = "cards";
	return ($value == "") ? $default : $value;
}

/*Admin Page*/
require_once(__DIR__ . '/admin/index.php');
require_once(__DIR__ . '/admin/pwa_settings.php');
/*Auto Updates*/
//require_once( __DIR__ . '/updates.php' );

/* 
Optional: 
This allows you to use widgets in Wordpress and all other WP Builders using shortcodes.
e.g. [BalanceWidget]
*/
// require_once( __DIR__ . '/widgets/shortcodes.php' );
