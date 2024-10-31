<?php

defined('ABSPATH') || exit;

function OrendaPWA()
{
    add_submenu_page('orenda-admin', 'PWA Settings', 'PWA Settings', 'manage_options', 'orenda_widget_options', 'addOrendaPWA');
}

add_action('admin_menu', 'OrendaPWA');

/* Plugin Admin HTML and Styling */
function addOrendaPWA()
{
?>
    <div class="wrap orenda-admin-pwa-settings">
        <img class="orenda-admin-logo" src=<?php echo ORENDA_ELEMENTOR_PLUGIN_URL . '/assets/img/logo.png' ?>>
        <div class="orenda-admin-head">
            <h1>No Code Embedded<br>Financial Solutions</h1>
            <div class="orenda-admin-intro">Create new revenue streams by building your own Fintech with our easy drag and drop no code, embedded financial products from one platform.</div>
        </div>
        <div class="orenda-admin-email">Contact Us: <a href="mailto:cs@orenda.finance">cs@orenda.finance</a></div>
        <div class="orenda-admin-version"><small><?PHP echo "Plugin version: " . ORENDA_ELEMENTOR_PLUGIN_VERSION ?></small></div>

        <form action="options.php" method="post">
            <?php
            settings_fields('dbi_orenda_pwa_prefs');
            do_settings_sections('dbi_orenda_plugin_pwa'); ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
        </form>
    </div>
<?php
}

function dbi_reg_orenda_pwa_settings()
{

    register_setting('dbi_orenda_pwa_prefs', 'dbi_orenda_pwa_prefs', 'dbi_plugin_orenda_pwa_validate');
    add_settings_section('pwa_settings', 'PWA configuration', 'dbi_plugin_orenda_pwa_section_text', 'dbi_orenda_plugin_pwa');
    add_settings_field('dbi_plugin_setting_orenda_pwa', 'PWA Active', 'dbi_plugin_setting_orenda_pwa', 'dbi_orenda_plugin_pwa', 'pwa_settings');
}

add_action('admin_init', 'dbi_reg_orenda_pwa_settings');

function dbi_plugin_orenda_pwa_section_text()
{
    echo '<p>Orenda widget preferences. <a href="http://orenda.finance">More info</a></p>';
}

