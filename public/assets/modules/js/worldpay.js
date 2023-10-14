    'use strict';
    var sessionIdStatus = false;
    var cardNumber = 0;

    function hiddenIframe(sanitizedCardValue) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = "https://secure-test.worldpay.com/shopper/3ds/ddc.html";
        form.setAttribute('target', 'myfram');
        let fields = [
            {
                name: 'Bin',
                value: sanitizedCardValue
            },
            {
                name: 'JWT',
                value: "{{$jwt}}"
            }
        ];

        for (let field in fields) {
            let input = document.createElement('input');
            input.name = fields[field].name;
            input.value = fields[field].value;
            input.setAttribute('type', 'hidden');
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }

    $(document).ready(function () {

        $('#wp_payment_form').on('submit', function (e) {
            var card = document.getElementById('cr_no');
            var sanitizedCardValue = card.value.replace(/[^0-9]/gi, '');
            console.log('Sanitize___', sanitizedCardValue, sessionIdStatus);
            if (sanitizedCardValue != cardNumber) {
                sessionIdStatus = false;
                cardNumber = sanitizedCardValue;
            }
            if (sanitizedCardValue.length < 6) {
                toastr.error('Please enter a valid card number', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
                return false;
            }
            if (sessionIdStatus) {
                console.log('Submit__');
                return true;
            } else {
                console.log('Prevent submit__');
                e.preventDefault();
                hiddenIframe(sanitizedCardValue);
                return false;
            }
            console.log('Nothing submit__');
        })
    });
    $(document).ready(function () {
        //For Card Number formatted input
        var cardNum = document.getElementById('cr_no');
        cardNum.onkeyup = function (e) {
            if (this.value == this.lastValue) return;
            var caretPosition = this.selectionStart;
            var sanitizedValue = this.value.replace(/[^0-9]/gi, '');
            var parts = [];

            for (var i = 0, len = sanitizedValue.length; i < len; i += 4) {
                parts.push(sanitizedValue.substring(i, i + 4));
            }

            for (var i = caretPosition - 1; i >= 0; i--) {
                var c = this.value[i];
                if (c < '0' || c > '9') {
                    caretPosition--;
                }
            }
            caretPosition += Math.floor(caretPosition / 4);

            this.value = this.lastValue = parts.join('-');
            this.selectionStart = this.selectionEnd = caretPosition;
        }

        //For Date formatted input
        var expDate = document.getElementById('exp');
        expDate.onkeyup = function (e) {
            if (this.value == this.lastValue) return;
            var caretPosition = this.selectionStart;
            var sanitizedValue = this.value.replace(/[^0-9]/gi, '');
            var parts = [];

            for (var i = 0, len = sanitizedValue.length; i < len; i += 2) {
                parts.push(sanitizedValue.substring(i, i + 2));
            }

            for (var i = caretPosition - 1; i >= 0; i--) {
                var c = this.value[i];
                if (c < '0' || c > '9') {
                    caretPosition--;
                }
            }
            caretPosition += Math.floor(caretPosition / 2);

            this.value = this.lastValue = parts.join('/');
            this.selectionStart = this.selectionEnd = caretPosition;
        };

        window.addEventListener("message", function (event) {
            var data = JSON.parse(event.data);
            if (data !== undefined && data.Status) {
                document.getElementById('sessionId').value = data.SessionId;
                sessionIdStatus = true;
                console.log('sessionCreated', document.getElementById('sessionId').value, data.SessionId);
                $('#wp_payment_form').submit();
            }
        }, false);
    })
