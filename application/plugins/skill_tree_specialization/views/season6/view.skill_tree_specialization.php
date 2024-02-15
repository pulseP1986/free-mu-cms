<?php
$this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
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
		<div id="content_center">
			<div class="box-style1" style="margin-bottom:55px;">
				<h2 class="title"><?php echo __($about['user_description']); ?></h2>
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
									<td><?php echo __('Master Level'); ?></td>
									<td><?php echo $chars['mLevel']; ?></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:center;"><a href="<?php echo $this->config->base_url;?>skill-tree-specialization/save/<?php echo $chars['Name']; ?>-<?php echo $chars['id']; ?>" class="custom_button"><?php echo __('Save Current SkillTree');?></a></td>
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
										$info = '<div style=\'text-align:left;\'><p>'.__('Level').': '.$spec['level'].'</p>';
										$info .= '<p>'.__('Master Level').': '.$spec['mlevel'].'</p>';
										$info .= '</div>';
								?>
									<tr id="<?php echo $spec['id'];?>">
										<td><?php echo $spec['title']; ?></td>
										<td>
											<a id="specialization**<?php echo $chars['Name'];?>**<?php echo $chars['id'];?>**<?php echo $spec['id'];?>" href="<?php echo $this->config->base_url;?>skill-tree-specialization/load/<?php echo $spec['id'];?>" class="custom_button" data-info="<?php echo $info;?>"><?php echo __('Load Specialization');?></a>
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
		</div>
		<?php
			endif;
		endif;
		?>
	</div>
</div>
<?php
$this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
$this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

