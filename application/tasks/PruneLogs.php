<?php

    class PruneLogs extends Job
    {
        private $registry, $config, $load;

        public function __construct()
        {
            $this->registry = controller::get_instance();
            $this->config = $this->registry->config;
            $this->load = $this->registry->load;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function execute()
        {
            $this->load->helper('website');
            return true;
        }
        
        // @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function add_cron_task($task, $time)
        {
            $file = BASEDIR . 'application' . DS . 'config' . DS . 'scheduler_config.json';
            $data = file_get_contents($file);
            $tasks = json_decode($data, true);
            if(!array_key_exists($task, $tasks['tasks'])){
                $tasks['tasks'][$task] = $time;
            }
            file_put_contents($file, json_encode($tasks));
        }
    }