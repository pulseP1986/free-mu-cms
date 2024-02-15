

function nganLuong(){
	this.url = '';
}

nganLuong.prototype.setUrl = function(data){
	this.url = data;
}
nganLuong.prototype.saveSettings = function(form){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/save_settings",
		data: form.serialize(),
		success: function(data){
			if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));
			}
			else{
				noty($.parseJSON('{"text":"'+data.success+'","layout":"topRight","type":"success"}'));
			}
		}
	});
}
nganLuong.prototype.addPackage = function(){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/add_package",
		data: {
			title: $("#title_new").val(),
			price: $("#price_new").val(),
			currency: $("#currency_new").val(),
			reward: $("#reward_new").val(),
			server: $("#server_new").val()
		},
		success: function( data ){
			if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));
			}
			else{
				noty($.parseJSON('{"text":"'+data.success+'","layout":"topRight","type":"success"}'));
				var html = '<tr id="'+data.id+'"><td><input class="input-medium" type="text" id="pack_title_'+data.id+'" value="'+$("#title_new").val()+'" /></td>';
					html += '<td class="center"><input class="input-small" type="text" id="pack_price_'+data.id+'" value="'+$("#price_new").val()+'" /></td>';
					html += '<td class="center"><input class="input-small" type="text" id="pack_currency_'+data.id+'" value="'+$("#currency_new").val()+'" /></td>';
					html += '<td class="center"><input class="input-small" type="text" id="pack_reward_'+data.id+'" value="'+$("#reward_new").val()+'" /></td>';
					html += '<td class="center"><select id="pack_server_'+data.id+'" class="input-medium">';
				$.each(data.servers, function(key, val){
					var selected = (key == data.server) ? 'selected="selected"' : '';
					html += '<option value="'+key+'" '+selected+'>'+val.title+'</option>';
				});
				html += '</select></td>';
				html += '<td class="center" id="status_icon_'+data.id+'"><span class="label label-success">Active</span></td><td class="center"><a class="btn btn-danger" href="#" id="status_button_'+data.id+'" onclick="nganLuong.changeStatus('+data.id+', 0);"><i class="icon-edit icon-white"></i> Disable</a>  <a class="btn btn-info" href="#" onclick="nganLuong.edit('+data.id+');"><i class="icon-edit icon-white"></i> Edit</a>  <a class="btn btn-danger" href="#" onclick="nganLuong.delete('+data.id+');"><i class="icon-trash icon-white"></i> Delete</a></td></tr>';
				$('#donate_sortable > tbody:last').append(html);
			}
		}
	});
}
nganLuong.prototype.edit = function(id){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/edit_package",
		data: {
			id: id,
			title: $("#pack_title_"+id).val(),
			price: $("#pack_price_"+id).val(),
			currency: $("#pack_currency_"+id).val(),
			reward: $("#pack_reward_"+id).val(),
			server: $("#pack_server_"+id).val()
		},
		success: function(data){
			if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));
			}
			else{
				noty($.parseJSON('{"text":"'+data.success+'","layout":"topRight","type":"success"}'));
			}
		}
	});
}
nganLuong.prototype.delete = function(id){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/delete_package",
		data: {
			id: id
		},
		success: function(data ){
			if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));
			}
			else{
				noty($.parseJSON('{"text":"'+data.success+'","layout":"topRight","type":"success"}'));
				$('tr#'+id).hide();
			}
		}
	});
}
nganLuong.prototype.changeStatus = function(id, status){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/change_status",
		data: {
			id: id,
			status: status
		},
		success: function( data ){
			if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));
			}
			else{
				noty($.parseJSON('{"text":"'+data.success+'","layout":"topRight","type":"success"}'));
				if(status == 1){
					$('#status_icon_'+id).html('<span class="label label-success">Active</span>');
					$('#status_button_'+id).attr({'class':'btn btn-danger'});
					$('#status_button_'+id).html('<i class="icon-edit icon-white"></i> Disable');
					$('#status_button_'+id).attr('onclick', 'nganLuong.changeStatus('+id+', 0);');
				}
				else{
					$('#status_icon_'+id).html('<span class="label label-important">Inactive</span>');
					$('#status_button_'+id).attr({'class':'btn btn-success'});
					$('#status_button_'+id).html('<i class="icon-edit icon-white"></i> Enable');
					$('#status_button_'+id).attr('onclick', 'nganLuong.changeStatus('+id+', 1);');
				}
			}
		}
	});
}
nganLuong.prototype.saveOrder = function(){
	var order = $('#donate_sortable_content').sortable('toArray');
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/save_order",
		data: {
			order: order
		},
		success: function(data){}
	});
}
nganLuong.prototype.checkout = function (id) {
	$.ajax({
		url:  this.url+"/checkout",
		data: {id: id},
		success: function (data) {
			if (data.error) {
				App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
			}
			else {
				window.location.href = data.redirect_url;
			}
		}
	});
}






