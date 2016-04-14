
    <div class="login-box">
      <div class="login-logo">
        <a href=""><b>Mcube</b>Connect</a>
      </div>
             <!-- BEGIN ERROR BOX -->
                        <div class="alert <?=($this->session->flashdata('msgt'))?$this->session->flashdata('msgt'):''?> <?=($this->session->flashdata('msgt'))?'':'hide'?>">
                            <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                            <h4>Error!</h4>
							<?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('msg');?>
                        </div>
              <!-- END ERROR BOX -->
      <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form id="form1" name="login" action="user/otp" method="post">
          <div class="form-group has-feedback">
            <input type="email" placeholder="Username" name="login_username" id="login_username"  class="form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password"  placeholder="Password" name="login_password" id="login_password" class="form-control" placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
 
           <div style="width:100%;margin:0 auto;float:left;">
			  <div style="width:45%;float:left;">
			    <input type="text" name="validator" class=" form-control validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="float:left;width:100%;">
			 </div>
		    <div style="width:5%;float:left;">&nbsp;</div>
	    	<div style="float:left;width:40%;">
			  <img src="captcha.php?width=100%&amp;height=28&amp;characters=7" id="cimg">
		    </div>

		</div>
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
              </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div><!-- /.col -->
          </div>
        </form>
        <a href="site/forgetpass">I forgot my password</a><br>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
