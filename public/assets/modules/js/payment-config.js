'use strict';
$(document).on('change', 'input[name="gateway_image"]', function () {
    var $input = $(this);
    var $form = $input.closest('form');
    var gatewayName = $form.attr('id');

    if (this.files && this.files[0]) {
        var reader = new FileReader();
        var $imagePreview = $form.find('.payment--gateway-img img'); // Find the img element within the form

        reader.onload = function (e) {
            $imagePreview.attr('src', e.target.result);
        }

        reader.readAsDataURL(this.files[0]);
    }
});
