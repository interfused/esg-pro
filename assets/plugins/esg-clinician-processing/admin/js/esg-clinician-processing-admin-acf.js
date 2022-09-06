function acf_city_state_zip() {
  console.log(`acf check things`);
  var filterZipEl = jQuery("#esgZipWrapper");
  if (filterZipEl.is(":visible")) {
    console.log("filterZipEl is visible");
  } else {
    console.log("filterZipEl is not visible");
  }
  acf.add_action("load", function ($el) {
    // $el will be equivalent to $('body')
    console.log("add acf actions");

    // find a specific field
    //var $field = $('#my-wrapper-id');

    // do something to $field
  });

  var baseHtml =
    '<div id="zipStateCountyStateFilter"><h2>Filter Here</h2></div>';
  //jQuery(baseHtml).insertAfter("#esgZipWrapper");
  jQuery("#esgZipWrapper").append("wtf");
}
(function ($) {
  "use strict";
  console.log("DO ACF STUFF");
  acf_city_state_zip();
  acf.add_action("load", function ($el) {
    // $el will be equivalent to $('body')

    // find a specific field
    //var $field = $el.find('#my-wrapper-id');

    // do something to $field
    console.log("ACF is loaded do something");
  });
  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
})(jQuery);
