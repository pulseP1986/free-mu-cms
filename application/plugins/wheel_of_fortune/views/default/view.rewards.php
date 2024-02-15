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
					<h2 class="title">
					<?php echo __('My Rewards'); ?>
					<nav class="nav nav-pills justify-content-center float-right">
						<a class="nav-item nav-link" href="<?php echo $this->config->base_url; ?>wheel-of-fortune"><?php echo __('Spin The Wheel');?></a>	
					</nav>
					
					</h2>
					
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
						<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
						<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
						<script>
						$(document).ready(function () {
							$('.hex').each(function () {
								App.initializeTooltip($(this), true);
							});
						});
						</script>
						<table class="table dmn-rankings-table table-striped">
						<thead>
						  <tr>
							<th><?php echo __('Reward'); ?></th>
							<th><?php echo __('Character'); ?></th>
							<th><?php echo __('Date Won'); ?></th>
							<th><?php echo __('Status'); ?></th>
						  </tr>
						</thead>
						<tbody>
						  <?php foreach($user_rewards_parsed AS $id => $data){	?>			
						  <tr>
							<td>
								<?php if($data['amount'] != false){ ?>
								<?php echo $data['name'];?> x<?php echo $data['amount'];?>
								<?php } else{ ?>
									<?php if($data['type'] == 0){ ?>
									<?php echo $data['name'];?>
									<?php } else { ?>
									<span class="hex" data-info="<?php echo $data['item']['hex']; ?>" data-info2='<?php echo $data['item']['item_info']; ?>'><?php echo $data['item']['name'];?></span>
									<?php } ?>
								<?php } ?>
							</td>
							<td id="character_<?php echo $data['id'];?>"><?php if($data['character'] != null){ echo $data['character']; } else { echo __('Not Claimed'); } ?></td>
							<td><?php echo $data['date_won'];?></td>
							<?php if($data['code'] != NULL && $data['is_claimed'] != 1){ ?>
							<td><?php echo $data['code'];?> <a href="<?php echo $this->config->base_url;?>gift-code" target="_blank"><?php echo __('Claim Reward');?></a></td>
							<?php } else { ?>
							<td><?php if($data['is_claimed'] == 1){ echo __('Claimed'); } else { echo '<a class="get_reward" href="javascript:;" id="reward_'.$data['id'].'" data-type="'.$data['type'].'" data-action="'.$this->config->base_url.'wheel-of-fortune/claim-reward/'.$data['id'].'">'.__('Claim Reward').'</a>'; } ?></td>
							<?php } ?>
						 </tr>
						  <?php } ?>
						</tbody>
					  </table>
					  <script type="text/javascript">
						$(document).ready(function (){
							$('.get_reward').on('click', function() {
								if($(this).data('action') != ''){
									var action = $(this).data('action');
									var that = $(this).attr('id');
									$(this).data('action', '');	
									$.alertable.prompt('Select Character', {
										prompt:
										 '<div class="form-group" style="width: 260px;">' +
										  '<select class="alertable-input" id="sel1" name="character">' +
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
													$('#'+that).parent().html('Claimed');
													$('#character_'+data.id).html(data.char);	
													$.alertable.alert(data.success, {
														html: true
													});
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
	