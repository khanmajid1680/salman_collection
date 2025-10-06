<?php 
	$this->load->view('templates/header'); 
	$action 			= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 		= !isset($_GET['search_status']);
	$debit_frm 			= (isset($_GET['debit_frm'])) ? $_GET['debit_frm'] : "";
	$debit_to 			= (isset($_GET['debit_to'])) ? $_GET['debit_to'] : "";
	$debited_frm 		= (isset($_GET['debited_frm'])) ? $_GET['debited_frm'] : "";
	$debited_to 		= (isset($_GET['debited_to'])) ? $_GET['debited_to'] : "";
	$bal_frm 			= (isset($_GET['bal_frm'])) ? $_GET['bal_frm'] : "";
	$bal_to 			= (isset($_GET['bal_to'])) ? $_GET['bal_to'] : "";
	$label 				= (isset($_GET['label'])) ? $_GET['label'] : 0;
	$url 				= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "general_outstanding";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/general_outstanding?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	GENERAL OUTSTANDING(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/general_outstanding?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/general_outstanding?submit=PDF&$url"); ?>">
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
					<?php if(isset($data['search']['acc_id'])): ?><p>GENERAL</p><?php endif; ?>
					<select class="form-control floating-select" id="acc_id" name="acc_id">
                    	<?php if(isset($data['search']['acc_id']) && !empty($data['search']['acc_id'])): ?>
                        	<option value="<?php echo $data['search']['acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
<!-- 				<div class="col-6 col-sm-3 col-md-2 col-lg-1 floating-label">
                    <select class="form-control floating-select" id="label" name="label">
                    	<option value=0 <?php echo $label == 0 ? 'selected' : ''; ?>>ALL</option>
                    	<option value=1 <?php echo $label == 1 ? 'selected' : ''; ?>>TO RECEIVE</option>
                    	<option value=-1 <?php echo $label == -1 ? 'selected' : ''; ?>>TO PAY</option>
                	</select>
				</div> -->
				<div class="d-flex col-6 col-sm-4 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="bal_frm" name="bal_frm" value="<?php echo $bal_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM BAL AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="bal_to" name="bal_to" value="<?php echo $bal_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO BAL AMT</label>
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
			                <th width="10%">GENERAL</th>
			                <th width="10%">TYPE</th>
		                    <th width="10%">OPENING AMT</th>
		                    <!-- <th width="10%">AMT TO CREDIT</th> -->
		                    <th width="10%">CREDITED AMT</th>
		                    <!-- <th width="10%">AMT TO DEBIT</th> -->
		                    <th width="10%">DEBITED AMT</th>
		                    <th width="10%">BALANCE AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
			                <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ><?php echo round($data['totals']['open_amt'], 2); ?></td>
		                    <!-- <td ><?php echo round($data['totals']['credit_amt'], 2); ?></td> -->
		                    <td ><?php echo round($data['totals']['credited_amt'], 2); ?></td>
		                    <!-- <td ><?php echo round($data['totals']['debit_amt'], 2); ?></td> -->
		                    <td ><?php echo round($data['totals']['debited_amt'], 2); ?></td>
		                    <td ><?php echo round($data['totals']['bal_amt'], 2); ?></td>
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
							foreach ($data['data'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="10%"><?php echo $value['account_name']; ?></td>
									<td width="10%"><?php echo $value['account_drcr']; ?></td>
									<td width="10%"><?php echo round($value['open_amt'], 2); ?></td>
									<!-- <td width="10%"><?php echo round($value['credit_amt'], 2); ?></td> -->
									<td width="10%"><?php echo round($value['credited_amt'], 2); ?></td>
									<!-- <td width="10%"><?php echo round($value['debit_amt'], 2); ?></td> -->
									<td width="10%"><?php echo round($value['debited_amt'], 2); ?></td>
									<td width="10%"><?php echo round($value['bal_amt'], 2); ?></td>

								</tr>
					<?php 
							endforeach;
					?>
								<tr style="font-size: 15px; font-weight: bold;">
									<td ></td>
									<td ></td>
				                    <td >TOTALS</td>
				                    <td ><?php echo round($data['totals']['open_amt'], 2); ?></td>
				                    <!-- <td ><?php echo round($data['totals']['credit_amt'], 2); ?></td> -->
				                    <td ><?php echo round($data['totals']['credited_amt'], 2); ?></td>
				                    <!-- <td ><?php echo round($data['totals']['debit_amt'], 2); ?></td> -->
				                    <td ><?php echo round($data['totals']['debited_amt'], 2); ?></td>
				                    <td ><?php echo round($data['totals']['bal_amt'], 2); ?></td>
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
	<script src="<?php echo assets('dist/js/report/general_outstanding.js')?>"></script>
	</body>
</html>