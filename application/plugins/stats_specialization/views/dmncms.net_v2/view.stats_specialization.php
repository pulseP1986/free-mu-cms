<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
	$this->load->view($this->config->config_entry('main|template').DS.'view.left_sidebar');
?>	
<div class="news_main">
	<?php 
	if(isset($config_not_found)):
		echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
	else:
		if(isset($module_disabled)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$module_disabled.'</div></div></div>';
		else:
	?>		
	<div class="heding">
		<h2><?php echo __($about['name']); ?></h2>
	</div>
	<div class="content_rght_info m5">
			<div class="entry">
				<?php if(isset($js)): ?>
				<script src="<?php echo $js;?>"></script>
				<?php endif;?>
				<script>
				var statsSpecialization = new statsSpecialization();
				statsSpecialization.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
				$(document).ready(function () {
						$('a[id^="specialization**"]').each(function () {
							App.initializeTooltip($(this), false);
						});
						$('a[id^="specialization**"]').on('click', function (e) {
							e.preventDefault();
							var name = $(this).attr("id").split("**")[1];
							var char_id = $(this).attr("id").split("**")[2];
							var id = $(this).attr("id").split("**")[3];
							e.preventDefault();
							if (App.confirmMessage(App.lc.translate('This function will save your current stats into this specialization and load stats from specialization to your character.').fetch())) {
								statsSpecialization.load(name, char_id, id);
							}
						});
						
						$('a[id^="remove**"]').on('click', function (e) {
							e.preventDefault();
							var name = $(this).attr("id").split("**")[1];
							var char_id = $(this).attr("id").split("**")[2];
							var id = $(this).attr("id").split("**")[3];
							e.preventDefault();
							if (App.confirmMessage(App.lc.translate('This function will remove stats specialization').fetch())) {
								statsSpecialization.remove(name, char_id, id);
							}
						});
					});
				</script>
				<?php
				if (!empty($char_list)):
					foreach ($char_list as $chars):
				?>
					<table class="add_to_card" cellspacing="0">
						<thead>
							<tr>
								<th colspan="2" style="text-align:center;"><?php echo $chars['Name']; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo __('Level');?></td>
								<td><?php echo $chars['cLevel']; ?></td>
							</tr>
							<tr>
								<td><?php echo __('Level Up Points'); ?></td>
								<td><?php echo $chars['LevelUpPoint']; ?></td>
							</tr>
							<tr>
								<td><?php echo __('Strength'); ?></td>
								<td><?php echo $chars['Strength']; ?></td>
							</tr>
							<tr>
								<td><?php echo __('Agility'); ?></td>
								<td><?php echo $chars['Dexterity']; ?></td>
							</tr>
							<tr>
								<td><?php echo __('Vitality'); ?></td>
								<td><?php echo $chars['Vitality']; ?></td>
							</tr>
							<tr>
								<td><?php echo __('Energy'); ?></td>
								<td><?php echo $chars['Energy']; ?></td>
							</tr>
							<?php if(in_array($chars['Class'], [64, 65, 66])):?>
							<tr>
								<td><?php echo __('Command'); ?></td>
								<td><?php echo $chars['Leadership']; ?></td>
							</tr>
							<?php endif;?>
							<tr>
								<td colspan="2" style="text-align:center;"><a href="<?php echo $this->config->base_url;?>stats-specialization/save/<?php echo $chars['Name']; ?>-<?php echo $chars['id']; ?>" class="custom_button"><?php echo __('Save Current Stats');?></a></td>
							</tr>
							<tr>
								<th colspan="2" style="text-align:center;"><?php echo __('Saved Specializations');?></th>
							</tr>
							<?php if(empty($chars['specializations'])):?>
							<tr>
								<td colspan="2" ><div class="i_note"><?php echo __('No specializations found.');?></div></td>
							</tr>	
							<?php 
							else:
								foreach($chars['specializations'] AS $spec):
									$info = '<div style=\'text-align:left;\'><p>'.__('Level Up Points').': '.$spec['free'].'</p>';
									$info .= '<p>'.__('Strength').': '.$spec['str'].'</p>';
									$info .= '<p>'.__('Agility').': '.$spec['agi'].'</p>';
									$info .= '<p>'.__('Vitality').': '.$spec['vit'].'</p>';
									$info .= '<p>'.__('Energy').': '.$spec['ene'].'</p>';
									if(in_array($chars['Class'], [64, 65, 66])):
									$info .= '<p>'.__('Command').': '.$spec['com'].'</p>';
									endif;
									$info .= '</div>';
							?>
								<tr id="<?php echo $spec['id'];?>">
									<td><?php echo $spec['title']; ?></td>
									<td>
										<a id="specialization**<?php echo $chars['Name'];?>**<?php echo $chars['id'];?>**<?php echo $spec['id'];?>" href="<?php echo $this->config->base_url;?>stats-specialization/load/<?php echo $spec['id'];?>" class="custom_button" data-info="<?php echo $info;?>"><?php echo __('Load Specialization');?></a>
										<a id="remove**<?php echo $chars['Name'];?>**<?php echo $chars['id'];?>**<?php echo $spec['id'];?>" href="#" class="custom_button"><?php echo __('Remove');?></a>
									</td>
								</tr>
							<?php 
								endforeach;
							endif;
							?>
						</tbody>
					</table>
					<br />
				<?php	
					endforeach;
				else:
					echo '<div class="i_note">'.__('No characters found.').'</div>';	
				endif;
				?>
			</div>
	</div>
	<?php
		endif;
	endif;
	?>
</div>
<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>	

