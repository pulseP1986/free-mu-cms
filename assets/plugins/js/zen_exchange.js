function zenExchange() {
    this.url = '';
    this.zen = 0;
    this.cred = 0;
    this.total = 0;
}

zenExchange.prototype.setUrl = function (data) {
    this.url = data;
}
zenExchange.prototype.saveSettings = function (form) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/save_settings",
        data: form.serialize(),
        success: function (data) {
            if (data.error) {
                noty($.parseJSON('{"text":"' + data.error + '","layout":"topRight","type":"error"}'));
            }
            else {
                noty($.parseJSON('{"text":"' + data.success + '","layout":"topRight","type":"success"}'));
            }
        }
    });
}
zenExchange.prototype.calculateCurrency = function (val, ratio) {
    this.zen = ratio.split("/")[0];
    this.cred = ratio.split("/")[1];
    if ((val.toString().search(/^-?[0-9]+$/) != 0)) {
        $('#zen').val('0');
        $('#game_currency').val('0');
        if (typeof $('#exchange_zen').attr("disabled") == 'undefined' || $('#exchange_zen').attr("disabled") == false) {
            $('#exchange_zen').attr("disabled", "disabled");
        }
    }
    else {
        if (val > 0) {
            this.total = Math.floor(val / this.zen) * this.cred;
            $('#game_currency').val(Math.floor(this.total));
            if (($('#game_currency').val() != 0)) {
                $('#exchange_zen').removeAttr("disabled");
            }
        }
        else {
            $('#game_currency').val(0);
            $('#exchange_zen').attr("disabled", "disabled");
        }
    }
}
zenExchange.prototype.submit = function (form) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/change_zen",
        data: form.serialize(),
        success: function (data) {
            if (data.error) {
                App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
            }
            else {
                App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
            }
        }
    });
}







