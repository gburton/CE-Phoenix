<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/
defined('AAS') or die;

class ajaxPagination
{
	var $perPage = 10;
	var $showFirstAndLast = true;
	var $firstString='«« First';
	var $lastString='Last »»';
	var $nextString='Next »';
	var $prevString='« Prev';
	var $jumpString='...';
	var $jumpToPageString='jump to page';
	var $queryStringArray = array();
	var $queryString='';
	var $currentPage=1;
	var $languageId=1;
	var $totalPages;
	var $totalRows=0;
	var $action='';
	var $drawSelect=false;
	var $drawPaginationIfSinglePage=false;
	var $file='';
 
	private static $instance;
 
  	public function __construct( $settings = '' ) {
		
		if (is_array($settings)){
			
			$this->showFirstAndLast	= (isset($settings['showFirstAndLast']))?$settings['showFirstAndLast']:$this->showFirstAndLast; 
			$this->firstString = (!empty($settings['firstString']))?$settings['firstString']:$this->firstString;
			$this->lastString = (!empty($settings['lastString']))?$settings['lastString']:$this->lastString;
			$this->nextString = (!empty($settings['nextString']))?$settings['nextString']:$this->nextString;
			$this->prevString = (!empty($settings['prevString']))?$settings['prevString']:$this->prevString;
			$this->jumpString = (!empty($settings['jumpString']))?$settings['jumpString']:$this->jumpString;
			$this->jumpToPageString = (!empty($settings['jumpToPageString']))?$settings['jumpToPageString']:$this->jumpToPageString;
			$this->queryString = (isset($settings['queryString']))?$settings['queryString']:$this->queryString;
			$this->perPage = (isset($settings['entriesPerPage']))?$settings['entriesPerPage']:$this->perPage;
			$this->currentPage = (isset($settings['currentPage']))?($settings['currentPage']-1):$this->currentPage;
			$this->languageId = (isset($settings['languageId']))?$settings['languageId']:$this->languageId;
			$this->totalRows = (isset($settings['totalRows']))?$settings['totalRows']:$this->totalRows;
			$this->totalPages = $this->totalPages($this->totalRows);
			$this->drawSelect = (isset($settings['drawSelect']))?$settings['drawSelect']:$this->drawSelect;
			$this->drawPaginationIfSinglePage = (isset($settings['drawPaginationIfSinglePage']))?$settings['drawPaginationIfSinglePage']:$this->drawPaginationIfSinglePage;
			$this->file = (isset($settings['file']))?$settings['file']:$this->file;
			
		  }
	}
 
	public static function getInstance(){
		if (!isset(self::$instance))
		{
		    $object = __CLASS__;
		    self::$instance = new $object;
		}
		return self::$instance;
	}
	
	public function totalPages($rows){
	
		$numOfPages = $this->perPage>0 ? $rows / $this->perPage : 0;

		if(is_float($numOfPages))$numOfPages=(int)$numOfPages+1 ;
		
		$this->totalPages =$numOfPages;
		
		return $numOfPages;
	
	}
	
	public function get_leftLimit($page){
	
		return $page*$this->perPage;
	}

	public function draw(){
		
		if($this->drawPaginationIfSinglePage==false){ 
			if($this->totalPages<=1 ) return false;	
		}
	
		$queryString= $this->queryString;

		echo'<ul class="ajaxPaginationButtons headerButtons">';
		
			if($this->currentPage+1>1 && $this->showFirstAndLast) echo '<li><a data-page="1" href="#"><span style="color:#800;">'.$this->firstString.'</span></a></li>';
			if($this->currentPage+1!=1) echo '<li><a data-page="'.($this->currentPage).'" href="#">'.$this->prevString.'</a></li>';
			
			$jump=false;
			
			$m = $this->currentPage<1 ? 0 : 2 ;
			if($this->currentPage==1) $m=1;
			for($i=$this->currentPage-$m;$i<$this->totalPages;$i++){
			
				if($i>$this->currentPage+2 && $i<$this->totalPages-2 ) { if($jump==false) echo '<li style="color:black;">'.$this->jumpString.'</li>'; $jump=true; continue;}
			
				echo '<li '.($i==$this->currentPage ? 'class="activePaginationLi"' : '').'><a data-page="'.($i+1).'" href="#">'.($i+1).'</a></li>';
			
			}
			if($this->currentPage+1!=$this->totalPages) echo '<li><a data-page="'.($this->currentPage+2).'" href="#">'.$this->nextString.'</a></li>';
			if($this->currentPage+1!=$this->totalPages  && $this->showFirstAndLast) echo '<li><a data-page="'.$this->totalPages.'" href="#"><span style="color:#800;">'.$this->lastString.'</span></a></li>';

		echo '</ul>';
	
	}
	
}
