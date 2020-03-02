/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

$(function () {
    // Conversion API
    var methods = [];
    $.conversionAdd = function (name, callback) {
        methods[name] = callback;
    };

    // Callback for Conversion API
    $.nette.ext({
        success: function (payload) {
            var conversion = payload.conversion;

            if(typeof conversion !== 'undefined') {
                methods[conversion.name](conversion);
            }
        }
    });

    // Token setup
    var $input_token = $('input[name="token"]');
    var $input_token_check = $('input[name="tokenCheck"]');

    var FORM_TIMEOUT_SEC = parseInt($input_token.data('token-timeout-sec'));
    var FORM_TRUE_TOKEN = $input_token.data('token');

    setTimeout(function () {

        $input_token.each(function () {
            $(this).val(FORM_TRUE_TOKEN);
        });

        $input_token_check.each(function () {
            $(this).val('');
        });

    }, FORM_TIMEOUT_SEC * 1000);
});
