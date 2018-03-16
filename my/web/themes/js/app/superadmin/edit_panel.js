customModule.superadminEditPanelController = {
    run : function(params) {

        new Clipboard('.copy');

        $('#generateApikey').click(function(e) {
            e.preventDefault();

            var btn = $(this);

            $.get(btn.attr('href'), function(response) {
                $('#editprojectform-apikey').val(response.key);
            });

            return false;
        });
    }
};