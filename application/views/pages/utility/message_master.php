<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
?>
<script>
    let link = "message";
    let sub_link = "message";
</script>
<section class="d-flex justify-content-between sticky_top neu_flat_primary breadcrumb_pagination">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item active" aria-current="page">
	    	SMS(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
	    </li>
        <li class="breadcrumb-item" aria-current="add-page">
    		<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('message?action=add')" data-toggle="tooltip" data-placement="bottom" title="ADD NEW"><i class="text-success fa fa-plus"></i></a >
    	</li>
	    <li class="breadcrumb-item" aria-current="reload-page">
	    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('message?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
			<table class="table table-sm table-reponsive table-dark">
				<thead>
					<tr>
						<th width="3%">#</th>
                        <th width="10%">ENTRY NO</th>
                        <th width="10%">ENTRY DATE</th>
                        <th width="40%">MESSAGE</th>
                        <th width="10%">TOTAL SENDER</th>
                        <th width="10%">TOTAL SENT SMS</th>
                        <th width="13%">TOTAL FAILED SMS</th>
                        <th width="4%" align="center">EDIT</th>
		            </tr>
				</thead>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-reponsive table-hover" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data)): 
							foreach ($data as $key => $value):
                    			$id = encrypt_decrypt("encrypt", $value['mm_id'], SECRET_KEY);
					?>

								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="10%"><?php echo $value['mm_entry_no']; ?></td>
									<td width="10%"><?php echo date('d-m-Y', strtotime($value['mm_entry_date'])); ?></td>
									<td width="40%">
										<span class="d-inline-block text-truncate" style="max-width: 550px;">
											<?php echo strtoupper($value['mm_description']); ?>
										</span>
									</td>
									<td width="10%"><?php echo $value['mm_total_qty']; ?></td>
									<td width="10%"><?php echo $value['mm_total_sent_qty']; ?></td>
									<td width="13%"><?php echo $value['mm_total_failed_qty']; ?></td>
									<td width="4%">
										<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('<?php echo 'message?action=edit&id='.$value['mm_id'] ?>')">
											<i class="text-success fa fa-edit"></i>
										</a>										
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
	</body>
</html>