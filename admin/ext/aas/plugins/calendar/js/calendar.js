/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/
var calendar=null;
$(function(){


	$( "#dialog-calendar" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-calendar-apply",
			    text: translate.button.apply,
			    click: function(){
			    	var dat=$(this).data('data');
			    	
			    	var input_dat=$('#calendar-title-field').val();
			    	var notes_dat=$('#calendar-notes-field').val();
			    	if(input_dat=='') aas.dialog.open('dialog-error',translate.AAS_CALENDAR_ENTER_EVENT_TITLE);
			    	else{
			    	
			    		if(dat.action=='new') addToCalendar(input_dat,notes_dat,dat.start,dat.end,dat.allDay);
			    		if(dat.action=='edit'){
				    		dat.event.title=input_dat;
				    		updateCalendarEvent(dat.event,notes_dat);
			    		}
			    	
			    	
			    	}
			    
			    }
			},
			
			{
			    id: "btn-dialog-calendar-delete",
			    text: translate.button.delete,
			    click: function(){
			    	var dat=$(this).data('data');
			    	
			    	deleteCalendarEvent(dat.event);
			    	$( this ).dialog( "close" );
			    
			    }
			},
			{
			    id: "btn-dialog-calendar-cancel",
			    text: translate.button.cancel,
			    click: function(){$( this ).dialog( "close" );}
			}
			],
		 open: function(){
		 
			 var dat=$(this).data('data');
			 
			 if(dat.action=='edit'){
			 
			 	$('#btn-dialog-calendar-delete').button("enable");
			 
				 $('#calendar-title-field').val(dat.event.title);
				 aas.ajax.do({data:{item:'getCalendarEventNotes',event_id:dat.event.id},url:config.url.actions},function(msg){
				 
				 	if(msg!='0'){			 	
				 		$('#calendar-notes-field').val(msg);
				 	}
				 
				 });
			 
			 }else{
			 
			 	$('#btn-dialog-calendar-delete').button("disable");
			 	$('#calendar-title-field').val('');
			 	$('#calendar-notes-field').val('');
			 
			 }
		 
			//overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
			//$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){//$('.ui-widget-overlay').css('background', overlayBackgroundNormal);
		},
		close: function(){}

	});

	$('#calendar .close-button').on('click',function(){
	
		$('#calendar').fadeOut('fast',function(){
		
		  toggleBodyVerticalScrollbar(1);
		
		});

	});
	
	$('#calendarbutton').on('click touchend',function(){
	
		if(calendar!=null){
		
			$('#calendar').fadeIn(function(){
			
			  toggleBodyVerticalScrollbar(0);
			
			});
		
		}else{
	
		$('#calendar').fadeIn(function(){
		
		    toggleBodyVerticalScrollbar(0);
		
				calendar = $('#calendar-elem').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				weekends:true,
				selectable: true,
				selectHelper: true,
				//aspectRatio: parseInt($(window).width()/$(window).height()),

				select: function(start, end) {

          var allDay=(start.hasTime()) ? false : true;
          			
          aas.dialog.open('dialog-calendar',translate.AAS_CALENDAR_NEW_EVENT,{action:'new',start: start.unix(),end: end.unix(),allDay: allDay});      
			
				},
				eventDrop: function(event, delta, revertFunc) {
				
					var allDay=(event.start.hasTime()) ? 'false' : 'true';
			
					aas.ajax.do({data:{item:'updateCalendarStartEnd',event_id:event.id,start:event.start.unix(),end:event.end.unix(),allDay:allDay},url:'ext/aas/plugins/calendar/aas.php'},function(msg){
			
						if(msg=='1'){
			
						}else{ aas.dialog.open('dialog-error',translate.AAS_CALENDAR_EVENT_NOT_UPDATED); revertFunc(); }
			
					},function(){
			
						revertFunc();
			
					});
			
				},
				eventResize: function(event,dayDelta,revertFunc) {
		
					aas.ajax.do({data:{item:'updateCalendarStartEnd',event_id:event.id,start: event.start.unix(),end: event.end.unix()},url:'ext/aas/plugins/calendar/aas.php'},function(msg){
			
						if(msg=='1'){
			
						}else{
				
							revertFunc();
							aas.dialog.open('dialog-error',translate.AAS_CALENDAR_EVENT_NOT_UPDATED);
				
						}
			
					},function(){
			
						revertFunc();
			
					});
			
				    },
				 eventClick: function(event, element) {
			
					aas.dialog.open('dialog-calendar',translate.AAS_CALENDAR_EDIT_EVENT,{action:'edit',event: event});

				 },
				editable: true,
				//events: "ext/aas/plugins/calendar/aas.php",
				
				events: function(start, end, timezone, callback) {
				  aas.dialog.open('dialog-processing');
				  aas.ajax.do({
				  
				        url: 'ext/aas/plugins/calendar/aas.php',
                dataType: 'json',
                data: {
                    start: start.unix(),
                    end: end.unix()
                },
				  
				  },function(obj){
  	  			  aas.dialog.close('dialog-processing');
              
              for(var i=0,n=obj.length; i<n;i++){
              
                  if(obj[i].id==0) break;

              		calendar.fullCalendar('renderEvent',
				            {
					            id: obj[i].id,
					            title: obj[i].title,
					            start: obj[i].start,
					            end: obj[i].end,
					            allDay: obj[i].allDay,
					            textColor:'#444',
					            backgroundColor:'#B7FC3F',
					            borderColor:'#999'
				            },
				            false
			            );
			       
			       }
	  
				  },function(fail){
				  
				  });

        },
				
	      height:$(window).height()-95,
				windowResize: function(view) {
					//calendar.fullCalendar('option', 'aspectRatio', parseInt($(document).width()/$(document).height()));
					calendar.fullCalendar('option', 'height', $(window).height()-95);
				//	calendar.fullCalendar('option', 'contentHeight', $(window).height()-95);
				}
				 
			});
		
		});//calendar fadein
		
	}//else calendar != null

	});
	
});
function deleteCalendarEvent(event){

	aas.dialog.open('dialog-processing');
	aas.ajax.do({data:{item:'deleteCalendarEvent',event_id:event.id},url:'ext/aas/plugins/calendar/aas.php'},function(msg){
	  aas.dialog.close('dialog-processing');
	
		if(msg=='1'){
		
			calendar.fullCalendar('removeEvents', event.id);
			aas.dialog.open('dialog-success',translate.AAS_CALENDAR_SUCCESSFULLY_DELETED_EVENT);
		
		}
		else aas.dialog.open('dialog-error',translate.AAS_CALENDAR_EVENT_NOT_DELETED);
	
	});
	
}	
function updateCalendarEvent(event,notes){

	aas.dialog.open('dialog-processing');
	aas.ajax.do({data:{item:'updateCalendarEventNotes',title:event.title,notes:notes,event_id:event.id},url:'ext/aas/plugins/calendar/aas.php'},function(msg){
  	aas.dialog.close('dialog-processing');
	
		if(msg=='1'){
			
			calendar.fullCalendar('updateEvent', event);
			aas.dialog.open('dialog-success',translate.AAS_CALENDAR_SUCCESSFULLY_UPDATED_EVENT); 
			
		}else aas.dialog.open('dialog-error',translate.AAS_CALENDAR_EVENT_NOT_UPDATED);
	
	});

}
function addToCalendar(title,notes,start,end,allDay){

	aas.dialog.open('dialog-processing');
	aas.ajax.do({data:{item:'addCalendarEvent',title:title,notes:notes,start:start,end:end,allDay:allDay},dataType: 'json',url:'ext/aas/plugins/calendar/aas.php'},function(msg){
	  aas.dialog.close('dialog-processing');
		
		if(msg.id!='0' && parseInt(msg.id)>0){
		
			calendar.fullCalendar('renderEvent',
				{
					id: msg.id,
					title: title,
					start: msg.start,
					end: msg.end,
					allDay: allDay,
				  textColor:'#444',
          backgroundColor:'#B7FC3F',
          borderColor:'#999'
				},
				false
			);
		
			calendar.fullCalendar('unselect');
			
			aas.dialog.close('dialog-calendar');
			$('#calendar-title-field').val('');

		}else{
		
			aas.dialog.open('dialog-error',translate.AAS_CALENDAR_EVENT_NOT_ADDED);
		
		}
	
	});

}
