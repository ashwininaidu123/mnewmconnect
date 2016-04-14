<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URImcube requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/
$route['default_controller'] = "site";
$route['scaffolding_trigger'] = "";

/* Genral section  */
$route['Home']									= 'dashboard';

/*  site  */
$route['AddSite/(.*)']				            ='mconnect/addsite/$1';
$route['ListSite/(.*)']				            ='mconnect/listSite/$1';
$route['DeletedSite/(.*)']						='mconnect/deleteSite/$1';
$route['DeleteSite/(.*)']						='mconnect/delSite/$1';

/* Location */
$route['AddLocation/(.*)']		                ='mconnect/addlocation/$1';
$route['ListLocation/(.*)']				        ='mconnect/listlocation/$1';
$route['DeleteLocation/(.*)']				    ='mconnect/deleteLocation/$1';
$route['DeletedLoc/(.*)']				        ='mconnect/deleteLoc/$1';

/* Executive */
$route['AddExeSite/(.*)']				        ='mconnect/addemptosites/$1';
$route['ListExeSite/(.*)']				        ='mconnect/site_emp_list/$1';
$route['ResCounter/(.*)']				        ='mconnect/refreshcounter/$1';

/* Reports */
$route['SiteVisits/(.*)']		                ='mconnect/sitevisits/$1';
$route['Referrals/(.*)']		                ='mconnect/sitereferrals/$1';

/* Offers  */
$route['Offers/(.*)']		                    ='mconnect/siteoffers/$1';
$route['AddOffers/(.*)']		                ='mconnect/addoffers/$1';
$route['ListOffers/(.*)']		                ='mconnect/listoffers/$1';

/* property  */
$route['AddProperty']		                ='mconnect/addproperty';
$route['ListProperty/(.*)']		                ='mconnect/listproperty/$1';
$route['DeleteProperty/(.*)']		            ='mconnect/deletedprop/$1';
$route['DeleteProp/(.*)']		                ='mconnect/delete_Prop/$1';

//$route['Employee/([a-zA-Z_-]+)/(:any)']	= '$1/admin/$2';






/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
