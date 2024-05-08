<?php
    in_file();

    class rankings extends controller
    {
        protected $vars = [], $errors = [];

         	
        public function __construct(){
            parent::__construct();
            $this->load->helper('website');
            $this->load->lib('session', ['DmNCMS']);
			if(!in_array($this->request->get_method(), ['load_ranking_data', 'get_mark', 'load_kill_stats', 'top_player', 'top_guild'])){
				$this->session->checkSession();
			}
			$this->load->lib('csrf');		
			$this->load->lib("pagination");	
            $this->load->helper('breadcrumbs', [$this->request]);
            $this->load->helper('meta');
            $this->load->model('rankings');
        }

        public function index($server = '', $page = 1, $class = 'all'){
            if($server == ''){
                $server = array_keys($this->website->server_list());
                $this->vars['server'] = $server[0];
            } else{
                $this->serv = $this->website->server_list();
                if(!array_key_exists($server, $this->serv)){
                    throw new exception('Invalid server selected');
                }
                $this->vars['server'] = $server;
            }
            $this->vars['config'] = $this->config->values('rankings_config', $this->vars['server']);
            if($this->vars['config'] && $this->vars['config']['active'] == 1){
				$this->vars['page'] = $page;
				$this->vars['class'] = $class;
                $this->load->view($this->config->config_entry('main|template') . DS . 'rankings' . DS . 'view.index', $this->vars);
            } else{
                $this->disabled();
            }
        }

        public function online_players($server = ''){
            if($server == ''){
                $server = array_keys($this->website->server_list());
                $this->vars['server'] = $server[0];
            } else{
                $this->serv = $this->website->server_list();
                if(!array_key_exists($server, $this->serv)){
                    throw new exception('Invalid server selected');
                }
                $this->vars['server'] = $server;
            }
            $this->vars['config'] = $this->config->values('rankings_config', $this->vars['server']);
            $this->vars['table_config'] = $this->config->values('table_config', $this->vars['server']);
            if(isset($this->vars['config']['online_list']['active']) && $this->vars['config']['online_list']['active'] == 1){
                $this->vars['online'] = $this->Mrankings->load_online_players($this->vars['config']['online_list'], $this->vars['table_config'], $this->vars['server']);
				$this->load->view($this->config->config_entry('main|template') . DS . 'rankings' . DS . 'view.online', $this->vars);
            } else{
                $this->disabled();
            }
        }

        public function gm_list($server = ''){
            if($server == ''){
                $server = array_keys($this->website->server_list());
                $this->vars['def_server'] = $server[0];
            } else{
                $this->serv = $this->website->server_list();
                if(!array_key_exists($server, $this->serv)){
                    throw new exception('Invalid server selected');
                }
                $this->vars['def_server'] = $server;
            }
            $this->vars['gm_list'] = $this->Mrankings->load_gm_list($this->vars['def_server']);
            $this->load->view($this->config->config_entry('main|template') . DS . 'rankings' . DS . 'view.gm_list', $this->vars);
        }

        public function ban_list($type = 'chars', $server = ''){
            if(!in_array($type, ['chars', 'accounts'])){
                $this->vars['error'] = __('Invalid BanList Selected');
            } else{
                if($server == ''){
                    $server = array_keys($this->website->server_list());
                    $this->vars['def_server'] = $server[0];
                } else{
                    $this->serv = $this->website->server_list();
                    if(!array_key_exists($server, $this->serv)){
                        throw new exception('Invalid server selected');
                    }
                    $this->vars['def_server'] = $server;
                }
                $this->vars['def_type'] = $type;
                $this->vars['ban_list'] = $this->Mrankings->load_ban_list($type, $this->vars['def_server']);
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'rankings' . DS . 'view.ban_list', $this->vars);
        }

        public function load_ranking_data($page = 1, $class = 'all'){
			if(isset($_GET['type'])){
				$_POST['type'] = $_GET['type'];	
			}
			if(isset($_GET['type'])){
				$_POST['server'] = $_GET['server'];	
			}
			if(isset($_GET['top'])){
				$_POST['top'] = $_GET['top'];	
			}
            if(!isset($_POST['type'], $_POST['server'])){
                json(['error' => __('Unable to load ranking data.')]);
            } else{
                if(trim($_POST['type']) == '' || trim($_POST['server']) == ''){
                    json(['error' => __('Unable to load ranking data.')]);
                } else{
                    if((!in_array($_POST['type'], ['players', 'guilds', 'votereward', 'killer', 'online', 'gens', 'bc', 'ds', 'cc', 'cs', 'duels', 'duelking', 'duelqueen', 'hunt', 'huntg'])))
                        json(['error' => __('Invalid ranking selected.')]); 
					else{
                        if(!array_key_exists($_POST['server'], $this->website->server_list()))
                            json(['error' => __('Invalid server selected.')]); 
						else{
                            $this->load->model('character');
                            if(isset($_POST['class'])){
                                $this->Mrankings->class_filter($_POST['class']);
                            }
							else{
								if($class != 'all'){
									$this->Mrankings->class_filter($class);
								}
							}
                            $this->vars['config'] = $this->config->values('rankings_config', $_POST['server']);
                            $this->vars['top'] = (isset($_POST['top']) && is_numeric($_POST['top'])) ? (int)$_POST['top'] : false;
							if($_POST['type'] == 'players'){
								if($this->vars['top'] == false){
									$this->vars['top'] = $this->vars['config']['player']['count']; 
								}
								$link = $this->config->base_url . 'rankings/index/' . $_POST['server'].'/%s';
								if(isset($_POST['class'])){
									$link = $this->config->base_url . 'rankings/index/' . $_POST['server'].'/%s/'. $_POST['class'];
								}
								else{
									if($class != 'all'){
										$link = $this->config->base_url . 'rankings/index/' . $_POST['server'].'/%s/'. $class;
									}
								}
								//$this->pagination->initialize($page, $this->vars['top'], $this->Mrankings->countTotalPlayers($_POST['server'], $this->vars['config']), $link);
							}
                            json([
								$_POST['type'] => $this->Mrankings->get_ranking_data($_POST['type'], $_POST['server'], $this->vars['config'], $this->config->values('table_config', $_POST['server']), $this->vars['top'], $page), 
								'config' => $this->vars['config'], 
								'server_selected' => $_POST['server'], 
								'cache_time' => $this->website->get_cache_time(), 
								'base_url' => $this->config->base_url, 
								'tmp_dir' => $this->config->config_entry('main|template'),
								//'pagination' => ($_POST['type'] == 'players') ? $this->pagination->create_links() : false,
								'pos' => ($_POST['type'] == 'players') ? ($page == 1) ? 1 : (int)(($page - 1) * $this->vars['top']) + 1 : 1,
								'mu_version' => MU_VERSION,
								'killer_info' => (defined('ELITE_KILLER_INFO') && ELITE_KILLER_INFO == true) ? true : false,
								't' => time(),
								'endt' => strtotime(date('Y-m-d 23:59', strtotime('last day of '.date('M', time()))))
							]);
                        }
                    }
                }
            }
        }

        public function top_player(){
            if(!isset($_POST['server'])){
                json(['error' => __('Unable to load ranking data.')]);
            } else{
                if(trim($_POST['server']) == ''){
                    json(['error' => __('Unable to load ranking data.')]);
                } else{
                    if(!array_key_exists($_POST['server'], $this->website->server_list()))
                        json(['error' => __('Invalid server.')]); else{
                        $this->load->model('character');
                        header('Content-type: application/json');
                        json($this->Mrankings->get_ranking_data('players', $_POST['server'], $this->config->values('rankings_config', $_POST['server']), $this->config->values('table_config', $_POST['server']), 1));
                    }
                }
            }
        }

        public function top_guild(){
            if(!isset($_POST['server'])){
                json(['error' => __('Unable to load ranking data.')]);
            } else{
                if(trim($_POST['server']) == ''){
                    json(['error' => __('Unable to load ranking data.')]);
                } else{
                    if(!array_key_exists($_POST['server'], $this->website->server_list()))
                        json(['error' => __('Invalid server.')]); else{
                        $this->load->model('rankings');
                        header('Content-type: application/json');
                        json($this->Mrankings->get_ranking_data('guilds', $_POST['server'], $this->config->values('rankings_config', $_POST['server']), $this->config->values('table_config', $_POST['server']), 1));
                    }
                }
            }
        }
		
		public function load_kill_stats($server){
			if(defined('ELITE_KILLER_INFO') && ELITE_KILLER_INFO == true){
				$this->vars['start'] = (isset($_POST['start']) && is_numeric($_POST['start'])) ? $_POST['start'] : 1;
				if($this->vars['start'] == 0){
					$this->vars['start'] = 1;
				}
				$this->vars['per_page'] = (isset($_POST['length']) && is_numeric($_POST['length'])) ? $_POST['length'] : ELITE_KILLER_PER_PAGE;
				$this->vars['order_column'] = (isset($_POST['order'][0]['column']) && is_numeric($_POST['order'][0]['column'])) ? $_POST['order'][0]['column'] : 2;
				$this->vars['order_dir'] = (isset($_POST['order'][0]['dir'])) ? $_POST['order'][0]['dir'] : 'desc';
				
				$this->vars['kill_stats'] = $this->Mrankings->kill_stats($this->vars['start'], $this->vars['per_page'], $server, $this->vars['order_column'], $this->vars['order_dir']);
				$this->vars['total_records'] = $this->Mrankings->count_total_kill_stats($server);
				if($this->vars['kill_stats'] != false){
					foreach($this->vars['kill_stats'] AS $kills){
				
					$this->vars['data'][] = ['<a href="'.$this->config->base_url.'character/'.bin2hex($kills['Killer']).'/'.$server.'">'.$kills['Killer'].'</a>', '<a href="'.$this->config->base_url.'character/'.bin2hex($kills['Victim']).'/'.$server.'">'.$kills['Victim'].'</a>', date('Y-m-d H:i:s', strtotime($kills['KillDate']))];
					}						
				}
				else{
					$this->vars['data'] = [];
				}
			}
			else{
				$this->vars['data'] = [];
				$this->vars['total_records'] = 0;
			}
			json(["draw" => (int)$_POST['draw'], "recordsTotal" => $this->vars['total_records'], "recordsFiltered" => $this->vars['total_records'], "data" => $this->vars['data']]);
		}

        public function search($server){
            if($server == ''){
                $this->vars['error'] = __('Invalid server');
            } else{
                if(isset($_POST['name'])){
                    if($_POST['name'] == '')
                        $this->vars['error'] = __('Please enter search string'); else{
                        if(strlen($_POST['name']) < 2)
                            $this->vars['error'] = __('Search string should be atleast 2 characters long'); else{
                            $this->vars['list_players'] = $this->Mrankings->load_found_chars($_POST['name'], $server);
                            $this->vars['list_guilds'] = $this->Mrankings->load_found_guilds($_POST['name'], $server);
                        }
                    }
                }
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'rankings' . DS . 'view.search', $this->vars);
        }

        public function get_mark($mark = '', $size = 24){
			if($size > 256){
				$size = 24;
			}
            $mark = (strlen($mark) > 64) ? $this->website->hex2bin($mark) : $mark;
            $this->Mrankings->load_mark($mark, $size);
        }

        public function disabled(){
            $this->load->view($this->config->config_entry('main|template') . DS . 'view.module_disabled');
        }
    }