<?php 
    $this->load->view('templates/header');
?>
<script>
    let link = "invoice";
    let sub_link = "invoice";
</script>
<section class="d-flex justify-content-between sticky_top">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('invoice?action=view'); ?>">CA REPORT</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_invoice(<?php echo empty($master_data) ? 0 : $master_data[0]['im_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="10" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save"></i></button>
        </li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo base_url('invoice?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="11"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
	<form class="form-horizontal" id="invoice_form">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">SEARCH SALES DATA</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input type="text" class="form-control floating-input datepicker" id="im_from_bill_date" name="im_from_bill_date" value="<?php echo empty($master_data) ? '' : date('d-m-Y', strtotime($master_data[0]['im_from_bill_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SALES BILL DATE (FROM)&nbsp;<span class="text-danger">*</span></label>
                                <!-- <small class="form-text text-muted helper-text" id="im_from_bill_date_msg"></small> -->
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input type="text" class="form-control floating-input datepicker" id="im_to_bill_date" name="im_to_bill_date" value="<?php echo empty($master_data) ? '' : date('d-m-Y', strtotime($master_data[0]['im_to_bill_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SALES BILL DATE (TO)&nbsp;<span class="text-danger">*</span></label>
                                <!-- <small class="form-text text-muted helper-text" id="im_to_bill_date_msg"></small> -->
                            </div>
							<div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <textarea class="form-control floating-input" id="im_notes" name="im_notes" placeholder=" " autocomplete="off" tabindex="5" rows="3"><?php echo (empty($master_data)?'':$master_data[0]['im_notes'])?></textarea>
                                <label for="inputEmail3">NOTES</label>
                            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">INVOICE DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="im_sm_bill_count" name="im_sm_bill_count" value="<?php echo empty($master_data) ? 0 : $master_data[0]['im_sm_bill_count'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL SALES BILL</label>
                                <!-- <small class="form-text text-muted helper-text" id="im_sm_bill_count_msg"></small> -->
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="im_sm_bill_amt" name="im_sm_bill_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['im_sm_bill_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL SALE AMT.</label>
                                <!-- <small class="form-text text-muted helper-text" id="im_sm_bill_amt_msg"></small> -->
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="im_total_qty" name="im_total_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['im_total_qty'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL INVOICE</label>
                                <!-- <small class="form-text text-muted helper-text" id="im_total_qty_msg"></small> -->
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="im_final_amt" name="im_final_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['im_final_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL INVOICE AMT.</label>
                                <!-- <small class="form-text text-muted helper-text" id="im_final_amt_msg"></small> -->
                            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="card mb-3">
					<div class="card-header">SALES DETAIL</div>
					<div class="card-body mt-2">
						<table class="table table-hover table-sm table-responsive">
                            <thead>
    							<tr style="font-size: 15px;">
                                    <th width="3%">
                                        <input type="checkbox" id="parent_checkbox" onchange="select_deselect()" <?php echo empty($trans_data) ? '' : 'checked'; ?>>
                                    </th>
                                    <th width="7%">BILL NO</th>
                                    <th width="7%">BILL DATE</th>
                                    <th width="10%">CUSTOMER</th>
                                    <th width="10%">SALES PERSON</th>
                                    <th width="10%">PAYMENT MODE</th>
                                    <th width="7%">TOTAL QTY</th>
                                    <th width="7%">SUB AMT</th>
                                    <th width="7%">DISC AMT</th>
                                    <th width="8%">PROMO DISC</th>
                                    <th width="10%">LOYALTY POINTS</th>
                                    <th width="8%">ROUND OFF</th>
                                    <th width="8%">BILL AMT</th>
    	                        </tr>
                            </thead>
                        </table>
                        <table class="table table-hover table-sm table-responsive" style="height: 51vh;">
                            <tbody id="sales_material_wrapper">
                                <?php if(!empty($trans_data)): ?>
                                    <?php 
                                        foreach ($trans_data as $key => $value):
                                            $disabled   = $value['it_payment_mode'] == 'CASH' ? '' : 'disabled';
                                    ?>
                                        <tr id="rowid_<?php echo $value['it_sm_id'] ?>">
                                            <td width="3%">
                                                <input type="checkbox" class="checkboxes" id="child_checkbox_<?php echo $value['it_sm_id'] ?>" onchange="calculate_master_total()" checked <?php echo $disabled ?> />
                                                <input type="hidden" id="it_id_<?php echo $value['it_sm_id'] ?>" name="it_id[]" value="<?php echo $value['it_id'] ?>"/>
                                                <input type="hidden" id="it_generate_<?php echo $value['it_sm_id'] ?>" name="it_generate[]" value="1"/>
                                            </td>
                                            <td width="7%">
                                                <?php echo $value['it_bill_no'] ?>
                                                <input type="hidden" id="it_sm_id_<?php echo $value['it_sm_id'] ?>" name="it_sm_id[]" value="<?php echo $value['it_sm_id'] ?>" />
                                                <input type="hidden" id="it_bill_no_<?php echo $value['it_sm_id'] ?>" name="it_bill_no[]" value="<?php echo $value['it_bill_no'] ?>" />
                                            </td>
                                            <td width="7%">
                                                <?php echo $value['it_bill_date'] ?>
                                                <input type="hidden" id="it_bill_date_<?php echo $value['it_sm_id'] ?>" name="it_bill_date[]" value="<?php echo date('d-m-Y', strtotime($value['it_bill_date'])) ?>" />
                                            </td>
                                            <td width="10%">
                                                <?php echo $value['account_name'] ?>
                                                <input type="hidden" id="it_acc_id_<?php echo $value['it_sm_id'] ?>" name="it_acc_id[]" value="<?php echo $value['it_acc_id'] ?>" />
                                            </td>
                                            <td width="10%">
                                                <?php echo $value['user_fullname'] ?>
                                                <input type="hidden" id="it_user_id_<?php echo $value['it_sm_id'] ?>" name="it_user_id[]" value="<?php echo $value['it_user_id'] ?>" />
                                            </td>
                                            <td width="10%">
                                                <?php echo $value['it_payment_mode'] ?>
                                                <input type="hidden" id="it_payment_mode_<?php echo $value['it_sm_id'] ?>" name="it_payment_mode[]" value="<?php echo $value['it_payment_mode'] ?>" />
                                            </td>
                                            <td width="7%">
                                                <?php echo $value['it_total_qty'] ?>
                                                <input type="hidden" id="it_total_qty_<?php echo $value['it_sm_id'] ?>" name="it_total_qty[]" value="<?php echo $value['it_total_qty'] ?>" />
                                            </td>
                                            <td width="7%">
                                                <?php echo $value['it_sub_amt'] ?>
                                                <input type="hidden" id="it_sub_amt_<?php echo $value['it_sm_id'] ?>" name="it_sub_amt[]" value="<?php echo $value['it_sub_amt'] ?>" />
                                            </td>
                                            <td width="7%">
                                                <?php echo $value['it_disc_amt'] ?>
                                                <input type="hidden" id="it_disc_amt_<?php echo $value['it_sm_id'] ?>" name="it_disc_amt[]" value="<?php echo $value['it_disc_amt'] ?>" />
                                            </td>
                                            <td width="8%">
                                                <?php echo $value['it_promo_amt'] ?>
                                                <input type="hidden" id="it_promo_amt_<?php echo $value['it_sm_id'] ?>" name="it_promo_amt[]" value="<?php echo $value['it_promo_amt'] ?>" />
                                            </td>
                                            <td width="10%">
                                                <?php echo $value['it_point_amt'] ?>
                                                <input type="hidden" id="it_point_amt_<?php echo $value['it_sm_id'] ?>" name="it_point_amt[]" value="<?php echo $value['it_point_amt'] ?>" />
                                            </td>
                                            <td width="8%">
                                                <?php echo $value['it_round_off'] ?>
                                                <input type="hidden" id="it_round_off_<?php echo $value['it_sm_id'] ?>" name="it_round_off[]" value="<?php echo $value['it_round_off'] ?>" />
                                            </td>
                                            <td width="8%">
                                                <?php echo $value['it_final_amt'] ?>
                                                <input type="hidden" id="it_final_amt_<?php echo $value['it_sm_id'] ?>" name="it_final_amt[]" value="<?php echo $value['it_final_amt'] ?>" />
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                <?php endif; ?>
                            </tbody>
                        </table>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/invoice/invoice.js')?>"></script>
</body>
</html>