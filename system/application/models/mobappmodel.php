<?php
class Mobappmodel extends Model
{
    
    function Mobappmodel()
    {
        parent::Model();
        $this->load->model('configmodel', 'CM');
        $this->load->model('empmodel', 'EMPM');
        $this->load->model('systemmodel', 'SYS');
        $this->load->dbforge();
    }
    function mob_auth($data)
    {
        $sql  = "SELECT usrnumber FROM mconnect_register WHERE usrnumber='" . $data['number'] . "'";
        $rest = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            return false;
        } else {
	    $sql1  = "SELECT number FROM mconnect_numotp WHERE number='" . $data['number'] . "'";
        $rest = $this->db->query($sql1);
        if ($rest->num_rows() > 0) {
			$sql  = "UPDATE mconnect_numotp SET otp ='" . $data['otp'] . "' WHERE number='" . $data['number'] . "'";
            $ret = $this->db->query($sql);
		}else{
            $id  = $this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM mconnect_numotp")->row()->id;
            $sql = "INSERT INTO mconnect_numotp SET  id	='" . $id . "', number ='" . $data['number'] . "',otp ='" . $data['otp'] . "'";
            $ret = $this->db->query($sql);
		}
            $sql = "SELECT otp FROM mconnect_numotp WHERE  number='" . $data['number'] . "'";
            $rst = $this->db->query($sql);
            if ($rst->num_rows() > 0) {
                $otp = $rst->row()->otp;
            }
            return $otp;

    }
}
    function authenticate()
    {
        $otp    = $_POST['otp'];
        $number = $_POST['number'];
        $retArr = array();
        $sql    = "SELECT otp FROM mconnect_numotp WHERE otp = " . $otp . " AND number = " . $number . "";
        $rst    = $this->db->query($sql);
        if ($rst->num_rows() > 0) {
            $retArr = $rst->row_array();
            if ($retArr['otp'] == $otp) {
                $sql = "DELETE FROM mconnect_numotp WHERE otp ='" . $otp . " AND number = " . $number . "";
                $del = $this->db->query($sql);
                return $retArr['otp'];
            }
        } else {
            return false;
        }
    }
    function register()
    {
        $sql  = "SELECT usremail,usrnumber FROM mconnect_register WHERE usrnumber='" . $_POST['number'] . "' AND usremail='" . $_POST['email'] . "'";
        $rest = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            return '1';
        } else {
            $authkey = "1.1." . substr(md5(uniqid(rand(), true)), 0, 13) . "";
            $id      = $this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as uid FROM mconnect_register")->row()->uid;
            $sql     = "INSERT INTO mconnect_register SET  uid	='" . $id . "',usrname ='" . $_POST['name'] . "', usremail ='" . $_POST['email'] . "',usrnumber ='" . $_POST['number'] . "',password =md5('" . $_POST['password'] . "'), authkey ='" . $authkey . "',keyword ='mobileapp'";
            $ret     = $this->db->query($sql);
        return '2';
        }
    }
    function loginDetails()
    {
        $retArr = array();
        $sql    = "SELECT * FROM mconnect_register WHERE usremail='" . $_POST['email'] . "' AND password=md5('" . $_POST['password'] . "')";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        }
        return $retArr;
    }
    function forgot_pws($data)
    {
        $sql    = "UPDATE mconnect_register SET otp='" . $data['otp'] . "' WHERE usrnumber='" . $_POST['number'] . "'";
        $rest   = $this->db->query($sql);
        $sql    = "SELECT usrnumber FROM mconnect_register WHERE usrnumber='" . $_POST['number'] . "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            return true;
        }
    }
    function changepwd()
    {
        $retArr = array();
        $sql    = "SELECT otp FROM mconnect_register WHERE otp='" . $_POST['otp'] . "' AND usrnumber='" . $_POST['number'] . "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $sql  = "UPDATE mconnect_register SET password=md5('" . $_POST['password'] . "') WHERE usrnumber='" . $_POST['number'] . "' AND otp='" . $_POST['otp'] . "'";
            $rest = $this->db->query($sql);
            return true;
        } else {
            return false;
        }
    }
    
    function checkvisit() {
        $retArr   = array();
        $retArr1  = array();
        $authKey  = $_POST['authkey'];
        $beaconid = $_POST['beaconid'];
	    $eid= (isset($_POST['eid']))?$_POST['eid']:"";
        $sql      = "SELECT `business`,`pid`,`siteid` FROM beacon  WHERE beacon_id ='" . $beaconid . "'";
        $rest     = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        }
        $bid    = $retArr['business'];
        $siteid = $retArr['siteid'];
        $pid = $retArr['pid'];
        // IF site is visited or Not
        $sql  = "SELECT * FROM visited_history WHERE authKey = '" . $authKey . "' AND siteid = '" . $siteid . "'  AND pid = '" . $pid . "' AND eid = '" . $eid . "'";
        $rest = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            // IF location is visited or Not
            $sql  = "SELECT * FROM visited_history WHERE authKey = '" . $authKey . "' AND siteid = '" . $siteid . "'  AND pid = '" . $pid . "' AND beaconid = '" . $beaconid . "'";
            $rest = $this->db->query($sql);
            if ($rest->num_rows() > 0) {
                $sql  = "SELECT * FROM visited_history WHERE authKey = '" . $authKey . "' AND pid = '" . $pid . "' AND siteid = '" . $siteid . "' AND beaconid = '" . $beaconid . "' AND detail_sent = '0' ";
                $rest = $this->db->query($sql);
                if ($rest->num_rows() > 0) {
                    $sql  = "SELECT o.beaconid,s.sitename AS 'name',s.bid,s.pid,s.siteid,s.siteicon as 'logo',n.landingnumber as 'number', o.locname AS 'lname',o.loc_desc AS 'desc',o.loc_image as 'media',ul.liked as 'like' FROM " . $bid . "_mc_location o
					   LEFT JOIN  " . $bid . "_site s ON s.siteid = o.siteid
					   LEFT JOIN  prinumbers n ON n.number = s.tracknum
					   LEFT JOIN  user_likes ul ON ul.siteid = s.siteid
					   LEFT JOIN  visited_history v ON s.siteid = v.siteid
					   WHERE o.beaconid ='" . $beaconid . "' AND v.authKey = '" . $authKey . "'";
                    $rest = $this->db->query($sql);
                    if ($rest->num_rows() > 0) {
                        $retArr1 = $rest->row_array();
                    }
                 
                    foreach ($retArr1 as $key => $val) {
                        if ($key == 'media') {
                            $sql = "SELECT s.sitemedia as 'video',l.loc_image,i.loc_image1,i.loc_image2,i.loc_image3 FROM " . $bid . "_loc_image i
			   LEFT JOIN " . $bid . "_mc_location l ON i.locid = l.locid 
			   LEFT JOIN  " . $bid . "_site s ON s.siteid = l.siteid
	           WHERE l.siteid	='" . $siteid . "'";
                            $res = $this->db->query($sql);
                            if ($res->num_rows() > 0) {
                                $retArr2 = $res->row_array();
                            }
                            $img              = array();
                            $rst              = substr($retArr2['video'], 0, 4);
                            $img['img']       = ($retArr1['media']) ? "" . base_url() . 'uploads/' . $retArr1['media'] . "" : '';
                            $img['img1']      = ($retArr2['loc_image1']) ? "" . base_url() . 'uploads/' . $retArr2['loc_image1'] . "" : '';
                            $img['img2']      = ($retArr2['loc_image2']) ? "" . base_url() . 'uploads/' . $retArr2['loc_image2'] . "" : '';
                            $img['img3']      = ($retArr2['loc_image3']) ? "" . base_url() . 'uploads/' . $retArr2['loc_image3'] . "" : '';
                            $img['video']     = ($retArr2['video']) ? (($rst == 'http') ? "" . $retArr2['video'] . "" : '') : '';
                            $retArr1['media'] = $img;
                        }
                        if ($key == 'logo') {
                            $img             = "" . base_url() . 'uploads/' . $retArr1['logo'] . "";
                            $retArr1['logo'] = $img;
                        }
                    }  
                    if ($key == 'like') {
                          $retArr1['like'] =  ($val == 1)? '1' : '0';
                        }
                       
                    $this->db->set('detail_sent', '1');
                    $this->db->where('siteid', $siteid);
                    $this->db->update("visited_history");
                    $retArr1['id'] = '2';
                    return $retArr1;
                } else {
                    return 'detailsent';
                }
            } else {
                $sql  = "SELECT sitename,siteicon,sitedesc,tracknum FROM ".$bid."_site WHERE siteid	='".$siteid ."'";
                $rest = $this->db->query($sql);
                if ($rest->num_rows() > 0) {
                    $retA = $rest->row_array();
                }
                $this->db->set('bid', $bid);
                $this->db->set('siteid', $siteid);
                $this->db->set('sitename', $retA['sitename']);
                $this->db->set('siteicon', $retA['siteicon']);
                $this->db->set('sitedesc', $retA['sitedesc']);
                $this->db->set('tracknum', $retA['tracknum']);
                $this->db->set('authKey', $authKey);
                $this->db->set('beaconid', $beaconid);
                $this->db->set('eid', $eid);
                $this->db->set('pid', $pid);
                $this->db->set('site_visited', $beaconid);
                $this->db->set('detail_sent', '0');
                $this->db->insert("visited_history");
                return 'enterdetail';
            }
        } else {
            $sql  = "SELECT sitename,siteicon,sitedesc,tracknum,CONCAT_WS(',',intrestopt, intrestopt1, intrestopt2,intrestopt3) as opt FROM ".$bid ."_site WHERE siteid ='" .$siteid."'";
            $rest = $this->db->query($sql);
            if ($rest->num_rows() > 0) {
                $retA = $rest->row_array();
            }
            $valres = array();
            $val = $retA['opt'];
            $this->db->set('bid', $bid);
            $this->db->set('siteid', $siteid);
            $this->db->set('sitename', $retA['sitename']);
            $this->db->set('siteicon', $retA['siteicon']);
            $this->db->set('sitedesc', $retA['sitedesc']);
            $this->db->set('tracknum', $retA['tracknum']);
            $this->db->set('authKey', $authKey);
            $this->db->set('beaconid', $beaconid);
            $this->db->set('eid', $eid);
            $this->db->set('pid', $pid);
            $this->db->set('site_visited', $beaconid);
            $this->db->insert("visited_history");
            if($val == ""){
            return 'enterdetail'; 
		    }else{
		    $valr= explode(',', $val);
		    $keys = array();
		    for($i=1;$i<=sizeof($valr);$i++){
		     $keys[$i] =$i;
		    }
		    $valres['data']=array_combine($keys, $valr);
            $valres['id'] = '1';
            $valres['siteid'] = $siteid;
            $valres['beaconid'] = $beaconid;
            return $valres;
		 }
        }
    }

    function visitlist()
    {
        $authKey = $_POST['authkey'];
        $retArr  = array();
        $sql     = "SELECT v.sitename,v.siteid,v.bid,v.siteicon,v.sitedesc,ul.liked as `like`,n.landingnumber as 'number',a.offerper,a.offer_desc FROM visited_history v
	     LEFT JOIN  prinumbers n ON n.number = v.tracknum
	     LEFT JOIN  offers a ON a.siteid = v.siteid
	     LEFT JOIN  user_likes ul ON ul.siteid = v.siteid
	     WHERE v.authKey ='" . $authKey . "' GROUP BY v.siteid";
        $rest    = $this->db->query($sql)->result_array();
        foreach ($rest as $key => $value) {
            $rest[$key]['like'] = ($value['like'] == 1)? '1' : '0';
            $rest[$key]['siteicon'] = "" . base_url() . 'uploads/' . $value['siteicon'] . ""; 
        }
        return $rest;
    }
    function sendlocation()
    { 
        $cbid=$this->session->userdata('cbid');
        $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
        $siteid = $_POST['siteid'];
        $sql    = "SELECT business,beacon_id FROM beacon WHERE siteid ='" . $siteid . "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr2 = $rest->row_array();
        }
        $bid = $retArr2['business'];
        $sql = "SELECT m.bid,m.beaconid as 'id',m.locname as 'name' FROM " . $bid . "_mc_location m
		        WHERE m.siteid ='" . $siteid . "'";
        $retArr1['site'] = $this->db->query($sql)->result_array();
        $sql = "SELECT  s.sitemedia,s.sitedesc FROM " . $bid . "_site s
		        WHERE s.siteid ='" . $siteid . "'";
		$rest   = $this->db->query($sql);
	    if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        }
           $res = substr($retArr['sitemedia'], 0, 4);
           $retArr1['sitemedia'] = ($retArr['sitemedia']) ? (($res == 'http') ? "" . $retArr['sitemedia'] . "" : '') : '';
           $retArr1['sitedesc']  = $retArr['sitedesc'];
        return $retArr1;
    }
    function locdetail_offline()
    {
        $beaconid = $_POST['beaconid'];
        $bid      = $_POST['id'];
        $sql    = "SELECT business,siteid FROM beacon WHERE beacon_id ='" . $beaconid . "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        }
        $siteid = $retArr['siteid'];
        $sql      = "SELECT o.beaconid,.s.sitename AS 'name',s.siteid,s.bid,s.siteicon as 'logo',ul.liked as `like`,n.landingnumber as 'number', o.locname AS 'lname',o.loc_desc AS 'desc',o.loc_image as 'media'  FROM " . $bid . "_mc_location o
	       LEFT JOIN  " . $bid . "_site s ON s.siteid = o.siteid
	       LEFT JOIN  prinumbers n ON n.number = s.tracknum
	       LEFT JOIN  user_likes ul ON ul.siteid = s.siteid
	       LEFT JOIN  visited_history v ON s.siteid = v.siteid
	       WHERE o.beaconid ='" . $beaconid . "'";
        $rest     = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr1 = $rest->row_array();
        }
        foreach ($retArr1 as $key => $val) {
            if ($key == 'media') {
                $sql = "SELECT s.sitemedia as 'video',l.loc_image,i.loc_image1,i.loc_image2,i.loc_image3 FROM " . $bid . "_loc_image i
			   LEFT JOIN " . $bid . "_mc_location l ON i.locid = l.locid 
			   LEFT JOIN  " . $bid . "_site s ON s.siteid = l.siteid
	                 WHERE l.beaconid	='" . $beaconid . "'";
                $res = $this->db->query($sql);
                if ($res->num_rows() > 0) {
                    $retArr2 = $res->row_array();
                }
                $img              = array();
                $rst              = substr($retArr2['video'], 0, 4);
                $img['img']       = ($retArr1['media']) ? "" . base_url() . 'uploads/' . $retArr1['media'] . "" : '';
                $img['img1']      = ($retArr2['loc_image1']) ? "" . base_url() . 'uploads/' . $retArr2['loc_image1'] . "" : '';
                $img['img2']      = ($retArr2['loc_image2']) ? "" . base_url() . 'uploads/' . $retArr2['loc_image2'] . "" : '';
                $img['img3']      = ($retArr2['loc_image3']) ? "" . base_url() . 'uploads/' . $retArr2['loc_image3'] . "" : '';
                $img['video']     = ($retArr2['video']) ? (($rst == 'http') ? "" . $retArr2['video'] . "" : '') : '';
                $retArr1['media'] = $img;
            }
            if ($key == 'logo') {
                $img             = "" . base_url() . 'uploads/' . $retArr1['logo'] . "";
                $retArr1['logo'] = $img;
            }
        }
        return $retArr1;
    }
    function getallimages(){
        $siteid      = $_POST['siteid'];
        $sql    = "SELECT business,beacon_id FROM beacon WHERE siteid ='" . $siteid . "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        }
        $beaconid = $retArr['beacon_id'];
        $bid = $retArr['business'];
        $sql      = "SELECT l.loc_image,i.loc_image1,i.loc_image2,i.loc_image3 FROM " . $bid . "_loc_image i
                     LEFT JOIN " . $bid . "_mc_location l ON i.locid = l.locid 
	                 WHERE l.siteid	='" . $siteid . "'";
        $res = $this->db->query($sql)->result_array();
        $newArray = array();
        $i = 0;
			foreach($res as $subArray){
				foreach($subArray as $key => $val){
					($val)?($newArray['img'.$i] ="" . base_url() . 'uploads/' . $val . ""):'';
					$i++;
				}
			}
        return $newArray;
    }
    function deletevisitlist()
    {
        $authKey = $_POST['authkey'];
        $siteid = $_POST['siteid'];
        $retArr  = array();
        $sql     = "DELETE FROM visited_history WHERE authKey ='" . $authKey . "' AND siteid ='" . $siteid . "'";
        $rest    = $this->db->query($sql);
        return $rest;
    }
    function likesite()
    {
        $authKey = $_POST['authkey'];
        $siteid = $_POST['siteid'];
        $bid = $_POST['bid'];
        $pid = $_POST['pid'];
        $sql    = "SELECT * FROM user_likes WHERE siteid ='" . $siteid . "' AND authKey ='" . $authKey . "' AND bid ='" . $bid . "'  AND pid ='" . $pid . "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        if($retArr['liked'] == '1'){
        $sql     = "DELETE FROM user_likes WHERE authKey ='" . $authKey . "' AND siteid ='" . $siteid . "' AND bid ='" . $bid . "'  AND pid ='" . $pid . "'";
        $rest    = $this->db->query($sql);
	    return false;
	    }else{
	    $this->db->set('liked',1);
		$this->db->where('authKey',$authKey);
		$this->db->where('siteid',$siteid);
		$this->db->where('bid',$bid);
		$this->db->where('pid',$pid);
		$this->db->update("user_likes");
		return true;
		}
	}else{
		   $this->db->set('liked',1);
		   $this->db->set('pid',$pid);
		   $this->db->set('authKey',$authKey);
		   $this->db->set('siteid',$siteid);
		   $this->db->set('bid',$bid);
		   $this->db->insert("user_likes");
		   return true;
		}
    }
    function profileupdate()
    {
        $sql    = "SELECT * FROM mconnect_register WHERE authKey ='" .$_POST['authkey']. "'";
        $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
		if(isset($_POST['dob'])){
			$dob=(isset($_POST['dob']))?$_POST['dob']:"";
			$this->db->set('dob',$dob);
		}
		if(isset($_POST['name'])){
			$name=(isset($_POST['name']))?$_POST['name']:"";
			$this->db->set('usrname',$name);
		}
		if(isset($_POST['password']) && ($_POST['password']) != ""){
			$password=(isset($_POST['password']))?md5($_POST['password']):"";
			$this->db->set('password',$password);
		}
		if(isset($_POST['email'])){
			$email=(isset($_POST['email']))?$_POST['email']:"";
			$this->db->set('usremail',$email);
		}
		if(isset($_POST['gender'])){
			$gender=(isset($_POST['gender']))?$_POST['gender']:"";
			$this->db->set('gender',$gender);
		}
		if(isset($_POST['image'])){
			$imgname = $_POST['name'];
			$imgname = preg_replace("/[^a-zA-Z]+/", "", $imgname);
			$imgname = $imgname.substr(md5(uniqid(rand(), true)), 0, 5).'.jpg';
			$imsrc = $_POST['image'];
			$fp = fopen('./uploads/'.$imgname, 'w');
			fwrite($fp, $imsrc);
			$image=(isset($_POST['image']))?$imsrc:"";
			$this->db->set('image',$image);
		}
		$this->db->where('usremail',$email);
		$this->db->update("mconnect_register");
	    return true;
	    }
    }
    function getprofiledetail()
    {
        $authKey = $_POST['authkey'];
        $retArr  = array();
        $sql     = "SELECT usrname as 'username',usremail as'email',dob ,image as 'img',gender FROM mconnect_register
	                WHERE authKey ='" . $authKey . "'";
	    $rest   = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            $retArr = $rest->row_array();
        }
        return $retArr;
    }
    function getlikes()
    {
        $authkey = $_POST['authkey'];
        $sql="SELECT liked as `like`,bid,siteid FROM `user_likes` WHERE authkey ='". $authkey."'";
        $rst = $this->db->query($sql)->result_array();
         $rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
   		if($rst1 > 0){
	    foreach ($rst as $key => $value){
		$bid = $rst[$key]['bid']; 
		$siteid = $rst[$key]['siteid']; 
		  $sql   = "SELECT v.sitename,v.siteid,v.bid,v.siteicon,v.sitedesc,ul.liked as `like`,a.offerper,a.offer_desc,n.landingnumber as 'number' FROM ".$bid."_site v
	     LEFT JOIN  prinumbers n ON n.number = v.tracknum
	     LEFT JOIN  offers a ON a.siteid = v.siteid
	     LEFT JOIN  user_likes ul ON ul.siteid = v.siteid
	     WHERE v.siteid ='". $siteid."' AND ul.authkey ='". $authkey."'";
	     $rest    = $this->db->query($sql)->result_array();
	     foreach ($rest as $key => $value) {
			$rest[$key]['like'] =($value['like'])? '1' : '0';
            $rest[$key]['siteicon'] = "" . base_url() . 'uploads/' . $value['siteicon'] . "";
	     }
	             $data[] = $rest;

	    }
	    $size = sizeof($data);
        $newarray = array();
        for($i=0;$i<$size; $i++){
        $newarray = array_merge($newarray,$data[$i]);
	    }
        return $newarray;
      }else{
		return false;
	}
}
    
    function userintrest(){
                $siteid = $_POST['siteid'];
                $authkey = $_POST['authkey'];
                $beaconid= $_POST['beaconid'];
                $sql     = "SELECT v.query FROM visited_history v
	         WHERE v.authKey ='". $authkey."' AND v.beaconid ='". $beaconid."'";
                  $rest   = $this->db->query($sql);
                    if ($rest->num_rows() > 0) {
                        $retArr = $rest->row_array();
                    }
                if(isset($_POST['query'])){
                if(empty($retArr['query'])){
                   $query=(isset($_POST['query']))?$_POST['query']:"";
                   $this->db->set('query',$query);
                } else{
			$query=(isset($_POST['query']))?$_POST['query']:"";
                        $this->db->set("query", "CONCAT( query, ',".$query."' )", false);
		}
                }
                 if(isset($_POST['intrestedin']) && $_POST['intrestedin'] != ""){
		    $intrestedin=(isset($_POST['intrestedin']))?$_POST['intrestedin']:"";
                        $this->db->set('Intrestedin',$intrestedin);
                       
      }
		$this->db->where('authKey',$authkey);
		$this->db->where('beaconid',$beaconid);
        $this->db->where('siteid',$siteid);
		$this->db->update("visited_history");
		return true;
    }
     function getoffers()
    {    
	     $limit = $_POST['limit'];
        $offset = $_POST['offset'];
         $authkey = $_POST['authkey'];
		$data= array();
		$sql = "SELECT bid,siteid FROM offers";
		$rst = $this->db->query($sql)->result_array();
	    foreach ($rst as $key => $value){
		$bid = $rst[$key]['bid']; 
		$siteid = $rst[$key]['siteid']; 
		  $sql   = "SELECT v.sitename,v.siteid,v.bid,v.siteicon,v.sitedesc,ul.liked as `like`,a.offerper,a.offer_desc,n.landingnumber as 'number' FROM ".$bid."_site v
	     LEFT JOIN  prinumbers n ON n.pri = v.tracknum
	     LEFT JOIN  offers a ON a.siteid = v.siteid
	     LEFT JOIN  user_likes ul ON ul.siteid = v.siteid AND ul.authkey ='". $authkey."'
	     WHERE v.siteid ='". $siteid."' AND a.status = '1' AND a.endtime >= curdate()
	     GROUP BY v.siteid ORDER BY offerid DESC";
	     $rest    = $this->db->query($sql)->result_array();
	     foreach ($rest as $key => $value) {

			$rest[$key]['like'] =($value['like'])? '1' : '0';
            $rest[$key]['number'] =($value['number'])? $value['number'] : "1";
            $rest[$key]['siteicon'] = "" . base_url() . 'uploads/' . $value['siteicon'] . "";
	     }
	             $data[] = $rest;
		}
        $size = sizeof($data);
        $newarray = array();
        for($i=0;$i<$size; $i++){
        $newarray = array_merge($newarray,$data[$i]);
	    }
          $this->tempoffers($limit,$offset,$newarray);
		  $sql     = "SELECT * FROM tempoffers LIMIT ".$limit." OFFSET ".$offset."";
		  $rst = $this->db->query($sql)->result_array();
		  return $rst;
  
}
	 function tempoffers($limit,$offset,$newarray){
		 $sql = "SELECT * FROM tempoffers";
         $rest   = $this->db->query($sql);
		 if ($rest->num_rows() > 0) {
			 $this->db->truncate('tempoffers');
		foreach($newarray as $row){
		 $this-> db->insert('tempoffers', $row);
	 }
		 }else{
			 foreach($newarray as $row){
		 $this-> db->insert('tempoffers', $row);
	 }
		 }
	}
  //~ function getallproperty()
    //~ {
        //~ $authkey = $_POST['authkey'];
        //~ $limit = $_POST['limit'];
        //~ $offset = $_POST['offset'];
	    //~ $data = array(); 
        //~ $sql     = "SELECT DISTINCT(bid) FROM business_feature WHERE feature_id = 17";
		//~ $rst = $this->db->query($sql)->result_array();
		//~ foreach ($rst as $key => $value) {
         //~ $bid = $rst[$key]['bid'];
         //~ $sql     = "SELECT v.propertyname,v.propertyicon,v.propertyid,ul.authKey,ul.liked as `like`,a.offerper,a.offer_desc FROM  ".$bid."_property v
	     //~ LEFT JOIN  offers a ON a.pid = v.propertyid
	     //~ LEFT JOIN  user_likes ul ON ul.pid = v.propertyid AND ul.authkey ='". $authkey."'
	     //~ GROUP BY  v.propertyid ORDER BY v.time ASC  LIMIT ".$limit." OFFSET ".$offset."";
	     //~ 
        //~ $rest    = $this->db->query($sql)->result_array();
      //~ 
        //~ foreach ($rest as $key => $value) {
		    //~ if($value['authKey'] == $authkey){
			//~ $rest[$key]['like'] =($value['like'])? '1' : '0';
		    //~ }else{
		     //~ $rest[$key]['like'] ="";
			//~ }
			//~ $rest[$key]['offerper'] =($value['offerper'])? $value['offerper'] : "";
			//~ $rest[$key]['offer_desc'] =($value['offer_desc'])? $value['offer_desc'] : "";
            //~ $rest[$key]['propertyicon'] = "" . base_url() . 'uploads/' . $value['propertyicon'] . "";
        //~ }
