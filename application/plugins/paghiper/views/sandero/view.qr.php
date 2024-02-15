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
					<div class="text-center">
						<p>Scan the QR CODE on your device to make the payment.</p>        
						<p><img src="<?php echo $qrcode; ?>"/></p>
					</div>
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