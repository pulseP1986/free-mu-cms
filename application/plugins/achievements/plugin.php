<?php
    //in_file();

    class _plugin_achievements extends controller implements pluginInterface
    {
        private $pluginaizer;
        private $vars = [];
		public $errors = [];

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
        public function index()
        {
            if($this->pluginaizer->data()->value('installed') == false){
                throw new Exception('Plugin has not yet been installed.');
            } else{
                if($this->pluginaizer->data()->value('installed') == 1){
                    if($this->pluginaizer->data()->value('is_public') == 0){
                        $this->user_module();
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
        private function user_module()
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
                        $this->load->model('application/plugins/achievements/models/achievements');
                        $this->vars['characters'] = $this->pluginaizer->Machievements->load_char_list($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.index', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function unlock($id)
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
						try{
							$this->load->model('application/plugins/achievements/models/achievements');
							if($this->pluginaizer->Machievements->checkUnlocked($id, $this->pluginaizer->session->userdata(['user' => 'server'])) != false){
								$this->pluginaizer->redirect($this->config->base_url . 'achievements/view/'.$id);
							}
							else{
								$this->pluginaizer->Machievements->char_info($id, $this->pluginaizer->session->userdata(['user' => 'server']), true);
								$checkStatus = false;
								if($this->pluginaizer->Machievements->char_info == false){
									throw new Exception(__('Invalid character'));
								}
								if($this->vars['plugin_config']['required_level'] > $this->pluginaizer->Machievements->char_info['cLevel'] && $this->pluginaizer->Machievements->char_info['resets'] == 0 && $this->pluginaizer->Machievements->char_info['grand_resets'] == 0){
									throw new Exception(sprintf(__('Character level required: %d'), $this->vars['plugin_config']['required_level']));
								}
								if($this->vars['plugin_config']['required_mlevel'] > 0){
									if($this->vars['plugin_config']['required_mlevel'] > $this->pluginaizer->Machievements->char_info['mlevel']){
										throw new Exception(sprintf(__('Character master level required: %d'), $this->vars['plugin_config']['required_mlevel']));
									}
								}
								if($this->vars['plugin_config']['required_resets'] > 0){
									if($this->vars['plugin_config']['required_resets'] > $this->pluginaizer->Machievements->char_info['resets'] && $this->pluginaizer->Machievements->char_info['grand_resets'] == 0){
										throw new Exception(sprintf(__('Character resets required: %d'), $this->vars['plugin_config']['required_resets']));
									}
								}
								if($this->vars['plugin_config']['required_gresets'] > 0){
									if($this->vars['plugin_config']['required_gresets'] > $this->pluginaizer->Machievements->char_info['grand_resets']){
										throw new Exception(sprintf(__('Character grand resets required: %s'), $this->pluginaizer->website->zen_format($this->vars['plugin_config']['required_gresets'])));
									}
								}
								if($this->vars['plugin_config']['required_zen'] > 0){
									$checkStatus = true;
									if($this->vars['plugin_config']['required_zen'] > $this->pluginaizer->Machievements->char_info['Money']){
										throw new Exception(sprintf(__('Character zen required: %s'), $this->pluginaizer->website->zen_format($this->vars['plugin_config']['required_zen'])));
									}
								}
								if(MU_VERSION >= 5){
									if($this->vars['plugin_config']['required_ruud'] > 0){
										$checkStatus = true;
										if($this->vars['plugin_config']['required_ruud'] > $this->pluginaizer->Machievements->char_info['Ruud']){
											throw new Exception(sprintf(__('Character Ruud required: %s'), $this->pluginaizer->website->zen_format($this->vars['plugin_config']['required_ruud'])));
										}
									}
								}
								
								if($checkStatus == true){
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
								}
								
								$this->pluginaizer->Machievements->unlockAchievements($id, $this->pluginaizer->session->userdata(['user' => 'server']));
								
								if($this->vars['plugin_config']['required_zen'] > 0){
									$this->pluginaizer->Machievements->remove_zen($this->vars['plugin_config']['required_zen'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
								}
								if(MU_VERSION >= 5){
									if($this->vars['plugin_config']['required_ruud'] > 0){
										$this->pluginaizer->Machievements->remove_ruud($this->vars['plugin_config']['required_ruud'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								}
								$this->pluginaizer->redirect($this->config->base_url . 'achievements/view/'.$id);
							}
						}
						catch(Exception $e){
							$this->vars['error'] = $e->getMessage();
						}
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.unlock', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function rankings($server = '')
        {
			//load website helper
			$this->load->helper('website');
			//load paginator
			$this->load->lib('pagination');
			//load plugin config
			if($server == ''){
				$server = array_keys($this->pluginaizer->website->server_list());
				$this->vars['server'] = $server[0];
			}
			else{
				$this->vars['server'] = $server;
			}
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
				if($this->pluginaizer->data()->value('is_multi_server') == 1){
					if(array_key_exists($this->vars['server'], $this->vars['plugin_config'])){
						$this->vars['plugin_config'] = $this->vars['plugin_config'][$this->vars['server']];
						$this->vars['about'] = $this->pluginaizer->get_about();
						$this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
					} else{
						$this->vars['config_not_found'] = __('Plugin configuration not found.');
					}
				}
				if($this->vars['plugin_config']['active'] == 0){
					$this->vars['module_disabled'] = __('This module has been disabled.');
				} else{
					
						$this->load->model('application/plugins/achievements/models/achievements');
						$this->vars['rankings'] = $this->Machievements->get_rankings($this->vars['server'], $this->vars['plugin_config']['rankings_amount'], $this->vars['plugin_config']['rankings_cache_time']);
					
				}
			} else{
				$this->vars['config_not_found'] = __('Plugin configuration not found.');
			}
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.rankings', $this->vars);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function view($id)
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
						$this->vars['id'] = $id;
						try{
							$this->load->model('application/plugins/achievements/models/achievements');
							if($this->pluginaizer->Machievements->checkUnlocked($id, $this->pluginaizer->session->userdata(['user' => 'server'])) == false){
								$this->pluginaizer->redirect($this->config->base_url . 'achievements/unlock/'.$id);
							}
							else{
								$this->vars['archievement_list'] = $this->config->values('achievement_list', $this->pluginaizer->session->userdata(['user' => 'server']));
								if(empty($this->vars['archievement_list'])){
									throw new Exception(__('No achievements found.'));
								}
								
								$this->pluginaizer->Machievements->char_info($id, $this->pluginaizer->session->userdata(['user' => 'server']), true);
								
								if($this->pluginaizer->Machievements->char_info == false){
									throw new Exception(__('Invalid character'));
								}
									
								foreach($this->vars['archievement_list'] AS $key => $achievement){
									if($achievement['class'] != ''){
										if(!in_array($this->pluginaizer->Machievements->char_info['Class'], $achievement['class'])){
											unset($this->vars['archievement_list'][$key]);
											continue;
										}
									}
									$data = $this->pluginaizer->Machievements->checkAchievementStatus($achievement, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									if($data != false){
										$this->vars['archievement_list'][$key]['full_amount'] = $data['amount'];
										$this->vars['archievement_list'][$key]['complete_amount'] = $data['amount_completed'];
										$this->vars['archievement_list'][$key]['items_left'] = json_decode($data['items'], true);
										$this->vars['archievement_list'][$key]['completed'] = $data['is_completed'];
									}
								}
							}
						}
						catch(Exception $e){
							$this->vars['error'] = $e->getMessage();
						}
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.view', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function check($id, $achId){
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
						$this->vars['id'] = $id;
						$this->vars['achid'] = $achId;
						try{
							$this->load->model('application/plugins/achievements/models/achievements');
							if($this->pluginaizer->Machievements->checkUnlocked($id, $this->pluginaizer->session->userdata(['user' => 'server'])) == false){
								$this->pluginaizer->redirect($this->config->base_url . 'achievements/unlock/'.$id);
							}
							else{
								$this->vars['archievement_list'] = $this->config->values('achievement_list', $this->pluginaizer->session->userdata(['user' => 'server']));
								
								if(empty($this->vars['archievement_list'])){
									throw new Exception(__('No achievements found.'));
								}
								
								$this->pluginaizer->Machievements->char_info($id, $this->pluginaizer->session->userdata(['user' => 'server']), true);
								
								if($this->pluginaizer->Machievements->char_info == false){
									throw new Exception(__('Invalid character'));
								}
								
								$this->vars['char_info'] = $this->pluginaizer->Machievements->char_info;
								
								$this->vars['achKey'] = -1;		
								
								foreach($this->vars['archievement_list'] AS $key => $achievement){
									if($achievement['id'] == $this->vars['achid']){
										$this->vars['achKey'] = $key;
										break;
									}
								}
								
								if($this->vars['achKey'] == -1){
									$this->vars['config_not_found'] = 'Archievement not found.';
								}
								else{
									$this->vars['achData'] = $this->vars['archievement_list'][$this->vars['achKey']];
								}
								
								if($this->vars['achData']['class'] != ''){
									if(!in_array($this->pluginaizer->Machievements->char_info['Class'], $this->vars['achData']['class'])){
										throw new Exception(__('Invalid achievement'));
									}
								}

								if(isset($this->vars['achData']['min_lvl']) && $this->vars['achData']['min_lvl'] > 0){
									if($this->vars['achData']['min_lvl'] > $this->pluginaizer->Machievements->char_info['cLevel'])
										throw new Exception(sprintf(__('Min level required %d'), $this->vars['achData']['min_lvl'])); 
								}
								
								if(isset($this->vars['achData']['min_mlvl']) && $this->vars['achData']['min_mlvl'] > 0){
									if($this->vars['achData']['min_mlvl'] > $this->pluginaizer->Machievements->char_info['mlevel'])
										throw new Exception(sprintf(__('Min master level required %d'), $this->vars['achData']['min_mlvl'])); 
								}

								if(isset($this->vars['achData']['min_res']) && $this->vars['achData']['min_res'] > 0){
									if($this->vars['achData']['min_res'] > $this->pluginaizer->Machievements->char_info['resets'])
										throw new Exception(sprintf(__('Min resets required %d'), $this->vars['achData']['min_res'])); 
								}

								if(isset($this->vars['achData']['min_gres']) && $this->vars['achData']['min_gres'] > 0){
									if($this->vars['achData']['min_gres'] > $this->pluginaizer->Machievements->char_info['grand_resets'])
										throw new Exception(sprintf(__('Min grand resets required %d'), $this->vars['achData']['min_gres'])); 
								}
								
								$this->load->lib('iteminfo');
								$this->load->lib('itemimage');
								$this->load->lib('createitem', [MU_VERSION, SOCKET_LIBRARY, $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size')]);
								$this->load->model('shop');
								
								$this->vars['archievement_rewards'] = $this->config->values('achievement_rewards', $this->pluginaizer->session->userdata(['user' => 'server']));
								
								if(!empty($this->vars['archievement_rewards'][$achId]['items'])){
									$this->vars['reward_items'] = [];
									
									foreach($this->vars['archievement_rewards'][$achId]['items'] AS $ritem){
										if($this->pluginaizer->iteminfo->setItemData($ritem['id'], $ritem['cat'], $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){	
											$this->pluginaizer->createitem->setItemData($this->pluginaizer->iteminfo->item_data);
											$this->pluginaizer->createitem->id($ritem['id']);
											$this->pluginaizer->createitem->cat($ritem['cat']);
											$this->pluginaizer->createitem->refinery(false);
											
											if($ritem['expires'] != ''){
												$this->pluginaizer->createitem->expirable();
											}
											if(isset($ritem['dur']) && $ritem['dur'] != ''){
												$this->pluginaizer->createitem->dur($ritem['dur']);
											}

											$this->pluginaizer->createitem->serial(0);
											if($this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size') == 64){
												$this->createitem->serial2(true);
											}
											if($ritem['lvl'] != ''){
												$this->pluginaizer->createitem->lvl($ritem['lvl']);
											}
											else{
												$this->pluginaizer->createitem->lvl(0);
											}
											if($ritem['skill'] != '' && $ritem['skill'] == 1){
												$this->pluginaizer->createitem->skill(true);
											}
											else{
												$this->pluginaizer->createitem->skill(false);
											}
											if($ritem['luck'] != '' && $ritem['luck'] == 1){
												$this->pluginaizer->createitem->luck(true);
											}
											else{
												$this->pluginaizer->createitem->luck(false);
											}
											if($ritem['opt'] != ''){
												$this->pluginaizer->createitem->opt($ritem['opt']);
											}
											else{
												$this->pluginaizer->createitem->opt(0);
											}
											if($ritem['exe'] != ''){
												$exe_opts = [0 => 1, 1 => 2, 2 => 4, 3 => 8, 4 => 16, 5 => 32];
												$iexe = explode(',', $ritem['exe']);
												$exe = 0;
												
												foreach($iexe AS $k => $val){
													if($val == 0){
														unset($iexe[$k]);
													}
												}
												
												if(!empty($iexe)){		
													foreach($iexe as $key => $exe_opt){
														if($exe_opt == 1){
															$exe += $exe_opts[$key];
														}
													}
												}
												$this->pluginaizer->createitem->addStaticExe($exe);
											}
											else{
												$this->pluginaizer->createitem->addStaticExe(0);
											}
											if($ritem['anc'] != '' && $ritem['anc'] != 0){
												$this->pluginaizer->createitem->ancient($ritem['anc']);
											}

											$itemHex = $this->pluginaizer->createitem->to_hex();
											$this->pluginaizer->iteminfo->itemData($itemHex, true, $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->vars['reward_items'][] = [
												'hex' => $itemHex,
												'name' => $this->pluginaizer->iteminfo->getNameStyle(true),
												'serial' => 00000000,
												'expires' => $ritem['expires'],
												'itemtype' => $this->pluginaizer->iteminfo->itemIndex($ritem['cat'], $ritem['id']),
												'item_info' => $this->itemimage->load($this->iteminfo->id, $this->iteminfo->type, (int)substr($this->iteminfo->getLevel(), 1)) . '<br />' . $this->iteminfo->allInfo()
											];
										}
									}
								}
								
								$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
								
								if($this->vars['achDataDB'] == false){
									header('Location: '.$this->config->base_url.'achievements/view/'.$id.'');
								}
								
								if(isset($_POST['get_reward'])){
									if($this->vars['achDataDB']['is_completed'] != 1){
										throw new Exception(__('Please complete achievement first.'));
									}
									
									$claimed = false;
									if(isset($this->vars['archievement_rewards'][$achId]['credits1']) && $this->vars['archievement_rewards'][$achId]['credits1'] > 0){
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'credits1');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$this->pluginaizer->website->add_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['archievement_rewards'][$achId]['credits1'], 1, false, $this->pluginaizer->Machievements->get_guid($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])));
											$this->pluginaizer->Machievements->add_account_log('Received ' . $this->pluginaizer->website->translate_credits(1, $this->pluginaizer->session->userdata(['user' => 'server'])).' for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['credits1'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'credits1');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['credits2']) && $this->vars['archievement_rewards'][$achId]['credits2'] > 0){
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'credits2');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$this->pluginaizer->website->add_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['archievement_rewards'][$achId]['credits2'], 2, false, $this->pluginaizer->Machievements->get_guid($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])));
											$this->pluginaizer->Machievements->add_account_log('Received ' . $this->pluginaizer->website->translate_credits(2, $this->pluginaizer->session->userdata(['user' => 'server'])).' for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['credits1'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'credits2');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['wcoin']) && $this->vars['archievement_rewards'][$achId]['wcoin'] > 0){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
											
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'wcoin');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											if(!isset($this->vars['table_config']['wcoins']))
												throw new Exception(__('WCoins configuration not found'));
											if($this->vars['table_config']['wcoins']['table'] == '')
												throw new Exception(__('WCoins configuration not found'));
											$acc = (in_array($this->vars['table_config']['wcoins']['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->pluginaizer->session->userdata(['user' => 'id']) : $this->pluginaizer->session->userdata(['user' => 'username']);
											$this->pluginaizer->Machievements->add_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['archievement_rewards'][$achId]['wcoin'], $this->vars['table_config']['wcoins']);
											$this->pluginaizer->Machievements->add_account_log('Received WCoins for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['wcoin'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'wcoin');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['goblin']) && $this->vars['archievement_rewards'][$achId]['goblin'] > 0){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
											
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'goblin');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											if(!isset($this->vars['table_config']['goblinpoint']))
												throw new Exception(__('GoblinPoint configuration not found'));
											if($this->vars['table_config']['goblinpoint']['table'] == '')
												throw new Exception(__('GoblinPoint configuration not found'));
											$acc = (in_array($this->vars['table_config']['goblinpoint']['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->pluginaizer->session->userdata(['user' => 'id']) : $this->pluginaizer->session->userdata(['user' => 'username']);
											$this->pluginaizer->Machievements->add_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['archievement_rewards'][$achId]['goblin'], $this->vars['table_config']['goblinpoint']);
											$this->pluginaizer->Machievements->add_account_log('Received GoblinPoint for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['goblin'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'goblin');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['zen']) && $this->vars['archievement_rewards'][$achId]['zen'] > 0){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
											
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'zen');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$zen = $this->pluginaizer->Machievements->checkZen($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											if(($zen['Money'] + $this->vars['archievement_rewards'][$achId]['zen']) > 2000000000){
												throw new Exception('Zen limit reached on character.');
											}
											$this->pluginaizer->Machievements->add_zen($this->vars['archievement_rewards'][$achId]['zen'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->add_account_log('Received Zen for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['zen'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'zen');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['credits3']) && $this->vars['archievement_rewards'][$achId]['credits3'] > 0){
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'credits3');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$this->pluginaizer->website->add_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['archievement_rewards'][$achId]['credits3'], 3, false, $this->pluginaizer->Machievements->get_guid($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])));
											$this->pluginaizer->Machievements->add_account_log('Received ' . $this->pluginaizer->website->translate_credits(3, $this->pluginaizer->session->userdata(['user' => 'server'])).' for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['credits1'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'credits3');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['ruud']) && $this->vars['archievement_rewards'][$achId]['ruud'] > 0){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
											
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'ruud');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$ruud = $this->pluginaizer->Machievements->checkRuud($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											if(($ruud['Ruud'] + $this->vars['archievement_rewards'][$achId]['ruud']) > 2000000000){
												throw new Exception('Ruud limit reached on character.');
											}
											$this->pluginaizer->Machievements->add_ruud($this->vars['archievement_rewards'][$achId]['ruud'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->add_account_log('Received Ruud for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['ruud'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'ruud');							
											$claimed = true;
										}								
									}
									if(isset($this->vars['archievement_rewards'][$achId]['hunt']) && $this->vars['archievement_rewards'][$achId]['hunt'] > 0){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
											
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'hunt');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$this->pluginaizer->Machievements->add_hunt($this->vars['archievement_rewards'][$achId]['hunt'], $this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->add_account_log('Received HunPoint for achievement: '.$this->vars['achData']['title'].'', $this->vars['archievement_rewards'][$achId]['hunt'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'hunt');							
											$claimed = true;
										}								
									}
									
									if(!empty($this->vars['archievement_rewards'][$achId]['items'])){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'items');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										
										if($status['reward'] != 1){
											$this->pluginaizer->Machievements->inventory($id, $this->pluginaizer->session->userdata(['user' => 'server']));
											$items = $this->pluginaizer->Machievements->getInventoryContents($this->pluginaizer->session->userdata(['user' => 'server']));
											$itemsList = implode('', $items);
											$itemInfo = $this->pluginaizer->iteminfo;
											$itemArr = str_split($itemsList, $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'));
											$takenSlots = [];
											$expirableItems = [];
											foreach($this->vars['reward_items'] AS $ritems){
												$this->pluginaizer->iteminfo->itemData($ritems['hex'], true, $this->pluginaizer->session->userdata(['user' => 'server']));
												$space = $this->pluginaizer->Machievements->check_space_inventory($itemArr, $this->pluginaizer->iteminfo->getX(), $this->pluginaizer->iteminfo->getY(), 64, $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'), 8, 8, false, $itemInfo, $takenSlots);
												
												if($space === null){
													throw new Exception($this->Machievements->errors[0]);
												}
												$takenSlots[$space] = $space;
												$itemArr[$space] = $ritems['hex'];
												if($ritems['expires'] != ''){
													$expirableItems[] = [
														'index' => $ritems['itemtype'],
														'time' => $ritems['expires'],
														'serial' => $ritems['serial']
													];
												}													
											}
											
											$newInv = $this->pluginaizer->Machievements->addItemsToInventory($itemArr, $this->pluginaizer->session->userdata(['user' => 'server']));
											if(!empty($expirableItems)){
												$currTime = time();
												foreach($expirableItems AS $expideData){
													$this->pluginaizer->Machievements->addExpirableItem($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->pluginaizer->Machievements->char_info['Name'], $expideData['index'], $expideData['time'], $expideData['serial'], $currTime);
												}
											}
											$this->pluginaizer->Machievements->updateInventory($id, $this->pluginaizer->session->userdata(['user' => 'server']), $newInv);
											$this->pluginaizer->Machievements->add_account_log('Received Items for achievement: '.$this->vars['achData']['title'].'', 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'items');
											$claimed = true;
										}
									}
									
									if(isset($this->vars['archievement_rewards'][$achId]['vip_type']) && $this->vars['archievement_rewards'][$achId]['vip_type'] != ''){
										if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
											throw new Exception(__('Please logout from game.'));
										}
											
										$status = $this->pluginaizer->Machievements->checkClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'vip');
										if($status == false){
											$this->pluginaizer->Machievements->insertRewardData($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$status['reward'] = 0;	
										}
										if($status['reward'] != 1){
											$vip_config = $this->pluginaizer->config->values('vip_config');
											$vip_query_config = $this->pluginaizer->config->values('vip_query_config');
											$table_config = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->load->model('shop');
											$this->load->model('account');
											
											$this->vars['vip_data'] = $this->pluginaizer->Mshop->check_vip($this->vars['archievement_rewards'][$achId]['vip_type'], $this->pluginaizer->session->userdata(['user' => 'server']));
											$viptime = time() + $this->vars['vip_data']['vip_time'];
											if($this->vars['existing'] = $this->pluginaizer->Mshop->check_existing_vip_package()){
												if($this->vars['existing']['viptime'] > time()){
													$viptime = $this->vars['existing']['viptime'] + $this->vars['vip_data']['vip_time'];
												}
												$this->pluginaizer->Mshop->update_vip_package($this->vars['archievement_rewards'][$achId]['vip_type'], $viptime);
												$this->pluginaizer->Mshop->add_server_vip($viptime, $this->vars['vip_data']['server_vip_package'], $this->vars['vip_data']['connect_member_load'], $vip_query_config);
												$this->pluginaizer->Maccount->set_vip_session($viptime, $this->vars['vip_data']);		
											}
											else{
												$this->pluginaizer->Mshop->insert_vip_package($this->vars['archievement_rewards'][$achId]['vip_type'], $viptime);
												$this->pluginaizer->Mshop->add_server_vip($viptime, $this->vars['vip_data']['server_vip_package'], $this->vars['vip_data']['connect_member_load'], $vip_query_config);
												$this->pluginaizer->Maccount->set_vip_session($viptime, $this->vars['vip_data']);		
											}	
											
											$this->pluginaizer->Machievements->add_account_log('Received Vip for achievement: '.$this->vars['achData']['title'], 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->pluginaizer->Machievements->setClaimedReward($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 'vip');							
											$claimed = true;
										}								
									}
									
									if($claimed == true){
										$this->vars['success'] = __('Rewards successfully claimed.');
									}
									else{
										throw new Exception('All rewards already claimed.');
									}
								}
								
								if($this->vars['achDataDB']['ach_type'] == 9){
									$this->vars['item_list'] = [];
									$this->vars['dbItems'] = json_decode($this->vars['achDataDB']['items'], true);
									if(!empty($this->vars['dbItems'])){
										foreach($this->vars['dbItems'] AS $itemKey => $item){
											if($this->pluginaizer->iteminfo->setItemData($item['id'], $item['cat'], $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){	
												$this->pluginaizer->createitem->setItemData($this->pluginaizer->iteminfo->item_data);
												$this->pluginaizer->createitem->id($item['id']);
												$this->pluginaizer->createitem->cat($item['cat']);
												$this->pluginaizer->createitem->refinery(false);
												$this->pluginaizer->createitem->serial(0);
												if($this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size') == 64){
													$this->createitem->serial2(true);
												}
												if($item['lvl'] != ''){
													$this->pluginaizer->createitem->lvl($item['lvl']);
												}
												else{
													$this->pluginaizer->createitem->lvl(0);
												}
												if($item['skill'] != '' && $item['skill'] == 1){
													$this->pluginaizer->createitem->skill(true);
												}
												else{
													$this->pluginaizer->createitem->skill(false);
												}
												if($item['luck'] != '' && $item['luck'] == 1){
													$this->pluginaizer->createitem->luck(true);
												}
												else{
													$this->pluginaizer->createitem->luck(false);
												}
												if($item['opt'] != ''){
													$this->pluginaizer->createitem->opt($item['opt']);
												}
												else{
													$this->pluginaizer->createitem->opt(0);
												}
												if($item['exe'] != ''){
													$exe_opts = [0 => 1, 1 => 2, 2 => 4, 3 => 8, 4 => 16, 5 => 32];
													$iexe = explode(',', $item['exe']);
													$exe = 0;

													foreach($iexe AS $k => $val){
														if($val == 0){
															unset($iexe[$k]);
														}
													}
													
													if(!empty($iexe)){		
														foreach($iexe as $key => $exe_opt){
															if($exe_opt == 1){
																$exe += $exe_opts[$key];
															}
														}
													}
													$this->pluginaizer->createitem->addStaticExe($exe);
													
												}
												if($item['anc'] != '' && $item['anc'] != 0){
													$this->pluginaizer->createitem->ancient($item['anc']);
												}

												$itemHex = $this->pluginaizer->createitem->to_hex();
												$this->pluginaizer->iteminfo->itemData($itemHex, true, $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['item_list'][] = [
													'hex' => $itemHex,
													'name' => $this->pluginaizer->iteminfo->getNameStyle(true),
													'amount' => $item['amount'],
													'data' => $item,
													'itemKey' => $itemKey,
													'item_info' => $this->itemimage->load($this->iteminfo->id, $this->iteminfo->type, (int)substr($this->iteminfo->getLevel(), 1)) . '<br />' . $this->iteminfo->allInfo()
												];
											}
										}
									}
								}

								if($this->vars['achDataDB']['ach_type'] == 7){
									$this->load->lib('npc');
									$this->vars['monster_list'] = [];
									if(!empty($this->vars['achData']['monsters'])){
										foreach($this->vars['achData']['monsters'] AS $monster){
											$this->vars['monster_list'][] = $this->pluginaizer->npc->name_by_id($monster);
										}
									}
								}
								
								if(isset($_POST['check_status'])){
									switch($this->vars['achDataDB']['ach_type']){
										case 0:
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										break;
										case 1:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$zen = $this->pluginaizer->Machievements->checkZen($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											
											if($required > 0){
												if(defined('ELITEMU_ACH_REQ_ALL_ITEMS') && ELITEMU_ACH_REQ_ALL_ITEMS == true){
													if($required > $zen['Money']){
														throw new Exception(__('Not enough zen in inventory'));
													}
												}										  
												if($required > $zen['Money']){
													$money = $completed + $zen['Money'];
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_zen($zen['Money'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													}
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $zen['Money']){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_zen($money, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													}
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));	
											}
										break;
										case 2:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$ruud = $this->pluginaizer->Machievements->checkRuud($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											
											if($required > 0){
												if($required > $ruud['Ruud']){
													$money = $completed + $ruud['Ruud'];
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_ruud($ruud['Ruud'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													}
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $ruud['Ruud']){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_ruud($money, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													}
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 3:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['wcoins']))
												throw new Exception(__('WCoins configuration not found'));
											if($this->vars['table_config']['wcoins']['table'] == '')
												throw new Exception(__('WCoins configuration not found'));
											$acc = (in_array($this->vars['table_config']['wcoins']['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->pluginaizer->session->userdata(['user' => 'id']) : $this->pluginaizer->session->userdata(['user' => 'username']);
											$wcoins = $this->pluginaizer->Machievements->get_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['wcoins']);
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											
											if($required > 0){
												if($required > $wcoins){
													$money = $completed + $wcoins;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $wcoins, $this->vars['table_config']['wcoins']);
													}
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $wcoins){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $money, $this->vars['table_config']['wcoins']);
													}
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 4:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['goblinpoint']))
												throw new Exception(__('GoblinPoint configuration not found'));
											if($this->vars['table_config']['goblinpoint']['table'] == '')
												throw new Exception(__('GoblinPoint configuration not found'));
											$acc = (in_array($this->vars['table_config']['goblinpoint']['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->pluginaizer->session->userdata(['user' => 'id']) : $this->pluginaizer->session->userdata(['user' => 'username']);
											$wcoins = $this->pluginaizer->Machievements->get_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['goblinpoint']);
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											
											if($required > 0){
												if($required > $wcoins){
													$money = $completed + $wcoins;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $wcoins, $this->vars['table_config']['goblinpoint']);
													}
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $wcoins){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													if($this->vars['achData']['decreaset_amount'] == 1){
														$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $money, $this->vars['table_config']['goblinpoint']);
													}
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 5:										
											$votes = $this->pluginaizer->Machievements->check_votes($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$votesOther = $this->pluginaizer->Machievements->check_votes_other($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											if($votesOther['votes'] == NULL){
												$votesOther['votes'] = 0;
											}
											$votes['votes'] = $votes['votes'] - $votesOther['votes'];
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$votes['votes'] = $votes['votes'] - $completed;
											if($votes['votes'] < 0){
												$votes['votes'] = 0;
											}
											if($required > 0){
												if($required > $votes['votes']){
													$money = $completed + $votes['votes'];
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $votes['votes']){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 6:
											$donations = $this->pluginaizer->Machievements->check_donations($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											
											$donationsOther = $this->pluginaizer->Machievements->check_donations_other($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											if($donationsOther['donations'] == NULL){
												$donationsOther['donations'] = 0;
											}
											$donations = $donations - $donationsOther['donations'];
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$donations = $donations - $completed;
											//print_r($donations);die();
											if($donations < 0){
												$donations = 0;
											}
											if($required > 0){
												if($required > $donations){
													$money = $completed + $donations;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $donations){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 7:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$monsters = $this->pluginaizer->Machievements->check_monsters($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['monsters']);
											if($monsters == NULL)
												$monsters = 0;
											
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$monsters = $monsters - $completed;
											if($monsters < 0){
												$monsters = 0;
											}
											if($required > 0){
												if($required > $monsters){
													$money = $completed + $monsters;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $monsters){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 8:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$unique = 0;
											$minRes = 0;
											if(isset($this->vars['achData']['unique']) && $this->vars['achData']['unique'] != ''){
												$uniqueData = explode('|', $this->vars['achData']['unique']);
												$unique = $uniqueData[0];
												$minRes = $uniqueData[1];
											}
											$kills = $this->pluginaizer->Machievements->check_kills($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $unique, $minRes);
											if(is_array($kills)){
												foreach($kills AS $info){
													$this->pluginaizer->Machievements->setKillsChecked($info['Victim'], $this->pluginaizer->Machievements->char_info['Name'], $info['KillDate'], $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												$kills = count($kills);
											}

											if($kills == NULL)
												$kills = 0;
											
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$kills = $kills - $completed;
											if($kills < 0){
												$kills = 0;
											}
											if($required > 0){
												if($required > $kills){
													$money = $completed + $kills;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $kills){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));	
											}
										break;
										case 9:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->pluginaizer->load->model('warehouse');
											$this->pluginaizer->Machievements->inventory($id, $this->pluginaizer->session->userdata(['user' => 'server']));
											$items = $this->pluginaizer->Machievements->getInventoryContents($this->pluginaizer->session->userdata(['user' => 'server']));
											$slotsToRemove = false;
											foreach($this->vars['item_list'] AS $key => $item){
												 $this->pluginaizer->iteminfo->itemData($item['hex'], true, $this->pluginaizer->session->userdata(['user' => 'server']));
												 $idd = $this->pluginaizer->iteminfo->id;
												 $type = $this->pluginaizer->iteminfo->type;
												 $skill = $this->pluginaizer->iteminfo->hasSkill();
												 $lvl = (int)substr($this->pluginaizer->iteminfo->getLevel(), 1);
												 $opt = ($this->pluginaizer->iteminfo->getOption()*4);
												 $exe = $this->pluginaizer->iteminfo->exeForCompare();
												 $luck = $this->pluginaizer->iteminfo->getLuck();
												 $found = false;
												 $lvlOk = false;
												 $optOk = false;
												 $skillOk = false;
												 $luckOk = false;
												 $exe0Ok = false;
												 $exe1Ok = false;
												 $exe2Ok = false;
												 $exe3Ok = false;
												 $exe4Ok = false;
												 $exe5Ok = false; 
												 $itemCount = $item['data']['amount'];
												 $itemsNewDur = [];
												 foreach($items AS $slot => $invItem){
													 if($invItem == str_repeat('F', $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){
														 continue;
													 }
													 $itemInfo = $this->pluginaizer->Mwarehouse->load_item_info($invItem);
													 
													 if($itemInfo['info']['id'] == $idd && $itemInfo['info']['cat'] == $type){
														//if(in_array($itemInfo['info']['id'], [21,100]) && $itemInfo['info']['cat'] == 14){
															//$itemCC = $itemCount;
															//$itemCount = ($itemInfo['info']['dur'] > $itemCount) ? 0 : $itemCount - $itemInfo['info']['dur'];
															//$itemsNewDur[$slot] = ($itemInfo['info']['dur'] > $itemCount) ? $itemInfo['info']['dur'] - $itemCC : false;
														//}															
														if($item['data']['lvl'] != ''){
															if($itemInfo['info']['lvl'] >= $lvl){
																$lvlOk = true;
															}
														}
														else{
															$lvlOk = true;
														}
														if($item['data']['opt'] != ''){
															if($itemInfo['info']['opt'] >= $opt){
																$optOk = true;
															}
														}
														else{
															$optOk = true;
														}
														if($item['data']['luck'] != ''){
															if($itemInfo['info']['luck'] >= $luck){
																$luckOk = true;
															}
														}
														else{
															$luckOk = true;
														}
														if($item['data']['skill'] != ''){
															if($itemInfo['info']['skill'] >= $skill){
																$skillOk = true;
															}
														}
														else{
															$skillOk = true;
														}
														if($item['data']['exe'] != ''){
															if($itemInfo['info']['id'] == 37 && $itemInfo['info']['cat'] == 13){
																if($exe[0] == 0 && $exe[1] == 0 && $exe[2] == 0 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																	$exe0Ok = true;
																	$exe1Ok = true;
																	$exe2Ok = true;
																	$exe3Ok = true;
																	$exe4Ok = true;
																	$exe5Ok = true;
																}
																else{	
																	if($exe[0] == 1 && $exe[1] == 0 && $exe[2] == 0 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																		if($itemInfo['exe_opts'][0] == 1){
																			$exe0Ok = true;
																			$exe1Ok = true;
																			$exe2Ok = true;
																			$exe3Ok = true;
																			$exe4Ok = true;
																			$exe5Ok = true;
																		}
																	}
																	if($exe[0] == 0 && $exe[1] == 1 && $exe[2] == 0 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																		if($itemInfo['exe_opts'][1] == 1){
																			$exe0Ok = true;
																			$exe1Ok = true;
																			$exe2Ok = true;
																			$exe3Ok = true;
																			$exe4Ok = true;
																			$exe5Ok = true;
																		}
																	}
																	if($exe[0] == 0 && $exe[1] == 0 && $exe[2] == 1 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																		if($itemInfo['exe_opts'][2] == 1){
																			$exe0Ok = true;
																			$exe1Ok = true;
																			$exe2Ok = true;
																			$exe3Ok = true;
																			$exe4Ok = true;
																			$exe5Ok = true;
																		}
																	}
																}
															}
															else{
																if($itemInfo['exe_opts'][0] == 1){
																	if($exe[0] <= $itemInfo['exe_opts'][0]){
																		$exe0Ok = true;
																	}
																}
																else{
																	$exe0Ok = true;
																}
																if($exe[1] == 1){
																	if($exe[1] <= $itemInfo['exe_opts'][1]){
																		$exe1Ok = true;
																	}
																}
																else{
																	$exe1Ok = true;
																}
																if($exe[2] == 1){
																	if($exe[2] <= $itemInfo['exe_opts'][2]){
																		$exe2Ok = true;
																	}
																}
																else{
																	$exe2Ok = true;
																}
																if($exe[3] == 1){
																	if($exe[3] <= $itemInfo['exe_opts'][3]){
																		$exe3Ok = true;
																	}
																}
																else{
																	$exe3Ok = true;
																}
																
																if($exe[4] == 1){
																	if($exe[4] <= $itemInfo['exe_opts'][4]){
																		$exe4Ok = true;
																	}
																}
																else{
																	$exe4Ok = true;
																}

																if($exe[5] == 1){
																	if($exe[5] <= $itemInfo['exe_opts'][5]){
																		$exe5Ok = true;
																	}
																}
																else{
																	$exe5Ok = true;
																}
															}
														}
														else{
															$exe0Ok = true;
															$exe1Ok = true;
															$exe2Ok = true;
															$exe3Ok = true;
															$exe4Ok = true;
															$exe5Ok = true; 
														}

														if($lvlOk == true && $optOk == true && $skillOk == true && $luckOk == true && $exe0Ok == true && $exe1Ok == true && $exe2Ok == true && $exe3Ok == true && $exe4Ok == true && $exe5Ok == true && $itemCount > 0){
															$slotsToRemove[$slot] = $slot;
															
															if($itemCount > 1){
																foreach($this->vars['dbItems'] AS $k => $dbItem){
																	if($k == $item['itemKey']){									
																		$this->vars['dbItems'][$k]['amount'] = $itemCount - 1;
																	}
																}
															}
															else{														
																foreach($this->vars['dbItems'] AS $k => $dbItem){																
																	if($itemCount == 1){																	
																		if($k == $item['itemKey']){																		
																			unset($this->vars['dbItems'][$k]);
																		}
																	}
																}
															}
															$itemCount -= 1;
														}
													 }
												 }
											}
											if(defined('ELITEMU_ACH_REQ_ALL_ITEMS') && ELITEMU_ACH_REQ_ALL_ITEMS == true){
												if($itemCount != 0){
													throw new Exception(__('Missing some of items in inventory'));
												}
											}
											if($slotsToRemove != false){
												$newItems = $this->pluginaizer->Machievements->updateInventorySlots($slotsToRemove, $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->pluginaizer->Machievements->updateInventory($id, $this->pluginaizer->session->userdata(['user' => 'server']), $newItems);
												$this->pluginaizer->Machievements->updateUserAchievementItems($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['dbItems']);
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['item_list'] = [];
												$this->vars['dbItems'] = json_decode($this->vars['achDataDB']['items'], true);
												if(!empty($this->vars['dbItems'])){
													foreach($this->vars['dbItems'] AS $item){
														if($this->pluginaizer->iteminfo->setItemData($item['id'], $item['cat'], $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){	
															$this->pluginaizer->createitem->setItemData($this->pluginaizer->iteminfo->item_data);
															$this->pluginaizer->createitem->id($item['id']);
															$this->pluginaizer->createitem->cat($item['cat']);
															$this->pluginaizer->createitem->refinery(false);
															$this->pluginaizer->createitem->serial(0);
															if($this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size') == 64){
																$this->createitem->serial2(true);
															}
															if($item['lvl'] != ''){
																$this->pluginaizer->createitem->lvl($item['lvl']);
															}
															else{
																$this->pluginaizer->createitem->lvl(0);
															}
															if($item['skill'] != '' && $item['skill'] == 1){
																$this->pluginaizer->createitem->skill(true);
															}
															else{
																$this->pluginaizer->createitem->skill(false);
															}
															if($item['luck'] != '' && $item['luck'] == 1){
																$this->pluginaizer->createitem->luck(true);
															}
															else{
																$this->pluginaizer->createitem->luck(false);
															}
															if($item['opt'] != ''){
																$this->pluginaizer->createitem->opt($item['opt']);
															}
															else{
																$this->pluginaizer->createitem->opt(0);
															}
															if($item['exe'] != ''){
																$exe_opts = [0 => 1, 1 => 2, 2 => 4, 3 => 8, 4 => 16, 5 => 32];
																$iexe = explode(',', $item['exe']);
																$exe = 0;
																	
																foreach($iexe AS $k => $val){
																	if($val == 0){
																		unset($iexe[$k]);
																	}
																}
																
																if(!empty($iexe)){		
																	foreach($iexe as $key => $exe_opt){
																		if($exe_opt == 1){
																			$exe += $exe_opts[$key];
																		}
																	}
																}
																$this->pluginaizer->createitem->addStaticExe($exe);
																
															}
															if($item['anc'] != '' && $item['anc'] != 0){
																$this->pluginaizer->createitem->ancient($item['anc']);
															}

															$itemHex = $this->pluginaizer->createitem->to_hex();
															$this->pluginaizer->iteminfo->itemData($itemHex, true, $this->pluginaizer->session->userdata(['user' => 'server']));
															$this->vars['item_list'][] = [
																'hex' => $itemHex,
																'name' => $this->pluginaizer->iteminfo->getNameStyle(true),
																'amount' => $item['amount'],
																'data' => $item
															];
														}
													}
												}
												else{
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
										break;
										case 10:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											$level = $this->pluginaizer->Machievements->char_info['cLevel'];
											if($level == NULL)
												$level = 0;
											
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$level = $level - $completed;
											if($level < 0){
												$level = 0;
											}
											if($required > 0){
												if($required > $level){
													$money = $completed + $level;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $level){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 11:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											$level = $this->pluginaizer->Machievements->char_info['mlevel'];
											if($level == NULL)
												$level = 0;
											
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$level = $level - $completed;
											if($level < 0){
												$level = 0;
											}
											if($required > 0){
												if($required > $level){
													$money = $completed + $level;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $level){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 12:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											$level = $this->pluginaizer->Machievements->char_info['resets'];
											if($level == NULL)
												$level = 0;
											
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$level = $level - $completed;
											if($level < 0){
												$level = 0;
											}
											if($required > 0){
												if($required > $level){
													$money = $completed + $level;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $level){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 13:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											$level = $this->pluginaizer->Machievements->char_info['grand_resets'];
											if($level == NULL)
												$level = 0;
											
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$level = $level - $completed;
											if($level < 0){
												$level = 0;
											}
											if($required > 0){
												if($required > $level){
													$money = $completed + $level;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $level){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 14:
											$referrals = $this->pluginaizer->Machievements->check_referrals($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											if($referrals == NULL)
												$referrals = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$referrals = $referrals - $completed;
											if($referrals < 0){
												$referrals = 0;
											}
											if($required > 0){
												if($required > $referrals){
													$money = $completed + $referrals;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $referrals){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 15:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['bc']))
												throw new Exception(__('BC configuration not found'));
											if($this->vars['table_config']['bc']['table'] == '')
												throw new Exception(__('BC configuration not found'));
											
											if(defined('ELITEMU_ACH_REQ_ALL_ITEMS') && ELITEMU_ACH_REQ_ALL_ITEMS == true){
												$score = $this->pluginaizer->Machievements->get_bc_playcount($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['bc']);
											}
											else{
												$score = $this->pluginaizer->Machievements->get_score_sum($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['bc']);
											}
											if($score == NULL)
												$score = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$score = $score - $completed;
											if($score < 0){
												$score = 0;
											}
											if($required > 0){
												if($required > $score){
													$money = $completed + $score;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $score){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 16:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['ds']))
												throw new Exception(__('DS configuration not found'));
											if($this->vars['table_config']['ds']['table'] == '')
												throw new Exception(__('DS configuration not found'));
											
											$score = $this->pluginaizer->Machievements->get_score($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['ds']);
											if($score == NULL)
												$score = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$score = $score - $completed;
											if($score < 0){
												$score = 0;
											}
											if($required > 0){
												if($required > $score){
													$money = $completed + $score;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $score){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 17:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['cc']))
												throw new Exception(__('CC configuration not found'));
											if($this->vars['table_config']['cc']['table'] == '')
												throw new Exception(__('CC configuration not found'));
											
											$score = $this->pluginaizer->Machievements->get_score($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['cc']);
											if($score == NULL)
												$score = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$score = $score - $completed;
											if($score < 0){
												$score = 0;
											}
											if($required > 0){
												if($required > $score){
													$money = $completed + $score;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $score){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 18:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['it']))
												throw new Exception(__('IT configuration not found'));
											if($this->vars['table_config']['it']['table'] == '')
												throw new Exception(__('IT configuration not found'));
										break;
										case 19:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if(!isset($this->vars['table_config']['duels']))
												throw new Exception(__('Duels configuration not found'));
											if($this->vars['table_config']['duels']['table'] == '')
												throw new Exception(__('Duels configuration not found'));
											
											$score = $this->pluginaizer->Machievements->get_score($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['duels']);
											if($score == NULL)
												$score = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$score = $score - $completed;
											if($score < 0){
												$score = 0;
											}
											if($required > 0){
												if($required > $score){
													$money = $completed + $score;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $score){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 20:
											if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
												throw new Exception(__('Please logout from game.'));
											}
											
											$score = $this->pluginaizer->Machievements->get_contribution($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']));
											if($score == NULL)
												$score = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$score = $score - $completed;
											if($score < 0){
												$score = 0;
											}
											if($required > 0){
												if($required > $score){
													$money = $completed + $score;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $score){
													$money = $required;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 21:
											$shop = $this->pluginaizer->Machievements->check_shop($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											if($shop == NULL)
												$shop = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$shop = $shop - $completed;
											if($shop < 0){
												$shop = 0;
											}
											if($required > 0){
												if($required > $shop){
													$money = $completed + $shop;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $shop){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 22:
											$market = $this->pluginaizer->Machievements->check_market($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if($market == NULL)
												$market = 0;
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$market = $market - $completed;
											if($market < 0){
												$market = 0;
											}
											if($required > 0){
												if($required > $market){
													$money = $completed + $market;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $market){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 23:
											$amount = $this->pluginaizer->Machievements->count_completed_achievements($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$newCompleted = $amount['count'] - $completed;
											if($required > 0){
												if($required > $newCompleted){
													$money = $completed + $newCompleted;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $newCompleted){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 24:
											$requirements =  explode('|', json_decode($this->vars['achDataDB']['items'], true));
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											
											if($required > 0){
												if($this->pluginaizer->Machievements->char_info['cLevel'] < $requirements[0])
													throw new Exception(sprintf(__('Minimum Level Required: %d'), $requirements[0]));
												if($this->pluginaizer->Machievements->char_info['mlevel'] < $requirements[1])
													throw new Exception(sprintf(__('Minimum Master Level Required: %d'), $requirements[1]));
												if($this->pluginaizer->Machievements->char_info['resets'] < $requirements[2])
													throw new Exception(sprintf(__('Minimum Resets Required: %d'), $requirements[2]));
												if($this->pluginaizer->Machievements->char_info['grand_resets'] < $requirements[3])
													throw new Exception(sprintf(__('Minimum GrandResets Required: %d'), $requirements[3]));
												
												$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 1);
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
										case 25:
											$countAch = $this->pluginaizer->Machievements->check_online_achievement_count($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));

											if($countAch['achCount'] == NULL || $countAch['achCount'] == false){
												$countAch['achCount'] = 0;
											}
											if($countAch['achCount'] > 2){
												throw new Exception(__('This achievement is limited to 3 characters'));
											}
											$online = $this->pluginaizer->Machievements->check_online_time($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$onlineOther = $this->pluginaizer->Machievements->check_online_time_other($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											
											if($online['OnlineMinutes'] == NULL || $online['OnlineMinutes'] == false){
												$online['OnlineMinutes'] = 0;
											}
											
											$onlineHours = floor($online['OnlineMinutes'] / 60);
											
											if($onlineOther['onlineHours'] == NULL || $onlineOther['onlineHours'] == false){
												$onlineOther['onlineHours'] = 0;
											}
											
											$onlineHours = $onlineHours - $onlineOther['onlineHours'];
											$total = (int)$this->vars['achDataDB']['amount'];
											$completed = (int)$this->vars['achDataDB']['amount_completed'];
											$required = $total - $completed;
											$onlineHours = $onlineHours - $completed;
											if($onlineHours < 0){
												$onlineHours = 0;
											}
											if($required > 0){
												if($required > $onlineHours){
													$money = $completed + $onlineHours;
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
												if($required <= $onlineHours){
													$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
													$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
													$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												}
											}
											else{
												$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
												$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
												$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										break;
									}
								}	
							}
						}
						catch(Exception $e){
							$this->vars['error'] = $e->getMessage();
						}
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.check', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function reload(){
			//check if visitor has user privilleges
            if($this->pluginaizer->session->is_user()){
                //load website helper
                $this->load->helper('website');
                //load plugin config
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
                    if($this->pluginaizer->data()->value('is_multi_server') == 1){
                        if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
                            $this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
                            $this->vars['about'] = $this->pluginaizer->get_about();
                            $this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
                        } else{
                            echo $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
							exit;
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
						exit;
                    } else{
						$id = isset($_POST['cid']) ? $_POST['cid'] : '';
						$this->vars['id'] = $id;
						$achId = isset($_POST['aid']) ? $_POST['aid'] : '';
						$this->vars['achid'] = $achId;
						try{
							$this->load->model('application/plugins/achievements/models/achievements');
							if($this->pluginaizer->Machievements->checkUnlocked($id, $this->pluginaizer->session->userdata(['user' => 'server'])) == false){
								throw new Exception(__('Achievements not unlocked'));
							}

							$this->vars['archievement_list'] = $this->config->values('achievement_list', $this->pluginaizer->session->userdata(['user' => 'server']));
							
							if(empty($this->vars['archievement_list'])){
								throw new Exception(__('No achievements found.'));
							}
							
							$this->pluginaizer->Machievements->char_info($id, $this->pluginaizer->session->userdata(['user' => 'server']), true);
							
							if($this->pluginaizer->Machievements->char_info == false){
								throw new Exception(__('Invalid character'));
							}
							
							$this->vars['achKey'] = -1;		
							
							foreach($this->vars['archievement_list'] AS $key => $achievement){
								if($achievement['id'] == $this->vars['achid']){
									$this->vars['achKey'] = $key;
									break;
								}
							}
							
							if($this->vars['achKey'] == -1){
								throw new Exception(__('Achievement not found.'));
							}
							else{
								$this->vars['achData'] = $this->vars['archievement_list'][$this->vars['achKey']];
							}
							
							if($this->vars['achData']['class'] != ''){
								if(!in_array($this->pluginaizer->Machievements->char_info['Class'], $this->vars['achData']['class'])){
									throw new Exception(__('Invalid achievement'));
								}
							}

							if(isset($this->vars['achData']['min_lvl']) && $this->vars['achData']['min_lvl'] > 0){
								if($this->vars['achData']['min_lvl'] > $this->pluginaizer->Machievements->char_info['cLevel'])
									throw new Exception(sprintf(__('Min level required %d'), $this->vars['achData']['min_lvl'])); 
							}
							
							if(isset($this->vars['achData']['min_mlvl']) && $this->vars['achData']['min_mlvl'] > 0){
								if($this->vars['achData']['min_mlvl'] > $this->pluginaizer->Machievements->char_info['mlevel'])
									throw new Exception(sprintf(__('Min master level required %d'), $this->vars['achData']['min_mlvl'])); 
							}

							if(isset($this->vars['achData']['min_res']) && $this->vars['achData']['min_res'] > 0){
								if($this->vars['achData']['min_res'] > $this->pluginaizer->Machievements->char_info['resets'])
									throw new Exception(sprintf(__('Min resets required %d'), $this->vars['achData']['min_res'])); 
							}

							if(isset($this->vars['achData']['min_gres']) && $this->vars['achData']['min_gres'] > 0){
								if($this->vars['achData']['min_gres'] > $this->pluginaizer->Machievements->char_info['grand_resets'])
									throw new Exception(sprintf(__('Min grand resets required %d'), $this->vars['achData']['min_gres'])); 
							}
							
							$this->load->lib('iteminfo');
							$this->load->lib('createitem', [MU_VERSION, SOCKET_LIBRARY, $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size')]);
							$this->load->model('shop');
							
							$this->vars['archievement_rewards'] = $this->config->values('achievement_rewards', $this->pluginaizer->session->userdata(['user' => 'server']));
							
							$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
							
							if($this->vars['achDataDB'] == false){
								throw new Exception(__('User achievement not found.'));
							}
							
							if($this->vars['achDataDB']['ach_type'] == 9){
								$this->vars['item_list'] = [];
								$this->vars['dbItems'] = json_decode($this->vars['achDataDB']['items'], true);
								if(!empty($this->vars['dbItems'])){
									foreach($this->vars['dbItems'] AS $itemKey => $item){
										if($this->pluginaizer->iteminfo->setItemData($item['id'], $item['cat'], $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){	
											$this->pluginaizer->createitem->setItemData($this->pluginaizer->iteminfo->item_data);
											$this->pluginaizer->createitem->id($item['id']);
											$this->pluginaizer->createitem->cat($item['cat']);
											$this->pluginaizer->createitem->refinery(false);
											$this->pluginaizer->createitem->serial(0);
											if($this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size') == 64){
												$this->createitem->serial2(true);
											}
											if($item['lvl'] != ''){
												$this->pluginaizer->createitem->lvl($item['lvl']);
											}
											else{
												$this->pluginaizer->createitem->lvl(0);
											}
											if($item['skill'] != '' && $item['skill'] == 1){
												$this->pluginaizer->createitem->skill(true);
											}
											else{
												$this->pluginaizer->createitem->skill(false);
											}
											if($item['luck'] != '' && $item['luck'] == 1){
												$this->pluginaizer->createitem->luck(true);
											}
											else{
												$this->pluginaizer->createitem->luck(false);
											}
											if($item['opt'] != ''){
												$this->pluginaizer->createitem->opt($item['opt']);
											}
											else{
												$this->pluginaizer->createitem->opt(0);
											}
											if($item['exe'] != ''){
												$exe_opts = [0 => 1, 1 => 2, 2 => 4, 3 => 8, 4 => 16, 5 => 32];
												$iexe = explode(',', $item['exe']);
												$exe = 0;

												foreach($iexe AS $k => $val){
													if($val == 0){
														unset($iexe[$k]);
													}
												}
												
												if(!empty($iexe)){		
													foreach($iexe as $key => $exe_opt){
														if($exe_opt == 1){
															$exe += $exe_opts[$key];
														}
													}
												}
												$this->pluginaizer->createitem->addStaticExe($exe);
												
											}
											if($item['anc'] != '' && $item['anc'] != 0){
												$this->pluginaizer->createitem->ancient($item['anc']);
											}

											$itemHex = $this->pluginaizer->createitem->to_hex();
											$this->pluginaizer->iteminfo->itemData($itemHex, true, $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->vars['item_list'][] = [
												'hex' => $itemHex,
												'name' => $this->pluginaizer->iteminfo->getNameStyle(true),
												'amount' => $item['amount'],
												'data' => $item,
												'itemKey' => $itemKey
											];
										}
									}
								}
							}

							if($this->vars['achDataDB']['ach_type'] == 7){
								$this->load->lib('npc');
								$this->vars['monster_list'] = [];
								if(!empty($this->vars['achData']['monsters'])){
									foreach($this->vars['achData']['monsters'] AS $monster){
										$this->vars['monster_list'][] = $this->pluginaizer->npc->name_by_id($monster);
									}
								}
							}
							
						
							switch($this->vars['achDataDB']['ach_type']){
								case 0:
									$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
									$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
								break;
								case 1:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$zen = $this->pluginaizer->Machievements->checkZen($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									
									if($required > 0){
										if(defined('ELITEMU_ACH_REQ_ALL_ITEMS') && ELITEMU_ACH_REQ_ALL_ITEMS == true){
											if($required > $zen['Money']){
												throw new Exception(__('Not enough zen in inventory.'));
											}
										}												
										if($required > $zen['Money']){
											$money = $completed + $zen['Money'];
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_zen($zen['Money'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										}
										if($required <= $zen['Money']){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_zen($money, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 2:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$ruud = $this->pluginaizer->Machievements->checkRuud($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									
									if($required > 0){
										if($required > $ruud['Ruud']){
											$money = $completed + $ruud['Ruud'];
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_ruud($ruud['Ruud'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
										}
										if($required <= $ruud['Ruud']){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_ruud($money, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											}
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 3:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['wcoins']))
										throw new Exception(__('WCoins configuration not found'));
									if($this->vars['table_config']['wcoins']['table'] == '')
										throw new Exception(__('WCoins configuration not found'));
									$acc = (in_array($this->vars['table_config']['wcoins']['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->pluginaizer->session->userdata(['user' => 'id']) : $this->pluginaizer->session->userdata(['user' => 'username']);
									$wcoins = $this->pluginaizer->Machievements->get_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['wcoins']);
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									
									if($required > 0){
										if($required > $wcoins){
											$money = $completed + $wcoins;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $wcoins, $this->vars['table_config']['wcoins']);
											}
										}
										if($required <= $wcoins){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $money, $this->vars['table_config']['wcoins']);
											}
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 4:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['goblinpoint']))
										throw new Exception(__('GoblinPoint configuration not found'));
									if($this->vars['table_config']['goblinpoint']['table'] == '')
										throw new Exception(__('GoblinPoint configuration not found'));
									$acc = (in_array($this->vars['table_config']['goblinpoint']['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->pluginaizer->session->userdata(['user' => 'id']) : $this->pluginaizer->session->userdata(['user' => 'username']);
									$wcoins = $this->pluginaizer->Machievements->get_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['goblinpoint']);
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									
									if($required > 0){
										if($required > $wcoins){
											$money = $completed + $wcoins;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $wcoins, $this->vars['table_config']['goblinpoint']);
											}
										}
										if($required <= $wcoins){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											if($this->vars['achData']['decreaset_amount'] == 1){
												$this->pluginaizer->Machievements->remove_wcoins($acc, $this->pluginaizer->session->userdata(['user' => 'server']), $money, $this->vars['table_config']['goblinpoint']);
											}
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 5:										
									$votes = $this->pluginaizer->Machievements->check_votes($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$votesOther = $this->pluginaizer->Machievements->check_votes_other($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									if($votesOther['votes'] == NULL){
										$votesOther['votes'] = 0;
									}
									$votes['votes'] = $votes['votes'] - $votesOther['votes'];
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$votes['votes'] = $votes['votes'] - $completed;
									if($votes['votes'] < 0){
										$votes['votes'] = 0;
									}
									if($required > 0){
										if($required > $votes['votes']){
											$money = $completed + $votes['votes'];
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $votes['votes']){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 6:
									$donations = $this->pluginaizer->Machievements->check_donations($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$donationsOther = $this->pluginaizer->Machievements->check_donations_other($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if($donationsOther['donations'] == NULL){
										$donationsOther['donations'] = 0;
									}
									$donations = $donations - $donationsOther['donations'];
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$donations = $donations - $completed;
									if($donations < 0){
										$donations = 0;
									}
									if($required > 0){
										if($required > $donations){
											$money = $completed + $donations;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $donations){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 7:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$monsters = $this->pluginaizer->Machievements->check_monsters($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['monsters']);
									if($monsters == NULL)
										$monsters = 0;
									
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$monsters = $monsters - $completed;
									if($monsters < 0){
										$monsters = 0;
									}
									if($required > 0){
										if($required > $monsters){
											$money = $completed + $monsters;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $monsters){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 8:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$unique = 0;
									$minRes = 0;
									if(isset($this->vars['achData']['unique']) && $this->vars['achData']['unique'] != ''){
										$uniqueData = explode('|', $this->vars['achData']['unique']);
										$unique = $uniqueData[0];
										$minRes = $uniqueData[1];
									}
									$kills = $this->pluginaizer->Machievements->check_kills($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $unique, $minRes);
									if(is_array($kills)){
										foreach($kills AS $info){
											$this->pluginaizer->Machievements->setKillsChecked($info['Victim'], $this->pluginaizer->Machievements->char_info['Name'], $info['KillDate'], $this->pluginaizer->session->userdata(['user' => 'server']));
										}
										$kills = count($kills);
									}

									if($kills == NULL)
										$kills = 0;
									
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$kills = $kills - $completed;
									if($kills < 0){
										$kills = 0;
									}
									if($required > 0){
										if($required > $kills){
											$money = $completed + $kills;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $kills){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 9:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->pluginaizer->load->model('warehouse');
									$this->pluginaizer->Machievements->inventory($id, $this->pluginaizer->session->userdata(['user' => 'server']));
									$items = $this->pluginaizer->Machievements->getInventoryContents($this->pluginaizer->session->userdata(['user' => 'server']));
									$slotsToRemove = false;
									foreach($this->vars['item_list'] AS $key => $item){
										 $this->pluginaizer->iteminfo->itemData($item['hex'], true, $this->pluginaizer->session->userdata(['user' => 'server']));
										 $idd = $this->pluginaizer->iteminfo->id;
										 $type = $this->pluginaizer->iteminfo->type;
										 $skill = $this->pluginaizer->iteminfo->hasSkill();
										 $lvl = (int)substr($this->pluginaizer->iteminfo->getLevel(), 1);
										 $opt = ($this->pluginaizer->iteminfo->getOption()*4);
										 $exe = $this->pluginaizer->iteminfo->exeForCompare();
										 $luck = $this->pluginaizer->iteminfo->getLuck();
										 $found = false;
										 $lvlOk = false;
										 $optOk = false;
										 $skillOk = false;
										 $luckOk = false;
										 $exe0Ok = false;
										 $exe1Ok = false;
										 $exe2Ok = false;
										 $exe3Ok = false;
										 $exe4Ok = false;
										 $exe5Ok = false; 
										 $itemCount = $item['data']['amount'];
										 foreach($items AS $slot => $invItem){
											 if($invItem == str_repeat('F', $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){
												 continue;
											 }
											 $itemInfo = $this->pluginaizer->Mwarehouse->load_item_info($invItem);
											
											 if($itemInfo['info']['id'] == $idd && $itemInfo['info']['cat'] == $type){
												if($item['data']['lvl'] != ''){
													if($itemInfo['info']['lvl'] >= $lvl){
														$lvlOk = true;
													}
												}
												else{
													$lvlOk = true;
												}
												if($item['data']['opt'] != ''){
													if($itemInfo['info']['opt'] >= $opt){
														$optOk = true;
													}
												}
												else{
													$optOk = true;
												}
												if($item['data']['luck'] != ''){
													if($itemInfo['info']['luck'] >= $luck){
														$luckOk = true;
													}
												}
												else{
													$luckOk = true;
												}
												if($item['data']['skill'] != ''){
													if($itemInfo['info']['skill'] >= $skill){
														$skillOk = true;
													}
												}
												else{
													$skillOk = true;
												}
												if($item['data']['exe'] != ''){
													if($itemInfo['info']['id'] == 37 && $itemInfo['info']['cat'] == 13){
														if($exe[0] == 0 && $exe[1] == 0 && $exe[2] == 0 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
															$exe0Ok = true;
															$exe1Ok = true;
															$exe2Ok = true;
															$exe3Ok = true;
															$exe4Ok = true;
															$exe5Ok = true;
														}
														else{	
															if($exe[0] == 1 && $exe[1] == 0 && $exe[2] == 0 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																if($itemInfo['exe_opts'][0] == 1){
																	$exe0Ok = true;
																	$exe1Ok = true;
																	$exe2Ok = true;
																	$exe3Ok = true;
																	$exe4Ok = true;
																	$exe5Ok = true;
																}
															}
															if($exe[0] == 0 && $exe[1] == 1 && $exe[2] == 0 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																if($itemInfo['exe_opts'][1] == 1){
																	$exe0Ok = true;
																	$exe1Ok = true;
																	$exe2Ok = true;
																	$exe3Ok = true;
																	$exe4Ok = true;
																	$exe5Ok = true;
																}
															}
															if($exe[0] == 0 && $exe[1] == 0 && $exe[2] == 1 && $exe[3] == 0 && $exe[4] == 0 && $exe[5] == 0){
																if($itemInfo['exe_opts'][2] == 1){
																	$exe0Ok = true;
																	$exe1Ok = true;
																	$exe2Ok = true;
																	$exe3Ok = true;
																	$exe4Ok = true;
																	$exe5Ok = true;
																}
															}
														}
													}
													else{
														if($itemInfo['exe_opts'][0] == 1){
															if($exe[0] <= $itemInfo['exe_opts'][0]){
																$exe0Ok = true;
															}
														}
														else{
															$exe0Ok = true;
														}
														if($exe[1] == 1){
															if($exe[1] <= $itemInfo['exe_opts'][1]){
																$exe1Ok = true;
															}
														}
														else{
															$exe1Ok = true;
														}
														if($exe[2] == 1){
															if($exe[2] <= $itemInfo['exe_opts'][2]){
																$exe2Ok = true;
															}
														}
														else{
															$exe2Ok = true;
														}
														if($exe[3] == 1){
															if($exe[3] <= $itemInfo['exe_opts'][3]){
																$exe3Ok = true;
															}
														}
														else{
															$exe3Ok = true;
														}
														
														if($exe[4] == 1){
															if($exe[4] <= $itemInfo['exe_opts'][4]){
																$exe4Ok = true;
															}
														}
														else{
															$exe4Ok = true;
														}

														if($exe[5] == 1){
															if($exe[5] <= $itemInfo['exe_opts'][5]){
																$exe5Ok = true;
															}
														}
														else{
															$exe5Ok = true;
														}
													}
												}
												else{
													$exe0Ok = true;
													$exe1Ok = true;
													$exe2Ok = true;
													$exe3Ok = true;
													$exe4Ok = true;
													$exe5Ok = true; 
												}

												if($lvlOk == true && $optOk == true && $skillOk == true && $luckOk == true && $exe0Ok == true && $exe1Ok == true && $exe2Ok == true && $exe3Ok == true && $exe4Ok == true && $exe5Ok == true && $itemCount > 0){
													$slotsToRemove[$slot] = $slot;
													
													if($itemCount > 1){
														foreach($this->vars['dbItems'] AS $k => $dbItem){
															if($k == $item['itemKey']){									
																$this->vars['dbItems'][$k]['amount'] = $itemCount - 1;
															}
														}
													}
													else{														
														foreach($this->vars['dbItems'] AS $k => $dbItem){																
															if($itemCount == 1){																	
																if($k == $item['itemKey']){																		
																	unset($this->vars['dbItems'][$k]);
																}
															}
														}
													}
													$itemCount -= 1;
												}
											 }
										 }
									}
									if(defined('ELITEMU_ACH_REQ_ALL_ITEMS') && ELITEMU_ACH_REQ_ALL_ITEMS == true){
										if($itemCount != 0){
											throw new Exception(__('Missing some of items in inventory'));
										}
									}
									if($slotsToRemove != false){
										$newItems = $this->pluginaizer->Machievements->updateInventorySlots($slotsToRemove, $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->pluginaizer->Machievements->updateInventory($id, $this->pluginaizer->session->userdata(['user' => 'server']), $newItems);
										$this->pluginaizer->Machievements->updateUserAchievementItems($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['dbItems']);
										$this->vars['achDataDB'] = $this->pluginaizer->Machievements->checkUserAchievement($achId, $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->vars['item_list'] = [];
										$this->vars['dbItems'] = json_decode($this->vars['achDataDB']['items'], true);
										if(!empty($this->vars['dbItems'])){
											foreach($this->vars['dbItems'] AS $item){
												if($this->pluginaizer->iteminfo->setItemData($item['id'], $item['cat'], $this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size'))){	
													$this->pluginaizer->createitem->setItemData($this->pluginaizer->iteminfo->item_data);
													$this->pluginaizer->createitem->id($item['id']);
													$this->pluginaizer->createitem->cat($item['cat']);
													$this->pluginaizer->createitem->refinery(false);
													$this->pluginaizer->createitem->serial(0);
													if($this->pluginaizer->website->get_value_from_server($this->pluginaizer->session->userdata(['user' => 'server']), 'item_size') == 64){
														$this->createitem->serial2(true);
													}
													if($item['lvl'] != ''){
														$this->pluginaizer->createitem->lvl($item['lvl']);
													}
													else{
														$this->pluginaizer->createitem->lvl(0);
													}
													if($item['skill'] != '' && $item['skill'] == 1){
														$this->pluginaizer->createitem->skill(true);
													}
													else{
														$this->pluginaizer->createitem->skill(false);
													}
													if($item['luck'] != '' && $item['luck'] == 1){
														$this->pluginaizer->createitem->luck(true);
													}
													else{
														$this->pluginaizer->createitem->luck(false);
													}
													if($item['opt'] != ''){
														$this->pluginaizer->createitem->opt($item['opt']);
													}
													else{
														$this->pluginaizer->createitem->opt(0);
													}
													if($item['exe'] != ''){
														$exe_opts = [0 => 1, 1 => 2, 2 => 4, 3 => 8, 4 => 16, 5 => 32];
														$iexe = explode(',', $item['exe']);
														$exe = 0;
															
														foreach($iexe AS $k => $val){
															if($val == 0){
																unset($iexe[$k]);
															}
														}
														
														if(!empty($iexe)){		
															foreach($iexe as $key => $exe_opt){
																if($exe_opt == 1){
																	$exe += $exe_opts[$key];
																}
															}
														}
														$this->pluginaizer->createitem->addStaticExe($exe);
														
													}
													if($item['anc'] != '' && $item['anc'] != 0){
														$this->pluginaizer->createitem->ancient($item['anc']);
													}
													
													$itemHex = $this->pluginaizer->createitem->to_hex();
													$this->pluginaizer->iteminfo->itemData($itemHex, true, $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->vars['item_list'][] = [
														'hex' => $itemHex,
														'name' => $this->pluginaizer->iteminfo->getNameStyle(true),
														'amount' => $item['amount'],
														'data' => $item
													];
												}
											}
										}
										else{
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
								break;
								case 10:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									$level = $this->pluginaizer->Machievements->char_info['cLevel'];
									if($level == NULL)
										$level = 0;
									
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$level = $level - $completed;
									if($level < 0){
										$level = 0;
									}
									if($required > 0){
										if($required > $level){
											$money = $completed + $level;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $level){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 11:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									$level = $this->pluginaizer->Machievements->char_info['mlevel'];
									if($level == NULL)
										$level = 0;
									
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$level = $level - $completed;
									if($level < 0){
										$level = 0;
									}
									if($required > 0){
										if($required > $level){
											$money = $completed + $level;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $level){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 12:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									$level = $this->pluginaizer->Machievements->char_info['resets'];
									if($level == NULL)
										$level = 0;
									
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$level = $level - $completed;
									if($level < 0){
										$level = 0;
									}
									if($required > 0){
										if($required > $level){
											$money = $completed + $level;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $level){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 13:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									$level = $this->pluginaizer->Machievements->char_info['grand_resets'];
									if($level == NULL)
										$level = 0;
									
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$level = $level - $completed;
									if($level < 0){
										$level = 0;
									}
									if($required > 0){
										if($required > $level){
											$money = $completed + $level;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $level){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 14:
									$referrals = $this->pluginaizer->Machievements->check_referrals($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									if($referrals == NULL)
										$referrals = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$referrals = $referrals - $completed;
									if($referrals < 0){
										$referrals = 0;
									}
									if($required > 0){
										if($required > $referrals){
											$money = $completed + $referrals;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $referrals){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 15:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['bc']))
										throw new Exception(__('BC configuration not found'));
									if($this->vars['table_config']['bc']['table'] == '')
										throw new Exception(__('BC configuration not found'));
									
									if(defined('ELITEMU_ACH_REQ_ALL_ITEMS') && ELITEMU_ACH_REQ_ALL_ITEMS == true){
										$score = $this->pluginaizer->Machievements->get_bc_playcount($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['bc']);
									}
									else{
										$score = $this->pluginaizer->Machievements->get_score_sum($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['bc']);
									}
									if($score == NULL)
										$score = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$score = $score - $completed;
									if($score < 0){
										$score = 0;
									}
									if($required > 0){
										if($required > $score){
											$money = $completed + $score;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $score){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 16:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['ds']))
										throw new Exception(__('DS configuration not found'));
									if($this->vars['table_config']['ds']['table'] == '')
										throw new Exception(__('DS configuration not found'));
									
									$score = $this->pluginaizer->Machievements->get_score($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['ds']);
									if($score == NULL)
										$score = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$score = $score - $completed;
									if($score < 0){
										$score = 0;
									}
									if($required > 0){
										if($required > $score){
											$money = $completed + $score;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $score){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 17:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['cc']))
										throw new Exception(__('CC configuration not found'));
									if($this->vars['table_config']['cc']['table'] == '')
										throw new Exception(__('CC configuration not found'));
									
									$score = $this->pluginaizer->Machievements->get_score($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['cc']);
									if($score == NULL)
										$score = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$score = $score - $completed;
									if($score < 0){
										$score = 0;
									}
									if($required > 0){
										if($required > $score){
											$money = $completed + $score;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $score){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 18:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['it']))
										throw new Exception(__('IT configuration not found'));
									if($this->vars['table_config']['it']['table'] == '')
										throw new Exception(__('IT configuration not found'));
								break;
								case 19:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$this->vars['table_config'] = $this->pluginaizer->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if(!isset($this->vars['table_config']['duels']))
										throw new Exception(__('Duels configuration not found'));
									if($this->vars['table_config']['duels']['table'] == '')
										throw new Exception(__('Duels configuration not found'));
									
									$score = $this->pluginaizer->Machievements->get_score($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['duels']);
									if($score == NULL)
										$score = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$score = $score - $completed;
									if($score < 0){
										$score = 0;
									}
									if($required > 0){
										if($required > $score){
											$money = $completed + $score;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $score){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 20:
									if(!$this->pluginaizer->Machievements->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
										throw new Exception(__('Please logout from game.'));
									}
									
									$score = $this->pluginaizer->Machievements->get_contribution($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'server']));
									if($score == NULL)
										$score = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$score = $score - $completed;
									if($score < 0){
										$score = 0;
									}
									if($required > 0){
										if($required > $score){
											$money = $completed + $score;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $score){
											$money = $required;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 21:
									$shop = $this->pluginaizer->Machievements->check_shop($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									if($shop == NULL)
										$shop = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$shop = $shop - $completed;
									if($shop < 0){
										$shop = 0;
									}
									if($required > 0){
										if($required > $shop){
											$money = $completed + $shop;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $shop){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 22:
									$market = $this->pluginaizer->Machievements->check_market($this->pluginaizer->Machievements->char_info['Name'], $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if($market == NULL)
										$market = 0;
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$market = $market - $completed;
									if($market < 0){
										$market = 0;
									}
									if($required > 0){
										if($required > $market){
											$money = $completed + $market;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $market){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 23:
									$amount = $this->pluginaizer->Machievements->count_completed_achievements($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$newCompleted = $amount['count'] - $completed;
									if($required > 0){
										if($required > $newCompleted){
											$money = $completed + $newCompleted;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $newCompleted){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 24:
									$requirements =  explode('|', json_decode($this->vars['achDataDB']['items'], true));
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									
									if($required > 0){
										if($this->pluginaizer->Machievements->char_info['cLevel'] < $requirements[0])
											throw new Exception(sprintf(__('Minimum Level Required: %d'), $requirements[0]));
										if($this->pluginaizer->Machievements->char_info['mlevel'] < $requirements[1])
											throw new Exception(sprintf(__('Minimum Master Level Required: %d'), $requirements[1]));
										if($this->pluginaizer->Machievements->char_info['resets'] < $requirements[2])
											throw new Exception(sprintf(__('Minimum Resets Required: %d'), $requirements[2]));
										if($this->pluginaizer->Machievements->char_info['grand_resets'] < $requirements[3])
											throw new Exception(sprintf(__('Minimum GrandResets Required: %d'), $requirements[3]));
										
										$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 1);
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
								case 25:
									$countAch = $this->pluginaizer->Machievements->check_online_achievement_count($id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));

									if($countAch['achCount'] == NULL || $countAch['achCount'] == false){
										$countAch['achCount'] = 0;
									}
									if($countAch['achCount'] > 2){
										throw new Exception(__('This achievement is limited to 3 characters'));
									}
									$online = $this->pluginaizer->Machievements->check_online_time($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									$onlineOther = $this->pluginaizer->Machievements->check_online_time_other($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									
									if($online['OnlineMinutes'] == NULL || $online['OnlineMinutes'] == false){
										$online['OnlineMinutes'] = 0;
									}
									
									$onlineHours = floor($online['OnlineMinutes'] / 60);
									
									if($onlineOther['onlineHours'] == NULL || $onlineOther['onlineHours'] == false){
										$onlineOther['onlineHours'] = 0;
									}
									
									$onlineHours = $onlineHours - $onlineOther['onlineHours'];
									$total = (int)$this->vars['achDataDB']['amount'];
									$completed = (int)$this->vars['achDataDB']['amount_completed'];
									$required = $total - $completed;
									$onlineHours = $onlineHours - $completed;
									if($onlineHours < 0){
										$onlineHours = 0;
									}
									if($required > 0){
										if($required > $onlineHours){
											$money = $completed + $onlineHours;
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $money);
										}
										if($required <= $onlineHours){
											$this->pluginaizer->Machievements->updateCompletedAmount($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $total);
											$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
											$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
											$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										}
									}
									else{
										$this->Machievements->setCompleted($id, $achId, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
										$this->Machievements->addRankingScore($id, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['achData']['points']);
										$this->Machievements->add_account_log(sprintf(__('Completed achievement: %s'), $this->vars['achData']['title']), 0, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
									}
								break;
							}
							$data = $this->pluginaizer->Machievements->checkAchievementStatus($this->vars['achData'], $id, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
							if($data != false){
								$percents = 0;
								$message = '';
								if($data['is_completed'] == 1){
									$percents = 100;
									$message = __('Completed');
								}
								else{
									if($data['ach_type'] != 0){
										if($data['ach_type'] != 9){
											$percents = ($data['amount_completed'] * 100) / $data['amount'];
											$message = $data['amount_completed'] . ' / ' . $data['amount'];
										}
										else{
											$amountTotal = 0;
											$amountLeft = 0;
											foreach($this->vars['achData']['items'] AS $total){
												$amountTotal += $total['amount'];
											}
											foreach(json_decode($data['items'], true) AS $left){
												$amountLeft += $left['amount'];
											}
											if($amountLeft <= 0){
												$left = $amountTotal;
												$percents = 100;
											}
											else{
												$left = $amountTotal - $amountLeft;
												$percents = ($left * 100) / $amountTotal;
											}
											$message = $left . ' / ' . $amountTotal;
										}
									}
								}
								echo $this->pluginaizer->jsone(['msg' => $message, 'percents' => $percents]);
								exit;
							}
						}
						catch(Exception $e){
							echo $this->pluginaizer->jsone(['error' => $e->getMessage()]);
							exit;
						}
                    }
                } else{
                    echo $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
                }
            } else{
               echo $this->pluginaizer->jsone(['error' => __('Please login first!')]);
			   exit;
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
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function achievement_list(){
			if($this->pluginaizer->session->is_admin()){
				$this->vars['server'] = isset($_GET['server']) ? $_GET['server'] : '';
				if($this->vars['server'] != ''){
					$this->load->helper('website');
					$this->load->helper('webshop');
					$this->load->lib('npc');
					$this->load->model('application/plugins/achievements/models/achievements');
					$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
					$this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
					$this->vars['monster_list'] = $this->pluginaizer->npc->get_list();
					$this->vars['class_list'] = $this->pluginaizer->website->get_char_class(0, false, true);
					
					if(isset($_POST['add_achievements'])){
						$title = isset($_POST['title']) ? $_POST['title'] : '';
						$desc = isset($_POST['desc']) ? $_POST['desc'] : '';
						$image = isset($_POST['image']) ? $_POST['image'] : '';
						$points = isset($_POST['points']) ? $_POST['points'] : '';
						$category = isset($_POST['category']) ? $_POST['category'] : '';
						$period = isset($_POST['period']) ? $_POST['period'] : '';
						$min_lvl = isset($_POST['min_lvl']) ? $_POST['min_lvl'] : 1;
						$min_mlvl = isset($_POST['min_lvl']) ? $_POST['min_mlvl'] : 0;
						$min_res = isset($_POST['min_res']) ? $_POST['min_res'] : 0;
						$min_gres = isset($_POST['min_gres']) ? $_POST['min_gres'] : 0;
						$class = isset($_POST['class']) ? $_POST['class'] : '';
						$achievement_type = isset($_POST['achievement_type']) ? $_POST['achievement_type'] : '';
						$amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
						$decreaset_amount = isset($_POST['decreaset_amount']) ? $_POST['decreaset_amount'] : 0;
						$total_stats = isset($_POST['total_stats']) ? $_POST['total_stats'] : 0;
						$total_ref = isset($_POST['total_ref']) ? $_POST['total_ref'] : 0;
						$total_contr = isset($_POST['total_contr']) ? $_POST['total_contr'] : 0;
						$total_items_buy = isset($_POST['total_items_buy']) ? $_POST['total_items_buy'] : 0;
						$total_items_sell = isset($_POST['total_items_sell']) ? $_POST['total_items_sell'] : 0;
						$total_kills = isset($_POST['total_kills']) ? $_POST['total_kills'] : 0;
						$unique = isset($_POST['unique']) ? $_POST['unique'] : 0;
						$total_votes = isset($_POST['total_votes']) ? $_POST['total_votes'] : 0;
						$total_donate = isset($_POST['total_donate']) ? $_POST['total_donate'] : 0;
						$total_score = isset($_POST['total_score']) ? $_POST['total_score'] : 0;
						$total_wins = isset($_POST['total_wins']) ? $_POST['total_wins'] : 0;
						$total_level = isset($_POST['total_level']) ? $_POST['total_level'] : 0;
						$total_mlevel = isset($_POST['total_mlevel']) ? $_POST['total_mlevel'] : 0;
						$total_res = isset($_POST['total_res']) ? $_POST['total_res'] : 0;
						$total_gres = isset($_POST['total_gres']) ? $_POST['total_gres'] : 0;
						$monsters = isset($_POST['monsters']) ? $_POST['monsters'] : '';
						$mamount = isset($_POST['mamount']) ? $_POST['mamount'] : 0;
						$items = [];
						if($_POST['item_count']){
							foreach($_POST['item_count'] AS $key => $val){
								if($val > 0){
									$items[] = [
										'amount' => $val,
										'cat' => $_POST['item_category'][$key],
										'id' => $_POST['item_index'][$key],
										'lvl' => $_POST['item_level'][$key],
										'skill' => $_POST['item_skill'][$key],
										'luck' => $_POST['item_luck'][$key],
										'opt' => $_POST['item_option'][$key],
										'exe' => $_POST['item_excellent'][$key],
										'anc' => $_POST['item_ancient'][$key],
									];
								}
							}
						}
						if($title == ''){
							$this->vars['error'] = 'Please enter title.';
						}
						else{
							if($desc == ''){
								$this->vars['error'] = 'Please enter description.';
							}
							else{
								if($points == ''){
									$this->vars['error'] = 'Please enter amount of ranking points.';
								}
								else{
									if($achievement_type == ''){
										$this->vars['error'] = 'Please select achievement type.';
									}
									else{
										
									}
								}
							}
						}
						$this->vars['achievement_list'] = $this->config->values('achievement_list');
						if(array_key_exists($this->vars['server'], $this->vars['achievement_list'])){
							if(empty($this->vars['achievement_list'][$this->vars['server']])){
								$this->vars['achievement_list'][$this->vars['server']] = [
									1 => [
										'id' => uniqid(),
										'title' => $title,
										'desc' => $desc,
										'image' => $image,
										'category' => $category,
										'points' => $points,
										'period'=> $period,
										'min_lvl' => $min_lvl,
										'min_mlvl' => $min_mlvl,
										'min_res' => $min_res,
										'min_gres' => $min_gres,	
										'class' => $class,
										'achievement_type' => $achievement_type,
										'amount' => $amount,
										'decreaset_amount' => $decreaset_amount,
										'total_stats' => $total_stats,
										'total_ref' => $total_ref,
										'total_contr' => $total_contr,
										'total_items_buy' => $total_items_buy,
										'total_items_sell' => $total_items_sell,
										'total_kills' => $total_kills,
										'unique' => $unique,
										'total_votes' => $total_votes,
										'total_donate' => $total_donate,
										'total_score' => $total_score,
										'total_wins' => $total_wins,
										'total_level' => $total_level,
										'total_mlevel' => $total_mlevel,
										'total_res' => $total_res,
										'total_gres' => $total_gres,
										'monsters' => $monsters,
										'mamount' => $mamount,
										'items' => $items
									]
								];
							}
							else{
								$this->vars['achievement_list'][$this->vars['server']][] = [
									'id' => uniqid(),
									'title' => $title,
									'desc' => $desc,
									'image' => $image,
									'category' => $category,
									'points' => $points,
									'period'=> $period,
									'min_lvl' => $min_lvl,
									'min_mlvl' => $min_mlvl,
									'min_res' => $min_res,
									'min_gres' => $min_gres,
									'class' => $class,
									'achievement_type' => $achievement_type,
									'amount' => $amount,
									'decreaset_amount' => $decreaset_amount,
									'total_stats' => $total_stats,
									'total_ref' => $total_ref,
									'total_contr' => $total_contr,
									'total_items_buy' => $total_items_buy,
									'total_items_sell' => $total_items_sell,
									'total_kills' => $total_kills,
									'unique' => $unique,
									'total_votes' => $total_votes,
									'total_donate' => $total_donate,
									'total_score' => $total_score,
									'total_wins' => $total_wins,
									'total_level' => $total_level,
									'total_mlevel' => $total_mlevel,
									'total_res' => $total_res,
									'total_gres' => $total_gres,
									'monsters' => $monsters,
									'mamount' => $mamount,
									'items' => $items
								];
							}
							$this->config->save_config_data($this->vars['achievement_list'], 'achievement_list');
						}
						else{
							$this->vars['new_config'] = [
								$this->vars['server'] => [
									'1' => [
										'id' => uniqid(),
										'title' => $title,
										'desc' => $desc,
										'image' => $image,
										'category' => $category,
										'points' => $points,
										'period' => $period,
										'min_lvl' => $min_lvl,
										'min_mlvl' => $min_mlvl,
										'min_res' => $min_res,
										'min_gres' => $min_gres,
										'class' => $class,
										'achievement_type' => $achievement_type,
										'amount' => $amount,
										'decreaset_amount' => $decreaset_amount,
										'total_stats' => $total_stats,
										'total_ref' => $total_ref,
										'total_contr' => $total_contr,
										'total_items_buy' => $total_items_buy,
										'total_items_sell' => $total_items_sell,
										'total_kills' => $total_kills,
										'unique' => $unique,
										'total_votes' => $total_votes,
										'total_donate' => $total_donate,
										'total_score' => $total_score,
										'total_wins' => $total_wins,
										'total_level' => $total_level,
										'total_mlevel' => $total_mlevel,
										'total_res' => $total_res,
										'total_gres' => $total_gres,
										'monsters' => $monsters,
										'mamount' => $mamount,
										'items' => $items
									]
								]
							];
							$this->vars['achievement_list'] = array_merge($this->vars['achievement_list'], $this->vars['new_config']);
                            $this->config->save_config_data($this->vars['achievement_list'], 'achievement_list');
						}
						 $this->vars['success'] = 'Achievement successfully added.';
					}
					
					$this->vars['achievements'] = $this->config->values('achievement_list', $this->vars['server']);
					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.achievement_list', $this->vars);
				}
				else{
					$this->vars['error'] = __('Invalid server.');
				}
			} else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function edit($id, $server){
			if($this->pluginaizer->session->is_admin()){
				$this->vars['server'] = $server;
				if($this->vars['server'] != ''){
					$this->load->helper('website');
					$this->load->helper('webshop');
					$this->load->lib('npc');
					$this->load->model('application/plugins/achievements/models/achievements');
					$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
					$this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
					$this->vars['monster_list'] = $this->pluginaizer->npc->get_list();
					$this->vars['class_list'] = $this->pluginaizer->website->get_char_class(0, false, true);
					$this->vars['achievements'] = $this->config->values('achievement_list', $this->vars['server']);
					$this->vars['achKey'] = -1;
					if(!empty($this->vars['achievements'])){
						foreach($this->vars['achievements'] AS $key => $data){
							if($data['id'] == $id){
								$this->vars['achKey'] = $key;
								break;
							}
						}
					}
					else{
						$this->vars['not_found'] = __('Achievement not found.');
					}
					
					if($this->vars['achKey'] == -1){
						$this->vars['not_found'] = __('Achievement not found.');
					}
					else{
						$this->vars['achData'] = $this->vars['achievements'][$this->vars['achKey']];
					}
					
					if(isset($_POST['edit_achievements'])){
						$title = isset($_POST['title']) ? $_POST['title'] : '';
						$desc = isset($_POST['desc']) ? $_POST['desc'] : '';
						$image = isset($_POST['image']) ? $_POST['image'] : '';
						$category = isset($_POST['category']) ? $_POST['category'] : '';
						$points = isset($_POST['points']) ? $_POST['points'] : '';
						$period = isset($_POST['period']) ? $_POST['period'] : '';
						$min_lvl = isset($_POST['min_lvl']) ? $_POST['min_lvl'] : 1;
						$min_mlvl = isset($_POST['min_lvl']) ? $_POST['min_mlvl'] : 0;
						$min_res = isset($_POST['min_res']) ? $_POST['min_res'] : 0;
						$min_gres = isset($_POST['min_gres']) ? $_POST['min_gres'] : 0;
						$class = isset($_POST['class']) ? $_POST['class'] : '';
						$achievement_type = isset($_POST['achievement_type']) ? $_POST['achievement_type'] : '';
						$amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
						$decreaset_amount = isset($_POST['decreaset_amount']) ? $_POST['decreaset_amount'] : 0;
						$total_stats = isset($_POST['total_stats']) ? $_POST['total_stats'] : 0;
						$total_ref = isset($_POST['total_ref']) ? $_POST['total_ref'] : 0;
						$total_contr = isset($_POST['total_contr']) ? $_POST['total_contr'] : 0;
						$total_items_buy = isset($_POST['total_items_buy']) ? $_POST['total_items_buy'] : 0;
						$total_items_sell = isset($_POST['total_items_sell']) ? $_POST['total_items_sell'] : 0;
						$total_kills = isset($_POST['total_kills']) ? $_POST['total_kills'] : 0;
						$unique = isset($_POST['unique']) ? $_POST['unique'] : 0;
						$total_votes = isset($_POST['total_votes']) ? $_POST['total_votes'] : 0;
						$total_donate = isset($_POST['total_donate']) ? $_POST['total_donate'] : 0;
						$total_score = isset($_POST['total_score']) ? $_POST['total_score'] : 0;
						$total_wins = isset($_POST['total_wins']) ? $_POST['total_wins'] : 0;
						$total_level = isset($_POST['total_level']) ? $_POST['total_level'] : 0;
						$total_mlevel = isset($_POST['total_mlevel']) ? $_POST['total_mlevel'] : 0;
						$total_res = isset($_POST['total_res']) ? $_POST['total_res'] : 0;
						$total_gres = isset($_POST['total_gres']) ? $_POST['total_gres'] : 0;
						$monsters = isset($_POST['monsters']) ? $_POST['monsters'] : '';
						$mamount = isset($_POST['mamount']) ? $_POST['mamount'] : 0;
						$items = [];
						if($_POST['item_count']){
							foreach($_POST['item_count'] AS $key => $val){
								if($val > 0){
									$items[] = [
										'amount' => $val,
										'cat' => $_POST['item_category'][$key],
										'id' => $_POST['item_index'][$key],
										'lvl' => $_POST['item_level'][$key],
										'skill' => $_POST['item_skill'][$key],
										'luck' => $_POST['item_luck'][$key],
										'opt' => $_POST['item_option'][$key],
										'exe' => $_POST['item_excellent'][$key],
										'anc' => $_POST['item_ancient'][$key],
									];
								}
							}
						}
						if($title == ''){
							$this->vars['error'] = 'Please enter title.';
						}
						else{
							if($desc == ''){
								$this->vars['error'] = 'Please enter description.';
							}
							else{
								if($points == ''){
									$this->vars['error'] = 'Please enter amount of ranking points.';
								}
								else{
									if($achievement_type == ''){
										$this->vars['error'] = 'Please select achievement type.';
									}
									else{
										
									}
								}
							}
						}
						
						$this->vars['achievement_list'] = $this->config->values('achievement_list');
						$this->vars['achievement_list'][$this->vars['server']][$this->vars['achKey']] = [
							'id' => $id,
							'title' => $title,
							'desc' => $desc,
							'image' => $image,
							'points' => $points,
							'category' => $category,
							'period' => $period,
							'min_lvl' => $min_lvl,
							'min_mlvl' => $min_mlvl,
							'min_res' => $min_res,
							'min_gres' => $min_gres,
							'class' => $class,
							'achievement_type' => $achievement_type,
							'amount' => $amount,
							'decreaset_amount' => $decreaset_amount,
							'total_stats' => $total_stats,
							'total_ref' => $total_ref,
							'total_contr' => $total_contr,
							'total_items_buy' => $total_items_buy,
							'total_items_sell' => $total_items_sell,
							'total_kills' => $total_kills,
							'unique' => $unique,
							'total_votes' => $total_votes,
							'total_donate' => $total_donate,
							'total_score' => $total_score,
							'total_wins' => $total_wins,
							'total_level' => $total_level,
							'total_mlevel' => $total_mlevel,
							'total_res' => $total_res,
							'total_gres' => $total_gres,
							'monsters' => $monsters,
							'mamount' => $mamount,
							'items' => $items
						];
						$this->config->save_config_data($this->vars['achievement_list'], 'achievement_list');
						$this->vars['success'] = 'Achievement successfully updated.';
						$this->vars['achData'] = $this->vars['achievement_list'][$this->vars['server']][$this->vars['achKey']];
					}

					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.edit', $this->vars);
				}
				else{
					$this->vars['error'] = __('Invalid server.');
				}
			} else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
		}

		public function save_order($server)
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                $this->vars['achievements'] = $this->config->values('achievement_list');
				$this->vars['new'] = [];	
				foreach($_POST['order'] AS $value){
                    if(array_key_exists($value, $this->vars['achievements'][$server])){
                        $this->vars['new'][$server][$value] = $this->vars['achievements'][$server][$value];
                    }
                }
				
				if(!empty($this->vars['new'])){
					$this->vars['achievements'][$server] = $this->vars['new'][$server];
					$this->config->save_config_data($this->vars['achievements'], 'achievement_list');
				}
            } 
			else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }
		
		public function delete($id, $server){
			if($this->pluginaizer->session->is_admin()){
				$this->vars['server'] = $server;
				if($this->vars['server'] != ''){
					$this->load->helper('website');
					$this->load->helper('webshop');
					$this->load->lib('npc');
					$this->load->model('application/plugins/achievements/models/achievements');
					$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
					$this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
					$this->vars['monster_list'] = $this->pluginaizer->npc->get_list();
					$this->vars['class_list'] = $this->pluginaizer->website->get_char_class(0, false, true);
					$this->vars['achievements'] = $this->config->values('achievement_list');
					$this->vars['achKey'] = -1;
					if(!empty($this->vars['achievements'][$this->vars['server']])){
						foreach($this->vars['achievements'][$this->vars['server']] AS $key => $data){
							if($data['id'] == $id){
								$this->vars['achKey'] = $key;
								break;
							}
						}
					}
					else{
						$this->vars['not_found'] = __('Achievement not found.');
					}
					
					if($this->vars['achKey'] == -1){
						$this->vars['not_found'] = __('Achievement not found.');
					}
					else{
						$this->Machievements->removeUserAchievement($this->vars['achievements'][$this->vars['server']][$this->vars['achKey']]['id'], $this->vars['server']);
						unset($this->vars['achievements'][$this->vars['server']][$this->vars['achKey']]);
					}
					
					$this->config->save_config_data($this->vars['achievements'], 'achievement_list');
					$this->vars['success'] = 'Achievement successfully removed.';

					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.delete', $this->vars);
				}
				else{
					$this->vars['error'] = __('Invalid server.');
				}
			} else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function rewards($id, $server){
			if($this->pluginaizer->session->is_admin()){
				$this->vars['server'] = $server;
				if($this->vars['server'] != ''){
					$this->load->helper('website');
					$this->load->helper('webshop');
					$this->load->lib('npc');
					$this->load->model('application/plugins/achievements/models/achievements');
					$this->load->model('admin');
					$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
					$this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
					$this->vars['monster_list'] = $this->pluginaizer->npc->get_list();
					$this->vars['class_list'] = $this->pluginaizer->website->get_char_class(0, false, true);
					$this->vars['achievements'] = $this->config->values('achievement_list');
					
					
					$this->vars['achKey'] = -1;
					if(!empty($this->vars['achievements'][$this->vars['server']])){
						foreach($this->vars['achievements'][$this->vars['server']] AS $key => $data){
							if($data['id'] == $id){
								$this->vars['achKey'] = $key;
								break;
							}
						}
					}
					else{
						$this->vars['not_found'] = __('Achievement not found.');
					}
					

					if($this->vars['achKey'] == -1){
						$this->vars['not_found'] = __('Achievement not found.');
					}
					
					$this->vars['reward_list'] = $this->config->values('achievement_rewards');
					
					$this->vars['achData'] = [];
					
					if(isset($this->vars['reward_list'][$this->vars['server']][$id])){
						$this->vars['achData'] = $this->vars['reward_list'][$this->vars['server']][$id];
					}
					
					if(isset($_POST['add_reward'])){
						$items = [];
						if($_POST['item_category']){
							foreach($_POST['item_category'] AS $key => $val){
								if($val != ''){
									$items[] = [
										'cat' => $_POST['item_category'][$key],
										'id' => $_POST['item_index'][$key],
										'dur' => $_POST['item_dur'][$key],
										'lvl' => $_POST['item_level'][$key],
										'skill' => $_POST['item_skill'][$key],
										'luck' => $_POST['item_luck'][$key],
										'opt' => $_POST['item_option'][$key],
										'exe' => $_POST['item_excellent'][$key],
										'anc' => $_POST['item_ancient'][$key],
										'expires' => $_POST['item_expires'][$key],
									];
								}
							}
						}
						
						$_POST['items'] = $items;
						
						if(array_key_exists($this->vars['server'], $this->vars['reward_list'])){
							if(empty($this->vars['reward_list'][$this->vars['server']])){
								$this->vars['reward_list'][$this->vars['server']] = [
									$id => [
										'credits1' => $_POST['credits1'],
										'credits2' => $_POST['credits2'],
										'wcoin' => $_POST['wcoin'],
										'goblin' => $_POST['goblin'],
										'zen' => $_POST['zen'],
										'credits3' => $_POST['credits3'],
										'ruud' => $_POST['ruud'],
										'hunt' => $_POST['hunt'],
										'vip_type' => $_POST['vip_type'],
										'items' => $items
									]
								];
							}
							else{
								$this->vars['reward_list'][$this->vars['server']][$id] = [
									'credits1' => $_POST['credits1'],
									'credits2' => $_POST['credits2'],
									'wcoin' => $_POST['wcoin'],
									'goblin' => $_POST['goblin'],
									'zen' => $_POST['zen'],
									'credits3' => $_POST['credits3'],
									'ruud' => $_POST['ruud'],
									'hunt' => $_POST['hunt'],
									'vip_type' => $_POST['vip_type'],
									'items' => $items
								];
							}
							$this->config->save_config_data($this->vars['reward_list'], 'achievement_rewards');
						}
						else{
							$this->vars['new_config'] = [
								$this->vars['server'] => [
									$id => [
										'credits1' => $_POST['credits1'],
										'credits2' => $_POST['credits2'],
										'wcoin' => $_POST['wcoin'],
										'goblin' => $_POST['goblin'],
										'zen' => $_POST['zen'],
										'credits3' => $_POST['credits3'],
										'ruud' => $_POST['ruud'],
										'hunt' => $_POST['hunt'],
										'vip_type' => $_POST['vip_type'],
										'items' => $items
									]
								]
							];
							$this->vars['reward_list'] = array_merge($this->vars['reward_list'], $this->vars['new_config']);
                            $this->config->save_config_data($this->vars['reward_list'], 'achievement_rewards');
						}
						$this->vars['achData'] = $_POST;
						$this->vars['success'] = 'Achievement reward successfully updated.';
					}
					
					

					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.reward', $this->vars);
				}
				else{
					$this->vars['error'] = __('Invalid server.');
				}
			} else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
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
                $this->load->model('application/plugins/achievements/models/achievements');
                $this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                //load any js, css files if required
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/achievements.js';
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
					'sidebar_public_item' => 0, //add link to module in public sidebar menu, if template supports
					'account_panel_item' => 1, //add link in user account panel
					'donation_panel_item' => 0, //add link in donation page
					'description' => 'Quests for players' //description which will see user
				]);
				//create plugin config template
				$this->pluginaizer->create_config(['active' => 0, 'required_level' => 100, 'required_mlevel' => 0, 'required_resets' => 0, 'required_gresets' => 0, 'required_zen' => 100000, 'required_ruud' => 0, 'rankings_active' => 1, 'rankings_amount' => 50, 'rankings_cache_time' => 360]);
				//add sql scheme if there is any into website database
				//all schemes should be located in plugin_folder/sql_schemes
				$this->pluginaizer->add_sql_scheme('unlocked_achievements');
				$this->pluginaizer->add_sql_scheme('user_achievements');
				$this->pluginaizer->add_sql_scheme('claimed_rewards');

				$this->add_cron_task('ResetAchievementDaily', '0 0 * * *', 'Reset daily achievements.');
				$this->add_cron_task('ResetAchievementWeekly', '0 0 * * 0', 'Reset Weekly achievements.');
				$this->add_cron_task('ResetAchievementMonthly', '0 0 1 * *', 'Reset Monthly achievements.');
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
                $this->pluginaizer->delete_config()->remove_sql_scheme('unlocked_achievements')->remove_sql_scheme('user_achievements')->remove_sql_scheme('claimed_rewards')->remove_plugin();
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
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		private function add_cron_task($task, $time, $desc, $owerwrite = 0)
        {
            $file = BASEDIR . 'application' . DS . 'config' . DS . 'scheduler_config.json';
            $data = file_get_contents($file);
            $tasks = json_decode($data, true);
            if(!array_key_exists($task, $tasks['tasks'])){
                $tasks['tasks'][$task] = [
					'time' => $time,
					'status' => 1,
					'desc' => $desc
				];
            }
			else{
				if($owerwrite == 1){
					unset($tasks['tasks'][$task]);
					$tasks['tasks'][$task] = [
						'time' => $time,
						'status' => 1,
						'desc' => $desc
					];
				}
			}
            file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
        }
    }