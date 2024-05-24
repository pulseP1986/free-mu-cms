<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <div class="title1">
            <h1><?php echo __($about['name']); ?></h1>
        </div>
        <div class="box-style1" style="margin-bottom: 20px;">
			<?php
			if(isset($config_not_found)){
				echo '<div class="e_note">' . $config_not_found . '</div>';
			}
			else{
			?>
            <h2 class="title"><?php echo __($about['user_description']); ?></h2>
            <div class="entry">
				<?php
				if(isset($error)){
					echo '<div class="e_note">' . $error . '</div>';
				}
				if(isset($success)){
					echo '<div class="s_note">' . $success . '</div>';
				}
                ?>
				<form method="post" action="">
					<table class="ranking-table">
						<thead>
							<tr class="main-tr">
								<th style="text-align: left;padding-left: 15px;" colspan="3"><?php echo __('Details'); ?></th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Online Hours'); ?></td>
                            <td style="width:70%;text-align: left;padding-left: 15px;"><?php echo $online_time; ?></td>
						</tr>
						<tr>
							<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Reward'); ?></td>
							<td style="width:70%;text-align: left;padding-left: 15px;">
								<?php
								$reward = $plugin_config['reward'];
								if($this->session->userdata('vip')){
									$reward += $this->session->userdata(['vip' => 'online_hour_exchange_bonus']);
								}
								echo (int)$reward * $online_time . ' ' . $this->website->translate_credits($plugin_config['reward_method'], $this->session->userdata(['user' => 'server']));
								?>
							</td>
						</tr>
						</tbody>
					</table>
					<div style="text-align:center;">
						<button class="custom_button" id="trade_hours" name="trade_hours"><?php echo __('Exchange Now'); ?></button>
					</div>
				</form>
            </div>
			<?php } ?>
        </div>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	