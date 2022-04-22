var legorduloMenu = {
    init: function() {                         // inicializal
        this.legordules();
        this.bindUIActions();
    },

    bindUIActions: function() {
        $("input[type='radio']").on("change", this.legordules);
    },

    legordules: function() {
        $(".egyiranyu-repjegy").each(function() {
            var p = $(this);
            if ($(p.data("require-pair")).is(":checked")) {
                p.prop("required", true);
            } else {
                p.prop("required", false);
            }
        });

        $(".tobb-megallos-repjegy").each(function() {
            var p = $(this);
            if ($(p.data("require-pair")).is(":checked")) {
                p.prop("required", true);
            } else {
                p.prop("required", false);
            }
        });
    }
};
legorduloMenu.init();