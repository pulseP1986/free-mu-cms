<?php
    in_file();

    class meta
    {
        protected $request, $meta = [], $lang, $data = [];

        public function __construct()
        {
            $this->registry = controller::get_instance();
            $this->config = $this->registry->config;
            $this->request = $this->registry->request;
            $this->meta = $this->config->values('meta_config');
            $this->lang = $this->config->language();
            if(array_key_exists($this->lang, $this->meta)){
                $this->data = $this->meta[$this->lang];
            } else{
                $this->data = $this->meta['en'];
            }
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function request_meta_title()
        {
            if(array_key_exists($this->request->get_controller() . '/' . $this->request->get_method(), $this->data)){
                return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data[$this->request->get_controller() . '/' . $this->request->get_method()]['title']);
            } else{
                if(array_key_exists($this->request->get_controller(), $this->data)){
                    return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data[$this->request->get_controller()]['title']);
                } else{
                    return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data['default']['title']);
                }
            }
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function request_meta_keywords()
        {
            if(array_key_exists($this->request->get_controller() . '/' . $this->request->get_method(), $this->data)){
                return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data[$this->request->get_controller() . '/' . $this->request->get_method()]['keywords']);
            } else{
                if(array_key_exists($this->request->get_controller(), $this->data)){
                    return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data[$this->request->get_controller()]['keywords']);
                } else{
                    return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data['default']['keywords']);
                }
            }
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function request_meta_description()
        {
            if(array_key_exists($this->request->get_controller() . '/' . $this->request->get_method(), $this->data)){
                return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data[$this->request->get_controller() . '/' . $this->request->get_method()]['description']);
            } else{
                if(array_key_exists($this->request->get_controller(), $this->data)){
                    return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data[$this->request->get_controller()]['description']);
                } else{
                    return str_replace('%server_title%', $this->config->config_entry('main|servername'), $this->data['default']['description']);
                }
            }
        }
    }