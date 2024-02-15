

function battlePass(){
	this.url = '';
}

battlePass.prototype.setUrl = function(data){
	this.url = data;
}
battlePass.prototype.saveSettings = function(form){
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
battlePass.prototype.saveOrder = function(server){
	var order = $('#battle_pass_sortable_content').sortable('toArray');
	$.ajax({
		type: "post",
		dataType: "json",
		url: this.url+"/save_order/"+server,
		data: {
			order: order
		},
		success: function(data) {
			if (data.error) {
				noty($.parseJSON('{"text":"' + data.error + '","layout":"topRight","type":"error"}'));
			}
		}
	});
}
battlePass.prototype.saveRewardOrder = function(bid, server){
	var order = $('#battle_pass_sortable_content').sortable('toArray');
	$.ajax({
		type: "post",
		dataType: "json",
		url: this.url+"/save_reward_order/"+bid+"/"+server,
		data: {
			order: order
		},
		success: function(data) {
			if (data.error) {
				noty($.parseJSON('{"text":"' + data.error + '","layout":"topRight","type":"error"}'));
			}
		}
	});
}







