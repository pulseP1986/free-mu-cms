function pluginJs() {
    this.url = '';
    this.cred = 0;
    this.total = 0;
}

pluginJs.prototype.setUrl = function (data) {
    this.url = data;
}
pluginJs.prototype.saveSettings = function (form) {
	
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
pluginJs.prototype.submit = function (form) {
	if(App.isSending) 
		return false;
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/do-hide",
        data: {hide: 1},
		beforeSend: function () {
			App.showLoader();
			App.isSending = true;
		},
		complete: function () {
			App.hideLoader();
			App.setIsSending();
		},
        success: function (data) {
            if(data.error) {
                App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
            }
            else {
                App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
                $('#hide_time').text(data.hide_time);
            }
        }
    });
}







