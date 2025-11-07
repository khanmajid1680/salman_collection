<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$_bill_no 	= (isset($_GET['_bill_no'])) ? $_GET['_bill_no'] : "";
	$sale_qty_to 	= (isset($_GET['sale_qty_to'])) ? $_GET['sale_qty_to'] : "";
	$st_amt_from 	= (isset($_GET['st_amt_from'])) ? $_GET['st_amt_from'] : "";
	$st_amt_to 		= (isset($_GET['st_amt_to'])) ? $_GET['st_amt_to'] : "";
	$profit_amt_from= (isset($_GET['profit_amt_from'])) ? $_GET['profit_amt_from'] : "";
	$profit_amt_to 	= (isset($_GET['profit_amt_to'])) ? $_GET['profit_amt_to'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
	$from_date 		= date('d-m-Y');
	$to_date 		= date('d-m-Y');
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
    let sub_link= "token_report_profit";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/token_report_profit?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	TOKEN SALE REPORT(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/token_report_profit?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			   <!--  <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/token_report_profit?submit=PDF&$url"); ?>">
			    			<i class="text-success fa fa-print"></i>
			    		</a>
			    	<?php else: ?>
			    		<button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" disabled="disabled">
			    			<i class="text-success fa fa-print"></i>
			    		</button>
			    	<?php endif; ?>
			    </li> -->
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
				<div class="d-flex col-6 col-sm-4 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="_bill_no" name="_bill_no" value="<?php echo $_bill_no ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">BILL NO</label>
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
                            <th width="8%">BILL DATE</th>
                            <th width="8%">BILL NO</th>
                            <th width="8%">SP NAME</th>
                            <th width="8%">BARCODE</th>
                            <th width="8%">DESIGN</th>
                            <th width="8%">STYLE</th>
                            <th width="5%">SOLD QTY</th>
                            <th width="8%">TOKEN AMT</th>
                            <th width="8%">SALE AMT</th>
                            <th width="8%">DISC AMT</th>
                            <th width="8%">ACT. SALE AMT</th>
                            <th width="8%">PURCHASE AMT</th>
                            <th width="8%">PROFIT</th>
                            <th width="8%"></th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ><?php echo $data['totals']['st_qty']; ?></td>
							<td ><?php echo $data['totals']['token_amt']; ?></td>
							<td ><?php echo round($data['totals']['st_rate'], 2); ?></td>
                            <td ><?php echo round($data['totals']['st_disc'], 2); ?></td>
                            <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['pt_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['profit'], 2); ?></td>
                            <td ></td>
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
							foreach ($data['data'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="8%"><?php echo $value['entry_date']; ?></td>
									<td width="8%"><?php echo $value['entry_no']; ?></td>
									<td width="8%"><?php echo $value['user_fullname']; ?></td>
									<td width="8%"><?php echo $value['item_code']; ?></td>
									<td width="8%"><?php echo $value['design_name']; ?></td>
									<td width="8%"><?php echo $value['style_name']; ?></td>
									<td width="5%"><?php echo $value['st_qty']; ?></td>
									<td width="8%"><?php echo $value['bm_token_amt']; ?></td>
									<td width="8%"><?php echo round($value['st_rate'], 2); ?></td>
									<td width="8%"><?php echo round($value['st_disc'], 2); ?></td>
									<td width="8%"><?php echo round($value['st_amt'], 2); ?></td>
									<td width="8%"><?php echo round($value['pt_amt'], 2); ?></td>
									<td width="8%"><?php echo round($value['profit'], 2); ?></td>
									<td width="8%"></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ><?php echo $data['totals']['st_qty']; ?></td>
								<td ><?php echo $data['totals']['token_amt']; ?></td>
	                            <td ><?php echo round($data['totals']['st_rate'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['st_disc'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['pt_amt'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['profit'], 2); ?></td>
	                            <td >TOTALS</td>
							</tr>
							<?php if(!empty($data['totals']['srt_amt'])): ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td colspan="12"></td>
	                            <td ><?php echo round($data['totals']['srt_amt'], 2); ?></td>
								<td>SALES RETURN</td>
							</tr>
							<?php endif; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td colspan="12"></td>
	                            <td class="<?php echo $data['totals']['profit_loss'] < 0 ? 'text-danger' : 'text-success'; ?>">
	                            	<?php echo round($data['totals']['profit_loss'], 2); ?>
                            	</td>
								<td>ACTUAL PROFIT</td>
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
		<script src="<?php echo assets('dist/js/report/token_report_profit.js')?>"></script>
	</body>
</html>