<?php
class Tabappmodel extends Model
{
    
    function Tabappmodel()
    {
        parent::Model();
        $this->load->model('configmodel', 'CM');
        $this->load->model('empmodel', 'EMPM');
        $this->load->model('systemmodel', 'SYS');
        $this->load->dbforge();
    }
   function loginDetails(){
		$retArr = array();
		$sql = "SELECT * FROM user WHERE status='1'
		AND app='1' AND username='".$_POST['email']."'
		AND password='".md5($_POST['password'])."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$retArr = $rst->row_array();
			if($retArr['authkey']==''){
				//$authkey = "";for($i = 0; $i<=6 ; $i++){$authkey .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
				$authkey = uniqid($retArr['bid'].'.'.$retArr['eid'].'.');
				$sql = "UPDATE user SET authkey='".$authkey."'
				WHERE uid='".$retArr['uid']."'";
				$this->db->query($sql);
				$retArr['authkey']=$authkey;
			}
		}
		return $retArr;
	}
    function getsites()
    {
        $limit = $_POST['limit'];
        $offset = $_POST['offset'];
        $bid = $_POST['bid'];
	    $data = array(); 
        $sql     = "SELECT v.sitename,v.time,v.pid,p.propertyname,v.bid,v.siteid,v.siteicon,v.sitedesc,a.offerper,a.offer_desc,n.landingnumber as 'number' FROM  ".$bid."_site v
	     LEFT JOIN  prinumbers n ON n.number = v.tracknum
	     LEFT JOIN   ".$bid."_property p ON p.propertyid = v.pid
	     LEFT JOIN  offers a ON a.siteid = v.siteid
	     GROUP BY  v.siteid ORDER BY v.time ASC";
        $rest    = $this->db->query($sql)->result_array();
        foreach ($rest as $key => $value) {
			$rest[$key]['number'] =($value['number'])? $value['number'] : "1";
			$rest[$key]['offerper'] =($value['offerper'])? $value['offerper'] : "";
			$rest[$key]['offer_desc'] =($value['offer_desc'])? $value['offer_desc'] : "";
            $rest[$key]['siteicon'] = "" . base_url() . 'uploads/' . $value['siteicon'] . "";
        }
        $data[] = $rest;
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

   function register()
    {
        $sql  = "SELECT usremail,usrnumber FROM mconnect_register WHERE usrnumber='" . $_POST['number'] . "' AND usremail='" . $_POST['email'] . "'";
        $rest = $this->db->query($sql);
        if ($rest->num_rows() > 0) {
            return '1';
        } else {
            $authkey = "1.1." . substr(md5(uniqid(rand(), true)), 0, 13) . "";
            $id      = $this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as uid FROM mconnect_register")->row()->uid;
            $sql     = "INSERT INTO mconnect_register SET  uid	='" . $id . "',usrname ='" . $_POST['name'] . "', usremail ='" . $_POST['email'] . "',usrnumber ='" . $_POST['number'] . "',address ='" . $_POST['address'] . "',city ='" . $_POST['city'] . "',state ='" . $_POST['state'] . "', authkey ='" . $authkey . "',pincode ='" . $_POST['pincode'] . "',keyword ='tabapp'";
            $ret     = $this->db->query($sql);
            $sql1 = "SELECT authKey as authkey FROM mconnect_register WHERE  usrnumber='" . $_POST['number'] . "'";
            $rst = $this->db->query($sql1);
            if ($rst->num_rows() > 0) {
            $retArr = $rst->row_array();
            }
        return $retArr;
        }
    }
}

/* end of model*/
