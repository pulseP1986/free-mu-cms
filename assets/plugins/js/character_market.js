

function characterMarket(){
	this.url = '';
}

characterMarket.prototype.setUrl = function(data){
	this.url = data;
}
characterMarket.prototype.saveSettings = function(form){
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





