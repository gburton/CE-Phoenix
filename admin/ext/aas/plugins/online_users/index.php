<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  Information: Wrapper - functions created by John Barounis, charts engine created by http://www.highcharts.com/, license: Free - Non-commercial

*/

defined('AAS') or die;
?>
<div class="overlay" id="online_users">
  <div class="container">
    <div class="close"><a href="#"><img style="opacity:0.3" src="ext/aas/images/remove_white_no_round_1.png" alt="Close"></a></div>
    <div class="fullscreenbutton"><a href="#" onclick="aas.fullscreen.toggle('online_users')"><img src="ext/aas/images/glyphicons_349_fullscreen.png" alt="FullScreen"></a></div>
    <div id="online_users_container"></div>
    <div id="currentUsers">0</div>
    <div  class="panel-wrapper" id="peakpanel">
      <div class="peakpanel-text-1"><?php echo AAS_TEXT_OU_MOST_USERS_ONLINE; ?></div>
      <div id="peakpanel-number"></div>
      <div  class="peakpanel-text-2"><?php echo AAS_TEXT_OU_AT; ?></div>
      <div id="peakpanel-date"></div>
    </div>
    <div class="panel-wrapper" id="countries-wrapper">
      <div id="online_users_countries"></div>
    </div>
  </div>
</div>
<script type="text/javascript" src="ext/aas/js/highcharts/highcharts.js"></script>
<script type="text/javascript">

//Add to main aas object
aas.online_users={

  loaded:false,
  chart:null,
  renderTo:'',
  title:'',
  subtitle:'',
  yAxis_text:'',

  users_number_elem:null,
  users_number_peak_elem:null,
  users_number_peak_date_elem:null,

  temp:0,
  tempCountries:0,
  peak:0,
  requestData_url:'',
  requestDataTimeout:null,

  interval:3000,

  countries:<?php echo $defaults['onlineUsers_displayCountriesFrom'] ? 'true' : 'false'; ?>,
  //requestCountryData is called by requestData online when number has changed
  //so call requestCountryData every 30 secs in case we have same number but different countries
  //set false if no need to use an auto update
  //better leave false because it will avoid to many calls to server
  countries_daemon:false,
  countries_interval:30000,
  countries_timeout:null,
  countries_online_users_elem:null,
  countries_requestData_url:'',

  init:function(){

    this.loaded=true;
    var jthis=this;

    this.chart = new Highcharts.Chart({
      chart: {
        renderTo: jthis.renderTo,
        defaultSeriesType: 'spline',
        events: {
          load:  function(event){
            jthis.requestData();
          }
        }
      },
      legend: {enabled: false},
      credits: {enabled: false},
      title: {
        text: jthis.title
      },
       subtitle: {
        text: jthis.subtitle
      },
      xAxis: {
        type: 'datetime',
        tickPixelInterval: 150,
        maxZoom: 20 * 1000
      },
      yAxis: {
        minPadding: 0.2,
        maxPadding: 0.2,
        title: {
          text: jthis.yAxis_text,
          margin: 10
        }
      },
      series: [{
        data: []
      }]

    });

  },
  requestData:function(){

    var jthis=this;

    aas.ajax.do({
    url: jthis.requestData_url,
    crossDomain:true,
    dataType: 'json',
    cache: false
    },function(point){

      var series = jthis.chart.series[0],
      shift = series.data.length > 20; // shift if the series is longer than 20

      if(point[1]!=jthis.temp){
        jthis.users_number_elem.fadeOut(1000,function(){
          $(this).html(point[1]).fadeIn(1000);
        });
        jthis.temp=point[1];

        if(jthis.peak<jthis.temp){
          jthis.peak=jthis.temp;

          jthis.users_number_peak_elem.text(jthis.peak);

          //var date = new Date(point[0]);
          //jthis.users_number_peak_date_elem.text(date.getDate()+'-'+(date.getMonth()+1)+'-'+date.getFullYear()+', '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds());
          //var loc=config.language_code=='gr' ? 'el' : config.language_code;
          //moment.locale(loc);
          jthis.users_number_peak_date_elem.text(moment(point[0]).format("lll"));

        }

        if(jthis.countries) jthis.requestCountryData();

      }

      jthis.chart.series[0].addPoint(point, true, shift);

      jthis.start();

    });

  },
  start:function(){

    var jthis=this;
    this.requestDataTimeout=setTimeout(function(){jthis.requestData()}, this.interval);

  },
  stop:function(){

    clearTimeout(this.requestDataTimeout);
    if(this.countries_daemon){ clearTimeout(this.countries_timeout); }

  },
  requestCountryData:function(){

    var jthis=this;

    aas.ajax.do({ type: 'GET',
      url: jthis.countries_requestData_url,
      dataType: 'json',
      cache: false
      },function(resp){

      var tempC='';
      for(var i in resp) tempC+= i+'&nbsp;( <span class="country_number">'+resp[i]+'</span> )<br>';

      if(tempC!=jthis.tempCountries){

        jthis.countries_online_users_elem.fadeOut(1000,function(){ $(this).html(tempC).fadeIn(1000);});
        jthis.tempCountries=tempC;

      }

      if(jthis.countries_daemon) jthis.countries_timeout=setTimeout(function(){jthis.requestCountryData()}, jthis.countries_interval);

    });

  }

};
$(function(){

  $('#onlineUsersbutton').on('click touchend',function(){

    if(aas.online_users.loaded) aas.online_users.start();
    else{

      aas.online_users.users_number_elem=$('#currentUsers');
      aas.online_users.users_number_peak_elem=$('#peakpanel-number');
      aas.online_users.users_number_peak_date_elem=$('#peakpanel-date');
      aas.online_users.countries_online_users_elem=$('#online_users_countries');
      aas.online_users.title="<strong><?php echo STORE_NAME; ?></strong>";
      aas.online_users.subtitle="<?php echo AAS_TEXT_OU_SUBTITLE; ?>";
      aas.online_users.renderTo='online_users_container';
      aas.online_users.yAxis_text="<?php echo AAS_TEXT_OU_ONLINE_USERS; ?>";
      aas.online_users.requestData_url='ext/aas/plugins/online_users/aas.php';
      aas.online_users.countries_requestData_url='ext/aas/plugins/online_users/online_users_by_country/aas.php';
      aas.online_users.init();

    }
    $('#online_users').fadeIn('fast',function(){

      toggleBodyVerticalScrollbar(0);

    });

  });

  var wh=$(window).height();
  var container=$('#online_users_container');
  var currentUsers=$('#currentUsers');

  container.css({'height':wh,'width':'100%'});
  currentUsers.css({'height': wh,'font-size':wh+'px'});

  $('#online_users .close a').on('click',function(){

    aas.fullscreen.close();
    aas.online_users.stop();
    $('#online_users').fadeOut('fast',function(){

      toggleBodyVerticalScrollbar(1);

    });
    return false;

  });

  $(window).on('resize', function() {
    var wh=$(this).height();
    container.css('height', wh);
    currentUsers.css({'height': wh,'font-size':wh+'px'});
  });

  $( ".panel-wrapper" ).draggable({});

});
</script>
