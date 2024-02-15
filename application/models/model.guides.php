<?php
    in_file();

    class Mguides extends model
    {
        protected $guides = [];

        public function __contruct()
        {
            parent::__construct();
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function load_guides($search = '')
        {
            $guides = $this->website->db('web')->query('SELECT id, title, text FROM DmN_Guides WHERE lang = \'' . $this->website->db('web')->sanitize_var($this->config->language()) . '\' ORDER BY date DESC')->fetch_all();
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
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function load_guides_by_category($id)
        {
            $guides = $this->website->db('web')->query('SELECT id, title, text FROM DmN_Guides WHERE lang = \'' . $this->website->db('web')->sanitize_var($this->config->language()) . '\' AND category = ' . $this->website->db('web')->sanitize_var($id) . ' ORDER BY date DESC')->fetch_all();
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
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function load_guide_by_id($id)
        {
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