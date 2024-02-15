

function achievements(){
	this.url = '';
}

achievements.prototype.setUrl = function(data){
	this.url = data;
}
achievements.prototype.saveSettings = function(form){
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
achievements.prototype.saveOrder = function(server){
	var order = $('#achievementlist_sortable_content').sortable('toArray');
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





