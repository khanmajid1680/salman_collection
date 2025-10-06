<?php 
	$this->load->view('templates/header'); 
	$action = (isset($_GET['action'])) ? $_GET['action'] : "";
?>
<script>
    let link 	= "report";
    let sub_link= "profit_loss";
</script>
<section class="d-flex justify-content-between sticky_top neu_flat_primary breadcrumb_pagination">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item active" aria-current="page">
	    	PROFIT & LOSS(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
	    </li>
	    <li class="breadcrumb-item" aria-current="reload-page">
	    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/profit_loss?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
	    </li>
	  </ol>
	</nav>
	<div class="d-none d-sm-block">
		<?= $this->pagination->create_links(); ?>
	</div>
</section>
<section class="container-fluid">
	<div class="row sticky_table_header">
		<div class="col-12">
			<table class="table table-sm table-dark">
				<thead>
					<tr>
		                <th width="25%">TOTAL INCOME HEAD</th>
                        <th width="25%">TOTAL EXPENSE HEAD</th>
                        <th width="25%">PROFIT</th>
                        <th width="25%">LOSS</th>
		            </tr>
				</thead>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="box">
				<table class="table table-sm table-hover">
					<tbody>
						<tr>
							<td width="25%"><?php echo number_format($data['total_income'][0]['total_income'], 2); ?></td>
							<td width="25%"><?php echo number_format($data['total_expense'][0]['total_expense'], 2); ?></td>
							<td width="25%"><?php echo $data['total_income'][0]['total_income'] - $data['total_expense'][0]['total_expense'] > 0 ? $data['total_income'][0]['total_income'] - $data['total_expense'][0]['total_expense'] : 0; ?></td>
							<td width="25%"><?php echo $data['total_income'][0]['total_income'] - $data['total_expense'][0]['total_expense'] < 0 ? $data['total_expense'][0]['total_expense'] - $data['total_income'][0]['total_income'] : 0; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="d-flex">
		<div class="col-6">
			<div class="row sticky_table_header">
				<div class="col-12">
					<table class="table table-sm table-dark">
						<thead>
							<tr>
				                <th width="10%">#</th>
		                        <th width="20%">EXPENSE HEAD</th>
		                        <th width="20%">AMT</th>
				            </tr>
						</thead>
					</table>
				</div>
			</div>
			<div class="row">
			<div class="col-12">
				<div class="box">
					<table class="table table-sm table-hover">
						<tbody>
							<?php 
								if(!empty($data['expense'])):
									$sr_no = 1;
									foreach ($data['expense'] as $key => $value):
							?>
										<tr>
											<td width="10%"><?php echo $sr_no; ?></td>
											<td width="20%"><?php echo strtoupper($value['account_name']); ?></td>
											<td width="20%"><?php echo number_format($value['expense'], 2); ?></td>
										</tr>
							<?php 
										$sr_no++;
									endforeach; 
									else: 
							?>
								<tr>
									<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			</div>	
		</div>
		<div class="col-6">
			<div class="row sticky_table_header">
				<div class="col-12">
					<table class="table table-sm table-dark">
						<thead>
							<tr>
				                <th width="10%">#</th>
		                        <th width="20%">INCOME HEAD</th>
		                        <th width="20%">AMT</th>
				            </tr>
						</thead>
					</table>
				</div>
			</div>
			<div class="row">
			<div class="col-12">
				<div class="box">
					<table class="table table-sm table-hover">
						<tbody>
							<?php 
								if(!empty($data['income'])):
									$sr_no = 1;
									foreach ($data['income'] as $key => $value):
							?>
										<tr>
											<td width="10%"><?php echo $sr_no; ?></td>
											<td width="20%"><?php echo strtoupper($value['account_name']); ?></td>
											<td width="20%"><?php echo number_format($value['income'], 2); ?></td>
										</tr>
							<?php 
										$sr_no++;
									endforeach; 
									else: 
							?>
								<tr>
									<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			</div>	
		</div>
	</div>
	
</section>
<?= $this->pagination->create_links(); ?>
<?php $this->load->view('templates/footer'); ?>
	</body>
</html>