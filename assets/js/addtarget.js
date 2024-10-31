(function () {
    //For adding target="_blank" to relevant <a> tags
    var a_elements = document.getElementsByTagName("a");
    var len = a_elements.length;
    for(var x = 0; x < len; x+=1){
        if (a_elements[x].getAttribute("href").includes("/terms_and_conditions") || a_elements[x].getAttribute("href").includes("/privacy_policy")){
            a_elements[x].setAttribute("target", "_blank");
        }
    };
  })();
  