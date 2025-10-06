<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$account_id 	= isset($_GET['account_id']) && !empty($_GET['account_id']) ? $_GET['account_id'] : '';
	$constant 		= isset($_GET['constant']) && !empty($_GET['constant']) ? $_GET['constant'] : '';
	$url 			= $_SERVER['QUERY_STRING'];
	$_date_from 	= date('d-m-Y');
	$_date_to 		= date('d-m-Y');
	if(!isset($_GET['_date_from'])){
		if(date('Y-m-d') > $_SESSION['start_year'] && date('Y-m-d') < $_SESSION['end_year']){
			$_date_from 	= date('d-m-Y');
		}else{
			$_date_from = date('d-m-Y', strtotime($_SESSION['start_year']));
		}
	}else{
		$_date_from = date('d-m-Y', strtotime($_GET['_date_from']));
	}
	if(!isset($_GET['_date_to'])){
		if(date('Y-m-d') > $_SESSION['start_year'] && date('Y-m-d') < $_SESSION['end_year']){
			$_date_to 		= date('d-m-Y');
		}else{
			$_date_to = date('d-m-Y', strtotime($_SESSION['end_year']));
		}
	}else{
		$_date_to = date('d-m-Y', strtotime($_GET['_date_to']));
	}
?>
<script>
    let link 	= "report";
    let sub_link= "general_ledger";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/general_ledger?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	GENERAL LEDGER
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/general_ledger?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/general_ledger?submit=PDF&$url"); ?>">
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
				<div class="col-12 col-sm-12 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['account_id'])): ?><p>GENERAL A/C</p><?php endif; ?>
					<select class="form-control floating-select" id="account_id" name="account_id">
                    	<?php if(isset($data['search']['account_id']) && !empty($data['search']['account_id'])): ?>
                        	<option value="<?php echo $data['search']['account_id']['value']; ?>" selected>
                            	<?php echo $data['search']['account_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-4 col-md-4 col-lg-4">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="_date_from" name="_date_from" value="<?php echo $_date_from ?>" placeholder=" " autocomplete="off" onchange="trigger_search()"/>   
	                    <label for="inputEmail3">FROM DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="_date_to" name="_date_to" value="<?php echo $_date_to ?>" placeholder=" " autocomplete="off" onchange="trigger_search()"/>   
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
	                    <th ><?php echo $data['open_amt']; ?></th>
	                    <th ><?php echo $data['sales_amt']; ?></th>
	                    <th colspan="2"><?php echo $data['return_amt']; ?></th>
	                    <th ><?php echo $data['receipt_amt']; ?></th>
	                    <th ><?php echo $data['payment_amt']; ?></th>
	                    <th ><?php echo $data['close_amt']; ?></th>
		            </tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<?= $this->pagination->create_links(); ?>

<?php $this->load->view('templates/footer'); ?>
	<script type="text/javascript">
		$("#account_id").select2(select2_default({
	        url:`report/general_ledger/get_select2/account_id`,
	        placeholder:'GENERAL A/C'
	    })).on('change', () => trigger_search());
	</script>
	</body>
</html>