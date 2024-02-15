<?php

class Mskill_tree_specialization extends model{
	private $characters = [];
	public $charInfo = [];
	
	public function __contruct(){
        parent::__construct();
    }
	
	/**
     * Load required character data from database on current account
     *
     * @param string $account
	 * @param string $server
     *
     * @return mixed
     */
	
	public function load_char_list($account, $server){
		if($this->website->db('game', $server)->check_if_table_exists('MasterSkillTree')){
			$stmt = $this->website->db('game', $server)->prepare('SELECT c.Name, c.cLevel, m.MasterLevel AS mLevel, m.MasterPoint AS mlPoint, c.'.$this->website->get_char_id_col($server).' AS id FROM Character AS c INNER JOIN MasterSkillTree AS m ON(c.Name Collate Database_Default = m.Name Collate Database_Default) WHERE c.AccountId = :account');
		}
		else{
			$stmt = $this->website->db('game', $server)->prepare('SELECT Name, cLevel, mLevel, mlPoint, '.$this->website->get_char_id_col($server).' AS id FROM Character WHERE AccountId = :account');
		}
        $stmt->execute([':account' => $account]);
		
        $i = 0;
        while($row = $stmt->fetch()){
            $this->characters[] = [
				'id' => $row['id'],
				'Name' => $row['Name'],
                'cLevel' => $row['cLevel'],
				'mLevel' => $row['mLevel'],
				'specializations' => $this->specialization_list($account, $server, $row['id'])
			];
            $i++;
        }
        if($i > 0){
            return $this->characters;
        } 
		else{
            return false;
        }
    }
	
	/**
     * Check if character exists
     *
     * @param string $account
	 * @param string $server
	 * @param int $id
     *
     * @return mixed
     */
	
	public function check_char($account, $server, $id){
		if($this->website->db('game', $server)->check_if_table_exists('MasterSkillTree')){
			$query_enchancement = '0 AS i4thSkillPoint';
			$join_enchancement = '';
			if(MU_VERSION >= 9){
				if($this->website->db('game', $server)->check_if_table_exists('EnhanceSkillTree')){
					$query_enchancement = 'e.EnhancePoint AS i4thSkillPoint, CONVERT(IMAGE, e.EnhanceSkill) AS MagicList2, CONVERT(IMAGE, e.EnhanceSkillPassive) AS MagicList3';
					$join_enchancement = 'INNER JOIN EnhanceSkillTree AS e ON(c.Name Collate Database_Default = e.Name Collate Database_Default)';
				}
			}
			$stmt = $this->website->db('game', $server)->prepare('SELECT TOP 1 c.Name, c.Class, c.cLevel, m.MasterLevel AS mLevel, m.MasterPoint AS mlPoint, m.MasterExperience AS mlExperience, CONVERT(IMAGE, m.MasterSkill) AS MagicList, '.$query_enchancement.', c.'.$this->website->get_char_id_col($server).' AS id FROM Character AS c INNER JOIN MasterSkillTree AS m ON(c.Name Collate Database_Default = m.Name Collate Database_Default) '.$join_enchancement.' WHERE c.AccountId = :account AND c.'.$this->website->get_char_id_col($server).' = :id');
			$stmt->execute([
				':account' => $account,
				':id' => $id
			]);
			if($this->charInfo = $stmt->fetch()){
				return $this->charInfo;
			}
		}
		else{
			$query_enchancement = '0 AS i4thSkillPoint, 0 AS AddStrength, 0 AS AddDexterity, 0 AS AddVitality, 0 AS AddEnergy';
			if(MU_VERSION >= 9){
				$query_enchancement = 'i4thSkillPoint, AddStrength, AddDexterity, AddVitality, AddEnergy';
			}
			$stmt = $this->website->db('game', $server)->prepare('SELECT TOP 1 Name, Class, cLevel, mLevel, mlPoint, mlExperience, mlNextExp, '.$query_enchancement.', '.$this->website->get_char_id_col($server).' AS id FROM Character WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :id');
			$stmt->execute([
				':account' => $account,
				':id' => $id
			]);
			if($this->charInfo = $stmt->fetch()){
				$this->charInfo['MagicList'] = $this->get_skill_list($account, $server, $id);
				return $this->charInfo;
			}
		}
		return false;
	}
	
	private function get_skill_list($account, $server, $id){
		$sql = (DRIVER == 'pdo_odbc') ? 'MagicList' : 'CONVERT(IMAGE, MagicList) AS MagicList';
		$stmt = $this->website->db('game', $server)->prepare('SELECT ' . $sql . ' FROM Character WHERE '.$this->website->get_char_id_col($server).' = :id AND AccountId = :user');
		$stmt->execute([
			':id' => $id, 
			':user' => $this->session->userdata(['user' => 'username'])
		]);
		if(DRIVER == 'pdo_dblib'){
			$skills = unpack('H*', implode('', $stmt->fetch()));
			return $this->clean_hex($skills[1]);
		} else{
			if($skills = $stmt->fetch()){
				return $this->clean_hex($skills['MagicList']);
			}
		}
	}
	
