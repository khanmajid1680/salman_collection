<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_bill_date = (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
	$to_bill_date 	= (isset($_GET['to_bill_date'])) ? $_GET['to_bill_date'] : "";
	
	$url 			= $_SERVER['QUERY_STRING'];
?>
<script>
    let link 	= "report";
    let sub_link= "ca_report";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('report/ca_report?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	CA SUMMARY(<span><i><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/ca_report?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="print-page">
			    	<?php if(!empty($data['data'])): ?>
			    		<a target="_blank" type="button" class="btn btn-sm btn-primary mr-2" data-toggle="tooltip" data-placement="bottom" title="PDF" href="<?php echo base_url("report/ca_report?submit=PDF&$url"); ?>">
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
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
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
				<div style="font-family: Arial, sans-serif; line-height: 1.5;">
				    <strong>M/S</strong>: Local sales (within the same state) &nbsp;|&nbsp; 
				    <strong>OMS</strong>: Out-of-state sales (interstate)
				</div>
			</div>
		</div>
		
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-reponsive table-hover">
				<thead class="table-dark">
					<tr>
		                <th width="3%">#</th>
                        <th width="7%">BILL DATE</th>
                        <th width="7%">BILL NO</th>
                        <th width="7%">M/S NET 5%</th>
                        <th width="7%">CGST 2.5%</th>
                        <th width="7%">SGST 2.5%</th>
                        <th width="7%">CASH</th>
                        <th width="7%">M/S NET 18%</th>
                        <th width="7%">CGST 9%</th>
                        <th width="7%">SGST 9%</th>
                        <th width="7%">OMS NET 5%</th>
                        <th width="7%">IGST 5%</th>
                        <th width="7%">OMS NET 18%</th>
                        <th width="7%">IGST 18%</th>
                        <th width="7%">TOTAL</th>
		            </tr>
				</thead>
				<tbody>
					<tr style="font-size: 15px; font-weight: bold;">
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo $data['totals']['ms_net_5']; ?></td>
                        <td><?php echo $data['totals']['cgst_25']; ?></td>
                        <td><?php echo $data['totals']['sgst_25']; ?></td>
                        <td><?php echo $data['totals']['cash_amt']; ?></td>
                        <td><?php echo $data['totals']['ms_net_18']; ?></td>
                        <td><?php echo $data['totals']['cgst_9']; ?></td>
                        <td><?php echo $data['totals']['sgst_9']; ?></td>
                        <td><?php echo $data['totals']['oms_net_5']; ?></td>
                        <td><?php echo $data['totals']['igst_5']; ?></td>
                        <td><?php echo $data['totals']['oms_net_18']; ?></td>
                        <td><?php echo $data['totals']['igst_18']; ?></td>
                        <td><?php echo $data['totals']['total_amt']; ?></td>
					</tr>
				</tbody>
				<tbody>
					<?php 
						if(!empty($data['data'])):
							foreach ($data['data'] as $key => $value):
					?>
								<tr>
									<td><?php echo $key+1; ?></td>
									<td><?php echo $value['entry_date']; ?></td>
									<td><?php echo $value['sm_bill_no']; ?></td>
									<td><?php echo $value['ms_net_5']; ?></td>
			                        <td><?php echo $value['cgst_25']; ?></td>
			                        <td><?php echo $value['sgst_25']; ?></td>
			                        <td><?php echo $value['cash_amt']; ?></td>
			                        <td><?php echo $value['ms_net_18']; ?></td>
			                        <td><?php echo $value['cgst_9']; ?></td>
			                        <td><?php echo $value['sgst_9']; ?></td>
			                        <td><?php echo $value['oms_net_5']; ?></td>
			                        <td><?php echo $value['igst_5']; ?></td>
			                        <td><?php echo $value['oms_net_18']; ?></td>
			                        <td><?php echo $value['igst_18']; ?></td>
			                        <td><?php echo $value['total_amt']; ?></td>
								</tr>
					<?php endforeach; ?>
							<tr style="font-size: 15px; font-weight: bold;">
								<td></td>
								<td></td>
								<td></td>
								<td><?php echo $data['totals']['ms_net_5']; ?></td>
		                        <td><?php echo $data['totals']['cgst_25']; ?></td>
		                        <td><?php echo $data['totals']['sgst_25']; ?></td>
		                        <td><?php echo $data['totals']['cash_amt']; ?></td>
		                        <td><?php echo $data['totals']['ms_net_18']; ?></td>
		                        <td><?php echo $data['totals']['cgst_9']; ?></td>
		                        <td><?php echo $data['totals']['sgst_9']; ?></td>
		                        <td><?php echo $data['totals']['oms_net_5']; ?></td>
		                        <td><?php echo $data['totals']['igst_5']; ?></td>
		                        <td><?php echo $data['totals']['oms_net_18']; ?></td>
		                        <td><?php echo $data['totals']['igst_18']; ?></td>
		                        <td><?php echo $data['totals']['total_amt']; ?></td>
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
		<script src="<?php echo assets('dist/js/report/ca_report.js?v=1')?>"></script>
	</body>
</html>