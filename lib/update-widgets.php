<?php

function getTemplateFileText()
{
    ob_start();
    include "template.txt";
    $contents = ob_get_clean();
    return $contents;
}
function buildOption($option_list)
{
    $rtrn = '';
    foreach ($option_list as $option) {
        $option_str = '"!value!" => esc_html__( "!option!", "plugin-name" ),';
        $val = $option->value;
        $op = $option->option;
        $option_str = str_replace("!value!", $val, $option_str);
        $option_str = str_replace("!option!", $op, $option_str);

        $rtrn = $rtrn . $option_str . "\n";
    }
    return $rtrn;
}

function buildRenderString($render_arr)
{

    $rtrn = "";
    foreach ($render_arr as $obj) {
        $attr = $obj->attr;
        $var = $obj->var;
        $default = $obj->default;
        $value = (empty($default)) ? 'esc_attr($settings["' . $var . '"' . "])" : "'" . $default . "'";
        $rtrn = $rtrn . '$' . $var . ' = $settings["' . $var . '"];' . "\n";
        $rtrn = $rtrn . "echo '" . $attr . "=" . '"' . "';\n";
        $rtrn = $rtrn . "echo ( $" . $var . " ) ? esc_attr($" . $var . ") : " . $value . ";" . "\n";
        $rtrn = $rtrn . "echo '" . '"' . "';\n";
    }
    return $rtrn;
}

function buildControls($control_list)
{
    $controls_str = "";
    $controls_str = $controls_str . '
        protected function register_controls() {
        $this->start_controls_section(
            "content_section",
            [
                "label" => esc_html__( "Content", "orenda-elementor" ),
                "tab" => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );';
    foreach ($control_list as $control) {
        $id = $control->id;
        $label = $control->label;
        $type = $control->type;

        if ($type == "text") {
            $c_str = '$this->add_control(
                    "!id!",
                        [
                            "label" => esc_html__( "!label!", "orenda-elementor" ),
                            "type" => \Elementor\Controls_Manager::TEXT,
                            "input_type" => "input_type",
                            "placeholder" => esc_html__( "!placeholder!", "orenda-elementor" ),
                        ]
                    );';
            $input_type = $control->input_type;
            $placeholder = $control->placeholder;
            $c_str = str_replace("!id!", $id, $c_str);
            $c_str = str_replace("!label!", $label, $c_str);

            if (empty($input_type)) {
                $c_str = str_replace('"input_type" => "input_type",', "", $c_str);
            } else {
                $c_str = str_replace("!input_type!", $input_type, $c_str);
            }

            $c_str = str_replace("!placeholder!", $placeholder, $c_str);
            $controls_str = $controls_str . "\n" . $c_str;
            continue;
        }

        if ($type == "select") {
            $c_str = '$this->add_control(
                "!id!",
                [
                    "type" => \Elementor\Controls_Manager::SELECT,
                    "label" => esc_html__( "!label!", "orenda-elementor" ),
                    "options" => [
                        !options!
                    ],
                    "default" => "!default!",
                ]
            );';
            $options = $control->options;
            $default = $control->default;
            $c_str = str_replace("!options!", buildOption($options), $c_str);
            $c_str = str_replace("!id!", $id, $c_str);
            $c_str = str_replace("!label!", $label, $c_str);
            $c_str = str_replace("!default!", $default, $c_str);
            $controls_str = $controls_str . "\n" . $c_str;
            continue;
        }
        
        if ($type == "hidden") {
            $c_str = '$this->add_control(
                "!id!",
                [
                    "type" => \Elementor\Controls_Manager::HIDDEN,
                    "label" => esc_html__( "!label!", "orenda-elementor" ),
                    "default" => !default!,
                ]
            );';
            $default = $control->default;
            $c_str = str_replace("!id!", $id, $c_str);
            $c_str = str_replace("!label!", $label, $c_str);
            $c_str = str_replace("!default!", $default, $c_str);
            $controls_str = $controls_str . "\n" . $c_str;
            continue;
        }
    }
    $controls_str = $controls_str . "\n" . '$this->end_controls_section();' . "\n}";
    return $controls_str;
}

function getWidgetString($obj)
{
    $template = $obj->template;
    $id = $obj->id;
    $title = $obj->title;
    $help_url = $obj->help_url;
    $categories = json_encode($obj->categories);
    $keywords = json_encode($obj->keywords);
    $url_id = $obj->url_id;
    $dom_id = $obj->dom_id;
    $id_no_space = str_replace("-", "", $id);
    $render_arr = $obj->render;

    $widget_string = GetTemplateFileText();

    if (!empty($url_id)) {
        $widget_string = str_replace("!urlid!", $url_id, $widget_string);
    } else {
        $widget_string = str_replace("!urlid!", $id_no_space, $widget_string);
    }

    if (!empty($render_arr)) {
        $render_string = buildRenderString($render_arr);
        $widget_string = str_replace("!render!", $render_string, $widget_string);
    } else {
        $widget_string = str_replace("!render!", "", $widget_string);
    }

    $widget_string = str_replace("!idnospace!", $id_no_space, $widget_string);
    $widget_string = str_replace("!widgetid!", $id, $widget_string);
    $widget_string = str_replace("!title!", $title, $widget_string);
    $widget_string = str_replace("!helpurl!", $help_url, $widget_string);
    $widget_string = str_replace("!categories!", $categories, $widget_string);
    $widget_string = str_replace("!keywords!", $keywords, $widget_string);
    $widget_string = str_replace("!domid!", $dom_id, $widget_string);

    if ($template == "controls") {
        $controls = $obj->controls;
        $widget_string = str_replace("!controls!", buildControls($controls), $widget_string);
        return $widget_string;
    }
    $widget_string = str_replace("!controls!", "", $widget_string);
    return $widget_string;
}

function updateWidgets($useHash = true)
{
    require_once(__DIR__ . '/getwidgets.php');
    $updated_list = [];
    if ($useHash == true) {
        $curl_data = getWidgetConfigAPIHash();
    } else {
        $curl_data = getWidgetConfigAPI();
    }
    if ($curl_data == false) {
        return;
    }

    $hash = $curl_data->hash;
    $widget_list = $curl_data->Items;

    if (!empty($widget_list)) {
        foreach ($widget_list as $wn) {
            $id = $wn->id;
            $id = str_replace("-", "", $id);
            $fn = $id . ".php";
            $myfile = fopen(dirname(__DIR__, 1) . "/widgets/" . $fn, "w");
            $d = getWidgetString($wn);
            fwrite($myfile, "<?php\n");
            fwrite($myfile, $d);
            fclose($myfile);
            array_push($updated_list, $id);
        }
        $d = implode(", ", $updated_list);
        update_option("active_widgets", $d);
    }
    update_option("widgets_hash", $hash);
}
