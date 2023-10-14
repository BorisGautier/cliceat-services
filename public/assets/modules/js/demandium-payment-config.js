'use strict';
// Function to update the image preview
function readURL(input, gatewayName) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + gatewayName + '-image-preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Trigger the image preview when a file input changes
$(document).on('change', 'input[name="gateway_image"]', function () {
    var gatewayName = $(this).attr('id').replace('-image', '');
    readURL(this, gatewayName);
});
