<?php

    class ResetAchievementWeekly extends Job
    {
        private $registry, $load, $config, $vars = [];

        public function __construct()
        {
            $this->registry = controller::get_instance();
            $this->config = $this->registry->config;
            $this->load = $this->registry->load;
        }
        
        public function execute()
        {
            $this->load->helper('website');
			
            $this->vars['achievements'] = $this->config->values('achievement_list');

            foreach($this->vars['achievements'] AS $server => $achievements){
                foreach($achievements AS $key => $data){
                    if(isset($data['period']) && $data['period'] == 2){
                        $this->reset_achievement($server, $data);
                    }
                }           
            }
        }
        
        private function reset_achievement($server, $data)
        {
            if($this->registry->website->db('web')->check_if_table_exists('DmN_User_Achievements')){
                $this->registry->website->db('web')->query('UPDATE DmN_User_Achievements SET amount_completed = 0, is_completed = 0, items = \''.json_encode($data['items']).'\', last_updated = '.time().' WHERE ach_id = \''.$data['id'].'\' AND server = \''.$server.'\'');
            }
        }
    }