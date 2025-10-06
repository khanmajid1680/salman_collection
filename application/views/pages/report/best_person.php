<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y', strtotime($_SESSION['start_year']));
	$to_date 		= (isset($_GET['to_date'])) ? $_GET['to_date'] : date('d-m-Y', strtotime($_SESSION['end_year']));
	$sm_qty_frm 	= (isset($_GET['sm_qty_frm'])) ? $_GET['sm_qty_frm'] : "";
	$sm_qty_to 		= (isset($_GET['sm_qty_to'])) ? $_GET['sm_qty_to'] : "";
	$srm_qty_frm 	= (isset($_GET['srm_qty_frm'])) ? $_GET['srm_qty_frm'] : "";
	$srm_qty_to 	= (isset($_GET['srm_qty_to'])) ? $_GET['srm_qty_to'] : "";
	$sale_qty_frm 	= (isset($_GET['sale_qty_frm'])) ? $_GET['sale_qty_frm'] : "";
	$sale_qty_to 	= (isset($_GET['sale_qty_to'])) ? $_GET['sale_qty_to'] : "";
	$sm_amt_frm 	= (isset($_GET['sm_amt_frm'])) ? $_GET['sm_amt_frm'] : "";
	$sm_amt_to 		= (isset($_GET['sm_amt_to'])) ? $_GET['sm_amt_to'] : "";
	$srm_amt_frm 	= (isset($_GET['srm_amt_frm'])) ? $_GET['srm_amt_frm'] : "";
	$srm_amt_to 	= (isset($_GET['srm_amt_to'])) ? $_GET['srm_amt_to'] : "";
	$sale_amt_frm 	= (isset($_GET['sale_amt_frm'])) ? $_GET['sale_amt_frm'] : "";
	$sale_amt_to 	= (isset($_GET['sale_amt_to'])) ? $_GET['sale_amt_to'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "best_person";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/best_person?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	BEST SALES PERSON(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/best_person?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/best_person?submit=PDF&$url"); ?>">
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
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['user_id'])): ?><p>SALES PERSON</p><?php endif; ?>
					<select class="form-control floating-select" id="user_id" name="user_id">
                    	<?php if(isset($data['search']['user_id']) && !empty($data['search']['user_id'])): ?>
                        	<option value="<?php echo $data['search']['user_id']['value']; ?>" selected>
                            	<?php echo $data['search']['user_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sm_qty_frm" name="sm_qty_frm" value="<?php echo $sm_qty_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM SALE QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sm_qty_to" name="sm_qty_to" value="<?php echo $sm_qty_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO SALE QTY</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="srm_qty_frm" name="srm_qty_frm" value="<?php echo $srm_qty_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM RETURN QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="srm_qty_to" name="srm_qty_to" value="<?php echo $srm_qty_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO RETURN QTY</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sale_qty_frm" name="sale_qty_frm" value="<?php echo $sale_qty_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ACT. SALE QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sale_qty_to" name="sale_qty_to" value="<?php echo $sale_qty_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ACT. SALE QTY</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sm_amt_frm" name="sm_amt_frm" value="<?php echo $sm_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM SALE AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sm_amt_to" name="sm_amt_to" value="<?php echo $sm_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO SALE AMT</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="srm_amt_frm" name="srm_amt_frm" value="<?php echo $srm_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM RETURN AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="srm_amt_to" name="srm_amt_to" value="<?php echo $srm_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO RETURN AMT</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sale_amt_frm" name="sale_amt_frm" value="<?php echo $sale_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ACT. SALE AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sale_amt_to" name="sale_amt_to" value="<?php echo $sale_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ACT. SALE AMT</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="10%">SALES PERSON</th>
			                <th width="10%">SALE QTY</th>
		                    <th width="10%">SALE RETURN QTY</th>
		                    <th width="10%">ACTUAL SALE QTY</th>
		                    <th width="10%">SALE AMT</th>
		                    <th width="10%">SALE RETURN AMT</th>
		                    <th width="10%">ACTUAL SALE AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
		                    <td ></td>
		                    <td ><?php echo $data['totals']['sm_qty']; ?></td>
		                    <td ><?php echo $data['totals']['srm_qty']; ?></td>
		                    <td ><?php echo $data['totals']['sale_qty']; ?></td>
		                    <td ><?php echo round($data['totals']['sm_amt'], 2); ?></td>
		                    <td ><?php echo round($data['totals']['srm_amt'], 2); ?></td>
		                    <td ><?php echo round($data['totals']['sale_amt'], 2); ?></td>
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
			<table class="table table-sm table-hover">
				<tbody>
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):

					?>
								<tr>
									<td width="10%"><?php echo $value['user_fullname']; ?></td>
									<td width="10%"><?php echo $value['sm_qty']; ?></td>
									<td width="10%"><?php echo $value['srm_qty']; ?></td>
									<td width="10%"><?php echo $value['sale_qty']; ?></td>
									<td width="10%"><?php echo round($value['sm_amt'], 2); ?></td>
									<td width="10%"><?php echo round($value['srm_amt'],2); ?></td>
									<td width="10%"><?php echo round($value['sale_amt'],2); ?></td>

								</tr>
					<?php 
							endforeach;
					?>
								<tr style="font-size: 15px; font-weight: bold;">
									<td >TOTALS</td>
				                    <td ><?php echo $data['totals']['sm_qty']; ?></td>
				                    <td ><?php echo $data['totals']['srm_qty']; ?></td>
				                    <td ><?php echo $data['totals']['sale_qty']; ?></td>
				                    <td ><?php echo round($data['totals']['sm_amt'], 2); ?></td>
				                    <td ><?php echo round($data['totals']['srm_amt'], 2); ?></td>
				                    <td ><?php echo round($data['totals']['sale_amt'], 2); ?></td>
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
	<script src="<?php echo assets('dist/js/report/best_person.js')?>"></script>
	</body>
</html>