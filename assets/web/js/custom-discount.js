jQuery(function ($) {
  $("#apply_custom_discount").on("click", function () {
    var code = $("#custom_discount_code").val();

    $.ajax({
      type: "POST",
      url: custom_discount_params.ajax_url,
      data: {
        action: "apply_custom_discount_code",
        discount_code: code,
      },
      success: function (response) {
        $("#discount_message")
          .text(response.message)
          .css("color", response.success ? "green" : "red");
        $("body").trigger("update_checkout"); // refresh totals
      },
    });
  });
});
