<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
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
$route['TrackDashboard']						= 'dashboard/calltrackdashboard';
$route['IVRSDashboard']							= 'dashboard/ivrsdashboard';
$route['PBXDashboard']							= 'dashboard/pbxdashboard';
$route['leadsDashboard']						= 'dashboard/leaddashboard';
$route['SupDashboard']						    = 'dashboard/supportdashboard';
$route['Blocknumbers']							= 'group/blocknumbers';
/* Employee section  */
$route['ManageEmployee/(.*)']					= 'Employee/manage_emp/$1';
$route['EditEmployee/([\w]+)']					= 'Employee/add_employee/$1';
$route['unconfirmEmployee/([\w]+)/([\w]+)']		= 'Employee/add_employee/$1/$2';
$route['AddEmployee/(.*)']						= 'Employee/add_employee/$1';
$route['DeletedEmployee']						= 'Employee/deleted_employees';
$route['UnconfirmEmployees']					= 'Employee/unconfirmlist/$1';
/* PBX  section  */
$route['PBXadd/(.*)']							= 'pbx/configure/$1';
$route['ManagePBX/(.*)']						= 'pbx/manage/$1';
$route['PBXDelete/(.*)']						= 'pbx/deleted/$1';
$route['EditPBX/([\w]+)']						= 'pbx/configure/$1';
$route['Addextn/([\w]+)/([\w]+)']				= 'pbx/addext/$1/$2';
$route['Listextn/(.*)']							= 'pbx/listext/$1';
$route['PBXReport/([\w]+)/(.*)']				= 'pbx/report/$1/$2';
$route['PBXEditReport/(.*)']					= 'pbx/PbxeditReport/$1';

/* IVRS  section  */
$route['IVRSadd']								= 'ivrs/add';
$route['ManageIVRS']							= 'ivrs/manage';
$route['IVRSDelete']							= 'ivrs/deleted';
$route['IVRSref']								= 'ivrs/ivRef';
$route['AddIVRSref']							= 'ivrs/AddIvrRef';
$route['IVRSEditReport/(.*)']					= 'ivrs/editReport/$1';
$route['IVRSReport/(.*)']						= 'ivrs/report/$1';
$route['AddOption/(.*)/(.*)/(.*)']				= 'ivrs/addopt/$1/$2/$3';
$route['ListOption/(.*)/(.*)']					= 'ivrs/options/$1/$2';

