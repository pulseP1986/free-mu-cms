function workshop() {
    this.url = '';
	this.credits = 0;
	this.price = 0;
	this.item_category = 0
	this.lvl_price = 0;
	this.option_price = 0;
    this.current_lvl_price = 0;
	this.current_option_price = 0;
	this.current_lvl = 0;
	this.max_lvl = 0;
	this.current_option = 0;
	this.luck_price = 0;
	this.current_luck_price = 0;
	this.skill_price = 0;
	this.current_skill_price = 0;
	this.exe_price = 0;
	this.remove_exe_price = 0;
    this.current_exe_price = 0;
	this.options = 0;
	this.s10_opts = 0;
	this.s10_values = ['7','8','9','10'];
	this.exe_limit = 0;
	this.current_exe_opts = [];
	this.allow_equal_seeds = 0;
    this.allow_equal_sockets = 0;
	this.current_socket_opts = [];
	this.socket_price = [0, 0, 0, 0, 0, 0];
	this.seeds = [];
}

workshop.prototype.setUrl = function (data) {
    this.url = data;
}
workshop.prototype.getUrl = function () {
    return this.url;
}
workshop.prototype.saveSettings = function (form) {
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
workshop.prototype.set_category = function (data) {
    this.item_category = data;
}
workshop.prototype.set_price = function (data) {
    this.price = data;
}
workshop.prototype.set_lvl_price = function (data) {
    this.lvl_price = data;
}
workshop.prototype.set_current_lvl = function (data) {
    this.current_lvl = data;
}
workshop.prototype.set_max_lvl = function (data) {
    this.max_lvl = data;
}
workshop.prototype.set_option_price = function (data) {
    this.option_price = data;
}
workshop.prototype.set_current_option = function (data) {
    this.current_option = data;
}
workshop.prototype.set_luck_price = function (data) {
    this.luck_price = data;
}
workshop.prototype.set_skill_price = function (data) {
    this.skill_price = data;
}
workshop.prototype.set_exe_price = function (data) {
    this.exe_price = data;
}
workshop.prototype.set_remove_exe_price = function (data) {
    this.remove_exe_price = data;
}
workshop.prototype.set_exe_limit = function (data) {
    this.exe_limit = data;
}
workshop.prototype.set_current_exe_opts = function (data) {
    this.current_exe_opts = data;
}
workshop.prototype.equal_seeds = function (data) {
    this.allow_equal_seeds = data;
}
workshop.prototype.equal_sockets = function (data) {
    this.allow_equal_sockets = data;
}
workshop.prototype.set_current_socket_opts = function (data) {
    this.current_socket_opts = data;
}
workshop.prototype.upgradeItem = function (payment_method) {
	if (App.confirmMessage(App.lc.translate('Are you sure you want to upgrade this item?').fetch())) {
		if (App.checkUserStatus() == true) {
			if (App.checkCredits(payment_method, this.credits, 0) == true) {
				this.provideItem($('#item_form').serialize());
			}
		}
	}
}
workshop.prototype.provideItem = function (info) {
    $.ajax({
        url: this.url + '/upgrade_item',
        data: info + '&' + $.param({'ajax': 1, 'dmn_csrf_protection': DmNConfig.dmn_csrf_token}),
        beforeSend: function () {
            App.showLoader();
        },
        complete: function () {
            App.hideLoader();
        },
        success: function (data) {
            if (data.error) {
                if ($.isArray(data.error)) {
                    $.each(data.error, function (key, val) {
                        App.notice(App.lc.translate('Error').fetch(), 'error', val);
                    });
                }
                else {
                    App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
                }
            }
            else {
                App.closeBuyWindows(1);
                $('#item_content').dialog('destroy');
                var credits = $('#my_credits').text().replace(/,/g, ''),
                    gcredits = $('#my_gold_credits').text().replace(/,/g, '');
                if (data.payment_method == 1) {
                    $('#my_credits').fadeOut('slow').html(App.formatMoney(parseInt(credits) - parseInt(data.price), 0, ',', ',')).fadeIn('slow');
                }
                else {
                    $('#my_gold_credits').fadeOut('slow').html(App.formatMoney(parseInt(gcredits) - parseInt(data.price), 0, ',', ',')).fadeIn('slow');
                }
                App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
				var div = $("div").find("[data-info='" + data.old_hex + "']");
				div.removeAttr("data-info2");
				div.attr('data-info', data.new_hex);
            }
        }
    });
}
workshop.prototype.intializeCalculator = function () {
    var that = this;
	
	$('#lvl_add').on('click', function () {
		var next_lvl = parseInt($('#item_level').val()) + 1;
		
		if(next_lvl <= that.max_lvl){
			that.current_lvl_price = (next_lvl - that.current_lvl) * that.lvl_price;
			$('#lvl_display').text('+'+next_lvl);
			$('#item_level').val(next_lvl);
			that.setPricesHTML();
		}

	});
	$('#opt_add').on('click', function () {
		var next_opt = parseInt($('#item_opt').val()) + 1;
		var maxopt = parseInt($(this).data('max-option'));
		if(next_opt <= 7){
			that.current_option_price = (next_opt - that.current_option) * that.option_price;
			if(that.item_category == 13){
				$('#opt_display').text(next_opt+'%');
			}
			else{
				$('#opt_display').text('+'+(next_opt*((that.item_category == 6) ? 5 : 4)));
			}
			$('#item_opt').val(next_opt);
			that.setPricesHTML();
		}
	});
	
	$('#luck_add').on('click', function () {
		that.current_luck_price = that.luck_price;
		$('#item_luck').val(1);
		$("#luck_add").addClass("ok skew_active");
		$("#luck_value").text("Yes");
		that.setPricesHTML();
		
	});
	
	$('#skill_add').on('click', function () {
		that.current_skill_price = that.skill_price;
		$('#item_skill').val(1);
		$("#skill_add").addClass("ok skew_active");
		$("#skill_value").text("Yes");
		that.setPricesHTML();
		
	});
	$('input[id^="ex"]').each(function () {
        $(this).on('change', function () {
			var attrclass = $(this).attr('class');
			if(typeof attrclass == 'undefined'){
				attrclass = 'no_exe';
			}
			//alert(attrclass);
            if ($(this).is(':checked')) {
                that.checkExe($(this), true, attrclass);
            }
            else {
                that.checkExe($(this), false, attrclass);
            }
            that.setPricesHTML();
        });
    });
	
	$('select[id^="socket"]').each(function () {
        $(this).on('change', function () {
            var div = this;
            if ($("option:selected", this).length && $("option:selected", this).val() != 'no') {
                var opt = $("option:selected", this).val().split("-");
                $.ajax({
                    url: DmNConfig.base_url + 'shop/getsocketprice',
                    data: {
                        ajax: 1,
                        option: opt[1]
                    },
                    success: function (data) {
                        if (data.socket_price) {
                            that.socket_price[$(div).attr('id').charAt($(div).attr('id').length - 1)] = parseInt(data.socket_price);
                        }
                        else {
                            if (data.error) {
                                App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
                                that.socket_price[$(div).attr('id').charAt($(div).attr('id').length - 1)] = 0;
                            }
                        }
                        that.setPricesHTML(false, true);
                    }
                });
                that.removeSocketOpt(opt[0], $(this));
            }
        });
    });
    
}
workshop.prototype.checkExe = function(cur_opt, check, attrclass){
	if(check){
		if(this.s10_values.indexOf(cur_opt.val()) != -1){
			this.s10_opts = this.s10_opts + 1;
		}
		this.options = this.options + 1;
		if(attrclass == 'existing_exe'){
			this.current_exe_price = this.current_exe_price-this.remove_exe_price;
		}
		else{
			this.current_exe_price = this.current_exe_price+this.exe_price;
		}
	}
	else{
		if(this.s10_values.indexOf(cur_opt.val()) != -1){
			this.s10_opts = this.s10_opts - 1;
		}
		this.options = this.options - 1;
		if(attrclass == 'existing_exe'){
			this.current_exe_price = this.current_exe_price+this.remove_exe_price;
		}
		else{
			this.current_exe_price = this.current_exe_price-this.exe_price;
		}
	}
	if(this.options > this.exe_limit){
		App.notice(App.lc.translate('Error').fetch(), 'error', App.sprintf(App.lc.translate('You can have only %d excellent options per item.').fetch(), this.exe_limit));
		cur_opt.attr('checked', false);
		this.options = this.options - 1;
		this.current_exe_price = this.current_exe_price-this.exe_price;
	}
	if(this.s10_opts > 3){
		App.notice(App.lc.translate('Error').fetch(), 'error', App.sprintf(App.lc.translate('You can have only 3 Season 10+ excellent options per item.').fetch(), this.exe_limit));
		cur_opt.attr('checked', false);
		this.options = this.options - 1;
		this.s10_opts = this.s10_opts - 1;
		this.current_exe_price = this.current_exe_price-this.exe_price;
	}
}
workshop.prototype.setPricesHTML = function (h_price, s_price) {
    $('#credits_level').attr('aria-label', 'Cost: '+this.current_lvl_price+' Points');
    $('#credits_opt').attr('aria-label', 'Cost: '+this.current_option_price+' Points');
    $('#credits_luck').attr('aria-label', 'Cost: '+this.current_luck_price+' Points');
	$('#credits_skill').attr('aria-label', 'Cost: '+this.current_skill_price+' Points');
	
    this.credits = parseInt(this.price + this.current_lvl_price + this.current_option_price + this.current_luck_price + this.current_skill_price + this.current_exe_price+ this.socket_price[1] + this.socket_price[2] + this.socket_price[3] + this.socket_price[4] + this.socket_price[5]);
    $('#total_credits').html(this.credits);

}
workshop.prototype.removeSocketOpt = function (seed, socket) {
    var remove_socket = true;
    if ($("option:selected", socket).val().split('-')[1] != '254' && $("option:selected", socket).val() != 'no') {
        this.seeds[socket.attr('id')] = seed;
    }
    else {
        remove_socket = false;
        delete this.seeds[socket.attr('id')];
    }

    $('option[id^="socket"]').show().prop('disabled', false);

    if (this.allow_equal_seeds == 0) {
        if (this.seeds.socket1 !== undefined) {
            $('option[id^="socket2-seed-' + this.seeds.socket1 + '-"]').prop('disabled', true);
            $('option[id^="socket3-seed-' + this.seeds.socket1 + '-"]').prop('disabled', true);
            $('option[id^="socket4-seed-' + this.seeds.socket1 + '-"]').prop('disabled', true);
            $('option[id^="socket5-seed-' + this.seeds.socket1 + '-"]').prop('disabled', true);
        }
        if (this.seeds.socket2 !== undefined) {
            $('option[id^="socket1-seed-' + this.seeds.socket2 + '-"]').prop('disabled', true);
            $('option[id^="socket3-seed-' + this.seeds.socket2 + '-"]').prop('disabled', true);
            $('option[id^="socket4-seed-' + this.seeds.socket2 + '-"]').prop('disabled', true);
            $('option[id^="socket5-seed-' + this.seeds.socket2 + '-"]').prop('disabled', true);
        }
        if (this.seeds.socket3 !== undefined) {
            $('option[id^="socket1-seed-' + this.seeds.socket3 + '-"]').prop('disabled', true);
            $('option[id^="socket2-seed-' + this.seeds.socket3 + '-"]').prop('disabled', true);
            $('option[id^="socket4-seed-' + this.seeds.socket3 + '-"]').prop('disabled', true);
            $('option[id^="socket5-seed-' + this.seeds.socket3 + '-"]').prop('disabled', true);
        }
        if (this.seeds.socket4 !== undefined) {
            $('option[id^="socket1-seed-' + this.seeds.socket4 + '-"]').prop('disabled', true);
            $('option[id^="socket2-seed-' + this.seeds.socket4 + '-"]').prop('disabled', true);
            $('option[id^="socket3-seed-' + this.seeds.socket4 + '-"]').prop('disabled', true);
            $('option[id^="socket5-seed-' + this.seeds.socket4 + '-"]').prop('disabled', true);
        }
        if (this.seeds.socket5 !== undefined) {
            $('option[id^="socket1-seed-' + this.seeds.socket5 + '-"]').prop('disabled', true);
            $('option[id^="socket2-seed-' + this.seeds.socket5 + '-"]').prop('disabled', true);
            $('option[id^="socket3-seed-' + this.seeds.socket5 + '-"]').prop('disabled', true);
            $('option[id^="socket4-seed-' + this.seeds.socket5 + '-"]').prop('disabled', true);
        }
    }
    else {
        if (this.allow_equal_sockets == 0) {
            if (remove_socket == true) {
                var arr = $.map(
                    $('select[id^="socket"] option:selected'), function (n) {
                        return n.value;
                    }
                );

                $('select[id^="socket"] option').filter(function () {
                    return $.inArray($(this).val(), arr) >= 1;
                }).hide();
            }
        }
    }
}





