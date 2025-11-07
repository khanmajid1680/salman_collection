<?php 
	$this->load->view('templates/header'); 
	$action 			= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 		= !isset($_GET['search_status']);
	$pt_amt_frm 		= (isset($_GET['pt_amt_frm'])) ? $_GET['pt_amt_frm'] : "";
	$pt_amt_to 			= (isset($_GET['pt_amt_to'])) ? $_GET['pt_amt_to'] : "";
	$st_amt_frm 		= (isset($_GET['st_amt_frm'])) ? $_GET['st_amt_frm'] : "";
	$st_amt_to 			= (isset($_GET['st_amt_to'])) ? $_GET['st_amt_to'] : "";
	$sold_amt_frm 		= (isset($_GET['sold_amt_frm'])) ? $_GET['sold_amt_frm'] : "";
	$sold_amt_to 		= (isset($_GET['sold_amt_to'])) ? $_GET['sold_amt_to'] : "";
	$bal_qty_frm 		= (isset($_GET['bal_qty_frm'])) ? $_GET['bal_qty_frm'] : 1;
	$bal_qty_to 		= (isset($_GET['bal_qty_to'])) ? $_GET['bal_qty_to'] : "";
	$bal_amt_frm 		= (isset($_GET['bal_amt_frm'])) ? $_GET['bal_amt_frm'] : "";
	$bal_amt_to 		= (isset($_GET['bal_amt_to'])) ? $_GET['bal_amt_to'] : "";
	$url 				= $_SERVER['QUERY_STRING'];
