jQuery(function ($) {
  $(".userInput").on("keyup", function () {
    var input = $(this).val();
    console.log("keyup and val: ", input);
    console.log("ajax object");
    console.dir(ajax_object);
    if (input != "") {
      var data = {
        action: "get_articles_titles",
        search_text: input,
        nonce: ajax_object.nonce,
      };
      /*
      $.post(ajax_object.url, data, function (response) {
        console.log("we got a response of");
        console.log(response);
        //$("#txtHint").html(response);
      });
      */
      /*
      jQuery.ajax({
        type: "POST",
        url: ajax_object.url,
        data,
        success: function (data) {
          console.log("success");
          console.dir(data);
        },
        error: function (errorThrown) {
          console.log("wtf");
          console.log(errorThrown);
        },
      });
      */

      jQuery
        .ajax({
          method: "POST",
          url: "/assets/plugins/esg-clinician-processing/public/ajax/ajax-test.php",
          data: { name: "John", location: "Boston" },
        })
        .done(function (msg) {
          console.log("Data Saved: " + msg);
        });
    }
  });
});
