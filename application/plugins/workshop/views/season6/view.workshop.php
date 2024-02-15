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
                    <div class="box-style1" style="margin-bottom: 20px;">
                        <h2 class="title"><?php echo __($about['user_description']); ?></h2>

                        <div class="entry">
                            <link rel="stylesheet" type="text/css"
                                  href="<?php echo $this->config->base_url; ?>assets/plugins/css/workshop.css?v2">
                            <link rel="stylesheet"
                                  href="<?php echo $this->config->base_url; ?>assets/plugins/css/font-awesome.min.css">
                            <?php if(isset($js)): ?>
                                <script src="<?php echo $js; ?>"></script>
                            <?php endif; ?>
                            <script>
                                var workshop = new workshop();
                                workshop.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
                                var mu_id = -1;
                                $(document).ready(function () {
                                    $('div[id^="main_inventory_"] div div img').each(function () {
                                        App.initializeTooltip($(this), true, 'warehouse/item_info');
                                    });
                                    $('div[id^="item-slot-occupied-"]').each(function () {
                                        App.initializeTooltip($(this), true, 'warehouse/item_info');
                                    });
                                    $('div[id^="char-"]').on('click', function () {
                                        mu_id = $(this).attr("id").split("-")[1];
                                        $('div[id^="upgrade-"]:visible').slideToggle();
                                        $('#upgrade-' + mu_id + ':hidden').slideToggle();
                                    });
                                })
                            </script>
                            <?php if(isset($char_list) && !empty($char_list)): ?>
                                <?php
                            foreach($char_list as $ch):
                                ?>
                                <div class="character" id="char-<?php echo $ch['id']; ?>">
                                    <div class="title-name"><?php echo $ch['name']; ?>
                                        <span><?php echo $this->website->get_char_class($ch['Class']); ?></span>
                                    </div>
                                </div>
                                <div id="upgrade-<?php echo $ch['id']; ?>" style="display: none;">
                                    <div class="itemupgrade">
                                        <div class="inventory">
                                            <div class="upgtitle">Inventory</div>
                                            <div class="item" style="display: block;">
                                                <div id="main_inventory_<?php echo $ch['id']; ?>">
                                                    <?php if($equipment[$ch['id']][0] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 50px; height: 76px; position: absolute; left: 16px; top:118px;">
                                                            <div style="width: 50px; height: 76px; line-height: 76px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][0]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][0]['serial'] . '-' . $equipment[$ch['id']][0]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][0]['item_id'], $equipment[$ch['id']][0]['item_cat'], $equipment[$ch['id']][0]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][7] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 87px; height: 48px; position: absolute; left: 198px; top:56px;">
                                                            <div style="width: 87px; height: 48px; line-height: 48px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][7]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][7]['serial'] . '-' . $equipment[$ch['id']][7]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][7]['item_id'], $equipment[$ch['id']][7]['item_cat'], $equipment[$ch['id']][7]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][9] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 32px; height: 32px; position: absolute; left: 80px; top:74px;">
                                                            <div style="width: 32px; height: 32px; line-height: 32px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][9]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][9]['serial'] . '-' . $equipment[$ch['id']][9]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][9]['item_id'], $equipment[$ch['id']][9]['item_cat'], $equipment[$ch['id']][9]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][1] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 50px; height: 76px; position: absolute; left: 234px; top:118px;">
                                                            <div style="width: 50px; height: 76px; line-height: 76px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][1]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][1]['serial'] . '-' . $equipment[$ch['id']][1]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][1]['item_id'], $equipment[$ch['id']][1]['item_cat'], $equipment[$ch['id']][1]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][2] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 50px; height: 50px; position: absolute; left: 126px; top:56px;">
                                                            <div style="width: 50px; height: 50px; line-height: 50px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][2]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][2]['serial'] . '-' . $equipment[$ch['id']][2]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][2]['item_id'], $equipment[$ch['id']][2]['item_cat'], $equipment[$ch['id']][2]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][3] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 50px; height: 76px; position: absolute; left: 126px; top:118px;">
                                                            <div style="width: 50px; height: 76px; line-height: 76px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][3]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][3]['serial'] . '-' . $equipment[$ch['id']][3]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][3]['item_id'], $equipment[$ch['id']][3]['item_cat'], $equipment[$ch['id']][3]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][4] != 0){ ?>
                                                        <div class="hover_inv" class=""
                                                             style="width: 50px; height: 50px; position: absolute; left: 126px; top:206px;">
                                                            <div style="width: 50px; height: 50px; line-height: 52px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][4]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][4]['serial'] . '-' . $equipment[$ch['id']][4]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][4]['item_id'], $equipment[$ch['id']][4]['item_cat'], $equipment[$ch['id']][4]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][5] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 50px; height: 50px; position: absolute; left: 16px; top:206px;">
                                                            <div style="width: 50px; height: 50px; line-height: 50px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][5]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][5]['serial'] . '-' . $equipment[$ch['id']][5]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][5]['item_id'], $equipment[$ch['id']][5]['item_cat'], $equipment[$ch['id']][5]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][6] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 50px; height: 50px; position: absolute; left: 235px; top:206px;">
                                                            <div style="width: 50px; height: 50px; line-height: 50px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][6]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][6]['serial'] . '-' . $equipment[$ch['id']][6]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][6]['item_id'], $equipment[$ch['id']][6]['item_cat'], $equipment[$ch['id']][6]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][10] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 32px; height: 32px; position: absolute; left: 80px; top:225px;">
                                                            <div style="width: 32px; height: 32px; line-height: 32px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][10]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][10]['serial'] . '-' . $equipment[$ch['id']][10]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][10]['item_id'], $equipment[$ch['id']][10]['item_cat'], $equipment[$ch['id']][10]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if($equipment[$ch['id']][11] != 0){ ?>
                                                        <div class="hover_inv"
                                                             style="width: 32px; height: 32px; position: absolute; left: 188px; top:225px;">
                                                            <div style="width: 32px; height: 32px; line-height: 32px; position: absolute; vertical-align: middle; text-align: center;">
                                                                <img data-info="<?php echo $equipment[$ch['id']][11]['hex']; ?>"
                                                                     data-serial="<?php echo $equipment[$ch['id']][11]['serial'] . '-' . $equipment[$ch['id']][11]['serial2']; ?>"
                                                                     style="position: relative; max-height: 100%; max-width: 100%;"
                                                                     src="<?php echo $this->itemimage->load($equipment[$ch['id']][11]['item_id'], $equipment[$ch['id']][11]['item_cat'], $equipment[$ch['id']][11]['level'], 0); ?>">
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div style=" border: solid 1px rgba(52, 54, 58, 0.5);width: 208px; height:208px;position: absolute; left: 46px; top:274px;">
                                                    <?php
                                                        $inv_content = '';
                                                        foreach($inventory[$ch['id']] AS $key => $item){
                                                            if(isset($item['hex'])){
                                                                $data = 'data-info="' . $item['hex'] . '" data-serial="' . $item['serial'] . '-' . $item['serial2'] . '"';
                                                                $inv_content .= '<div class="slot" id="item-slot-occupied-' . $key . '" style="margin-top:' . ($item['yy'] * 26) . 'px; margin-left:' . ($item['xx'] * 26) . 'px; position:absolute; z-index:999;width:' . ($item['x'] * 26) . 'px; height:' . ($item['y'] * 26) . 'px;" ' . $data . '><img width="100%" height="100%" alt="' . $item['name'] . '" src="' . $this->itemimage->load($item['item_id'], $item['item_cat'], $item['level'], 0) . '" /></div>' . "\n";
                                                            } else{
                                                                $inv_content .= '<div id="item-slot-' . $key . '" style="margin-top:' . ($item['yy'] * 28) . 'px; margin-left:' . ($item['xx'] * 28) . 'px; position:absolute; z-index:1;width:28px; height:28px;"></div>' . "\n";
                                                            }
                                                        }
                                                        echo $inv_content;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-menu" id="item-content-<?php echo $ch['id']; ?>"></div>
                                    </div>
                                </div>
                            <?php
                                endforeach;
                            ?>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $('div[id^="item-slot-occupied-"], div[id^="main_inventory_"] div div img').on('click', function (e) {
                                            e.preventDefault();
                                            var serial = $(this).data("serial");
                                            $.ajax({
                                                url: '<?php echo $this->config->base_url;?>workshop/get_item_data',
                                                data: {serial: serial, character: '-id' + mu_id},
                                                success: function (data) {
                                                    if (data.error) {
                                                        App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
                                                        $('#item-content-' + mu_id).html('');
                                                        $('#item-content-' + mu_id + ':visible').slideToggle();
                                                    }
                                                    else {
                                                        EJS.config({cache: false});
                                                        var html = new EJS({url: DmNConfig.base_url + 'assets/plugins/js_templates/item_upgrade.ejs'}).render(data);
                                                        $('#item-content-' + mu_id).html(html);
                                                        $('#item-content-' + mu_id + ':hidden').slideToggle();
                                                        App.initializeModalBoxes();
                                                    }
                                                }
                                            });
                                        });
                                    });
                                </script>
                            <?php
                                else:
                            ?>
                                <div class="w_note"><?php echo __('No Characters Found.'); ?></div>
                            <?php
                            endif;
                            ?>
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
	