<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$acc_id 		= isset($_GET['acc_id']) ? $_GET['acc_id'] : '';
	$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y', strtotime($_SESSION['start_year']));
	$to_date 		= (isset($_GET['to_date'])) ? $_GET['to_date'] : date('d-m-Y', strtotime($_SESSION['end_year']));
	$url 			= $_SERVER['QUERY_STRING'];	
?>
<script>
    let link 	= "report";
    let sub_link= "supplier_ledger";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/supplier_ledger?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	SUPPLIER LEDGER
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/supplier_ledger?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<a target="_blank" type="button" name="submit" value="PDF" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/supplier_ledger?action=view&submit=PDF&acc_id=$acc_id&from_date=$from_date&to_date=$to_date"); ?>">
			    		<i class="text-success fa fa-print"></i>
			    	</a>
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
				<div class="col-12 col-sm-12 col-md-4 col-lg-4 floating-label">
					<?php if(isset($data['search']['acc_id'])): ?><p>SUPPLIER</p><?php endif; ?>
					<select class="form-control floating-select" id="acc_id" name="acc_id">
                    	<?php if(isset($data['search']['acc_id']) && !empty($data['search']['acc_id'])): ?>
                        	<option value="<?php echo $data['search']['acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-4 col-md-4 col-lg-4">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_date" name="from_date" value="<?php echo $from_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_date" name="to_date" value="<?php echo $to_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO DATE</label>
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
		                    <th width="10%">SUPPLIER</th>
		                    <th width="8%">BILL / ENTRY NO</th>
		                    <th width="8%">BILL / ENTRY DATE</th>
		                    <th width="8%">BILL AMT</th>
		                    <th width="8%">ACTION</th>
		                    <th width="8%">RECEIVED AMT</th>
		                    <th width="8%">PAID AMT</th>
		                    <th width="8%">BALANCE AMT</th>
			            </tr>
					</thead>
					<tbody id="table_reload1">
						<tr id="table_tbody1" style="font-size: 15px; font-weight: bold;">
			                <td ></td>
		                    <td >OPENING AMT</td>
		                    <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ><?php echo $data['open_bal']; ?></td>
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
			<table class="table table-sm table-hover" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							$sr_no = 1;
							foreach ($data['data'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $sr_no; ?></td>
									<td width="10%"><?php echo $value['account_name']; ?></td>
									<td width="8%"><?php echo $value['entry_no']; ?></td>
									<td width="8%"><?php echo $value['entry_date']; ?></td>
									<td width="8%"><?php echo !empty($value['amt_to_debit']) ? $value['amt_to_debit'] : $value['amt_to_credit']; ?></td>
									<td width="8%"><?php echo $value['action']; ?></td>
									<td width="8%"><?php echo $value['amt_debited']; ?></td>
									<td width="8%"><?php echo $value['amt_credited']; ?></td>
									<td width="8%"><?php echo $value['bal_amt']; ?></td>
								</tr>
					<?php 
								$sr_no++;
							endforeach;
					?>
							<tr style="font-size: 15px; font-weight: bold;">
				                <td ></td>
			                    <td >CLOSING AMT</td>
			                    <td ></td>
			                    <td ></td>
			                    <td ></td>
			                    <td ></td>
			                    <td ></td>
			                    <td ></td>
			                    <td ><?php echo $data['close_bal']; ?></td>
				            </tr>
					<?php
						else: 
					?>
						<tr>
							<td class="text-danger font-weight-bold text-center" colspan="10">NO RECORD FOUND!!!</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>

<?= $this->pagination->create_links(); ?>

<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/report/supplier_ledger.js')?>"></script>
	</body>
</html>