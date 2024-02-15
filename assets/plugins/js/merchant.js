function Merchant() {
    this.url = '';
	this.wcoin = 0;
    this.currency = 0;
	this.currency_required_for_bonus = 0;
	this.wcoin_bonus = 0;
	this.wcoin_bonus_total = 0;
	this.currency_required_for_web_bonus = 0;
	this.webcurrency_bonus = 0;
}
Merchant.prototype.setUrl = function (data) {
    this.url = data;
}
Merchant.prototype.setRequirementForBonus = function (data) {
    this.currency_required_for_bonus = data;
}
Merchant.prototype.setWcoinBonus = function (data) {
    this.wcoin_bonus = data;
}
Merchant.prototype.setWcoinBonusTotal = function (data) {
    this.wcoin_bonus_total = data;
}
Merchant.prototype.setRequirementForWebCurrencyBonus = function (data) {
    this.currency_required_for_web_bonus = data;
}
Merchant.prototype.setWebCurrencyBonus = function (data) {
    this.webcurrency_bonus = data;
}
Merchant.prototype.saveSettings = function (form) {
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
Merchant.prototype.add = function () {
	var wallet = isNaN(parseInt($("#merchant_wallet_new").val())) ? 0 : parseInt($("#merchant_wallet_new").val());
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/add_merchant",
        data: {
            account: $("#merchant_account_new").val(),
            name: $("#merchant_name_new").val(),
            contact: $("#merchant_contact_new").val(),
			wallet: wallet,
            server: $("#server_new").val()
        },
        success: function (data) {
            if (data.error) {
                noty($.parseJSON('{"text":"' + data.error + '","layout":"topRight","type":"error"}'));
            }
            else {
                noty($.parseJSON('{"text":"' + data.success + '","layout":"topRight","type":"success"}'));
                var html = '<tr id="' + data.id + '">';
				html += '<td><input class="input-medium" type="text" id="merchant_account_' + data.id + '" value="' + $("#merchant_account_new").val() + '" /></td>';
                html += '<td class="center"><input class="input-medium" type="text" id="merchant_name_' + data.id + '" value="' + $("#merchant_name_new").val() + '" /></td>';
                html += '<td class="center"><input class="input-medium" type="text" id="merchant_contact_' + data.id + '" value="' + $("#merchant_contact_new").val() + '" /></td>';
                html += '<td class="center"><input class="input-small" type="text" id="merchant_wallet_' + data.id + '" value="' + wallet + '" /></td>';
                html += '<td class="center"><select id="merchant_server_' + data.id + '" class="input-medium">';
                $.each(data.servers, function (key, val) {
                    var selected = (key == data.server) ? 'selected="selected"' : '';
                    html += '<option value="' + key + '" ' + selected + '>' + val.title + '</option>';
                });
                html += '</select></td>';
                html += '<td class="center" id="status_icon_' + data.id + '"><span class="label label-success">Active</span></td><td class="center"><a class="btn btn-danger" href="#" id="status_button_' + data.id + '" onclick="Merchant.changeStatus(' + data.id + ', 0);"><i class="icon-edit icon-white"></i> Disable</a>  <a class="btn btn-info" href="#" onclick="Merchant.edit(' + data.id + ');"><i class="icon-edit icon-white"></i> Edit</a>  <a class="btn btn-danger" href="#" onclick="Merchant.delete(' + data.id + ');"><i class="icon-trash icon-white"></i> Delete</a></td></tr>';
                $('#donate_sortable_merchant > tbody:last').append(html);
            }
        }
    });
}
Merchant.prototype.edit = function (id) {
	var wallet = isNaN(parseInt($("#merchant_wallet_" + id).val())) ? 0 : parseInt($("#merchant_wallet_" + id).val());
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/edit_merchant",
        data: {
            id: id,
			account: $("#merchant_account_" + id).val(),
            name: $("#merchant_name_" + id).val(),
            contact: $("#merchant_contact_" + id).val(),
			wallet: wallet,
            server: $("#merchant_server_" + id).val()
        },
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

Merchant.prototype.delete = function (id) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/delete_merchant",
        data: {
            id: id
        },
        success: function (data) {
            if (data.error) {
                noty($.parseJSON('{"text":"' + data.error + '","layout":"topRight","type":"error"}'));
            }
            else {
                noty($.parseJSON('{"text":"' + data.success + '","layout":"topRight","type":"success"}'));
                $('tr#' + id).hide();
            }
        }
    });
}
Merchant.prototype.changeStatus = function (id, status) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/change_status",
        data: {
            id: id,
            status: status
        },
        success: function (data) {
            if (data.error) {
                noty($.parseJSON('{"text":"' + data.error + '","layout":"topRight","type":"error"}'));
            }
            else {
                noty($.parseJSON('{"text":"' + data.success + '","layout":"topRight","type":"success"}'));
                if (status == 1) {
                    $('#status_icon_' + id).html('<span class="label label-success">Active</span>');
                    $('#status_button_' + id).attr({'class': 'btn btn-danger'});
                    $('#status_button_' + id).html('<i class="icon-edit icon-white"></i> Disable');
                    $('#status_button_' + id).attr('onclick', 'Merchant.changeStatus(' + id + ', 0);');
                }
                else {
                    $('#status_icon_' + id).html('<span class="label label-important">Inactive</span>');
                    $('#status_button_' + id).attr({'class': 'btn btn-success'});
                    $('#status_button_' + id).html('<i class="icon-edit icon-white"></i> Enable');
                    $('#status_button_' + id).attr('onclick', 'Merchant.changeStatus(' + id + ', 1);');
                }
            }
        }
    });
}
Merchant.prototype.calculateCurrency = function (val, ratio) {
	var bonus_times = Math.floor(val/this.currency_required_for_bonus, 1);
	var bonus_times_web = Math.floor(val/this.currency_required_for_web_bonus, 1);
	var bonus = bonus_times*this.wcoin_bonus;
	var bonus_web = bonus_times_web*this.webcurrency_bonus;
	if((isFinite(bonus) != true) || isNaN(bonus)){
		bonus = 0;
	}
	if((isFinite(bonus_web) != true) || isNaN(bonus_web)){
		bonus_web = 0;
	}
    this.currency = ratio.split("/")[0];
    this.wcoin = ratio.split("/")[1];
    if ((val.toString().search(/^-?[0-9]+$/) != 0)) {
        $('#credits').val('0');
        $('#game_currency').val('0');
        if (typeof $('#exchange_currency').attr("disabled") == 'undefined' || $('#exchange_ruud').attr("disabled") == false) {
            $('#exchange_currency').attr("disabled", "disabled");
        }
    }
    else {
        if (val > 0) {
            this.total = Math.floor(val / this.currency) * this.wcoin;
			var total_bonus = 0;
			if(this.wcoin_bonus_total != 0){
				total_bonus = parseInt((this.wcoin_bonus_total / 100) * this.total);
			}
            $('#game_currency').val(Math.floor(this.total+total_bonus+bonus));
			$('#web_currency').val(Math.floor(bonus_web));
            if (($('#game_currency').val() != 0)) {
                $('#exchange_currency').removeAttr("disabled");
            }
        }
        else {
            $('#game_currency').val(0);
            $('#exchange_ruud').attr("disabled", "disabled");
        }
    }
}
Merchant.prototype.submitExchange = function (form) {
	var balance = parseInt($('#merchant_balance').text());
	var amount = parseInt($('#credits').val());
    $.ajax({
        type: "post",
        dataType: "json",
        url: this.url + "/change_currency",
        data: form.serialize(),
        success: function(data) {
            if (data.error) {
                App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
            }
            else {
                App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
				$('#currency_exchange_form').trigger("reset");
				$('#merchant_balance').text(balance-amount);
            }
        }
    });
}






