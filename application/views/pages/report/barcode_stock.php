<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$pt_amt_frm 	= (isset($_GET['pt_amt_frm'])) ? $_GET['pt_amt_frm'] : "";
	$pt_amt_to 		= (isset($_GET['pt_amt_to'])) ? $_GET['pt_amt_to'] : "";
	$st_rate_frm 	= (isset($_GET['st_rate_frm'])) ? $_GET['st_rate_frm'] : "";
	$st_rate_to 	= (isset($_GET['st_rate_to'])) ? $_GET['st_rate_to'] : "";
	$st_amt_frm 	= (isset($_GET['st_amt_frm'])) ? $_GET['st_amt_frm'] : "";
	$st_amt_to 		= (isset($_GET['st_amt_to'])) ? $_GET['st_amt_to'] : "";
	$bal_qty_frm 	= (isset($_GET['bal_qty_frm'])) ? $_GET['bal_qty_frm'] : "";
	$bal_qty_to 	= (isset($_GET['bal_qty_to'])) ? $_GET['bal_qty_to'] : "";
	$bal_amt_frm 	= (isset($_GET['bal_amt_frm'])) ? $_GET['bal_amt_frm'] : "";
	$bal_amt_to 	= (isset($_GET['bal_amt_to'])) ? $_GET['bal_amt_to'] : "";
	$profit_frm 	= (isset($_GET['profit_frm'])) ? $_GET['profit_frm'] : "";
	$profit_to 		= (isset($_GET['profit_to'])) ? $_GET['profit_to'] : "";
	$_token 	= (isset($_GET['_token'])) ? $_GET['_token'] : "";

	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>   
    let link 	= "report";
    let sub_link= "barcode_stock";
</script>