	/**
     * Check if specialization exists
     *
     * @param string|bool $title
	 * @param string $account
	 * @param string $server
	 * @param int $char_id
	 * @param int|bool $id
     *
     * @return mixed
     */
	
	public function check_specialization($title = false, $account, $server, $char_id, $id = false){
		$search = [($title != false) ? $title : $id, ($title != false) ? 'title' : 'id'];
		$stmt = $this->website->db('web')->prepare('SELECT TOP 1 id, name, skills, skills2, skills3, mlevel, mlpoints, mlpoints2, mlexp, mlnextexp, class, addstr, adddex, addene, addvit FROM DmN_Skill_Tree_Specialization WHERE '.$search[1].' = :title AND account = :account AND server = :server AND mu_id = :char_id');
		$stmt->execute([
				':title' => $search[0],
				':account' => $account,
				':server' => $server,
				':char_id' => $char_id		
		]);
		return $stmt->fetch();
	}
	
	/**
     * Remove specialization
     *
     * @param string $account
	 * @param string $server
	 * @param int $char_id
	 * @param int $id
     *
     * @return mixed
     */
	
	public function remove_specialization($account, $server, $char_id, $id){
		$stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Skill_Tree_Specialization WHERE id = :id AND account = :account AND server = :server AND mu_id = :char_id');
		return $stmt->execute([
				':id' => $id,
				':account' => $account,
				':server' => $server,
				':char_id' => $char_id		
		]);
	}
	
	/**
     * Count how many specializations have character
     *
     * @param string $account
	 * @param string $server
	 * @param int $id
     *
     * @return int
     */
	
	public function count_specializations($account, $server, $id){
		$stmt = $this->website->db('web')->prepare('SELECT COUNT(id) AS count FROM DmN_Skill_Tree_Specialization WHERE account = :account AND server = :server AND mu_id = :id');
		$stmt->execute([
				':account' => $account,
				':server' => $server,
				':id' => $id		
		]);
		$count = $stmt->fetch();
		return $count['count'];
	}
	
	/**
     * Save stats specialization
     *
	 * @param string $title
     * @param string $account
	 * @param string $server
	 * @param array $date
     *
     * @return mixed
     */
	
