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
                                <?php
                                    if(isset($config_not_found)):
                                        echo '<div class="e_note">' . $config_not_found . '</div>';
                                    else:
                                        if(isset($module_disabled)):
                                            echo '<div class="e_note">' . $module_disabled . '</div>';
                                        else:
                                        if(isset($js)):
                                        ?>
                                            <script src="<?php echo $js; ?>"></script>
                                        <?php endif; ?>
                                            <script>
                                                var transferCurrency = new transferCurrency();
                                                transferCurrency.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
                                                $(document).ready(function () {
                                                    $('#credits_exchange_form').on("submit", function (e) {
                                                        e.preventDefault();
                                                        transferCurrency.submit($(this));
                                                    });
                                                });
                                            </script>
                                            <div class="form">
                                                <form method="post" action="" id="credits_exchange_form">
                                                    <table>
														<?php if (strpos($plugin_config['transfer_type'], ',') !== false) { ?>
														<tr>
                                                            <td style="width:150px;">
																<select class="custom-select" name="transfer_type" id="transfer_type">
																<?php 
																$currencies = explode(',', $plugin_config['transfer_type']);
																foreach($currencies AS $cr){
																?>
																<option value="<?php echo $cr; ?>"><?php echo $this->website->translate_credits($cr, $this->session->userdata(['user' => 'server'])); ?></option>
																<?php	
																}
																?>
																</select>
															</td>
                                                            <td>
															
																<input type="text" id="game_currency" name="game_currency" value="" class="text" onblur="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');" onkeyup="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');"/>
                                                            </td>
                                                        </tr>
														<?php } else { ?>
                                                        <tr>
                                                            <td style="width:150px;"><?php echo __('Amount of') . ' ' . $this->website->translate_credits($plugin_config['transfer_type'], $this->session->userdata(['user' => 'server'])); ?>:</td>
                                                            <td>
																<input type="text" id="game_currency" name="game_currency" value="" class="text" onblur="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');" onkeyup="transferCurrency.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['tax']; ?>');"/>
                                                            </td>
                                                        </tr>
														<?php } ?>
                                                        <tr>
                                                            <td><?php echo __('Amount'); ?> <?php echo __('+Tax'); ?>:</td>
                                                            <td><input type="text" id="cred2"
                                                                       name="cred2" value="" class="text"
                                                                       disabled/></td>
                                                        </tr>
														<tr>
															<td style="width:150px;"><?php echo __('Username'); ?></td>
															<td><input type="text" name="username" id="username" value="" /></td>
														</tr>
														<tr>
															<td style="width:150px;"><?php echo __('Message'); ?></td>
															<td><input type="text" name="message" id="message" value="" /></td>
														</tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <button type="submit" id="exchange_credits"
                                                                        name="exchange_credits" disabled="disabled"
                                                                        class="button-style"><?php echo __('Submit'); ?></button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            </div>
											<div style="padding-top:20px;"></div>
											<h2>My Transfers</h2>
											<?php if(isset($my_transfers) && !empty($my_transfers)): ?>
											<table class="ranking-table">
												<thead>
												<tr class="main-tr">
													<td>#</td>
													<td><?php echo __('Amount');?></td>
													<td><?php echo __('To');?></td>
													<td><?php echo __('Date');?></td>
													<td><?php echo __('Message');?></td>					
												</tr>
												</thead>
												<tbody>
												<?php
												$i = 0;
												foreach($my_transfers as $my):
												$i++;
												?>
												<tr>
													<td><?php echo $i;?></td>
													<td><?php echo $my['amount'];?> <?php echo $this->website->translate_credits($my['type'], $this->session->userdata(['user' => 'server']));?></td>
													<td><?php echo $my['toAccount'];?></td>
													<td><?php echo date(DATETIME_FORMAT, $my['transferDate']);?></td>
													<td><?php echo $my['message'];?></td>
												</tr>
												<?php
												endforeach;
												?>
												</tbody>
											</table>
											<?php 
											endif;
											?>
											<div style="padding-top:20px;"></div>
											<h2>My Received Transfers</h2>
											<?php if(isset($my_received_transfers) && !empty($my_received_transfers)): ?>
											<table class="ranking-table">
												<thead>
												<tr class="main-tr">
													<td>#</td>
													<td><?php echo __('Amount');?></td>
													<td><?php echo __('From');?></td>
													<td><?php echo __('Date');?></td>
													<td><?php echo __('Message');?></td>					
												</tr>
												</thead>
												<tbody>
												<?php
												$i = 0;
												foreach($my_received_transfers as $my):
												$i++;
												?>
												<tr>
													<td><?php echo $i;?></td>
													<td><?php echo $my['amount'];?> <?php echo $this->website->translate_credits($my['type'], $this->session->userdata(['user' => 'server']));?></td>
													<td><?php echo $my['fromAccount'];?></td>
													<td><?php echo date(DATETIME_FORMAT, $my['transferDate']);?></td>
													<td><?php echo $my['message'];?></td>
												</tr>
												<?php
												endforeach;
												?>
												</tbody>
											</table>
											<?php 
											endif;
											?>
                                        <?php
                                        endif;
                                    endif;
                                ?>
								
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