<?php
    in_file();

    class plugin
    {
        private $registry;
        private $config;
        private $load;
        private $about_file;
        private $about = false;
        private $file_name = '';
        private $plugin_data = [];
        private $plugin_config;
        private $plugin_class = '';
        public $error = [];
        private $scheme;
        private $table_name;
		
		public function __construct(){
            $this->registry = controller::get_instance();
            $this->config = load_class('config');
            $this->load = load_class('load');
            $this->load->helper('website');
            $this->load->lib('session', ['DmNCMS']);
			$this->session->checkSession();
            $this->load->helper('meta');
        }

        public function set_plugin_class($class){
            $this->plugin_class = $class;
        }

        public function get_plugin_class(){
            return $this->plugin_class;
        }

        public function set_about(){
            $this->about_file = APP_PATH . DS . 'plugins' . DS . $this->plugin_class . DS . 'about.json';
            if(file_exists($this->about_file)){
                $this->about = $this->jsond(file_get_contents($this->about_file), true);
            } 
            else{
                $this->about = ['name' => $this->plugin_class, 'version' => false, 'description' => false, 'developed_by' => false, 'website' => false, 'update_url' => false];
            }
            return $this;
        }

        public function get_about(){
            $this->about_file = APP_PATH . DS . 'plugins' . DS . $this->plugin_class . DS . 'about.json';
            if(file_exists($this->about_file)){
                return $this->jsond(file_get_contents($this->about_file), true);
            }
            return false;
        }
		
		public function add_plugin($data = []){
            try{
                if(!is_array($data)){
                    throw new Exception('Plugin data should be array');
                }
                if(empty($data)){
                    throw new Exception('Plugin data is empty');
                }
                
                $config = $this->config->values('plugin_config');
                
                if(isset($config[$this->plugin_class])){
                    throw new Exception('Plugin data already exists. Skipping...'); 
                }
                
                $config[$this->plugin_class] = $data;
                $config[$this->plugin_class]['about'] = $this->about;
                return $this->config->save_config_data($config, 'plugin_config');
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
        }

        public function remove_plugin(){
            try{
                $config = $this->config->values('plugin_config');
                if(!isset($config[$this->plugin_class])){
                    throw new Exception('Plugin data not found. Skipping...'); 
                }
                
                unset($config[$this->plugin_class]);
                return $this->config->save_config_data($config, 'plugin_config');
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
        }

        public function enable_plugin(){
            try{
                $config = $this->config->values('plugin_config');
                if($config[$this->plugin_class] == false){
                    throw new Exception('Plugin data not found.');
                }
                if($config[$this->plugin_class]['installed'] == 1){
                    throw new Exception('Plugin already enabled.');
                }
                $config[$this->plugin_class]['installed'] = 1;
                return $this->config->save_config_data($config, 'plugin_config');
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
        }

        public function disable_plugin(){
            try{
                $config = $this->config->values('plugin_config');
                if($config[$this->plugin_class] == false){
                    throw new Exception('Plugin data not found.');
                }
                if($config[$this->plugin_class]['installed'] == 0){
                    throw new Exception('Plugin already disabled.');
                }
                $config[$this->plugin_class]['installed'] = 0;
                return $this->config->save_config_data($config, 'plugin_config');
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
        }
		
		public function add_sql_scheme($scheme_file){
            $file = APP_PATH . DS . 'plugins' . DS . $this->plugin_class . DS . 'sql_schemes' . DS . $scheme_file . '.json';
            if(file_exists($file)){
                $this->scheme = $this->jsond(file_get_contents($file), true);
                $this->table_name = array_key_first($this->scheme);
                if(is_array($this->scheme) && !empty($this->scheme)){
                    $this->create_table()->create_columns();
                }
            }
        }

        public function remove_sql_scheme($scheme_file){
            $file = APP_PATH . DS . 'plugins' . DS . $this->plugin_class . DS . 'sql_schemes' . DS . $scheme_file . '.json';
            if(file_exists($file)){
                $this->scheme = $this->jsond(file_get_contents($file), true);
                $this->table_name = array_key_first($this->scheme);
                if(is_array($this->scheme) && !empty($this->scheme)){
                    $this->remove_table();
                }
            }
            return $this;
        }

        private function create_table(){
            try{
                if($this->website->db('web')->check_if_table_exists($this->table_name)){
                    throw new Exception('Sql table ' . $this->table_name . ' already exists. Skipping...');
                }
                if(!isset($this->scheme[$this->table_name]['create_query'])){
                    throw new Exception('Unable to find table create query. Please correct your sql scheme file.');
                }
                $this->website->db('web')->query($this->scheme[$this->table_name]['create_query']);
                unset($this->scheme[$this->table_name]['create_query']);
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
            return $this;
        }

        private function remove_table(){
            try{
                if(!$this->website->db('web')->check_if_table_exists($this->table_name)){
                     throw new Exception('Table ' . $this->table_name . ' not found. Skipping...');
                }
                if(!$this->website->db('web')->remove_table($this->table_name)){
                    throw new Exception('Unable to remove table ' . $this->table_name . '. Skipping...');
                }
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
            return $this;
        }

        private function create_columns(){
            try{
                if(isset($this->scheme[$this->table_name]['create_query'])){
                    unset($this->scheme[$this->table_name]['create_query']);
                }
                foreach($this->scheme[$this->table_name] AS $key => $column_data){
                    if($this->website->db('web')->check_if_column_exists($key, $this->table_name) == null){
                        $this->website->db('web')->add_column($key, $this->table_name, $column_data);
                    }
                }
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
        }

        public function create_config($data = []){
            try{
                if(!is_array($data)){
                    throw new Exception('Plugin config data should be array');
                }
                if(empty($data)){
                    throw new Exception('Plugin config data is empty');
                }
                
                $this->plugin_config = $this->config->values($this->plugin_class);
                
                if(!empty($this->plugin_config)){
                   throw new Exception('Plugin config data already exists. Skipping...'); 
                }
                
                $this->data();

                if(isset($this->plugin_data[$this->plugin_class]['is_multi_server']) && $this->plugin_data[$this->plugin_class]['is_multi_server'] == 1){
                    foreach($this->website->server_list() AS $key => $value){
                        $this->plugin_config[$key] = $data;
                    }
                    return $this->config->save_config_data($this->plugin_config, $this->plugin_class);
                } 
                else{
                    return $this->config->save_config_data($data, $this->plugin_class);
                }
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
        }

        public function delete_config(){
            try{
                $config_file = APP_PATH . DS . 'config' . DS . $this->plugin_class . '.json';
                if(!file_exists($config_file)){
                    throw new Exception('Config file not found. Skipping...');
                }
                if(!unlink($config_file)){
                    throw new Exception('Unable to remove plugin config file. Skipping...');
                }
            } catch(\Exception $e){
                $this->error[] = $e->getMessage();
            }
            return $this;
        }

        public function data(){
            if(isset($this->plugin_data[$this->plugin_class])){
                return $this;
            } 
            else{				
                $this->plugin_data[$this->plugin_class] = $this->config->values('plugin_config', $this->plugin_class);
                return $this;
            }
        }

        public function value($value){
            if($this->plugin_data[$this->plugin_class] != false){
                if(array_key_exists($value, $this->plugin_data[$this->plugin_class])){
                    return $this->plugin_data[$this->plugin_class][$value];
                }
            }
            return false;
        }

        public function values(){
            if($this->plugin_data[$this->plugin_class] != false){
                return $this->plugin_data[$this->plugin_class];
            }
            return false;
        }

        public function plugin_config(){
            if(isset($this->plugin_config)){
                return $this->plugin_config;
            } 
            else{
                $this->plugin_config = $this->config->values($this->plugin_class);
                return $this->plugin_config;
            }
        }

        public function save_config($config){
            return $this->config->save_config_data($config, $this->plugin_class);
        }

        public function redirect($module){
            header('Location: ' . $module);
        }

        public function jsond($data, $array = true){
            return $this->config->from_json($data, 'plugin data', $array);
        }

        public function jsone($data, $pretty_print = JSON_PRETTY_PRINT){
            return $this->config->to_json($data, 'plugin data', $pretty_print);
        }

        public function __get($var){
            if(isset($this->registry->$var))
                return $this->registry->$var;
        }

        public function __set($key, $val){
            $this->$key = $val;
        }
    }

