<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content-full">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="dmn-page-content">
			<div class="row">
				<div class="col-12"> 
					<nav class="nav nav-pills justify-content-center float-right">
						<?php if(defined('WHEEL_KEY_PURCHASE') && WHEEL_KEY_PURCHASE == true){ ?>
						<a class="nav-item nav-link" href="<?php echo $this->config->base_url; ?>donate?type=keys"><?php echo __('Buy Keys');?></a>
						<?php } ?>
						<a class="nav-item nav-link" href="<?php echo $this->config->base_url; ?>wheel-of-fortune/my-rewards"><?php echo __('View My Reward List');?></a>	
					</nav>
					<div class="clearfix"></div>
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
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/wheel2.css?v6">
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
					<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
					<script>var names = [];var rarity = []</script>
					<div class="mx-auto pt-4 text-center">
						<div class="lootbox lootbox-case">
							<div class="lootbox-case-window" id="spinnerContainer">
								<div class="lootbox-case-line"><div class="lootbox-case-line-dash"></div></div>
								<div class="lootbox-case-spinnable" id="spinnerList">
									<?php 
									if(!empty($rewards)){
										$lastElements = array_slice($rewards, -2, 2, true);
										foreach($lastElements AS $idd => $dataa){	
									?>
										<div class="lootbox-case-item itemRoulette" id="item-<?php echo $idd-1; ?>" style="border: 2px solid <?php echo $dataa['color'];?>; background-color:<?php echo $dataa['color'];?>">
										<div class="percent_item"><?php echo $dataa['propability']; ?>%</div>
										<div class="lootbox-content">
											<div class="image">
												<?php if(!in_array($dataa['type'], [0, 7])){ ?>
													<?php if($dataa['type'] == 8){ ?>
													<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/free_spin.png">
													<?php } else { ?>
													<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/currencyNew.png">
													<?php } ?>
												<?php } elseif($dataa['type'] == 7) { ?>
												<img src="<?php echo $dataa['item']['image'];?>">
												<?php } else { ?>
												
												<?php } ?>
											</div>
											<div class="name text-center">
											<p>
												<?php if(!in_array($dataa['type'], [0, 7])){ ?>
												<?php echo $dataa['name'];?> x<?php echo $dataa['amount'];?> 
												<?php } elseif($dataa['type'] == 7) { ?>
												<?php echo $dataa['item']['name_no_style'];?>
												<?php } else { ?>
												<?php echo $dataa['name'];?>
												<?php } ?>
											</p>
											<p style="color:<?php echo $dataa['color'];?>;"><?php echo $dataa['title'];?></p>
											</div>
										</div>
									</div>
									<?php
										}
										foreach($rewards AS $id => $data){	
									?>
									<div class="lootbox-case-item itemRoulette" id="item-<?php echo $id-1; ?>" style="border: 2px solid <?php echo $data['color'];?>; background-color:<?php echo $data['color'];?>">
										<div class="percent_item"><?php echo $data['propability']; ?>%</div>
										<div class="lootbox-content">
											<div class="image">
												<?php if(!in_array($data['type'], [0, 7])){ ?>
													<?php if($data['type'] == 8){ ?>
													<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/free_spin.png">
													<?php } else { ?>
													<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/currencyNew.png">
													<?php } ?>
												<?php } elseif($data['type'] == 7) { ?>
												<img src="<?php echo $data['item']['image'];?>">
												<?php } else { ?>
												
												<?php } ?>
											</div>
											<div class="name text-center">
											<p>
												<?php if(!in_array($data['type'], [0, 7])){ ?>
												<?php echo $data['name'];?> x<?php echo $data['amount'];?> 
												<script>names.push({'text' : '<?php echo $data['name'];?> x<?php echo $data['amount'];?>'});</script>
												<?php } elseif($data['type'] == 7) { ?>
												<?php echo $data['item']['name_no_style'];?>
												<script>names.push({'text' : '<?php echo $data['item']['name'];?>'});</script>
												<?php } else { ?>
												<?php echo $data['name'];?>
												<script>names.push({'text' : '<?php echo $data['name'];?>'});</script>
												<?php } ?>
												<script>rarity.push({'text' : '<?php echo $data['title'];?>'});</script>
											</p>
											<p style="color:<?php echo $data['color'];?>;"><?php echo $data['title'];?></p>
											</div>
										</div>
									</div>
									<?php
										}
										$rewardss = array_slice($rewards, 0, 6, true);
										foreach($rewardss AS $id => $data){	
									?>
									<div class="lootbox-case-item itemRoulette" id="item-<?php echo $id-1; ?>" style="border: 2px solid <?php echo $data['color'];?>; background-color:<?php echo $data['color'];?>">
										<div class="percent_item"><?php echo $data['propability']; ?>%</div>
										<div class="lootbox-content">
											<div class="image">
												<?php if(!in_array($data['type'], [0, 7])){ ?>
													<?php if($data['type'] == 8){ ?>
													<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/free_spin.png">
													<?php } else { ?>
													<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/currencyNew.png">
													<?php } ?>
												<?php } elseif($data['type'] == 7) { ?>
												<img src="<?php echo $data['item']['image'];?>">
												<?php } else { ?>
												
												<?php } ?>
											</div>
											<div class="name text-center">
											<p>
												<?php if(!in_array($data['type'], [0, 7])){ ?>
												<?php echo $data['name'];?> x<?php echo $data['amount'];?> 
												<?php } elseif($data['type'] == 7) { ?>
												<?php echo $data['item']['name_no_style'];?>
												<?php } else { ?>
												<?php echo $data['name'];?>
												<?php } ?>
											</p>
											<p style="color:<?php echo $data['color'];?>;"><?php echo $data['title'];?></p>
											</div>
										</div>
									</div>
									<?php }} ?>
								</div>
							</div>
							<div class="mx-content mx-auto p-3">
								<div class="summary_user mx-auto" style="display:flex;">
									<div class="summary_user_content">
										<?php echo __('Price');?>
										<div class="user_balance">
											<div class="lootbox-price coin_box"><?php echo $plugin_config['spin_price'];?> <?php echo $currency_name;?></div>
										</div>
									</div>
									<div class="summary_user_content">
										<?php echo __('Free Spins');?>
										<div class="user_balance">
											<div class="lootbox-price coin_box" id="free-spins"><?php echo $freeSpins;?></div>
										</div>
									</div>
								</div>
								<div class="summary_user mx-auto" style="display:flex;">
									<div class="summary_user_content">
										<a class="btn btn-lg btn-primary btn-spin" id="btn-spin" data-free="0" href="#"><?php echo __('Spin');?></a>
									</div>
									<div class="summary_user_content">
										<a class="btn btn-lg btn-primary btn-spin" id="btn-free-spin" data-free="1" href="#"><?php echo __('Free Spin');?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
						}
					}
					?>
				</div>	
			</div>
			<script>
			$(document).ready(function () {
				$('.hex').each(function () {
					App.initializeTooltip($(this), true);
				});
			});
			</script>
			<div class="row">
				<div class="col-12 boxItems" style="padding-left: 50px;">
					<h2 class="title">Possible Rewards</h2>
					<div class="mx-auto d-flex justify-content-center align-items-center flex-wrap">
						<?php foreach($rewards AS $id => $data){ ?>
						<div class="lootbox-case-item m-1 luckyItem" style="border: 2px solid <?php echo $data['color'];?>; background-color:<?php echo $data['color'];?>">
							<div class="percent_item"><?php echo $data['propability']; ?>%</div>
							<div class="lootbox-content">
								<div class="image">
									<?php if(!in_array($data['type'], [0, 7])){ ?>
										<?php if($data['type'] == 8){ ?>
										<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/free_spin.png">
										<?php } else { ?>
										<img src="<?php echo $this->config->base_url; ?>assets/plugins/images/wheel_of_fortune/currencyNew.png">
										<?php } ?>
									<?php } elseif($data['type'] == 7) { ?>
									<img class="hex" data-info="<?php echo $data['item']['hex']; ?>" data-info2='<?php echo $data['item']['item_info']; ?>' src="<?php echo $data['item']['image'];?>">
									<?php } else { ?>
									
									<?php } ?>
								</div>
								<div class="name text-center">
								<p>
									<?php if(!in_array($data['type'], [0, 7])){ ?>
									<?php echo $data['name'];?> x<?php echo $data['amount'];?> 
									<?php } elseif($data['type'] == 7) { ?>
									<?php echo $data['item']['name_no_style'];?>
									<?php } else { ?>
									<?php echo $data['name'];?>
									<?php } ?>
								</p>
								<p style="color:<?php echo $data['color'];?>;"><?php echo $data['title'];?></p>
								</div>
							</div>
						</div>
						<?php } ?>                                                                                
					</div>
				</div>
			</div>			
		</div>	
	</div>	
