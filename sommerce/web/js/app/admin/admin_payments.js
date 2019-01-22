/**
 * /admin/settings/payments custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPayments = {
    run: function (params) {
        /******************************************************************
         *            Toggle `payment method` active status
         ******************************************************************/
        $(document).on('change', '.toggle-active', function (e) {
            var $checkbox = $(e.currentTarget),
                actionUrl = $checkbox.data('action_url'),
                method = $checkbox.data('payment_method'),
                active = $checkbox.prop('checked') | 0;

            if (active == true) {
                $('#met-'+method).removeClass('text-muted');
            } else {
                $('#met-'+method).addClass('text-muted');
            }

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    active: active
                },
                success: function (data, textStatus, jqXHR) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error on update', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $('.add-method').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#addPaymentMethodModal');
            var form = $('#addPaymentMethodForm');
            var errorBlock = $('#addPaymentMethodError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#addPaymentMethodButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#addPaymentMethodForm');
            var errorBlock = $('#addPaymentMethodError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#editCustomerModal').modal('hide');
                        location.reload();
                    }

                    if ('error' == response.status) {
                        errorBlock.removeClass('hidden');
                        errorBlock.html(response.error);
                    }
                }
            });

            return false;
        });

        $(document).on('click', '.add-multi-input', function(e) {
            e.preventDefault();

            var link = $(this);
            var elementClass = link.data('class');
            var container = $('#multi_input_container_descriptions');
            var label = link.data('label');
            var elementName = link.data('name');
            var elementId = link.data('id');

            if (!container.length) {
                return false;
            }

            var inputTemplate = templates['admin/payments'];
            container.append(inputTemplate({
                "elementId":elementId,
                "label":label,
                "elementClass":elementClass,
                "elementName":elementName
            }));

            return false;
        });

        $(document).on('click', '.remove-description', function(e) {
            e.preventDefault();

            var element = $(this);

            element.parents('.form-group-description').remove();

            return false;
        });

        $('#editPaymentMethodOptions textarea').summernote({
            dialogsInBody: true,
            minHeight: 200,
            toolbar: [
                ['style', ['style', 'bold', 'italic']],
                ['lists', ['ul', 'ol']],
                ['para', ['paragraph']],
                ['color', ['color']],
                ['insert', ['link', 'picture', 'video']],
                ['codeview', ['codeview']]
            ],
            disableDragAndDrop: true,
            styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
            popover: {
                image: [
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ]
            },
            dialogsFade: true
        });

        /*****************************************************************************************************
         *                     Update payment position
         *****************************************************************************************************/
        (function (window, alert){

            var updatePositionUrl = params.action_update_pos;

            var $neatable = $('#nestable');
            if ($neatable.length > 0) {

                $neatable.nestable({
                    maxDepth: 1,
                    handleClass: 'dd-handle-pay',
                }).on('change', updater);

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
    }
};