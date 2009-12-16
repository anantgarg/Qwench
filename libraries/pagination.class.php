<?php

/* http://code.google.com/p/spaceshipcollaborative/wiki/PHPagination */

class Pagination{
    var $output = '';    
    var $options = array(
        'urlscheme' => '',
        'perpage' => '',
        'page' => '',
        'total' => '',
        'numlinks' => '5',
        'nexttext' => '>',
        'prevtext' => '<',
        'focusedclass' => 'selected',
        'delimiter' => '',
		'lasttext' => '>>',
		'firsttext' => '<<',
		'order' => 'votes',
		'search' => ''
    );
   
   function set($who,$what){
       $this->output = '';
       $this->options[$who] = $what;
   }
   
   function checkValues(){
       $errors = array();
       if($this->options['perpage']=='') $errors[] = 'Invalid perpage value';
       if($this->options['page']=='') $errors[] = 'Invalid page value';
       if($this->options['total']=='') $errors[] = 'Invalid total value';
       if($this->options['numlinks']=='') $errors[] = 'Invalid numlinks value';
   }
   function display($return = true){
       $this->checkValues();
       if($this->output=='') $this->generateOutput();
       if(!$return) echo $this->output;
       else return $this->output;
   }
   function generateOutput(){
       $elements = array();
       $num_pages = ceil($this->options['total']/$this->options['perpage']);
       $front_links = ceil($this->options['numlinks']/2);
       $end_links = floor($this->options['numlinks']/2);
       if($this->options['page'] > $num_pages){ $this->set('page',1); }
       
       $start_page = max(1,($this->options['page']-$front_links+1));
       $end_page = min($this->options['numlinks'] + $start_page-1,$num_pages);

       if($this->options['page'] > 1){
		   if ($num_pages > 10) {
			   $elements[] = $this->generate_link(1,$this->options['firsttext']);
		   }
		
		$elements[] = $this->generate_link($this->options['page']-1,$this->options['prevtext']);
		   
       }
       
       for($i=$start_page;$i<=$end_page;$i++){
           $elements[] = $this->generate_link($i);
       }
       
       if($this->options['page'] < $num_pages){
           $elements[] = $this->generate_link($this->options['page']+1,$this->options['nexttext']);
   		   if ($num_pages > 10) {
			   $elements[] = $this->generate_link($num_pages,$this->options['lasttext']);
		   }
       }
      
       $this->output = implode($this->options['delimiter'],$elements);
   }
   function generate_link($page,$label=''){
       $url = str_replace('%page%',$page,$this->options['urlscheme']);
       if($label=='') $label=$page;
       $html = "<a ".(($this->options['focusedclass']!='' && $page == $this->options['page'])?"class=\"{$this->options['focusedclass']}\" ":"")."href=\"{$url}\">{$label}</a>";
       return $html;
   }

    function displayOptions(){
		if ($this->options['search']!='') {
			$elements[] = $this->generate_seclink('relevance');
		}
		 $elements[] = $this->generate_seclink('votes');
		 $elements[] = $this->generate_seclink('newest');
		 $elements[] = $this->generate_seclink('oldest');
		 return implode($this->options['delimiter'],$elements);

	}


     function generate_seclink($label){
       $url = str_replace('%label%',$label,$this->options['urlscheme']);
       $html = "<a ".(($this->options['focusedclass']!='' && $label == $this->options['order'])?"class=\"{$this->options['focusedclass']}\" ":"")."href=\"{$url}\">{$label}</a>";
       return $html;
   }
}