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
		<h2 class="title"><?php echo __($about['user_description']); ?></h2>
		<div class="entry" >
			<div style="float:right;">
				<a class="custom_button" href="<?php echo $this->config->base_url;?>achievements"><?php echo __('Character List');?></a>
				<a class="custom_button" href="<?php echo $this->config->base_url;?>achievements/rankings/<?php echo $this->session->userdata(['user' => 'server']);?>"><?php echo __('Rankings');?></a>
			</div>
			<div style="padding-top:40px;"></div>
			<div style="clear:left;"></div>
			<link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url; ?>assets/plugins/css/achievements.css?v2">
			<?php
			if(isset($success)):
				echo '<div class="s_note">'.$success.'</div>';
			endif;
			if(isset($error)):
				echo '<div class="e_note">'.$error.'</div>';
			endif;
			?>
			
			<?php foreach($archievement_list AS $key => $achievement){ ?>
			
			<div class="achievement">
			  <table>
				<tbody><tr>
				  <?php if($achievement['image'] != ''){ ?><td class="ach-icon"><img src="<?php echo $achievement['image'];?>" alt=""></td><?php } ?>
				  <td class="ach-text">
					<h3><a style="float:left;" href="<?php echo $this->config->base_url; ?>achievements/check/<?php echo $id;?>/<?php echo $achievement['id'];?>"><?php echo $achievement['title'];?></a><?php if($achievement['completed'] != 1){ ?><a style="float:right;text-decoration: none;text-decorations:none;cursor: pointer;" id="reload_ach_<?php echo $id;?>_<?php echo $achievement['id'];?>" data-action="<?php echo $this->config->base_url;?>achievements/reload" title="<?php echo __('Reload Status');?>">&#x21bb;</a><?php } ?></h3>
					<div class="spacer2"></div>
					<div class="small"><?php echo $achievement['desc'];?></div>
				  </td>
				</tr>
				<tr>
				</tr>
			  </tbody>
			  </table>
			  <div class="progressbar">
				<?php
					$percents = 0;
					$message = '';
					if($achievement['completed'] == 1){
						$percents = 100;
						$message = __('Completed');
					}
					else{
						if($achievement['achievement_type'] != 0){
							if($achievement['achievement_type'] != 9){
								if(($achievement['complete_amount'] * 100) == 0 && $achievement['full_amount'] == 0){
									$percents = 0;
								}
								else{
									$percents = ($achievement['complete_amount'] * 100) / $achievement['full_amount'];
								}
								$message = $achievement['complete_amount'] . ' / ' . $achievement['full_amount'];
							}
							else{
								$amountTotal = 0;
								$amountLeft = 0;
								foreach($achievement['items'] AS $total){
									$amountTotal += $total['amount'];
								}
								foreach($achievement['items_left'] AS $left){
									$amountLeft += $left['amount'];
								}
								
								if($amountLeft <= 0){
									$left = $amountTotal;
									$percents = 100;
								}
								else{
									$left = $amountTotal - $amountLeft;
									$percents = ($left * 100) / $amountTotal;
								}
								$message = $left . ' / ' . $amountTotal;
							}
						}
					}
				?>
				<div id="stage_<?php echo $achievement['id'];?>" class="stage yellow" style="width:<?php echo $percents; ?>%"></div>
				<i id="stage_msg_<?php echo $achievement['id'];?>"><?php echo $message;?></i>
			</div>
		</div>
		<?php 
		} 
		?>
		<script type="text/javascript">
		$(document).ready(function () {
			$('a[id^="reload_ach_"]').on('click', function (e) {
				e.preventDefault();
				if($(this).data('action') != ''){	
					var action = $(this).data('action');
					var cid = $(this).attr('id').split('_')[2];
					var aid = $(this).attr('id').split('_')[3];
					var that = $(this).attr('id');
					$(this).data('action', '');	
					$.ajax({
						url: action,
						data: {cid: cid, aid: aid},
						beforeSend: function () {
							App.showLoader();
						},
						complete: function () {
							setInterval(function () {
								App.hideLoader();
							}, 2000);
						},
						success: function (data) {
							if (data.error) {
								$('#'+that).data('action', action);		
								App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
							}
							else {
								$('#stage_msg_'+aid+'').html(data.msg);
								$('#stage_'+aid+'').css({'width':''+data.percents+'%'});
								$('#'+that).data('action', action);		
								if(data.percents == 100){
									$('#'+that).hide();
								}
							}
						}
					});
				}
			});				
		});
		</script>
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
	
	