<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/barcode_stock?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	BARCODE STOCK(<span><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/barcode_stock?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/barcode_stock?submit=PDF&$url"); ?>">
			    			<i class="text-success fa fa-print"></i>
			    		</a>
			    	<?php else: ?>
			    		<button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" disabled="disabled">
			    			<i class="text-success fa fa-print"></i>
			    		</button>
			    	<?php endif; ?>
			    </li>
			    <li class="breadcrumb-item" aria-current="update-barcode">
			    	<a type="button" onclick='multiple_barcode_popup();' class="btn btn-md btn-primary mx-2" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="UPDATE SELLING PRICE"><i class="text-success fa fa-plus"></i></a>
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
					<?php if(isset($data['search']['bm_id'])): ?><p>BARCODE</p><?php endif; ?>
					<select class="form-control floating-select" id="bm_id" name="bm_id[]" multiple="true">
                    	<?php if (isset($data['search']['bm_id']) && !empty($data['search']['bm_id'])): ?>
						    <?php 
						        $values = explode(',', $data['search']['bm_id']['value']);
						        $texts  = explode(',', $data['search']['bm_id']['text']);
						        foreach ($values as $i => $val): 
						    ?>
						        <option value="<?php echo trim($val); ?>" selected>
						            <?php echo isset($texts[$i]) ? trim($texts[$i]) : trim($val); ?>
						        </option>
						    <?php endforeach; ?>
						<?php endif; ?>
                	</select>
				</div>
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
				<div class="col-6 col-sm-4 col-md-3 col-lg-1 floating-label">
					<p>TOKEN</p>
					<select class="form-control floating-select" id="_token" name="_token">
                    	<option value="">ALL</option>
                    	<option value="YES" <?php echo ($_token=='YES') ?'selected' : ''?>>YES</option>
                    	<option value="NO" <?php echo ($_token=='NO') ?'selected' : ''?>>NO</option>
                	</select>
				</div>
				
			</div>
		</div>
		
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
				<table class="table table-sm">
					<thead class="table-dark">
						<tr>
			                <td width="3%">#</td>
			                <th width="10%">BARCODE</th>
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
		                    <th width="5%">BALANCE QTY</th>
		                    <th width="5%">STOCK AMT</th>
		                    <th width="5%">PROFIT AMT</th>
		                    <th width="5%">TOKEN</th>
			            </tr>
			            <tr style="font-size: 15px; font-weight: bold;">
			                <td ></td>
			                <td ></td>
			                <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ></td>
		                    <td ><?php echo $data['totals']['pt_qty']; ?></td>
		                    <td ></td>
		                    <td ><?php echo round($data['totals']['pt_amt'], 2); ?></td>
		                    <td ><?php echo $data['totals']['prt_qty']; ?></td>
		                    <td ><?php echo $data['totals']['st_qty']; ?></td>
		                    <td></td>
		                    <td ><?php echo round($data['totals']['st_amt'], 2); ?></td>
		                    <td ><?php echo $data['totals']['srt_qty']; ?></td>
		                    <td ><?php echo $data['totals']['bal_qty']; ?></td>
		                    <td ><?php echo round($data['totals']['bal_amt'], 2); ?></td>
		                    <td ><?php echo round($data['totals']['profit_amt'], 2); ?></td>
		                    <td ><?php echo round($data['totals']['token_amt'], 2); ?></td>

			            </tr>
					</thead>
				</table>	
				<div class="col-12">
					<form id="stock_form">	
						<div id="scroll_wrapper" style="height: 50vh; overflow-y: auto;">
							<table class="table table-sm table-reponsive table-hover" id="table_wrapper">	 
							<?php 
								if(!empty($data['data'])): ?>
									<tbody id="report_wrapper">
								<?php foreach (array_slice($data['data'], 0, PER_PAGE) as $key => $value): ?>
									<?php //foreach ($data['data'] as $key => $value): ?>
										<tr>
											<td width="3%"><?php echo $key+1; ?></td>   
											<td width="10%">
											<?php if($value['bal_qty'] != 0): ?>
												<input type="checkbox" name="barcode_id[<?php echo $value['bm_id']?>]" id="barcode_id_<?php echo $value['bm_id']?>" style="height: 20px; width: 20px">
											<?php endif; ?>	
												<a target="_blank" href="<?php echo base_url('/purchase?action=single_print&clause=bm.bm_id&id='.$value['bm_id']) ?>" data-toggle="tooltip" data-placement="bottom" title="PRINT">
													<?php echo $value['bm_item_code']; ?>
												</a>		
												<br/>								
												<a class="mr-5" target="_blank" href="<?php echo base_url('purchase?action=edit&id='.$value['pm_id']) ?>" data-toggle="tooltip" data-placement="bottom" title="PURCHASE">
													<i class="fa fa-eye"></i>
												</a>										
												<?php if($value['bal_qty'] != 0): ?>
													<a  href="#" onclick='barcode_popup(<?php echo json_encode($value); ?>);' data-toggle="tooltip" data-placement="bottom" title="SINGLE">
														<i class="fa fa-edit"></i>
													</a>
												<?php endif; ?>										
											</td> 
											<td width="8%"><?php echo $value['account_code']; ?></td>
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
											<td width="5%"><?php echo $value['bal_qty']; ?></td>
											<td width="5%"><?php echo round($value['bal_amt'],2); ?></td>
											<td width="5%"><?php echo round($value['profit_amt'],2); ?></td>
											<td width="5%"><?php echo $value['token']; ?></td>
										</tr>
									<?php endforeach; ?>	
									</tbody>	
							<?php
								else: 
							?>
								<tr>
									<td class="text-danger font-weight-bold text-center" colspan="10">NO RECORD FOUND!!!</td>
								</tr>
							<?php endif; ?>
							</table>
						</div>
					</form>
				</div>	
		</div>
	</div>
</section> 
<?= $this->pagination->create_links(); ?>
<?php $this->load->view('templates/footer'); ?>
<script>
		const queryString = "<?php echo $_SERVER['QUERY_STRING']; ?>";
</script>
	<script src="<?php echo assets('dist/js/report.js?v=1')?>"></script>
	<script src="<?php echo assets('dist/js/report/barcode_stock.js?v=4')?>"></script>
	<script type="text/javascript"> let raw = <?php echo json_encode($data['data']); ?></script>
</body>
</html>