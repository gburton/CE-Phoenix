<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: loads events from db, limits data fetched so it will fetch only needed
  
*/

chdir('../../../../');
require('includes/application_top.php');
//echo getcwd();

if(!isset($_SERVER['HTTP_X_AAS'])) die;
if(isset($sessionTimeout)){

	$dataType = (isset($_POST['dataType']) ? $_POST['dataType'] : 'html');
	if($dataType=='json') echo json_encode(array('response'=>'aasSessionTimeout'));
	else echo'aasSessionTimeout';
	die;
	
}
if($_SERVER['HTTP_X_AAS']!==$_SESSION['admin']['AAS']['ajaxToken']) die;
defined('AAS') or define('AAS', 1);

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language.'.php'; else include 'ext/aas/languages/english.php';
if(isset($sessionTimeout)){

	$dataType = (isset($_POST['dataType']) ? $_POST['dataType'] : 'html');
	if($dataType=='json') json_encode('aasSessionTimeout');
	else echo'aasSessionTimeout';
	die;
	
}

$item=(isset($_POST['item']) ? $_POST['item'] : '');

switch($item){
	case'updateCalendarStartEnd':
	
		$event_id = (isset($_POST['event_id']) ? $_POST['event_id'] : '');
		$start = (isset($_POST['start']) ? $_POST['start'] : '');
		$end = (isset($_POST['end']) ? $_POST['end'] : '');
		$allDay = (isset($_POST['allDay']) ? $_POST['allDay'] : '');

		if(tep_not_null($event_id) ){
		
			if(tep_not_null($allDay)) $allDay=$allDay=='true'?"allDay=1,":"allDay=0,";
			else $allDay='';
	
			$end_set=tep_not_null($end) ? "end='".tep_db_prepare_input($end)."'," : '';
			if(tep_db_query("UPDATE aas_calendar SET start='".tep_db_prepare_input($start)."', ".$end_set." ".$allDay." date='".time()."' WHERE id=".(int)$event_id." ")) echo '1'; else echo '0';
		
		}else echo '0';	
		
	break;

	case'updateCalendarEventNotes':
	
		$event_id = (isset($_POST['event_id']) ? $_POST['event_id'] : '');
		$title = (isset($_POST['title']) ? $_POST['title'] : NULL);
		$notes = (isset($_POST['notes']) ? $_POST['notes'] : NULL);
		if(tep_not_null($event_id) && tep_not_null($title)){
		
			if(tep_db_query("UPDATE aas_calendar SET title='".tep_db_prepare_input($title)."', notes='".tep_db_prepare_input($notes)."' WHERE id=".(int)$event_id."")) echo '1';
			else echo '0';
		}else echo '0';
		
	break;

	case'getCalendarEventNotes':
	
		$event_id = (isset($_POST['event_id']) ? $_POST['event_id'] : '');
		if(tep_not_null($event_id)){
					
			$query=tep_db_query("SELECT notes FROM aas_calendar WHERE id='".tep_db_prepare_input($event_id)."' LIMIT 1");
			
			if($query){
				if(tep_db_num_rows($query)==1){
			
					$row=tep_db_fetch_array($query);	
					echo $row['notes'];
				
				}else echo '0';
			
			}else echo '0';
		
		}else echo '0';
	
	break;


	case'deleteCalendarEvent':
	
		$event_id = (isset($_POST['event_id']) ? $_POST['event_id'] : '');
				
		if(tep_not_null($event_id)){
					
			if(tep_db_query("DELETE FROM aas_calendar  WHERE id='".tep_db_prepare_input($event_id)."' ")) echo '1'; else echo '0';
		
		}else echo '0';	
	
	break;
	case'addCalendarEvent':
	
		$title = (isset($_POST['title']) ? $_POST['title'] : '');
		$start = (isset($_POST['start']) ? $_POST['start'] : '');
		$end = (isset($_POST['end']) ? $_POST['end'] : '');
		$allDay = (isset($_POST['allDay']) ? $_POST['allDay'] : '');
		$notes = (isset($_POST['notes']) ? $_POST['notes'] : NULL);

		if(tep_not_null($title) && tep_not_null($start) && tep_not_null($end) && tep_not_null($allDay)){
		
			$ad=$allDay=='true'?1:0;
			
			if(tep_db_query("INSERT INTO aas_calendar ( title,notes,start, end, allDay, date ) VALUES ('".tep_db_prepare_input($title)."','".tep_db_prepare_input($notes)."','".tep_db_prepare_input($start)."','".tep_db_prepare_input($end)."','".tep_db_prepare_input($ad)."','".time()."') ")) echo json_encode(array('id'=>tep_db_insert_id(),'start'=>date('Y-m-d',$start),'end'=>date('Y-m-d',$end))); else echo json_encode(array('id'=>0));
		
		}else echo json_encode(array('id'=>0));
	
	break; 
  default:

    $start = (isset($_POST['start']) ? $_POST['start'] : '');
    $end = (isset($_POST['end']) ? $_POST['end'] : '');

    if(tep_not_null($start) && tep_not_null($end)){

	    $query=tep_db_query("SELECT id,title,start,end,allDay FROM aas_calendar WHERE start>='".$start."' AND end<='".$end."'  ");
	    if(tep_db_num_rows($query)>0){
		    $data=array();
		    while($row=tep_db_fetch_array($query)){
		
			    $data[]=array('id' => $row['id'],
			    'title' => $row['title'],
			    'start' => $row['allDay']=='1'?date('Y-m-d',$row['start']):date('Y-m-d H:i:s',$row['start']),
			    'end' => $row['allDay']=='1'?date('Y-m-d',$row['end']):date('Y-m-d H:i:s',$row['end']),
			    //'start' => $row['start'],
			    //'end' => $row['end'],
			    
			    'allDay' => $row['allDay']=='1'?true:false,
			    );
		
		    }
		
		    echo json_encode($data);
		    	
	    }else echo json_encode(array('id'=>0));
	    
    }

}


?>
