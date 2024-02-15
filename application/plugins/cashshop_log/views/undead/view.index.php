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
					} 
					else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} 
						else{
					?>	
					<h2 class="title"><?php echo __($about['user_description']); ?></h2>
					<div class="mb-5">
						<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.1/css/jquery.dataTables.css">
						<style>
							table.dataTable tbody tr{
								background-color: rgba(255, 255, 255, 0) !important;
							}
						</style>
						<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.js"></script>
						<table class="table dmn-rankings-table table-striped" id="logs">
							<thead>
							<tr>
								<th><?php echo __('Item'); ?></th>
								<th><?php echo __('Price'); ?></th>
								<th><?php echo __('Purchase Date'); ?></th>
							</tr>
							</thead>
							<tbody>
								<?php 
									if(!empty($logs)){
										foreach($logs AS $log){
											$type = ($log['Cointype'] == 0) ? __('Wcoin') : __('GoblinPoint');
								?>
										<tr>
											<td><?php echo $log['name']; ?></td>
											<td><?php echo $log['Price']; ?> <?php echo $type;?></td>
											<td><?php echo date('Y-m-d H:i:s', strtotime($log['BuyDate'])); ?></td>
										</tr>
								<?php 
										}
									}
								?>	
							</tbody>
						</table>
						<script>
						$(document).ready(function(){
							$('#logs').DataTable({
								"searching": false, 
								"info": false, 
								"lengthChange": false, 
								"order": [[ 2, "desc" ]], 
								"pageLength": 10,
								"language": {
									"emptyTable": "<?php echo __('No data available in table');?>",	
									"paginate": {
									  "previous": "<?php echo __('Previous');?>",
									  "next": "<?php echo __('Next');?>"
									}
								}
							});
						});
						</script>
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