/*
 * Velzon layout persistence — patched for the Degchi backend.
 * The original script cleared every localStorage key when the HTML snapshot
 * changed, which wiped the dark-mode choice after a reload/redirect. It now
 * simply re-syncs the snapshot and re-applies the stored layout values.
 */
(function () {
    "use strict";

    var themeKeys = [
        "data-layout",
        "data-sidebar-size",
        "data-bs-theme",
        "data-layout-width",
        "data-sidebar",
        "data-sidebar-image",
        "data-layout-direction",
        "data-layout-position",
        "data-layout-style",
        "data-topbar",
        "data-preloader",
        "data-body-image",
        "data-theme",
        "data-theme-colors"
    ];

    var currentAttributes = {};
    Array.from(document.documentElement.attributes).forEach(function (attribute) {
        if (attribute && attribute.nodeName && attribute.nodeName !== "undefined") {
            currentAttributes[attribute.nodeName] = attribute.nodeValue;
        }
    });

    var storedDefault = localStorage.getItem("defaultAttribute");
    if (storedDefault && storedDefault !== JSON.stringify(currentAttributes)) {
        try {
            localStorage.setItem("defaultAttribute", JSON.stringify(currentAttributes));
        } catch (e) {}
    }

    var storedValues = {};
    themeKeys.forEach(function (key) {
        var value = localStorage.getItem(key);
        if (value) {
            storedValues[key] = value;
        }
    });

    Object.keys(storedValues).forEach(function (key) {
        document.documentElement.setAttribute(key, storedValues[key]);
    });
})();
