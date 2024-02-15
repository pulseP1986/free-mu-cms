<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <div class="title1">
            <h1><?php echo __('Warp Character'); ?></h1>
        </div>
        <div class="box-style1" style="margin-bottom: 20px;">
            <h2 class="title"><?php echo __('Teleport your character to another location'); ?></h2>

            <div class="entry">
                <?php
                    if(isset($disabled)){
                        echo '<div class="e_note">' . __('This module has been disabled.') . '</div>';
                    } else{
                        if(isset($error)){
                            echo '<div class="e_note">' . $error . '</div>';
                        }
                        if(isset($success)){
                            echo '<div class="s_note">' . $success . '</div>';
                        }
                        if(isset($char_list) && $char_list != false){
                            ?>
                            <div class="form">
                                <form method="post" action="<?php echo $this->config->base_url; ?>account-panel/warp-char">
                                    <table>
                                        <tr>
                                            <td style="width: 150px;"><?php echo __('Character'); ?></td>
                                            <td>
                                                <select class="custom-select" name="character">
                                                    <?php foreach($char_list as $char): ?>
                                                        <option
                                                                value="<?php echo $char['name']; ?>"><?php echo $char['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('Location'); ?></td>
                                            <td>
												<?php if(!empty($teleport_config['teleports'])){ ?>
												<select name="world">
												<?php 
                                                foreach($teleport_config['teleports'] AS $key => $data){ 
                                                    $additionalText = isset($data['add_text']) ? $data['add_text'] : '';
                                                ?>
													<option value="<?php echo $key;?>"><?php echo $this->website->get_map_name($data['map_id']).$additionalText;?> [Lv. <?php echo $data['req_lvl'];?>]</option>
												<?php } ?>
                                                </select>
												<?php } else { ?>
												<?php echo __('No teleports'); ?>
												<?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <button type="submit"
                                                        class="button-style"><?php echo __('Warp'); ?></button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                            <?php
                        } else{
                            ?>
                            <div
                                    class="e_note"><?php echo __('Character not found.'); ?></div>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	