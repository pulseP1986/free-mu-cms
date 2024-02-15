<?php $this->load->view('plugins' . DS . $plugin_class . DS . 'views' . DS . 'default' . DS . 'view.header'); ?>
<body>
<div id="navbar">
	<div style="float: left;"><a href="<?php echo $this->config->base_url; ?>account-panel" style="color: #fff"><?php echo __('Account Panel');?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $this->config->base_url; ?>wheel-of-fortune/my-rewards" style="color: #fff"><?php echo __('View My Reward List');?></a></div><?php echo __('Welcome');?> <?php echo $this->session->userdata(['user' => 'username']);?>, <?php echo __('your');?> <?php echo $currency_name;?> <?php echo __('balance');?>: <span id="balance" style="color: #fff;"><?php echo $currency_amount;?></span>
</div>
<div id="content">
	<?php if(isset($config_not_found)){ ?>
		<div style="margin-top: 10px;height: 600px;background-color: #fff;padding: 20px;"><div class="alert alert-danger"><?php echo $config_not_found;?></div></div>
	<?php } else{ ?>	
	<script>
	$(document).ready(function () {
		$('.lottery-unit img, .hex').each(function () {
			App.initializeTooltip($(this), true, 'warehouse/item_info');
		});
	});
	var names = [];
	</script>
	<div class="luckyDraw">
		<span class="prize"></span>
		<span class="record"><?php echo __('Spins Left');?>:</span>
		<span class="record_money">
			<span class="float-right">
				<span class="text-danger" id="specialItemSpins"><?php echo ($plugin_config['spins_required_for_special_award'] - $totalSpins);?></span> <?php echo __(' For Special Item');?>
			</span>
		</span>
		 <span class="money">
			<span class="float-right" style="margin-left: 20px;margin-top: 5px;"><span id="balance" class="text-danger"><?php echo $currency_amount;?></span> <?php echo $currency_name; ?></span>
		</span>
		<ul id="lottery">
			<?php 
			if(!empty($rewards)){
				$i = 0;
				foreach($rewards AS $id => $data){
			?>
			<li class="lottery-unit lottery-unit-<?=$i?>">
				<?php if(isset($plugin_config['special_award_id']) && $plugin_config['special_award_id'] == ($i+1)){ ?>
				<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/2/diamonds.png" style="max-height: 50px;" data-info2="<?php echo __('Special Item');?>" />
				<span class="item-name"><?php echo __('Special Item');?></span>
				<script>
					names.push({'text' : '<?php echo __('Special Item');?>'});
				</script>
				<?php } else { ?>
				<?php if($data['amount'] != false){ ?>
				<font style="color: #e6e2d4;">x<?php echo $data['amount'];?></font>
				<span class="item-name"><?php echo $data['name'];?></span>
				<script>
					names.push({'text' : '<?php echo $data['name'];?> x<?php echo $data['amount'];?>'});
				</script>
				<?php } else {?>
					<?php if($data['type'] == 0){ ?>
					<font style="color: #e6e2d4;"><?php echo $data['name'];?></font>
					<script>
						names.push({'text' : '<?php echo $data['name'];?>'});
					</script>
					<?php } else { ?>
					<img src="<?php echo $data['item']['image'];?>" style="max-height: 50px;" data-info="<?php echo $data['item']['hex']; ?>" />
					<span class="item-name hex" data-info="<?php echo $data['item']['hex']; ?>"><?php echo $this->website->set_limit(strip_tags($data['item']['name']), 15, '.');?></span>
					<script>
						names.push({'text' : '<?php echo $data['item']['name'];?>'});
					</script>
					<?php } ?>
				<?php } ?>
				<?php } ?>
			</li>
			<?php
				$i++;
				}
			}
			?>
			<li id="me"><?php echo __('Spin');?> <?php echo $plugin_config['spin_price'];?> <?php echo $currency_name;?></li>
		</ul>
	</div>
	<script type="text/javascript">
                var lottery = {
                    index: 0, 
                    count: 14,
                    timer: 0, 
                    speed: 20,
                    times: 0,
                    cycle: 50,
                    prize: 0,
                    init: function(id){
                        if($("#" + id).find(".lottery-unit").length > 0){
                            $lottery = $("#" + id);
                            $units = $lottery.find(".lottery-unit");
                            this.obj = $lottery;
                            this.count = $units.length;
                            $lottery.find(".lottery-unit-" + this.index).addClass("active");
                        }
                    },
                    roll: function(){
                        var index = this.index;
                        var count = this.count;
                        var lottery = this.obj;
                        $(lottery).find(".lottery-unit-" + index).removeClass("active");
                        index += 1;
                        if(index > count -1){
                            index = 0;
                        }
                        $(lottery).find(".lottery-unit-" + index).addClass("active");
                        music2();
                        this.index = index;
                        return false;
                    },
                    stop: function(index) {
                        this.prize = index;
                        return false;
                    }
                };

                function roll() {
                    lottery.times += 1;
                    lottery.roll();
                    var prize_site = $("#lottery").attr("prize_site");
					var specialItemSpins = parseInt($("#specialItemSpins").text());
					if(prize_site > 0) prize_site -= 1;
                    if (lottery.times > lottery.cycle + 10 && lottery.index == prize_site) {
                        var record_money = $("#lottery").attr("record_money");
						$.alertable.alert('<div class="text-center"><h5>You have won '+names[lottery.index]['text']+'！</h5></div>', {
							html: true
						});
                        $("#balance").html(record_money);
						if(specialItemSpins <= 1 || (lottery.index + 1) == <?php echo $plugin_config['special_award_id'];?>){
							$("#specialItemSpins").html(<?php echo $plugin_config['spins_required_for_special_award'];?>);
						}
						else{
							$("#specialItemSpins").html(specialItemSpins - 1);
						}
						/*if((lottery.index + 1) == <?php echo $plugin_config['special_award_id'];?>){
							$("#specialItemSpins").html(0);
						}
						else{
							$("#specialItemSpins").html(specialItemSpins + 1);
						}*/
                        clearTimeout(lottery.timer);
                        lottery.prize = -1;
                        lottery.times = 0;
                        click = false;
                    } else {
                        if (lottery.times < lottery.cycle) {
                            lottery.speed -= 10;
                        } else if (lottery.times == lottery.cycle) {
                            var index = Math.random() * (lottery.count) | 0;
                            lottery.prize = index;
                        } else {
                            if (lottery.times > lottery.cycle + 10 && ((lottery.prize == 0 && lottery.index == 7) || lottery.prize == lottery.index + 1)) {
                                lottery.speed += 110;
                            } else {
                                lottery.speed += 20;
                            }
                        }
                        if (lottery.speed < 40) {
                            lottery.speed = 40;
                        }
                        lottery.timer = setTimeout(roll, lottery.speed);
                    }
                    return false;
                }

                var click = false;

                $(function() {
                    lottery.init('lottery');
                    $("#lottery #me").click(function() {
                        if (click) {
                            return false;
                        } else {
                            lottery.speed = 200;
                            $.post("<?php echo $this->config->base_url;?>wheel-of-fortune/spin", {}, function(data){
								if(data.error){
								  $.alertable.alert(data.error, {
									html: true
								  });
								  click = false;
                                  return false;
								}
								if(data.rid){
									$("#lottery").attr("prize_site", data.id);
									$("#lottery").attr("record_money", data.left_amount);
									roll();
									click = true;
									return false;
								}
								$('.lottery-unit img, .hex').each(function () {
									App.initializeTooltip($(this), true, 'warehouse/item_info');
								});
                            }, "json")
                        }
                    });
                });

                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                var audioCtx = new AudioContext();
                var arrFrequency = [196.00, 220.00, 246.94, 261.63, 293.66, 329.63, 349.23, 392.00, 440.00, 493.88, 523.25, 587.33, 659.25, 698.46, 783.99, 880.00, 987.77, 1046.50];
                var start = 0, direction = 1;

                function music2() {
                    var frequency = arrFrequency[start];
                    if (!frequency) {
                        direction = -1 * direction;
                        start = start + 2 * direction;
                        frequency = arrFrequency[start];
                    }
                    start = start + direction;
                    var oscillator = audioCtx.createOscillator();
                    var gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.type = 'sine';
                    oscillator.frequency.value = frequency;
                    gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                    gainNode.gain.linearRampToValueAtTime(1, audioCtx.currentTime + 0.01);
                    oscillator.start(audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1);
                    oscillator.stop(audioCtx.currentTime + 1);
                };
            </script>
	<?php } ?>
</div>
<?php $this->load->view('plugins' . DS . $plugin_class . DS . 'views' . DS . 'default' . DS . 'view.footer'); ?>	