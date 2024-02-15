<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<script>
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	return true;
}
</script>
<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="dmn-page-content">
			<div class="row">
				<div class="col-12"> 
					<?php
					if(isset($error)){
						echo '<div class="alert alert-danger" role="alert">' . $error . ' </div>';
					}
					?>
					<form method="post" action="">
						<div class="form-group">
							<label class="control-label">First Name:</label>
							<input type="text" maxlength="20" value="<?php if(isset($customer_data['fname'])){ echo $customer_data['fname']; } ?>" class="form-control" id="fname" name="fname" required />
						</div>
						<div class="form-group">
							<label class="control-label">Last Name:</label>
							<input type="text" maxlength="20" value="<?php if(isset($customer_data['lname'])){ echo $customer_data['lname']; } ?>" class="form-control" id="lname" name="lname" required />
						</div>
						<div class="form-group">
							<label class="control-label">CPF/CNPJ:</label>
							<input type="text" onkeypress="return isNumberKey(event)" maxlength="14" value="<?php if(isset($customer_data['cpf_cnpj'])){ echo $customer_data['cpf_cnpj']; } ?>" class="form-control" id="cpf_cnpj" name="cpf_cnpj" required />
						</div>
						<div class="form-group mb-5">
							<div class="d-flex justify-content-center align-items-center"><button type="submit" class="btn btn-success" id="paghiper_customer_form" name="paghiper_customer_form">Confirm</button></div>
						</div>
					</form>
				</div>	
			</div>	
		</div>	
	</div>	
</div>		
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>