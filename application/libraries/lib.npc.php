<?php
    in_file();

    class npc extends library
    {
        private $npc_list;
        private $name = 'Unknown';
        private $id = -1;

        public function __construct()
        {
            $this->load->lib('serverfile');
            $this->npc_list = $this->serverfile->monster_list()->get('mlist');
        }
		
		public function get_list(){
			return $this->npc_list;
		}

        public function name_by_id($id)
        {
            foreach($this->npc_list AS $key => $npc){
                if($key == (int)$id){
					$this->name = $npc['name'];
					break;
				}
            }
            return $this->name;
        }

        public function id_by_name($name)
        {
            foreach($this->npc_list AS $key => $npc){
                if($npc['name'] === $name){
					$this->id = $key;
					break;
				}
            }
            return $this->id;
        }
    }
	