?>
<script> 
    let link 	= "report";
    let sub_link= "balance_stock";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/balance_stock?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	BALANCE STOCK(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/balance_stock?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/balance_stock?submit=PDF&$url"); ?>">
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
					<?php if(isset($data['search']['bm_acc_id'])): ?><p>SUPPLIER</p><?php endif; ?>
					<select class="form-control floating-select" id="bm_acc_id" name="bm_acc_id">
                    	<?php if(isset($data['search']['bm_acc_id']) && !empty($data['search']['bm_acc_id'])): ?>
                        	<option value="<?php echo $data['search']['bm_acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['bm_acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['bm_style_id'])): ?><p>STYLE</p><?php endif; ?>
					<select class="form-control floating-select" id="bm_style_id" name="bm_style_id">
                    	<?php if(isset($data['search']['bm_style_id']) && !empty($data['search']['bm_style_id'])): ?>
                        	<option value="<?php echo $data['search']['bm_style_id']['value']; ?>" selected>
                            	<?php echo $data['search']['bm_style_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['bm_design_id'])): ?><p>DESIGN</p><?php endif; ?>
					<select class="form-control floating-select" id="bm_design_id" name="bm_design_id">
                    	<?php if(isset($data['search']['bm_design_id']) && !empty($data['search']['bm_design_id'])): ?>
                        	<option value="<?php echo $data['search']['bm_design_id']['value']; ?>" selected>
                            	<?php echo $data['search']['bm_design_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['bm_brand_id'])): ?><p>BRAND</p><?php endif; ?>
					<select class="form-control floating-select" id="bm_brand_id" name="bm_brand_id">
                    	<?php if(isset($data['search']['bm_brand_id']) && !empty($data['search']['bm_brand_id'])): ?>
                        	<option value="<?php echo $data['search']['bm_brand_id']['value']; ?>" selected>
                            	<?php echo $data['search']['bm_brand_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				
				<div class="d-flex col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="pt_amt_frm" name="pt_amt_frm" value="<?php echo $pt_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FRM PUR AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="pt_amt_to" name="pt_amt_to" value="<?php echo $pt_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO PUR AMT</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="st_amt_frm" name="st_amt_frm" value="<?php echo $st_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FRM SALE AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="st_amt_to" name="st_amt_to" value="<?php echo $st_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO SALE AMT</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sold_amt_frm" name="sold_amt_frm" value="<?php echo $sold_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FRM SOLD AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="sold_amt_to" name="sold_amt_to" value="<?php echo $sold_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO SOLD AMT</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="bal_qty_frm" name="bal_qty_frm" value="<?php echo $bal_qty_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FRM BAL QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="bal_qty_to" name="bal_qty_to" value="<?php echo $bal_qty_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO BAL QTY</label>
					</div>
				</div>
				<div class="d-flex mt-2 col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="bal_amt_frm" name="bal_amt_frm" value="<?php echo $bal_amt_frm ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FRM STOCK AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="bal_amt_to" name="bal_amt_to" value="<?php echo $bal_amt_to ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO STOCK AMT</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="8%">SUPPLIER</th>
		                    <th width="7%">STYLE</th>
		                    <th width="7%">DESIGN</th>
		                    <th width="7%">BRAND</th>
		                    <th width="5%">PUR QTY</th>
		                    <th width="5%">PUR RATE</th>
		                    <th width="5%">PUR AMT</th>
		                    <th width="5%">PUR RET. QTY</th>
		                    <th width="5%">SALE QTY</th>
		                    <th width="5%">SALE RATE</th>
		                    <th width="5%">SALE AMT</th>
		                    <th width="5%">SALE RET QTY</th>
		                    <th width="5%">SOLD QTY <br/> X <br/> PUR RATE</th>
		                    <th width="5%">BALANCE QTY</th>
		                    <th width="5%">STOCK AMT</th>
			            </tr>
					</thead>
					<tbody>
						<tr style="font-size: 15px; font-weight: bold;">
			                <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ><?php echo $data['totals']['pt_qty']; ?></td>
		                    <td ></td>
		                    <td ><?php echo round($data['totals']['pt_amt'], 2); ?></td>
		                    <td ><?php echo $data['totals']['prt_qty']; ?></td>
		                    <td ><?php echo $data['totals']['st_qty']; ?></td>
		                    <td ></td>
		                    <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
		                    <td ><?php echo $data['totals']['srt_qty']; ?></td>
		                    <td ><?php echo round($data['totals']['sold_amt'], 2); ?></td>
		                    <td ><?php echo $data['totals']['bal_qty']; ?></td>
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
			<table class="table table-sm table-hover">
				<tbody>
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):

					?>
								<tr>
									<td width="8%"><?php echo $value['account_name']; ?></td>
									<td width="7%"><?php echo $value['style_name']; ?></td>
									<td width="7%"><?php echo $value['design_name']; ?></td>
									<td width="7%"><?php echo $value['brand_name']; ?></td>
									<td width="5%"><?php echo $value['pt_qty']; ?></td>
									<td width="5%"><?php echo round($value['pt_rate'], 2); ?></td>
									<td width="5%"><?php echo round($value['pt_amt'],2); ?></td>
									<td width="5%"><?php echo $value['prt_qty']; ?></td>
									<td width="5%"><?php echo $value['st_qty']; ?></td>
									<td width="5%"><?php echo round($value['st_rate'], 2); ?></td>
									<td width="5%"><?php echo round($value['st_amt'],2); ?></td>
									<td width="5%"><?php echo $value['srt_qty']; ?></td>
									<td width="5%"><?php echo round($value['sold_amt'],2); ?></td>
									<td width="5%"><?php echo $value['bal_qty']; ?></td>
									<td width="5%"><?php echo round($value['bal_amt'],2); ?></td>

								</tr>
					<?php 
							endforeach;
					?>
								<tr style="font-size: 15px; font-weight: bold;">
									<td ></td>
				                    <td ></td>
				                    <td ></td>
				                    <td >TOTALS</td>
				                    <td ><?php echo $data['totals']['pt_qty']; ?></td>
				                    <td ></td>
				                    <td ><?php echo round($data['totals']['pt_amt'], 2); ?></td>
				                    <td ><?php echo $data['totals']['prt_qty']; ?></td>
				                    <td ><?php echo $data['totals']['st_qty']; ?></td>
				                    <td ></td>
				                    <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
				                    <td ><?php echo $data['totals']['srt_qty']; ?></td>
				                    <td ><?php echo round($data['totals']['sold_amt'], 2); ?></td>
				                    <td ><?php echo $data['totals']['bal_qty']; ?></td>
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
	<script src="<?php echo assets('dist/js/report/balance_stock.js')?>"></script>
	</body>
</html>