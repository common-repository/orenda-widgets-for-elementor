<?php

//Build the endpoint url based on the widget stage
function getURL()
{
    $stage = orenda_getStage();

    $endpoint_id = ($stage == "-dev") ?  "ax1td6cpqe" : "pee6rrxp6k";
    $url = "https://" . $endpoint_id . ".execute-api.eu-west-1.amazonaws.com/";
    return $url;
}

function curlPost($url, $data)
{
    $curl = curl_init();
    $data_string = json_encode(array("hash" => $data));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($resp);
    return $data;
}

function getWidgetConfigAPI()
{
    $data = curlPost(getURL() . "widgets", "");

    return $data;
}

function getWidgetConfigAPIHash()
{   
    $localhash = "";
    if (!get_option("widgets_hash")) {
        add_option("widgets_hash");
    } else {
        $localhash = get_option("widgets_hash");
    }
    $data = curlPost(getURL() . "widgets", $localhash);

    return $data;
}
