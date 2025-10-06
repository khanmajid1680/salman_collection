<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_bill_date = (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
	$to_bill_date   = (isset($_GET['to_bill_date'])) ? $_GET['to_bill_date'] : "";
	$from_credit_day= (isset($_GET['from_credit_day'])) ? $_GET['from_credit_day'] : "";
	$to_credit_day 	= (isset($_GET['to_credit_day'])) ? $_GET['to_credit_day'] : "";
	$from_rem_day 	= (isset($_GET['from_rem_day'])) ? $_GET['from_rem_day'] : "";
	$to_rem_day 	= (isset($_GET['to_rem_day'])) ? $_GET['to_rem_day'] : "";
	$from_bill_amt 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
	$to_bill_amt 	= (isset($_GET['to_bill_amt'])) ? $_GET['to_bill_amt'] : "";
	$as_on_date 	= (isset($_GET['as_on_date'])) ? $_GET['as_on_date'] : date('d-m-Y');
	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "payment_reminder";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/payment_reminder?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="<?php echo base_url('report/payment_reminder?action=view'); ?>">REPORT</a></li>
			    <li class="breadcrumb-item active" aria-current="page">
			    	PAYMENT REMINDER(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/payment_reminder?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/payment_reminder?submit=PDF&$url"); ?>">
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
				<div class="d-flex col-6 col-sm-6 col-md-2 col-lg-1 floating-label">
					<input type="text" class="form-control floating-input datepicker" id="as_on_date" name="as_on_date" value="<?php echo $as_on_date ?>" placeholder=" " autocomplete="off"/>   
                    <label for="inputEmail3">AS ON DATE</label>
				</div>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3 floating-label">
					<?php if(isset($data['search']['_acc_id'])): ?><p>SUPPLIER</p><?php endif; ?>
					<select class="form-control floating-select" id="_acc_id" name="_acc_id">
                    	<?php if(isset($data['search']['_acc_id']) && !empty($data['search']['_acc_id'])): ?>
                        	<option value="<?php echo $data['search']['_acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['_acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['pm_bill_no'])): ?><p>BILL NO</p><?php endif; ?>
					<select class="form-control floating-select" id="pm_bill_no" name="pm_bill_no">
                    	<?php if(isset($data['search']['pm_bill_no']) && !empty($data['search']['pm_bill_no'])): ?>
                        	<option value="<?php echo $data['search']['pm_bill_no']['value']; ?>" selected>
                            	<?php echo $data['search']['pm_bill_no']['text']; ?> 
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
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_credit_day" name="from_credit_day" value="<?php echo $from_credit_day ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM CREDIT DAY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_credit_day" name="to_credit_day" value="<?php echo $to_credit_day ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO CREDIT DAY</label>
					</div>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_rem_day" name="from_rem_day" value="<?php echo $from_rem_day ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM OVERDUE DAY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_rem_day" name="to_rem_day" value="<?php echo $to_rem_day ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO OVERDUE DAY</label>
					</div>
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
	                        <th width="20%">SUPPLIER</th>
	                        <th width="10%">BILL NO</th>
	                        <th width="10%">BILL DATE</th>
	                        <th width="10%">CREDIT DAYS</th>
	                        <th width="10%">DUE DATE</th>
	                        <th width="10%">OVERDUE DAYS</th>
	                        <th width="10%">BAL AMT</th>
	                        <th width="2%"></th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td colspan="7"></td>
	                        <td><?php echo round($data['totals']['bal_amt'], 0); ?></td>
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
									<td width="20%"><?php echo $value['account_name']; ?></td>
									<td width="10%"><?php echo $value['pm_bill_no']; ?></td>
									<td width="10%"><?php echo date('d-m-Y', strtotime($value['pm_bill_date'])); ?></td>
									<td width="10%"><?php echo $value['account_credit_days']; ?></td>
									<td width="10%"><?php echo $value['due_date']; ?></td>
									<td width="10%"><?php echo $value['diff']; ?></td>
									<td width="10%"><?php echo round($value['bal_amt'], 2); ?></td>
									<td width="2%">
										<a target="_blank" type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="left" title="CLICK HERE FOR PAYMENT" href="<?php echo base_url("voucher/payment?action=add&party_id=".$value['account_id']); ?>">
							    			<i class="text-success fa fa-money"></i>
							    		</a>
									</td>
								</tr>
					<?php 
							endforeach;
					?>
								<tr style="font-size: 15px; font-weight: bold;">
									<td colspan="6"></td>
									<td>TOTAL</td>
									<td><?php echo round($data['totals']['bal_amt'], 0); ?></td>
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
	<script src="<?php echo assets('dist/js/report/payment_reminder.js')?>"></script>
	</body>
</html>