</div>	
<script>
const audioWav = new Audio('<?php echo $this->config->base_url;?>assets/plugins/sound/tick.wav');

window.requestAnimFrame = (function() {
    return window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function(callback, element) {
            window.setTimeout(callback, 1000 / 60);
        };
})();

function easeOutSine(t, b, c, d) {
    return c * Math.sin(t/d * (Math.PI/2)) + b;
}

const SpinCase = function(rewardCount, wonIndex, complete) {
    const _this = this;

    _this.el = $(".lootbox-case-spinnable");
    _this.position = 0;
    _this.items = rewardCount;
    _this.itemsWidth = $(".lootbox-case-item").outerWidth(true);
    _this.spinTime = _this.el.data("speed") ?? 5000;
    _this.running = false;
    _this.resultId = wonIndex;
    _this.totalWidth = _this.items * _this.itemsWidth;

    _this.start = function() {
        _this.el.css({
            'transform': 'translateX(0px)'
        });

        const resultOffset = _this.resultId * _this.itemsWidth;
        const loops = 3;
        _this.totalDistance = (loops * _this.totalWidth) + resultOffset;

        _this.running = true;
        _this.startTime = performance.now();

        (function gameLoop(rafTime) {
            _this.update(rafTime);
            if (_this.running) {
                requestAnimFrame(gameLoop);
            }
        })(_this.startTime);
    };

    _this.update = function(rafTime) {
        const deltaTime = rafTime - _this.startTime;
        if (deltaTime >= _this.spinTime) {
            _this.running = false;
            complete();
            return;
        }

        const t = easeOutSine(deltaTime, 0, 1, _this.spinTime);
        _this.position = Math.round(t * _this.totalDistance);
        const translateX = _this.position % _this.totalWidth;

        if (translateX % _this.itemsWidth <= 5) {
             audioWav.currentTime = 0;
             audioWav.play();
         }
        _this.el.css({
            'transform': 'translateX(-' + translateX + 'px)'
        });
    };

    _this.start();

    return _this;
};

