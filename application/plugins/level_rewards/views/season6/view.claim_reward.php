<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <?php
            if(isset($config_not_found)){
                echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $config_not_found . '</div></div></div>';
			}
            else{
				?>
				<div class="title1">
					<h1><?php echo __($about['name']); ?></h1>
				</div>
				<div id="content_center">
					<div class="box-style1" style="margin-bottom:55px;">
						<h2 class="title"><?php echo __($about['user_description']); ?></h2>
						<div class="entry">
							<div style="float:right;">
								<a class="custom_button" href="<?php echo $this->config->base_url;?>level-rewards"><?php echo __($about['name']); ?></a>
							</div>
							<div style="padding-top:40px;"></div>
							<div style="clear:left;"></div>
							<?php
							if(isset($module_disabled)){
								echo '<div class="e_note">' . $module_disabled . '</div>';
							}
							else{
								if(isset($error)){
									echo '<div class="e_note">' . $error . '</div>';
								}
								if(isset($success)){
									echo '<div class="s_note">' . $success . '</div>';
								}
							?>
							<div class="form">
								<form method="post" action="" id="claim_reward">
									<table style="width:100%;">
									<tr>
										<td><?php echo __('Character for reward');?></td>
										<td>
											<select class="custom-select" name="character" id="character">
											<?php foreach($characters as $char): ?>
												<option value="<?php echo $char['id'];?>"><?php echo $char['name'];?></option>
											<?php endforeach; ?>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="text-align:center !important;width:100%;"><button type="submit" id="claim" name="claim" class="button-style" style="display:inline-block;"><?php echo __('Get Reward');?></button></td>
									</tr>
									</table>
								</form>
							</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
        ?>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

