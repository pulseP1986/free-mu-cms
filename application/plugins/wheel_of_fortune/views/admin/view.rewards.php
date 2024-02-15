<?php
$this->load->view('admincp' . DS . 'view.header');
$this->load->view('admincp' . DS . 'view.sidebar');
?>
<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url; ?>admincp">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url; ?>admincp/manage-plugins">Manage Plugins</a></li>
        </ul>
    </div>
	<?php $server_list = ($is_multi_server == 0) ? ['all' => ['title' => 'All']] : $this->website->server_list(); ?>
	<div class="row-fluid">
        <div class="span12">
            <ul class="nav nav-pills">
				<li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/admin" role="tab">Settings</a></li>
				<li role="presentation" class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Rewards<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/rewards?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs">Logs</a></li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php
	if(isset($invalid)){
		echo '<div class="alert alert-error">' .$invalid . '</div>';
	}
	else{
	if(isset($error)){
		echo '<div class="alert alert-error">' . $error . '</div>';
	}
	if(isset($success)){
		echo '<div class="alert alert-success">' . $success . '</div>';
	}
	$count = isset($plugin_config[$server]['total_rewards']) ? $plugin_config[$server]['total_rewards'] : 10;
	?>
	<div class="alert alert-info">Total probabily sum of all rewards should be = 10000</div>
	<form class="form-horizontal" method="POST" action="" id="rewards_list">
		<?php for($i = 1; $i <= $count; $i++){ ?>
			<?php $start = $i % 2; ?>
			<?php if($start == 1){ ?>
			<div class="row-fluid">
			<?php } ?>
			<div class="box span6">
				<div class="box-header well" data-original-title="">
					<h2><i class="icon-th"></i> Reward <?php echo $i;?></h2>
				</div>
				<div class="box-content">
					<div class="control-group">
						<label class="control-label" for="reward_type_<?php echo $i;?>">Reward Type</label>
						<div class="controls">
							<select class="span8 typeahead" id="reward_type_<?php echo $i;?>" name="reward_type[<?php echo $i;?>]" required>
								<option value="0" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 0){ echo 'selected'; } ?>>No Reward</option>
								<option value="1" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 1){ echo 'selected'; } ?>>Credits 1</option>
								<option value="2" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 2){ echo 'selected'; } ?>>Credits 2</option>
								<option value="3" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 3){ echo 'selected'; } ?>>Web Zen</option>
								<option value="4" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 4){ echo 'selected'; } ?>>WCoin</option>
								<option value="5" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 5){ echo 'selected'; } ?>>GoblinPoint</option>
								<option value="6" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 6){ echo 'selected'; } ?>>Ruud</option>
								<option value="7" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 7){ echo 'selected'; } ?>>Item</option>
								<option value="8" <?php if(isset($reward_list[$server]['rewards'][$i]['reward_type']) && $reward_list[$server]['rewards'][$i]['reward_type'] == 8){ echo 'selected'; } ?>>Wheel Spin</option>
							</select>
						</div>
					</div>	
					<div class="control-group">
						<label class="control-label">Probability</label>
						<div class="controls">
							<input type="text" class="span8 typeahead" name="probability[<?php echo $i;?>]" value="<?php if(isset($reward_list[$server]['rewards'][$i])){ echo $reward_list[$server]['rewards'][$i]['probability']; } ?>" placeholder="100" />								
						</div>
					</div>	
					<div class="control-group" id="amount_<?php echo $i;?>" style="display:none;">
						<label class="control-label">Amount</label>
						<div class="controls">
							<input type="text" class="span8 typeahead" name="amount[<?php echo $i;?>]" value="<?php if(isset($reward_list[$server]['rewards'][$i])){ echo $reward_list[$server]['rewards'][$i]['amount']; } ?>" placeholder="100" />								
						</div>
					</div>	
					<div class="control-group">
						<label class="control-label" for="generate_code_<?php echo $i;?>">Generate Code</label>
						<div class="controls">
							<select class="span8 typeahead" id="generate_code_<?php echo $i;?>" name="generate_code[<?php echo $i;?>]" required>
								<option value="0" <?php if(isset($reward_list[$server]['rewards'][$i]['generate_code']) && $reward_list[$server]['rewards'][$i]['generate_code'] == 0){ echo 'selected'; } ?>>No</option>
								<option value="1" <?php if(isset($reward_list[$server]['rewards'][$i]['generate_code']) && $reward_list[$server]['rewards'][$i]['generate_code'] == 1){ echo 'selected'; } ?>>Yes</option>
							</select>
						</div>
					</div>	
					<div class="control-group" id="item_<?php echo $i;?>" style="display:none;">
						
						<label class="control-label" for="items">Item</label>
						<div class="controls" id="itemlist">
							<?php 
							if(!empty($reward_list[$server]['rewards'][$i]['item'])){ 
								$data = $reward_list[$server]['rewards'][$i]['item'];
							?>
								Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[<?php echo $i;?>]" value="<?php echo $data['cat'];?>" /> 
								Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[<?php echo $i;?>]" value="<?php echo $data['id'];?>" /> 
								Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[<?php echo $i;?>]" value="<?php echo $data['dur'];?>" /> 
								Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[<?php echo $i;?>]" value="<?php echo $data['lvl'];?>" placeholder="0-15" /> 
								Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[<?php echo $i;?>]" value="<?php echo $data['skill'];?>" placeholder="0/1" />
								Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[<?php echo $i;?>]" value="<?php echo $data['luck'];?>" placeholder="0/1" /><br /><br />   
								Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[<?php echo $i;?>]" value="<?php echo $data['opt'];?>" placeholder="0-7" /> 
								Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[<?php echo $i;?>]" value="<?php echo $data['exe'];?>" placeholder="1,1,1,1,1,1" /> 
								Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[<?php echo $i;?>]" value="<?php echo $data['anc'];?>" placeholder="0/5/6/9/10" /> 
								Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[<?php echo $i;?>]" value="<?php echo $data['expires'];?>" placeholder="" /> 
							<?php
							} else { 
								$data = [];
							?>		
								Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[<?php echo $i;?>]" value="" /> 
								Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[<?php echo $i;?>]" value="" /> 
								Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[<?php echo $i;?>]" value="" /> 
								Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[<?php echo $i;?>]" value="" placeholder="0-15" /> 
								Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[<?php echo $i;?>]" value="" placeholder="0/1" /> 
								Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[<?php echo $i;?>]" value="" placeholder="0/1" /><br /><br /> 
								Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[<?php echo $i;?>]" value="" placeholder="0-7" /> 
								Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[<?php echo $i;?>]" value="" placeholder="1,1,1,1,1,1" /> 
								Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[<?php echo $i;?>]" value="" placeholder="0/5/6/9/10" /> 
								Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[<?php echo $i;?>]" value="" placeholder="" />  	
							<?php } ?>	
						</div>
					</div>
			  </div>
			</div>
			<?php if($start == 0){ ?>
			</div>
			<?php } ?>
		<?php } ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" name="add_reward" id="add_reward">Update Rewards</button>
		</div>
	</form>
	<script>
	$(document).ready(function() {
		<?php 
		for($i = 1; $i <= $count; $i++){ 
		?>
		$('#reward_type_<?php echo $i;?>').on("change", function() {
			if ($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4 || $(this).val() == 5 || $(this).val() == 6 || $(this).val() == 8) {
				$('#amount_<?php echo $i;?>').show();
			} else {
				$('#amount_<?php echo $i;?>').hide();
			}
			if ($(this).val() == 7) {
				$('#item_<?php echo $i;?>').show();
			} else {
				$('#item_<?php echo $i;?>').hide();
			}
		});
		
		var reward_type = $("#reward_type_<?php echo $i;?> option:selected").val();
		if (reward_type == 1 || reward_type == 2 || reward_type == 3 || reward_type == 4 || reward_type == 5 || reward_type == 6 || reward_type == 8) {
			$('#amount_<?php echo $i;?>').show();
		} else {
			$('#amount_<?php echo $i;?>').hide();
		}
		if (reward_type == 7) {
			$('#item_<?php echo $i;?>').show();
		} else {
			$('#item_<?php echo $i;?>').hide();
		}
		<?php } ?>
	});
	</script>
	<?php } ?>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
