<?php
    //in_file();

    class Mcharacter extends model
    {
        private $characters = [], $guild_info = [], $ml_points = 0, $ml_level = 0, $skillTreeTable, $defaultStats = [], $charge_from_zen_wallet = 0;
        public $error = false, $vars = [], $char_info = [];

        public function __contruct()
        {
            parent::__construct();
        }

        public function __set($key, $val)
        {
            $this->vars[$key] = $val;
        }

        public function __isset($name)
        {
            return isset($this->vars[$name]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function load_char_list()
        {
			$ruud = ', 0 AS Ruud';
			if(MU_VERSION >= 5){
				$ruud = ', Ruud';
			}
			if(MU_VERSION >= 12){
				if($this->game_db->check_if_column_exists('RuudMoney', 'Character') != false){
					$ruud = ', RuudMoney AS Ruud';
				}
				else{
					$ruud = ', Ruud';
				}
			}
            $stmt = $this->game_db->prepare('SELECT Name, cLevel, Class, ' . $this->reset_column($this->session->userdata(['user' => 'server'])) . $this->greset_column($this->session->userdata(['user' => 'server'])) . ' Money, LevelUpPoint, CtlCode, PkCount, PkLevel '.$ruud.' FROM Character WHERE AccountId = :account');
            $stmt->execute([':account' => $this->session->userdata(['user' => 'username'])]);
            $i = 0;
            while($row = $stmt->fetch()){
                $this->characters[] = ['name' => $row['Name'], 'level' => $row['cLevel'], 'Class' => $row['Class'], 'resets' => $row['resets'], 'gresets' => $row['grand_resets'], 'money' => $row['Money'], 'points' => $row['LevelUpPoint'], 'ctlcode' => $row['CtlCode'], 'pkcount' => $row['PkCount'], 'pklevel' => $row['PkLevel'], 'CtlCode' => $row['CtlCode'], 'Ruud' => $ruud];
                $i++;
            }
            if($i > 0){
                return $this->characters;
            } else{
                return false;
            }
        }

        private function reset_column($server = '')
        {
            $resets = $this->config->values('table_config', [$server, 'resets', 'column']);
            if($resets && $resets != ''){
                return $resets . ' AS resets,';
            }
            return '0 AS resets,';
        }

        private function greset_column($server = '')
        {
            $grand_resets = $this->config->values('table_config', [$server, 'grand_resets', 'column']);
            if($grand_resets && $grand_resets != ''){
                return $grand_resets . ' AS grand_resets,';
            }
            return '0 AS grand_resets,';
        }

        public function check_char($char = '', $custom_field = '', $lock = false)
        {
            $c = ($char != '') ? $char : $this->vars['character'];
			$with_lock = ($lock == true) ? 'WITH (UPDLOCK,HOLDLOCK)' : '';
			$leadership = (MU_VERSION < 1) ? '0 AS Leadership,' : 'Leadership,';
            $stmt = $this->game_db->prepare('SELECT Name, Money, Class, cLevel, ' . $this->reset_column($this->session->userdata(['user' => 'server'])) . $this->greset_column($this->session->userdata(['user' => 'server'])) . ' LevelUpPoint, Strength, Dexterity, Vitality, Energy, '.$leadership.' PkLevel, PkCount, CtlCode, MagicList, last_reset_time, '.$this->website->get_char_id_col($this->session->userdata(['user' => 'server'])).' AS id ' . $custom_field . ' FROM Character '.$with_lock.' WHERE AccountId = :user AND Name = :char');
            $stmt->execute([':user' => $this->session->userdata(['user' => 'username']), ':char' => $this->website->c($c)]);
            if($this->char_info = $stmt->fetch()){
                $this->char_info['mlevel'] = $this->load_master_level($this->char_info['Name'], $this->session->userdata(['user' => 'server']));
                $this->get_inventory_content($this->char_info['Name']);
                $this->getQuest($this->char_info['Name'], $this->session->userdata(['user' => 'server']));
                return true;
            }
            return false;
        }

        private function getQuest($char, $server)
        {
            $sql = (DRIVER == 'pdo_odbc') ? 'Quest' : 'CONVERT(IMAGE, Quest) AS Quest';
            $stmt = $this->website->db('game', $server)->prepare('SELECT ' . $sql . ' FROM Character WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($char)]);
			if($quest = $stmt->fetch()){
				$this->char_info['Quest'] = $this->clean_hex($quest['Quest']);
			}
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function check_char_no_account($char = '', $server)
        {
			$leadership = (MU_VERSION < 1) ? '0 AS Leadership,' : 'Leadership,';
            $stmt = $this->website->db('game', $server)->prepare('SELECT AccountId, Name, Money, Class, cLevel, ' . $this->reset_column($server) . $this->greset_column($server) . ' LevelUpPoint, Strength, Dexterity, Vitality, Energy, '.$leadership.' PkLevel, PkCount, CtlCode, MagicList, last_reset_time, '.$this->website->get_char_id_col($server).' AS id FROM Character WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($char)]);
            if($this->char_info = $stmt->fetch()){
                $this->char_info['mlevel'] = $this->load_master_level($this->char_info['Name'], $server);
                $this->get_inventory_content($char);
                return true;
            }
            return false;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function check_zen($required = 0, $multiply = 0, $col = 'resets')
        {
            if($this->session->userdata('vip')){
                $required -= $this->session->userdata(['vip' => 'reset_price_decrease']);
            }
            if($multiply == 1){
                $this->char_info['res_money'] = $required * ($this->char_info[$col] + 1);
            } 
			else{
                $this->char_info['res_money'] = $required;
            }
            return ($this->char_info['Money'] >= $this->char_info['res_money']) ? true : $this->char_info['res_money'];
        }

        public function check_zen_wallet($required = 0, $multiply = 0, $col = 'resets')
        {
            $this->charge_from_zen_wallet = 1;
            $status = $this->website->get_user_credits_balance($this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), 3, $this->session->userdata(['user' => 'id']));
            return ($status['credits'] >= $this->char_info['res_money']) ? true : $this->char_info['res_money'];
        }

        public function check_lvl($req_lvl = 400)
        {
            if($this->session->userdata('vip')){
                $req_lvl -= $this->session->userdata(['vip' => 'reset_level_decrease']);
            }
            return ($this->char_info['cLevel'] >= $req_lvl) ? true : $req_lvl;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function check_mlvl($req_lvl = 400)
        {
            return ($this->char_info['mlevel'] >= $req_lvl) ? true : $req_lvl;
        }

        public function check_resets($req_resets = 100)
        {
            return ($this->char_info['resets'] >= $req_resets);
        }

        public function check_gresets($req_gresets = 0)
        {
            return ($this->char_info['grand_resets'] >= $req_gresets);
        }

        public function check_stats()
        {
            $this->vars['str_stat'] = isset($this->vars['str_stat']) ? (int)$this->vars['str_stat'] : 0;
            $this->vars['agi_stat'] = isset($this->vars['agi_stat']) ? (int)$this->vars['agi_stat'] : 0;
            $this->vars['ene_stat'] = isset($this->vars['ene_stat']) ? (int)$this->vars['ene_stat'] : 0;
            $this->vars['vit_stat'] = isset($this->vars['vit_stat']) ? (int)$this->vars['vit_stat'] : 0;
            $this->vars['com_stat'] = isset($this->vars['com_stat']) ? (int)$this->vars['com_stat'] : 0;
            $this->vars['allstats'] = in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78]) ? ($this->vars['str_stat'] + $this->vars['agi_stat'] + $this->vars['ene_stat'] + $this->vars['vit_stat'] + $this->vars['com_stat']) : ($this->vars['str_stat'] + $this->vars['agi_stat'] + $this->vars['ene_stat'] + $this->vars['vit_stat']);
        }

        public function set_new_stats()
        {
            $this->vars['new_str'] = $this->show65kStats($this->char_info['Strength']) + $this->vars['str_stat'];
            $this->vars['new_agi'] = $this->show65kStats($this->char_info['Dexterity']) + $this->vars['agi_stat'];
            $this->vars['new_ene'] = $this->show65kStats($this->char_info['Energy']) + $this->vars['ene_stat'];
            $this->vars['new_vit'] = $this->show65kStats($this->char_info['Vitality']) + $this->vars['vit_stat'];
            $this->vars['new_com'] = in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78]) ? $this->show65kStats($this->char_info['Leadership']) + $this->vars['com_stat'] : 0;
            $this->vars['new_lvlup'] = $this->char_info['LevelUpPoint'] - $this->vars['allstats'];
        }

        public function check_max_stat_limit()
        {
            if($this->vars['new_str'] > $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats')){
                $this->vars['error'] = 'Max Strength: ' . $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats');
            }
            if($this->vars['new_agi'] > $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats')){
                $this->vars['error'] = 'Max Agility: ' . $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats');
            }
            if($this->vars['new_ene'] > $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats')){
                $this->vars['error'] = 'Max Energy: ' . $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats');
            }
            if($this->vars['new_vit'] > $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats')){
                $this->vars['error'] = 'Max Vitality: ' . $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats');
            }
            if(in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78])){
                if($this->vars['new_com'] > $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats')){
                    $this->vars['error'] = 'Max Command: ' . $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|max_stats');
                }
            }
            if(isset($this->vars['error']))
                return false;
            return true;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function add_stats($char = '')
        {
			$data = [
				':new_lvlup' => $this->vars['new_lvlup'], 
				':new_str' => $this->vars['new_str'], 
				':new_agi' => $this->vars['new_agi'], 
				':new_ene' => $this->vars['new_ene'], 
				':new_vit' => $this->vars['new_vit']
			];
			$leadership = '';
			
			if(MU_VERSION >= 1){
				$ld = [':new_com' => $this->vars['new_com']];
				$data = $data + $ld;
				$leadership = ', Leadership = :new_com';
			}
			$otherData = [
				':char' => $char, 
				':user' => $this->session->userdata(['user' => 'username'])
			];
			$data = $data + $otherData;
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = :new_lvlup, Strength = :new_str, Dexterity = :new_agi, Energy = :new_ene, Vitality = :new_vit '.$leadership.' WHERE Name = :char AND AccountId = :user');
            $stmt->execute($data);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function reset_character()
        {
			$location = 'MapNumber = 0, MapPosX = 123, MapPosY = 130,';
			if(!defined('CRYSTALMU')){
				if(in_array($this->char_info['Class'], [32,33,34,35,39])){
					$location = 'MapNumber = 3, MapPosX = 175, MapPosY = 114,';
				}
				if(in_array($this->char_info['Class'], [80,81,82,83,84,87])){
					$location = 'MapNumber = 51, MapPosX = 52, MapPosY = 226,';
				}
			}
			$level_after_reset = isset($this->char_info['res_info']['level_after_reset']) ? (int)$this->char_info['res_info']['level_after_reset'] : 1;
			
            if($this->charge_from_zen_wallet == 0){
                $query = 'UPDATE Character SET ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' = ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' + 1, clevel = '.$level_after_reset.', Money = Money - :reset_money, '.$location.' Experience = 0, last_reset_time = :time WHERE Name = :char AND AccountId = :user';
                $data = [':reset_money' => $this->char_info['res_money'], ':time' => time(), ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])];
            } else{
                $this->website->charge_credits($this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->char_info['res_money'], 3);
                $query = 'UPDATE Character SET ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' = ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' + 1, clevel = '.$level_after_reset.', '.$location.' Experience = 0, last_reset_time = :time WHERE Name = :char AND AccountId = :user';
                $data = [':time' => time(), ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])];
            }
            $stmt = $this->game_db->prepare($query);
            $stmt->execute($data);
            if($this->char_info['res_info']['clear_magic'] != 0){
                $this->clear_magic_list();
            }
            $this->clear_inventory($this->char_info['res_info']);
            if($this->char_info['res_info']['clear_stats'] != 0){
                $this->clear_reset_stats();
            }
            if($this->char_info['res_info']['clear_level_up'] != 0){
                $this->clear_reset_levelup();
            }
            $this->add_bonus_reset_points();
            if($this->char_info['res_info']['bonus_gr_points'] == 1 && $this->char_info['bonus_greset_stats_points'] > 0){
                $this->add_bonus_stats_for_gresets();
            }
            if(defined('RES_CUSTOM_BACKUP_MASTER') && RES_CUSTOM_BACKUP_MASTER == true){
                if(in_array($this->char_info['Class'], [2, 3, 18, 19, 34, 35, 49, 50, 65, 66, 82, 83, 97, 98])){
                    $this->backup_master_level();
                    $this->change_reset_class();
                }
            }
            if($this->char_info['res_info']['bonus_credits'] != 0){
                $this->add_account_log('Reward ' . $this->config->config_entry('credits_' . $this->session->userdata(['user' => 'server']) . '|title_1') . ' for reset: ' . $this->vars['character'] . '', $this->char_info['res_info']['bonus_credits'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
                $this->add_bonus_credits(1, $this->char_info['res_info']['bonus_credits']);
            }
            if($this->char_info['res_info']['bonus_gcredits'] != 0){
                $this->add_account_log('Reward ' . $this->config->config_entry('credits_' . $this->session->userdata(['user' => 'server']) . '|title_2') . ' for reset: ' . $this->vars['character'] . '', $this->char_info['res_info']['bonus_gcredits'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
                $this->add_bonus_credits(2, $this->char_info['res_info']['bonus_gcredits']);
            }
			if($this->char_info['res_info']['bonus_credits'] == 0 && $this->char_info['res_info']['bonus_gcredits'] == 0){
                $this->add_account_log('Character ' . $this->vars['character'] . ' made reset', 0, $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
            }
            if(isset($this->char_info['res_info']['bonus_ruud']) && $this->char_info['res_info']['bonus_ruud'] > 0){
				$this->add_ruud($this->char_info['res_info']['bonus_ruud'], $this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
			}
			if(isset($this->char_info['res_info']['clear_masterlevel']) && $this->char_info['res_info']['clear_masterlevel'] == 1){
				$skill_tree = $this->reset_skill_tree($this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skill_tree_type'), $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skilltree_reset_level'), $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skilltree_reset_points'), $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skilltree_points_multiplier'));
			}
			
			$this->vars['current_date'] = date('Y-m-d', time());
			
			if($this->checkResetLog($this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['current_date']) == false){
				$this->insertResetLog($this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['current_date']);
			}
			else{
				$this->updateResetLog($this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['current_date']);
			}
		
            return true;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function checkResetLog($char, $user, $server, $date){
			return $this->website->db('web')->query('SELECT id, resets FROM DmN_Character_Reset_Log WHERE char_id = '.$this->website->db('web')->sanitize_var($char).' AND account = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\' AND date = \''.$this->website->db('web')->sanitize_var($date).'\'')->fetch();
		}
		
		public function getTotalResets($char, $user, $server){
			return $this->website->db('web')->query('SELECT SUM(resets) AS resets FROM DmN_Character_Reset_Log WHERE char_id = '.$this->website->db('web')->sanitize_var($char).' AND account = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'')->fetch()['resets'];
		}
	
		public function clearResetLog($char, $user, $server, $date){
			$stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Character_Reset_Log WHERE char_id = :char_id AND account = :account AND server = :server and date >= :date');
			$stmt->execute([
				':char_id' => $char, 
				':account' => $user, 
				':server' => $server,
				':date' => $date
			]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	
		public function insertResetLog($char, $user, $server, $date){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Character_Reset_Log (char_id, account, server, resets, date) VALUES (:char_id, :account, :server, 1, :date)');
			$stmt->execute([
				':char_id' => $char,
				':account' => $user,
				':server' => $server,
				':date' => $date
			]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	
		public function updateResetLog($char, $user, $server, $date){
			$stmt = $this->website->db('web')->prepare('UPDATE DmN_Character_Reset_Log SET resets = resets + 1 WHERE  char_id = :char_id AND account = :account AND server = :server AND date = :date');
			$stmt->execute([
				':char_id' => $char,
				':account' => $user,
				':server' => $server,
				':date' => $date
			]);
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM																																					  
        public function greset_character()
        {
            if(isset($this->char_info['gres_info']['clear_all_resets']) && $this->char_info['gres_info']['clear_all_resets'] == 0){
                $resets = $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . '-' . $this->char_info['gres_info']['reset'];
            } else{
                $resets = 0;
            }
			$location = 'MapNumber = 0, MapPosX = 123, MapPosY = 130,';
			if(in_array($this->char_info['Class'], [32,33,34,35,39])){
				$location = 'MapNumber = 3, MapPosX = 175, MapPosY = 114,';
			}
			if(in_array($this->char_info['Class'], [80,81,82,83,84,87])){
				$location = 'MapNumber = 51, MapPosX = 52, MapPosY = 226,';
			}
            if($this->charge_from_zen_wallet == 0){
                $query = 'UPDATE Character SET ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' = ' . $resets . ', ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'grand_resets', 'column']) . ' = ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'grand_resets', 'column']) . ' + 1, clevel = 1, '.$location.' Experience = 0, Money = Money - :money, last_greset_time = :time WHERE Name = :char AND AccountId = :user';
                $data = [':money' => $this->char_info['res_money'], ':time' => time(), ':char' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])];
            } else{
                $this->website->charge_credits($this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->char_info['res_money'], 3);
                $query = 'UPDATE Character SET ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' = ' . $resets . ', ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'grand_resets', 'column']) . ' = ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'grand_resets', 'column']) . ' + 1, clevel = 1, '.$location.' Experience = 0, last_greset_time = :time WHERE Name = :char AND AccountId = :user';
                $data = [':time' => time(), ':char' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])];
            }
            $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare($query);
            $stmt->execute($data);
            if($this->char_info['gres_info']['clear_magic'] != 0){
                $this->clear_magic_list();
            }
            if($this->char_info['gres_info']['clear_inventory'] != 0){
                $this->clear_inventory();
            }
            if($this->char_info['gres_info']['clear_stats'] != 0){
                $this->clear_greset_stats();
            }
            if($this->char_info['gres_info']['clear_level_up'] != 0){
                $this->clear_greset_levelup();
            }
            $this->add_bonus_greset_points();
            if($this->char_info['gres_info']['bonus_reset_stats'] == 1 && $this->char_info['bonus_reset_stats_points'] > 0){
                $this->add_bonus_stats_for_resets();
            }
            if($this->session->userdata('vip')){
                $this->char_info['gres_info']['bonus_credits'] += $this->session->userdata(['vip' => 'grand_reset_bonus_credits']);
                $this->char_info['gres_info']['bonus_gcredits'] += $this->session->userdata(['vip' => 'grand_reset_bonus_gcredits']);
            }
            if($this->char_info['gres_info']['bonus_credits'] != 0){
                $this->add_account_log('Reward ' . $this->config->config_entry('credits_' . $this->session->userdata(['user' => 'server']) . '|title_1') . ' for grand reset: ' . $this->vars['character'] . '', $this->char_info['gres_info']['bonus_credits'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
                $this->add_bonus_credits(1, $this->char_info['gres_info']['bonus_credits']);
            }
            if($this->char_info['gres_info']['bonus_gcredits'] != 0){
                $this->add_account_log('Reward ' . $this->config->config_entry('credits_' . $this->session->userdata(['user' => 'server']) . '|title_2') . ' for grand reset: ' . $this->vars['character'] . '', $this->char_info['gres_info']['bonus_gcredits'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
                $this->add_bonus_credits(2, $this->char_info['gres_info']['bonus_gcredits']);
            }
			if(isset($this->char_info['gres_info']['bonus_ruud']) && $this->char_info['gres_info']['bonus_ruud'] > 0){
				$this->add_ruud($this->char_info['gres_info']['bonus_ruud'], $this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
			}
			if($this->char_info['gres_info']['bonus_credits'] == 0 && $this->char_info['gres_info']['bonus_gcredits'] == 0){
                $this->add_account_log('Character ' . $this->vars['character'] . ' made grand reset', 0, $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']));
            }

			if(isset($this->char_info['gres_info']['clear_masterlevel']) && $this->char_info['gres_info']['clear_masterlevel'] == 1){
				$skill_tree = $this->reset_skill_tree($this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'skill_tree_type'), $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skilltree_reset_level'), $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skilltree_reset_points'), $this->config->config_entry('character_' . $this->session->userdata(['user' => 'server']) . '|skilltree_points_multiplier'));
			}
			
			$this->vars['current_date'] = date('Y-m-d', time());
			
			if($this->checkGResetLog($this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['current_date']) == false){
				$this->insertGResetLog($this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['current_date']);
			}
			else{
				$this->updateGResetLog($this->char_info['id'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['current_date']);
			}
			
            return true;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	
		public function checkGResetLog($char, $user, $server, $date){
			return $this->website->db('web')->query('SELECT id, resets FROM DmN_Character_GReset_Log WHERE char_id = '.$this->website->db('web')->sanitize_var($char).' AND account = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\' AND date = \''.$this->website->db('web')->sanitize_var($date).'\'')->fetch();
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	
		public function clearGResetLog($char, $user, $server, $date){
			$stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Character_GReset_Log WHERE char_id = :char_id AND account = :account AND server = :server and date >= :date');
			$stmt->execute([
				':char_id' => $char, 
				':account' => $user, 
				':server' => $server,
				':date' => $date
			]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	
		public function insertGResetLog($char, $user, $server, $date){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Character_GReset_Log (char_id, account, server, resets, date) VALUES (:char_id, :account, :server, 1, :date)');
			$stmt->execute([
				':char_id' => $char,
				':account' => $user,
				':server' => $server,
				':date' => $date
			]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	
		public function updateGResetLog($char, $user, $server, $date){
			$stmt = $this->website->db('web')->prepare('UPDATE DmN_Character_GReset_Log SET resets = resets + 1 WHERE  char_id = :char_id AND account = :account AND server = :server AND date = :date');
			$stmt->execute([
				':char_id' => $char,
				':account' => $user,
				':server' => $server,
				':date' => $date
			]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function clear_reset_stats()
        {
            if(!defined('RES_DECREASE_STATS_PERC') || RES_DECREASE_STATS_PERC == false){
                $data = [
					':str' => $this->Mcharacter->char_info['res_info']['new_stat_points'], 
					':agi' => $this->Mcharacter->char_info['res_info']['new_stat_points'], 
					':vit' => $this->Mcharacter->char_info['res_info']['new_stat_points'], 
					':ene' => $this->Mcharacter->char_info['res_info']['new_stat_points']
				];
                if(in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78])){
                    $dl = ', Leadership = :com';
                    $data[':com'] = $this->Mcharacter->char_info['res_info']['new_stat_points'];
                } 
				else{
                    $dl = '';
                }
            } else{
                $data = [
					':str' => $this->show65kStats($this->char_info['Strength']) - round((RES_DECREASE_STATS_PERC / 100) * $this->show65kStats($this->char_info['Strength'])), 
					':agi' => $this->show65kStats($this->char_info['Dexterity']) - round((RES_DECREASE_STATS_PERC / 100) * $this->show65kStats($this->char_info['Dexterity'])), 
					':vit' => $this->show65kStats($this->char_info['Vitality']) - round((RES_DECREASE_STATS_PERC / 100) * $this->show65kStats($this->char_info['Vitality'])), 
					':ene' => $this->show65kStats($this->char_info['Energy']) - round((RES_DECREASE_STATS_PERC / 100) * $this->show65kStats($this->char_info['Energy']))
				];
                if(in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78])){
                    $dl = ', Leadership = :com';
                    $data[':com'] = $this->show65kStats($this->char_info['Leadership'])/* - round((RES_DECREASE_STATS_PERC / 100) * $this->show65kStats($this->char_info['Leadership']))*/;
                } 
				else{
                    $dl = '';
                }
            }
            $data[':char'] = $this->vars['character'];
            $data[':user'] = $this->session->userdata(['user' => 'username']);
            $stmt = $this->game_db->prepare('UPDATE Character SET Strength = :str, Dexterity = :agi, Vitality = :vit, Energy = :ene' . $dl . ' WHERE Name = :char AND AccountId = :user');
            $stmt->execute($data);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function clear_greset_stats()
        {
            $data = [':str' => $this->Mcharacter->char_info['gres_info']['new_stat_points'], ':agi' => $this->Mcharacter->char_info['gres_info']['new_stat_points'], ':vit' => $this->Mcharacter->char_info['gres_info']['new_stat_points'], ':ene' => $this->Mcharacter->char_info['gres_info']['new_stat_points']];
            if(in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78])){
                $dl = ', Leadership = :com';
                $data[':com'] = $this->Mcharacter->char_info['gres_info']['new_stat_points'];
            } else{
                $dl = '';
            }
            $data[':char'] = $this->vars['character'];
            $data[':user'] = $this->session->userdata(['user' => 'username']);
            $stmt = $this->game_db->prepare('UPDATE Character SET Strength = :str, Dexterity = :agi, Vitality = :vit, Energy = :ene' . $dl . ' WHERE Name = :char AND AccountId = :user');
            $stmt->execute($data);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function clear_reset_levelup()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = :freepoints WHERE Name = :character AND AccountId = :user');
            $stmt->execute([':freepoints' => $this->Mcharacter->char_info['res_info']['new_free_points'], ':character' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function clear_greset_levelup()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = :freepoints WHERE Name = :character AND AccountId = :user');
            $stmt->execute([':freepoints' => $this->Mcharacter->char_info['gres_info']['new_free_points'], ':character' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        public function clear_magic_list()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET MagicList = CAST(REPLICATE(char(0xff), 180) as varbinary(180)) WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function clear_inventory($res_config = false)
        {
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
            if($res_config != false){
                if(isset($res_config['clear_inventory']) && $res_config['clear_inventory'] == 1){
                    for($a = 12; $a < 76; $a++){
                        $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                    }
                }
                if(isset($res_config['clear_equipment']) && $res_config['clear_equipment'] == 1){
                    for($a = 0; $a < 12; $a++){
                        $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                    }
                    if(isset($items_array[236])){
                        $items_array[236] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                    }
                }
                if($this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'inv_multiplier') == 236){
                    if(isset($res_config['clear_store']) && $res_config['clear_store'] == 1){
                        for($a = 204; $a < 236; $a++){
                            $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                        }
                    }
                    if(isset($res_config['clear_exp_inventory']) && $res_config['clear_exp_inventory'] == 1){
                        for($a = 76; $a < 140; $a++){
                            $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                        }
                    }
                } else{
                    if(isset($res_config['clear_store']) && $res_config['clear_store'] == 1){
                        for($a = 76; $a < 108; $a++){
                            $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                        }
                    }
                }
            } else{
                for($a = 0; $a <= $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'inv_multiplier'); $a++){
                    $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                }
            }
            $stmt = $this->game_db->prepare('UPDATE Character SET Inventory = 0x' . implode('', $items_array) . ' WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function add_bonus_reset_points()
        {
            $bonus = ($this->char_info['resets'] + 1) * $this->bonus_points_by_class($this->char_info['Class']);
			if($this->session->userdata('vip')){
                $bonus += $this->session->userdata(['vip' => 'reset_bonus_points']);
            }
			if(defined('CRYSTALMU') && CRYSTALMU == 1){
				$bonusFromQuest = $this->game_db->query('SELECT QuestStat FROM Character WHERE Name = \''.$this->website->c($this->vars['character']).'\'')->fetch();
				if($bonusFromQuest != false){
					$bonus += $bonusFromQuest['QuestStat'];
				}
			}
			
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = LevelUpPoint + :lvlup WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':lvlup' => $bonus, ':char' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        private function add_bonus_greset_points()
        {
            if($this->char_info['gres_info']['bonus_points_save'] == 1){
                $bonus = ($this->char_info['grand_resets'] + 1) * $this->bonus_points_by_class($this->char_info['Class'], 'gres_info');
            } else{
                $bonus = $this->bonus_points_by_class($this->char_info['Class'], 'gres_info');
            }
			if(defined('CRYSTALMU') && CRYSTALMU == 1){
				$bonusFromQuest = $this->game_db->query('SELECT QuestStat FROM Character WHERE Name = \''.$this->website->c($this->vars['character']).'\'')->fetch();
				if($bonusFromQuest != false){
					$bonus += $bonusFromQuest['QuestStat'];
				}
			}
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = LevelUpPoint + :lvlup WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':lvlup' => $bonus, ':char' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        private function add_bonus_stats_for_resets()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = LevelUpPoint + :lvlup WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':lvlup' => $this->char_info['bonus_reset_stats_points'], ':char' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function add_bonus_stats_for_gresets()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = LevelUpPoint + :lvlup WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':lvlup' => $this->char_info['bonus_greset_stats_points'], ':char' => $this->vars['character'], ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        public function bonus_points_by_class($class, $type = 'res_info', $data = false)
        {
            $char_info = ($data != false) ? $data : $this->Mcharacter->char_info;
			if(isset($char_info[$type]['bonus_points'][$class])){
				return $char_info[$type]['bonus_points'][$class];
			}
			return 0;
        }
		
        private function backup_master_level()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Master = mLevel WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        private function change_reset_class()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Class = CASE WHEN Class IN(50, 66, 98) THEN Class - 2 ELSE Class - 1 END WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        public function restore_master_level()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET mLevel = Master, Class = CASE WHEN Class IN(48, 64, 96) THEN Class + 2 ELSE Class + 1 END, mlPoint = Master + (' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . '*150) WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function questToReadable($quest)
        {
            $quest = substr($quest, 0, 100);
            if($quest == 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF0000FF00FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'){
                return 0; //No quest
            } else if($quest == 'FAFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF000000FF00FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'){
                return 1; //2ndQuestFinished
            } else if($quest == 'EAFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF000000FF00FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'){
                return 2; //MarlonQuestFinished
            } else if($quest == 'AAFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF000000FF00FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'){
                return 3; //DarkStoneFinished
            } else if($quest == 'AAEAFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF000000000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'){
                return 4; //3rdQuestFinishedA
            } else if($quest == 'FFEAFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF000000000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'){
                return 5; //3rdQuestFinishedB
            } else{
                return -1;
            }
        }
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function getBaseStats($class, $server)
        {
			$leadership = (MU_VERSION >= 1) ? ', Leadership' : '';
            switch($class){
                case 0:
                case 1:
                case 2:
                case 3:
                case 7:
				case 15:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy '.$leadership.' FROM DefaultClassType WHERE Class = 0')->fetch();
                    break;
                case 16:
                case 17:
                case 18:
                case 19:
                case 23:
				case 31:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy '.$leadership.' FROM DefaultClassType WHERE Class = 16')->fetch();
                    break;
                case 32:
                case 33:
                case 34:
                case 35:
                case 39:
				case 47:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy '.$leadership.' FROM DefaultClassType WHERE Class = 32')->fetch();
                    break;
                case 48:
                case 49:
                case 50:
				case 51:
                case 54:
				case 62:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy '.$leadership.' FROM DefaultClassType WHERE Class = 48')->fetch();
                    break;
                case 64:
                case 65:
                case 66:
				case 67:
                case 70:
				case 78:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 64')->fetch();
                    break;
                case 80:
                case 81:
                case 82:
                case 83:
				case 84:
                case 87:
				case 95:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 80')->fetch();
                    break;
                case 96:
                case 97:
                case 98:
				case 99:
                case 102:
				case 110:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 96')->fetch();
                    break;
                case 112:
                case 114:
				case 115:
                case 118:
				case 126:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 112')->fetch();
                    break;
                case 128:
                case 129:
				case 130:
                case 131:
                case 135:
				case 143:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 128')->fetch();
                    break;
				case 144:	
                case 145:
                case 147:
				case 151:
				case 159:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 144')->fetch();
                    break;
				case 160:	
                case 161:
                case 163:
				case 167:
				case 175:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 160')->fetch();
                    break;	
				case 176:	
                case 177:
                case 178:
				case 179:
				case 183:
				case 191:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 176')->fetch();
                    break;	
				case 192:	
                case 193:
                case 194:
				case 195:
				case 199:
				case 207:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 192')->fetch();
                    break;	
				case 208:	
                case 209:
				case 210:
                case 211:
				case 215:
				case 223:
                    return $this->website->db('game', $server)->query('SELECT TOP 1 Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = 208')->fetch();
                    break;		
            }
        }

        public function calculateNewStats()
        {
            $new_stats = 0;
            $this->defaultStats = $this->getBaseStats($this->char_info['Class'], $this->session->userdata(['user' => 'server']));
            $quest = $this->questToReadable($this->char_info['Quest']);
            if(defined('CUSTOM_RESET_STATS') && CUSTOM_RESET_STATS == true){
                if(in_array($this->char_info['Class'], [0, 1, 2, 3, 7, 16, 17, 18, 19, 23, 32, 33, 34, 35, 39, 80, 81, 82, 83, 87])){
                    if($this->char_info['resets'] < 1){
                        $new_stats = 5 * ($this->char_info['cLevel'] - 1);
                        if($quest == 1)
                            $new_stats += 20;
                        if($quest == 2 || $quest == 3)
                            $new_stats = 5 * 219 + 6 * ($this->char_info['cLevel'] - 220) + 20;
                        if(in_array($this->char_info['Class'], [2, 3, 7, 18, 19, 23, 34, 35, 39, 82, 83, 87]))
                            $new_stats = 5 * 219 + 6 * ($this->char_info['cLevel'] - 220) + 90;
                    } else if($this->char_info['resets'] >= 1){
                        $new_stats = $this->char_info['resets'] * 1995;
                        if($quest == 1)
                            $new_stats += 20;
                        if($quest == 2 || $quest == 3)
                            $new_stats = 2175 + ($this->char_info['resets'] - 1) * 2394 + 20 + $this->char_info['cLevel'] * 6;
                        if(in_array($this->char_info['Class'], [2, 3, 8, 18, 19, 23, 34, 35, 39, 82, 83, 87]))
                            $new_stats = 2175 + ($this->char_info['resets'] - 1) * 2394 + 90 + $this->char_info['cLevel'] * 6;
                    } else{
                        $new_stats = 404;
                    }
                } else{
                    if($this->char_info['resets'] < 1){
                        $new_stats = 7 * ($this->char_info['cLevel'] - 1);
                        if(in_array($this->char_info['Class'], [49, 50, 54, 65, 66, 70, 97, 98, 102, 114, 118]))
                            $new_stats = 7 * ($this->char_info['cLevel'] - 1) + 70;
                    } else if($this->char_info['resets'] >= 1){
                        $new_stats = $this->char_info['resets'] * 2793;
                        if(in_array($this->char_info['Class'], [49, 50, 54, 65, 66, 70, 97, 98, 102, 114, 118]))
                            $new_stats = $this->char_info['resets'] * 2793 + 70 + $this->char_info['cLevel'] * 7;
                    } else{
                        $new_stats = 404;
                    }
                }
            } else{
                if($this->char_info['Strength'] > $this->defaultStats['Strength']){
                    $new_stats += $this->char_info['Strength'] - $this->defaultStats['Strength'];
                }
                if($this->char_info['Dexterity'] > $this->defaultStats['Dexterity']){
                    $new_stats += $this->char_info['Dexterity'] - $this->defaultStats['Dexterity'];
                }
                if($this->char_info['Energy'] > $this->defaultStats['Energy']){
                    $new_stats += $this->char_info['Energy'] - $this->defaultStats['Energy'];
                }
                if($this->char_info['Vitality'] > $this->defaultStats['Vitality']){
                    $new_stats += $this->char_info['Vitality'] - $this->defaultStats['Vitality'];
                }
                if(in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78]) && $this->char_info['Leadership'] > $this->defaultStats['Leadership']){
                    $new_stats += $this->char_info['Leadership'] - $this->defaultStats['Leadership'];
                }
            }
            return $new_stats;
        }

        public function reset_stats($character = '', $freePoints = false, $baseStats = false)
        {
			if($character != ''){
				$this->vars['character'] = $character;
			}
			if($freePoints !== false){
				$this->defaultStats = $baseStats;
				$stats = $freePoints;
			}
			else{
				$stats = $this->calculateNewStats();
			}
            if(defined('CUSTOM_RESET_STATS') && CUSTOM_RESET_STATS == true){
                $lvl_up = ':lvlUp';
            } else{
                $lvl_up = 'LevelUpPoint + :lvlUp';
            }
            if(in_array($this->char_info['Class'], [64, 65, 66, 67, 70, 78])){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE Character SET LevelUpPoint = ' . $lvl_up . ', Strength = ' . $this->defaultStats['Strength'] . ', Dexterity = ' . $this->defaultStats['Dexterity'] . ', Vitality = ' . $this->defaultStats['Vitality'] . ', Energy = ' . $this->defaultStats['Energy'] . ', Leadership = ' . $this->defaultStats['Leadership'] . ' WHERE Name = :char AND AccountId = :user');
            } else{
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE Character SET LevelUpPoint = ' . $lvl_up . ', Strength = ' . $this->defaultStats['Strength'] . ', Dexterity = ' . $this->defaultStats['Dexterity'] . ', Vitality = ' . $this->defaultStats['Vitality'] . ', Energy = ' . $this->defaultStats['Energy'] . ' WHERE Name = :char AND AccountId = :user');
            }
            $stmt->execute([':lvlUp' => $stats, ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		public function setNewResGR($gr, $res, $level, $name){
			 $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE Character SET ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'grand_resets', 'column']) . ' = :newgr, ' . $this->config->values('table_config', [$this->session->userdata(['user' => 'server']), 'resets', 'column']) . ' = :newres, cLevel = :newlvl WHERE Name = :char AND AccountId = :user');
			 $stmt->execute([':newgr' => $gr, ':newres' => $res, ':newlvl' => $level, ':char' => $this->website->c($name), ':user' => $this->session->userdata(['user' => 'username'])]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function reset_skill_tree($type, $reset_level = 1, $reset_points = 1, $points_multiplier = 1)
        {
            switch($type){
                default:
                    return false;
                    break;
                case 'scf':
                    return $this->reset_skill_tree_scf($reset_level, $reset_points, $points_multiplier);
                    break;
                case 'igcn':
                    return $this->reset_skill_tree_igcn($reset_level, $reset_points, $points_multiplier);
                    break;
                case 'muengine':
                    return $this->reset_skill_tree_muengine($reset_level, $reset_points, $points_multiplier);
                    break;
                case 'xteam':
                    return $this->reset_skill_tree_xteam($reset_level, $reset_points, $points_multiplier);
                    break;
                case 'zteam':
                    return $this->reset_skill_tree_zteam($reset_level, $reset_points, $points_multiplier);
                    break;
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function reset_skill_tree_scf($reset_level = 1, $reset_points = 1, $points_multiplier = 1)
        {
            if($reset_level == 0){
                $this->ml_level = $this->get_master_level_scf();
            }
            if($reset_points == 0){
                $this->ml_points = ($this->get_master_level_scf() * $points_multiplier);
            }
            $stmt = $this->game_db->prepare('UPDATE Character SET SCFMasterPoints = ' . $this->ml_points . ', SCFMasterLevel = ' . $this->ml_level . ', SCFMasterSkill = NULL WHERE Name = :char AND AccountId = :user');
            return $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function reset_skill_tree_igcn($reset_level = 1, $reset_points = 1, $points_multiplier = 1)
        {
            if($reset_points == 0){
                $this->ml_points = $this->get_master_level_igcn();
            }
            if($reset_level == 0){
                $this->ml_level = $this->get_master_level_igcn();
                $ml_exp = '';
            } 
			else{
                $ml_exp = ',  mlExperience = 0, mlNextExp = 35507050';
            }
			
			$query_enchancement = '';
			
			$skill_size = 6;
			$empty = 'ff0000';
			if(MU_VERSION >= 9){
				$enchancement_points = 0;
				if($this->ml_points > 400){
					$enchancement_points = $this->ml_points - 400;
					$enchancement_points = $enchancement_points * $points_multiplier;
					$this->ml_points = 400;
				}
				$query_enchancement = ', i4thSkillPoint = '.$enchancement_points.', AddStrength = 0, AddDexterity = 0, AddVitality = 0, AddEnergy = 0';
				$skill_size = 10;
				$empty = 'ff00000000';
			}
			
			if($reset_points == 0){
				$this->ml_points = $this->ml_points * $points_multiplier;
			}
			
            $this->get_skill_list();
            $skills_array = str_split($this->char_info['MagicList'], $skill_size);
            foreach($skills_array AS $key => $skill){
                $index = $this->skill_index($skill);
                if($this->is_master_skill($index)){
                    $skills_array[$key] = $empty;
                }
            }
			
            $stmt = $this->game_db->prepare('UPDATE Character SET mLevel = ' . $this->ml_level . $ml_exp . ', mlPoint = ' . $this->ml_points . ', MagicList = 0x' . implode('', $skills_array) . ' '.$query_enchancement.' WHERE Name = :char AND AccountId = :user');
            return $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function reset_skill_tree_zteam($reset_level = 1, $reset_points = 1, $points_multiplier = 1)
        {
            $this->set_zteam_skilltree_table();
            $this->get_skill_list();
            $skills_array = str_split($this->char_info['MagicList'], 6);
            foreach($skills_array AS $key => $skill){
                $index = $this->skill_index($skill);
                if($this->is_master_skill($index)){
                    $skills_array[$key] = 'ff0000';
                }
            }
            $stmt = $this->game_db->prepare('UPDATE Character SET MagicList = 0x' . implode('', $skills_array) . ' WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
            if($reset_level == 0){
                $this->ml_level = $this->get_master_level_zteam();
                $ml_exp = '';
            } else{
                $ml_exp = ', ML_EXP = 0,  ML_NEXTEXP = 35507050';
            }
            if($reset_points == 0){
                $this->ml_points = ($this->get_master_level_zteam() * $points_multiplier);
            }
            $stmt = $this->game_db->prepare('UPDATE ' . $this->skillTreeTable . ' SET MASTER_LEVEL = ' . $this->ml_level . $ml_exp . ', ML_POINT = ' . $this->ml_points . ' WHERE CHAR_NAME = :char');
            return $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function reset_skill_tree_muengine($reset_level = 1, $reset_points = 1, $points_multiplier = 1)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET MagicList = CAST(REPLICATE(char(0xff), 180) AS varbinary(180)) WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
            if($reset_points == 0){
                $this->ml_points = ($this->get_master_level_muengine() * $points_multiplier);
            }
            if($reset_level == 0){
                $this->ml_level = $this->get_master_level_muengine();
                $ml_exp = '';
            } else{
                $ml_exp = ', ML_EXP = 0,  ML_NEXTEXP = 35507050';
            }
            $stmt = $this->game_db->prepare('UPDATE T_MasterLevelSystem SET MASTER_LEVEL = ' . $this->ml_level . $ml_exp . ', ML_POINT = ' . $this->ml_points . ' WHERE CHAR_NAME = :char');
            return $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function reset_skill_tree_xteam($reset_level = 1, $reset_points = 1, $points_multiplier = 1)
        {
            if($reset_points == 0){
                $this->ml_points = $this->get_master_level_xteam();
            }
   
            if($reset_level == 0){
                $this->ml_level = $this->get_master_level_xteam();
                $ml_exp = '';
            } else{
                $ml_exp = ', MasterExperience = 0';
            }
			
			if(MU_VERSION >= 9){
				$enchancement_points = 0;
				if($this->ml_points > 400){
					$enchancement_points = $this->ml_points - 400;
					$enchancement_points = $enchancement_points * $points_multiplier;
					$this->ml_points = 400;
				}
				
				$stmt1 = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE EnhanceSkillTree SET EnhancePoint = ' . $enchancement_points . ', EnhanceSkill = NULL, EnhanceSkillPassive = NULL WHERE Name = :char');
				$stmt1->execute([':char' => $this->website->c($this->vars['character'])]);
			}
			
			 if($reset_points == 0){
                $this->ml_points = $this->ml_points * $points_multiplier;
            }
			
			$skill_size = 6;
			$empty = 'ff0000';
			$this->get_skill_list();
			$skills_array = str_split($this->char_info['MagicList'], $skill_size);
            foreach($skills_array AS $key => $skill){
                $index = $this->skill_index($skill);
                if($this->is_master_skill($index)){
                    $skills_array[$key] = $empty;
                }
            }
			
			$rSkills = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE Character SET MagicList = 0x' . implode('', $skills_array) . ' WHERE Name = :char AND AccountId = :user');
            $rSkills->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);

			$stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE MasterSkillTree SET MasterLevel = ' . $this->ml_level . $ml_exp . ', MasterPoint = ' . $this->ml_points . ', MasterSkill = NULL WHERE Name = :char');
            return $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
        }

        private function get_master_level_scf()
        {
            $stmt = $this->game_db->prepare('SELECT SCFMasterLevel FROM Character WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
            $points = $stmt->fetch();
            return $points['SCFMasterLevel'];
        }

        private function set_zteam_skilltree_table()
        {
            $check = $this->game_db->snumrows('SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_name = \'T_MasterLevelSystem\'');
            $this->skillTreeTable = ($check > 0) ? 'T_MasterLevelSystem' : 'T_SkillTree_Info';
        }

        private function get_master_level_zteam()
        {
            $stmt = $this->game_db->prepare('SELECT MASTER_LEVEL FROM ' . $this->skillTreeTable . ' WHERE CHAR_NAME = :char');
            $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
            $points = $stmt->fetch();
            return $points['MASTER_LEVEL'];
        }

        private function get_master_level_muengine()
        {
            $stmt = $this->game_db->prepare('SELECT MASTER_LEVEL FROM T_MasterLevelSystem WHERE CHAR_NAME = :char');
            $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
            $points = $stmt->fetch();
            return $points['MASTER_LEVEL'];
        }

        private function get_master_level_igcn()
        {
            $stmt = $this->game_db->prepare('SELECT mLevel FROM Character WHERE AccountId = :user AND Name = :char');
            $stmt->execute([':user' => $this->session->userdata(['user' => 'username']), ':char' => $this->website->c($this->vars['character'])]);
            $points = $stmt->fetch();
            if($points != false){
                return $points['mLevel'];
            }
            return 0;
        }

        private function get_master_level_xteam()
        {
            $this->skillTreeTable = 'MasterSkillTree';
            $stmt = $this->game_db->prepare('SELECT MasterLevel FROM ' . $this->skillTreeTable . ' WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
            $points = $stmt->fetch();
            return $points['MasterLevel'];
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function is_master_skill($skill_id)
        {
            static $SkillList = null;
            $is_master_skill = false;
            libxml_use_internal_errors(true);
            if($SkillList == null)
                $SkillList = simplexml_load_file(APP_PATH . DS . 'data' . DS . 'ServerData' . DS . 'SkillList.xml');
            if($SkillList === false){
                $err = 'Failed loading XML<br>';
                foreach(libxml_get_errors() as $error){
                    $err .= $error->message . '<br>';
                }
                writelog('[Server File Parser] Unable to parse xml file: ' . $err, 'system_error');
            }
            $skill_data = $SkillList->xpath("//SkillList/Skill[@Index='" . $skill_id . "']");
            if(!empty($skill_data)){
                if(in_array((string)$skill_data[0]->attributes()->UseType, [3,4,7,8,9,10,11])){
                    $is_master_skill = true;
                }
            }
            return $is_master_skill;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function skill_index($hex)
        {
            $id = hexdec(substr($hex, 0, 2));
            $id2 = hexdec(substr($hex, 2, 2));
            $id3 = hexdec(substr($hex, 4, 2));
            if(($id2 & 7) > 0){
                $id = $id * ($id2 & 7) + $id3;
            }
            return $id;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function get_skill_list()
        {
            $sql = (DRIVER == 'pdo_odbc') ? 'MagicList' : 'CONVERT(IMAGE, MagicList) AS MagicList';
            $stmt = $this->game_db->prepare('SELECT ' . $sql . ' FROM Character WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
            if(DRIVER == 'pdo_dblib'){
                $skills = unpack('H*', implode('', $stmt->fetch()));
                $this->char_info['MagicList'] = $this->clean_hex($skills[1]);
            } else{
                if($skills = $stmt->fetch()){
                    $this->char_info['MagicList'] = $this->clean_hex($skills['MagicList']);
                }
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function get_skill_list_xteam()
        {
            $sql = (DRIVER == 'pdo_odbc') ? 'MasterSkill' : 'CONVERT(IMAGE, MasterSkill) AS MasterSkill';
            $stmt = $this->game_db->prepare('SELECT ' . $sql . ' FROM MasterSkillTree WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($this->vars['character'])]);
            if(DRIVER == 'pdo_dblib'){
                $skills = unpack('H*', implode('', $stmt->fetch()));
                $this->char_info['MasterSkill'] = $this->clean_hex($skills[1]);
            } else{
                if($skills = $stmt->fetch()){
                    $this->char_info['MasterSkill'] = $this->clean_hex($skills['MasterSkill']);
                }
            }
        }

        public function check_pk()
        {
            if($this->char_info['PkLevel'] <= 3){
                return false;
            }
            return true;
        }

        public function clear_pk($money = 0)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET PkLevel = 3, PkCount = 0, Money = Money - :money WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':money' => $money, ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        public function teleport_char($cords, $money)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET MapNumber = :world, MapPosX = :x, MapPosY = :y WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':world' => $this->vars['world'], ':x' => $cords[0], ':y' => $cords[1], ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
			if($money > 0){
				$this->decrease_zen($this->session->userdata(['user' => 'username']), $money, $this->vars['character']);
			}
        }

        public function update_level()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET cLevel = :new_level WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':new_level' => $this->char_info['cLevel'] + (int)$this->vars['level'], ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
            return true;
        }

        public function update_points()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET LevelUpPoint = LevelUpPoint + :new_point WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':new_point' => (int)$this->vars['points'], ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
            return true;
        }

        public function update_gm()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET CtlCode = :ctlcode WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':ctlcode' => $this->config->config_entry('buygm|gm_ctlcode'), ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
            return true;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function add_wcoins($amount = 0, $config = [])
        {
            $acc = (in_array($config['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->session->userdata(['user' => 'id']) : $this->session->userdata(['user' => 'username']);
            $stmt = $this->website->db($config['db'], $this->session->userdata(['user' => 'server']))->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' + :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $acc]);
            if($stmt->rows_affected() == 0){
                $stmt = $this->website->db($config['db'], $this->session->userdata(['user' => 'server']))->prepare('INSERT INTO ' . $config['table'] . ' (' . $config['identifier_column'] . ', ' . $config['column'] . ') values (:user, :wcoins)');
                $stmt->execute([':user' => $acc, ':wcoins' => $amount]);
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function remove_wcoins($config = [], $amount = false)
        {
			if($amount != false){
				$this->vars['credits'] = $amount;
			}
            $acc = (in_array($config['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->session->userdata(['user' => 'id']) : $this->session->userdata(['user' => 'username']);
            $stmt = $this->website->db($config['db'], $this->session->userdata(['user' => 'server']))->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' - :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $this->vars['credits'], ':account' => $acc]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function get_wcoins($config = [], $server)
        {
            $acc = (in_array($config['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $this->session->userdata(['user' => 'id']) : $this->session->userdata(['user' => 'username']);
            $stmt = $this->website->db($config['db'], $server)->prepare('SELECT ' . $config['column'] . ' FROM ' . $config['table'] . ' WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':account' => $acc]);
            if($wcoins = $stmt->fetch()){
                return $wcoins[$config['column']];
            }
            return false;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function remove_ruud($money, $char, $user, $server)
        {
			if($this->website->db('game', $server)->check_if_column_exists('RuudMoney', 'Character') != false){
				$ruud = 'RuudMoney';
			}
			else{
				$ruud = 'Ruud';
			}
            $stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET '.$ruud.' = '.$ruud.' - :money WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :char');
			$stmt->execute([':money' => (int)$money, ':account' => $user, ':char' => $char]);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function add_ruud($money, $char, $user, $server)
        {
			if($this->website->db('game', $server)->check_if_column_exists('RuudMoney', 'Character') != false){
				$ruud = 'RuudMoney';
			}
			else{
				$ruud = 'Ruud';
			}
            $stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET '.$ruud.' = '.$ruud.' + :money WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :char');
			$stmt->execute([':money' => (int)$money, ':account' => $user, ':char' => $char]);
        }
		
        public function check_reward()
        {
            $stmt = $this->website->db('web')->prepare('SELECT account FROM DmN_Rewards WHERE account = :account AND server = :server');
            $stmt->execute([':account' => $this->session->userdata(['user' => 'username']), ':server' => $this->session->userdata(['user' => 'server'])]);
            if($stmt->fetch()){
                return true;
            }
            return false;
        }

        public function log_reward()
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Rewards (account, server) VALUES (:account, :server)');
            $stmt->execute([':account' => $this->session->userdata(['user' => 'username']), ':server' => $this->session->userdata(['user' => 'server'])]);
        }

        public function add_reward()
        {
            $db = ($this->config->config_entry('account|wcoin_db') == 'account') ? $this->account_db : $this->game_db;
            $acc = (in_array($this->config->config_entry('account|wcoin_account_column'), ['MemberGuid', 'memb_guid'])) ? $this->session->userdata(['user' => 'id']) : $this->session->userdata(['user' => 'username']);
            $stmt = $db->prepare('UPDATE ' . $this->config->config_entry('account|wcoin_table') . ' SET ' . $this->config->config_entry('account|wcoin_coin_column') . ' = ' . $this->config->config_entry('account|wcoin_coin_column') . ' + :wcoins WHERE ' . $this->config->config_entry('account|wcoin_account_column') . ' = :account');
            $stmt->execute([':wcoins' => 450, ':account' => $acc]);
        }

        private function add_bonus_credits($type, $amount)
        {
            $this->website->add_credits($this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $amount, $type);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function show65kStats($stat_value)
        {
            return ($stat_value < 0) ? $stat_value += 65536 : $stat_value;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
        public function load_character_info($char, $server = '', $by_id = false)
        {
			$ruud = ', 0 AS Ruud';
			if(MU_VERSION >= 5){
				$ruud = ', Ruud';
			}
			if(MU_VERSION >= 12){
				if($this->game_db->check_if_column_exists('RuudMoney', 'Character') != false){
					$ruud = ', RuudMoney AS Ruud';
				}
				else{
					$ruud = ', Ruud';
				}
			}
			$zs = '';
			if($this->game_db->check_if_column_exists('zs_count', 'Character') != false){
				$zs = ', zs_count, zs';
			}
			$leadership = (MU_VERSION >= 1) ? 'Leadership,' : '0 AS Leadership,';
			$QuestIndex = (defined('CRYSTALMU')) ? ', QuestIndex' : '';
			
            $where = ($by_id == true) ? $this->website->get_char_id_col($server) .'  = :char' : 'Name = :char';
            $stmt = $this->game_db->prepare('SELECT TOP 1 AccountId, Name, Money, Class, cLevel, ' . $this->reset_column($server) . $this->greset_column($server) . ' LevelUpPoint, Strength, Dexterity, Vitality, Energy, '.$leadership.' MapNumber, MapPosX, MapPosY, PkLevel, PkCount, CtlCode '.$ruud.$zs.$QuestIndex.' FROM Character WHERE ' . $where . '');
            $stmt->execute([':char' => $this->website->c($char)]);
            if($this->char_info = $stmt->fetch()){
                $this->char_info['mlevel'] = $this->load_master_level($this->char_info['Name'], $server);
                $this->get_inventory_content($char, $server);
                return true;
            }
            return false;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function get_inventory_content($char, $server = '')
        {
            $server = ($server == '') ? $this->session->userdata(['user' => 'server']) : $server;
			$sql = (DRIVER == 'pdo_odbc') ? 'Inventory' : 'CONVERT(IMAGE, Inventory) AS Inventory';
			$stmt = $this->website->db('game', $server)->prepare('SELECT ' . $sql . ' FROM Character WHERE Name = :char');
			$stmt->execute([':char' => $this->website->c($char)]);
			if($inv = $stmt->fetch()){
				if(in_array(DRIVER, ['sqlsrv', 'pdo_sqlsrv', 'pdo_dblib'])){
					$unpack = unpack('H*', $inv['Inventory']);
					$this->char_info['Inventory'] = $this->clean_hex($unpack[1]);
				}
				else{
					$this->char_info['Inventory'] = $this->clean_hex($inv['Inventory']);
				}
			} 
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function load_equipment($server = '')
        {
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($server, 'item_size'));
            $eq = array_chunk($items_array, 12);
            $equipment = [];
            foreach($eq[0] as $key => $item){
                if($item != str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F")){
                    $this->iteminfo->itemData($item, true, $server);
                    $equipment[$key]['item_id'] = $this->iteminfo->id;
                    $equipment[$key]['item_cat'] = $this->iteminfo->type;
                    $equipment[$key]['name'] = $this->iteminfo->realName();
                    $equipment[$key]['level'] = (int)substr($this->iteminfo->getLevel(), 1);
                    $equipment[$key]['hex'] = $item;
					$equipment[$key]['item_info'] = $this->iteminfo->allInfo();
                } else{
                    $equipment[$key] = 0;
                }
            }
			if(isset($items_array[236]) && $items_array[236] != str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F")){
                $this->iteminfo->itemData($items_array[236], true, $server);
                $equipment[12]['item_id'] = $this->iteminfo->id;
                $equipment[12]['item_cat'] = $this->iteminfo->type;
                $equipment[12]['name'] = $this->iteminfo->realName();
                $equipment[12]['level'] = (int)substr($this->iteminfo->getLevel(), 1);
                $equipment[12]['hex'] = $items_array[236];
				$equipment[12]['item_info'] = $this->iteminfo->allInfo();
            }
            if(isset($items_array[237]) && $items_array[237] != str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F")){
                $this->iteminfo->itemData($items_array[237], true, $server);
                $equipment[13]['item_id'] = $this->iteminfo->id;
                $equipment[13]['item_cat'] = $this->iteminfo->type;
                $equipment[13]['name'] = $this->iteminfo->realName();
                $equipment[13]['level'] = (int)substr($this->iteminfo->getLevel(), 1);
                $equipment[13]['hex'] = $items_array[237];
				$equipment[13]['item_info'] = $this->iteminfo->allInfo();
            }
            if(isset($items_array[238]) && $items_array[238] != str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F")){
                $this->iteminfo->itemData($items_array[238], true, $server);
                $equipment[14]['item_id'] = $this->iteminfo->id;
                $equipment[14]['item_cat'] = $this->iteminfo->type;
                $equipment[14]['name'] = $this->iteminfo->realName();
                $equipment[14]['level'] = (int)substr($this->iteminfo->getLevel(), 1);
                $equipment[14]['hex'] = $items_array[238];
				$equipment[14]['item_info'] = $this->iteminfo->allInfo();
            }													   
            return $equipment;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function load_inventory($inv = 1, $server = '')
        {
            $server = ($server == '') ? $this->session->userdata(['user' => 'server']) : $server;
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($server, 'item_size'));
            $inventory = [];
            $items = [];
            $loop = [12, 76]; //default inv
            if($inv == 2)
                $loop = [76, 108]; //store
            if($inv == 3)
                $loop = [108, 140]; //exp inv 1
            if($inv == 4)
                $loop = [204, 236]; //exp inv 2
            for($a = $loop[0]; $a < $loop[1]; $a++){
                $inventory[$a] = !empty($items_array[$a]) ? $items_array[$a] : str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F");
            }
            $i = 0;
            $x = 0;
            $y = 0;
            foreach($inventory as $item){
                $i++;
                if($item != str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F")){
                    $this->iteminfo->itemData($item, true, $server);
                    $items[$i]['item_id'] = $this->iteminfo->id;
                    $items[$i]['item_cat'] = $this->iteminfo->type;
                    $items[$i]['name'] = $this->iteminfo->realName();
                    $items[$i]['level'] = (int)substr($this->iteminfo->getLevel(), 1);
                    $items[$i]['x'] = $this->iteminfo->getX();
                    $items[$i]['y'] = $this->iteminfo->getY();
                    $items[$i]['xx'] = $x;
                    $items[$i]['yy'] = $y;
                    $items[$i]['hex'] = $item;
					$items[$i]['item_info'] = $this->iteminfo->allInfo();
                } else{
                    $items[$i]['xx'] = $x;
                    $items[$i]['yy'] = $y;
                }
                $x++;
                if($x >= 8){
                    $x = 0;
                    $y++;
                }
            }
            return $items;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function clear_inv()
        {
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
            if(isset($this->vars['inventory'])){
                for($a = 12; $a < 76; $a++){
                    $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                }
            }
            if(isset($this->vars['equipment'])){
                for($a = 0; $a < 12; $a++){
                    $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                }
            }
            if(isset($this->vars['store'])){
                for($a = 204; $a < 236; $a++){
                    $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                }
            }
            if(isset($this->vars['exp_inv_1'])){
                for($a = 76; $a < 108; $a++){
                    $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                }
            }
            if(isset($this->vars['exp_inv_2'])){
                for($a = 108; $a < 140; $a++){
                    $items_array[$a] = str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
                }
            }
            $stmt = $this->game_db->prepare('UPDATE Character SET Inventory = 0x' . implode('', $items_array) . ' WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }

        public function check_equipment()
        {
            return (strtoupper(substr($this->char_info['Inventory'], 0, $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 12)) === str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 12));
        }
		
		public function check_store()
        {
            return (strtoupper(substr($this->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 204, $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 32)) === str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 32));
        }
		
		public function check_inventory()
        {
            return (strtoupper(substr($this->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 12, $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 64)) === str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 64));
        }
		
		public function check_exp_inv1()
        {
            return (strtoupper(substr($this->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 76, $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 32)) === str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 32));
        }
		
		public function check_exp_inv2()
        {
            return (strtoupper(substr($this->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 108, $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 32)) === str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 32));
        }

        public function gen_class_select_field($config = false)
        {
            if($config != false){
				if(isset($config[$this->char_info['Class']])){
					$select = '<option disabled="disabled" selected="selected" value="">- SELECT -</option>';
                    foreach($config[$this->char_info['Class']] AS $class){
                        $select .= '<option value="' . $class . '">' . $this->website->get_char_class($class) . '</option>';
                    }
					return $select;
				}
            }
            return false;
        }

        public function update_char_class()
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Class = :class WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':class' => $this->website->c($this->vars['class_select']), ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
            return true;
        }
		
		public function clear_quests_list($class)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Quest = (SELECT TOP 1 Quest FROM Character WHERE Class = :class) WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':class' => $this->website->c($class), ':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }
		
		public function count_change_class_times($server, $char_id){
			$stmt = $this->website->db('web')->prepare('SELECT COUNT(id) AS count FROM DmN_Change_Class_Log WHERE char_id = :char AND server = :server');
			$stmt->execute([':char' => $char_id, ':server' => $server]);
			return $stmt->fetch();
		}
		
		public function add_change_class_log($server, $char_id, $old, $new){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Change_Class_Log (char_id, server, old, new) VALUES (:char, :server, :old, :new)');
			$stmt->execute([':char' => $char_id, ':server' => $server, ':old' => $old, ':new' => $new]);
		}

        public function get_status($name, $server)
        {
            $accountDb = ($this->website->is_multiple_accounts() == true) ? $this->website->get_db_from_server($server, true) : $this->website->get_default_account_database();
            $stmt = $this->website->db('game', $server)->prepare('SELECT TOP 1 a.Id, a.GameIDC, m.ConnectStat, m.ConnectTM, m.DisConnectTM, m.IP, m.ServerName FROM AccountCharacter AS a RIGHT JOIN [' . $accountDb . '].dbo.MEMB_STAT AS m ON (a.Id Collate Database_Default = m.memb___id) WHERE m.memb___id = :user ' . $this->website->server_code($this->website->get_servercode($server)) . '');
            $stmt->execute([':user' => $this->website->c($name)]);
            return $stmt->fetch();
        }

        public function load_chars($name)
        {
            $stmt = $this->game_db->prepare('SELECT Name FROM Character WHERE AccountId = :user');
            $stmt->execute([':user' => $this->website->c($name)]);
            return $stmt->fetch_all();
        }

        public function check_guild($name)
        {
            $stmt = $this->game_db->prepare('SELECT G_Name FROM GuildMember WHERE Name = :char');
            $stmt->execute([':char' => $this->website->c($name)]);
            return $stmt->fetch();
        }

        public function load_guild_info($g_name)
        {
            $stmt = $this->game_db->prepare('SELECT G_Mark, G_Master FROM Guild WHERE G_Name = :g_name');
            $stmt->execute([':g_name' => $this->website->c($g_name)]);
            return $stmt->fetch();
        }

        public function guild_member_count($g_name)
        {
            $stmt = $this->game_db->prepare('SELECT COUNT(Name) AS count FROM GuildMember WHERE G_Name = :g_name');
            $stmt->execute([':g_name' => $this->website->c($g_name)]);
            return $stmt->fetch();
        }

        public function check_hidden_char($name, $server, $gs = null)
        {
			if($gs != null && defined('HIDE_CHARS_GS') && in_array($gs, HIDE_CHARS_GS[$server])){
				return true;
			}
			//else{
				$stmt = $this->website->db('web')->prepare('SELECT until_date FROM DmN_Hidden_Chars WHERE account = :name AND server = :server');
				$stmt->execute([':name' => $this->website->c($name), ':server' => $this->website->c($server)]);
				if($info = $stmt->fetch()){
					if($info['until_date'] > time()){
						return true;
					} else{
						$this->delete_expired_hide($name, $server);
						return false;
					}
				} else{
					return false;
				}
			//}
        }
		
		public function check_hidden_char_PK($name, $server)
        {
            $stmt = $this->website->db('web')->prepare('SELECT until_date FROM DmN_Hidden_Chars_PK WHERE account = :name AND server = :server');
            $stmt->execute([':name' => $this->website->c($name), ':server' => $this->website->c($server)]);
            if($info = $stmt->fetch()){
                if($info['until_date'] > time()){
                    return true;
                } else{
                    $this->delete_expired_hide($name, $server);
                    return false;
                }
            } else{
                return false;
            }
        }

        public function delete_expired_hide($name, $server)
        {
            $stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Hidden_Chars WHERE account = :name AND server = :server');
            $stmt->execute([':name' => $this->website->c($name), ':server' => $this->website->c($server)]);
        }

        public function add_account_log($log, $credits, $acc, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Account_Logs (text, amount, date, account, server, ip) VALUES (:text, :amount, GETDATE(), :acc, :server, :ip)');
            $stmt->execute([':text' => $log, ':amount' => round($credits), ':acc' => $acc, ':server' => $server, ':ip' => $this->website->ip()]);
            $stmt->close_cursor();
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function get_guild_info($guild, $server)
        {
			$g_union = (MU_VERSION < 1) ? '' : ', Number, G_Union';
            $stmt = $this->website->db('game', $server)->prepare('SELECT G_Name, G_Master, G_Mark, G_Score '.$g_union.' FROM Guild WHERE G_Name = :name');
            $stmt->execute([':name' => $this->website->c($guild)]);
            if($row = $stmt->fetch()){
                $membercount = $this->website->db('game', $server)->snumrows('SELECT COUNT(Name) as count FROM GuildMember WHERE G_Name = \'' . $this->website->db('game', $server)->sanitize_var($this->website->c($guild)) . '\'');
                $union = '';
                $hostility = '';
				if($g_union != ''){
					if($row['G_Union'] != 0){
						$stmt = $this->website->db('game', $server)->prepare('SELECT G_Name FROM Guild WHERE G_Union = :number AND G_Name != :name');
						$stmt->execute([':number' => $row['G_Union'], ':name' => $guild]);
						while($row2 = $stmt->fetch()){
							$union .= '<a href="' . $this->config->base_url . 'info/guild/' . bin2hex($row2['G_Name']) . '/' . $server . '">' . $row2['G_Name'] . '</a>,';
						}
					}
				}
                return [
					'G_Name' => $row['G_Name'], 
					'G_Master' => $row['G_Master'], 
					'G_Mark' => urlencode(bin2hex($row['G_Mark'])), 
					'G_Score' => (int)$row['G_Score'], 
					'MemberCount' => $membercount, 
					'aliance_guilds' => ($union != '') ? substr($union, 0, -1) : 'N/A'
				];
            }
            return false;
        }
		
		private function checkStatus($acc, $server)
        {
			$stmt = $this->website->db('account', $server)->prepare('SELECT ConnectStat, IP FROM MEMB_STAT WHERE memb___id = :user');
			$stmt->execute([':user' => $acc]);
			if($status = $stmt->fetch()){
				return $status;
			}
			return false;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function get_guild_members($guild, $server)
        {
			$g_status = (MU_VERSION < 1) ? '0 AS G_Status' : 'g.G_Status';
			$order = (MU_VERSION < 1) ? 'g.Name ASC' : 'g.G_Status DESC';
			$stmt = $this->website->db('game', $server)->prepare('SELECT g.Name, '.$g_status.', c.Class, c.cLevel, c.AccountId, ' . $this->reset_column($server) . ' ' . substr_replace($this->greset_column($server), '', -1) . ' FROM GuildMember AS g INNER JOIN Character AS c ON (g.Name Collate Database_Default = c.Name Collate Database_Default) WHERE g.G_Name = :name ORDER BY '.$order);
            $stmt->execute([':name' => $this->website->c($guild)]);
            $data = $stmt->fetch_all();
			foreach($data AS $id => $row){
				$status = $this->checkStatus($row['AccountId'], $server);
				if(!$status){
					$connectStat = 0;
					$cntrCode = 'us';
				}
				else{
					$connectStat = ($status['ConnectStat'] == 1) ? 1 : 0;
					$cntrCode = $this->website->get_country_code($status['IP']);
				}
	
                $this->guild_info[] = [
					'name' => $row['Name'], 
					'position' => $this->website->get_guild_status($row['G_Status']), 
					'level' => $row['cLevel'], 
					'mlevel' => $this->load_master_level($row['Name'], $server),
					'resets' => $row['resets'], 
					'gresets' => $row['grand_resets'], 
					'class' => $this->website->get_char_class($row['Class'], true),
					'status' => $connectStat,
					'country' => $cntrCode,
					
				];
            }
            return $this->guild_info;
        }

        public function decrease_zen($account, $money, $char)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Money = Money - :money WHERE AccountId = :account AND Name = :char');
            return $stmt->execute([':money' => (int)$money, ':account' => $this->website->c($account), ':char' => $this->website->c($char)]);
        }

        public function add_zen($account, $money, $char)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Money = Money + :money WHERE AccountId = :account AND Name = :char');
            return $stmt->execute([':money' => (int)$money, ':account' => $this->website->c($account), ':char' => $this->website->c($char)]);
        }

        public function load_chars_from_ref($ref_acc, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('SELECT Name, cLevel, ' . $this->reset_column($server) . substr_replace($this->greset_column($server), '', -1) . ' FROM Character WHERE AccountId = :ref_acc');
            $stmt->execute([':ref_acc' => $this->website->c($ref_acc)]);
            $char_list = [];
            while($row = $stmt->fetch()){
                $char_list[] = ['Name' => $row['Name'], 'cLevel' => $row['cLevel'], 'resets' => $row['resets'], 'grand_resets' => $row['grand_resets'], 'mlevel' => $this->load_master_level($row['Name'], $server)];
            }
            return $char_list;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function load_master_level($char, $server)
        {
            if($this->config->values('table_config', [$server, 'master_level', 'column']) != false){
                $stmt = $this->website->db('game', $server)->prepare('SELECT ' . $this->config->values('table_config', [$server, 'master_level', 'column']) . ' AS mlevel FROM ' . $this->config->values('table_config', [$server, 'master_level', 'table']) . ' WHERE ' . $this->config->values('table_config', [$server, 'master_level', 'identifier_column']) . ' = :char');
                $stmt->execute([':char' => $char]);
                $mlevel = $stmt->fetch();
                $stmt->close_cursor();
                if($mlevel != false){
                    return $mlevel['mlevel'];
                }
            }
            return 0;
        }

        public function check_if_char_exists($char, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('SELECT Name, AccountId, '.$this->website->get_char_id_col($server).' AS id FROM Character WHERE Name = :name');
            $stmt->execute([':name' => $char]);
            return $stmt->fetch();
        }

        public function has_guild($char)
        {
            $stmt = $this->game_db->prepare('SELECT Name FROM GuildMember WHERE Name = :name');
            $stmt->execute([':name' => $char]);
            return $stmt->fetch();
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function update_account_character($old, $new)
        {
			$data = 'UPDATE AccountCharacter SET 
					GameIDC = CASE WHEN (GameIDC = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameIDC END,
					GameId1 = CASE WHEN (GameId1 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId1 END,
					GameId2 = CASE WHEN (GameId2 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId2 END,
					GameId3 = CASE WHEN (GameId3 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId3 END,
					GameId4 = CASE WHEN (GameId4 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId4 END,
					GameId5 = CASE WHEN (GameId5 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId5 END';
			if(MU_VERSION >= 9){
				$data .= ', GameId6 = CASE WHEN (GameId6 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId6 END, GameId7 = CASE WHEN (GameId7 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId7 END, GameId8 = CASE WHEN (GameId8 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId8 END';
			}	
			if(MU_VERSION >= 10){
				$data .= ', GameId9 = CASE WHEN (GameId9 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId9 END, GameId10 = CASE WHEN (GameId10 = \'' . $this->game_db->sanitize_var($old) . '\') THEN \'' . $this->game_db->sanitize_var($new) . '\' ELSE GameId10 END';
			}				
            return $this->game_db->query($data);
        }

        public function update_guild($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE Guild SET G_Master = :name WHERE G_Master = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }

        public function update_guild_member($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE GuildMember SET Name = :name WHERE Name = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }

        public function update_character($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE Character SET Name = :name WHERE Name = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }

        public function update_option_data($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE OptionData SET Name = :name WHERE Name = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }

        public function update_t_friendlist($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE T_FriendList SET FriendName = :name WHERE FriendName = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }

        public function update_t_friendmail($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE T_FriendMail SET FriendName = :name WHERE FriendName = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }
		
		public function update_t_friendmain($old, $new)
        {
			if($this->game_db->check_table('T_FriendMain') > 0){
				$stmt = $this->game_db->prepare('UPDATE T_FriendMain SET Name = :name WHERE Name = :old_name');
				return $stmt->execute([':name' => $new, ':old_name' => $old]);  
			}
			else{
                return;
            }
        }     

        public function update_t_cguid($old, $new)
        {
            $stmt = $this->game_db->prepare('UPDATE T_CGuid SET Name = :name WHERE Name = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }
		
		public function update_T_CurCharName($old, $new)
        {
			if($this->game_db->check_table('T_CurCharName') > 0){
				$stmt = $this->game_db->prepare('UPDATE T_CurCharName SET Name = :name WHERE Name = :old_name');
				return $stmt->execute([':name' => $new, ':old_name' => $old]);
			}
			else{
                return;
            }
        }
		
		public function update_T_Event_Inventory($old, $new)
        {
			if($this->game_db->check_table('T_Event_Inventory') > 0){
				$stmt = $this->game_db->prepare('UPDATE T_Event_Inventory SET Name = :name WHERE Name = :old_name');
				return $stmt->execute([':name' => $new, ':old_name' => $old]);
			}
			else{
                return;
            }
        }

        public function update_master_level_table($old, $new, $server)
        {
            $stmt = $this->game_db->prepare('UPDATE ' . $this->config->values('table_config', [$server, 'master_level', 'table']) . ' SET ' . $this->config->values('table_config', [$server, 'master_level', 'identifier_column']) . ' = :name WHERE ' . $this->config->values('table_config', [$server, 'master_level', 'identifier_column']) . ' = :old_name');
            return $stmt->execute([':name' => $new, ':old_name' => $old]);
        }

        public function update_IGC_Gens($old, $new)
        {
            if($this->game_db->check_table('IGC_Gens') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_Gens SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_IGC_GensAbuse($old, $new)
        {
            if($this->game_db->check_table('IGC_GensAbuse') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_GensAbuse SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_GremoryCase($old, $new)
        {
            if($this->game_db->check_table('IGC_GremoryCase') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_GremoryCase SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_HuntingRecord($old, $new)
        {
            if($this->game_db->check_table('IGC_HuntingRecord') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_HuntingRecord SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_HuntingRecordOption($old, $new)
        {
            if($this->game_db->check_table('IGC_HuntingRecordOption') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_HuntingRecordOption SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_LabyrinthClearLog($old, $new)
        {
            if($this->game_db->check_table('IGC_LabyrinthClearLog') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_LabyrinthClearLog SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_LabyrinthInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_LabyrinthInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_LabyrinthInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_LabyrinthLeagueLog($old, $new)
        {
            if($this->game_db->check_table('IGC_LabyrinthLeagueLog') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_LabyrinthLeagueLog SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_LabyrinthLeagueUser($old, $new)
        {
            if($this->game_db->check_table('IGC_LabyrinthLeagueUser') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_LabyrinthLeagueUser SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_LabyrinthMissionInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_LabyrinthMissionInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_LabyrinthMissionInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_MixLostItemInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_MixLostItemInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_MixLostItemInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_Muun_Inventory($old, $new)
        {
            if($this->game_db->check_table('IGC_Muun_Inventory') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_Muun_Inventory SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_Muun_Period($old, $new, $server)
        {
			if($this->game_db->check_table('IGC_Muun_Period') > 0){
				$stmt = $this->website->db('game', $server)->prepare('UPDATE IGC_Muun_Period SET Name = :name WHERE Name = :old_name');
				return $stmt->execute([':name' => $new, ':old_name' => $old]);
			} else{
                return;
            }
        }
		
		public function update_IGC_RestoreItem_Inventory($old, $new)
        {
            if($this->game_db->check_table('IGC_RestoreItem_Inventory') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_RestoreItem_Inventory SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
        public function update_IGC_PeriodBuffInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_PeriodBuffInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_PeriodBuffInfo SET CharacterName = :name WHERE CharacterName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_IGC_PeriodExpiredItemInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_PeriodExpiredItemInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_PeriodExpiredItemInfo SET CharacterName = :name WHERE CharacterName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_IGC_PeriodItemInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_PeriodItemInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_PeriodItemInfo SET CharacterName = :name WHERE CharacterName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_PentagramInfo($old, $new)
        {
            if($this->game_db->check_table('IGC_PentagramInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_PentagramInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

		public function update_IGC_PStore_Items($old, $new)
        {
            if($this->game_db->check_table('IGC_PStore_Items') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_PStore_Items SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_PStore_Data($old, $new)
        {
            if($this->game_db->check_table('IGC_PStore_Data') > 0){
                $stmt = $this->game_db->prepare('UPDATE IGC_PStore_Data SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		 
        public function update_IGC_PersonalStore_Info($old, $new)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('IGC_PersonalStore_Info') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE IGC_PersonalStore_Info SET name = :name WHERE name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function update_IGC_ArtifactInfo($old, $new)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('IGC_ArtifactInfo') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE IGC_ArtifactInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_BlessingBox_Character($old, $new)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('IGC_BlessingBox_Character') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE IGC_BlessingBox_Character SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_HuntPoint($old, $new)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('IGC_HuntPoint') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE IGC_HuntPoint SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_IGC_StatsSystem($old, $new)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('IGC_StatsSystem') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE IGC_StatsSystem SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function insert_IGC_PersonalStore_ChangeName($old, $new, $id)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('IGC_PersonalStore_ChangeName') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('INSERT INTO IGC_PersonalStore_ChangeName (old_name, new_name, character_id) VALUES (:old_name, :new_name, :character_id)');
                return $stmt->execute([':old_name' => $old, ':new_name' => $new, ':character_id' => $id]);
            } else{
                return;
            }
        }
		
        public function update_T_3rd_Quest_Info($old, $new)
        {
            if($this->game_db->check_table('T_3rd_Quest_Info') > 0){
                $stmt = $this->game_db->prepare('UPDATE T_3rd_Quest_Info SET CHAR_NAME = :name WHERE CHAR_NAME = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_T_GMSystem($old, $new)
        {
            if($this->game_db->check_table('T_GMSystem') > 0){
                $stmt = $this->game_db->prepare('UPDATE T_GMSystem SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_T_LUCKY_ITEM_INFO($old, $new)
        {
            if($this->game_db->check_table('T_LUCKY_ITEM_INFO') > 0){
                $stmt = $this->game_db->prepare('UPDATE T_LUCKY_ITEM_INFO SET CharName = :name WHERE CharName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_T_PentagramInfo($old, $new)
        {
            if($this->game_db->check_table('T_PentagramInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE T_PentagramInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_T_QUEST_EXP_INFO($old, $new)
        {
            if($this->game_db->check_table('T_QUEST_EXP_INFO') > 0){
                $stmt = $this->game_db->prepare('UPDATE T_QUEST_EXP_INFO SET CHAR_NAME = :name WHERE CHAR_NAME = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_PetWarehouse($old, $new)
        {
            if($this->game_db->check_table('PetWarehouse') > 0){
                $stmt = $this->game_db->prepare('UPDATE PetWarehouse SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_T_WaitFriend($old, $new)
        {
            if($this->game_db->check_table('T_WaitFriend') > 0){
                $stmt = $this->game_db->prepare('UPDATE T_WaitFriend SET FriendName = :name WHERE FriendName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_T_PSHOP_ITEMVALUE_INFO($old, $new)
        {
            if($this->website->db('game', $this->session->userdata(['user' => 'server']))->check_table('T_PSHOP_ITEMVALUE_INFO') > 0){
                $stmt = $this->website->db('game', $this->session->userdata(['user' => 'server']))->prepare('UPDATE T_PSHOP_ITEMVALUE_INFO SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_C_Monster_KillCount($old, $new)
        {
            if($this->game_db->check_table('C_Monster_KillCount') > 0){
                $stmt = $this->game_db->prepare('UPDATE C_Monster_KillCount SET name = :name WHERE name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_C_PlayerKiller_Info($old, $new)
        {
            if($this->game_db->check_table('C_PlayerKiller_Info') > 0){
                $stmt = $this->game_db->prepare('UPDATE C_PlayerKiller_Info SET Victim = :name WHERE Victim = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_C_PlayerKiller_Info2($old, $new)
        {
            if($this->game_db->check_table('C_PlayerKiller_Info') > 0){
                $stmt = $this->game_db->prepare('UPDATE C_PlayerKiller_Info SET Killer = :name WHERE Killer = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_Gens_Left($old, $new)
        {
            if($this->game_db->check_table('Gens_Left') > 0){
                $stmt = $this->game_db->prepare('UPDATE Gens_Left SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_Gens_Rank($old, $new)
        {
            if($this->game_db->check_table('Gens_Rank') > 0){
                $stmt = $this->game_db->prepare('UPDATE Gens_Rank SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_Gens_Reward($old, $new)
        {
            if($this->game_db->check_table('Gens_Reward') > 0){
                $stmt = $this->game_db->prepare('UPDATE Gens_Reward SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EnhanceSkillTree($old, $new)
        {
            if($this->game_db->check_table('EnhanceSkillTree') > 0){
                $stmt = $this->game_db->prepare('UPDATE EnhanceSkillTree SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EventEntryCount($old, $new)
        {
            if($this->game_db->check_table('EventEntryCount') > 0){
                $stmt = $this->game_db->prepare('UPDATE EventEntryCount SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EventEntryLimit($old, $new)
        {
            if($this->game_db->check_table('EventEntryLimit') > 0){
                $stmt = $this->game_db->prepare('UPDATE EventEntryLimit SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EventInventory($old, $new)
        {
            if($this->game_db->check_table('EventInventory') > 0){
                $stmt = $this->game_db->prepare('UPDATE EventInventory SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_FavoriteWarpList($old, $new)
        {
            if($this->game_db->check_table('FavoriteWarpList') > 0){
                $stmt = $this->game_db->prepare('UPDATE FavoriteWarpList SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_GremoryCase($old, $new)
        {
            if($this->game_db->check_table('GremoryCase') > 0){
                $stmt = $this->game_db->prepare('UPDATE GremoryCase SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_HelperData($old, $new)
        {
            if($this->game_db->check_table('HelperData') > 0){
                $stmt = $this->game_db->prepare('UPDATE HelperData SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_MuHelperPlus($old, $new)
        {
            if($this->game_db->check_table('MuHelperPlus') > 0){
                $stmt = $this->game_db->prepare('UPDATE MuHelperPlus SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_MuQuestInfo($old, $new)
        {
            if($this->game_db->check_table('MuQuestInfo') > 0){
                $stmt = $this->game_db->prepare('UPDATE MuQuestInfo SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

		public function update_MuunInventory($old, $new)
        {
            if($this->game_db->check_table('MuunInventory') > 0){
                $stmt = $this->game_db->prepare('UPDATE MuunInventory SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_PentagramJewel($old, $new)
        {
            if($this->game_db->check_table('PentagramJewel') > 0){
                $stmt = $this->game_db->prepare('UPDATE PentagramJewel SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_PersonalShopRenewalList($old, $new)
        {
            if($this->game_db->check_table('PersonalShopRenewalList') > 0){
                $stmt = $this->game_db->prepare('UPDATE PersonalShopRenewalList SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_PShopItemValue($old, $new)
        {
            if($this->game_db->check_table('PShopItemValue') > 0){
                $stmt = $this->game_db->prepare('UPDATE PShopItemValue SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_QuestGuide($old, $new)
        {
            if($this->game_db->check_table('QuestGuide') > 0){
                $stmt = $this->game_db->prepare('UPDATE QuestGuide SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_QuestKillCount($old, $new)
        {
            if($this->game_db->check_table('QuestKillCount') > 0){
                $stmt = $this->game_db->prepare('UPDATE QuestKillCount SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_QuestWorld($old, $new)
        {
            if($this->game_db->check_table('QuestWorld') > 0){
                $stmt = $this->game_db->prepare('UPDATE QuestWorld SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_RankingBloodCastle($old, $new)
        {
            if($this->game_db->check_table('RankingBloodCastle') > 0){
                $stmt = $this->game_db->prepare('UPDATE RankingBloodCastle SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_RankingCastleSiege($old, $new)
        {
            if($this->game_db->check_table('RankingCastleSiege') > 0){
                $stmt = $this->game_db->prepare('UPDATE RankingCastleSiege SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_RankingChaosCastle($old, $new)
        {
            if($this->game_db->check_table('RankingChaosCastle') > 0){
                $stmt = $this->game_db->prepare('UPDATE RankingChaosCastle SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_RankingDevilSquare($old, $new)
        {
            if($this->game_db->check_table('RankingDevilSquare') > 0){
                $stmt = $this->game_db->prepare('UPDATE RankingDevilSquare SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_RankingDuel($old, $new)
        {
            if($this->game_db->check_table('RankingDuel') > 0){
                $stmt = $this->game_db->prepare('UPDATE RankingDuel SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_RankingIllusionTemple($old, $new)
        {
            if($this->game_db->check_table('RankingIllusionTemple') > 0){
                $stmt = $this->game_db->prepare('UPDATE RankingIllusionTemple SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EVENT_INFO($old, $new, $table_config, $server)
        {
            if($this->website->db($table_config['db'], $server)->check_table('EVENT_INFO') > 0){
                $stmt = $this->website->db($table_config['db'], $server)->prepare('UPDATE EVENT_INFO SET CharacterName = :name WHERE CharacterName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EVENT_INFO_BC_5TH($old, $new, $table_config, $server)
        {
            if($this->website->db($table_config['db'], $server)->check_table('EVENT_INFO_BC_5TH') > 0){
                $stmt = $this->website->db($table_config['db'], $server)->prepare('UPDATE EVENT_INFO_BC_5TH SET CharacterName = :name WHERE CharacterName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EVENT_INFO_CC($old, $new, $table_config, $server)
        {
            if($this->website->db($table_config['db'], $server)->check_table('EVENT_INFO_CC') > 0){
                $stmt = $this->website->db($table_config['db'], $server)->prepare('UPDATE EVENT_INFO_CC SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_EVENT_INFO_IT($old, $new, $table_config, $server)
        {
            if($this->website->db($table_config['db'], $server)->check_table('EVENT_INFO_IT') > 0){
                $stmt = $this->website->db($table_config['db'], $server)->prepare('UPDATE EVENT_INFO_IT SET Name = :name WHERE Name = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_T_ENTER_CHECK_BC($old, $new, $table_config, $server)
        {
            if($this->website->db($table_config['db'], $server)->check_table('T_ENTER_CHECK_BC') > 0){
                $stmt = $this->website->db($table_config['db'], $server)->prepare('UPDATE T_ENTER_CHECK_BC SET CharName = :name WHERE CharName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }
		
		public function update_T_ENTER_CHECK_ILLUSION_TEMPLE($old, $new, $table_config, $server)
        {
            if($this->website->db($table_config['db'], $server)->check_table('T_ENTER_CHECK_ILLUSION_TEMPLE') > 0){
                $stmt = $this->website->db($table_config['db'], $server)->prepare('UPDATE T_ENTER_CHECK_ILLUSION_TEMPLE SET CharName = :name WHERE CharName = :old_name');
                return $stmt->execute([':name' => $new, ':old_name' => $old]);
            } else{
                return;
            }
        }

        public function update_DmN_Ban_List($old, $new)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Ban_List SET name = :name WHERE name = :old_name AND type = 2 AND server = :server');
            return $stmt->execute([':name' => $new, ':old_name' => $old, ':server' => $this->session->userdata(['user' => 'server'])]);
        }

        public function update_DmN_Gm_List($old, $new)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Gm_List SET character = :name WHERE character = :old_name AND server = :server');
            return $stmt->execute([':name' => $new, ':old_name' => $old, ':server' => $this->session->userdata(['user' => 'server'])]);
        }

        public function update_DmN_Market($old, $new)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Market SET char = :name WHERE char = :old_name AND server = :server');
            return $stmt->execute([':name' => $new, ':old_name' => $old, ':server' => $this->session->userdata(['user' => 'server'])]);
        }

        public function update_DmN_Market_Logs($old, $new)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Market_Logs SET char = :name WHERE char = :old_name AND server = :server');
            return $stmt->execute([':name' => $new, ':old_name' => $old, ':server' => $this->session->userdata(['user' => 'server'])]);
        }

        public function update_DmN_Votereward_Ranking($old, $new)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Votereward_Ranking SET character = :name WHERE character = :old_name AND server = :server');
            return $stmt->execute([':name' => $new, ':old_name' => $old, ':server' => $this->session->userdata(['user' => 'server'])]);
        }

        public function add_to_change_name_history($old, $new)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_ChangeName_History (account, old_name, new_name, change_date, server) VALUES (:acc, :old, :new, GETDATE(), :server)');
            return $stmt->execute([':acc' => $this->session->userdata(['user' => 'username']), ':old' => $old, ':new' => $new, ':server' => $this->session->userdata(['user' => 'server'])]);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function check_amount_of_coins()
        {
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
            //$check_in = ['inv' => [12, 76], 'inv2' => [76, 107], 'inv3' => [108, 140],];
			$check_in = ['inv' => [12, 76]];
            $items = [];
            $coins = [];
            foreach($check_in AS $name => $loops){
                for($a = $loops[0]; $a < $loops[1]; $a++){
                    $items[$a] = strtoupper($items_array[$a]);
                }
            }
            $items = array_diff($items, [str_pad("", $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'), "F")]);
            foreach($items as $key => $item){
                $this->iteminfo->itemData($item);
                if($this->iteminfo->id == 100 && $this->iteminfo->type == 14){
                    $coins[$key] = $this->iteminfo->dur;
                }
            }
            return $coins;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function check_amount_of_items($id, $cat)
        {
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
            //$check_in = ['inv' => [12, 76], 'inv2' => [76, 107], 'inv3' => [108, 140],];
			$check_in = ['inv' => [12, 76]];
            $items = [];
            $coins = [];
            foreach($check_in AS $name => $loops){
                for($a = $loops[0]; $a < $loops[1]; $a++){
                    $items[$a] = strtoupper($items_array[$a]);
                }
            }
            $items = array_diff($items, [str_pad("", $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'), "F")]);
            foreach($items as $key => $item){
                $this->iteminfo->itemData($item);
                if($this->iteminfo->id == $id && $this->iteminfo->type == $cat){
                    $coins[$key] = $this->iteminfo->dur;
                }
            }
            return $coins;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function remove_old_coins($data)
        {
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
            foreach($data AS $key => $val){
                if(array_key_exists($key, $items_array)){
                    $items_array[$key] = str_pad("", $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'), "F");
                }
            }
            $stmt = $this->game_db->prepare('UPDATE Character SET Inventory = 0x' . implode('', $items_array) . ' WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function add_new_coins($where_to_add, $hex)
        {
            $this->get_inventory_content($this->vars['character'], $this->session->userdata(['user' => 'server']));
            $items_array = str_split($this->Mcharacter->char_info['Inventory'], $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size'));
            if(array_key_exists($where_to_add, $items_array)){
                $items_array[$where_to_add] = $hex;
            }
            $stmt = $this->game_db->prepare('UPDATE Character SET Inventory = 0x' . implode('', $items_array) . ' WHERE Name = :char AND AccountId = :user');
            $stmt->execute([':char' => $this->website->c($this->vars['character']), ':user' => $this->session->userdata(['user' => 'username'])]);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function draw_chance($probabilities, $max_propability = 1000)
        {
            $rand = rand(0, $max_propability);
            $keys = array_keys($probabilities);
            arsort($keys);
            do{
                $sum = array_sum($keys);
                if($rand <= $sum && $rand >= $sum - end($keys)){
                    return $keys[key($keys)];
                }
            } while(array_pop($keys));
        }

        public function check_claimed_level_rewards($id, $char, $server)
        {
            $stmt = $this->website->db('web')->prepare('SELECT TOP 1 id FROM DmN_Level_Claimed_Rewards WHERE reward_id = :id AND account = :account AND character = :char AND server = :server');
            $stmt->execute([':id' => $id, ':account' => $this->session->userdata(['user' => 'username']), ':char' => $char, ':server' => $server]);
            return $stmt->fetch();
        }

        public function log_level_reward($id, $char, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Level_Claimed_Rewards (reward_id, account, character, server) VALUES (:id, :account, :char, :server)');
            return $stmt->execute([':id' => $id, ':account' => $this->session->userdata(['user' => 'username']), ':char' => $char, ':server' => $server]);
        }
		
        public function load_artifacts($name, $server){
			if($this->website->db('game', $server)->check_if_table_exists('IGC_ArtifactInfo')){
				$stmt = $this->website->db('game', $server)->prepare('SELECT Position, ArtifactLevel, ArtifactType FROM IGC_ArtifactInfo WHERE Name = :char ORDER BY Position ASC');
				$stmt->execute([':char' => $name]);
				return $stmt->fetch_all();
			}
			return false;
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function is_hex($hex_code) {
			return @preg_match("/^[a-f0-9]{2,}$/i", $hex_code) && !(strlen($hex_code) & 1);
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
        private function clean_hex($data)
        {
			
            if(!$this->is_hex($data)){
                $data = bin2hex($data);
            }
            if(substr_count($data, "\0")){
                $data = str_replace("\0", '', $data);
            }
            return strtoupper($data);
        }
    }