	public function save_specialization($title, $account, $server, $data){
		$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Skill_Tree_Specialization (title, account, server, name, mu_id, skills, skills2, skills3, level, mlevel, mlpoints, mlpoints2, class, mlexp, mlnextexp, addstr, adddex, addene, addvit) VALUES (:title, :account, :server, :name, :mu_id, :skills, :skills2, :skills3, :level, :mlevel, :mlpoints, :mlpoints4th, :class, :mlexp, :mlnextexp, :addstr, :adddex, :addene, :addvit)');
		if($this->website->db('game', $server)->check_if_table_exists('MasterSkillTree')){
			if(isset($data['MagicList'])){
				if(DRIVER == 'pdo_dblib'){
					$skills = unpack('H*', implode('', $data['MagicList']));
					$data['MagicList'] = $this->clean_hex($skills[1]);
				} else{
					$data['MagicList'] = $this->clean_hex($data['MagicList']);
				}
			}
			if(isset($data['MagicList2'])){
				if(DRIVER == 'pdo_dblib'){
					$skills2 = unpack('H*', implode('', $data['MagicList2']));
					$data['MagicList2'] = $this->clean_hex($skills2[1]);
				} else{
					$data['MagicList2'] = $this->clean_hex($data['MagicList2']);
				}
			}
			if(isset($data['MagicList3'])){
				if(DRIVER == 'pdo_dblib'){
					$skills3 = unpack('H*', implode('', $data['MagicList3']));
					$data['MagicList3'] = $this->clean_hex($skills3[1]);
				} else{
					$data['MagicList3'] = $this->clean_hex($data['MagicList3']);
				}
			}
		}
		$stmt->execute([
			':title' => $title,
			':account' => $account,
			':server' => $server,
			':name' => $data['Name'],
			':mu_id' => $data['id'],
			':skills' => $data['MagicList'],
			':skills2' => isset($data['MagicList2']) ? $data['MagicList2'] : NULL,
			':skills3' => isset($data['MagicList3']) ? $data['MagicList3'] : NULL,
			':level' => $data['cLevel'],
			':mlevel' => $data['mLevel'],
			':mlpoints' => $data['mlPoint'],
			':mlpoints4th' => $data['i4thSkillPoint'],
			':class' => $data['Class'],
			':mlexp' => $data['mlExperience'],
			':mlnextexp' => isset($data['mlNextExp']) ? $data['mlNextExp'] : 0,
			':addstr' => ($data['AddStrength'] != null) ? $data['AddStrength'] : 0,
			':adddex' => ($data['AddDexterity'] != null) ? $data['AddDexterity'] : 0,
			':addene' => ($data['AddEnergy'] != null) ? $data['AddEnergy'] : 0,
			':addvit' => ($data['AddVitality'] != null) ? $data['AddVitality'] : 0,
		]);
		
		if($this->website->db('game', $server)->check_if_table_exists('MasterSkillTree')){
			$newPoints = $data['mLevel'];
			$newEnchacementPoints = 0;
			if($data['mLevel'] > 400){
				$newPoints = 400;
				$newEnchacementPoints = $data['mLevel'] - 400;
			}
			
			$stmt = $this->website->db('game', $server)->prepare('UPDATE MasterSkillTree SET MasterPoint = ' . $newPoints . ', MasterSkill = NULL WHERE Name = :name');
			$stmt->execute([':name' => $data['Name']]);
			
			if($this->website->db('game', $server)->check_if_table_exists('EnhanceSkillTree')){
				$stmt2 = $this->website->db('game', $server)->prepare('UPDATE EnhanceSkillTree SET EnhancePoint = ' . $newEnchacementPoints . ', EnhanceSkill = NULL, EnhanceSkillPassive = NULL WHERE Name = :name');
				$stmt2->execute([':name' => $data['Name']]);
			}
		}
		else{
			$skill_size = 6;
			$empty = 'ff0000';
			if(MU_VERSION >= 9){
				$skill_size = 10;
				$empty = 'ff00000000';
			}
			
			$skills = $this->get_skill_list($account, $server, $data['id']);
			$skills_array = str_split($skills, $skill_size);
			foreach($skills_array AS $key => $skill){
				$index = $this->skill_index($skill);
				if($this->is_master_skill($index)){
					$skills_array[$key] = $empty;
				}
			}
			
			$newPoints = $data['mLevel'];
			$newEnchacementPoints = 0;
			if($data['mLevel'] > 400){
				$newPoints = 400;
				$newEnchacementPoints = $data['mLevel'] - 400;
			}
			
			$query_enchancement = '';
			
			if(MU_VERSION >= 9){
				$query_enchancement = ', i4thSkillPoint = '.$newEnchacementPoints.', AddStrength = 0, AddDexterity = 0, AddVitality = 0, AddEnergy = 0';
			}
			
			$stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET mlPoint = ' . $newPoints . ', MagicList = 0x' . implode('', $skills_array).' '.$query_enchancement.' WHERE '.$this->website->get_char_id_col($server).' = :id AND AccountId = :user');
			$stmt->execute([':id' => $data['id'], ':user' => $this->session->userdata(['user' => 'username'])]);
		}
	}
	
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
	
	/**
     * Set character stats from specialization
     *
     * @param string $account
	 * @param string $server
	 * @param int $id
	 * @param array $date
     *
     * @return mixed
     */
	
	public function set_character_stats($account, $server, $id, $data){
		if($this->website->db('game', $server)->check_if_table_exists('MasterSkillTree')){
			$stmt = $this->website->db('game', $server)->prepare('UPDATE MasterSkillTree SET MasterLevel = :mLevel, MasterPoint = :Points, MasterExperience = :mlexp, MasterSkill = 0x' . $data['skills'] .' WHERE Name = :name');
			$stmt->execute([
				':mLevel' => $data['mlevel'],
				':Points' => $data['mlpoints'],
				':mlexp' => $data['mlexp'],
				':name' => $data['name']
			]);
			
			if($this->website->db('game', $server)->check_if_table_exists('EnhanceSkillTree')){
				$stmt2 = $this->website->db('game', $server)->prepare('UPDATE EnhanceSkillTree SET EnhancePoint = :echpoints, EnhanceSkill = 0x' . $data['skills2'] .', EnhanceSkillPassive = 0x' . $data['skills3'] .' WHERE Name = :name');
				$stmt2->execute([':echpoints' => $data['mlpoints2'], ':name' => $data['name']]);
			}
		}
		else{
			$query_enchancement = '';
			if(MU_VERSION >= 9){
				$query_enchancement = 'i4thSkillPoint = '.$data['mlpoints2'].', AddStrength = '.$data['addstr'].', AddDexterity = '.$data['adddex'].', AddVitality = '.$data['addvit'].', AddEnergy = '.$data['addene'].', ';
			}
			
			$stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET mlPoint = :Points, mLevel = :mLevel, mlExperience = :mlexp, mlNextExp = :mlnextexp, '.$query_enchancement.' MagicList = 0x' . $data['skills'] .' WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :id');
			return $stmt->execute([
				':Points' => $data['mlpoints'],
				':mLevel' => $data['mlevel'],
				':mlexp' => $data['mlexp'],
				':mlnextexp' => $data['mlnextexp'],
				':account' => $account,
				':id' => $id
			]);
		}
	}
	
