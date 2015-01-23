osCommerce-234-bootstrap
=========================

osCommerce 2.3.4 with Bootstrap, what more needs to be said.  

The point is to try to keep changes to a minimum as this will allow easy porting of Addons.


This will be an ongoing COMMUNITY effort.  

If you cannot code, you can still help;

Check out the demo site at http://template.me.uk/2334bs3/ - have a look around and note any areas that you feel need attention, then post your feedback at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/

Are you a Coder ?
Please fork this project and start coding.  Let me know your Github Project URL by posting at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/   

Not a Coder ...
Please support this project by giving as much feedback as you possibly can.  Or by donating beer to the coders.

How to keep a clean Master copy using Github
============================================

I have put together a couple of videos.
1.  shows how to create a new Github account and Fork this project.
2.  shows how to check for new commits to this project and pull them into your own Fork.

You can find these videos at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/?p=1709648


Installation
============

Install as if this is a new osCommerce installation.  Then enter the admin area and turn on 3 new Header Tag modules;

1.  colorbox
2.  datepicker
3.  grid/list view

The functionality of these have been moved to header tag modules so that the site will only load them on the pages needed rather than on all pages.  Admin > Modules > Header Tags > {install}

You also need to install other components such as the logo, breadcrumb, footer boxes, side column boxes and so on.  Admin > Modules > Boxes > {install} AND Admin > Modules > Content > {install}.  Boxes and Modules can be sorted using the sort order, lowest is displayed first.

Database Conversion Script
==========================

This script will change a 2.3.4 Database into a fully useable database for osCommerce-234-Bootstrap.

http://forums.oscommerce.com/topic/399678-234normal-to-234responsive-database-conversion-script/
