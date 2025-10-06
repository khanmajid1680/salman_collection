<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$account_id 	= isset($_GET['account_id']) && !empty($_GET['account_id']) ? $_GET['account_id'] : '';
	$constant 		= isset($_GET['constant']) && !empty($_GET['constant']) ? $_GET['constant'] : '';
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
    let sub_link= "daily_transaction";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/daily_transaction?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	DAILY TRANSACTION
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/daily_transaction?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/daily_transaction?submit=PDF&$url"); ?>">
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
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<p>ACCOUNT</p>
					<select class="form-control floating-select" id="constant" name="constant" onchange="trigger_search()">
                    	<option value='CASH' <?php echo $constant == 'CASH' ? 'selected' : ''; ?>>CASH A/C</option>
                    	<option value='BANK' <?php echo $constant == 'BANK' ? 'selected' : ''; ?>>BANK A/C</option>
                	</select>
				</div>
				<!-- <div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<p>ACCOUNT</p>
					<select class="form-control floating-select" id="account_id" name="account_id" onchange="trigger_search()">
                    	<option value='1' <?php echo $account_id == '1' ? 'selected' : ''; ?>>CASH A/C</option>
                    	<option value='2' <?php echo $account_id == '2' ? 'selected' : ''; ?>>BANK A/C</option>
                	</select>
				</div> -->
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
		                    <th width="10%">PARTY NAME</th>
		                    <th width="8%">ENTRY NO</th>
		                    <th width="8%">ENTRY DATE</th>
		                    <th width="8%">ACTION</th>
		                    <th width="8%">RECEIVED AMT</th>
		                    <th width="8%">PAID AMT</th>
		                    <th width="8%"></th>
			            </tr>
					</thead>
				</table>
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-hover">
				<tbody>
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
									<td width="8%"><?php echo $value['action']; ?></td>
									<td width="8%"><?php echo $value['amt_debited']; ?></td>
									<td width="8%"><?php echo $value['amt_credited']; ?></td>
									<td width="8%"></td>
								</tr>
					<?php 
								$sr_no++;
							endforeach;
						else:
					?>
								<tr>
									<td class="text-danger font-weight-bold text-center" colspan="10">NO RECORD FOUND!!!</td>
								</tr>
					<?php
						endif; 
					?>
					<tr class="bg-primary text-white" style=" font-size: 15px; font-weight: bold;">
		                <th ></th>
	                    <th >OPENING BAL</th>
	                    <th >TOTAL SALES AMT</th>
	                    <th colspan="2">TOTAL SALES RETURN AMT</th>
	                    <th >TOTAL RECEIPT AMT</th>
	                    <th >TOTAL PAID AMT</th>
	                    <th >CLOSING BAL</th>
		            </tr>
		            <tr class="bg-primary text-white" style=" font-size: 15px; font-weight: bold;">
		                <th ></th>
	                    <th ><?php echo $data['open_bal']; ?></th>
	                    <th ><?php echo $data['sales_amt']; ?></th>
	                    <th colspan="2"><?php echo $data['return_amt']; ?></th>
	                    <th ><?php echo $data['receipt_amt']; ?></th>
	                    <th ><?php echo $data['payment_amt']; ?></th>
	                    <th ><?php echo $data['close_bal']; ?></th>
		            </tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<?= $this->pagination->create_links(); ?>

<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/report/daily_transaction.js')?>"></script>
	</body>
</html>