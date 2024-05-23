<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <div class="title1">
            <h1><?php echo __($about['name']); ?></h1>
        </div>
        <div class="box-style1" style="margin-bottom: 20px;">
            <h2 class="title"><?php echo __($about['user_description']); ?></h2>
			<?php if(isset($js)){ ?>
			<script src="<?php echo $js;?>"></script>
			<?php } ?>
			<script>
				var pluginJs = new pluginJs();
				pluginJs.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
				$(document).ready(function () {
					$('#hide_info').on("submit", function (e) {
						e.preventDefault();
						pluginJs.submit($(this));
					});
				});
			</script>
            <div class="entry">
				<form method="post" action="" id="hide_info">
					<table class="ranking-table">
						<thead>
							<tr class="main-tr">
								<th style="text-align: left;padding-left: 15px;" colspan="3"><?php echo __('Details'); ?></th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Hide Till'); ?></td>
							<td style="width:70%;text-align: left;padding-left: 15px;" id="hide_time"><?php echo ($hide_time == false) ? __('None') : $hide_time; ?></td>
						</tr>
						<tr>
							<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Hide Info'); ?></td>
							<td style="width:70%;text-align: left;padding-left: 15px;"><?php echo __('Everyone can\'t see location or inventory items on all chars'); ?></td>
						</tr>
						<tr>
							<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Hide Price'); ?></td>
							<td style="width:70%;text-align: left;padding-left: 15px;">
								<?php
								$price = $plugin_config['price'];
								if($this->session->userdata('vip')){
									$price -= ($price / 100) * $this->session->userdata(['vip' => 'hide_info_discount']);
								}
								echo $price . ' ' . $this->website->translate_credits($plugin_config['payment_method'], $this->session->userdata(['user' => 'server'])) . ', ' . $plugin_config['days'] . ' ' . __('days');
								?>
							</td>
						</tr>
						</tbody>
					</table>
					<div style="text-align:center;">
						<button class="custom_button"><?php echo __('Hide Now'); ?></button>
					</div>
				</form>
            </div>
        </div>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	