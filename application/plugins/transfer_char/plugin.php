<?php
    in_file();

    class _plugin_transfer_char extends controller implements pluginInterface
    {
        private $pluginaizer;
        private $vars = [];

        /**
         *
         * Plugin constructor
         * Initialize plugin class
         *
         */
        public function __construct()
        {
            //initialize parent constructor
            parent::__construct();
            //initialize pluginaizer
            $this->pluginaizer = $this->load_class('plugin');
            //set plugin class name
            $this->pluginaizer->set_plugin_class(substr(get_class($this), 8));
        }

        /**
         *
         * Main module body
         * All main things related to user side
         *
         *
         * Return mixed
         */
        public function index($page = 1)
        {
            if($this->pluginaizer->data()->value('installed') == false){
                throw new Exception('Plugin has not yet been installed.');
            } else{
                if($this->pluginaizer->data()->value('installed') == 1){
                    if($this->pluginaizer->data()->value('is_public') == 0){
                        $this->user_module($page);
                    } else{
                        $this->public_module();
                    }
                } else{
                    throw new Exception('Plugin has been disabled.');
                }
            }
        }

        /**
         *
         * Load user module data
         *
         * return mixed
         *
         */
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		 
        private function user_module($page = 1)
        {
            //check if visitor has user privilleges
            if($this->pluginaizer->session->is_user()){
                //load website helper
                $this->load->helper('website');
                //load paginator
                $this->load->lib('pagination');
                //load plugin config
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
                    if($this->pluginaizer->data()->value('is_multi_server') == 1){
                        if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
                            $this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
                            $this->vars['about'] = $this->pluginaizer->get_about();
                            $this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
                        } else{
                            $this->vars['config_not_found'] = __('Plugin configuration not found.');
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        $this->vars['module_disabled'] = __('This module has been disabled.');
                    } else{
						$this->load->model('application/plugins/transfer_char/models/transfer_char');
						 if(isset($_POST['transfer_character'])){
                            foreach($_POST as $key => $value){
                                $this->pluginaizer->Mtransfer_char->$key = trim($value);
                            }
                            if(!$this->pluginaizer->Mtransfer_char->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])))
                                $this->vars['error'] = __('Please logout from game.'); 
							else{
                                if(!isset($this->pluginaizer->Mtransfer_char->vars['character']))
                                    $this->vars['error'] = __('Please select character.'); 
								else{
                                    if(!$this->pluginaizer->Mtransfer_char->check_char($this->pluginaizer->Mtransfer_char->vars['character'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])))
                                        $this->vars['error'] = __('Character not found.'); 
									else{
										$account = $this->pluginaizer->Mtransfer_char->checkAccount($this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
										if($account == false)
											$this->vars['error'] = __('User not found.'); 
										else{
											if(!$this->pluginaizer->Mtransfer_char->check_connect_stat($this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server'])))
												$this->vars['error'] = __('Other user need to logout from game.'); 
											else{
												if($this->pluginaizer->Mtransfer_char->vars['username'] == $this->pluginaizer->session->userdata(['user' => 'username']))
													$this->vars['purchase_error'] = __('You can not transfer to same user.'); 
												else{
													$this->pluginaizer->Mtransfer_char->char_info($this->pluginaizer->Mtransfer_char->vars['character'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													if($this->pluginaizer->Mtransfer_char->char_info['CtlCode'] == 1)
														$this->vars['error'] = __('You can not transfer banned character.'); 
													else{
														if($this->pluginaizer->Mtransfer_char->char_info['CtlCode'] == 32)
															$this->vars['error'] = __('You can not transfer gm character.'); 
														else{
						
															if($this->vars['plugin_config']['allow_transfer_with_gens'] == 0 && $this->pluginaizer->Mtransfer_char->get_gens_info($this->pluginaizer->Mtransfer_char->vars['character'], $this->pluginaizer->session->userdata(['user' => 'server'])))
																$this->vars['error'] = __('You are not allowed to transfer character with gens.'); 
															else{
																if($this->vars['plugin_config']['allow_transfer_with_guild'] == 0){
																	if($this->pluginaizer->Mtransfer_char->has_guild($this->pluginaizer->Mtransfer_char->vars['character'], $this->pluginaizer->session->userdata(['user' => 'server'])))
																		$this->vars['error'] = __('You are not allowed to transfer character while it is in guild.');
																}
																$currentLevel = $this->pluginaizer->Mtransfer_char->char_info['cLevel']+$this->pluginaizer->Mtransfer_char->char_info['mlevel'];
																if(isset($this->vars['plugin_config']['max_character_level']) && $this->vars['plugin_config']['max_character_level'] < $currentLevel){
																	$this->vars['error'] = __('Max character level can be '.$this->vars['plugin_config']['max_character_level'].'');
																}
																if(isset($this->vars['plugin_config']['min_character_level']) && $this->vars['plugin_config']['min_character_level'] > $currentLevel){
																	$this->vars['error'] = __('Min character level can be '.$this->vars['plugin_config']['min_character_level'].'');
																}
																if(isset($this->vars['plugin_config']['reg_empty_inventory']) && $this->vars['plugin_config']['reg_empty_inventory'] == 1 && $this->pluginaizer->Mtransfer_char->check_inventory($this->pluginaizer->session->userdata(['user' => 'server'])) == false){
																	$this->vars['error'] = __('Please empty character inventory');
																}
																if(isset($this->vars['plugin_config']['reg_empty_exp_inventory']) && $this->vars['plugin_config']['reg_empty_exp_inventory'] == 1 && ($this->pluginaizer->Mtransfer_char->check_exp_inv1($this->pluginaizer->session->userdata(['user' => 'server'])) == false || $this->pluginaizer->Mtransfer_char->check_exp_inv2($this->pluginaizer->session->userdata(['user' => 'server'])) == false)){
																	$this->vars['error'] = __('Please empty character expanded inventory');
																}
																if(isset($this->vars['plugin_config']['reg_empty_personal_store']) && $this->vars['plugin_config']['reg_empty_personal_store'] == 1 && $this->pluginaizer->Mtransfer_char->check_store($this->pluginaizer->session->userdata(['user' => 'server'])) == false){
																	$this->vars['error'] = __('Please empty character personal store');
																}
																$space = $this->pluginaizer->Mtransfer_char->check_free_slot($this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																if(!$space){
																	$this->vars['error'] = __('All character slots are used. Please remove some character.');
																}
																else{
																	if($this->vars['plugin_config']['price'] > 0){
																		$this->status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['price_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
																		if($this->status['credits'] < $this->vars['plugin_config']['price']){
																			$this->vars['error'] = sprintf(__('You have insufficient amount of %s'), $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['price_type'], $this->pluginaizer->session->userdata(['user' => 'server'])));
																		}
																	}
																	if(!isset($this->vars['error'])){
																		if($this->pluginaizer->Mtransfer_char->add_to_account_character($space, $this->pluginaizer->Mtransfer_char->char_info['Name'], $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server'])) != false){                                                              
																			$this->pluginaizer->website->charge_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['price'], $this->vars['plugin_config']['price_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
																			$this->pluginaizer->Mtransfer_char->update_account_character($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->add_account_log('Transfered Character '.$this->pluginaizer->Mtransfer_char->char_info['Name'].' to '.$this->pluginaizer->Mtransfer_char->vars['username'].'', -$this->vars['plugin_config']['price'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->add_account_log('Received Character '.$this->pluginaizer->Mtransfer_char->char_info['Name'].' from '.$this->pluginaizer->session->userdata(['user' => 'username']).'', 0, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_character($this->pluginaizer->Mtransfer_char->char_info['id'], $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			if(isset($this->vars['plugin_config']['delete_ruud']) && $this->vars['plugin_config']['delete_ruud'] == 1){
																				$this->pluginaizer->Mtransfer_char->remove_ruud($this->pluginaizer->Mtransfer_char->char_info['id'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			}
																			if(isset($this->vars['plugin_config']['delete_zen']) && $this->vars['plugin_config']['delete_zen'] == 1){
																				$this->pluginaizer->Mtransfer_char->remove_zen($this->pluginaizer->Mtransfer_char->char_info['id'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			}
																			$this->pluginaizer->Mtransfer_char->update_IGC_PeriodExpiredItemInfo($account['memb_guid'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_IGC_PeriodItemInfo($account['memb_guid'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_IGC_PentagramInfo($account['memb_guid'], $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_T_LUCKY_ITEM_INFO($account['memb_guid'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_T_MuRummy(true, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_T_MuRummyInfo(true, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_T_MuRummyLog(true, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_T_PentagramInfo(true, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_T_PSHOP_ITEMVALUE_INFO(true, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_PetWarehouse(true, $this->pluginaizer->Mtransfer_char->vars['username'], $this->pluginaizer->session->userdata(['user' => 'server']));
																			$this->pluginaizer->Mtransfer_char->update_DmN_User_Achievements(true, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->pluginaizer->Mtransfer_char->char_info['id']);
																			
																			if(isset($this->vars['plugin_config']['empty_s16_pstore']) && $this->vars['plugin_config']['empty_s16_pstore'] == 1){
																				$this->pluginaizer->Mtransfer_char->remove_items_from_pstore($this->pluginaizer->session->userdata(['user' => 'server']));
																			}
																			$this->vars['success'] = __('Character transfered successfully.');
																		}
																		else{
																			$this->vars['error'] = __('User should create atleast 1 character in account before purchase.');
																		}
																	}
																}
															} 
														}
													}
												}												
											}             
                                        }             
                                    }
                                }
                            }
                        }
                        $this->vars['char_list'] = $this->pluginaizer->Mtransfer_char->load_char_list($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/transfer_char.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.transfer_char', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }

        /**
         *
         * Load public module data
         *
         * return mixed
         *
         */
        private function public_module()
        {
            // public module not used in this plugin
        }

        /**
         *
         * Main admin module body
         * All main things related to admincp
         *
         *
         * Return mixed
         */
        public function admin()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                $this->load->model('application/plugins/transfer_char/models/transfer_char');
                $this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                //load any js, css files if required
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/transfer_char.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
        }

        /**
         *
         * Save plugin settings
         *
         *
         * Return mixed
         */
        public function save_settings()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                if(isset($_POST['server']) && $_POST['server'] != 'all'){
                    foreach($_POST AS $key => $val){
                        if($key != 'server'){
                            $this->vars['plugin_config'][$_POST['server']][$key] = $val;
                        }
                    }
                } else{
                    foreach($_POST AS $key => $val){
                        if($key != 'server'){
                            $this->vars['plugin_config'][$key] = $val;
                        }
                    }
                }
                if($this->pluginaizer->save_config($this->vars['plugin_config'])){
                    echo $this->pluginaizer->jsone(['success' => 'Plugin configuration successfully saved']);
                } else{
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                }
            }
        }

        /**
         *
         * Plugin installer
         * Admin module for plugin installation
         * Set plugin data, create plugin config template, create sql schemes
         *
         */
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		 
        public function install()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //create plugin info
                $this->pluginaizer->set_about()->add_plugin([
					'installed' => 1, 
					'module_url' => str_replace('_', '-', $this->pluginaizer->get_plugin_class()), //link to module
                    'admin_module_url' => str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin', //link to admincp module
                    'is_public' => 0, //if is public module or requires to login
                    'is_multi_server' => 1, //will this plugin have different config for each server, multi server is supported only by not user modules
                    'main_menu_item' => 0, //add link to module in main website menu,
                    'sidebar_user_item' => 0, //add link to module in user sidebar
                    'sidebar_public_item' => 1, //add link to module in public sidebar menu, if template supports
                    'account_panel_item' => 1, //add link in user account panel
                    'donation_panel_item' => 0, //add link in donation page
                    'description' => 'Transfer Characters' //description which will see user
                ]);
                //create plugin config template
                $this->pluginaizer->create_config(['active' => 0,  'allow_transfer_with_guild' => 1, 'allow_transfer_with_gens' => 1, 'max_character_level' => 400, 'min_character_level' => 1, 'reg_empty_inventory' => 1, 'reg_empty_exp_inventory' => 1, 'reg_empty_personal_store' => 1, 'empty_s16_pstore' => 1, 'delete_ruud' => 0, 'delete_zen' => 0, 'price' => 0, 'price_type' => 1]);
                //add sql scheme if there is any into website database
                //all schemes should be located in plugin_folder/sql_schemes
                $this->pluginaizer->add_sql_scheme('character_transfer_logs');
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    $data['error'] = $this->pluginaizer->error;
                }
                $data['success'] = 'Plugin installed successfully';
                echo $this->pluginaizer->jsone($data);
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        /**
         *
         * Plugin uninstaller
         * Admin module for plugin uninstall
         * Remove plugin data, delete plugin config, delete sql schemes
         *
         */
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		 
        public function uninstall()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //delete plugin config and remove plugin data
                $this->pluginaizer->delete_config()->remove_sql_scheme('character_transfer_logs')->remove_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                }
                echo $this->pluginaizer->jsone(['success' => 'Plugin uninstalled successfully']);
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        public function enable()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //enable plugin
                $this->pluginaizer->enable_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                } else{
                    echo $this->pluginaizer->jsone(['success' => 'Plugin successfully enabled.']);
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        public function disable()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //disable plugin
                $this->pluginaizer->disable_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                } else{
                    echo $this->pluginaizer->jsone(['success' => 'Plugin successfully disabled.']);
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        public function about()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //create plugin info
                $about = $this->pluginaizer->get_about();
                if($about != false){
                    $description = '<div class="box-content">
								<dl>
								  <dt>Plugin Name</dt>
								  <dd>' . $about['name'] . '</dd>
								  <dt>Version</dt>
								  <dd>' . $about['version'] . '</dd>
								  <dt>Description</dt>
								  <dd>' . $about['description'] . '</dd>
								  <dt>Developed By</dt>
								  <dd>' . $about['developed_by'] . ' <a href="' . $about['website'] . '" target="_blank">' . $about['website'] . '</a></dd>
								</dl>            
							</div>';
                } else{
                    $description = '<div class="alert alert-info">Unable to find plugin description.</div>';
                }
                echo $this->pluginaizer->jsone(['about' => $description]);
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

    }