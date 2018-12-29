/**
 * /admin/settings/pages custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPages = {
    run: function (params) {
        /*****************************************************************************************************
         *                      Delete (mark as deleted) Page
         *****************************************************************************************************/
        var $modal = $('#delete-modal'),
            $modalLoader = $modal.find('.modal-loader'),
            $buttonDelete = $modal.find('#feature-delete'),
            actionUrl,
            successRedirectUrl;

        $buttonDelete.on('click', function(){
            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "DELETE",
                success: function (data, textStatus, jqXHR){
                    //Success
                    _.delay(function(){
                        $(location).attr('href', successRedirectUrl);
                    }, 500);
                },
                error: function (jqXHR, textStatus, errorThrown){
                    $modalLoader.addClass('hidden');
                    $modal.modal('hide');
                    console.log('Error on service save', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $modal.on('show.bs.modal', function (event){
            var button = $(event.relatedTarget);
            actionUrl = button.data('action_url');
            successRedirectUrl = $modal.data('success_redirect');
        });

        $modal.on('hidden.bs.modal', function (){
            actionUrl = null;
        });
    }
};