//~ 
        //~ }
		  //~ return $rest;
    //~ }
	//~ 
     function getallsites()
    {
        $authkey = $_POST['authkey'];
        $limit = $_POST['limit'];
        $offset = $_POST['offset'];
	    $data = array(); 
        $sql     = "SELECT DISTINCT(bid) FROM business_feature WHERE feature_id = 17";
		$rst = $this->db->query($sql)->result_array();
		foreach ($rst as $key => $value) {
         $bid = $rst[$key]['bid'];
         $sql     = "SELECT v.sitename,v.pid,p.propertyname,v.time,v.bid,v.siteid,v.siteicon,v.sitedesc,ul.authKey,ul.liked as `like`,a.offerper,a.offer_desc,n.landingnumber as 'number' FROM  ".$bid."_site v
	     LEFT JOIN  prinumbers n ON n.pri = v.tracknum
	     LEFT JOIN  offers a ON a.siteid = v.siteid
	     LEFT JOIN   ".$bid."_property p ON p.propertyid = v.pid
	     LEFT JOIN  user_likes ul ON ul.siteid = v.siteid AND ul.authkey ='". $authkey."'
	     GROUP BY  v.siteid ORDER BY v.time ASC";
        $rest    = $this->db->query($sql)->result_array();
        foreach ($rest as $key => $value) {
		    if($value['authKey'] == $authkey){
			$rest[$key]['like'] =($value['like'])? '1' : '0';
		    }else{
		     $rest[$key]['like'] ="";
			}
			$rest[$key]['number'] =($value['number'])? $value['number'] : "1";
			$rest[$key]['offerper'] =($value['offerper'])? $value['offerper'] : "";
			$rest[$key]['offer_desc'] =($value['offer_desc'])? $value['offer_desc'] : "";
            $rest[$key]['siteicon'] = "" . base_url() . 'uploads/' . $value['siteicon'] . "";
        }
        $data[] = $rest;
        }
        $size = sizeof($data);
        $newarray = array();
        for($i=0;$i<$size; $i++){
        $newarray = array_merge($newarray,$data[$i]);
	    }
	    usort($newarray,function ($a,$b){
			$t1 = strtotime($a['time']);
			$t2 = strtotime($b['time']);
			if ($t1 == $t2) {
				return 0;
			}
			return ($t1 < $t2) ? 1 : -1;
        });
          $this->tempsite($limit,$offset,$newarray);
		  $sql     = "SELECT * FROM tempsite LIMIT ".$limit." OFFSET ".$offset."";
		  $rst = $this->db->query($sql)->result_array();
		  return $rst;
    }
    function tempsite($limit,$offset,$newarray){
		 $sql = "SELECT * FROM tempsite";
         $rest   = $this->db->query($sql);
		 if ($rest->num_rows() > 0) {
			 $this->db->truncate('tempsite');
		foreach($newarray as $row){
		 $this-> db->insert('tempsite', $row);
	     }
		 }else{
			 foreach($newarray as $row){
		 $this-> db->insert('tempsite', $row);
	 }
		 }
	}
    function refer()
    { 
		$siteid = $_POST['siteid'];
        $authkey = $_POST['authkey'];
        $number = $_POST['number'];
        $pid = $_POST['pid'];
        $sql     = "SELECT * FROM referrals r
	         WHERE r.authKey ='". $authkey."' AND r.siteid ='". $siteid."' AND r.referedtonum ='". $number."' AND r.pid = '". $pid."'";
                  $rest   = $this->db->query($sql);
		 if ($rest->num_rows() > 0) {
                   return false;
         }else{
              $sql = "INSERT INTO referrals SET  authKey	='" . $authkey . "', siteid ='" . $siteid . "',referedto ='" .$_POST['name'] . "', referedtonum	 ='" . $number . "', referedtoemail	 ='" . $_POST['email'] . "', comment	 ='" . $_POST['message'] . "' AND pid = '". $pid."'";
            $ret = $this->db->query($sql);
        return true;
	}
    }
    function feedback(){
		   $authkey = $_POST['authkey'];
		    $sql = "INSERT INTO feedbackmconnect SET  authKey	='" . $authkey . "', feedback	 ='" . $_POST['feedback'] . "'";
            $ret = $this->db->query($sql);
        return true;
	}
    
}

/* end of model*/
