<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_bill_date = (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
	$to_bill_date 	= (isset($_GET['to_bill_date'])) ? $_GET['to_bill_date'] : "";
	$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
	$to_qty 		= (isset($_GET['to_qty'])) ? $_GET['to_qty'] : "";
	$from_bill_amt 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
	$to_bill_amt 	= (isset($_GET['to_bill_amt'])) ? $_GET['to_bill_amt'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "trial_report";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/trial_report?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	ALTER SUMMARY(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/trial_report?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['sm_bill_no'])): ?><p>BILL NO</p><?php endif; ?>
					<select class="form-control floating-select" id="sm_bill_no" name="sm_bill_no">
                    	<?php if(isset($data['search']['sm_bill_no']) && !empty($data['search']['sm_bill_no'])): ?>
                        	<option value="<?php echo $data['search']['sm_bill_no']['value']; ?>" selected>
                            	<?php echo $data['search']['sm_bill_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-4 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_bill_date" name="from_bill_date" value="<?php echo $from_bill_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM BILL DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_bill_date" name="to_bill_date" value="<?php echo $to_bill_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO BILL DATE</label>
					</div>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['sm_user_id'])): ?><p>SALES PERSON</p><?php endif; ?>
					<select class="form-control floating-select" id="sm_user_id" name="sm_user_id">
                    	<?php if(isset($data['search']['sm_user_id']) && !empty($data['search']['sm_user_id'])): ?>
                        	<option value="<?php echo $data['search']['sm_user_id']['value']; ?>" selected>
                            	<?php echo $data['search']['sm_user_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['sm_acc_id'])): ?><p>CUSTOMER</p><?php endif; ?>
					<select class="form-control floating-select" id="sm_acc_id" name="sm_acc_id">
                    	<?php if(isset($data['search']['sm_acc_id']) && !empty($data['search']['sm_acc_id'])): ?>
                        	<option value="<?php echo $data['search']['sm_acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['sm_acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-reponsive table-hover">
				<thead class="table-dark">
					<tr>
		                <th width="3%">#</th>
                        <th width="5%">BILL NO</th>
                        <th width="7%">BILL DATE</th>
                        <th width="10%">SALES PERSON</th>
                        <th width="10%">CUSTOMER</th>
                        <th width="7%">BARCODE</th>
                        <th width="7%">DESIGN</th>
                        <th width="7%">STYLE</th>
                        <th width="7%">BRAND</th>
                        <th width="7%">TRIAL</th>
                        <th width="7%">DL DATE</th>
                        <th width="10%">STATUS</th>
                        <th width="7%">TOTAL AMT</th>
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
                        <td ></td> 
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td ><?php echo round($data['totals']['total_amt'], 2); ?></td>
					</tr>
				</tbody>
				<tbody>
					<?php 
						if(!empty($data['data'])):
							foreach ($data['data'] as $key => $value):
								$status = $value['st_alter_status'];
					?>
								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="5%"><?php echo $value['sm_bill_no']; ?></td>
									<td width="7%"><?php echo date('d-m-Y', strtotime($value['sm_bill_date'])); ?></td>
									<td width="10%"><?php echo strtoupper($value['user_fullname']); ?></td>
									<td width="10%"><?php echo strtoupper($value['account_name']); ?></td>
									<td width="10%"><?php echo $value['bm_item_code']; ?></td>
									<td width="10%"><?php echo $value['design_name']; ?></td>
									<td width="10%"><?php echo $value['style_name']; ?></td>
									<td width="10%"><?php echo $value['brand_name']; ?></td>
									<td width="7%"><?php echo $value['trial']; ?></td>
									<td width="7%"><?php echo (empty($value['st_dispatch_date'])) ? '' : date('d-m-Y',strtotime($value['st_dispatch_date'])) ; ?></td>
									<td width="10%">
										<select name="st_alter_status" onchange="set_order_status(<?php echo $value['st_id']; ?>,this.value)"
										 class="form-control floating-select">
											<option value="0">SELECT</option>
											<option value="1" <?php echo ($status==1)?'selected' : ''?>>READY FOR DELIVERY</option>
											<option value="2" <?php echo ($status==2)?'selected' : ''?>>DELIVERED</option>
										</select>
									</td>
									<td width="7%"><?php echo round($value['sm_final_amt'], 2); ?></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>TOTAL</td>
								<td ><?php echo round($data['totals']['total_amt'], 2); ?></td>
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
		<script src="<?php echo assets('dist/js/report/trial_report.js?v=2')?>"></script>
	</body>
</html>