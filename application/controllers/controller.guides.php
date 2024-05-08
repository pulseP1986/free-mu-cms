<?php
    in_file();

    class guides extends controller
    {
        protected $vars = [], $errors = [];

        public function __construct(){
            parent::__construct();
            $this->load->helper('website');
            $this->load->lib('session', ['DmNCMS']);
			$this->session->checkSession();
			$this->load->lib('csrf');
            $this->load->model('guides');
            $this->load->helper('breadcrumbs', [$this->request]);
            $this->load->helper('meta');
            $this->load->lib('fb');
        }

        public function index(){
            $this->vars['guides'] = $this->Mguides->load_guides();
            $this->load->view($this->config->config_entry('main|template') . DS . 'guides' . DS . 'view.guides', $this->vars);
        }
        
        public function search(){
            if(isset($_POST['search']) && $_POST['search'] != ''){
                $this->vars['guides'] = $this->Mguides->load_guides($_POST['search']);
            }
            else{
                $this->vars['error'] = __('Please enter search text');
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'guides' . DS . 'view.guides', $this->vars);
        }
        
        public function category($id){
            if(ctype_digit($id)){
                $this->vars['guides'] = $this->Mguides->load_guides_by_category($id);
                if(!$this->vars['guide']){
                    $this->vars['error'] = __('Guides article not found.');
                }
            } else{
                $this->vars['error'] = __('Guides article not found.');
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'guides' . DS . 'view.guides', $this->vars);
        }
        
        public function read($title, $id){
            if(ctype_digit($id)){
                $this->vars['guide'] = $this->Mguides->load_guide_by_id($id);
                
                if(!$this->vars['guide']){
                    $this->vars['guides'] = [];
                    $this->vars['error'] = __('Guides article not found.');
                }
                else{
                    if($this->vars['guide']['category'] != null){
                        $this->vars['guides'] = $this->Mguides->load_guides_by_category($this->vars['guide']['category']);
                    }
                    else{
                        $this->vars['guides'] = [];
                    }
                }
            } else{
                $this->vars['error'] = __('Guides article not found.');
            }
            $this->load->view($this->config->config_entry('main|template') . DS . 'guides' . DS . 'view.read_guide', $this->vars);
        }
    }