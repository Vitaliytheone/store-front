// TODO:: Convert scripts to Custom module after developing is finished


/*****************************************************************************************************
 *                     Nestable menu items
 *****************************************************************************************************/
(function (window, alert){

    var updateOutput = function updateOutput(e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            console.log('Ok');
        } else {
            output.html('JSON browser support required for this demo.');
        }
    };
    if ($('#nestable').length > 0) {

        $('#nestable').nestable({
            group: 0,
            maxDepth: 3
        }).on('change', updateOutput);
        updateOutput($('#nestable').data('output', $('#nestable-output')));
    }

})({}, function (){});


/*****************************************************************************************************
 *                          Create/Edit menu item
 *****************************************************************************************************/
(function (window, alert){

    var $modal = $('.edit_navigation'),
        $navForm = $('#navForm'),
        $submit = $navForm.find('submit'),
        $modalTitle = $navForm.find('.modal-title'),
        $errorContainer = $navForm.find('.form-error'),
        $modalLoader = $modal.find('.modal-loader');
    
    var $formFields = {
        name         : $navForm.find('.form_field__name'),
        link         : $navForm.find('.form_field__link'),
        link_id      : $navForm.find('.form_field__link_id'),
        url          : $navForm.find('.form_field__url')
    };

    var defaultFormData = {
        name         : $formFields.name.val(),
        link         : $formFields.link.val(),
        link_id      : $formFields.link_id.val(),
        url          : $formFields.url.val()
    };


    /**
     *  Fill form fields by data
     * @param formData
     */
    function fillFormFields(formData){
        if (formData === undefined || !_.isObject(formData)){
            return;
        }
        _.each(formData, function(fieldValue, formField, list){
            if (!_.has($formFields, formField)) {
                return;
            }
            $formFields[formField].val(fieldValue);
        });
    }


    function resetForm() {
        $errorContainer.empty();
        fillFormFields(defaultFormData);
    }
    


    $('#select-menu-link').change(function () {
        $('.hide-link').hide();

        var linkType = $("#select-menu-link option:selected").val(),
            mergedLinkTypes = [2, 3];

        linkType = mergedLinkTypes.indexOf(linkType|0) !== -1 ? mergedLinkTypes.join('') : linkType;

        $('.link-' + linkType).fadeIn();
    });

    /**
     * Modal events
     */
    $modal.on('hidden.bs.modal', function (){

        resetForm();
    });

    $modal.on('show.bs.modal', function (event){
        $modalLoader.removeClass('hidden');

    });

    $modal.on('shown.bs.modal', function (event){
        $modalLoader.addClass('hidden');

    });



})({}, function (){});




