(function () {
  jQuery(document).on("elementor/popup/show", () => {
    if (jQuery("#PinWidget").length) {
      jQuery.getScript("https://pin.orenda.link/");
    }
    if (jQuery("#ActivateCardWidget").length) {
      jQuery.getScript("https://activatecard.orenda.link/");
    }
    if (jQuery("#UnBlockCardWidget").length) {
      jQuery.getScript("https://unblockcard.orenda.link/");
    }
    if (jQuery("#ReplaceCardWidget").length) {
      jQuery.getScript("https://replacecard.orenda.link/");
    }
  });
})();
