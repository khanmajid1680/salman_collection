<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_entry_date= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
	$to_entry_date 	= (isset($_GET['to_entry_date'])) ? $_GET['to_entry_date'] : "";
	$from_bill_date = (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
	$to_bill_date 	= (isset($_GET['to_bill_date'])) ? $_GET['to_bill_date'] : "";
	$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
	$to_qty 		= (isset($_GET['to_qty'])) ? $_GET['to_qty'] : "";
	$from_bill_amt 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
	$to_bill_amt 	= (isset($_GET['to_bill_amt'])) ? $_GET['to_bill_amt'] : "";
?>
<script>
    let link = "purchase";
    let sub_link = "purchase";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('purchase?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	PURCHASE(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="add-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('purchase?action=add')" data-toggle="tooltip" data-placement="bottom" title="ADD NEW"><i class="text-success fa fa-plus"></i></a >
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('purchase?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
				<div class="col-6 col-sm-3 col-md-2 col-lg-2 floating-label">
					<?php if(isset($data['search']['entry_no'])): ?><p>ENTRY NO</p><?php endif; ?>
					<select class="form-control floating-select" id="entry_no" name="entry_no">
                    	<?php if(isset($data['search']['entry_no']) && !empty($data['search']['entry_no'])): ?>
                        	<option value="<?php echo $data['search']['entry_no']['value']; ?>" selected>
                            	<?php echo $data['search']['entry_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_entry_date" name="from_entry_date" value="<?php echo $from_entry_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ENTRY DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_entry_date" name="to_entry_date" value="<?php echo $to_entry_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ENTRY DATE</label>
					</div>
				</div>
				<div class="col-6 col-sm-3 col-md-2 col-lg-1 floating-label">
					<?php if(isset($data['search']['bill_no'])): ?><p>BILL NO</p><?php endif; ?>
					<select class="form-control floating-select" id="bill_no" name="bill_no">
                    	<?php if(isset($data['search']['bill_no']) && !empty($data['search']['bill_no'])): ?>
                        	<option value="<?php echo $data['search']['bill_no']['value']; ?>" selected>
                            	<?php echo $data['search']['bill_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_bill_date" name="from_bill_date" value="<?php echo $from_bill_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM BILL DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_bill_date" name="to_bill_date" value="<?php echo $to_bill_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO BILL DATE</label>
					</div>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-3 floating-label">
					<?php if(isset($data['search']['acc_id'])): ?><p>SUPPLIER</p><?php endif; ?>
					<select class="form-control floating-select" id="acc_id" name="acc_id">
                    	<?php if(isset($data['search']['acc_id']) && !empty($data['search']['acc_id'])): ?>
                        	<option value="<?php echo $data['search']['acc_id']['value']; ?>" selected>
                            	<?php echo $data['search']['acc_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-2">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_qty" name="from_qty" value="<?php echo $from_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM QTY</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_qty" name="to_qty" value="<?php echo $to_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO QTY</label>
					</div>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_bill_amt" name="from_bill_amt" value="<?php echo $from_bill_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM BILL AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_bill_amt" name="to_bill_amt" value="<?php echo $to_bill_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO BILL AMT</label>
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
	                        <th width="10%">ENTRY NO</th>
	                        <th width="12%">ENTRY DATE</th>
	                        <th width="10%">BILL NO</th>
	                        <th width="15%">BILL DATE</th>
	                        <th width="20%">SUPPLIER</th>
	                        <th width="10%">TOTAL QTY</th>
	                        <th width="10%">BILL AMT</th>
	                        <th width="5%" align="center">BARCODE</th> 
	                        <th width="3%" align="center">PRINT</th> 
	                        <th width="3%" align="center">EDIT</th> 
	                        <th width="4%" align="center">DELETE</th>
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
			<table class="table table-sm table-reponsive table-hover" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):
                    			$id = encrypt_decrypt("encrypt", $value['pm_id'], SECRET_KEY);
					?>

								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="10%"><?php echo $value['pm_entry_no']; ?></td>
									<td width="12%"><?php echo date('d-m-Y', strtotime($value['pm_entry_date'])); ?></td>
									<td width="10%"><?php echo $value['pm_bill_no']; ?></td>
									<td width="15%"><?php echo date('d-m-Y', strtotime($value['pm_bill_date'])); ?></td>
									<td width="20%"><?php echo strtoupper($value['account_name']); ?></td>
									<td width="10%"><?php echo $value['pm_total_qty']; ?></td>
									<td width="10%"><?php echo $value['pm_final_amt']; ?></td>
									<td width="5%">
										<a type="button" class="btn btn-sm btn-primary" target="_blank" href="<?php echo base_url('purchase?action=print&id='.$value['pm_id']) ?>">
											<i class="text-info fa fa-barcode"></i>
										</a>										
									</td>
									<td width="3%">
										<a type="button" class="btn btn-sm btn-primary" target="_blank" href="<?php echo base_url('purchase?action=bill&id='.$value['pm_id']) ?>">
											<i class="text-info fa fa-print"></i>
										</a>										
									</td>
									<td width="3%">
										<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('<?php echo 'purchase?action=edit&id='.$value['pm_id'] ?>')">
											<i class="text-success fa fa-edit"></i>
										</a>										
									</td>
									<td width="4%">
										<?php if($value['isExist']): ?>
											<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
										<?php else: ?>
											<a type="button" class="btn btn-sm btn-primary" onclick="remove_master('<?php echo 'purchase/remove/'.$value['pm_id']?>');">
												<i class="text-danger fa fa-trash"></i>
											</a>
										<?php endif; ?>                         
									</td>
								</tr>
					<?php 
							endforeach;
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
		<script src="<?php echo assets('dist/js/purchase/purchase.js?v=1')?>"></script>
	</body>
</html>