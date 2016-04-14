<?php
class Primodel extends Model {
	var $data;
    function Primodel(){
        parent::Model();
    }
	function getListAvailable(){
		$con = "status = '0'";
		$rst = $this->db->getwhere('prinumbers',$con);
		return $rst->result();
	}
	function assign2business($post){
		if(count($post['number'])>0){
			foreach($post['number'] as $number){
				$sql = "UPDATE prinumbers SET
						status='1',
						bid='".$post['bid']."',
						assigndate='".date('y-m-d H:i:s')."'
						WHERE number='".$number."'";
				$this->db->query($sql);
			}
		}
	}
	function getList($bid,$ofset='0',$limit='2'){
		$sql = "SELECT SQL_CALC_FOUND_ROWS g.groupname,p.* FROM prinumbers p
				LEFT JOIN groups g on p.gid=g.gid
				WHERE p.status = '1' AND p.bid='".$bid."'
				limit $ofset,$limit";
		$rst = $this->db->query($sql);
		$ret['rec'] = $rst->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		return $ret;
	}
}
?>
