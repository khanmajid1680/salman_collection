<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
?>
<script>
    let link 	= "report";
    let sub_link= "supplier_mis";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/supplier_mis?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	SUPPLIER MIS
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/supplier_mis?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
			</div>
		</div>
		<div class="row">
			<div class="col-4">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="3%">#</th>
                            <th width="20%">SUPPLIER</th>
                            <th width="10%">BILL AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
							<td ></td>
							<td ><?php echo round($data['totals']['bill_amt'], 2); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-4">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="3%">#</th>
                            <th width="20%">SUPPLIER</th>
                            <th width="10%">SALE AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
							<td ></td>
							<td ><?php echo round($data['totals']['sale_amt'], 2); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-4">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="3%">#</th>
                            <th width="20%">SUPPLIER</th>
                            <th width="10%">PAYMENT AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
							<td ></td>
							<td ></td>
							<td ><?php echo round($data['totals']['voucher_amt'], 2); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-4">
			<table class="table table-sm table-reponsive table-hover">
				<tbody>
					<?php 
						if(!empty($data['data'])):
							foreach ($data['data'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="20%"><?php echo $value['account_name']; ?></td>
									<td width="10%"><?php echo round($value['bill_amt'], 2); ?></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
								<td >TOTALS</td>
								<td ><?php echo round($data['totals']['bill_amt'], 2); ?></td>
							</tr>
					<?php else: ?>
						<tr>
							<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-4">
			<table class="table table-sm table-reponsive table-hover">
				<tbody>
					<?php 
						if(!empty($data['sdata'])):
							foreach ($data['sdata'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="20%"><?php echo $value['account_name']; ?></td>
									<td width="10%"><?php echo round($value['sale_amt'], 2); ?></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
								<td >TOTALS</td>
								<td ><?php echo round($data['totals']['sale_amt'], 2); ?></td>
							</tr>
					<?php else: ?>
						<tr>
							<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="col-4">
			<table class="table table-sm table-reponsive table-hover">
				<tbody>
					<?php 
						if(!empty($data['vdata'])):
							foreach ($data['vdata'] as $key => $value):
					?>
								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="20%"><?php echo $value['account_name']; ?></td>
									<td width="10%"><?php echo round($value['voucher_amt'], 2); ?></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td ></td>
								<td >TOTALS</td>
								<td ><?php echo round($data['totals']['voucher_amt'], 2); ?></td>
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
		<script src="<?php echo assets('dist/js/report/supplier_mis.js')?>"></script>
	</body>
</html>