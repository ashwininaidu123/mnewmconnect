<?php
   Class Dashboardmodel extends Model {
	   
    function Dashboard(){
		parent::Model();
		$this->load->model('configmodel','CM');
		$this->load->model('empmodel','EMPM');
		$this->load->model('systemmodel','SYS');
	}
	function feature_access(){
		$show=0;
		$data1=array();
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(17,$checklist))	
		$data1['mconnect']='1';
		return $data1;
	}
   	function propvist(){
		if(!$this->feature_access(17))redirect('Employee/access_denied');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
		$sql     = "SELECT p.*,COUNT(DISTINCT(s.siteid)) as sites,COUNT(DISTINCT(ul.authkey)) as likes,COUNT(DISTINCT(r.referedtonum)) as referrals,COUNT(DISTINCT(o.propertyname)) as offers  FROM  ".$bid."_property p
		                     LEFT JOIN  ".$bid."_site s ON s.pid = p.propertyid
		                     LEFT JOIN  user_likes ul ON ul.siteid = s.siteid
		                      LEFT JOIN referrals r ON r.siteid = s.siteid
		                      LEFT JOIN offers o ON o.propertyname = p.propertyid
		                      GROUP BY  p.propertyid ORDER BY s.time DESC  LIMIT 3 OFFSET 0";
         $rest    = $this->db->query($sql)->result_array();
              return $rest;
	}
  	function totalcount(){
		if(!$this->feature_access(17))redirect('Employee/access_denied');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
	  $sql     = "SELECT COUNT(DISTINCT(o.propertyname)) as offers,COUNT(DISTINCT(ul.authkey)) as likes,COUNT(DISTINCT(vh.authkey)) as              visits,COUNT(DISTINCT(r.referedtonum)) as referrals FROM  ".$bid."_property p 
			 LEFT JOIN  user_likes ul ON ul.pid = p.propertyid
			 LEFT JOIN  visited_history vh ON vh.pid = p.propertyid
			 LEFT JOIN referrals r ON r.pid = p.propertyid
		      LEFT JOIN offers o ON o.propertyname = p.propertyid
			 GROUP BY  p.bid ORDER BY p.propertyid DESC";
        $rest    = $this->db->query($sql)->result_array();
              return $rest;
	}     
	
  	function sitevist(){
		if(!$this->feature_access(17))redirect('Employee/access_denied');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
         $sql     = "SELECT v.sitename,v.siteicon,p.propertyname,COUNT(DISTINCT(o.propertyname)) as offers,COUNT(DISTINCT(ul.authkey)) as likes,COUNT(DISTINCT(vh.authkey)) as visits,COUNT(DISTINCT(r.referedtonum)) as  referrals FROM  ".$bid."_site v
			 LEFT JOIN  ".$bid."_property p ON p.propertyid = v.pid
			 LEFT JOIN  user_likes ul ON ul.siteid = v.siteid
			 LEFT JOIN  visited_history vh ON vh.siteid = v.siteid
			 LEFT JOIN referrals r ON r.siteid = v.siteid
		       LEFT JOIN offers o ON o.propertyname = p.propertyid
			 GROUP BY  v.siteid ORDER BY v.time DESC  LIMIT 3 OFFSET 0";
         $rest    = $this->db->query($sql)->result_array();
              return $rest;
	}   
	  function visitcount(){
		if(!$this->feature_access(17))redirect('Employee/access_denied');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
         //~ $sql     = "SELECT COUNT(DISTINCT(authKey)) as visits,MONTHNAME(visit_time) AS MonthName FROM visited_history 
			    	  //~ WHERE     year(visit_time) = YEAR(NOW())
                                     //~ GROUP BY  month(visit_time)";
        $year =  date("Y");                      
		$sql     = "SELECT COUNT(DISTINCT(authkey)) as visits, DATE_FORMAT(merge_date,'%b') AS month, YEAR(merge_date) AS year
		 FROM (
			   SELECT ' ".$year."-01-01' AS merge_date
			   UNION SELECT '".$year."-02-01' AS merge_date
			   UNION SELECT '".$year."-03-01' AS merge_date
			   UNION SELECT '".$year."-04-01' AS merge_date
			   UNION SELECT '".$year."-05-01' AS merge_date
			   UNION SELECT '".$year."-06-01' AS merge_date
			   UNION SELECT '".$year."-07-01' AS merge_date
			   UNION SELECT '".$year."-08-01' AS merge_date
			   UNION SELECT '".$year."-09-01' AS merge_date
			   UNION SELECT '".$year."-10-01' AS merge_date
			   UNION SELECT '".$year."-11-01' AS merge_date
			   UNION SELECT '".$year."-12-01' AS merge_date
			  ) AS m
	LEFT JOIN visited_history u ON MONTH(merge_date) = MONTH(u.visit_time) AND YEAR(m.merge_date) = YEAR(u.visit_time)
	GROUP BY m.merge_date
	ORDER BY 1+1;";        
         $rest    = $this->db->query($sql)->result_array();
         if(count($rest)>0){
			 foreach($rest as $re){
			    $res[$re['month']]=$re['visits'];
			 }
	      }
	    //  json_encode($res); 
         return $res;
	}       
	  function likecount(){
		if(!$this->feature_access(17))redirect('Employee/access_denied');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$roleDetail['modules']['48']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = $this->session->userdata('eid');
	    $year =  date("Y");
         //~ $sql     = "SELECT COUNT(DISTINCT(authkey)) as likes, year(like_time) AS Year, MONTHNAME(like_time) AS MonthName FROM user_likes
                                     //~ WHERE     year(like_time) = YEAR(NOW())
                                     //~ GROUP BY  month(like_time)
                                     //~ ORDER BY  month(like_time) ASC";  
                                     //~ 
          $sql     = "SELECT COUNT(DISTINCT(authkey)) as likes, DATE_FORMAT(merge_date,'%b') AS month, YEAR(merge_date) AS year
						 FROM (
					   SELECT ' ".$year."-01-01' AS merge_date
			   UNION SELECT '".$year."-02-01' AS merge_date
			   UNION SELECT '".$year."-03-01' AS merge_date
			   UNION SELECT '".$year."-04-01' AS merge_date
			   UNION SELECT '".$year."-05-01' AS merge_date
			   UNION SELECT '".$year."-06-01' AS merge_date
			   UNION SELECT '".$year."-07-01' AS merge_date
			   UNION SELECT '".$year."-08-01' AS merge_date
			   UNION SELECT '".$year."-09-01' AS merge_date
			   UNION SELECT '".$year."-10-01' AS merge_date
			   UNION SELECT '".$year."-11-01' AS merge_date
			   UNION SELECT '".$year."-12-01' AS merge_date
							  ) AS m
					LEFT JOIN user_likes u ON MONTH(merge_date) = MONTH(u.like_time) AND YEAR(m.merge_date) = YEAR(u.like_time)
					GROUP BY m.merge_date
					ORDER BY 1+1;";                                                                          
         $rest    = $this->db->query($sql)->result_array();
        if(count($rest)>0){
			 foreach($rest as $re){
			    $res[$re['month']]=$re['likes'];
			 }
	     }
	    //  json_encode($res); 
         return $res;
	}    
 }
   ?>



