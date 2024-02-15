<?php
    in_file();

    class xml extends library
    {
		private $document;
		private $filename;
		private $res;
		public $dom;
		private $lang;

        public function __construct() 
		{
			$this->set_language();
		}

        public function load($file){
			$fileFull = APP_PATH . DS . 'data'. DS . 'ServerData'. DS . $this->lang . DS . $file;
		
			if(!file_exists($fileFull)){
				$fileFull =  APP_PATH . DS . 'data'. DS . 'ServerData'. DS . 'en' . DS . $file;
			    if(!file_exists($fileFull)){
					return false;
				}
			}

			$this->dom = new \DomDocument();
			$this->dom->load($fileFull);
		}  

		private function set_language()
        {
            $this->lang = htmlspecialchars($_COOKIE['dmn_language']);
        }	
    }
	