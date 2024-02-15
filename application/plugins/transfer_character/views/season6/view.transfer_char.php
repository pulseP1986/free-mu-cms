<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
?>	
<div id="content">
	<div id="box1">
		<?php 
		if(isset($config_not_found)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
		else:
			if(isset($module_disabled)):
				echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$module_disabled.'</div></div></div>';
			else:
		?>	
		<div class="title1">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="box-style1" style="margin-bottom: 20px;">
			<h2 class="title"><?php echo sprintf(__('Transfer character to %s server'), $this->website->get_title_from_server($plugin_config['to']));?></h2>
			<div class="entry" >
				<?php
				if(isset($error)){
					echo '<div class="e_note">'.$error.'</div>';
				}
				if(isset($success)){
					echo '<div class="s_note">'.$success.'</div>';
				}	
				?>
                    <div class="form" id="transfer_char">
                        <form method="post" action="">
                            <table>
                                <tr>
                                    <td style="width: 150px;"><?php echo __('Character'); ?></td>
                                    <td>
                                        <select name="character" id="character">
                                            <?php foreach($char_list as $char): ?>
                                                <option value="<?php echo $char['id']; ?>"><?php echo $char['Name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 150px;"><?php echo __('New Name'); ?></td>
                                    <td>
                                         <input class="validate[maxSize[10]]" type="text" name="new_name" id="new_name" value="" placeholder="<?php echo __('Empty if same');?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <button type="submit" name="transfer" class="button-style"><?php echo __('Submit'); ?></button>
                                    </td>
                                </tr>
                            </table>
                        </form>
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
	$this->load->view($this->config->config_entry('main|template').DS.'view.right_sidebar');
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>

	