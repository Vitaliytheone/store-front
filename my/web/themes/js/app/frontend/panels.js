customModule.panelsController = {
    run : function() {
        $('.alert .close').click(function (e) {
            $(this).parent().addClass('hidden');
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }
};
