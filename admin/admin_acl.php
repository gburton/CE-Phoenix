<?php
/*
 * admin_acl.php
 * April 9, 2020
 * Author: G. Miaoudakis
 * Based on : admin_acl.php by Kristen G. Thorson
 * Admin access control list version 1.0
 *
 *
 * Released under the GNU General Public License
 *	
 */

  /**********************************************/

	require( 'includes/application_top.php' );
	$secMessageStack = new messageStack();
	
	$error = $_GET['error'] ?? '';
	if( isset($error) && tep_not_null( $error ) ) {
		$messageStack->add( $error, 'error' );
	}
	
	$message = $_GET['message'] ?? '';
	if( isset($message) && tep_not_null( $message ) ) {
		$messageStack->add( $message, 'success' );
	}
	
	//$action = ( isset( $_POST['action'] ) ? $_POST['action'] : '' );
	$action = $_POST['action'] ?? '';
	
	if( isset( $_GET['aID'] ) && $_GET['aID'] != '' )
		$aID = tep_db_input( $_GET['aID'] );
	else
		tep_redirect( tep_href_link( 'admin_acl.php', 'error='.ERROR_ADMIN_ACL_SAVE ) );
	
		//if( !( $admin_acl = new admin_acl( $aID ) ) ) tep_redirect( tep_href_link( 'admin_acl.php', 'aID='.$aID.'&error='.ERROR_ADMIN_ACL_INVALID ) );
		if( !$aID ) tep_redirect( tep_href_link( 'admin_acl.php', 'aID='.$aID.'&error='.ERROR_ADMIN_ACL_INVALID ) );

	if( tep_not_null( $action ) )
	{
		switch( $action ) {
			case 'Save':
				//print_r( '<pre>'.print_r( $exclusion, true ).'</pre>' );
				//print_r( '<pre>'.print_r( $_POST['selected_options'], true ).'</pre>' );exit;				
			    tep_db_query( $sql = 'DELETE FROM administrators_acl WHERE aID="'.$aID.'"' );
			    $selected_options = $_POST['selected_options']; 
			    if( is_array( $selected_options ) && count( $selected_options ) > 0 ) {
			        foreach( $selected_options as $ids ) {
			            $pieces = explode(",", $ids);
			            tep_db_query( $sql = 'INSERT INTO administrators_acl VALUES ( "'.$aID.'", "'.$pieces[0].'", "'.$pieces[1].'", "'.$pieces[2].'" )' );
			        }
			    }			    
				//$admin_acl->save( $_POST['selected_options'] );
				tep_redirect( tep_href_link( 'admin_acl.php', 'aID='.$aID.'&message='.MESSAGE_ADMIN_ACL_SAVED ) );
				break;
			case 'Cancel':
				break;
		}
		tep_redirect( tep_href_link( 'admin_acl.php', 'aID='.$aID ) );
	}
	else
	{

		//	get_selected_options
		$selected_options = '';
		$selected_ids = array();
		$sql_selected = 'SELECT * FROM administrators_acl WHERE aID="'.$aID.'" group by menu_heading,page_name ';
 		$result = tep_db_query( $sql_selected );
		$selected_urls = array();
  		if( tep_db_num_rows( $result ) > 0 )
		{
			$menu_heading = '';
			$h = 0;
			while( $row = tep_db_fetch_array( $result ) )
			{
				if($menu_heading != $row['menu_heading'])
				{
					$menu_heading = $row['menu_heading'];
					if($h) $selected_options .= '</optgroup>';
					$selected_options .= '<optgroup label="'.$row['menu_heading'].'">';
				}
					
				$selected_ids[] = $row['blocked_url'];
				$selected_options .= '<option value="'.$row['menu_heading'].','.$row['page_name'].','.$row['blocked_url'].'">'.$row['page_name'].'</option>';
			}
			if($h) $selected_options .= '</optgroup>';
		}
		
			
		
		//	get_all_options
		$cl_box_groups = array();
		$menu_options = '';

		require( 'includes/template_top.php');
		
		foreach ($cl_box_groups as $groups)
		{
		    $title = explode('</i>',$groups['heading']);
		    $groups['heading'] = $title[1];		  
		    $menu_options .= ' <optgroup label="'.$groups['heading'].'">';
			foreach ($groups['apps'] as $app) {
				if(!(in_array($app['link'], $selected_ids)) )
				    $menu_options .= '<option value="'.$groups['heading'].','.$app['title'].','.$app['link'].'">'.$app['title'].'</option>';
				    
			}
			
			$menu_options .= '</optgroup>';
		}
			
	}
	
	$user = tep_db_fetch_array(tep_db_query( $sql = 'SELECT user_name FROM administrators WHERE id='.$aID ));
	
	
