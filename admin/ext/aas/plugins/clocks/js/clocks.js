/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/
aas.clocks={

	renderTo:'body',

	create:function(titleText,timeOffset){

		var tmp=titleText.trim().split("%");
		var clock_id=tmp[0].trim().split(" ").join('-').toLowerCase();

			var clock_html='<div class="clock-wrapper" id="clock-'+clock_id+'"><div class="title">'+tmp[0]+' <span class="offset">'+(tmp[1]==0?'':tmp[1])+'</span></div><div class="date"></div><div class="rotatingHands"><img class="hours" src="ext/aas/plugins/clocks/images/hours.png" alt="hours" /></div><div class="rotatingHands"><img class="minutes" src="ext/aas/plugins/clocks/images/minutes.png" alt="minutes" /></div><div class="rotatingHands"><img class="seconds" src="ext/aas/plugins/clocks/images/seconds.png" alt="seconds" /></div><img src="ext/aas/plugins/clocks/images/clock.png" alt="clock" /><div class="bullet-dot"></div><div class="time"></div></div>';

			$(this.renderTo).append(clock_html);
			
			if(!timeOffset) timeOffset='local';
			
			var now = this.getNow(timeOffset);
			this.move(clock_id,timeOffset);

	},
	
	getNow:function(offset) {

	var now = new Date();

		if(offset!='local'){

			var utc = now.getTime() + ((now.getTimezoneOffset() + (parseInt(offset)*60)) * 60000) ;
		 	now=new Date(utc);

		}
    var moment_now=moment(now);
		return {
		    hours: ((now.getHours() * 5 + now.getMinutes() / 12) * 6),
		    minutes: now.getMinutes()*6,
		    seconds: now.getSeconds()*6,
		    fullDate: now.getDate() +'/'+now.getMonth()+'/'+now.getFullYear(),
 		    fullDate: moment_now.format('MMMM Do YYYY'),//now.getDate() +'/'+now.getMonth()+'/'+now.getFullYear()
		    time: moment_now.format('h:mm:ss a')//now.getHours()+':'+now.getMinutes()+':'+now.getSeconds()
		};
	
	},
	
	move:function(id,timeOffset){
		var jthis=this;
		
		setInterval(function(){
		    
			var now = jthis.getNow(timeOffset);
		        
			$("#clock-"+id+" .seconds").rotate(now.seconds);
			$("#clock-"+id+" .minutes").rotate(now.minutes);
			$("#clock-"+id+" .hours").rotate(now.hours);
			$("#clock-"+id+" .date").text(now.fullDate);
		  $("#clock-"+id+" .time").text(now.time);
		}, 1000);
	
	}

}
