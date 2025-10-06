<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_entry_date= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
	$to_entry_date 	= (isset($_GET['to_entry_date'])) ? $_GET['to_entry_date'] : "";
	$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
	$to_qty 		= (isset($_GET['to_qty'])) ? $_GET['to_qty'] : "";
	$from_bill_amt 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
	$to_bill_amt 	= (isset($_GET['to_bill_amt'])) ? $_GET['to_bill_amt'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "sales_return_summary";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/sales_return_summary?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	SALES RETURN SUMMARY(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/sales_return_summary?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/sales_return_summary?submit=PDF&$url"); ?>">
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
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['srm_entry_no'])): ?><p>ENTRY NO</p><?php endif; ?>
					<select class="form-control floating-select" id="srm_entry_no" name="srm_entry_no">
                    	<?php if(isset($data['search']['srm_entry_no']) && !empty($data['search']['srm_entry_no'])): ?>
                        	<option value="<?php echo $data['search']['srm_entry_no']['value']; ?>" selected>
                            	<?php echo $data['search']['srm_entry_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-4 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_entry_date" name="from_entry_date" value="<?php echo $from_entry_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ENTRY DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_entry_date" name="to_entry_date" value="<?php echo $to_entry_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ENTRY DATE</label>
					</div>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['srm_acc_id'])): ?><p>CUSTOMER</p><?php endif; ?>
					<select class="form-control floating-select" id="srm_acc_id" name="srm_acc_id">
                    	<?php if(isset($data['search']['srm_acc_id']) && !empty($data['search']['srm_acc_id'])): ?>
                        	<option value="<?php echo $data['search']['srm_acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['srm_acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['srt_user_id'])): ?><p>SALES PERSON</p><?php endif; ?>
					<select class="form-control floating-select" id="srt_user_id" name="srt_user_id">
                    	<?php if(isset($data['search']['srt_user_id']) && !empty($data['search']['srt_user_id'])): ?>
                        	<option value="<?php echo $data['search']['srt_user_id']['value']; ?>" selected>
                            	<?php echo $data['search']['srt_user_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['bm_id'])): ?><p>BARCODE</p><?php endif; ?>
					<select class="form-control floating-select" id="bm_id" name="bm_id">
                    	<?php if(isset($data['search']['bm_id']) && !empty($data['search']['bm_id'])): ?>
                        	<option value="<?php echo $data['search']['bm_id']['value']; ?>" selected>
                            	<?php echo $data['search']['bm_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<!-- <div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_qty" name="from_qty" value="<?php echo $from_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM TOTAL QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_qty" name="to_qty" value="<?php echo $to_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO TOTAL QTY</label>
					</div>
				</div> -->
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['style_id'])): ?><p>STYLE</p><?php endif; ?>
					<select class="form-control floating-select" id="style_id" name="style_id">
                    	<?php if(isset($data['search']['style_id']) && !empty($data['search']['style_id'])): ?>
                        	<option value="<?php echo $data['search']['style_id']['value']; ?>" selected>
                            	<?php echo $data['search']['style_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['brand_id'])): ?><p>BRAND</p><?php endif; ?>
					<select class="form-control floating-select" id="brand_id" name="brand_id">
                    	<?php if(isset($data['search']['brand_id']) && !empty($data['search']['brand_id'])): ?>
                        	<option value="<?php echo $data['search']['brand_id']['value']; ?>" selected>
                            	<?php echo $data['search']['brand_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['design_id'])): ?><p>DESIGN</p><?php endif; ?>
					<select class="form-control floating-select" id="design_id" name="design_id">
                    	<?php if(isset($data['search']['design_id']) && !empty($data['search']['design_id'])): ?>
                        	<option value="<?php echo $data['search']['design_id']['value']; ?>" selected>
                            	<?php echo $data['search']['design_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_bill_amt" name="from_bill_amt" value="<?php echo $from_bill_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM BILL AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_bill_amt" name="to_bill_amt" value="<?php echo $to_bill_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO BILL AMT</label>
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
                            <th width="6%">ENTRY NO</th>
                            <th width="7%">ENTRY DATE</th>
                            <th width="10%">CUSTOMER</th>
                            <th width="10%">SALES PERSON</th>
                            <th width="8%">BARCODE</th>
                            <th width="8%">STYLE</th>
                            <th width="8%">BRAND</th>
                            <th width="8%">DESIGN</th>
                            <th width="8%">RATE</th>
                            <th width="8%">DISC AMT</th>
                            <th width="8%">TAXABLE AMT</th>
                            <th width="8%">GST AMT</th>
                            <th width="8%">TOTAL AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ><?php echo round($data['totals']['total_qty'], 0); ?></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ><?php echo round($data['totals']['disc_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['taxable_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['gst_amt'], 2); ?></td>
                            <td ><?php echo round($data['totals']['total_amt'], 2); ?></td>
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
									<td width="6%"><?php echo $value['srm_entry_no']; ?></td>
									<td width="7%"><?php echo date('d-m-Y', strtotime($value['srm_entry_date'])); ?></td>
									<td width="10%"><?php echo strtoupper($value['account_name']); ?></td>
									<td width="10%"><?php echo strtoupper($value['user_fullname']); ?></td>
									<td width="8%"><?php echo $value['bm_item_code']; ?></td>
									<td width="8%"><?php echo strtoupper($value['style_name']); ?></td>
									<td width="8%"><?php echo strtoupper($value['brand_name']); ?></td>
									<td width="8%"><?php echo strtoupper($value['design_name']); ?></td>
									<td width="8%"><?php echo round($value['srt_rate'], 2); ?></td>
									<td width="8%"><?php echo round($value['srt_disc_amt'], 2); ?></td>
									<td width="8%"><?php echo round($value['srt_taxable_amt'], 2); ?></td>
									<td width="8%"><?php echo round($value['gst_amt'], 2); ?></td>
									<td width="8%"><?php echo round($value['srt_total_amt'], 2); ?></td>

								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td >TOTALS</td>
	                            <td ><?php echo round($data['totals']['total_qty'], 0); ?></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ><?php echo round($data['totals']['disc_amt'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['taxable_amt'], 2); ?></td>
	                            <td ><?php echo round($data['totals']['gst_amt'], 2); ?></td>
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
		<script src="<?php echo assets('dist/js/report/sales_return_summary.js')?>"></script>
	</body>
</html>