<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
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
					<h2 class="title"><?php echo __($about['user_description']); ?> <span class="float-right"><?php echo __('My').' '.__($plugin_config['currency_name']);?>: <span id="my_currency_points"><?php echo $currency_points;?></span></span></h2>
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
					<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
					<script>
					$(document).ready(function(){
						$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
							localStorage.setItem('item-exchange-panel-tab', $(e.target).attr('href'));
						});
						var activeTab = localStorage.getItem('item-exchange-panel-tab');
						if(activeTab){
							$('#item-exchange-panel-tabs a[href="' + activeTab + '"]').tab('show');
						}
					});
					</script>
					<div class="mb-5">
					<ul class="nav nav-tabs" id="item-exchange-panel-tabs" role="tablist">
					  <li class="nav-item">
						<a class="nav-link active" id="exchange-info-tab" data-toggle="tab" href="#exchange-info" role="tab" aria-controls="exchange-info" aria-selected="true"><?php echo __('Item Exchange');?></a>
					  </li>
					  <li class="nav-item">
						<a class="nav-link" id="reward-info-tab" data-toggle="tab" href="#reward-info" role="tab" aria-controls="reward-info" aria-selected="false"><?php echo __('Reward List');?></a>
					  </li>
					</ul>
					
					<div class="tab-content" id="myTabContent">
					  <div class="tab-pane fade show active p-2" id="exchange-info" role="tabpanel" aria-labelledby="exchange-info-tab">
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<th><?php echo __('Items Required'); ?></th>
								<th><?php echo __('Reward'); ?></th>
								<th><?php echo __('Action'); ?></th>
							</tr>
							</thead>
							<tbody>
								<?php if(!empty($exchange_list)){ ?>
								<?php foreach($exchange_list AS $eid => $exchange){ ?>
								<?php if($exchange['status'] == 1){ ?>
								<tr>
									<td>
										<?php	
										if(!empty($exchange_items[$eid])){			
											foreach($exchange_items[$eid] AS $key => $item){
												echo '<div style="display: flex;flex-wrap: nowrap;"><div id="ach_ritem_'.$key.'" data-info="'.$item['hex'].'">'.$item['name'].'</div><div style="margin-left: 5px;color: #d76440; font-weight: bold;" class="item_size_12 item_font_family">x'.$item['amount'].'</div></div>';
											}	
										}
										?>
									</td>
									<td><?php echo $exchange['currency_amount'].' '.__($plugin_config['currency_name']);?> </td>
									<td>
										<a class="exchange_item" href="javascript:;" id="exchange_<?php echo $eid; ?>" data-action="<?php echo  $this->config->base_url.'item-exchange/exchange/'.$eid; ?>"><?php echo __('Exchange');?></a>
									</td>
								</tr>
								<?php } ?>
								<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					  </div>
					  <div class="tab-pane fade p-2" id="reward-info" role="tabpanel" aria-labelledby="reward-info-tab">
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<th><?php echo sprintf(__('%s Required'), $plugin_config['currency_name']); ?></th>
								<th><?php echo __('Rewards'); ?></th>
								<th><?php echo __('Action'); ?></th>
							</tr>
							</thead>
							<tbody>
								<?php if(!empty($reward_list)){ ?>
								<?php foreach($reward_list AS $rid => $reward){ ?>
								<?php if($reward['status'] == 1){ ?>
								<tr>
									<td><?php echo $reward['currency_amount'];?></td>
									<td>
										<?php if(isset($reward['credits1']) && $reward['credits1'] > 0){ ?>
											<div><?php echo $reward['credits1'];?> <?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></div>
										<?php } ?>
										<?php if(isset($reward['credits1']) && $reward['credits2'] > 0){ ?>
											<div><?php echo $reward['credits2'];?> <?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></div>
										<?php } ?>
										<?php if(isset($reward['wcoin']) && $reward['wcoin'] > 0){ ?>
											<div><?php echo $reward['wcoin'];?> <?php echo __('WCoins');?></div>
										<?php } ?>
										<?php if(isset($reward['goblin']) && $reward['goblin'] > 0){ ?>
											<div><?php echo $reward['goblin'];?> <?php echo __('GoblinPoint');?></div>
										<?php } ?>
										<?php if(isset($reward['credits3']) && $reward['credits3'] > 0){ ?>
											<div><?php echo $this->website->zen_format($reward['credits3']);?> <?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></div>
										<?php } ?>
										<?php if(isset($reward['vip_type']) && $reward['vip_type'] != ''){ ?>
											<?php $vipData = $this->Mitem_exchange->get_vip_package_title($reward['vip_type']); ?>
											<div><?php echo __('Vip');?> <?php echo $vipData['package_title'];?> [<?php echo $this->website->seconds2days($vipData['vip_time']);?>]</div>
										<?php } ?>
										<?php if(!empty($reward['items']) || !empty($reward['items2'])){ ?>
											<?php	
											if(!empty($reward_items[$rid])){
												echo '<div>';
												foreach($reward_items[$rid] AS $key => $item){
													echo '<span id="ach_ritem_'.$key.'" data-info="'.$item['hex'].'">'.$item['name'].'</span>';
												}
												echo '</div>';
											}
											?>
										<?php } ?>
									</td>
									<td>
										<?php if($currency_points >= $reward['currency_amount']){ ?>
										<a class="get_reward" href="javascript:;" id="reward_<?php echo $rid; ?>" data-action="<?php echo  $this->config->base_url.'item-exchange/claim/'.$rid; ?>"><?php echo __('Claim Reward');?></a>
										<?php } else { ?>
										<?php echo sprintf(__('More %s required'), $plugin_config['currency_name']);?>
										<?php } ?>
									</td>
								</tr>
								<?php } ?>
								<?php } ?>
								<?php } ?>
							</tbody>
						</table>	
						<script type="text/javascript">
							$(document).ready(function (){
								$('div[id^="ach_ritem_"], span[id^="ach_ritem_"]').each(function () {
									App.initializeTooltip($(this), true, 'warehouse/item_info_image');
								});
								
								$('.exchange_item').on('click', function() {
									if($(this).data('action') != ''){
										var action = $(this).data('action');
										var that = $(this).attr('id');
										$(this).data('action', '');	
										$.alertable.confirm('Are you sure?').then(function(cdata) {
											$.ajax({
												dataType: 'json',
												method: 'post',
												url: action,
												data: {'exchange': 1},
												success: function (data) {
													if (data.error) {
														$('#'+that).data('action', action);	
														$.alertable.alert(data.error, {
															html: true
														});	
													}
													else {
														$('#'+that).data('action', action);	
														$.alertable.alert(data.success, {
															html: true
														});
														$('#my_currency_points').html(data.new_points);
													}
												},
												error: function (xhr, ajaxOptions, thrownError){
													$('#'+that).data('action', action);		
													alert(thrownError);
												}
											});
										}, function() {
											$('#'+that).data('action', action);										
										});
									}
								});
								
								$('.get_reward').on('click', function() {
									if($(this).data('action') != ''){
										var action = $(this).data('action');
										var that = $(this).attr('id');
										$(this).data('action', '');	
										$.alertable.prompt('Select Character', {
											prompt:
											 '<div class="form-group" style="width: 280px;">' +
											  '<select class="alertable-input" id="character" name="character">' +
											  <?php 
											  if($characters != false){ 
												foreach($characters AS $char){
											  ?>
												'<option value="<?php echo $char["id"];?>"><?php echo $char["name"];?></option>' +
											  <?php
												}
											  }
											  ?>
											  '</select>' +
											'</div> ' 
										}).then(function(cdata) {
											if(typeof cdata.character != 'undefined'){
												$.ajax({
													dataType: 'json',
													method: 'post',
													url: action,
													data: {'claim': 1, 'character': cdata.character},
													success: function (data) {
														if (data.error) {
															$('#'+that).data('action', action);	
															$.alertable.alert(data.error, {
																html: true
															});	
														}
														else {
															$('#'+that).data('action', action);	
															$.alertable.alert(data.success, {
																html: true
															});
															$('#my_currency_points').html(data.new_points);
														}
													},
													error: function (xhr, ajaxOptions, thrownError){
														$('#'+that).data('action', action);		
														alert(thrownError);
													}
												});
											}
											else{
												$('#'+that).data('action', action);	
												alert('<?php echo __("Invalid character.");?>');
											}
										}, function() {
											$('#'+that).data('action', action);										
										});
									}
								});
								
							});
						</script>
					</div>
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