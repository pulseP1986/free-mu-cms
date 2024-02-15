<?php

    class CheckUpdates extends Job
    {
        private $registry, $config, $load, $current_version, $lattest_version;

        public function __construct()
        {
            $this->registry = controller::get_instance();
            $this->config = $this->registry->config;
            $this->load = $this->registry->load;
        }

        public function execute()
        {
            $this->load->helper('website');
            $this->registry->website->set_cache('available_upgrades', $this->get_available_upgrade(), (3600 * 24) * 14);
            return true;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function get_available_upgrade()
        {
            if($file = get_autoupdate_file('version_control.json')){
                $patch_data = json_decode($file, true);
                if(is_array($patch_data)){
                    $this->current_version = $this->get_cms_version();
					if($this->current_version != ''){
						$this->lattest_version = key($patch_data['lattest_version']);
						$found = false;
						foreach($patch_data['sub_versions'] AS $k => $val){
							if($k > $this->current_version){
								$patch_data['sub_versions'][$k]['change_log'] = $this->get_change_log($k);
								$found = true;
							} else{
								unset($patch_data['sub_versions'][$k]);
							}
						}
						if($this->lattest_version > $this->current_version){
							$patch_data['lattest_version'][$this->lattest_version]['change_log'] = $this->get_change_log($this->lattest_version);
							$found = true;
						}
						if($found)
							return $patch_data;
					}
                }
            }
            return false;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function get_cms_version()
        {
			if(is_readable($path = BASEDIR . 'application' . DS . 'config' . DS . 'cms_config.json')){
				return json_decode(file_get_contents($path), true)['version'];
			}
			return '';
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function get_change_log($version)
        {
            $change_log = get_autoupdate_file($version . '/change_log.txt');
            if($change_log != false){
                return $change_log;
            }
            return '';
        }
    }