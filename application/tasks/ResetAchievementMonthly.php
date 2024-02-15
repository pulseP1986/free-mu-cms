<?php

    class ResetAchievementMonthly extends Job
    {
        private $registry, $load, $config, $vars = [];

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
			
            $this->vars['achievements'] = $this->config->values('achievement_list');

            foreach($this->vars['achievements'] AS $server => $achievements){
                foreach($achievements AS $key => $data){
                    if(isset($data['period']) && $data['period'] == 3){
                        $this->reset_achievement($server, $data);
                    }
                }           
            }
        }
        
        // @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function reset_achievement($server, $data)
        {
            if($this->registry->website->db('web')->check_if_table_exists('DmN_User_Achievements')){
                $this->registry->website->db('web')->query('UPDATE DmN_User_Achievements SET amount_completed = 0, is_completed = 0, items = \''.json_encode($data['items']).'\', last_updated = '.time().' WHERE ach_id = \''.$data['id'].'\' AND server = \''.$server.'\'');
            }
        }
    }