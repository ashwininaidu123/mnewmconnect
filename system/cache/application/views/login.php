 <!-- BEGIN LOGIN BOX -->
    <div class="container" id="login-block">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
                <div class="login-box clearfix animated flipInY">
                    <div class="page-icon animated bounceInDown">
                        <img src="system/application/img/logo2.png" alt="Key icon" width="40" height="46" >
                    </div>
                    <div class="login-logo">
                        <a href="#?login-theme-3">
                        </a>
                    </div>
                    <div class="login-form">
                        <!-- BEGIN ERROR BOX -->
                        <div class="alert <?=($this->session->flashdata('msgt'))?$this->session->flashdata('msgt'):''?> <?=($this->session->flashdata('msgt'))?'':'hide'?>">
                            <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                            <h4>Error!</h4>
							<?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('msg');?>
                        </div>
                        <!-- END ERROR BOX -->
                        <form id="form1" name="login" method="POST" action="user/otp" autocomplete="off" parsley-validate class="icon-validation">
							<div class="maindiv"> 
								<div class="innerdiv">
									<input type="text" placeholder="Username" name="login_username" id="login_username" class="input-field form-control user" />
									<input type="password" placeholder="Password" name="login_password" id="login_password" class="input-field form-control password" />
								</div>
                           </div>
                            <div style="width:100%;margin:0 auto;float:left;">
								<div style="width:70%;margin:0 auto;">
									<div style="width:100%;margin:0 auto;float:left;">
										<div style="width:45%;float:left;">
											<input type="text" name="validator" class="validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="float:left;width:100%;">
										</div>
										<div style="width:5%;float:left;">&nbsp;</div>
										<div style="float:left;width:40%;">
											<img src="captcha.php?width=100%&amp;height=28&amp;characters=7" id="cimg">
										</div>
									</div>
									<div style="width:100%;margin:0 auto;float:left; margin-top:-15px;">
										
										<button type="submit" class="btn btn-login">Login</button>
									</div>
								</div>
							</div>
                        </form>
                        <div class="login-links">
                            <a href="site/forgetpass">Forgot password?</a> | <a href="http://mcube.vmc.in"><b>Classic View</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END LOCKSCREEN BOX -->
    <!-- BEGIN MANDATORY SCRIPTS -->
    <script src="system/application/plugins/jquery-1.11.js"></script>
    <script src="system/application/plugins/jquery-migrate-1.2.1.js"></script>
	<script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
	<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
    <script src="system/application/plugins/bootstrap/bootstrap.min.js"></script>
    <!-- END MANDATORY SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="system/application/backstretch/backstretch.min.js"></script>
    <script src="system/application/js/account.js"></script>
<!--
    <script src="system/application/js/application.js"></script>
-->
    <script src="system/application/plugins/parsley/parsley.js"></script>
    <script src="system/application/plugins/parsley/parsley.extend.js"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
</body>
</html>
