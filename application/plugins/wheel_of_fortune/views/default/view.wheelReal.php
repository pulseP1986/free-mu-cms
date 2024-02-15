<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<style>
span{
		display: inline-block;
}
.tbl-battlepass tr{
	height: 35px !important;
}

</style>

<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-content">
			<div class="row">
				<div class="col-12">   
					<?php 
					if(isset($config_not_found)){
						echo '<div class="alert alert-danger" role="alert">'.$config_not_found.'</div>';
					} 
					else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} 
						else{
					?>	
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/wheel.css">
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
					<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
					<div style="width: 100%;margin:0 auto;">
					<script type="text/javascript">
						//animatecase();
						//generateitem();
						function generateitem(){
							var itemlist = [
							  {
								"id": 1,
								"identifier": 1,
								"title": "x10 Three Vacancies ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 2,
								"rate": "12,0%",
								"start_break": 1,
								"end_break": 1200000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 13,
								"index": 510,
								"type_sec": 1
							  },
							  {
								"id": 2,
								"identifier": 1,
								"title": "x4 Guardian Enhanced Stone ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 3,
								"rate": "12,0%",
								"start_break": 1200001,
								"end_break": 2400000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 467,
								"type_sec": 1
							  },
							  {
								"id": 3,
								"identifier": 1,
								"title": "x25 Jewel of Bless ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 4,
								"rate": "12,0%",
								"start_break": 2400001,
								"end_break": 3600000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 13,
								"type_sec": 1
							  },
							  {
								"id": 4,
								"identifier": 1,
								"title": "x25 Jewel of Soul ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 5,
								"rate": "12,0%",
								"start_break": 3600001,
								"end_break": 4800000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 14,
								"type_sec": 1
							  },
							  {
								"id": 5,
								"identifier": 1,
								"title": "x50 Jewel of Bless ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 6,
								"rate": "8,0%",
								"start_break": 4800001,
								"end_break": 5600000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 13,
								"type_sec": 1
							  },
							  {
								"id": 6,
								"identifier": 1,
								"title": "x50 Jewel of Soul ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 7,
								"rate": "8,0%",
								"start_break": 5600001,
								"end_break": 6400000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 14,
								"type_sec": 1
							  },
							  {
								"id": 7,
								"identifier": 1,
								"title": "x40 Three Vacancies ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 9,
								"rate": "8,0%",
								"start_break": 6400001,
								"end_break": 7200000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 13,
								"index": 510,
								"type_sec": 1
							  },
							  {
								"id": 8,
								"identifier": 1,
								"title": "x10 Guardian Enhanced Stone ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 8,
								"rate": "8,0%",
								"start_break": 7200001,
								"end_break": 8000000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 467,
								"type_sec": 1
							  },
							  {
								"id": 9,
								"identifier": 1,
								"title": "x10 Bless of Light (Greater) ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 10,
								"rate": "7,0%",
								"start_break": 8000001,
								"end_break": 8700000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 224,
								"type_sec": 1
							  },
							  {
								"id": 10,
								"identifier": 1,
								"title": "x5 Talisman of Chaos Assembly ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 5,
								"item": 11,
								"rate": "3,0%",
								"start_break": 8700001,
								"end_break": 9000000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 14,
								"index": 96,
								"type_sec": 1
							  },
							  {
								"id": 17,
								"identifier": 1,
								"title": "2 Mystery Keys",
								"status": 1,
								"server": "DEFAULT",
								"type": 0,
								"amount": 2,
								"item": 13,
								"rate": "3,0%",
								"start_break": 9000001,
								"end_break": 9300000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 30,
								"index": 2,
								"type_sec": 3
							  },
							  {
								"id": 11,
								"identifier": 1,
								"title": "5000 Goblin Points",
								"status": 1,
								"server": "DEFAULT",
								"type": 0,
								"amount": 5000,
								"item": 12,
								"rate": "3,0%",
								"start_break": 9300001,
								"end_break": 9600000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 30,
								"index": 0,
								"type_sec": 2
							  },
							  {
								"id": 12,
								"identifier": 1,
								"title": "x1 Shining Tail Seal ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 15,
								"rate": "1,0%",
								"start_break": 9600001,
								"end_break": 9700000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 20,
								"index": 84,
								"type_sec": 1
							  },
							  {
								"id": 13,
								"identifier": 1,
								"title": "x1 Manticore Anvil ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 16,
								"rate": "1,0%",
								"start_break": 9700001,
								"end_break": 9800000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 12,
								"index": 82,
								"type_sec": 1
							  },
							  {
								"id": 14,
								"identifier": 1,
								"title": "x1 Brilliant Soul ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 17,
								"rate": "1,0%",
								"start_break": 9800001,
								"end_break": 9900000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 12,
								"index": 81,
								"type_sec": 1
							  },
							  {
								"id": 16,
								"identifier": 1,
								"title": "x1 Jewel of Luck ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 19,
								"rate": "0,5%",
								"start_break": 9900001,
								"end_break": 9950000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 20,
								"index": 504,
								"type_sec": 1
							  },
							  {
								"id": 15,
								"identifier": 1,
								"title": "x1 Jewel of Excess ",
								"status": 1,
								"server": "DEFAULT",
								"type": 1,
								"amount": 1,
								"item": 18,
								"rate": "0,5%",
								"start_break": 9950001,
								"end_break": 10000000,
								"unique_1": 0,
								"unique_2": 0,
								"unique_3": 0,
								"group": 20,
								"index": 503,
								"type_sec": 1
							  }
							];
							var total = itemlist.length;
							var rand = 0;
							var items = [];
							$(".scrolled").css("opacity", "0");
							var item = -210;
							var min = 1; 
							var max = 14;
							$(".scrolled .item").html("<div class='before'></div>");

							for(var i = 0; i < 30; i++){
								var br = '';
								var title = '';
								var molde = 'molde-a';
								rand = Math.floor(Math.random() * total);
								title = itemlist[rand]['title'];
								title = title.substr(0,25);
								if (itemlist[rand]['start_break'] >= 1 && itemlist[rand]['end_break'] <= 4800000) {
									molde = 'molde-reward-ac';
								}
								else if (itemlist[rand]['start_break'] >= 4800001 && itemlist[rand]['end_break'] <= 8700000) {
									molde = 'molde-reward-rs';
								}
								else if (itemlist[rand]['start_break'] >= 8700001 && itemlist[rand]['end_break'] <= 9600000) {
									molde = 'molde-reward-rx';
								}
								else if (itemlist[rand]['start_break'] >= 9600001 && itemlist[rand]['end_break'] <= 9900000) {
									molde = 'molde-reward-vm';
								}
								else if (itemlist[rand]['start_break'] >= 9900001 && itemlist[rand]['end_break'] <= 10000000) {
									molde = 'molde-reward-am';
								}
								else {
									molde = 'molde-reward-ac';
								}

								$(".scrolled .item .before").before("<div class='item-list " + molde + "' id='itemid-" + i +"'><img src='https://realmu.net/assets/plugins/images/items/"+itemlist[rand]['group']+"/"+itemlist[rand]['index']+".png' class='item-list-pos' /><br/><div class='item-list-title'>"+title+br+"</div></div>");
							}
							//$(".scrolled .item .before").before("<div class='item-list' id='itemid-32'><img src='https://realmu.net/assets/plugins/images/items/0/0.png' class='item-list-pos' /><br/><div class='item-list-title'>Item Random</div></div>");
							/*for(var i = 33; i < 300; i++){
								var br = '';
								var title = '';
								rand = Math.floor(Math.random() * total);
								title = itemlist[rand]['title'];
								title = title.substr(0,25);
								if (itemlist[rand]['start_break'] >= 1 && itemlist[rand]['end_break'] <= 4800000) {
									molde = 'molde-reward-ac';
								}
								else if (itemlist[rand]['start_break'] >= 4800001 && itemlist[rand]['end_break'] <= 8700000) {
									molde = 'molde-reward-rs';
								}
								else if (itemlist[rand]['start_break'] >= 8700001 && itemlist[rand]['end_break'] <= 9600000) {
									molde = 'molde-reward-rx';
								}
								else if (itemlist[rand]['start_break'] >= 9600001 && itemlist[rand]['end_break'] <= 9900000) {
									molde = 'molde-reward-vm';
								}
								else if (itemlist[rand]['start_break'] >= 9900001 && itemlist[rand]['end_break'] <= 10000000) {
									molde = 'molde-reward-am';
								}
								else {
									molde = 'molde-reward-ac';
								}
								$(".scrolled .item .before").before("<div class='item-list " + molde + "' id='itemid-" + i +"'><img src='https://realmu.net/assets/plugins/images/items/"+itemlist[rand]['group']+"/"+itemlist[rand]['index']+".png' class='item-list-pos' /><br/><div class='item-list-title'>"+title+br+"</div></h3></div>");
							}*/
							$(".scrolled").css("opacity", "1");
						}
						$(document).ready(function() {
							$(".spin").on('click', function(){
								//console.log('test2');
								var gfg_down = document.getElementById("scrollar");
								gfg_down.remove();
								$(".cases .scrolled").html("<div class='scrollar' id='scrollar'><div class='item' id='item'><div class='before'></div></div></div></div>");
								generateitem();
								setTimeout(scrollar, 1);
							});
						});
						function scrollar(){
							var scroll = 0;
							var min = 9; 
							var max = 15;  
							var variable = 0;
							var audio = new Audio('<?php echo $this->config->base_url;?>assets/plugins/images/wheel/Prize_Wheel_Spin.mp3');
							audio.play();
							
							for(var i = 0; i < 20; i++){
								variable = Math.floor(Math.random() * 80);
								max = 5468 + variable;
								scroll = -max;
								$(".scrollar").css("transform", "translateX(" + scroll +"px)");
							}
						}
					</script>
					<script type="text/javascript">
					
						function redeemRewards(identifier) {
							$(document).ready(function() {
							$('#send_roulette').attr({'onclick': 'return false;'});
							if (identifier > 0) {
								var getReward = new XMLHttpRequest();
								var nameType = '';
								var br = '';
								var title = '';
								var couldown = '';
								getReward.onreadystatechange = function() {
								if (this.readyState == 4 && this.status == 200) {
									if (this.responseText === 'error:required-login') {
										App.notice(App.lc.translate('Error').fetch(), 'error', 'You must be logged in to claim rewards.');
										$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
									} else {
										if (this.responseText === 'error:defeated') {
											App.notice(App.lc.translate('Error').fetch(), 'error', 'This season has ended, wait until the next season starts!');
											$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
										} else {
											if (this.responseText === 'error:id') {
												App.notice(App.lc.translate('Error').fetch(), 'error', 'Invalid requeriment id.');
												$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
											} else {
												if (this.responseText === 'error:invalid-id') {
													App.notice(App.lc.translate('Error').fetch(), 'error', 'This ID was not found.');
													$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
												} else {
													if (this.responseText === 'error:gc') {
														App.notice(App.lc.translate('Error').fetch(), 'error', 'Your Gremory Case is full!<br/>Free up space to use the roulette wheel.');
														$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
													} else {
														if (this.responseText === 'error:insufficient-1') {
															$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
															App.notice(App.lc.translate('Error').fetch(), 'error', 'Insufficient <b>WCoin</b> to spin the wheel of fortune');
														} else {
															if (this.responseText === 'error:insufficient-2') {
																$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
																App.notice(App.lc.translate('Error').fetch(), 'error', 'Insufficient <b>Goblin Points</b> to spin the wheel of fortune');
															} else {
																if (this.responseText === 'error:insufficient-3') {
																	$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
																	App.notice(App.lc.translate('Error').fetch(), 'error', 'Insufficient <b>Wheel Keys</b> to spin the wheel of fortune');
																} else {
																	//document.getElementById("spin").click();
																	$('.spin').trigger('click');
																	var str = '0:Test:13:12:9950000';
																	var arr = str.split(":");
																	console.log(arr[4]);
																	if (arr[4] >= 1 && arr[4] <= 4800000) {
																		molde = 'molde-reward-ac';
																	}
																	else if (arr[4] >= 4800001 && arr[4] <= 8700000) {
																		molde = 'molde-reward-rs';
																	}
																	else if (arr[4] >= 8700001 && arr[4] <= 9600000) {
																		molde = 'molde-reward-rx';
																	}
																	else if (arr[4] >= 9600001 && arr[4] <= 9900000) {
																		molde = 'molde-reward-vm';
																	}
																	else if (arr[4] >= 9900001 && arr[4] <= 10000000) {
																		molde = 'molde-reward-am';
																	}
																	else {
																		molde = 'molde-reward-ac';
																	}

																	//$('#send_roulette').attr({'class': 'wheelfortune-button'});
																	title = arr[1];
																	title = title.substr(0,25);
																		
																	var element = document.getElementById("itemid-10");
																	element.classList.add(molde);

																	//document.getElementById("balance-wk").innerHTML = arr[0];
																	//document.getElementById("reward-item").innerHTML = arr[8];//'<img src="https://realmu.net/assets/plugins/images/items/' + arr[2] + '/' + arr[3] + '.png" style="max-height: 30px;"><br/>' + arr[1];
																	document.getElementById("itemid-10").innerHTML = '<img src="<?php echo $this->config->base_url;?>assets/item_images/' + arr[2] + '/' + arr[3] + '.gif" class="item-list-pos"><br/><div class="item-list-title">' + title + br + '</div>';

																	App.notice(App.lc.translate('Success').fetch(), 'success', 'Mystery Box successfully opened!');
																	couldown = setTimeout(function() { 
																		App.notice(App.lc.translate('Success').fetch(), 'success', 'Congratulations, you won '+ title +'!');
																		$('#send_roulette').attr({'onclick': 'redeemRewards(' + identifier + ');'});
																	}, 5700);
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								};
								getReward.open("GET", "<?php echo $this->config->base_url;?>/wheel_of_fortune/spin", true);
								getReward.send();
							} else {
								App.notice(App.lc.translate('Error').fetch(), 'error', 'Unidentified reward.');
							}
							});
						}
						
						
					
						</script>
					
					<div class="cases">
					<button id="spin" class="spin" style="display: none">Spin</button>
					<div class="caixa pulse" id="send_roulette" <!--onclick="redeemRewards(1);"-->></div>
					<div class="scrolled" style="opacity: 1;">
					 <style>
					 .container {
						  display: grid;
						  place-items: center;
						  height: 100%;
						}

						.spinner, .spinner__won {
						  position: relative;
						  overflow-x: hidden;
						}

						.spinner {
						  max-width: 1130px;
						  min-width: 1130px;
						  height: 204px;
						}

						.spinner-items {
						  position: relative;
						  display: inline-flex;
						  margin: 0;
						  padding: 0;
						}

						.spinner-items__item {
						  display: block;
						  list-style-type: none;
						  overflow: hidden;
						  text-align: center;
						}

						
					 </style>
					 <div class="container">
					  <div class="spinner" id="spinnerContainer">
						<ul class="spinner-items" id="spinnerList">
						  <li class="spinner-items__item item-list molde-reward-rs" id="8">
							<img src="https://realmu.net/assets/plugins/images/items/13/510.png" class="item-list-pos">
								<br>
								<div class="item-list-title">x40 Three Vacancies </div>
						  </li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="9">
							<img src="https://realmu.net/assets/plugins/images/items/13/510.png" class="item-list-pos">
								<br>
								<div class="item-list-title">x40 Three Vacancies </div>
						  </li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="1">
							<img src="https://realmu.net/assets/plugins/images/items/13/510.png" class="item-list-pos">
								<br>
								<div class="item-list-title">x40 Three Vacancies </div>
						  </li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="2">
							<img src="https://realmu.net/assets/plugins/images/items/13/510.png" class="item-list-pos">
								<br>
								<div class="item-list-title">x40 Three Vacancies </div>
						  </li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="3">🐵</li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="4">🐰</li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="5">🐭</li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="6">🐮</li>
						  <li class="spinner-items__item item-list molde-reward-rs" id="7">🐨</li>
						</ul>

					  </div>
					</div>
					<script>
					class SpinnerAnimation {
							constructor({container, list}) {
							  this.tickSound = new Audio("<?php echo $this->config->base_url;?>assets/plugins/images/wheel/Prize_Wheel_Spin.mp3");
							  this.tickSound.playbackRate = 1;
							  
							  //this.winSound = new Audio("https://freesound.org/data/previews/511/511484_6890478-lq.mp3");
							  
							  this.firstRound = true;

							  this.reset();

							  this.spinnerContainer = document.getElementById(container);
							  this.spinnerList = spinnerContainer.children.namedItem(list);
							  this.spinnerMarker = spinnerContainer.children.namedItem("spinnerMarker");
							  this.spinnerItems = this.spinnerList.children;
							  //this.spinnerWon = document.getElementById("spinnerWon");
							}
						  
							reset() {
								this.started = false;
								this.stopped = false;
								this.stopAnimation = false;
								this.lowerSpeed = 0;
								this.ticks = 0;
								this.offSet = 0;
								this.recycle = false;
								this.tick = false;
								this.state = null;
								this.speed = 0;
								this.winningItem = 0;
								this.firstRound = false;
							}

							start(speed = 1200) {
								this.started = true;
								this.speed = speed;
								//console.log(this.speed);
								this.loop();
							}

							loop() {
								let dt = 0; // Delta Time is the amount of time between two frames
								let last = 0; // Last time of frame

								// The Animation Loop
								function loop(ms) {

									if(this.recycle) {
										this.recycle = false;
										const item = spinnerList.firstElementChild;
										spinnerList.append(item);
									}

									if(this.tick) {
										this.tick = false;
										this.tickSound.play();
									}

									this.offSet += this.speed * dt;

									const ct = ms / 1000; // MS == The amount of Milliseconds the animation is already going for. Divided by 1000 is the amount of seconds
									dt = ct - last;
									last = ct;

									// Move the item to the left
									//this.spinnerList.style.right = this.offSet + "px";
									$("#spinnerList").css("transform", "translateX(-" + this.offSet +"px)");
								  
									if(this.offSet >= 122 ) {
										this.recycle = true;
										this.offSet = 0;
										this.tick = true;
										this.ticks += 1;
										if(this.ticks >= 20 && (Math.random() * 10) >= 5) {
											this.stop();
										}
									}
									
									console.log(this.offSet);
									
									if(this.stopped) {
										let stopped = false;
										if(!stopped) this.speed -= this.lowerSpeed;

										if(this.speed <= 0) {
											stopped = true;
											this.speed = 0;
										}

										if(stopped) {
											if(this.offSet >= 58.6) {
												this.offSet += 6;
											} else {
												this.offSet -= 6;
											}

											if(this.offSet >= 122 || this.offSet <= 0) {
												this.stopAnimation = true;
												
												//this.winSound.play();
											  
												if(this.offSet >= 122) {
												  this.winningItem = 5;
												 // this.spinnerItems.item(5).classList.add("win");
												  //this.spinnerWon.innerText += this.spinnerItems.item(5).innerText;
												  this.offSet = 122;
												}
												
												if(this.offSet <= 0) {
												  this.winningItem = 4;
												  //this.spinnerItems.item(4).classList.add("win");
												  //this.spinnerWon.innerText += this.spinnerItems.item(4).innerText;
												  this.offSet = 0;
												}
											  
											}
										  
										}
									}

									if(!this.stopAnimation) {
										requestAnimationFrame(loop);
									}
								}

								// Bind Class to loop function
								loop = loop.bind(this);
								requestAnimationFrame(loop);
							}

							stop() {
								this.stopped = true;

								// Calculate a random lower speed
								this.lowerSpeed = Math.ceil(Math.random() * 10) + 1;
							}
						}

						const startSpinnerBtn = document.getElementById("send_roulette");

						const animation = new SpinnerAnimation({
							container: "spinnerContainer",
							list: "spinnerList"
						});

						startSpinnerBtn.addEventListener("click", (e) => {
							if(animation.started == "ready") { return; }
						  
							if(!animation.firstRound) animation.spinnerItems.item(animation.winningItem).classList.remove("win");
							animation.reset();
							animation.start();
						});
					</script>	
					</div>
					<div class="molde"></div>
					</div>
					</div>
					<?php
						}
					}
					?>
				</div>	
			</div>	
		</div>	
	</div>	
</div>		
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	