/* Track section  */
$route['ManageGroup/(.*)']						='group/manage_group/$1';
$route['AddGroup']								='group/add_group';
$route['EditGroup/([\w]+)']						='group/add_group/$1';
$route['DeleteGroup/(.*)']						='group/deletedgroup/$1';
$route['AddempGroup/(.*)']						='group/addemptogroup/$1';
$route['AddempGroups/(.*)']						='Employee/addemptogroups/$1';
$route['ListempGroup/(.*)']						='group/group_emp_list/$1';
$route['AddContacts']							='Report/addContacts/$1';
$route['Listcontacts/(.*)']						='Report/listcontacts/$1';
/* Admin Section */
$route['BusinessProfile']						='Business/profile_edit';
$route['ManageCustom']							='customfield/Managecustomfield';
$route['AddRole/(.*)']							='emp/role/$1';
$route['ManageRole']							='customfield/system_rolelist';
$route['AddHoliday']							='group/addhday';
$route['ManageHoliday/(.*)']					='group/hlist/$1';
$route['EditHoliday/(.*)']						='group/EditHoliday/$1';
$route['AuditTrail/(.*)']						='customfield/auditlog/$1';
$route['ManageRegion']							='customfield/manageregion';
$route['AddRegion/(.*)']						='customfield/AddRegion/$1';
$route['AddGrpRegion/(.*)/(.*)']				='customfield/AddGroup_Region/$1/$2';
$route['ManageGrpRegion/(.*)']					='customfield/View_GroupRegion/$1';
$route['BlockList']								='group/blocklist';
$route['AddEmailTemplate/(.*)']					='Email/template/$1';
$route['ManageEmailTemplate']					='Email/listemplate';
$route['AddSMSTemplate/(.*)']					='Email/Smstemplate/$1';
$route['ManageSMSTemplate']						='Email/list_smsemplate';
$route['EmailConfig']							='Email/config';
$route['AccountSettings']						='admin/acc_config';
/* Leads section  */
$route['AddLeadGroup']							='leads/lead_grp_add';
$route['EditLeadGroup/(.*)']					='leads/lead_grp_add/$1';
$route['ListLeadGroup/(.*)']					='leads/lead_grp_list/act/$1';
$route['DeleteLeadGroup/(.*)']					='leads/lead_grp_list/del/$1';
$route['AddempLeadGroup/(.*)']					='leads/addgrpemp/$1';
$route['ListempLeadGroup/(.*)']					='leads/lead_grpemp_list/$1';
//~ $route['AddProspect/1']							='leads/addLead/1';
//~ $route['ListProspect/1']						='leads/index/1';
//~ $route['DeleteProspect/1']						='leads/deleteList/1';
$route['EditLead/(.*)/(.*)']					='leads/edit/$1/$2';
$route['ImportLeads/(.*)/(.*)']					='leads/import/$1/$2';
$route['AddLead/(.*)']							='leads/addLead/$1';
$route['ListLead/(.*)']							='leads/index/$1';
$route['DeleteLead/(.*)']						='leads/deleteList/$1';
/* Reports Section */
$route['TrackReport/(.*)']						='Report/call/all/$1';
$route['MissedTrackReport/(.*)']				='Report/call/m/$1';
$route['AttTrackReport/(.*)']					='Report/call/at/$1';
$route['QualTrackReport/(.*)']					='Report/call/q/$1';
$route['UnQualTrackReport/(.*)']				='Report/call/u/$1';
$route['EditTrackReport/(.*)']					='Report/edit/$1';
$route['SentEmails']							='Email/sent';
$route['C2CReport/(.*)']						='Report/outbound_calls/$1';
$route['C2GroupReport/(.*)']					='Report/click2call/$1';
$route['SMSReport']								='Report/smsreport';
$route['FollowupReport']						='Report/followupreport';
$route['CallAnalytics']							='Report/callanalytics';
$route['EmpBreakHis/(.*)']						='Report/empBreakHis/$1';
$route['OutboundReport']						='Report/outbound';
$route['CallSummary/(.*)']						='Report/callsummary/$1';
$route['CallArchive/(.*)/(.*)']					='Report/callarchive/$1/$2';
$route['DeleteC2CReport']						='Report/undeleteC2Calls';
$route['DeleteReport']							='Report/undeleteCalls';

/* Support section  */
$route['AddSupGroup']							='support/addSupportGrp';
$route['ListSupGroup/(.*)']						='support/listSupportGrp/act/$1';
$route['DeleteSupGroup/(.*)']					='support/listSupportGrp/del/$1';
$route['AddempSupGroup/(.*)']					='support/addGrpEmp/$1';
$route['ListempSupGroup/(.*)']					='support/listGrpEmp/$1';
$route['AddSupTkt/(.*)']						='support/addSupportTkt/$1';
$route['EditSupTkt/(.*)']						='support/editSupportTkt/$1';
$route['ListSupTkt/(.*)']						='support/listSupportTkt/$1';
$route['DeleteSupTkt']							='support/delSupTktList';
$route['supConfigure']							='support/followupsetup';

/*Mcube Connect  */
$route['AddSite']							    ='mconnect/addsite';
$route['ListSite/(.*)']					        ='mconnect/listSite/act/$1';
$route['DeleteSite/(.*)']				        ='mconnect/listSite/del/$1';
$route['SiteVisits']				            ='mconnect/sitevisits';
$route['SiteReferrals']				            ='mconnect/referrals';
$route['SiteOffers']				            ='mconnect/offers';


/* End of group section  */


//~ $route['([a-z]+)/']	= 'dashboard';


//$route['Employee/([a-zA-Z_-]+)/(:any)']	= '$1/admin/$2';






/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
