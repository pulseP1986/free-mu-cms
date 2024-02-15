

function statsSpecialization(){
	this.url = '';
}

statsSpecialization.prototype.setUrl = function(data){
	this.url = data;
}
statsSpecialization.prototype.saveSettings = function(form){
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
statsSpecialization.prototype.remove = function(name, char_id, id){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/remove",
		data: {name: name, char_id: char_id, id: id},
		success: function(data){
			if (data.error) {
				App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
			}
			else {
				App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
				$('#'+id).remove();
			}
		}
	});
}
statsSpecialization.prototype.load = function(name, char_id, id){
	$.ajax({
		type: "post",
		dataType: "json",
		url:  this.url+"/load",
		data: {name: name, char_id: char_id, id: id},
		success: function(data){
			if (data.error) {
				App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
			}
			else {
				App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
				setTimeout(function(){ 
					location.reload(); 
				}, 1000);
			}
		}
	});
}






