<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
		  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		  	<title><?php echo $title[1].' '. $title[2]; ?> | Log in</title>
		  	
		  	<!-- Tell the browser to be responsive to screen width -->
		  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

		  	<!-- Bootstrap 4 -->
		  	<link rel="stylesheet" href="<?php echo assets('plugins/bootstrap/css/bootstrap.min.css')?>">
  
		  	<!-- Font Awesome -->
		  	<link rel="stylesheet" href="<?php echo assets('plugins/font-awesome/css/font-awesome.min.css')?>">

		  	<!--Toastify-->
	  		<link rel="stylesheet" href="<?php echo assets('plugins/toastify/css/toastify.min.css'); ?>" media="screen,projection" />
		
			<!-- Google Font -->
  			<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  			
  			<!-- custom style sheet -->
  			<link rel="stylesheet" href="<?php echo assets('dist/css/bootstrap.css')?>">
  			<link rel="stylesheet" href="<?php echo assets('dist/css/common.css')?>">
  			<link rel="stylesheet" href="<?php echo assets('dist/css/floating.css')?>">
  			<link rel="stylesheet" href="<?php echo assets('dist/css/loader.css')?>">
  			<link rel="stylesheet" href="<?php echo assets('dist/css/login.css')?>">
		</head>
		<body class="wrapper bg_image blur">
			<main>
				<section class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 p-5">
					<div class="d-flex flex-column justify-content-center align-items-center p-4 rounded_50 login_wrapper">
						<div class="w-40 p-3 mb-2 logo rounded_50">
							<p class="text-center font-weight-bold text-white">
								<?php echo $title[1]; ?></br>
								<?php echo $title[2]; ?>
							</p>
						</div>
						<div class="d-flex flex-column justify-content-center align-items-center w-100 my-1">
							<form class="floating-form" id="login_form" onsubmit="login_action()">
								<div class="form-group floating-label">
						            <?php echo form_dropdown('', $year, 0 ,'id="fin_year" name="fin_year" class="form-control floating-select" tabindex="1" placeholder=" "'); ?>
						            <label for="fin_year"><span class="text-white">Financial Year</span></label>
						            <small class="form-text text-muted helper-text" id="fin_year_msg"></small>          
						          </div>
						          <div class="form-group floating-label">
						            <?php echo form_dropdown('', $branch, 0 ,'id="user_branch_id" name="user_branch_id" class="form-control floating-select" tabindex="2" autofocus="on" placeholder=" " onchange="validate_dropdown(this)"'); ?>
						            <label for="user_branch_id"><span class="text-white">Branch</span><span class="text-danger"> *</span></label>
						            <small class="form-text text-muted helper-text" id="user_branch_id_msg"></small>          
						          </div>
						          <div class="form-group floating-label">
						            <input type="text" class="form-control floating-input" id="user_name" name="user_name" placeholder=" " tabindex="2" autocomplete="off" required style="text-transform: none;" onkeyup="validate_textfield(this)">
						            <label for="user_name"><span class="text-white">Username</span><span class="text-danger"> *</span></label>
						            <small class="form-text text-muted helper-text" id="user_name_msg"></small>          
						          </div>
						          <div class="form-group floating-label">
						            <input type="password" class="form-control floating-input" id="user_password" name="user_password" placeholder=" " tabindex="3" required style="text-transform: none;" onkeyup="validate_textfield(this)">
						            <label for="user_password"><span class="text-white">Password</span><span class="text-danger"> *</span></label>
						            <small class="form-text text-muted helper-text" id="user_password_msg"></small>          
						          </div>
						          <button type="submit" tabindex="4" class="btn btn-primary btn-sm btn-block" onclick="login_action()">
						          	LOGIN
						          </button>
							</form>
						</div>
					</div>
				</section>
			</main>
			<footer class="d-flex justify-content-around">
				<span>
	        		<b>Powered by</b> Interlink Consultant
				</span>
				<span class="d-none d-sm-none d-md-block">
	        		<strong>Copyright &copy; <?php echo date('Y')-1?>-<?php echo date('Y')?>.</strong> All rights reserved.
				</span>
			</footer>
			<!-- loader -->
		    <div id="ftco-loader" class="show fullscreen">
		    	<svg class="circular" width="48px" height="48px">
		    		<circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/>
		    		<circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/>
		    	</svg>
		    </div>
		  
	  		<!-- jQuery 3 -->
		  	<script src="<?php echo assets('plugins/jquery/jquery.min.js')?>"></script>
		  	
		  	<!-- Bootstrap -->
		  	<script src="<?php echo assets('plugins/bootstrap/js/bootstrap.min.js')?>"></script>

		  	<!-- Bootstrap -->
		  	<script src="<?php echo assets('plugins/bootstrap/js/bootstrap.bundle.min.js')?>"></script>

		  	<!-- Toastisy -->
			<script src="<?php echo assets('plugins/toastify/js/toastify.js')?>"></script>
		  	
		  	<!-- Custom JS files. Note: Keep the sequence of following custom files -->
		  	<script src="<?php echo assets('dist/js/custom/constants.js')?>"></script>
		  	<script src="<?php echo assets('dist/js/custom/notify.js')?>"></script>
		  	<script src="<?php echo assets('dist/js/custom/ajax.js')?>"></script>
		  	<script src="<?php echo assets('dist/js/custom/common.js')?>"></script>
		  	<script src="<?php echo assets('dist/js/custom/loader.js')?>"></script>
		  	<script src="<?php echo assets('dist/js/custom/validate.js')?>"></script>

		  	<!-- Related JS files -->
		  	<script src="<?php echo assets('dist/js/login.js')?>"></script>
		</body>
</html>

