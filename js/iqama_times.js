/**
 * Created by mshafi on 8/1/14.
 */

(function($) {
    $('#fzami_iqama_maghrib_input').change(function(event) {
        var value = event.target.value;
        if (parseInt(value)) {
            $("form").addClass("with-maghrib-offset");
        } else {
            $("form").removeClass("with-maghrib-offset");
            event.target.value = "";
        }
    });
    $('#fzami_iqama_maghrib_input').change();
})(jQuery);