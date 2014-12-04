/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: time up down functions
  
*/
function date_time(id){

        date = new Date;
        year = date.getFullYear();
        month = date.getMonth();
        months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'Jully', 'August', 'September', 'October', 'November', 'December');
        d = date.getDate();
        day = date.getDay();
        days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        h = date.getHours();
        if(h<10) h = "0"+h;
        
        m = date.getMinutes();
        if(m<10) m = "0"+m;
        
        s = date.getSeconds();
        if(s<10) s = "0"+s;
        
        result = ''+days[day]+' '+months[month]+' '+d+' '+year+' '+h+':'+m+':'+s;
        document.getElementById(id).innerHTML = result;
        setTimeout('date_time("'+id+'");','1000');
        return true;
}


function timerUp(start_time,server_time,update){

	var stepper=1;
	var interval = setInterval(function(){
		update(aas.format.seconds((server_time*1000)-(start_time*1000)+(stepper++)*100));
	},100);
    
}

function timerDown(start_time,update,complete){

	var stepper=1;
	var csecs=0;
	var interval = setInterval(function(){
	
		csecs = (start_time*1000)-(stepper++)*100;
		
		if(csecs<=0){ complete(); clearInterval(interval); }
		
		update(aas.format.seconds(csecs));
	
	},100);
    
}
