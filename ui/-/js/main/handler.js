// head {
var __nodeId__ = "ewma_handlers_ui__main_handler";
var __nodeNs__ = "ewma_handlers_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, {
        options: {},

        _create: function () {
            var widget = this;

            $(".path", widget.element).click(function () {
                $("textarea", widget.element).select();

                document.execCommand("copy");
            });
        }
    });
})(__nodeNs__, __nodeId__);
