require(
    [
        'jquery',
        'jquery/ui',
        'Magento_Ui/js/modal/modal'
    ],
    function ($) {
        $(document).ready(function () {
            $('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline legend.admin__legend.legend').append(' <span class="package-down-arrow">&#9660;</span><span class="package-up-arrow">&#9650;</span><span class="package-delete">&#10006;</span>');
            $('#infomodus_upslabel_items_edit_tabs_package_section').append('<button type="button" class="scalable primary add-package">Add new package</button>');
            var package0 = $('#item_package_fieldset_0_').clone(true);
            $('#infomodus_upslabel_items_edit_tabs_package_section').on('click', 'button.add-package', function () {
                var countFieldSet = $('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline fieldset').length, i = 0;
                for (i = 0; i < 200; i++) {
                    if ($('#item_package_fieldset_' + countFieldSet + '_').size() == 0) {
                        break;
                    }
                    countFieldSet++;
                }
                var packageCurrent = package0.clone(true);
                var legend = packageCurrent.find('legend span:first-child');
                var label = legend.text().split(' ');
                label.splice(label.length-1, 1);
                console.log(label);
                label = label.join(' ');
                legend.text(label+" "+(countFieldSet + 1));
                packageCurrent.attr('id', "item_package_fieldset_" + countFieldSet + "_");
                packageCurrent.html(packageCurrent.html().replace(/_0_/g, '_' + countFieldSet + '_').replace(/-0\"/g, '-' + countFieldSet + '"'));
                $('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline legend.admin__legend.legend .package-up-arrow').trigger('click');
                packageCurrent.appendTo($('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline'));
            });

            $('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline').on('click', '.package-up-arrow', function () {
                $(this).closest('fieldset').find('div.admin__field').hide();
                $(this).closest('fieldset').find('.package-up-arrow').hide();
                $(this).closest('fieldset').find('.package-down-arrow').show();
                $(this).closest('fieldset').addClass('package-collapsed');
            });
            $('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline').on('click', '.package-down-arrow', function () {
                $(this).closest('fieldset').find('div.admin__field').show();
                $(this).closest('fieldset').find('.package-down-arrow').hide();
                $(this).closest('fieldset').find('.package-up-arrow').show();
                $(this).closest('fieldset').removeClass('package-collapsed');
            });
            $('#infomodus_upslabel_items_edit_tabs_package_section_content .entry-edit.form-inline').on('click', '.package-delete', function () {
                $(this).closest('fieldset').remove();
            });

            $('#edit_form').on('change', '.box-selected', function(){
                if($(this).val().length > 1) {
                    var dimensions = $(this).val().split('x');
                    $(this).closest('fieldset').find('.box-width').val(dimensions[0]);
                    $(this).closest('fieldset').find('.box-height').val(dimensions[1]);
                    $(this).closest('fieldset').find('.box-length').val(dimensions[2]);
                } else {
                    $(this).closest('fieldset').find('.box-width').val('');
                    $(this).closest('fieldset').find('.box-height').val('');
                    $(this).closest('fieldset').find('.box-length').val('');
                }
            });
        });
        function getLabelPrice(href) {
            var formData = $('#edit_form').serialize(), obj = {};
            /*formData.forEach(function (item, i, arr) {
                if(obj[item.name]){

                } else {
                obj[item.name] = item.value;
                }
            });*/
            $.ajax({
                url: href,
                data: formData,
                type: 'post',
                dataType: 'text',
                showLoader: true,
                context: $('#edit_form'),
                traditional: true
            }).done(function(data){
                $('<div />').html(data)
                    .modal({
                        title: 'Price',
                        autoOpen: true,
                    });
            });
        }

        window.getLabelPrice = getLabelPrice;
    });