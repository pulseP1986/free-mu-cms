<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <?php
            if(isset($config_not_found)):
                echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $config_not_found . '</div></div></div>';
            else:
                if(isset($module_disabled)):
                    echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $module_disabled . '</div></div></div>';
                else:
                    ?>
                    <div class="title1">
                        <h1><?php echo __($about['name']); ?></h1>
                    </div>
                    <div id="content_center">
                        <div class="box-style1" style="margin-bottom:55px;">
                            <h2 class="title"><?php echo __($about['user_description']); ?></h2>
                            <div class="entry">
                                <div style="margin-top:10px;"></div>
								<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.1/css/jquery.dataTables.css">
								<style>
									table.dataTable tbody tr{
										background-color: rgba(255, 255, 255, 0) !important;
									}
									table.dataTable tbody tr:hover{
										background-color: rgba(255, 255, 255, 0.4) !important;
									}
								</style>
								<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.js"></script>
								<table class="ranking-table" cellpadding="0" cellspacing="0">
									<thead>
									<tr class="main-tr" style="padding-left: 15px;">
										<th><?php echo __('Logs'); ?></th>
										
									</tr>
									</thead>
								</table>	
								<table class="ranking-table" id="logs" cellpadding="0" cellspacing="0">
									<thead>
									<tr class="main-tr">
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

