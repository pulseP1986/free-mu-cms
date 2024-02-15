function creditsExchange() {
    this.url = '';
    this.zen = 0;
    this.cred = 0;
    this.total = 0;
}

creditsExchange.prototype.setUrl = function (data) {
    this.url = data;
}
creditsExchange.prototype.saveSettings = function (form) {
	
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
creditsExchange.prototype.calculateCurrency = function (val, ratio) {
	this.cred = ratio.split("/")[0];
    this.cred2 = ratio.split("/")[1];
    if ((val.toString().search(/^-?[0-9]+$/) != 0)) {
        $('#game_currency').val('0');
        $('#cred2').val('0');
        if (typeof $('#exchange_credits').attr("disabled") == 'undefined' || $('#exchange_credits').attr("disabled") == false) {
            $('#exchange_credits').attr("disabled", "disabled");
        }
    }
    else {
        if (val > 0) {
            this.total = Math.floor(val / this.cred) * this.cred2;
            $('#cred2').val(Math.floor(this.total));
            if (($('#cred2').val() != 0)) {
                $('#exchange_credits').removeAttr("disabled");
            }
        }
        else {
            $('#cred2').val(0);
            $('#exchange_credits').attr("disabled", "disabled");
        }
    }
}
creditsExchange.prototype.submit = function (form) {
	if(App.isSending) 
		return false;
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/change_credits",
        data: form.serialize(),
		beforeSend: function () {
			App.showLoader();
			App.isSending = true;
		},
		complete: function () {
			App.hideLoader();
			App.setIsSending();
		},
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







