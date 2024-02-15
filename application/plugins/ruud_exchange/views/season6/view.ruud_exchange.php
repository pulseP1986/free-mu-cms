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
                                        <?php endif;
                                        ?>
                                            <script>
                                                var ruudExchange = new ruudExchange();
                                                ruudExchange.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
                                                $(document).ready(function () {
                                                    $('#ruud_exchange_form').on("submit", function (e) {
                                                        e.preventDefault();
                                                        ruudExchange.submit($(this));
                                                    });
                                                });
                                            </script>

                                        <?php
                                            if(!empty($char_list)):
                                            list($zen, $cred) = explode('/', $plugin_config['ratio']);
                                        ?>
                                            <div class="i_note"><?php echo __('Exchange info'); ?>:
                                                <?php
                                                    echo __('You can get') . ' ' . $cred . ' ' . __('Ruud') . ' for ' . $this->website->zen_format($zen) . ' ' . $this->website->translate_credits($plugin_config['payment_type'], $this->session->userdata(['user' => 'server']));
                                                ?>
                                            </div>
                                            <div class="form">
                                                <form method="post" action="" id="ruud_exchange_form">
                                                    <table>
                                                        <tr>
                                                            <td style="width: 150px;"><?php echo __('Location'); ?></td>
                                                            <td>
                                                                <select name="character">
                                                                    <?php foreach($char_list as $chars): ?>
                                                                        <option
                                                                                value="<?php echo $chars['id']; ?>"><?php echo $chars['Name']; ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width:150px;"><?php echo sprintf(__('Amount of %s'), $this->website->translate_credits($plugin_config['payment_type'], $this->session->userdata(['user' => 'server']))); ?>
                                                                :
                                                            </td>
                                                            <td><input type="text" id="credits" name="credits" value=""
                                                                       class="text"
                                                                       onblur="ruudExchange.calculateCurrency($('#credits').val(), '<?php echo $plugin_config['ratio']; ?>');"
                                                                       onkeyup="ruudExchange.calculateCurrency($('#credits').val(), '<?php echo $plugin_config['ratio']; ?>');"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo __('Will Receive'); ?> <?php echo __('Ruud'); ?>:
                                                            </td>
                                                            <td><input type="text" id="game_currency"
                                                                       name="game_currency" value="" class="text"
                                                                       disabled/></td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <button type="submit" id="exchange_ruud"
                                                                        name="exchange_ruud" disabled="disabled"
                                                                        class="button-style"><?php echo __('Submit'); ?></button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            </div>
                                        <?php
                                        else:
                                            echo '<div class="i_note">' . __('No characters found.') . '</div>';
                                        endif;
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

