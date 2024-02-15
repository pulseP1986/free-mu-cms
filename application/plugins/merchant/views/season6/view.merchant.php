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
                    if(isset($not_allowed)):
                        echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $not_allowed . '</div></div></div>';
                    else:
                        ?>
                        <div class="title1">
                            <h1><?php echo __($about['name']); ?></h1>
                        </div>
                        <div id="content_center">
                            <div class="box-style1" style="margin-bottom:55px;">
                                <h2 class="title"><?php echo __($about['user_description']); ?></h2>

                                <div class="entry">
                                    <?php if(isset($js)): ?>
                                        <script src="<?php echo $js; ?>"></script>
                                    <?php
                                    endif;
                                        $wcoin_bonus_ratio = explode('/', $plugin_config['wcoin_bonus_ratio']);
                                        $reward_bonus_ratio = explode('/', $plugin_config['reward_bonus_ratio']);
                                    ?>
                                    <script>
                                        var Merchant = new Merchant();
                                        Merchant.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
                                        Merchant.setRequirementForBonus('<?php echo $wcoin_bonus_ratio[0];?>');
                                        Merchant.setWcoinBonus('<?php echo $wcoin_bonus_ratio[1];?>');
                                        Merchant.setWcoinBonusTotal('<?php echo $plugin_config['wcoin_total_bonus'];?>');
                                        Merchant.setRequirementForWebCurrencyBonus('<?php echo $reward_bonus_ratio[0];?>');
                                        Merchant.setWebCurrencyBonus('<?php echo $reward_bonus_ratio[1];?>');
                                        $(document).ready(function () {
                                            $('#currency_exchange_form').on("submit", function (e) {
                                                e.preventDefault();
                                                Merchant.submitExchange($(this));
                                            });
                                        });
                                    </script>
                                    <?php
                                        list($currency, $wcoin) = explode('/', $plugin_config['wcoin_ratio']);
                                    ?>
                                    <div class="i_note"><?php echo __('Exchange info'); ?>:
                                        <?php
                                            echo $currency . ' ' . $plugin_config['currency_used'] . ' = ' . $wcoin . ' ' . __('WCoin');
                                        ?>
                                        <br/>
                                        <?php echo __('Your Balance'); ?>: <span
                                                id="merchant_balance"><?php echo $data['wallet']; ?></span> <?php echo $plugin_config['currency_used']; ?>
                                    </div>
                                    <div class="form">
                                        <form method="post" action="" id="currency_exchange_form">
                                            <table>
                                                <tr>
                                                    <td style="width:150px;"><?php echo __('Account Id'); ?>:</td>
                                                    <td>
                                                        <input type="text" id="account" name="account" value=""
                                                               class="text"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:150px;"><?php echo sprintf(__('Amount of %s'), $plugin_config['currency_used']); ?>
                                                        :
                                                    </td>
                                                    <td>
                                                        <input type="text" id="credits" name="credits" value="" class="text"
                                                               onblur="Merchant.calculateCurrency($('#credits').val(), '<?php echo $plugin_config['wcoin_ratio']; ?>');"
                                                               onkeyup="Merchant.calculateCurrency($('#credits').val(), '<?php echo $plugin_config['wcoin_ratio']; ?>');"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Will Receive'); ?> <?php echo __('WCoin'); ?>:</td>
                                                    <td><input type="text" id="game_currency" name="game_currency" value=""
                                                               class="text" disabled/></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Will Receive'); ?> <?php echo $this->website->translate_credits($plugin_config['reward_type'], $this->session->userdata(['user' => 'server'])); ?>
                                                        :
                                                    </td>
                                                    <td><input type="text" id="web_currency" name="web_currency" value=""
                                                               class="text" disabled/></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <button type="submit" id="exchange_currency"
                                                                name="exchange_currency" disabled="disabled"
                                                                class="button-style"><?php echo __('Submit'); ?></button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endif;
                endif;
            endif;
        ?>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

