customModule.adminBlocks = {
    run : function(params) {
        var self = this;

        $(document).on('change', '.change-status', function(e) {
            e.preventDefault();

            var checkbox = $(this);
            var enableUrl = checkbox.data('enable');
            var disableUrl = checkbox.data('disable');
            var url = undefined;

            if (checkbox.prop('checked')) {
                url = enableUrl;
            } else {
                url = disableUrl;
            }

            custom.ajax({
                url: url
            });

            return false;
        });
    }
};