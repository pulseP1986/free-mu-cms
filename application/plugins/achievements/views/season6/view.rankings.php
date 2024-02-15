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
		<h2 class="title">
			<?php echo __('Rankings'); ?>
			<div style="float:right;">
				<a class="custom_button" href="<?php echo $this->config->base_url;?>achievements"><?php echo __('Achievements');?></a>
			</div>
		</h2>
		<div class="entry" >
			<ul class="tabrow">
				<?php
				foreach($this->website->server_list() as $key => $servers):
					if($servers['visible'] == 1){
						$selectd = ($server == $key) ? 'class="selected"' : '';
						?>
						<li <?php echo $selectd; ?>><a href="<?php echo $this->config->base_url . 'achievements/rankings/' . $key; ?>"><?php echo $servers['title']; ?></a></li>
						<?php
					}
				endforeach;
				?>
			</ul>
			<?php if(!empty($rankings)){ ?>
			<table class="ranking-table">
				<thead>
					<tr class="main-tr">
						<th style="text-align:center;">#</th>
						<th style="text-align:center;"><?php echo __('Name');?></th>
						<th style="text-align:center;"><?php echo __('Class');?></th>
						<th style="text-align:center;"><?php echo __('Points');?></th>
						<th style="text-align:center;"><?php echo __('Achievements Completed');?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				$i = 1;
				foreach($rankings AS $rank){ 
				?>
					<tr>
						<td style="text-align:center;"><?php echo $i; ?></td>
						<td style="text-align:center;"><a href="<?php echo $this->config->base_url . 'info/character/'. bin2hex($rank['char_data']['Name']) .'/' . $server; ?>"><?php echo $rank['char_data']['Name'];?></a></td>
						<td style="text-align:center;"><?php echo $this->website-> get_char_class($rank['char_data']['Class']);?></td>
						<td style="text-align:center;"><?php echo $rank['ranking_points'];?></td>
						<td style="text-align:center;"><?php echo $rank['achievements_completed'];?> / <?php echo $rank['achievements_total'];?></td>
					</tr>
				<?php 
					$i++;
				} 
				?>
				</tbody>
			</table>	
			<?php } else { ?>
			<div class="w_note"><?php echo __('No players found');?></div>
			<?php } ?>
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
	