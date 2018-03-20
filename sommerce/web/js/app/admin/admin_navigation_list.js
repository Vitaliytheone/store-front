customModule.adminNavigationList = {
    run: function(params) {

        /*****************************************************************************************************
         *                     Nestable menu items
         *****************************************************************************************************/
        (function (window, alert){

            var params = {}; // TODO:: DELETE IT! Prepare for custom modules

            var updatePositionUrl = params.action_update_url;

            var $neatable = $('#nestable'),
                updateOutput = function updateOutput(e) {
                    var list = e.length ? e : $(e.target),
                        output = list.data('output');
                    if (window.JSON) {
                        console.log('Ok');
                    } else {
                        output.html('JSON browser support required for this demo.');
                    }
                };
            if ($neatable.length > 0) {

                $neatable.nestable({
                    group: 0,
                    maxDepth: 3
                }).on('change', updater);

                // updateOutput($('#nestable').data('output', $('#nestable-output')));
            }

            function updater(e) {

                var positions = $neatable.nestable('serialize');

                $.ajax({
                    url: updatePositionUrl,
                    type: "POST",
                    data: {
                        positions: positions
                    },
                    success: function(data, textStatus, jqXHR) {
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    }
                });

            }

        })({}, function (){});


        /*****************************************************************************************************
         *                      Delete (mark as deleted) Nav
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var params = {}; // TODO:: DELETE IT! Prepare for custom modules
            var successRedirectUrl  = params.successRedirectUrl || '/admin/settings/navigation';

            var modelId;

            var deleteModelUrl;

            var $modal = $('#delete-modal'),
                $modalLoader = $modal.find('.modal-loader'),
                $buttonDelete = $modal.find('#feature-delete');

            $buttonDelete.on('click', function(){

                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: deleteModelUrl,
                    type: "DELETE",
                    success: function (data, textStatus, jqXHR){
                        if (data.error){
                            $modalLoader.addClass('hidden');
                            return;
                        }
                        //Success
                        _.delay(function(){
                            $(location).attr('href', successRedirectUrl);
                            // $modalLoader.addClass('hidden');
                            // $modal.modal('hide');
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
                var $button = $(event.relatedTarget);
                modelId =  $button.closest('li').data('id');
                deleteModelUrl = $button.data('delete_url');
            });

            $modal.on('hidden.bs.modal', function (){
                modelId = null;
                deleteModelUrl = null;
            });

        })({}, function (){});

    }
};


