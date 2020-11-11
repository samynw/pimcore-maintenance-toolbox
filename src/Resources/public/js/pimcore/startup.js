pimcore.registerNS("pimcore.plugin.MaintenanceToolboxBundle");

pimcore.plugin.MaintenanceToolboxBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.MaintenanceToolboxBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("MaintenanceToolboxBundle ready!");
    }
});

var MaintenanceToolboxBundlePlugin = new pimcore.plugin.MaintenanceToolboxBundle();