	/**
     * Set specialization stats from character
     *
     * @param string $account
	 * @param string $server
	 * @param array $date
	 * @param int $id	 
     *
     * @return mixed
     */
	
	public function set_specialization_stats($account, $server, $data, $id){
		$stmt = $this->website->db('web')->prepare('UPDATE DmN_Skill_Tree_Specialization SET mlpoints = :Points, mlpoints2 = :Points2,  mlevel = :mLevel, skills = :skills, skills2 = :skills2, skills3 = :skills3, mlexp = :mlexp, mlnextexp = :mlnextexp, addstr = :addstr, adddex = :adddex, addene = :addene, addvit = :addvit WHERE account = :account AND server = :server AND mu_id = :char_id AND id = :id');
		if($this->website->db('game', $server)->check_if_table_exists('MasterSkillTree')){
			if(isset($data['MagicList'])){
				if(DRIVER == 'pdo_dblib'){
					$skills = unpack('H*', implode('', $data['MagicList']));
					$data['MagicList'] = $this->clean_hex($skills[1]);
				} else{
					$data['MagicList'] = $this->clean_hex($data['MagicList']);
				}
			}
			if(isset($data['MagicList2'])){
				if(DRIVER == 'pdo_dblib'){
					$skills2 = unpack('H*', implode('', $data['MagicList2']));
					$data['MagicList2'] = $this->clean_hex($skills2[1]);
				} else{
					$data['MagicList2'] = $this->clean_hex($data['MagicList2']);
				}
			}
			if(isset($data['MagicList3'])){
				if(DRIVER == 'pdo_dblib'){
					$skills3 = unpack('H*', implode('', $data['MagicList3']));
					$data['MagicList3'] = $this->clean_hex($skills3[1]);
				} else{
					$data['MagicList3'] = $this->clean_hex($data['MagicList3']);
				}
			}
		}
		return $stmt->execute([
			':Points' => $data['mlPoint'],
			':Points2' => $data['i4thSkillPoint'],
			':mLevel' => $data['mLevel'],
			':skills' => $data['MagicList'],
			':skills2' => isset($data['MagicList2']) ? $data['MagicList2'] : NULL,
			':skills3' => isset($data['MagicList3']) ? $data['MagicList3'] : NULL,
			':mlexp' => $data['mlExperience'],
			':mlnextexp' => isset($data['mlNextExp']) ? $data['mlNextExp'] : 0,
			':addstr' => ($data['AddStrength'] != null) ? $data['AddStrength'] : 0,
			':adddex' => ($data['AddDexterity'] != null) ? $data['AddDexterity'] : 0,
			':addene' => ($data['AddEnergy'] != null) ? $data['AddEnergy'] : 0,
			':addvit' => ($data['AddVitality'] != null) ? $data['AddVitality'] : 0,
			':account' => $account,
			':server' => $server,
			':char_id' => $data['id'],
			':id' => $id
		]);
	}
	
	/**
     * Load specialization list for character
     *
     * @param string $account
	 * @param string $server
	 * @param int $id	 
     *
     * @return mixed
     */
	
	private function specialization_list($account, $server, $id){
		$stmt = $this->website->db('web')->prepare('SELECT id, title, level, mlevel FROM DmN_Skill_Tree_Specialization WHERE account = :account AND server = :server AND mu_id = :id');
		$stmt->execute([
				':account' => $account,
				':server' => $server,
				':id' => $id		
		]);
		return $stmt->fetch_all();
	}
	
	/**
     * Check if account is connected to game
     *
     * @param string $account
	 * @param string $server
     *
     * @return bool
     */
	
	public function check_connect_stat($account, $server){
		$stmt = $this->website->db('account', $server)->prepare('SELECT ConnectStat FROM MEMB_STAT WHERE memb___id = :user');
		$stmt->execute(array(':user' => $account));
		if ($status = $stmt->fetch()) {
			return ($status['ConnectStat'] == 0);
		}
		return true;
    }
	
	private function is_hex($hex_code) {
		return @preg_match("/^[a-f0-9]{2,}$/i", $hex_code) && !(strlen($hex_code) & 1);
	}
	
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
