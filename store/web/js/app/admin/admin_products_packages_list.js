
customModule.adminProductsList = {
    run: function(params) {

        /*****************************************************************************************************
         *                     Sortable Products-Packages
         *****************************************************************************************************/
        (function (window, alert){
            var $productsSortable = $('.sortable'),
                $packagesSortable = $(".group-items");

            // Init sortable
            if ($productsSortable.length > 0) {
                // Sort the parents
                $productsSortable.sortable({
                    containment: "document",
                    items: "> div.product-item",
                    handle: ".move",
                    tolerance: "pointer",
                    cursor: "move",
                    opacity: 0.7,
                    revert: false,
                    delay: false,
                    placeholder: "movable-placeholder"
                });

                // Sort the children
                $packagesSortable.sortable({
                    items: "> div.package-item",
                    handle: ".move",
                    tolerance: "pointer",
                    containment: "parent"
                });
            }

            $productsSortable.sortable({
                update: function(event, ui) {
                    var currentItem = ui.item,
                        newPosition = currentItem.index(),
                        actionUrl = currentItem.data('action-url') + newPosition;

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        success: function (data, textStatus, jqXHR){
                            if (data.error){
                                return;
                            }
                            //Success
                        },
                        error: function (jqXHR, textStatus, errorThrown){
                            console.log('Error on save', jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });

            $packagesSortable.sortable({
                update: function (event, ui) {
                    var currentItem = ui.item,
                        newPosition = currentItem.index(),
                        actionUrl = currentItem.data('action-url') + newPosition;

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        success: function (data, textStatus, jqXHR) {
                            if (data.error) {
                                return;
                            }
                            //Success
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Error on save', jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });

        })({}, function (){});


        /*****************************************************************************************************
         *                      Delete (mark as deleted) Package
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';
            var $modal = $('#delete-modal'),
                $modalLoader = $modal.find('.modal-loader'),
                buttonDelete = $modal.find('#feature-delete'),
                actionUrl,
                successRedirectUrl;

            buttonDelete.on('click', function(){
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: actionUrl,
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
                var button = $(event.relatedTarget);
                actionUrl = button.data('action-url');
                successRedirectUrl = $modal.data('success_redirect');
            });

            $modal.on('hidden.bs.modal', function (){
                actionUrl = null;
            });

        })({}, function (){});
    }
};
