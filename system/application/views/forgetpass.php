 <!-- BEGIN LOGIN BOX -->
    <div class="login-box">
      <div class="login-logo">
        <a href=""><b>Mcube</b>Connect</a>
      </div>
      <div class="login-box-body">
                    <div class="login-form">
                        <!-- BEGIN ERROR BOX -->
                        <div class="alert alert-danger <?=($this->session->flashdata('msgt'))?'':'hide'?>">
                            <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                            <h4>Error!</h4>
							<?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('msg');?>
                        </div>
                        <!-- END ERROR BOX -->
                         <div class="login-box-body">
                        <form id="form" name="login" method="POST" action="user/forgetpass" autocomplete="off">
							<div class="form-group has-feedback">
							<input  type="text" placeholder="Email" name="login_username" id="login_username"  class="form-control" placeholder="Email">
							<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                           </div>
                           
               <div style="width:100%;margin:0 auto;float:left;">
			  <div style="width:45%;float:left;">
			    <input type="text" name="validator" class=" form-control validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="float:left;width:100%;">
			 </div>
		    <div style="width:5%;float:left;">&nbsp;</div>
	    	<div style="float:left;width:40%;">
			  <img src="captcha.php?width=100%&amp;height=28&amp;characters=7" id="cimg">
		    </div>
	         <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
              </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div><!-- /.col -->
          </div>
										
									</div>
								</div>
							</div>
                        </form>
                        <div class="login-links" style="flaot:left;">
                            <a href="site/login">Already have an account?  <strong>Sign In</strong></a>
                        </div>
                    </div>

