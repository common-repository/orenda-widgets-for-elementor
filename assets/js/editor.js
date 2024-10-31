(function () {
  //For reloading React in Elementor Editor
  if (orendaWidgetUrl.length) {
    var { id, url } = JSON.parse(orendaWidgetUrl);
    var existCondition = setInterval(function () {
      if (jQuery(id).length) {
        console.log("Starting: " + id);
        jQuery.getScript(url);
        clearInterval(existCondition);
      }
    }, 100); // check every 100ms
  }
})();
