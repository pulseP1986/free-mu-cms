<?php

class Mstats_specialization extends model{
	private $characters = [];
	
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
        $stmt = $this->website->db('game', $server)->prepare('SELECT Name, cLevel, Class, Strength, Dexterity, Vitality, Energy, Leadership, LevelUpPoint, '.$this->website->get_char_id_col($server).' AS id FROM Character WHERE AccountId = :account');
        $stmt->execute([':account' => $account]);
		
        $i = 0;
        while($row = $stmt->fetch()){
            $this->characters[] = [
				'id' => $row['id'],
				'Name' => $row['Name'],
                'cLevel' => $row['cLevel'],
                'Class' => $row['Class'],
                'Strength' => $row['Strength'],
                'Dexterity' => $row['Dexterity'],
                'Vitality' => $row['Vitality'],
                'Energy' => $row['Energy'],
                'Leadership' => $row['Leadership'],
				'LevelUpPoint' => $row['LevelUpPoint'],
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
		$stmt = $this->website->db('game', $server)->prepare('SELECT TOP 1 Name, cLevel, Class, Strength, Dexterity, Vitality, Energy, Leadership, LevelUpPoint, '.$this->website->get_char_id_col($server).' AS id FROM Character WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :id');
		$stmt->execute([
			':account' => $account,
			':id' => $id
		]);
		return $stmt->fetch();
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
		$stmt = $this->website->db('web')->prepare('SELECT TOP 1 id, str, agi, ene, vit, com, free FROM DmN_Stats_Specialization WHERE '.$search[1].' = :title AND account = :account AND server = :server AND mu_id = :char_id');
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
		$stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Stats_Specialization WHERE id = :id AND account = :account AND server = :server AND mu_id = :char_id');
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
		$stmt = $this->website->db('web')->prepare('SELECT COUNT(id) AS count FROM DmN_Stats_Specialization WHERE account = :account AND server = :server AND mu_id = :id');
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
	
	public function save_specialization($title, $account, $server, $date){
		$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Stats_Specialization (title, account, server, name, mu_id, str, agi, ene, vit, com, free) VALUES (:title, :account, :server, :name, :mu_id, :str, :agi, :ene, :vit, :com, :free)');
		return $stmt->execute([
			':title' => $title,
			':account' => $account,
			':server' => $server,
			':name' => $date['Name'],
			':mu_id' => $date['id'],
			':str' => $date['Strength'],
			':agi' => $date['Dexterity'],
			':ene' => $date['Energy'],
			':vit' => $date['Vitality'],
			':com' => $date['Leadership'],
			':free' => $date['LevelUpPoint'],
		]);
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
		$stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET Strength = :Strength, Dexterity = :Dexterity, Vitality = :Vitality, Energy = :Energy, Leadership = :Leadership, LevelUpPoint = :LevelUpPoint WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :id');
		return $stmt->execute([
			':Strength' => $data['str'],
			':Dexterity' => $data['agi'],
			':Vitality' => $data['vit'],
			':Energy' => $data['ene'],
			':Leadership' => $data['com'],
			':LevelUpPoint' => $data['free'],
			':account' => $account,
			':id' => $id
		]);
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
		$stmt = $this->website->db('web')->prepare('UPDATE DmN_Stats_Specialization SET str = :Strength, agi = :Dexterity, vit = :Vitality, ene = :Energy, com = :Leadership, free = :LevelUpPoint WHERE account = :account AND server = :server AND mu_id = :char_id AND id = :id');
		return $stmt->execute([
			':Strength' => $data['Strength'],
			':Dexterity' => $data['Dexterity'],
			':Vitality' => $data['Vitality'],
			':Energy' => $data['Energy'],
			':Leadership' => $data['Leadership'],
			':LevelUpPoint' => $data['LevelUpPoint'],
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
		$stmt = $this->website->db('web')->prepare('SELECT id, title, str, agi, ene, vit, com, free FROM DmN_Stats_Specialization WHERE account = :account AND server = :server AND mu_id = :id');
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
		$stmt = $this->website->db('account', $server)->prepare('SELECT ConnectStat FROM MEMB_STAT WHERE memb___id = :user ' . $this->website->server_code($this->website->get_servercode($server)) . '');
		$stmt->execute(array(':user' => $account));
		if ($status = $stmt->fetch()) {
			return ($status['ConnectStat'] == 0);
		}
		return true;
    }
}
