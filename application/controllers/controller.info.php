<?php
    in_file();

    class info extends controller
    {
        public $vars = [], $errors = [];

        public function __construct(){
            parent::__construct();
            $this->load->helper('website');
            $this->load->lib('session', ['DmNCMS']);
			$this->session->checkSession();
			$this->load->lib('csrf');						 
            $this->load->model('character');
			$this->load->model('stats');
            $this->load->helper('breadcrumbs', [$this->request]);
            $this->load->helper('meta');
            $this->load->lib("itemimage");
            $this->load->lib("iteminfo");
        }

        public function index(){
            throw new Exception('Nothing to see in here.');
        }

        public function character($name = '', $server = ''){
            $this->load->model('account');
			
            if($server == ''){
                throw new Exception('Invalid server selected.');
            }
			
            if($name == ''){
                $this->vars['error'] = __('Invalid Character');
            } else{
                $this->Mcharacter->load_character_info($this->website->hex2bin($name), $server);
                if($this->Mcharacter->char_info != false){
					$this->vars['hidden'] = false;
					if($this->config->is_plugin_installed('hide_info')){
						$this->load->model('application/plugins/hide_info/models/hide_info'); 
						$this->vars['hidden'] = $this->Mhide_info->check_hide_time($this->Mcharacter->char_info['AccountId'], $server);
					}
                    $this->vars['status'] = $this->Mcharacter->get_status($this->Mcharacter->char_info['AccountId'], $server);
                    if($this->vars['status'] != false && $this->vars['status']['GameIDC'] != $this->Mcharacter->char_info['Name'] && $this->vars['status']['ConnectStat'] == 1){
                        $this->vars['status']['ConnectStat'] = 0;
                    }
                    $this->vars['country_code'] = ($this->vars['status'] != false) ? $this->website->get_country_code($this->vars['status']['IP']) : 'us';
                    $this->vars['country'] = $this->website->codeToCountryName($this->vars['country_code']);
                    $this->vars['char_list'] = $this->Mcharacter->load_chars($this->Mcharacter->char_info['AccountId'], $server);
                    if($this->vars['guild_check'] = $this->Mcharacter->check_guild($this->Mcharacter->char_info['Name'], $server)){
                        $this->vars['guild_info'] = $this->Mcharacter->load_guild_info($this->vars['guild_check']['G_Name'], $server);
                        $this->vars['member_count'] = $this->Mcharacter->guild_member_count($this->vars['guild_check']['G_Name'], $server);
                    } 
					else{
                        $this->vars['no_guild'] = true;
                    }
					$this->vars['vote_count'] = $this->Mstats->count_total_votes($this->Mcharacter->char_info['AccountId'], $server);
					if($this->vars['vote_count'] == null){
						$this->vars['vote_count'] = 0;
					}
                    if($this->config->config_entry('character_' . $server . '|show_equipment') == 1){
                        $this->vars['equipment'] = $this->Mcharacter->load_equipment($server);
						if(isset($this->vars['equipment'][12]) && $this->vars['equipment'][12] != 0){
							$this->vars['pentagram_data'] = $this->Mcharacter->load_pentagram_data($this->Mcharacter->char_info['AccountId'], $this->Mcharacter->char_info['Name'], $server, $this->vars['equipment'][12]['socket']);
						}
						else{
							$this->vars['pentagram_data'] = false;
						}
                    }
                    $this->vars['inventory'] = $this->Mcharacter->load_inventory(1, $server);
					$this->vars['artifacts'] = $this->Mcharacter->load_artifacts($this->Mcharacter->char_info['Name'], $server);
                } else{
                    $this->vars['error'] = __('Invalid Character');
                }
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'info' . DS . 'view.character', $this->vars);
        }

        public function guild($name = '', $server = ''){
            if($server == ''){
               throw new Exception('Invalid server selected.');
            }
			
            if($name == ''){
                $this->vars['errors'] = __('Invalid Guild');
            } else{
                if($this->vars['guild_info'] = $this->Mcharacter->get_guild_info($this->website->hex2bin($name), $server))
                    $this->vars['guild_members'] = $this->Mcharacter->get_guild_members($this->website->hex2bin($name), $server); 
				else
                    $this->vars['error'] = __('Invalid Guild');
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'info' . DS . 'view.guild', $this->vars);
        }
    }