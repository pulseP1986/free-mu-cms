<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/manage-settings/reset">Reset Settings</a></li>
        </ul>
    </div>
	<div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Add Reset Items</h2>
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
					<div class="control-group">
                            <label class="control-label" for="ckey">Config Key</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="ckey" name="ckey"
                                       value="<?php if(isset($_POST['ckey'])){
                                           echo $_POST['ckey'];
                                       } ?>" placeholder="some string" required/>
                            </div>
                        </div>
					<table class="table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
						<tr>
							<td>Item</td>
							<td>Min Lv</td>
							<td>Max Lv</td>
							<td>Min Opt</td>
							<td>Max Opt</td>
							<td>Exe Opts</td>
							<td>Skip Price</td>
						</tr>
						</thead>
						<tbody>
						<?php 
						$ii = 0;
						foreach($items as $key => $item){ 
						?>
						<input type="hidden" name="name[<?php echo $item; ?>]" value="<?php echo $itemData[$key];?>"/>
						<tr>
							<td><?php echo $itemData[$key];?></td>
							<td>
								<select id="item_min_lvl_<?php echo $ii; ?>" name="item_min_lvl[<?php echo $item; ?>]" class="input-small">
									<?php for($i = 0; $i <= 15; $i++){ ?>
										<option value="<?php echo $i; ?>">+ <?php echo $i; ?></option>
									<?php } ?>
								</select> <?php if($ii == 0){ ?><button class="btn" type="button" id="set_min_lvl">To All</button><?php } ?>
							</td>
							<td>
								<select id="item_max_lvl_<?php echo $ii; ?>" name="item_max_lvl[<?php echo $item; ?>]" class="input-small">
									<?php for($i = 0; $i <= 15; $i++){ ?>
										<option value="<?php echo $i; ?>">+ <?php echo $i; ?></option>
									<?php } ?>
								</select> <?php if($ii == 0){ ?><button class="btn" type="button" id="set_max_lvl">To All</button><?php } ?>
							</td>
							<td>
								<select id="item_min_opt_<?php echo $ii; ?>" name="item_min_opt[<?php echo $item; ?>]" class="input-small">
									<?php for($i = 0; $i <= 7; $i++){ ?>
										<option value="<?php echo $i; ?>">+ <?php echo $i * 4; ?></option>
									<?php } ?>
								</select> <?php if($ii == 0){ ?><button class="btn" type="button" id="set_min_opt">To All</button><?php } ?>
							</td>
							<td>
							<select id="item_max_opt_<?php echo $ii; ?>" name="item_max_opt[<?php echo $item; ?>]" class="input-small">
								<?php for($i = 0; $i <= 7; $i++){ ?>
									<option value="<?php echo $i; ?>">+ <?php echo $i * 4; ?></option>
								<?php } ?>
							</select> <?php if($ii == 0){ ?><button class="btn" type="button" id="set_max_opt">To All</button><?php } ?>
							</td>
							<td>
								<input type="text" id="item_exe_<?php echo $ii; ?>" name="item_exe[<?php echo $item; ?>]" value="" placeholder="1,1,1,1,1,1|0-6"/> <?php if($ii == 0){ ?><button class="btn" type="button" id="set_exe_opt">To All</button><?php } ?>
							</td>
							<td>
								<div class="control-group">
                                    
                                    <div class="controls">
                                        <input class="input-small" type="text" id="skip_price_<?php echo $ii; ?>" name="skip_price[<?php echo $item; ?>]" value="0" placeholder="0"/>
										<select id="skip_price_type_<?php echo $ii; ?>" name="skip_price_type[<?php echo $item; ?>]" class="input-small">
											<option value="0">None</option>
											<option value="1">Credits 1</option>
											<option value="2">Credits 2</option>										
										</select> <?php if($ii == 0){ ?><button class="btn" type="button" id="set_skip_price">To All</button><?php } ?>
                                    </div> 
									
                                </div>
							</td>
						</tr>
						<?php 
							$ii++;
						} 
						?>
						</tbody>
					</table>
					<div class="form-actions">
						<button type="submit" class="btn btn-primary" name="add_items_settings">Add Items
						</button>
						<button type="reset" class="btn">Cancel</button>
					</div>
				</form> 	
			</div>
		</div>
    </div>
</div>
<script>
$(document).ready(function() {
	$('#set_min_lvl').on('click', function() {
		var value = $('#item_min_lvl_0 option:selected').val();
		if ($.trim(value)) {
			if ($.isNumeric(value)) {
				$('select[id^="item_min_lvl_"]').each(function() {
					$(this).val(value);
				});
			} else {
				noty($.parseJSON('{"text":"Value can only be numeric","layout":"topRight","type":"error"}'));
			}
		} else {
			noty($.parseJSON('{"text":"Please enter some numeric value","layout":"topRight","type":"error"}'));
		}

	});
	$('#set_max_lvl').on('click', function() {
		var value = $('#item_max_lvl_0 option:selected').val();
		if ($.trim(value)) {
			if ($.isNumeric(value)) {
				$('select[id^="item_max_lvl_"]').each(function() {
					$(this).val(value);
				});
			} else {
				noty($.parseJSON('{"text":"Value can only be numeric","layout":"topRight","type":"error"}'));
			}
		} else {
			noty($.parseJSON('{"text":"Please enter some numeric value","layout":"topRight","type":"error"}'));
		}
	});
	$('#set_min_opt').on('click', function() {
		var value = $('#item_min_opt_0 option:selected').val();
		if ($.trim(value)) {
			if ($.isNumeric(value)) {
				$('select[id^="item_min_opt_"]').each(function() {
					$(this).val(value);
				});
			} else {
				noty($.parseJSON('{"text":"Value can only be numeric","layout":"topRight","type":"error"}'));
			}
		} else {
			noty($.parseJSON('{"text":"Please enter some numeric value","layout":"topRight","type":"error"}'));
		}
	});
	$('#set_max_opt').on('click', function() {
		var value = $('#item_max_opt_0 option:selected').val();
		if ($.trim(value)) {
			if ($.isNumeric(value)) {
				$('select[id^="item_max_opt_"]').each(function() {
					$(this).val(value);
				});
			} else {
				noty($.parseJSON('{"text":"Value can only be numeric","layout":"topRight","type":"error"}'));
			}
		} else {
			noty($.parseJSON('{"text":"Please enter some numeric value","layout":"topRight","type":"error"}'));
		}
	});
	$('#set_exe_opt').on('click', function() {
		var value = $('#item_exe_0').val();
		if($.trim(value)) {
			$('input[id^="item_exe_"]').each(function() {
				$(this).val(value);
			});
		} else {
			noty($.parseJSON('{"text":"Please enter some value","layout":"topRight","type":"error"}'));
		}
	});
	$('#set_skip_price').on('click', function() {
		var value = $('#skip_price_0').val();
		var value2 = $('#skip_price_type_0 option:selected').val();
		if ($.trim(value)) {
			if ($.isNumeric(value)) {
				$('input[id^="skip_price_"]').each(function() {
					$(this).val(value);
				});
				$('select[id^="skip_price_type_"]').each(function() {
					$(this).val(value2);
				});
			} else {
				noty($.parseJSON('{"text":"Value can only be numeric","layout":"topRight","type":"error"}'));
			}
		} else {
			noty($.parseJSON('{"text":"Please enter some numeric value","layout":"topRight","type":"error"}'));
		}
	});
});
</script>