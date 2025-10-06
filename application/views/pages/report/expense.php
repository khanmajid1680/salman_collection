<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$amt_from 		= (isset($_GET['amt_from'])) ? $_GET['amt_from'] : "";
	$amt_to 		= (isset($_GET['amt_to'])) ? $_GET['amt_to'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
	$from_date 		= date('d-m-Y', strtotime($_SESSION['start_year']));
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
			$to_date = date('d-m-Y', strtotime($_SESSION['end_year']));
		}
	}else{
		$to_date = date('d-m-Y', strtotime($_GET['to_date']));
	}
?>
<script>
    let link 	= "report";
    let sub_link= "expense";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/expense?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	EXPENSE REPORT(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/expense?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/expense?submit=PDF&$url"); ?>">
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
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_date" name="from_date" value="<?php echo $from_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_date" name="to_date" value="<?php echo $to_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO DATE</label>
					</div>
				</div>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3 floating-label">
					<?php if(isset($data['search']['_party_id'])): ?><p>DESCRIPTION</p><?php endif; ?>
					<select class="form-control floating-select" id="_party_id" name="_party_id">
                    	<?php if(isset($data['search']['_party_id']) && !empty($data['search']['_party_id'])): ?>
                        	<option value="<?php echo $data['search']['_party_id']['value']; ?>" selected>
                            	<?php echo $data['search']['_party_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="amt_from" name="amt_from" value="<?php echo $amt_from ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="amt_to" name="amt_to" value="<?php echo $amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO AMT</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="5%">#</th>
                            <th width="15%">DESCRIPTION</th>
                            <th width="10%">AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
							<td ></td>
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
									<td width="5%"><?php echo $key+1; ?></td>
									<td width="15%"><?php echo $value['account_name']; ?></td>
									<td width="10%"><?php echo round($value['total_amt'], 2); ?></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
								<td >TOTALS</td>
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
		<script src="<?php echo assets('dist/js/report/expense.js')?>"></script>
	</body>
</html>