<?php
    in_file();

    class Mguides extends model
    {
        protected $guides = [];

        public function __contruct(){
            parent::__construct();
        }
		
		public function load_guides($search = ''){
            $guides = $this->website->db('web')->query('SELECT id, title, text FROM DmN_Guides WHERE lang = '.$this->website->db('web')->escape($this->config->language()).' ORDER BY date DESC')->fetch_all();
			$newGuides = [];
			$sGuides = [];
			foreach($guides AS $guide){
				if($search != '' && mb_strpos(hex2bin($guide['title']), $search) !== false){
					$sGuides[] = [
						'id' => $guide['id'],
						'title' => (ctype_xdigit($guide['title'])) ? hex2bin($guide['title']) : $guide['title'],
						'text' => (ctype_xdigit($guide['text'])) ? hex2bin($guide['text']) : $guide['text'],
					];
				}
				$newGuides[] = [
					'id' => $guide['id'],
					'title' => (ctype_xdigit($guide['title'])) ? hex2bin($guide['title']) : $guide['title'],
					'text' => (ctype_xdigit($guide['text'])) ? hex2bin($guide['text']) : $guide['text'],
				];
			}
			return ($search != '') ? $sGuides : $newGuides;
        }
		
		
		public function load_guides_by_category($id){
            $guides = $this->website->db('web')->query('SELECT id, title, text FROM DmN_Guides WHERE lang = '.$this->website->db('web')->escape($this->config->language()).' AND category = ' . $this->website->db('web')->escape($id) . ' ORDER BY date DESC')->fetch_all();
			$newGuides = [];
			foreach($guides AS $guide){
				$newGuides[] = [
					'id' => $guide['id'],
					'title' => (ctype_xdigit($guide['title'])) ? hex2bin($guide['title']) : $guide['title'],
					'text' => (ctype_xdigit($guide['text'])) ? hex2bin($guide['text']) : $guide['text'],
				];
			}
			return $newGuides;
        }
		
		public function load_guide_by_id($id){
            $stmt = $this->website->db('web')->prepare('SELECT id, title, text, date, category FROM DmN_Guides WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch();
			if($data != false){
				$newData = [
					'id' => $data['id'],
					'title' => (ctype_xdigit($data['title'])) ? hex2bin($data['title']) : $data['title'],
					'text' => (ctype_xdigit($data['text'])) ? hex2bin($data['text']) : $data['text'],
					'date' => $data['date'],
					'category' => $data['category'],
				];
				return $newData;
			}
			return $data;
        }
    }