$(function () {
  $(".btn-spin").on('click', function(e){
    e.preventDefault();
	$("#btn-spin").attr("class", "d-none");
	$("#btn-free-spin").attr("class", "d-none");
	var isFree = $(this).data('free');
	$.ajax({
		url: '<?php echo $this->config->base_url;?>wheel-of-fortune/spin/'+isFree,
		dataType: 'json',	
		success: function(data){
			if(data.error){
			  $.alertable.alert('<span style="color: #000;">'+data.error+'</span>', {
				html: true
			  });
			  $("#btn-spin").attr("class", "btn btn-sm btn-primary btn-spin");
			  $("#btn-free-spin").attr("class", "btn btn-sm btn-primary btn-spin");
			  return;
			}
			if(data.rid){
				const spin = new SpinCase(data.rewardCount, data.cid, function () {
				  $("#btn-spin").attr("class", "btn btn-sm btn-primary btn-spin");
				  $("#btn-free-spin").attr("class", "btn btn-sm btn-primary btn-spin");
				  if(isFree == 1){
					$('#free-spins').html(data.left_free_spins);
				  }
				  $.alertable.alert('<span style="color: #000;">You have won ['+rarity[data.cid]['text']+']'+names[data.cid]['text']+'</span>', {
					html: true
				  });
				});
			}
		},
		error: function (xhr, ajaxOptions, thrownError){
			alert(thrownError);
			return;
		}
	});
  });
});
</script>	
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	