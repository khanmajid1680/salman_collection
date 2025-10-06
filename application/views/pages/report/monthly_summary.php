<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
	$to_qty 		= (isset($_GET['to_qty'])) ? $_GET['to_qty'] : "";
	$from_amt 		= (isset($_GET['from_amt'])) ? $_GET['from_amt'] : "";
	$to_amt 		= (isset($_GET['to_amt'])) ? $_GET['to_amt'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
	$from_date 		= date('01-m-Y');
	$to_date 		= date('t-m-Y');
	if(!isset($_GET['from_date'])){
		if(date('Y-m-d') < $_SESSION['start_year']){
			$from_date = date('d-m-Y', strtotime($_SESSION['start_year']));
		}
	}else{
		$from_date = date('d-m-Y', strtotime($_GET['from_date']));
	}
	if(!isset($_GET['to_date'])){
		if(date('Y-m-d') < $_SESSION['start_year']){
			$to_date = date('d-m-Y', strtotime($_SESSION['start_year']));
		}
	}else{
		$to_date = date('d-m-Y', strtotime($_GET['to_date']));
	}
?>
<script>
    let link 	= "report";
    let sub_link= "monthly_summary";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/monthly_summary?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	MONTHLY SUMMARY(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/monthly_summary?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/monthly_summary?submit=PDF&$url"); ?>">
			    			<i class="text-success fa fa-print"></i>
			    		</a>
			    	<?php else: ?>
			    		<button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" disabled="disabled">
			    			<i class="text-success fa fa-print"></i>
			    		</button>
			    	<?php endif; ?>
			    </li>
			    <li class="breadcrumb-item" aria-current="search-box">
			    	<input type="checkbox" id="search_status" name="search_status" data-toggle="toggle" data-on="FILTER <i class='fa fa-eye'></i>" data-off="FILTER <i class='fa fa-eye-slash'></i>" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="mini" data-style="show-hide" onchange="set_search_box()" <?php echo empty($search_status) ? 'checked' : ''; ?>>
			    </li>
			  </ol>
			</nav>
			<div class="d-none d-sm-block height_60_px">
				<?= $this->pagination->create_links(); ?>
			</div>
		</div>
		<div class="row collapse mt-2 <?php echo empty($search_status) ? '' : 'show'  ?>" id="search_box">
			<div class="d-flex flex-wrap justify-content-center floating-form">
				<div class="d-flex col-6 col-sm-4 col-md-3 col-lg-2">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_date" name="from_date" value="<?php echo $from_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_date" name="to_date" value="<?php echo $to_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO DATE</label>
					</div>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_qty" name="from_qty" value="<?php echo $from_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ACT.SALE QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_qty" name="to_qty" value="<?php echo $to_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ACT.SALE QTY</label>
					</div>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_amt" name="from_amt" value="<?php echo $from_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ACT.SALE AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_amt" name="to_amt" value="<?php echo $to_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ACT.SALE AMT</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="3%">#</th>
                            <th width="10%">DATE</th>
                            <th width="10%">DAY</th>
                            <th width="10%">SALE QTY</th>
                            <th width="10%">SALE RETURN QTY</th>
                            <th width="10%">ACTUAL SALE QTY</th>
                            <th width="10%">SALE AMT</th>
                            <th width="10%">DISC AMT</th>
                            <th width="10%">SALES RETURN AMT</th>
                            <th width="10%">ACTUAL SALE AMT</th>
                            <th width="10%">BILL GENERATED</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
							<td ></td>
							<td ></td>
                            <td ><?php echo $data['totals']['st_qty']; ?></td>
                            <td ><?php echo $data['totals']['srt_qty']; ?></td>
                            <td ><?php echo $data['totals']['sale_qty']; ?></td>
                            <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['st_disc'], 2); ?></td>
                            <td ><?php echo round($data['totals']['srt_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['sale_amt'], 2); ?></td>
                            <td ><?php echo $data['totals']['bill']; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-reponsive table-hover">
				<tbody>
					<?php 
						if(!empty($data['data'])):
							$sr_no = 1;
							foreach ($data['data'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $sr_no; ?></td>
									<td width="10%"><?php echo $value['entry_date']; ?></td>
									<td width="10%"><?php echo $value['day']; ?></td>
									<td width="10%"><?php echo $value['st_qty']; ?></td>
									<td width="10%"><?php echo $value['srt_qty']; ?></td>
									<td width="10%"><?php echo $value['sale_qty']; ?></td>
									<td width="10%"><?php echo round($value['st_amt'], 2); ?></td>
									<td width="10%"><?php echo round($value['st_disc'], 2); ?></td>
									<td width="10%"><?php echo round($value['srt_amt'], 2); ?></td>
									<td width="10%"><?php echo round($value['sale_amt'], 2); ?></td>
									<td width="10%"><?php echo $value['bill']; ?></td>
								</tr>
					<?php $sr_no++; endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
								<td ></td>
								<td ></td>
	                            <td ><?php echo $data['totals']['st_qty']; ?></td>
	                            <td ><?php echo $data['totals']['srt_qty']; ?></td>
	                            <td ><?php echo $data['totals']['sale_qty']; ?></td>
	                            <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['st_disc'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['srt_amt'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['sale_amt'], 2); ?></td>
	                            <td ><?php echo $data['totals']['bill']; ?></td>
							</tr>
					<?php else: ?>
						<tr>
							<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?= $this->pagination->create_links(); ?>

<?php $this->load->view('templates/footer'); ?>
		<script src="<?php echo assets('dist/js/report/monthly_summary.js')?>"></script>
	</body>
</html>