function app_store_images()
{
    $options = get_option('dbi_orenda_pwa_prefs');
    // echo '<link rel="stylesheet" href=' . dirname(__DIR__, 1) . "/assets/css/tabcontent.css" . ' type="text/css">';
    // echo '<script src='.dirname(__DIR__, 1) . "/assets/js/tabcontent.js".'></script>'; 
?>
    <div id="orenda-pwa-screenshots">
        <h2>App Store Screenshots</h2>
        <div class="tab">
            <?php

            for ($x = 0; $x < 7; $x += 1) {
                $status = "";
                if ($x == 0) {
                    $status = " active";
                }
            ?>
                <button class='tablinks<?php echo $status ?>' onclick="open_img_config(event, 'img_<?php echo esc_attr($x) ?>')" type="button">Image <?php echo $x + 1 ?></button>
            <?php
            }

            ?>
        </div>
        <?php


        for ($y = 0; $y < 7; $y += 1) {
            $optionJPG = ($options['screenshot_type_' . $y] == 'jpg') ? 'selected' : '';
            $optionPNG = ($options['screenshot_type_' . $y] == 'png') ? 'selected' : '';
            $style = "display: none";
            if ($y == 0) {
                $style = 'display: block';
            }
        ?>
            <div id="img_<?php echo esc_attr($y) ?>" class="tabcontent" style="<?php echo esc_attr($style) ?>">
                <?php
                echo "<div style='width:100; display: flex'><br><b style='line-height: 2em; flex-basis: 15%;'>Image Path</b><input style='flex-basis: 85%;' id='dbi_plugin_setting_orenda_screenshot_path_" . esc_attr($y) . "' name='dbi_orenda_pwa_prefs[screenshot_path_" . esc_attr($y) . "]' type='text' value='" . esc_attr($options['screenshot_path_' . $y]) . "' /></div>";
                echo "<div style='width:100; display: flex'><br><b style='line-height: 2em; flex-basis: 15%;'>Image Type</b><select style='flex-basis: 85%;' id='dbi_plugin_setting_orenda_screenshot_type_" . esc_attr($y) . "' name='dbi_orenda_pwa_prefs[screenshot_type_" . esc_attr($y) . "]'><option value='jpg' " . esc_attr($optionJPG) . ">JPG</option><option value='png' " . esc_attr($optionPNG) . ">PNG</option></select></div>";
                echo "<div style='width:100; display: flex'><br><b style='line-height: 2em; flex-basis: 15%;'>Image Size</b><input style='flex-basis: 85%;' id='dbi_plugin_setting_orenda_screenshot_size_" . esc_attr($y) . "' name='dbi_orenda_pwa_prefs[screenshot_size_" . esc_attr($y) . "]' type='text' value='" . esc_attr($options['screenshot_size_' . $y]) . "' /></div>";
                echo "<div style='width:100; display: flex'><br><b style='line-height: 2em; flex-basis: 15%;'>Optional Text</b><input style='flex-basis: 85%;' id='dbi_plugin_setting_orenda_screenshot_text_" . esc_attr($y) . "' name='dbi_orenda_pwa_prefs[screenshot_text_" . esc_attr($y) . "]' type='text' value='" . esc_attr($options['screenshot_text_' . $y]) . "' /></div>";
                ?>
            </div>
    </div>
<?php
        }
    }

    function dbi_plugin_setting_orenda_pwa()
    {
        $options = get_option('dbi_orenda_pwa_prefs');
        $optionTrue = ($options['pwa'] == 'true') ? 'selected' : '';
        $optionFalse = ($options['pwa'] !== 'true') ? 'selected' : '';
        $message = ($options['pwa_message']) ? $options['pwa_message'] : '';

        $toggleTrue = ($options['pwa_static_manifest'] == 'true') ? 'selected' : '';
        $toggleFalse = ($options['pwa_static_manifest'] == 'false') ? 'selected' : '';

        echo "<select id='dbi_plugin_setting_orenda_pwa' name='dbi_orenda_pwa_prefs[pwa]'><option value='true' " . esc_attr($optionTrue) . " >YES</option><option value='false' " . esc_attr($optionFalse) . ">NO</option></select>";

        if ($message) {
            echo "<br><b style='line-height: 2em;'>Static manifest (Toggle TRUE for PWA Builder submission)</b><br><select id='pwa_static_manifest' name='dbi_orenda_pwa_prefs[pwa_static_manifest]'><option value='true' " . esc_attr($toggleTrue) . ">TRUE</option><option value='false' " . esc_attr($toggleFalse) . ">FALSE</option></select>";
            echo "<br><b style='line-height: 2em;'>App Name</b><br><input id='dbi_plugin_setting_orenda_app_name' name='dbi_orenda_pwa_prefs[app_name]' maxlength='11' type='text' value='" . esc_attr($options['app_name']) . "' />";
            echo "<br><b style='line-height: 2em;'>Image Path</b><br><input id='dbi_plugin_setting_orenda_image_path' name='dbi_orenda_pwa_prefs[image_path]' type='text' value='" . esc_attr($options['image_path']) . "' />";
            echo "<br><b style='line-height: 2em;'>PWA colour</b><br><input id='dbi_plugin_setting_orenda_pwa_colour' name='dbi_orenda_pwa_prefs[pwa_colour]' type='text' value='" . esc_attr($options['pwa_colour']) . "' />";
            echo '<br><b style="line-height: 2em;">Add the following to .htaccess</b><br><div style="border: 1px solid gray; padding: 10px; margin: 0;"><pre>' . $message . '</pre></div><br><div style="border: 1px solid gray; padding: 10px 10px 0 10px; margin: 0 0 10px;">or copy <i>/wp-content/plugins/orenda-widgets-for-elementor/assets/js/pwa/progressier.js</i> to the root hosting folder.</div>';
            app_store_images();
        };
    }

    function dbi_plugin_orenda_pwa_validate($input)
    {
        $img_obj = [];
        $img_obj_arr = array();

        for ($x = 0; $x < 7; $x += 1) {

            $newinput['screenshot_path_' . $x] = $input['screenshot_path_' . $x];
            $newinput['screenshot_type_' . $x] = $input['screenshot_type_' . $x];
            $dims = preg_replace("/ /", "", $input['screenshot_size_' . $x]);
            $newinput['screenshot_size_' . $x] = $dims;
            $newinput['screenshot_text_' . $x] = $input['screenshot_text_' . $x];

            $img_obj['img'] = $input['screenshot_path_' . $x];
            $img_obj['type'] = "image/" . $input['screenshot_type_' . $x];
            $img_obj['size'] = $dims;
            $img_obj['text'] = $input['screenshot_text_' . $x];

            if (!(empty($img_obj['img']) || empty($img_obj['type']) || empty($img_obj['size']))) {
                array_push($img_obj_arr, $img_obj);
            }
        }

        $newinput['manifest_screenshots'] = $img_obj_arr;
        $newinput['image_path'] = trim($input['image_path']);
        $newinput['pwa_colour'] = trim($input['pwa_colour']);
        $newinput['app_name'] = trim($input['app_name']);
        $newinput['pwa'] = trim($input['pwa']);
        $newinput['pwa_static_manifest'] = trim($input['pwa_static_manifest']);

        if ($newinput['pwa'] !== "true") {
            $newinput['pwa'] = 'false';
            $newinput['pwa_message'] = '';
        } else {
            $newinput['pwa_message'] = addHtaccess("progressier");
        }

        if (!preg_match('/^#[a-f0-9]{6}$/i', $newinput['pwa_colour'])) {
            $newinput['pwa_colour'] = '#ffffff';
        }

        if (empty($newinput['app_name'])) {
            $newinput['app_name'] = get_bloginfo('name');
        }

        return $newinput;
    }

    function addHtaccess($config)
    {
        if ($config == "progressier") {
            $lines = array(
                'Options +FollowSymLinks',
                'RewriteEngine On',
                'RewriteRule ^progressier.js$ /wp-content/plugins/orenda-widgets-for-elementor/assets/js/pwa/progressier.js [L]',
            );
            return implode("<br>", $lines);
        }
    }

?>