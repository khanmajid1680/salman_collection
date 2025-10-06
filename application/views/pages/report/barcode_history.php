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
    let sub_link= "barcode_history";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/barcode_history?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	BARCODE HISTORY(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/barcode_history?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
					<?php if(isset($data['search']['_item_code'])): ?><p>SCAN BARCODE</p><?php endif; ?>
					<select class="form-control floating-select" id="_item_code" name="_item_code">
                    	<?php if(isset($data['search']['_item_code']) && !empty($data['search']['_item_code'])): ?>
                        	<option value="<?php echo $data['search']['_item_code']['value']; ?>" selected>
                            	<?php echo $data['search']['_item_code']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12 d-flex flex-wrap mt-2">
			<?php 
				if(!empty($data['data'])): 
					foreach ($data['data'] as $key => $value):
			?>
						<div class="col-12 col-sm-6 col-md-6 col-lg-6">
							<h6 class="text-center bg-dark text-light neu_flat_secondary">PRODUCT DETAIL</h6>
							<table class="table table-sm table-reponsive">
								<tbody class="font-weight-bold" style="font-size:0.8em;">
									<tr>
										<td width="32%">DESIGN</td>
										<td width="68%">: <?php echo $value['design_name']; ?></td>
									</tr>
									<tr>
										<td width="32%">STYLE</td>
										<td width="68%">: <?php echo $value['style_name']; ?></td>
									</tr>
									<tr>
										<td width="32%">BRAND</td>
										<td width="68%">: <?php echo $value['brand_name']; ?></td>
									</tr>
									<tr>
										<td width="32%">DESCRIPTION</td>
										<td width="68%">: <?php echo $value['description']; ?></td>
									</tr>
									<tr>
										<td width="32%">LAST LOCATION</td>
										<td width="68%">: <?php echo $value['branch_name']; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-12 col-sm-6 col-md-6 col-lg-6">
							<h6 class="text-center bg-dark text-light neu_flat_secondary">BARCODE DETAIL</h6>
							<table class="table table-sm table-reponsive">
								<tbody class="font-weight-bold" style="font-size:0.8em;">
									<tr>
										<td width="32%">BARCODE</td>
										<td width="68%">: <?php echo $value['bm_item_code']; ?></td>
									</tr>
									<tr>
										<td width="32%">SUPPLIER</td>
										<td width="68%">: <?php echo $value['account_name']; ?></td>
									</tr>
									<tr>
										<td width="32%">PURCHASE QTY</td>
										<td width="68%">: <?php echo $value['pt_qty']; ?></td>
									</tr>
									<tr>
										<td width="32%">SELLING PRICE</td>
										<td width="68%">: <?php echo $value['st_rate']; ?></td>
									</tr>
									<tr>
										<td width="32%">
											BALANCE QTY
										</td>
										<td width="68%">: <?php echo $value['bal_qty']; ?> </td>
									</tr>
									<?php if($value['bm_delete_status'] == 1): ?>
										<tr>
											<td width="32%">STATUS</td>
											<td width="68%">
												: 
												<span class="font-italic font-weight-bold text-danger">
													DELETED
												</span>
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-12">
							<h6 class="text-center bg-dark text-light neu_flat_secondary py-1">BARCODE HISTORY</h6>
							<table class="table table-sm table-reponsive">
								<tbody class="font-weight-bold" style="font-size:0.8em;">
									<tr>
										<td width="4%">SR NO</td>
										<td width="10%">ACTION</td>
										<td width="10%">BRANCH</td>
										<td width="5%">ENTRY NO</td>
										<td width="8%">ENTRY DATE</td>
										<td width="8%">ENTRY TIME</td>
										<td width="8%">ENTRY BY</td>
									</tr>
									<?php 
										if($value['history_data']): 
											$sr_no = 1;
											foreach ($value['history_data'] as $k => $v):
									?>
												<tr>
													<td ><?php echo $sr_no; ?></td>
													<td ><?php echo $v['module']; ?></td>
													<td ><?php echo $v['branch_name']; ?></td>
													<td ><?php echo $v['entry_no']; ?></td>
													<td ><?php echo $v['entry_date']; ?></td>
													<td ><?php echo date('h:i:s a', strtotime($v['created_at'])); ?></td>
													<td ><?php echo $v['user_name']; ?></td>
												</tr>		
									<?php 
												$sr_no++;
											endforeach;
										else:
									?>
										<tr>
											<td class="text-danger font-weight-bold text-center" colspan="11">NO RECORD FOUND!!!</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
			<?php 
					endforeach;
				else: 
			?>
					<h5 class="text-danger font-weight-bold text-center my-2 w-100">NO RECORD FOUND!!!</h5>
			<?php endif; ?>		
		</div>
	</div>
</section>
<?php $this->load->view('templates/footer'); ?>
<script type="text/javascript">
	$("#_item_code").select2(select2_default({
        url:`report/barcode_history/get_select2/_item_code`,
        placeholder:'SCAN BARCODE',
        maximumInputLength:12,
        minimumInputLength:12,
        barcode:'_item_code',
    })).on('change', () => trigger_search());
</script>
</body>
</html>