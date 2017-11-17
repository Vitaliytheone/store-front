/**
 * Order details custom js module
 * @type {{run: customModule.ordersDetails.run}}
 */
customModule.ordersDetails = {
    run : function(params) {
        $(document).ready(function () {
            var ajaxEndpoint = '/admin/orders/get-order-details';
            var $detailsModal = $('#suborder-details-modal'),
                $modalTitle = $detailsModal.find('.modal-title'),
                $provider = $detailsModal.find('#order-detail-provider'),
                $providerOrderId = $detailsModal.find('#order-detail-provider-order-id'),
                $providerResponse = $detailsModal.find('#order-detail-provider-response'),
                $providerUpdate = $detailsModal.find('#order-detail-lastupdate'),
                $modalLoader = $detailsModal.find('.modal-loader');

            $detailsModal.on('show.bs.modal', function(e) {
                var $target = $(e.relatedTarget),
                    suborderId = $target.data('suborder-id'),
                    modalTitle = $target.data('modal_title');

                if (suborderId === undefined || isNaN(suborderId)) {
                    return;
                }
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: ajaxEndpoint,
                    type: "GET",
                    data: {
                        'suborder_id': suborderId
                    },
                    success: function (data) {
                        $modalLoader.addClass('hidden');
                        if (data.details === undefined) {
                            return;
                        }
                        renderLogs(data.details);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log('Something is wrong!');
                        console.log(jqXHR, textStatus, errorThrown);
                        $modalLoader.addClass('hidden');
                    }
                });

                function renderLogs(details){
                    $modalTitle.html(modalTitle);
                    $provider.val(details.provider);
                    $providerOrderId.val(details.provider_order_id);
                    $providerResponse.html(details.provider_response);
                    $providerUpdate.val(details.updated_at);
                }
            });

            $detailsModal.on('hidden.bs.modal',function(e) {
                var $currentTarget = $(e.currentTarget);
                $currentTarget.find('input').val('');
                $providerResponse.html('');
            });
        });
    }
};

/**
 * Order clipboard custom js module
 * @type {{run: customModule.ordersClipboard.run}}
 */
customModule.ordersClipboard = {
    run : function(params) {
        $(document).ready(function () {
            var ClipboardDemo = function () {
                var n = function n() {
                    new Clipboard("[data-clipboard=true]").on("success", function (n) {
                        n.clearSelection(), alert("Copied!");
                    });
                };return { init: function init() {
                    n();
                } };
            }();jQuery(document).ready(function () {
                ClipboardDemo.init();
            });
        });
    }
};