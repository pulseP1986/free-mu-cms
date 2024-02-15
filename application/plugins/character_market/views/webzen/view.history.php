<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
?>	
<div id="content">
	<div id="box1">
		<?php 
		if(isset($config_not_found)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
		else:
			if(isset($module_disabled)):
				echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$module_disabled.'</div></div></div>';
			else:
		?>	
		<div class="title1">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="box-style1" style="margin-bottom: 20px;">
			<h2 class="title"><?php echo  __('Character History');?></h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market"><?php echo __('Character Market');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market/sell-character"><?php echo __('Sell Character');?></a>	
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<div style="padding-top:20px;"></div>
				<?php if(isset($chars) && !empty($chars)): ?>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td>#</td>
						<td><?php echo __('Character');?></td>
						<td><?php echo __('Status');?></td>					
					</tr>
					</thead>
					<tbody>
					<?php
					$i = 0;
					foreach($chars as $ch):
					$i++;
					?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $this->Mcharacter_market->get_char_name_by_id($ch['mu_id'], $this->session->userdata(['user' => 'server']));?></td>
						<td>
						<?php
							if($ch['is_sold'] == 1){
								echo 'Sold';
							}
							elseif($ch['removed'] == 1){
								echo 'Removed';
							}
							else{
								echo '<a href="'.$this->config->base_url.'character-market/remove/'.$ch['id'].'">Remove</a>';
							}
						?>
						</td>
						</td>

					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				<?php 
				else:
				?>
				<div class="w_note"><?php echo __('No Characters Found.');?></div>
				<?php
				endif;
				?>
			</div>
		</div>
		<?php
			endif;
		endif;
		?>
	</div>
</div>
<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.right_sidebar');
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>
	