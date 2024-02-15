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
					} else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} else{
					?>	
					<h2 class="title"><?php echo __($about['user_description']); ?></h2>
					<div class="mb-5">
						<?php if(isset($js)){ ?>
						<script src="<?php echo $js;?>"></script>
						<?php } ?>
						<script>
							var transferCurrency = new transferCurrency();
							transferCurrency.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
							$(document).ready(function () {
								$('#credits_exchange_form').on("submit", function (e) {
									e.preventDefault();
									transferCurrency.submit($(this));
								});
							});
						</script>
						<form method="post" action="" id="credits_exchange_form">
							<?php if (strpos($plugin_config['transfer_type'], ',') !== false) { ?>
							<div class="form-group">
								<label class="control-label"><?php echo __('Type');?></label>
								<div>
									<select name="transfer_type" id="transfer_type" class="form-control">
										<?php 
										$currencies = explode(',', $plugin_config['transfer_type']);
										foreach($currencies AS $cr){
										?>
										<option value="<?php echo $cr; ?>"><?php echo $this->website->translate_credits($cr, $this->session->userdata(['user' => 'server'])); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Amount'); ?></label>
								<input type="text" id="game_currency" name="game_currency" value="" class="form-control" onblur="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');" onkeyup="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');" />
							</div>
							<?php } else { ?>
							<div class="form-group">
								<label class="control-label"><?php echo __('Amount of') . ' ' . $this->website->translate_credits($plugin_config['transfer_type'], $this->session->userdata(['user' => 'server'])); ?></label>
								<input type="text" id="game_currency" name="game_currency" value="" class="form-control" onblur="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');" onkeyup="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');" />
							</div>
							<?php } ?>
							<div class="form-group">
								<label class="control-label"><?php echo __('Amount'); ?> <?php echo __('+Tax'); ?></label>
								<input type="text" id="cred2" name="cred2" value="" class="form-control" disabled />
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Username'); ?></label>
								<input type="text" name="username" id="username" value="" class="form-control" />
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Message'); ?></label>
								<input type="text" name="message" id="message" value="" class="form-control" />
							</div>
							<div class="form-group">
								<div class="d-flex justify-content-center align-items-center"><button type="submit" id="exchange_credits" name="exchange_credits" disabled="disabled" class="btn btn-primary"><?php echo __('Submit'); ?></button></div>
							</div>
						</form>
						<h2><?php echo __('My Transfers');?></h2>
						<?php if(isset($my_transfers) && !empty($my_transfers)): ?>
						<table cclass="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<td>#</td>
								<td><?php echo __('Amount');?></td>
								<td><?php echo __('To');?></td>
								<td><?php echo __('Date');?></td>
								<td><?php echo __('Message');?></td>					
							</tr>
							</thead>
							<tbody>
							<?php
							$i = 0;
							foreach($my_transfers as $my):
							$i++;
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $my['amount'];?> <?php echo $this->website->translate_credits($my['type'], $this->session->userdata(['user' => 'server']));?></td>
								<td><?php echo $my['toAccount'];?></td>
								<td><?php echo date(DATETIME_FORMAT, $my['transferDate']);?></td>
								<td><?php echo $my['message'];?></td>
							</tr>
							<?php
							endforeach;
							?>
							</tbody>
						</table>
						<?php 
						endif;
						?>
						<h2><?php echo __('My Received Transfers');?></h2>
						<?php if(isset($my_received_transfers) && !empty($my_received_transfers)): ?>
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<td>#</td>
								<td><?php echo __('Amount');?></td>
								<td><?php echo __('From');?></td>
								<td><?php echo __('Date');?></td>
								<td><?php echo __('Message');?></td>					
							</tr>
							</thead>
							<tbody>
							<?php
							$i = 0;
							foreach($my_received_transfers as $my):
							$i++;
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $my['amount'];?> <?php echo $this->website->translate_credits($my['type'], $this->session->userdata(['user' => 'server']));?></td>
								<td><?php echo $my['fromAccount'];?></td>
								<td><?php echo date(DATETIME_FORMAT, $my['transferDate']);?></td>
								<td><?php echo $my['message'];?></td>
							</tr>
							<?php
							endforeach;
							?>
							</tbody>
						</table>
						<?php 
						endif;
						?>
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