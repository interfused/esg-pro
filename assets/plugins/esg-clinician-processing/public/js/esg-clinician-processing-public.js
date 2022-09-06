function esg_profile_edit_functionality() {
  jQuery(".filteredLocations").hide();
  /*
  jQuery("a#addNewSystemLocations").on("click", function () {
    jQuery("#practiceLocations").hide();
    jQuery("form#addSystemPracticeLocation").removeClass("hidden");
  });

  jQuery("a#addNewSystemLocationServices").on("click", function () {
    jQuery("#practiceLocations").hide();
    jQuery("form#addNewSystemLocationServices").removeClass("hidden");
  });
  */

  jQuery(document).on("blur input", "input[name=zipcode]", function (e) {
    searchValue = jQuery.trim(jQuery(this).val());
    console.dir(`searchValue: ${searchValue}`);

    if (searchValue.length !== 5) {
      console.log("zip not valid");
      jQuery("[name=city], [name=state]").val("");
      jQuery(".filteredLocations").hide();
      return;
    }

    var ajaxUrl = esgPublicAjaxPath + "/ajax-processor.php";
    console.log(`ajaxUrl: ${ajaxUrl}`);

    jQuery.ajax({
      type: "POST",
      url: ajaxUrl,
      data: {
        action: "esg_zip_table_search",
        data: {
          zipcode: jQuery("[name=zipcode]").val(),
        },
      },
      success: function (response) {
        //Success
        var jsonData = JSON.parse(response);
        console.log("success returned");
        console.dir(jsonData);
        if (jsonData.length > 1) {
          console.log(`dynamic population of ${jsonData.length} results`);
        } else {
          console.log("final result: ");
          jQuery(".filteredLocations").show();
          jQuery(".filteredLocations > h3").hide();
          jQuery('[name="city"]').val(jsonData[0].city).attr("readonly", true);
          jQuery('[name="state"]')
            .val(jsonData[0].state)
            .attr("readonly", true);
          jQuery('[name="zipTableRecId"]').val(jsonData[0].id);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        //Error
        console.log("error thrown wtf:");
        console.log(errorThrown);
        console.log("textStatus:");
        console.log(textStatus);
        console.log("XMLHttpRequest");
        console.log(XMLHttpRequest);
      },
      timeout: 60000,
    });
  });
}

function matchStart(params, data) {
  // If there are no search terms, return all of the data
  if (jQuery.trim(params.term) === "") {
    return data;
  }

  // Skip if there is no 'children' property
  if (typeof data.children === "undefined") {
    return null;
  }

  // `data.children` contains the actual options that we are matching against
  var filteredChildren = [];
  jQuery.each(data.children, function (idx, child) {
    if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
      filteredChildren.push(child);
    }
  });

  // If we matched any of the timezone group's children, then set the matched children on the group
  // and return the group object
  if (filteredChildren.length) {
    var modifiedData = jQuery.extend({}, data, true);
    modifiedData.children = filteredChildren;

    // You can return modified objects from here
    // This includes matching the `children` how you want in nested data sets
    return modifiedData;
  }

  // Return `null` if the term should not be displayed
  return null;
}
(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
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

  $(function () {
    console.log("I AM READY");

    jQuery(".select2Convert").select2({
      placeholder: "Select an option. Type to search...",
      allowClear: true,
      width: "100%",
    });

    jQuery(".select2Multiple").select2({
      placeholder: "Select all that apply",
      allowClear: true,
      width: "100%",
    });

    esg_profile_edit_functionality();
  });
})(jQuery);
