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

<!-- Date Picker -->
<script src="<?php echo assets('plugins/datepicker/js/bootstrap-datepicker.js')?>"></script>

<!-- Toastisy -->
<script src="<?php echo assets('plugins/toastify/js/toastify.js')?>"></script>

<!-- Toggle Switch -->
<script src="<?php echo assets('plugins/toggle-switch/js/toggle.min.js')?>"></script>

<!-- Select2 -->
<script src="<?php echo assets('plugins/select2/js/select2.min.js')?>"></script>


<script type="text/javascript">
	// const locat 			= window.location;
	// const base_url 			= locat.protocol + "//" + locat.host + "/" + locat.pathname.split("/")[1];
	
	const SALES 			= "<?php echo SALES; ?>";
	const DISPATCH 			= "<?php echo DISPATCH; ?>";
	const WITHIN 			= "<?php echo WITHIN; ?>";
	const OUTSIDE 			= "<?php echo OUTSIDE; ?>";
	const REFRESH 			= "<?php echo REFRESH; ?>";
	const PER_PAGE 			= "<?php echo PER_PAGE; ?>";
	const NOIMAGE 			= "<?php echo assets(NOIMAGE); ?>";
	const USERIMAGE 		= "<?php echo assets(USERIMAGE); ?>";
	const LAZYLOADING 		= "<?php echo assets(LAZYLOADING); ?>";
	// const RELOAD_TIME 		= 800;
</script>

<!-- Custom JS files. Note: Keep the sequence of following custom files -->
<script src="<?php echo assets('dist/js/custom/constants.js')?>"></script>
<script src="<?php echo assets('dist/js/custom/notify.js')?>"></script>
<script src="<?php echo assets('dist/js/custom/ajax.js')?>"></script>
<script src="<?php echo assets('dist/js/custom/common.js?v=2')?>"></script>
<script src="<?php echo assets('dist/js/custom/loader.js')?>"></script>
<script src="<?php echo assets('dist/js/custom/validate.js')?>"></script>
<script src="<?php echo assets('dist/js/custom/main.js?v=1')?>"></script>
<script src="<?php echo assets('dist/js/custom/select2.js')?>"></script>

<!-- Related JS files -->