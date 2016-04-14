<?php
   Class Mconnectmodel extends Model
   {
   	function Mconnectmodel(){
   		 parent::Model();
   	     $this->load->model('auditlog');
   		 $this->load->model('configmodel');
   		 $this->load->model('ivrsmodel');
   		 $this->load->model('systemmodel');
   	}
   function addproperty($bid){
     $arr = array_keys($_POST);
     	for($i=0;$i<sizeof($arr);$i++){
   	    if(!in_array($arr[$i],array("update_system","bid","propertyid"))){
   			if(is_array($_POST[$arr[$i]]))
   				$val = @implode(',',$_POST[$arr[$i]]);
   			elseif($_POST[$arr[$i]]!="")
   		        $val=$_POST[$arr[$i]];
   			else
   				$val='';
   			$this->db->set($arr[$i],$val);
   		}
   	}
     $sql=$this->db->query("SELECT * FROM ".$bid."_property");
     if($sql->num_rows()==0){
                 $propertyid = $this->db->query("SELECT COALESCE(MAX(`propertyid`),0)+1 as id FROM ".$bid."_property")->row()->id;
                 $id = $propertyid;
                 $this->db->set('propertyid',$id);
   					$this->db->set('bid',$bid);
   					$this->db->set('status','1');
    }else{
                 $id = $this->db->query("SELECT COALESCE(MAX(`propertyid`),0)+1 as id FROM ".$bid."_property")->row()->id;
   					$this->db->set('bid',$bid);
   					$this->db->set('status','1');
   					$this->db->set('propertyid',$id);  
    }
        
       $this->db->set('propertyname',$_POST['propertyname']);	
   			    if(isset($_FILES['propertyicon']) && $_FILES['propertyicon']['error']==0){
   				$ext=pathinfo($_FILES['propertyicon']['name'],PATHINFO_EXTENSION);
   		     	$newName = "propertyicon".date('YmdHis').".".$ext;
   		        $size=filesize($_FILES['propertyicon']['tmp_name']);
   				move_uploaded_file($_FILES['propertyicon']['tmp_name'],"./uploads/".$newName);
   				$this->db->set('propertyicon',$newName);
   	  }
   
      $this->db->insert($bid."_property");
   return '0';
   }
   	
   function editproperty($bid,$id){
     $arr = array_keys($_POST);
   for($i=0;$i<sizeof($arr);$i++){
   					   if(!in_array($arr[$i],array("update_system","bid","propertyid"))){
   							/* Changed for custom fields */
   							if(is_array($_POST[$arr[$i]]))
   								$val = @implode(',',$_POST[$arr[$i]]);
   							elseif($_POST[$arr[$i]]!="")
   								$val=$_POST[$arr[$i]];
   							else
   								$val='';
   							$this->db->set($arr[$i],$val);
   						}
   					}
   	  if(isset($_FILES['propertyicon']) && $_FILES['propertyicon']['error']==0){
   				$ext=pathinfo($_FILES['propertyicon']['name'],PATHINFO_EXTENSION);
   				$newName = "propertyicon".date('YmdHis').".".$ext;
   				move_uploaded_file($_FILES['propertyicon']['tmp_name'],"./uploads/".$newName);
   				$this->db->set('propertyicon',$newName);
   	  }
   					$this->db->where('propertyid',$id);
   					$this->db->update($bid."_property");
   				return '1';
   	}
   	
    function getlistproperty($bid,$ofset,$limit){
   		$q= '';
   		if(isset($_POST['submit'])){
   			$this->session->set_userdata('search',$_POST);
   		}
   		if($this->session->userdata('search')){
   			$s = $this->session->userdata('search');
   		}
   		$q .=(isset($s['propertyname']) && $s['propertyname']!='')?" AND a.locmane LIKE '%".$s['propertyname']."%'":"";
   		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ".$bid."_property WHERE status !='2'";  
   		$rst = $this->db->query($sql)->result_array();
   		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   		$ret['count'] = $rst1->row()->cnt;
   		foreach($roleDetail['modules'] as $mod){
   			if($mod['modid']=='52'){
   				$opt_add 	= $mod['opt_add'];
   				$opt_view 	= $mod['opt_view'];
   				$opt_delete = $mod['opt_delete'];
   			}
   		}
   
   		$fieldset = $this->configmodel->getFields('52',$bid);
   		$keys = array();
   		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
   			if($opt_add || $opt_view || $opt_delete)
   			array_push($header,$this->lang->line('level_Action'));
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
   				foreach($roleDetail['system'] as $f){
   					if($f['fieldid']==$field['fieldid'])$checked = true;
   				}
   				if($checked){
   					array_push($keys,$field['fieldname']);
   					array_push($header,(($field['customlabel']!="")
   										?$field['customlabel']
   										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
   				}
   			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
   				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldKey']);
   					array_push($header,$field['customlabel']);
   				}
   			}
   		}
   
   		$ret['header'] = $header;
   		$list = array();
   		$i = $ofset+1;
   		foreach($rst as $rec){
   			$data = array($i);
   			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['propertyid'].'"/>';	
   			array_push($data,$v);
   			$act = '';
   if($opt_add || $opt_view || $opt_delete){
   	$act .=  '<div class="btn-group">
                      <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
       $act .= ($opt_add) ?'<li><a href="mconnect/addproperty/'.$rec['propertyid'].'"><span title="Edit" class="fa fa-edit">&nbsp;&nbsp;Edit</span></a></li>':'';
       $act .= ($opt_delete) ? '<li><a href="DeleteProp/'.$rec['propertyid'].'" class="deleteClass"><span title="Delete" class="fa fa-fw fa-trash">&nbsp;&nbsp;Delete</span></a></li>':'';
       $act .= ($opt_add)? '<li><a href="AddSite/'.$rec['propertyid'].'"  ><span class="fa fa-fw fa-cube" title="Add ShowHome" >&nbsp;Add ShowHome</span></a><li>':'';
   				$act .= '<li><a href="ListSite/'.$rec['propertyid'].'"><span title="List ShowHome" class="fa fa-list-ul">&nbsp;List ShowHome</span></a></li>';
   	$act .= '</ul></div>';
   	$data['action'] = $act;
   }
   			$r = $this->configmodel->getDetail('52',$rec['propertyid'],'',$bid);
   			foreach($keys as $k){
   	if($k == 'propertyicon'){
                 $v = ($r['propertyicon'] != '')?"<img style='text-align:center' class='imgset' alt='uploaded image'  height=\"100\" width=\"100\"  src='".base_url().'/uploads/'.$r['propertyicon']."'>":'';
                }else{
   			     $v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
     }
     				  array_push($data,$v);
   			}
   			$i++;
   			array_push($list,$data);
   		}
   		$ret['rec'] = $list;
   		return $ret;
   	}
   	
   	 	function delete_Prop($pid){
     $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $this->db->set('status','2');
   		$this->db->where('propertyid',$pid);
   		$this->db->update($bid."_property");
   		return '1';
   }
    function deletedprop($bid,$ofset='0',$limit='20'){//echo $limit;
   		$q= '';
   		if(isset($_POST['submit'])){
   			$this->session->set_userdata('search',$_POST);
   		}
   		if($this->session->userdata('search')){
   			$s = $this->session->userdata('search');
   		}
   		$q.=(isset($s['propertyname']) && $s['propertyname']!='')?" AND a.propertyname = '".$s['propertyname']."'":"";
   		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
   					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
   		$sql = "SELECT a.* FROM ".$bid."_property a 
   				WHERE a.status='2' $q";
   		$rst = $this->db->query($sql)->result_array();
   		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   		$ret['count'] = $rst1->row()->cnt;
   		foreach($roleDetail['modules'] as $mod){
   			if($mod['modid']=='52'){
   				$opt_add 	= $mod['opt_add'];
   				$opt_view 	= $mod['opt_view'];
   				$opt_delete = $mod['opt_delete'];
   			}
   		}
   		$fieldset = $this->configmodel->getFields('52',$bid);
   		$keys = array();
   		$header = array('#');
   		if($opt_add || $opt_view || $opt_delete)
   			array_push($header,$this->lang->line('level_Action'));
   	
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
   				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldname']);
   					array_push($header,(($field['customlabel']!="")
   										?$field['customlabel']
   										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
   				}
   			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
   				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldKey']);
   					array_push($header,$field['customlabel']);
   				}
   			}
   		}
   		$ret['header'] = $header;
   		$list = array();
   		$i = $ofset+1;
   		foreach($rst as $rec){
   			$r = $this->configmodel->getDetail('52',$rec['propertyid'],'',$bid);
   			$data = array($i);
   			if($opt_add || $opt_view || $opt_delete){
   	$act = '<div class="btn-group">
                  <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
   				$act .= '<li><a href="'.base_url().'mconnect/undeletedprop/'.$rec['propertyid'].'"><span title="Delete" class="glyphicon glyphicon-refresh">&nbsp;UnDelete</span></a></a></li>';
   				$act .= '</ul></div>';
   	$data['action'] = $act;
   
   			}
   			foreach($keys as $k){
   						if($k == 'propertyicon'){
                 $v = ($r['propertyicon'] != '')?"<img style='text-align:center' class='imgset' alt='uploaded image'  height=\"100\" width=\"100\"  src='".base_url().'/uploads/'.$r['propertyicon']."'>":'';
                }else{
   			     $v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
     }
     				  array_push($data,$v);
   			}
   			
   			$i++;
   			array_push($list,$data);
   		}
   		$ret['rec'] = $list;
   		return $ret;
   	}
   	function undeletedprop($pid,$bid){
   		$this->db->set('status','1');
   		$this->db->where('propertyid',$pid);
   		$this->db->update($bid."_property");
   		return true;
   	}
   	
   	function addsite($pid,$id){
   $q = "";
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
     $arr = array_keys($_POST);
     $_POST['tracknum'] = ($_POST['tracknum']!='0') ? $_POST['tracknum']:$this->getFreePri();
     	for($i=0;$i<sizeof($arr);$i++){
   	    if(!in_array($arr[$i],array("update_system","bid","siteid","id","siteicon","sitevideo","siteimg","intrestopt"))){
   			if(is_array($_POST[$arr[$i]]))
   				$val = @implode(',',$_POST[$arr[$i]]);
   			elseif($_POST[$arr[$i]]!="")
   		        $val=$_POST[$arr[$i]];
   			else
   				$val='';
   			$this->db->set($arr[$i],$val);
   		}
   	}
     if($id == ""){
   	    $sql=$this->db->query("SELECT * FROM ".$bid."_site");
                if($sql->num_rows()==0){  
          $siteid1 = $this->db->query("SELECT COALESCE(MAX(`siteid`),0)+1 as id FROM ".$bid."_site")->row()->id;
                $siteid = $bid.$siteid1;
          }else{
                 $siteid = $this->db->query("SELECT COALESCE(MAX(`siteid`),0)+1 as id FROM ".$bid."_site")->row()->id;
         	}
                 $this->db->set('siteid',$siteid);
   					$this->db->set('bid',$bid);
   					$this->db->set('pid',$pid);
   					$this->db->set('status','1');
   					   $intrestopt = array();
   				 $capture_field_vals ="";
   				 $intrestopt = $_POST["intrestopt"];
   		if(isset($intrestopt)){    
   			foreach($intrestopt as $key => $text_field){
   			   unset ($intrestopt[$key]);
   			   $intrestopt[0] = 'intrestopt';
   			   $intrestopt[1] = 'intrestopt1';
   			   $intrestopt[2] = 'intrestopt2';
   			   $intrestopt[3] = 'intrestopt3';
   			   $this->db->set($intrestopt[$key],$text_field);
   		}
   	}
       $this->db->set('sitename',$_POST['sitename']);
   	$this->db->set('siteinterest_opt',$_POST['siteinterest_opt']);
   	$this->db->set('sitedesc',$_POST['sitedesc']);
   	$this->db->set('tracknum',$_POST['tracknum']);
   	$this->db->set('email',$_POST['email']);
   	if(isset($_POST["sitevideo"])){ 
   	$this->db->set('sitemedia',$_POST['sitevideo']);	
       	}	
   			    if(isset($_FILES['siteicon']) && $_FILES['siteicon']['error']==0){
   				$ext=pathinfo($_FILES['siteicon']['name'],PATHINFO_EXTENSION);
   		     	$newName = "siteicon".date('YmdHis').".".$ext;
   		        $size=filesize($_FILES['siteicon']['tmp_name']);
   				move_uploaded_file($_FILES['siteicon']['tmp_name'],"./uploads/".$newName);
   				$this->db->set('siteicon',$newName);
   	  }
    
   	if(isset($_FILES['siteimg'])){
   				$ext=pathinfo($_FILES['siteimg']['name'],PATHINFO_EXTENSION);
   		     	$newName = "siteimg".date('YmdHis').".".$ext;
   				move_uploaded_file($_FILES['siteimg']['tmp_name'],"./uploads/".$newName);
   				 $this->db->set('sitemedia',$newName);
   	  }	
   $this->ivrsmodel->updatePri($_POST['prinumber'],1,$bid,0,$id);
      $this->db->insert($bid."_site");
       	if(isset($_FILES['siteimage'])){
    	   	$imgid = $this->db->query("SELECT COALESCE(MAX(`imgid`),0)+1 as id FROM ".$bid."_site_image")->row()->id;
    	    $this->db->set('imgid',$imgid);
    	    $this->db->set('siteid',$id);
    	    $this->db->set('bid',$bid);
    	    $this->db->set('pid',$pid);
                  $images_arr = array();
   		foreach($_FILES['siteimage']['name'] as $key=>$val){
   			$target_dir = "./uploads/";
   			$target_file = $target_dir.$_FILES['siteimage']['name'][$key];
   			$target_file1 = $_FILES['siteimage']['name'][$key];
   			if(move_uploaded_file($_FILES['siteimage']['tmp_name'][$key],$target_file)){
   				$images_arr[] = $target_file1;
   			}
   		 }
   			foreach($images_arr as $key => $val ){
   			   unset ($images_arr[$key]);
   			   $images_arr[0] = 'site_image';
   			   $images_arr[1] = 'site_image1';
   			   $images_arr[2] = 'site_image2';
   			   $this->db->set($images_arr[$key],$val);
   		   }
   					$this->db->insert($bid."_site_image");
   	}
   	return '0';
    }else{
   				 $intrestopt = array();
   				 $capture_field_vals ="";
   				 $intrestopt = $_POST["intrestopt"];
   		if(isset($intrestopt)){    
   			foreach($intrestopt as $key => $text_field){
   			   unset ($intrestopt[$key]);
   			   $intrestopt[0] = 'intrestopt';
   			   $intrestopt[1] = 'intrestopt1';
   			   $intrestopt[2] = 'intrestopt2';
   			   $intrestopt[3] = 'intrestopt3';
   			   $this->db->set($intrestopt[$key],$text_field);
   			}
   	}
   	  if(isset($_FILES['siteicon']) && $_FILES['siteicon']['error']==0){
   				$ext=pathinfo($_FILES['siteicon']['name'],PATHINFO_EXTENSION);
   				$newName = "siteicon".date('YmdHis').".".$ext;
   				move_uploaded_file($_FILES['siteicon']['tmp_name'],"./uploads/".$newName);
   				$this->db->set('siteicon',$newName);
   	  }
   	  if(isset($_FILES['siteimg']) && $_FILES['siteimg']['error']==0){
   				$ext=pathinfo($_FILES['siteimg']['name'],PATHINFO_EXTENSION);
   				$newimg= "siteimg".date('YmdHis').".".$ext;
   				move_uploaded_file($_FILES['siteimg']['tmp_name'],"./uploads/".$newimg);
   				 $this->db->set('sitemedia',$newimg);
   	  }
   	
   	   	if(isset($_POST["sitevideo"])){ 
   	    $this->db->set('sitemedia',$_POST['sitevideo']);	
           }
   				
   					$this->db->where('siteid',$id);
   					$this->ivrsmodel->updatePri($_POST['tracknum'],1,$bid,0,$id);
   					$this->db->update($bid."_site");
   				
   				    $s=$this->db->query("SELECT siteid FROM ".$bid."_site_image WHERE siteid='".$id."'");
   		            if($s->num_rows()>0){
   				    $images_arr = array();
   		foreach($_FILES['siteimage']['name'] as $key=>$val){
   			$target_dir = "./uploads/";
   			$target_file = $target_dir.$_FILES['siteimage']['name'][$key];
   			$target_file1 = $_FILES['siteimage']['name'][$key];
   			if(move_uploaded_file($_FILES['siteimage']['tmp_name'][$key],$target_file)){
   				$images_arr[] = $target_file1;
   			}
   		}
   		if(is_array($images_arr)){
   			foreach($images_arr as $key => $val ){
   			   unset ($images_arr[$key]);
   			   $images_arr[0] = 'site_image';
   			   $images_arr[1] = 'site_image1';
   			   $images_arr[2] = 'site_image2';
   			$sql = "UPDATE `".$bid."_site_image` SET `".$images_arr[$key]."`=('".$val."') WHERE `siteid`=$id";
   		   	$rst = $this->db->query($sql);
   		   }
   		}
   	}else{
   
    	   	$imgid = $this->db->query("SELECT COALESCE(MAX(`imgid`),0)+1 as id FROM ".$bid."_site_image")->row()->id;
    	    $this->db->set('imgid',$imgid);
    	    $this->db->set('siteid',$id);
    	    $this->db->set('bid',$bid);
                  $images_arr = array();
   		foreach($_FILES['siteimage']['name'] as $key=>$val){
   			$target_dir = "./uploads/";
   			$target_file = $target_dir.$_FILES['siteimage']['name'][$key];
   			$target_file1 = $_FILES['siteimage']['name'][$key];
   		
   			if(move_uploaded_file($_FILES['siteimage']['tmp_name'][$key],$target_file)){
   				$images_arr[] = $target_file1;
   			}
   			
   		 }
   			foreach($images_arr as $key => $val ){
   			   unset ($images_arr[$key]);
   			   $images_arr[0] = 'site_image';
   			   $images_arr[1] = 'site_image1';
   			   $images_arr[2] = 'site_image2';
   			   $this->db->set($images_arr[$key],$val);
   		   }
   					$this->db->insert($bid."_site_image");
   	}
   				return $id;
    }
   }
   
   	function getlistSite($pid,$bid,$ofset,$limit){
   		$q= '';
   		if(isset($_POST['submit'])){
   			$this->session->set_userdata('search',$_POST);
   		}
   		if($this->session->userdata('search')){
   			$s = $this->session->userdata('search');
   		}
   		$q .=(isset($pid) && $pid!='0')?"AND a.pid = ".$pid."":"";
   		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
   					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
   		$sql = "SELECT SQL_CALC_FOUND_ROWS a.*,e.empname as employee,CONCAT_WS(',',IFNULL(a.intrestopt,''),IFNULL( a.intrestopt1,''),IFNULL(a.intrestopt2,''),IFNULL(a.intrestopt3,'')) as opt FROM ".$bid."_site a
   			    LEFT JOIN prinumbers n on n.number = a.tracknum
   			    LEFT JOIN ".$bid."_employee e on e.eid = a.site_employee
   			   WHERE a.status =1 $q ORDER BY a.siteid  DESC 
   		          LIMIT $ofset,$limit";   
   		$rst = $this->db->query($sql)->result_array();
   		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   		$ret['count'] = $rst1->row()->cnt;
   		foreach($roleDetail['modules'] as $mod){
   			if($mod['modid']=='48'){
   				$opt_add 	= $mod['opt_add'];
   				$opt_view 	= $mod['opt_view'];
   				$opt_delete = $mod['opt_delete'];
   			}
   		}
   
   		$fieldset = $this->configmodel->getFields('48',$bid);
   		$keys = array();
   		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
   			if($opt_add || $opt_view || $opt_delete)
   			array_push($header,$this->lang->line('level_Action'));
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing'] ){
   				foreach($roleDetail['system'] as $f){
   					if($f['fieldid']==$field['fieldid'])$checked = true;
   				}
   				if($checked){
   		if(!in_array($field['fieldname'],array('siteimg','sitevideo'))){
   					array_push($keys,$field['fieldname']);
   					array_push($header,(($field['customlabel']!="")
   										?$field['customlabel']
   										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
   				}
   }
   			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
   				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldKey']);
   					array_push($header,$field['customlabel']);
   				}
   			}
   		}
   
   	   array_push($keys,'intrestopt','propertyname');
   	   array_push($header,'Options','Property Name');
   		$ret['header'] = $header;
   		$list = array();
   		$i = $ofset+1;
   		foreach($rst as $rec){
   			$data = array($i);
   			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['siteid'].'"/>';	
   			array_push($data,$v);
   			if($opt_add || $opt_view || $opt_delete){
   				$act = '';
   				   $act .=  '<div class="btn-group">
                  <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
   					$act .= ($opt_add) ?'<li><a href="mconnect/addsite/'.$rec['pid'].'/'.$rec['siteid'].'"><span title="Edit" class="fa fa-edit">&nbsp;Edit</span></a></li>':'';
   					$act .= ($opt_delete) ? '<li><a href="'.base_url().'DeleteSite/'.$rec['siteid'].'/'.$rec['tracknum'].'" class="deleteClass"><span title="Delete" class="fa fa-fw fa-trash">&nbsp;Delete</span></a></li>':'';
   				    $act .= ($opt_add)? '<li><a href="AddLocation/'.$rec['pid'].'/'.$rec['siteid'].'"  ><span class="fa fa-fw fa-cube" title="Add Location" >&nbsp;Add Location</span></a><li>':'';
   					$act .= '<li><a href="ListLocation/'.$rec['pid'].'/'.$rec['siteid'].'" ><span title="List Locations" class="fa fa-list-ul">&nbsp;List Locations</span></a></li>';
   					$act .= ($opt_add) ? '<li><a href="AddExeSite/'.$rec['pid'].'/'.$rec['siteid'].'"><span class="fa fa-fw fa-user-plus" title="Add Executive">&nbsp;Add&nbsp;Executive</span></a></li>':'';
   	    $act .= ($opt_view) ? '<li><a href="ListExeSite/'.$rec['pid'].'/'.$rec['siteid'].'"><span title="List Executive" class="fa fa-fw fa-user">&nbsp;List&nbsp;Executive</span></a>':'';
   					$act .= '</ul></div>';
   					$data['action'] = $act;
   			}
   			$r = $this->configmodel->getDetail('48',$rec['siteid'],'',$bid);
   		   foreach($keys as $k){
      if($k == 'intrestopt'){ 
                 $v =  ($rec['opt']!=',,,') ?$rec['opt']:'';
                }elseif($k == 'siteicon'){
                 $v = ($r['siteicon'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['siteicon']."'>":'';
                }elseif($k == 'sitemedia') {
   	 $rest = substr($r['sitemedia'], 0, 4);
             if($rest == 'site'){ 
                $img  = ($r['sitemedia'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['sitemedia']."'>":'';
                $img .= ($r['site_image'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['site_image']."'>":'';
                $img .= ($r['site_image1'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['site_image1']."'>":'';
                $img .= ($r['site_image2'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['site_image2']."'>":'';
                $v = $img;
                }elseif($rest == 'http'){
                 $v = ($r['sitemedia'] != '')?"<iframe width=\"300\" height=\"100\"  frameborder=\"0\" allowfullscreen src='".$r['sitemedia']."'></iframe>":'';
					   }else{
						$v= "";
					}
                }elseif($k == 'site_employee'){
        	      $v =  ($r['employee']) ?$r['employee']:'';
   	           }else{
   					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
   				}
   				array_push($data,$v);
   			}
   			$i++;
   			array_push($list,$data);
   		}
   		$ret['rec'] = $list;
   		return $ret;
   	}
   	function getFreePri(){
   $sql = "SELECT * FROM dummynumber WHERE status='0' AND bid='0' LIMIT 0,30";
   $rst = $this->db->query($sql);
   $rec = $rst->result_array();
   return $rec[0]['number'];
   }
   function delete_site($pid,$id,$siteid){
     $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $sql=$this->db->query("DELETE FROM ".$bid."_site_emp WHERE eid=$id AND siteid=$siteid AND pid =$pid");
   $query=$this->db->query("UPDATE ".$bid."_site_emp SET callcounter=0 WHERE siteid=$siteid AND pid = $pid");
   $emp_name=$this->get_empname($id);
   $gname=$this->db->query("SELECT sitename FROM ".$bid."_site WHERE siteid='".$siteid."'")->row()->sitename;
   $this->auditlog->auditlog_info('Site Employee',$emp_name->empname." is Removed From the site ".$gname);
   return true;
   }
   function delSlist($bid,$ofset,$limit){//echo $limit;
   		if(isset($_POST['submit'])){
   			$this->session->set_userdata('search',$_POST);
   		}
   		if($this->session->userdata('search')){
   			$s = $this->session->userdata('search');
   		}
   
   		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
   					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
   		$sql = "SELECT a.* FROM ".$bid."_site a 
   				WHERE a.status='2'";
   		$rst = $this->db->query($sql)->result_array();
   		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   		$ret['count'] = $rst1->row()->cnt;
   		foreach($roleDetail['modules'] as $mod){
   			if($mod['modid']=='48'){
   				$opt_add 	= $mod['opt_add'];
   				$opt_view 	= $mod['opt_view'];
   				$opt_delete = $mod['opt_delete'];
   			}
   		}
   		$fieldset = $this->configmodel->getFields('48',$bid);
   
   		$keys = array();
   		$header = array('#');
   		if($opt_add || $opt_view || $opt_delete)
   			array_push($header,$this->lang->line('level_Action'));
   	
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing'] && !in_array($field['fieldname'],array('siteicon','sitevideo','siteimg'))){
   				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldname']);
   					array_push($header,(($field['customlabel']!="")
   										?$field['customlabel']
   										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
   				}
   			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
   				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldKey']);
   					array_push($header,$field['customlabel']);
   				}
   			}
   		}
   		$ret['header'] = $header;
   		$list = array();
   		$i = $ofset+1;
   		foreach($rst as $rec){
   			$r = $this->configmodel->getDetail('48',$rec['siteid'],'',$bid);
   			$data = array($i);
   			if($opt_add || $opt_view || $opt_delete){
   	$act = '<div class="btn-group">
                     <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
   				$act .= '<li><a href="'.base_url().'mconnect/undelSite/'.$r['siteid'].'"><span title="Delete" class="glyphicon glyphicon-refresh">&nbsp;UnDelete</span></a></a></li>';
   				$act .= '</ul></div>';
   	$data['action'] = $act;
   
   			}
 	   foreach($keys as $k){
      if($k == 'intrestopt'){ 
                 $v =  ($rec['opt']!=',,,') ?$rec['opt']:'';
                }elseif($k == 'siteicon'){
                 $v = ($r['siteicon'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['siteicon']."'>":'';
                }elseif($k == 'sitemedia') {
   	 $rest = substr($r['sitemedia'], 0, 4);
             if($rest == 'site'){ 
                $img  = ($r['sitemedia'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['sitemedia']."'>":'';
                $img .= ($r['site_image'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['site_image']."'>":'';
                $img .= ($r['site_image1'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['site_image1']."'>":'';
                $img .= ($r['site_image2'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['site_image2']."'>":'';
                $v = $img;
                }elseif($rest == 'http'){
                 $v = ($r['sitemedia'] != '')?"<iframe width=\"300\" height=\"100\"  frameborder=\"0\" allowfullscreen src='".$r['sitemedia']."'></iframe>":'';
       }else{
        $v= "";
   	}
                }elseif($k == 'site_employee'){
   	 $v =  ($r['employee']) ?$r['employee']:'';
   	}else{
   					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
   				}
   				array_push($data,$v);
   			}
   			$i++;
   			array_push($list,$data);
   		}
   		$ret['rec'] = $list;
   		return $ret;
   	}
   	function delSite($id,$pri){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$this->db->set('status','2');
   		$this->db->where('siteid',$id);
   		$this->db->update($bid."_site");
   		return '1';
   	}
   	function bulkDelSite($arr){
   		$cbid = $this->session->userdata('cbid');
   		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
   		$sql="UPDATE ".$bid."_site SET status=2 WHERE siteid IN(".$arr.")";
   		$this->db->query($sql);
   		$s=$this->db->query("SELECT sitename FROM ".$bid."_site WHERE siteid IN(".$arr.")");
   		if($s->num_rows()>0){
   			foreach($s->result_array() as $row){
   				$this->auditlog->auditlog_info('Site',$row['sitename']. " Deleted By ".$this->session->userdata('username'));
   			}	
   		}
   		return 1;
   	}
   
   	function undelSt($siteid,$bid){
   		$this->db->set('status','1');
   		$this->db->where('siteid',$siteid);
   		$this->db->update($bid."_site");
   		return true;
   	}
   
   function getBeaconlist(){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $res=array();	
   $sql = $this->db->query("SELECT * FROM beacon WHERE business = ".$bid." AND usedinbusiness != '1'");
     
   if($sql->num_rows()>0){
   	$ress=$sql->result_array();	
       $res['']=$this->lang->line('level_select');
   foreach($ress as $rec)
   	$res[$rec['beacon_id']] = $rec['beacon_id'];
     }				
     return $res;
   }
   function addnewlocation($pid,$id,$locid){
        $arr = array_keys($_POST);
   $bid = $_POST['bid'];
        $sql=$this->db->query("SELECT locid FROM ".$bid."_mc_location WHERE beaconid='".$_POST['beaconid']."'");
   if($sql->num_rows()==0){ 
   for($i=0;$i<count($arr);$i++){
   	if(isset($_POST[$arr[$i]]) && $_POST[$arr[$i]] != '' && !in_array($arr[$i],array('update_system','locid',"visited"))){
   		$this->db->set($arr[$i],$_POST[$arr[$i]]);
   
   	}
   }
   $ext = pathinfo($_FILES['loc_image']['name'],PATHINFO_EXTENSION);
   $newName = "Location".date('YmdHis').".".$ext;
      if(@move_uploaded_file($_FILES['loc_image']['tmp_name'],"./uploads/".$newName)){
   			$locid = $this->db->query("SELECT COALESCE(MAX(`locid`),0)+1 as locid FROM ".$bid."_mc_location")->row()->locid;
   			$this->db->set('locid',$locid);
   			$this->db->set('pid',$pid);
   			$this->db->set('status','1');
   			$this->db->set('loc_image',$newName);
   			$this->db->insert($bid."_mc_location");
   			
   			$this->db->set('usedinbusiness','1');
   			$this->db->set('siteid',$id);
   			$this->db->set('pid',$pid);
   			$this->db->where('beacon_id',$_POST['beaconid']);
   			$this->db->update("beacon");
   			
   		$imgid = $this->db->query("SELECT COALESCE(MAX(`imgid`),0)+1 as id FROM ".$bid."_loc_image")->row()->id;
   			$this->db->set('imgid',$imgid);
   			$this->db->set('locid',$locid);
   			$this->db->set('bid',$bid);
   			$this->db->set('pid',$pid);
                        $images_locarr = array();
   		foreach($_FILES['locimage']['name'] as $key=>$val){
   			$target_dir = "./uploads/";
   			$target_file = $target_dir.$_FILES['locimage']['name'][$key];
   			$target_file1 = $_FILES['locimage']['name'][$key];
   			if(move_uploaded_file($_FILES['locimage']['tmp_name'][$key],$target_file)){
   				$images_locarr[] = $target_file1;
   			}
   		}
   		if(is_array($images_locarr)){
   			foreach($images_locarr as $key => $val ){
   			   unset ($images_locarr[$key]);
   			   $images_locarr[0] = 'loc_image1';
   			   $images_locarr[1] = 'loc_image2';
   			   $images_locarr[2] = 'loc_image3';
   			   $this->db->set($images_locarr[$key],$val);
   		   }
   		   	$this->db->insert($bid."_loc_image");
   		}
   
   	return '0';
                }
                 
   	}else{
   					for($i=0;$i<sizeof($arr);$i++){
   					   if(!in_array($arr[$i],array('update_system','locid',"visited"))){
   							/* Changed for custom fields */
   							if(is_array($_POST[$arr[$i]]))
   								$val = @implode(',',$_POST[$arr[$i]]);
   							elseif($_POST[$arr[$i]]!="")
   								$val=$_POST[$arr[$i]];
   							else
   								$val='';
   							$this->db->set($arr[$i],$val);
   						}
   					}
   	  if(isset($_FILES['loc_image']) && $_FILES['loc_image']['error']==0){
   				$ext=pathinfo($_FILES['loc_image']['name'],PATHINFO_EXTENSION);
   				$newName = "Location".date('YmdHis').".".$ext;
   				move_uploaded_file($_FILES['loc_image']['tmp_name'],"./uploads/".$newName);
   				$this->db->set('loc_image',$newName);
   	  } 
   	
   					$this->db->where('locid',$locid);
   					$this->db->update($bid."_mc_location");
   			        $images_locarr = array();
   		foreach($_FILES['locimage']['name'] as $key=>$val){
   			$target_dir = "./uploads/";
   			$target_file = $target_dir.$_FILES['locimage']['name'][$key];
   			$target_file1 = $_FILES['locimage']['name'][$key];
   			if(move_uploaded_file($_FILES['locimage']['tmp_name'][$key],$target_file)){
   				$images_locarr[] = $target_file1;
   			}
   		}
   		if(is_array($images_locarr)){
   			foreach($images_locarr as $key => $val ){
   			   unset ($images_locarr[$key]);
   			   $images_locarr[0] = 'loc_image1';
   			   $images_locarr[1] = 'loc_image2';
   			   $images_locarr[2] = 'loc_image3';
   			$sql = "UPDATE `".$bid."_loc_image` SET `".$images_locarr[$key]."`=('".$val."') WHERE `locid`='".$locid."'";
   		   	$rst = $this->db->query($sql);
   		   }
   		}	
   				return '1';
   				}			
   }
   
   function getlistlocation($bid,$pid,$ofset,$limit,$id){
   		$q= '';
   		if(isset($_POST['submit'])){
   			$this->session->set_userdata('search',$_POST);
   		}
   		if($this->session->userdata('search')){
   			$s = $this->session->userdata('search');
   		}
   		$q .=(isset($s['sitename']) && $s['sitename']!='')?" AND a.locmane LIKE '%".$s['locname']."%'":"";
   		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
   					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
   		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM ".$bid."_mc_location a
   				WHERE siteid='".$id."' AND pid='".$pid."' AND a.status = '1' ORDER BY a.locid DESC 
   		        LIMIT $ofset,$limit";  
   		$rst = $this->db->query($sql)->result_array();
   		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   		$ret['count'] = $rst1->row()->cnt;
   		foreach($roleDetail['modules'] as $mod){
   			if($mod['modid']=='49'){
   				$opt_add 	= $mod['opt_add'];
   				$opt_view 	= $mod['opt_view'];
   				$opt_delete = $mod['opt_delete'];
   			}
   		}
   
   		$fieldset = $this->configmodel->getFields('49',$bid);
   		$keys = array();
   		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
   			if($opt_add || $opt_view || $opt_delete)
   			array_push($header,$this->lang->line('level_Action'));
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
   				foreach($roleDetail['system'] as $f){
   					if($f['fieldid']==$field['fieldid'])$checked = true;
   				}
   				if($checked){
   					array_push($keys,$field['fieldname']);
   					array_push($header,(($field['customlabel']!="")
   										?$field['customlabel']
   										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
   				}
   			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
   				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldKey']);
   					array_push($header,$field['customlabel']);
   				}
   			}
   		}
   
   		$ret['header'] = $header;
   		$list = array();
   		$i = $ofset+1;
   		foreach($rst as $rec){
   			$data = array($i);
   			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['locid'].'"/>';	
   			array_push($data,$v);
   			$act = '';
   if($opt_add || $opt_view || $opt_delete){
   	$act .=  '<div class="btn-group">
                      <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
       $act .= ($opt_add) ?'<li><a href="AddLocation/'.$rec['pid'].'/'.$rec['siteid'].'/'.$rec['locid'].'"><span title="Edit" class="fa fa-edit">&nbsp;&nbsp;Edit</span></a></li>':'';
   				$act .= ($opt_delete) ? '<li><a href="DeleteLocation/'.$rec['locid'].'" class="deleteClass"><span title="Delete" class="fa fa-fw fa-trash">&nbsp;&nbsp;Delete</span></a></li>':'';
   	$act .= '</ul></div>';
   	$data['action'] = $act;
   }
   			$r = $this->configmodel->getDetail('49',$rec['locid'],'',$bid);
   			foreach($keys as $k){
       if($k == 'loc_image'){
   	$img  = "<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['loc_image']."'>";
                $img .= ($r['loc_image1'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['loc_image1']."'>":'';
                $img .= ($r['loc_image2'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['loc_image2']."'>":'';
                $img .= ($r['loc_image3'] != '')?"<img class='imgset' alt='uploaded image'  height=\"75\" width=\"75\"  src='".base_url().'/uploads/'.$r['loc_image3']."'>":'';
                $v = $img;
                }else{
   					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
   	}
   	  array_push($data,$v);
   			}
   
   			$i++;
   			array_push($list,$data);
   		}
   		$ret['rec'] = $list;
   		return $ret;
   	}
   function deleteLocation($id,$bid,$beaconid){
   		$sql=$this->db->query("SELECT beaconid FROM ".$bid."_mc_location WHERE locid='".$id."'");
   if($sql->num_rows()>0){ 
   $ress=$sql->result_array();	
   $this->db->set('status','2');
   		$this->db->where('locid',$id);
   		$this->db->update($bid."_mc_location");
   		return '1';
   	} 
   }
   
   function sitevisits(){
     $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $eid = $this->session->userdata('eid');
   $roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   $q = '';
   $sql="SELECT r.usrname as name,e.empname,r.usremail as email,r.uid,ul.`liked`,r.usrnumber as number,s.sitename,s.siteid,COUNT(Distinct v.authKey) as sitecount,CAST(visit_time AS DATE) as date,CAST(visit_time as time) as time,GROUP_CONCAT(l.locname)locationname,GROUP_CONCAT(v.query)query,GROUP_CONCAT(v.Intrestedin)Intrestedin FROM visited_history v
                         LEFT JOIN ".$bid."_site s on s.siteid = v.siteid
                         LEFT JOIN mconnect_register r on r.authKey = v.authKey
                         LEFT JOIN user_likes ul on ul.siteid = v.siteid
                         LEFT JOIN ".$bid."_employee e on v.eid=e.eid
                         LEFT JOIN ".$bid."_mc_location l on l.beaconid = v.beaconid
                         WHERE v.bid='".$bid."' AND  v.eid='".$eid."' GROUP BY v.siteid,v.authKey ASC";							 
   $rst = $this->db->query($sql)->result_array();
   $rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   $ret['count'] = $rst1->row()->cnt;
   foreach($roleDetail['modules'] as $mod){
   if($mod['modid']=='48'){
   	$opt_add 	= $mod['opt_add'];
   	$opt_view 	= $mod['opt_view'];
   	$opt_delete     = $mod['opt_delete'];
   }
   }
   $header = array('#'
               ,'Action'
   			,'Sitename'
   			,'Visit date'
   			,'Visit Time'
   			,'Name'
   			,'Email'
   			,'Intrested In'
                        ,'Query'
   			,'Like'
   			,'Location visited'
   			,'Executive Name'
   			);
   $ret['header'] = $header;
   $list = array();
   $i = 1;
   foreach($rst as $rec){
   $act = '';
   if($opt_add || $opt_view || $opt_delete){
   	$act .=  '<div class="btn-group">
                      <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
   	$act .= '<li><a href="Report/followup/'.$rec['siteid'].'/0/sitevisit" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><span title="Followups" class="glyphicon glyphicon-book">&nbsp;Followups</span></a></li>';	
   	$act .= "<li><a href=\"Javascript:void(null)\" onClick=\"window.open('".base_url()."Email/compose/".$rec['uid']."', 'Counter', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\"><span title='Send Mail' class='glyphicon glyphicon-envelope'>&nbsp;SendMail</span></a></li>";
   	$act .= "<li>".anchor("Report/sendSms/".$rec['number']."/sitevisit", '<span title="Click to send SMS" class="glyphicon glyphicon-comment">&nbsp;SendSMS</span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"')."</li>";
   	$act .= "<li>".anchor("Report/converttolead/".$rec['siteid'], '<span title="Convert to lead" class="glyphicon glyphicon-share">&nbsp;ConvertToLead</span>',array('class'=>'converttolead'))."</li>";
   	$act .= "<li>".anchor("Report/mcubecalls/".$rec['number']."/48", '<span title="Mcube calls" class="fa fa-phone">&nbsp;&nbsp;&nbsp; ClickToConnect</span>',array('class'=>'mcubecalls'))."</li>";
   	$act .= '</ul></div>';
   	$ret['action'] = $act;
   }
                 
   $data = array($i
                 ,$ret['action']
   			  ,$rec['sitename']
   			  ,$rec['date']
   			  ,$rec['time']
   			  ,$rec['name']
   			  ,$rec['email']
                          ,(($rec['Intrestedin'])? ($rec['Intrestedin']):'NA')
                          ,(($rec['query'])? ($rec['query']):'NA')
                          ,(($rec['liked'] == '1')? 'Yes':(($rec['liked'] == '0')? 'No':'NA'))
   			  ,$rec['locationname']
   			  ,$rec['empname']
   			  );
   $i++;
   array_push($list,$data);
   }
   $ret['rec'] = $list;
   return $ret;
   }
   
   function getallsite(){
       $cbid=$this->session->userdata('cbid');
       $res = array();
	   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
	   $option='';
	   $option .='<option value=""> Select </option>';
	   $rst=$this->db->query("SELECT sitename,siteid,status FROM ".$bid."_site
			WHERE bid='".$bid."' AND status = 1")->result_array();
	   if(count($rst)>0){
		   foreach($rst as $re){
				$res[$re['siteid']]=$re['sitename'];
		   }
	   }
	   return $res;
	}
   	function getSitelist($val){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$rst=$this->db->query("SELECT sitename,siteid,status FROM ".$bid."_site
                                               WHERE bid='".$bid."' AND pid = '".$val."' AND status = 1")->result_array();
		return $rst;
	}
   function getpropertylist(){
   $res=array(''=>"Select");
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $rst=$this->db->query("SELECT propertyname,propertyid FROM ".$bid."_property
        WHERE bid='".$bid."' AND status = 1")->result_array();
   if(count($rst)>0){
   foreach($rst as $re){
   		$res[$re['propertyid']]=$re['propertyname'];
   }
   }
   return $res;
   }
   function addoffers($bid,$offerid){
        $arr = array_keys($_POST);
        $starttime =  $_POST['starttime'];
        $endtime = strtotime($_POST['endtime']);
        $offerper =  $_POST['offerper'];
	    $bid = $_POST['bid'];
	    $id = $_POST['siteid'];
        $sql=$this->db->query("SELECT * FROM offers WHERE siteid='".$id."'");
   if($sql->num_rows()==0){ 
	           $offerid = $this->db->query("SELECT COALESCE(MAX(`offerid`),0)+1 as id FROM offers")->row()->id;
   			   $this->db->set('offer_desc',$_POST['offer_desc']);
   			   $this->db->set('offerper',$_POST['offerper']);
   			   if(isset($_POST['starttime']))$this->db->set('starttime', date('Y-m-d H:i:s',strtotime($_POST['starttime'])));
   			   if(isset($_POST['endtime']))$this->db->set('endtime',date('Y-m-d H:i:s',strtotime($_POST['endtime'])));
   			   $this->db->set('propertyname',$_POST['propertyname']);
               $this->db->set('status',1);
               $this->db->set('bid',$bid);
               $this->db->set('offerid',$offerid);
   		       $this->db->set('siteid',$id);
   		   	   $this->db->insert("offers");
   	return '0';
                }else{
   					for($i=0;$i<sizeof($arr);$i++){
   					   if(!in_array($arr[$i],array('update_system','offerid','starttime','endtime','propertyname'))){
   							/* Changed for custom fields */
   							if(is_array($_POST[$arr[$i]]))
   							echo 	$val = @implode(',',$_POST[$arr[$i]]);
   							elseif($_POST[$arr[$i]]!="")
   							echo	$val=$_POST[$arr[$i]];
   							else
   								$val='';
   							$this->db->set($arr[$i],$val);
   						}
   					}
   					$this->db->set('offer_desc',$_POST['offer_desc']);
   				    if(isset($_POST['starttime']))$this->db->set('starttime', date('Y-m-d H:i:s',strtotime($_POST['starttime'])));
   			        if(isset($_POST['endtime']))$this->db->set('endtime',date('Y-m-d H:i:s',strtotime($_POST['endtime'])));
   					$this->db->set('propertyname',$_POST['propertyname']);
   					$this->db->where('offerid',$offerid);
   					$this->db->where('siteid',$id);
   					$this->db->update("offers");
   				return '1';
   				}			
   }
   function getlistoffers($bid,$ofset,$limit,$id){
	   $cbid=$this->session->userdata('cbid');
       $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   		$q= '';
   		if(isset($_POST['submit'])){
   			$this->session->set_userdata('search',$_POST);
   		}
   		if($this->session->userdata('search')){
   			$s = $this->session->userdata('search');
   		}
   		$q .=(isset($s['offerper']) && $s['offerper']!='')?" AND a.offerper LIKE '%".$s['offerper']."%'":"";
   		$q .=(isset($s['sitename']) && $s['sitename']!='')?" AND a.sitename LIKE '%".$s['locname']."%'":"";
   
   		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
   					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
   		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM offers a
   				WHERE  a.status = '1' AND endtime >= curdate() GROUP BY a.siteid
   		              LIMIT $ofset,$limit";  
   		$rst = $this->db->query($sql)->result_array();
   		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   		$ret['count'] = $rst1->row()->cnt;
   		foreach($roleDetail['modules'] as $mod){
   			if($mod['modid']=='50'){
   				$opt_add 	= $mod['opt_add'];
   				$opt_view 	= $mod['opt_view'];
   				$opt_delete = $mod['opt_delete'];
   			}
   		}
   
   		$fieldset = $this->configmodel->getFields('50',$bid);
   		$keys = array();
   		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
   			if($opt_add || $opt_view || $opt_delete)
   			array_push($header,$this->lang->line('level_Action'));
   		foreach($fieldset as $field){
   			$checked = false;
   			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
   				foreach($roleDetail['system'] as $f){
   					if($f['fieldid']==$field['fieldid'])$checked = true;
   				}
   				if($checked){
   					array_push($keys,$field['fieldname']);
   					array_push($header,(($field['customlabel']!="")
   										?$field['customlabel']
   										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
   				}
   			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
   				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
   				if($checked){
   					array_push($keys,$field['fieldKey']);
   					array_push($header,$field['customlabel']);
   				}
   			}
   		}
   
   		$ret['header'] = $header;
   		$list = array();
   		$i = $ofset+1;
   		foreach($rst as $rec){
   			$data = array($i);
   			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['offerid'].'"/>';	
   			array_push($data,$v);
   			$act = '';
   if($opt_add || $opt_view || $opt_delete){
   	$act .= '	<div class="btn-group">
                      <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;	
       $act .= ($opt_add) ?'<li><a href="mconnect/addoffers/'.$rec['offerid'].'"><span title="Edit" class="fa fa-edit">&nbsp;&nbsp;Edit</span></a></li>':'';
   				$act .= ($opt_delete) ? '<li><a href="mconnect/deleteoffer/'.$rec['offerid'].'" class="deleteClass"><span title="Delete" class="fa fa-fw fa-trash">&nbsp;&nbsp;Delete</span></a></li>':'';
   	$act .= '</ul></div>';
   	$data['action'] = $act;
   }
   			$r = $this->configmodel->getDetail('50',$rec['offerid'],'',$bid);
   			foreach($keys as $k){
   	if($k  == 'siteid'){
                    $v = $r['sitename'];
   	}elseif($k  == 'propertyname'){
   					$v = $r['property'];
    }else{
   					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
   	}
   	  array_push($data,$v);
   			}
   
   			$i++;
   			array_push($list,$data);
   		}
   		$ret['rec'] = $list;
   		return $ret;
   	}
   
   	function deleteoffer($bid,$offerid){
   		$sql=$this->db->query("SELECT offerid FROM offers WHERE offerid='".$offerid."'");
   if($sql->num_rows()>0){ 
   $ress=$sql->result_array();	
   		$this->db->where('offerid',$offerid);
   		$this->db->delete('offers');
   		return '1';
   	} 
   }
   	function sitereferrals(){
     $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   $q = '';
   $sql="SELECT m.usrname as name,m.usremail as email,m.usrnumber as number,m.uid,s.sitename,s.siteid,r.referedto as rname,r.referedtoemail as remail,r.referedtonum as rnumber,r.comment,CAST(date AS DATE) as date,CAST(date as time) as time FROM referrals r
                         LEFT JOIN ".$bid."_site s on s.siteid = r.siteid
                         LEFT JOIN mconnect_register m on r.authKey = m.authKey";							 
   $rst = $this->db->query($sql)->result_array();
     $rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
   $ret['count'] = $rst1->row()->cnt;
   $header = array('#'
               ,'Action'
               ,'Sitename'
   			,'Referred By'
   			,'Email'
   			,'Number'
   			,'Referred To'
   			,'Email'
   			,'Number'
                        ,'Data'
   			,'Time'
   			,'Comment'
   			);
   $ret['header'] = $header;
   $list = array();
   $i = 1;
   foreach($rst as $rec){
   $act = '';
   
   	$act .= '<div class="btn-group">
                      <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">Actions&nbsp;
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="text-align:left;">' ;
   	$act .= '<li><a href="Report/followup/'.$rec['siteid'].'/0/sitevisit" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><span title="Followups" class="glyphicon glyphicon-book">&nbsp;Followups</span></a></li>';	
   	$act .= "<li><a href=\"Javascript:void(null)\" onClick=\"window.open('".base_url()."Email/compose/".$rec['uid']."', 'Counter', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\"><span title='Send Mail' class='glyphicon glyphicon-envelope'>&nbsp;SendMail</span></a></li>";
   	$act .= "<li>".anchor("Report/sendSms/".$rec['number']."/referrals", '<span title="Click to send SMS" class="glyphicon glyphicon-comment">&nbsp;SendSMS</span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"')."</li>";
   	$act .= "<li>".anchor("Report/converttolead/".$rec['siteid'], '<span title="Convert to lead" class="glyphicon glyphicon-share">&nbsp;ConvertToLead</span>',array('class'=>'converttolead'))."</li>";
   	$act .= "<li>".anchor("Report/mcubecalls/".$rec['rnumber']."/48", '<span title="Mcube calls" class="fa fa-phone">&nbsp;&nbsp;&nbsp; ClickToConnect</span>',array('class'=>'mcubecalls'))."</li>";
   	$act .= '</ul></div>';
   	$ret['action'] = $act;
   
   $data = array($i
                 ,$ret['action']
   			  ,$rec['sitename']
   			  ,$rec['name']
   			  ,$rec['email']
   			  ,$rec['number']
   			  ,$rec['rname']
   			  ,$rec['remail']
   			  ,$rec['rnumber']
   			  ,$rec['date']
   			  ,$rec['time']
   			  ,$rec['comment']
   			  );
   $i++;
   array_push($list,$data);
   }
   $ret['rec'] = $list;
   return $ret;
   }
   function getuserdetail($id){
   $sql="SELECT  usrname as name,usremail as email,usrnumber as number FROM mconnect_register where uid='".$id."'";
   $rst = (array)$this->db->query($sql)->row();
   return $rst;
   }
   
   function site_employee(){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $query=$this->db->query("select eid,empname from ".$bid."_employee where status=1 ORDER BY empname");
   $roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
   $q='';
   $q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
   $query ="SELECT * FROM ".$bid."_employee WHERE status='1' ORDER BY empname";
   $query=$this->db->query($query);
   $res['']='Select Employee';
   if($query->num_rows()>0){
   foreach($query->result_array() as $rt)
   $res[$rt['eid']]=$rt['empname'];
   }
   return $res;
   }
   
   function addemp_site($pid,$siteid){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $err=0;
   $cnt = '0';
   foreach($_POST['emp_ids'] as $eids){
   $check=$this->db->query("SELECT * FROM ".$bid."_site_emp WHERE eid=".$eids." AND siteid='".$siteid."' AND pid='".$pid."'");
   if($check->num_rows()==0){
   	$err++;
   	$this->db->set('bid', $bid);                       
   	$this->db->set('siteid',$siteid);                       
   	$this->db->set('eid', $eids); 
   	$this->db->set('pid', $pid); 
   	$this->db->set('starttime', $this->input->post('starttime'.$eids));                       
   	$this->db->set('endtime', $this->input->post('endtime'.$eids));                       
   	$this->db->set('status',1);
   	$this->db->set('callcounter',$cnt);
   	if($this->input->post('isfailover'.$eids)){
   		$this->db->set('isfailover',$this->input->post('isfailover'.$eids));	
   	} 
   	$this->db->insert($bid."_site_emp");
   	$emp_name=$this->get_empname($eids);
   	$gname=$this->db->query("SELECT sitename from 	".$bid."_site where siteid='".$siteid."'")->row()->sitename;
   	$this->auditlog->auditlog_info('Site Executive',$emp_name->empname." added to the site ".$gname);
   }
   }
   if($err!=0){
   return 1;
   }else{
   return 0;
   }
   }
   function get_empname($eid){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $sql=$this->db->query("select * from ".$bid."_employee where eid=$eid");
   $res=$sql->row();
   return $res;
   }
   function site_enteremplist($siteid){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $res=array();
   $res1=$this->db->query("SELECT SQL_CALC_FOUND_ROWS a.empnumber,
   					a.starttime,a.endtime,g.sitename,e.empname,a.eid,a.siteid ,
   					if(a.isfailover=1,'yes','no') as failover,a.status
   					FROM ".$bid."_site_emp a
   					LEFT JOIN ".$bid."_site g on a.siteid=g.siteid
   					LEFT JOIN ".$bid."_employee e on a.eid=e.eid
   					WHERE a.siteid=$siteid
   				   ");
   if($res1->num_rows()>0){					   
   foreach($res1->result_array() as $row){
   	$res[]=$row['eid'];
   }
   }
   return $res;
   }
   function getSiteEmpDetail($siteid='',$eid=''){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $res=array();
   $res=$this->db->query("SELECT a.*,e.empname
   				   FROM ".$bid."_site_emp a
   				   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
   				   WHERE a.siteid='".$siteid."' and a.eid='".$eid."'")->result_array();
   return $res['0'];
   }
   
   function siteemplist($pid,$siteid,$ofset='0',$limit='20'){
   $q='';
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   if(isset($_POST['submit'])){
   $this->session->set_userdata('search',$_POST);
   }else{
   $this->session->unset_userdata('search');
   }
   if($this->session->userdata('search')){
   $s = $this->session->userdata('search');
   }
   
   $q= (isset($s['empname']) && $s['empname']!='')?" AND e.empname like '%".$s['empname']."%'":"";
   $res=array();
   $sql=           "SELECT  e.empnumber,g.pid,
   					a.starttime,a.endtime,g.sitename,a.callcounter,e.empname,a.eid,a.siteid ,
   					if(a.isfailover=1,'Yes','No') as failover,a.status,
   					if(e.status='0',0,if(e.selfdisable='1','0','1')) as estatus
   					FROM ".$bid."_site_emp a
   					LEFT JOIN ".$bid."_site g ON a.siteid=g.siteid
   					LEFT JOIN ".$bid."_employee e ON a.eid=e.eid
   					WHERE a.siteid=".$siteid." AND  a.pid=".$pid." $q
   					ORDER BY e.empname
   					 LIMIT $ofset,$limit";
       $res['data']=$this->db->query($sql)->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
    return $res;
   }
   function refreshcounter($siteid,$bid){
   $sql=$this->db->query("UPDATE ".$bid."_site_emp SET callcounter = 0 WHERE siteid=$siteid");
   if($this->db->affected_rows() >0){
   $this->auditlog->auditlog_info('Site',$siteid." Call Counter Reset By ".$this->session->userdata('username'));
   return 1;
   }
   else
   return 0;
   }
   function editemp_site($pid,$siteid){
   $cbid=$this->session->userdata('cbid');
   $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
   $cnt =$this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_site_emp WHERE siteid='".$siteid."' AND pid='".$pid."'")->row()->cnt;
   $sql = "UPDATE ".$bid."_site_emp SET
   	starttime	= '".$_POST['starttime']."'
   	,endtime	= '".$_POST['endtime']."'
   	,callcounter= '".$cnt."'
   	,isfailover	= '".(isset($_POST['isfailover'])?$_POST['isfailover']:0)."'
   	WHERE siteid		= '".$siteid."'
   	AND  eid		= '".$_POST['empid']."'
   	AND  pid		= '".$_POST['pid']."'";
   $this->db->query($sql);
   $emp_name=$this->get_empname($_POST['empid']);
   $gname=$this->db->query("SELECT sitename FROM ".$bid."_site WHERE siteid='".$siteid."' AND pid='".$pid."'")->row()->sitename;
   $this->auditlog->auditlog_info('Site Employee',$emp_name->empname." weight changed for the site ".$gname);
   return 1;
   }
   	function dis_site_employee($pid,$siteid,$eid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_site_emp where eid=".$eid." and siteid=$siteid")->row_array();	
		$status=($check['status']==0)?'1':'0';
		$cnt = $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_site_emp WHERE siteid='".$siteid."' AND  pid='".$pid."'")->row()->cnt;
		$this->db->set('callcounter',$cnt);
		$this->db->set('status',$status);
		$cnt = $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_site_emp WHERE siteid='".$siteid."' AND  pid='".$pid."'")->row()->cnt;
        $this->db->set('callcounter',$cnt);
		$this->db->where('eid',$eid);	
		$this->db->where('pid',$pid);	
		$this->db->where('siteid',$siteid);
		$this->db->update($bid.'_site_emp');
		$itemDetail= $this->configmodel->getDetail('48',$siteid,'',$bid);
		$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		$text=($status)?" Enabled":" Disabled";
		$this->auditlog->auditlog_info('Site Employee', $empDetail['empname'].$text." from the site ".$itemDetail['sitename']);
		return $status;
	}
   }
   ?>
