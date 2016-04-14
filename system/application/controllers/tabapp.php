<?php
class Tabapp extends Controller
{
    
    function Tabapp()
    {
        parent::controller();
        
        $this->load->model('tabappmodel', 'TM');
        $this->form_validation->_error_prefix = "<br>";
        $this->form_validation->_error_suffix = "";
    }
    
    function index()
    {
        echo "This is a privete URL.";
    }
    function loginDetail()
    {
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        if (!$this->form_validation->run() == FALSE) {
            $user = $this->TM->loginDetails($_POST);
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'Invalid Credentials'
                );
                echo json_encode($out);
                exit;
            }else{
                $out = array(
                    'code'     => '400',
                    'msg'      => 'Login Successful',
                    'authkey'  => $user['authkey'],
                    'useremail' => $user['username'],
                    'eid' => $user['eid'],
                    'bid' => $user['bid']
                );
                echo json_encode($out);
                exit;
            }
        } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }
    function getsites(){
		    $this->form_validation->set_rules('bid', 'bid', 'required');
		      if (!$this->form_validation->run() == FALSE) {
            $user = $this->TM->getsites();
            if (empty($user)) {
                $out = array(
                    'code' => '202',
                    'msg' => 'No Sites'
                );
                echo json_encode($out);
                exit;
            }else{

                $out = array(
                    'code' => '400',
                    'msg' => 'All Sites',
                    'data' => $user
                );
                
                echo json_encode($out);
                exit;
            }
            } else {
            $out = array(
                'code' => '420',
                'msg' => validation_errors()
            );
        }
        echo json_encode($out);
        exit;
    }

    function register()
    {
        $user = $this->TM->register($_POST);
        if($user == '1') {
             $out = array(
                'code' => '202',
                'msg' => 'Already registered Please Login'
            );
            echo json_encode($out);
            exit;
        } else{
			 $out = array(
                'code' => '400',
                'msg' => 'successfully Registered',
                'authkey' => $user['authkey'],
            );
            echo json_encode($out);
            exit;
        }
    }
}
?>
