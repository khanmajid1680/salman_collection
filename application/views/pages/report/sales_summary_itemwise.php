<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_entry_date= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
	$to_entry_date 	= (isset($_GET['to_entry_date'])) ? $_GET['to_entry_date'] : "";
	$from_bill_date = (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
	$to_bill_date   = (isset($_GET['to_bill_date'])) ? $_GET['to_bill_date'] : "";
	$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
	$to_qty 		= (isset($_GET['to_qty'])) ? $_GET['to_qty'] : "";
	$from_bill_amt 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
	$to_bill_amt 	= (isset($_GET['to_bill_amt'])) ? $_GET['to_bill_amt'] : "";

	$_token 	= (isset($_GET['_token'])) ? $_GET['_token'] : "";
	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "sales_summary_itemwise";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/sales_summary_itemwise?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="<?php echo base_url('report/sales_summary_itemwise?action=view'); ?>">REPORT</a></li>
			    <li class="breadcrumb-item active" aria-current="page">
			    	SALE SUMMARY ITEMWISE(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/sales_summary_itemwise?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			     <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="EXCEL" href="<?php echo base_url("report/sales_summary_itemwise/excel?$url"); ?>">
			    			<i class="text-success fa fa-file-excel-o"></i>
			    		</a>
			    	<?php else: ?>
			    		<button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" disabled="disabled">
			    			<i class="text-success fa fa-file-excel-o"></i>
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
				<div class="col-6 col-sm-6 col-md-3 col-lg-1 floating-label">
					<?php if(isset($data['search']['sm_bill_no'])): ?><p>BILL NO</p><?php endif; ?>
					<select class="form-control floating-select" id="sm_bill_no" name="sm_bill_no">
                    	<?php if(isset($data['search']['sm_bill_no']) && !empty($data['search']['sm_bill_no'])): ?>
                        	<option value="<?php echo $data['search']['sm_bill_no']['value']; ?>" selected>
                            	<?php echo $data['search']['sm_bill_no']['text']; ?> 
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
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['sm_acc_id'])): ?><p>CUSTOMER</p><?php endif; ?>
					<select class="form-control floating-select" id="sm_acc_id" name="sm_acc_id">
                    	<?php if(isset($data['search']['sm_acc_id']) && !empty($data['search']['sm_acc_id'])): ?>
                        	<option value="<?php echo $data['search']['sm_acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['sm_acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['_design_name'])): ?><p>DESIGN</p><?php endif; ?>
					<select class="form-control floating-select" id="_design_name" name="_design_name">
                    	<?php if(isset($data['search']['_design_name']) && !empty($data['search']['_design_name'])): ?>
                        	<option value="<?php echo $data['search']['_design_name']['value']; ?>" selected>
                            	<?php echo $data['search']['_design_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['_style_name'])): ?><p>STYLE</p><?php endif; ?>
					<select class="form-control floating-select" id="_style_name" name="_style_name">
                    	<?php if(isset($data['search']['_style_name']) && !empty($data['search']['_style_name'])): ?>
                        	<option value="<?php echo $data['search']['_style_name']['value']; ?>" selected>
                            	<?php echo $data['search']['_style_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				

			</div>
		</div>  
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead> 
						<tr>
			                <th width="3%">#</th>
	                        <th width="5%">BILL NO</th>
	                        <th width="5%">BILL DATE</th>
	                        <th width="15%">CUSTOMER</th>
	                        <th width="8%">DESIGN</th>
	                        <th width="8%">STYLE</th>
	                        <th width="8%">BRAND</th>
	                        <th width="7%">QTY</th>
	                        <th width="7%">RATE</th>
	                        <th width="7%">SUB AMT</th>
	                        <th width="5%">DISC AMT</th>
	                        <th width="8%">TAXABLE AMT</th>
	                        <th width="7%">SGST&nbsp;AMT</th>
	                        <th width="7%">CGST&nbsp;AMT</th>
	                        <th width="7%">IGST&nbsp;AMT</th>
	                        <th width="8%">TOTAL&nbsp;AMT</th>
			            </tr>
						<tr style="font-size: 15px; font-weight: bold;">
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td><?php echo round($data['totals']['total_qty'], 0); ?></td>
	                        <td></td>
	                        <td><?php echo round($data['totals']['sub_amt'], 2); ?></td>
	                        <td><?php echo round($data['totals']['disc_amt'], 2); ?></td>
	                        <td><?php echo round($data['totals']['taxable_amt'], 2); ?></td>
	                        <td><?php echo round($data['totals']['sgst_amt'], 2); ?></td>
	                        <td><?php echo round($data['totals']['cgst_amt'], 2); ?></td>
	                        <td><?php echo round($data['totals']['igst_amt'], 2); ?></td>
	                        <td><?php echo round($data['totals']['total_amt'], 2); ?></td>
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
									<td width="5%"><?php echo $value['sm_bill_no']; ?>
										<a class="mr-5" target="_blank" href="<?php echo base_url('sales?action=edit&id='.$value['sm_id']) ?>" data-toggle="tooltip" data-placement="bottom" title="SALE">
											<i class="fa fa-eye"></i>
										</a>	
									</td>
									<td width="5%"><?php echo date('d-m-Y', strtotime($value['sm_bill_date'])); ?></td>
									<td width="15%"><?php echo strtoupper($value['account_name']); ?></td>
									<td width="8%"><?php echo $value['design_name']; ?></td>
									<td width="8%"><?php echo $value['style_name']; ?></td>
									<td width="8%"><?php echo $value['brand_name']; ?></td>
									<td width="7%"><?php echo $value['st_qty']; ?></td>
									<td width="7%"><?php echo round($value['st_rate'], 2); ?></td>
									<td width="7%"><?php echo round($value['st_sub_total'], 2); ?></td>
									<td width="5%"><?php echo round($value['st_disc_amt'], 2); ?></td>
									<td width="8%"><?php echo round($value['st_taxable_amt'], 2); ?></td>
									<td width="7%"><?php echo round($value['st_sgst_amt'], 2); ?><br/><span style="font-size: 12px"><?php echo $value['st_sgst_per']?>%</span></td>
									<td width="7%"><?php echo round($value['st_cgst_amt'], 2); ?><br/><span style="font-size: 12px"><?php echo $value['st_cgst_per']?>%</span></td>
									<td width="7%"><?php echo round($value['st_igst_amt'], 2); ?><br/><span style="font-size: 12px"><?php echo $value['st_igst_per']?>%</span></td>
									<td width="8%"><?php echo round($value['st_sub_total_amt'], 2); ?></td>
								</tr>
					<?php 
							endforeach;
					?>
								
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
	<script src="<?php echo assets('dist/js/report/sales_summary_itemwise.js')?>"></script>
	</body>
</html>