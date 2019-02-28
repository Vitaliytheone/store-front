customModule.superadminToolsControllerLevopanelAction = {
    run : function(params) {

        $(document).on('click', '#addDomainButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#addDomainForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#add_domain_modal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('#add_domain_modal').on('show.bs.modal', function (e) {
            $(this).find('#addDomainError').addClass('hidden');
            $(this).find('#adddomainform-domain').val('');
        })
    }
};