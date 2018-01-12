// head {
var __nodeId__ = "ewma_handlers_ui_handler_nodes__main";
var __nodeNs__ = "ewma_handlers_ui_handler_nodes";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, {
        options: {},

        _create: function () {
            var widget = this;

            widget.bindBarsClick();
        },

        bindBarsClick: function () {
            var widget = this;

            widget._clearCp();

            $(".bar", widget.element).rebind("click", function (e) {
                widget._openCp($(this), e);

                e.stopPropagation();
            });

            $(window).rebind("click." + __nodeId__, function () {
                widget._closeCp();
            });
        },

        _getCpWrapper: function () {
            var $cp = $("." + __nodeId__ + "____cp");

            if (!$cp.length) {
                $cp = $("<div>")
                    .appendTo("body")
                    .addClass(__nodeId__ + "____cp")
                    .css({position: 'absolute'});
            }

            return $cp;
        },

        _openCp: function ($bar, e) {
            var widget = this;

            var $cp = widget._getCpWrapper();

            var opened = widget._isCpOpened($bar);

            widget._closeCp();

            if (!opened) {
                $cp.css({
                    left: e.pageX + 1,
                    top:  e.pageY + 1
                });

                $bar.find(".cp").appendTo($cp).show();
            }
        },

        _isCpOpened: function ($bar) {
            var opened = false;

            this._getCpWrapper().find(".cp").each(function () {
                if ($(this).attr("node_id") == $bar.attr("node_id")) {
                    opened = true;
                }
            });

            return opened;
        },

        _clearCp: function () {
            this._getCpWrapper().html("");
        },

        closeCp: function () {
            this._closeCp();
        },

        _closeCp: function () {
            this._getCpWrapper().find(".cp").each(function () {
                $(this).appendTo(".bar[node_id='" + $(this).attr("node_id") + "']").removeClass("opened").hide();
            });
        }
    });
})(__nodeNs__, __nodeId__);
