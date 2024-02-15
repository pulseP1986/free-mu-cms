<?php

class _plugin_stats_specialization extends controller implements pluginInterface{
	private $pluginaizer;
	private $vars = [];
	
	/**
	 *
	 * Plugin constructor
	 * Initialize plugin class
	 *
	 */
	 
	public function __construct(){
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
	 
	public function index(){
		if($this->pluginaizer->data()->value('installed') == false){
			throw new Exception('Plugin has not yet been installed.');
		}
		else{
			if($this->pluginaizer->data()->value('installed') == 1){
				if($this->pluginaizer->data()->value('is_public') == 0){
					$this->user_module();
				}
				else{
					$this->public_module();
				}
			}
			else{
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
	
	private function user_module(){
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
					}
					else{
						$this->vars['config_not_found'] = __('Plugin configuration not found.');
					}
				}
				if($this->vars['plugin_config']['active'] == 0){
					$this->vars['module_disabled'] =  __('This module has been disabled.');
				}
				else{
					$this->load->model('application/plugins/stats_specialization/models/stats_specialization');
					$this->vars['char_list'] = $this->pluginaizer->Mstats_specialization->load_char_list($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
				}
			}
			else{
				$this->vars['config_not_found'] = __('Plugin configuration not found.');
			}
			//set js
			$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/stats_specialization.js';
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.stats_specialization', $this->vars);
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
		}
	}
	
	/**
	 *
	 * Save stats specialization
	 * 
	 * return mixed
	 *
	 */
	
	public function save($character = ''){
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
					}
					else{
						$this->vars['config_not_found'] = __('Plugin configuration not found.');
					}
					$this->vars['plugin_config']['payment_name'] = $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['payment_type'], $this->pluginaizer->session->userdata(['user' => 'server']));
				}
				
				$this->load->model('application/plugins/stats_specialization/models/stats_specialization');
				
				if(!$this->pluginaizer->Mstats_specialization->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])))
					$this->vars['error'] = __('Please logout from game.');
				else{
					if($this->check_valid_char($character)){
						if(count($_POST) > 0){
							$title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
							
							if($title == '')
								$this->vars['error'] = __('Please enter specialization title');
							else{
								if($this->vars['char_data']['cLevel'] < $this->vars['plugin_config']['req_level'])
									$this->vars['error'] = sprintf(__('Your character level is too low. Required: %d level.'), $this->vars['plugin_config']['req_level']);
								else{
									if($this->pluginaizer->Mstats_specialization->count_specializations($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data']['id']) >= $this->vars['plugin_config']['max_specializations'])
										$this->vars['error'] = sprintf(__('You have reached max specializations: %d, please remove some specialization before proceeding.'), $this->vars['plugin_config']['max_specializations']);
									else{
										if($this->pluginaizer->Mstats_specialization->check_specialization($title, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data']['id']))
											$this->vars['error'] = __('Specialization with this name already exists.');
										else{
											if($this->vars['plugin_config']['price'] > 0){
												$status = $this->pluginaizer->website->get_user_credits_balance(
													$this->pluginaizer->session->userdata(['user' => 'username']), 
													$this->pluginaizer->session->userdata(['user' => 'server']), 
													$this->vars['plugin_config']['payment_type'], $this->pluginaizer->session->userdata(['user' => 'id'])
												);
												if($status['credits'] < $this->vars['plugin_config']['price']){
													$this->vars['error'] = sprintf(__('You have insufficient amount of %s'), $this->vars['plugin_config']['payment_name']);
												}
												else{
													$this->pluginaizer->website->charge_credits(
														$this->pluginaizer->session->userdata(['user' => 'username']), 
														$this->pluginaizer->session->userdata(['user' => 'server']), 
														$this->vars['plugin_config']['price'], 
														$this->vars['plugin_config']['payment_type'], 
														$this->pluginaizer->session->userdata(['user' => 'id'])
													);
												}
											}
											if(!isset($this->vars['error'])){
												$this->pluginaizer->Mstats_specialization->save_specialization(
													$title, 
													$this->pluginaizer->session->userdata(['user' => 'username']), 
													$this->pluginaizer->session->userdata(['user' => 'server']), 
													$this->vars['char_data']
												);
												$this->vars['success'] = __('Specialization successfully saved.');
											}
										}
									}
								}
							}
						}
					}
				}
			}
			else{
				$this->vars['config_not_found'] = __('Plugin configuration not found.');
			}
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.save_stats', $this->vars);
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/save/' . $character);
		}
	}
	
	/**
	 *
	 * Remove saved stats specialization
	 * 
	 * return mixed
	 *
	 */
	
	public function remove(){
		//check if visitor has user privilleges
		if($this->pluginaizer->session->is_user()){
			//load website helper
			$this->load->helper('website');		
				
			$this->load->model('application/plugins/stats_specialization/models/stats_specialization');
			
			if(count($_POST) > 0){
				$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
				$char = isset($_POST['char_id']) ? (int)$_POST['char_id'] : '';
				$id = isset($_POST['id']) ? (int)$_POST['id'] : '';
				
				if($this->check_valid_char($name . '-' . $char)){
					if($this->pluginaizer->Mstats_specialization->check_specialization(false, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data']['id'], $id)){
						$this->pluginaizer->Mstats_specialization->remove_specialization($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data']['id'], $id);
						echo $this->pluginaizer->jsone(['success' => __('Specialization successfully removed.')]);
					}
					else{
						echo $this->pluginaizer->jsone(['error' => __('Stats specialization not found.')]);
					}
				}
				else{
					if(isset($this->vars['char_error']))
						echo $this->pluginaizer->jsone(['error' => $this->vars['char_error']]);
				}
			}	
		}
		else{
			echo $this->pluginaizer->jsone(['error' => __('Please login into website.')]);
		}	
	}
	
	/**
	 *
	 * Load stats from specialization into character and save current stats
	 * 
	 * return mixed
	 *
	 */
	
	public function load(){
		//check if visitor has user privilleges
		if($this->pluginaizer->session->is_user()){
			//load website helper
			$this->load->helper('website');		
				
			$this->load->model('application/plugins/stats_specialization/models/stats_specialization');
			
			if(count($_POST) > 0){
				$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
				$char = isset($_POST['char_id']) ? (int)$_POST['char_id'] : '';
				$id = isset($_POST['id']) ? (int)$_POST['id'] : '';
				
				if(!$this->pluginaizer->Mstats_specialization->check_connect_stat($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server'])))
					echo $this->pluginaizer->jsone(['error' => __('Please logout from game.')]);
				else{
					if($this->check_valid_char($name . '-' . $char)){
						if($this->vars['specialization_data'] = $this->pluginaizer->Mstats_specialization->check_specialization(false, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data']['id'], $id)){
							$this->pluginaizer->Mstats_specialization->set_character_stats($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data']['id'], $this->vars['specialization_data']);
							$this->pluginaizer->Mstats_specialization->set_specialization_stats($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['char_data'], $this->vars['specialization_data']['id']);				
							echo $this->pluginaizer->jsone(['success' => __('Specialization successfully loaded.')]);
						}
						else{
							echo $this->pluginaizer->jsone(['error' => __('Stats specialization not found.')]);
						}
					}
					else{
						if(isset($this->vars['char_error']))
							echo $this->pluginaizer->jsone(['error' => $this->vars['char_error']]);
					}
				}
			}	
		}
		else{
			echo $this->pluginaizer->jsone(['error' => __('Please login into website.')]);
		}	
	}
	
	/**
	 *
	 * Check if character is valid and exists in database
	 * 
	 * return bool
	 *
	 */
	
	private function check_valid_char($char = ''){
		if(strpos($char, '-') !== false){
			$id = substr(strrchr($char, '-'), 1);
			
			if(is_numeric($id)){
				if(!$this->vars['char_data'] = $this->pluginaizer->Mstats_specialization->check_char($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $id)){
					$this->vars['char_error'] = __('Character not found.');
				}
				else{
					return true;
				}
			}
			else{
				$this->vars['char_error'] = __('Invalid character');
			}
		   
		} 
		else{
			$this->vars['char_error'] = __('Invalid character');
		}
		return false;
	}
	
	/**
	 *
	 * Load public module data
	 * 
	 * return mixed
	 *
	 */
	
	private function public_module(){
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
	 
	public function admin(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			$this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			//load any js, css files if required
			$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/stats_specialization.js';
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
		
		}
		else{
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
	
	public function save_settings(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			if(isset($_POST['server']) && $_POST['server'] != 'all'){
				foreach($_POST AS $key => $val){
					if($key != 'server'){
						$this->vars['plugin_config'][$_POST['server']][$key] = $val;
					}
				}
			}
			else{
				foreach($_POST AS $key => $val){
					if($key != 'server'){
						$this->vars['plugin_config'][$key] = $val;
					}
				}
			}
			if($this->pluginaizer->save_config($this->vars['plugin_config'])){
				echo $this->pluginaizer->jsone(['success' => 'Plugin configuration successfully saved']);
			}
			else{
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
	
	public function install(){
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
				'description' => 'Here you can save and load your stat builds for using in different situations' //description which will see user
			]);
			
			//create plugin config template
			$this->pluginaizer->create_config([
				'active' => 0,
				'req_level' =>  400,
				'max_specializations' => 5,
				'price' => 500,
				'payment_type' => 1
			]);
			//add sql scheme if there is any into website database
			//all schemes should be located in plugin_folder/sql_schemes
			$this->pluginaizer->add_sql_scheme('stats_specialization');
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				$data['error'] = $this->pluginaizer->error;
			}
			$data['success'] = 'Plugin installed successfully';
			echo $this->pluginaizer->jsone($data);
		}
		else{
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
	
	public function uninstall(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//delete plugin config and remove plugin data
			$this->pluginaizer->delete_config()->remove_sql_scheme('stats_specialization')->remove_plugin();
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
			}
			echo $this->pluginaizer->jsone(['success' => 'Plugin uninstalled successfully']);
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
	
	public function enable(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//enable plugin
			$this->pluginaizer->enable_plugin();
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
			}
			else{		
				echo $this->pluginaizer->jsone(['success' => 'Plugin successfully enabled.']);
			}
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
	
	public function disable(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//disable plugin
			$this->pluginaizer->disable_plugin();
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
			}
			else{		
				echo $this->pluginaizer->jsone(['success' => 'Plugin successfully disabled.']);
			}
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
	
	public function about(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//create plugin info
			$about = $this->pluginaizer->get_about();
			if($about != false){
				$description = '<div class="box-content">
								<dl>
								  <dt>Plugin Name</dt>
								  <dd>'.$about['name'].'</dd>
								  <dt>Version</dt>
								  <dd>'.$about['version'].'</dd>
								  <dt>Description</dt>
								  <dd>'.$about['description'].'</dd>
								  <dt>Developed By</dt>
								  <dd>'.$about['developed_by'].' <a href="'.$about['website'].'" target="_blank">'.$about['website'].'</a></dd>
								</dl>            
							</div>';
			}
			else{
				$description = '<div class="alert alert-info">Unable to find plugin description.</div>';
			}
			echo $this->pluginaizer->jsone(['about' => $description]);
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
}