?>
  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo sprintf( HEADING_TITLE, $user['user_name'] ); ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php echo tep_draw_button('Back', 'document', tep_href_link('administrators.php','aID='.$aID)); ?>
    </div>
  </div>
    
  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table ">          
    	 <tbody>
    	 <tr>
          	<td colspan="2">
			<?php
				echo tep_draw_form( 'choose'.$admin['username'], 'admin_acl.php', 'aID='.$aID, 'post', 'onsubmit="form_submission( document.getElementById(\'selected_optons\') )"' ).'
				<table class="table">
					<tr>
						<td align="center" class="main">'.HEADING_AVAILABLE.'</td>
						<td align="center">&nbsp;</td>
						<td align="center" class="main">'.HEADING_SELECTED.'</td>
					</tr>
					<tr>
						<td rowspan="5" align="center">
							<select name="available_options[]" size="20" multiple="" style="width: 300px" id="available_optons">
							'.$menu_options.'
							</select>
						</td>
						<td align="center"><input name="choose_all" type="button" id="choose_all" value="Choose All &gt;" onclick="selectAll( document.getElementById(\'selected_optons\'), document.getElementById(\'available_optons\') )"></td>
						<td rowspan="5" align="center">
							<select name="selected_options[]" size="20" multiple="" style="width: 300px" id="selected_optons">
							'.$selected_options.'
							</select>
						</td>
					</tr>
					<tr>
						<td align="center"><input name="add" type="button" id="add" value="&gt; &gt;" onclick="updateSelect( document.getElementById(\'selected_optons\'), document.getElementById(\'available_optons\') )"></td>
					</tr>
					<tr>
						<td align="center"><input name="subtract" type="button" id="subtract" value="&lt; &lt;" onclick="updateSelect( document.getElementById(\'available_optons\'), document.getElementById(\'selected_optons\') )"></td>
					</tr>
					<tr>
						<td align="center"><input name="remove_all" type="button" id="remove_all" value="&lt; Remove All" onclick="selectAll( document.getElementById(\'available_optons\'), document.getElementById(\'selected_optons\') )"></td>
					</tr>
					<tr>
						<td align="center"><input name="action" type="submit" id="action" value="Save"> <input name="action" type="submit" id="action" value="Cancel"></td>
					</tr>
				</table>
				</form>';
			?>
			</td>
          </tr>
       </tbody>
        </table>
      </div>

      <?php
        echo $secMessageStack->output();
      ?>

    </div>  
	
<script language="javascript" type="text/javascript"><!--

	function updateSelect( to_select, from_select ) {
		 for( var i = 0; i < from_select.options.length; i++ ) {
			  if( from_select.options[i].selected ) {
				var newOption = new Option( from_select.options[i].text, from_select.options[i].value )
				to_select.options[ to_select.options.length ] = newOption;
			  }
		 }
		 deleteOptions( from_select );
	}

	function deleteOptions( delete_select ) {
	  for( var i = 0; i < delete_select.options.length; i++ ) {
		if( delete_select.options[i].selected ) {
		  delete_select.options[i] = null;
		  i=-1;
		}
	  }
	}

	function selectAll( to_select, from_select ) {
		for( var i=0; i < from_select.options.length; i++ ) {
		  from_select.options[i].selected = true;
		}
		updateSelect( to_select, from_select );
	}

	function form_submission( to_select ) {
	  for( var i=0; i < to_select.options.length; i++ ) {
		  to_select.options[i].selected = true;
		}
	}

//--></script>

<?php 
require( 'includes/template_bottom.php');
require( 'includes/application_bottom.php'); 
?>
