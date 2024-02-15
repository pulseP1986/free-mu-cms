<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/manage-settings/greset">GrandReset Settings</a></li>
        </ul>
    </div>
	<div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Add Grand Reset Items</h2>
            </div>
            <div class="box-content">
                <?php
                    if(isset($error)){
                        if(is_array($error)){
                            foreach($error AS $note){
                                echo '<div class="alert alert-error">' . $note . '</div>';
                            }
                        } else{
                            echo '<div class="alert alert-error">' . $error . '</div>';
                        }
                    }
                    if(isset($success)){
                        echo '<div class="alert alert-success">' . $success . '</div>';
                    }
					
                ?>
				 <form class="form-horizontal" method="post" action="<?php echo $this->config->base_url.$this->request->get_controller().'/'.$this->request->get_method().'/'.$key.'/'.$server; ?>?step=2" id="reset_item_form">
					<fieldset>
					<legend></legend>
					<div class="control-group">
						<label class="control-label" for="items">Items</label>
						<div class="controls">
							<select id="items" name="items[]" style="height: 500px !important;" multiple>
								<?php
									foreach($items AS $cat => $item){
								?>
									<optgroup label="<?php echo $this->webshop->category_from_id($cat);?>"><?php echo $this->webshop->category_from_id($cat);?></optgroup>
									<?php foreach($item AS $it){ ?>
									<option value="<?php echo $it['id'];?>-<?php echo $cat;?>"><?php echo $it['name'];?></option>
									<?php } ?>
								<?php									
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-actions">
						<button type="submit" class="btn btn-primary" name="add_items_settings">Continue
						</button>
						<button type="reset" class="btn">Cancel</button>
					</div>
				</form>	
				<?php if(!empty($reset_items_config[$server][$key])){ ?>
				<table class="table table-striped table-bordered bootstrap-datatable datatable">
				<thead>
				<tr>
					<td>Item</td>
					<td>Key</td>
					<td>Min Lv</td>
					<td>Max Lv</td>
					<td>Min Opt</td>
					<td>Max Opt</td>
					<td>Exe</td>
					<td>Action</td>
				</tr>
				</thead>
				<tbody>
				<?php foreach($reset_items_config[$server][$key] AS $cat => $itemsInfo){ 
						foreach($itemsInfo AS $id => $itemInfo){
				?>
					<tr>
					<td><?php echo $itemInfo['name'];?></td>
					<td><?php echo $cat;?></td>
					<td><?php echo $itemInfo['minLvl'];?></td>
					<td><?php echo $itemInfo['maxLvl'];?></td>
					<td><?php echo $itemInfo['minOpt'];?></td>
					<td><?php echo $itemInfo['maxOpt'];?></td>
					<td><?php echo $itemInfo['exe'];?></td>
					<td><a href="<?php echo $this->config->base_url.$this->request->get_controller().'/'.$this->request->get_method().'/'.$key.'/'.$server; ?>?remove=<?php echo $id;?>&key=<?php echo $cat;?>" class="btn btn-primary">Remove</a></td>
				</tr>
				<?php }
					} ?>	
				</tbody>
				</table>
				<?php } ?>	
			</div>
		</div>
    </div>
</div>			