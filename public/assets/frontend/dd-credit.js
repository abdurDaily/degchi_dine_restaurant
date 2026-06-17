/**
 * Degchi Dine — developer attribution (protected console credit)
 */
(function (w) {
    if (w.__DD_DEV_CREDIT__) return;
    w.__DD_DEV_CREDIT__ = 1;

    var _d = function (s) {
        try {
            return decodeURIComponent(
                atob(s)
                    .split("")
                    .map(function (c) {
                        return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
                    })
                    .join(""),
            );
        } catch (e) {
            return "";
        }
    };

    var _label = _d("ZGV2ZWxvcGVkIGJ5");
    var _name = _d("QWJkdXIgUmFobWFu");
    var _url = _d("aHR0cHM6Ly9naXRodWIuY29tL2FiZHVyRGFpbHk=");

    function _show() {
        if (!_label || !_name || !_url) return;
        w.console.log(
            "%c " +
                _label +
                " %c" +
                _name +
                "%c  →  %c" +
                _url,
            "color:#5a7a85;font-size:12px;font-family:Manrope,Poppins,sans-serif;",
            "color:#116b83;font-size:13px;font-weight:700;font-family:Manrope,Poppins,sans-serif;",
            "color:transparent;font-size:0;",
            "color:#e7ae07;font-size:11px;font-family:monospace;",
        );
    }

    _show();

    if (w.console && typeof w.console.clear === "function") {
        var _clear = w.console.clear.bind(w.console);
        w.console.clear = function () {
            _clear.apply(w.console, arguments);
            setTimeout(_show, 60);
        };
    }

    try {
        Object.defineProperty(w, "__DD_DEV_CREDIT__", {
            value: 1,
            writable: false,
            configurable: false,
        });
    } catch (e) {
        /* ignore */
    }
})(window);
