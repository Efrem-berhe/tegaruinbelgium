(function ($) {
    $('div#edit-symptoms input#edit-symptoms-none').click(function () {
        if(this.checked) {
            $("div#edit-symptoms input[type=checkbox]").not(this).prop("checked",false);
        }
    })
    $('div#edit-additional-symptoms input#edit-additional-symptoms-none').click(function () {
        if(this.checked) {
            $("div#edit-additional-symptoms input[type=checkbox]").not(this).prop("checked",false);
        }
    })
    $('div#edit-pre-existing-diseases input#edit-pre-existing-diseases-none').click(function () {
        if(this.checked) {
            $("div#edit-pre-existing-diseases input[type=checkbox]").not(this).prop("checked",false);
        }
    })
})(jQuery);
;
