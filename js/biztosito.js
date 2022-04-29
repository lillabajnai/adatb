var legorduloMenu = {
    init: function() {                         // inicializal
        this.legordules();
        this.bindUIActions();
    },

    bindUIActions: function() {
        $("input[type='radio']").on("change", this.legordules);
    },

    legordules: function() {
        $(".biztositas-1").each(function() {
            var p = $(this);
            if ($(p.data("require-pair")).is(":checked")) {
                p.prop("required", true);
            } else {
                p.prop("required", false);
            }
        });

        $(".biztositas-2").each(function() {
            var p = $(this);
            if ($(p.data("require-pair")).is(":checked")) {
                p.prop("required", true);
            } else {
                p.prop("required", false);
            }
        });

        $(".biztositas-3").each(function() {
            var p = $(this);
            if ($(p.data("require-pair")).is(":checked")) {
                p.prop("required", true);
            } else {
                p.prop("required", false);
            }
        });

        $(".biztositas-4").each(function() {
            var p = $(this);
            if ($(p.data("require-pair")).is(":checked")) {
                p.prop("required", true);
            } else {
                p.prop("required", false);
            }
        });

        $(".biztositas-5").each(function() {
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