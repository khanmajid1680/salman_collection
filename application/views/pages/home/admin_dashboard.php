<?php $this->load->view('templates/header'); ?>
<script>
    var link = "home";
    var sub_link = "home";
</script>
<link rel="stylesheet" href=<?php echo assets('plugins/chart/css/chart.min.css'); ?>>
<link rel="stylesheet" href=<?php echo assets('dist/css/home/first.css'); ?>>
<section class="container-fluid sticky_top">
	<div class="d-flex justify-content-between">
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		    <li class="breadcrumb-item"><a href="<?php echo base_url('home'); ?>">HOME</a></li>
		  </ol>
		</nav>
	</div>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12 col-sm-12 col-md-12 col-lg-6">
			<div class="card my-2 neu_flat_primary" style="height: 60vh; width: auto;">
				<div class="card-header">
					<h6>BALANCE STOCK</h6>
				</div>
				<div class="card-body d-flex flex-wrap">
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>
					      <div class="percent">
					        <span class="percent__int" id="pur_qty"><?php echo $first['pur_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Purchase Qty</span>
					</div>
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>
					      <div class="percent">
					        <span class="percent__int" id="pret_qty"><?php echo $first['pret_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Purchase Return Qty</span>
					</div>
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>

					      <div class="percent">
					        <span class="percent__int" id="sale_qty"><?php echo $first['sale_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Sale Qty</span>
					</div>
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>

					      <div class="percent">
					        <span class="percent__int" id="sret_qty"><?php echo $first['sret_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Sales Return Qty</span>
					</div>
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>

					      <div class="percent">
					        <span class="percent__int" id="out_qty"><?php echo $first['out_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Outward Qty</span>
					</div>
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>

					      <div class="percent">
					        <span class="percent__int" id="grn_qty"><?php echo $first['grn_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Grn Qty</span>
					</div>
					<div class="col-6 col-sm-3 col-md-3 col-lg-3 note-display">
						<div class="circle">
					      <svg class="circle__svg">
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--path"></circle>
					        <circle cx="50%" cy="50%" r="50%" class="circle__progress circle__progress--fill"></circle>
					      </svg>

					      <div class="percent">
					        <span class="percent__int" id="bal_qty"><?php echo $first['bal_qty']; ?></span>
					        <!-- <span class="percent__dec">00</span> -->
					      </div>
					    </div>
					    <span class="label">Balance Qty</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-12 col-lg-6">
			<div class="card my-2 neu_flat_primary" style="position: relative; height: 60vh; width: auto;">
				<div class="card-header">
					<h6>MONTHLY PROFIT</h6>
				</div>
				<div class="card-body d-flex flex-wrap chart-container" >
					<canvas id="second-chart" aria-label="chart" role="img"></canvas>
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-12 col-lg-6">
			<div class="card my-2 neu_flat_primary" style="position: relative; height: 60vh; width: auto;">
				<div class="card-header">
					<h6>CATEGORY WISE SALE</h6>
				</div>
				<div class="card-body d-flex flex-wrap chart-container" >
					<canvas id="third-chart" aria-label="chart" role="img"></canvas>
				</div>
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-12 col-lg-6">
			<div class="card my-2 neu_flat_primary" style="position: relative; height: 60vh; width: auto;">
				<div class="card-header">
					<h6>PAYMENT MODE WISE SALE</h6>
				</div>
				<div class="card-body d-flex flex-wrap chart-container" >
					<canvas id="fourth-chart" aria-label="chart" role="img"></canvas>
				</div>
			</div>
		</div>
	</div>
</section>
<?php $this->load->view('templates/footer'); ?>
		<script src="<?php echo assets('plugins/chart/js/chart.min.js')?>"></script>
		<script src="<?php echo assets('plugins/chart/js/bundle.min.js')?>"></script>
		<script src="<?php echo assets('dist/js/home/home.js')?>"></script>
		<script src="<?php echo assets('dist/js/home/first.js')?>"></script>
		<script src="<?php echo assets('dist/js/home/second.js')?>"></script>
		<script src="<?php echo assets('dist/js/home/third.js')?>"></script>
		<script src="<?php echo assets('dist/js/home/fourth.js')?>"></script>
	</body>
</html>