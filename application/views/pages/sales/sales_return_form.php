<?php $this->load->view('templates/header'); ?>
<script>
    let link = "sales_return";
    let sub_link = "sales_return";
</script>
<section class="d-flex justify-content-between sticky_top">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('sales_return?action=view'); ?>">SALES RETURN</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_sales_return(<?php echo empty($master_data) ? 0 : $master_data[0]['srm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="5" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save"></i></button>
        </li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary master_block_btn" href="<?php echo base_url('sales_return?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="6"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
	<form class="form-horizontal" id="sales_return_form">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">BILL DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <p for="inputEmail3">BARCODE</p>
                                <select class="form-control floating-select" id="bm_id" placeholder="" tabindex="1">                                                
                                </select>
                                <input type="hidden" id="srm_id" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_id'] ?>">
                                <small class="form-text text-muted helper-text" id="bm_id_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <textarea class="form-control floating-textarea" id="srm_notes" name="srm_notes" placeholder=" " tabindex="2" autocomplete="off"><?php echo empty($master_data) ? '' : $master_data[0]['srm_notes']; ?></textarea>
                                <label for="inputEmail3">NOTES</label>
                            </div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="srm_entry_no" name="srm_entry_no" value="<?php echo empty($master_data) ? $srm_entry_no : $master_data[0]['srm_entry_no'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY NO</label>
                                <small class="form-text text-muted helper-text" id="srm_entry_no_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="text" class="form-control floating-input" id="srm_entry_date" name="srm_entry_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['srm_entry_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY DATE</label>
                                <small class="form-text text-muted helper-text" id="srm_entry_date_msg"></small>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6 floating-label">
								<input type="text" class="form-control floating-input" id="account_name" value="<?php echo empty($master_data) ? '' : $master_data[0]['account_name'] ?>" placeholder=" " readonly/>   
                                <input type="hidden" id="srm_acc_id" name="srm_acc_id" value="<?php echo empty($master_data) ? '' : $master_data[0]['srm_acc_id'] ?>"/>   
                                <label for="inputEmail3">CUSTOMER <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="srm_acc_id_msg"></small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">AMOUNT DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="srm_total_qty" name="srm_total_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_total_qty'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL QTY</label>
                                <small class="form-text text-muted helper-text" id="srm_total_qty_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="srm_sub_total" name="srm_sub_total" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_sub_total'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SUB TOTAL AMT.</label>
                                <small class="form-text text-muted helper-text" id="srm_sub_total_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_total_disc" name="srm_total_disc" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_total_disc'] ?>" placeholder=" " readonly />   
                                <label for="inputEmail3">TOTAL DISC AMT.</label>
                                <small class="form-text text-muted helper-text" id="srm_total_disc_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_taxable_amt" name="srm_taxable_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_taxable_amt'] ?>" placeholder=" " readonly />   
                                <label for="inputEmail3">TAXABLE AMT</label>
                                <small class="form-text text-muted helper-text" id="srm_taxable_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_sgst_amt" name="srm_sgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_sgst_amt'] ?>" placeholder=" " readonly />   
                                <label for="inputEmail3">SGST AMT</label>
                                <small class="form-text text-muted helper-text" id="srm_sgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_cgst_amt" name="srm_cgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_cgst_amt'] ?>" placeholder=" " readonly />   
                                <label for="inputEmail3">CGST AMT</label>
                                <small class="form-text text-muted helper-text" id="srm_cgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_igst_amt" name="srm_igst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_igst_amt'] ?>" placeholder=" " readonly />   
                                <label for="inputEmail3">IGST AMT</label>
                                <small class="form-text text-muted helper-text" id="srm_igst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_bill_disc" name="srm_bill_disc" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_bill_disc'] ?>" placeholder=" " autocomplete="off" tabindex="3" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_total()" />   
                                <label for="inputEmail3">BILL DISC AMT.</label>
                                <small class="form-text text-muted helper-text" id="srm_bill_disc_msg"></small>
                            </div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="srm_round_off" name="srm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_round_off'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ROUND OFF AMT.</label>
                                <small class="form-text text-muted helper-text" id="srm_round_off_msg"></small>
							</div>
							<div class="col-sm-12 col-md-6 floating-label">
								<input type="number" class="form-control floating-input" id="srm_final_amt" name="srm_final_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_final_amt'] ?>" placeholder=" " readonly="readonly" style="font-size: 20px; font-weight: bold;" />   
                                <label for="inputEmail3">BILL AMT.</label>
                                <small class="form-text text-muted helper-text" id="srm_final_amt_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="srm_amt_paid" name="srm_amt_paid" value="<?php echo empty($master_data) ? 0 : $master_data[0]['srm_amt_paid'] ?>" placeholder=" " autocomplete="off" tabindex="7" min="0" oninput="this.value = Math.abs(this.value)" />
                                <label >AMT TO PAY</label>
                                <small class="form-text text-muted helper-text" id="srm_amt_paid_msg"></small>
                            </div>  
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="card mb-3">
					<div class="card-header">PRODUCT DETAIL</div>
					<div class="card-body mt-2">
						<table class="table table-hover table-sm table-responsive">
                            <thead>
    							<tr style="font-size: 15px;">
                                    <th width="10%">SALES PERSON</th>
                                    <th width="10%">BARCODE</th>
                                    <th width="8%">BILL NO</th>
                                    <th width="8%">BILL DATE</th>
                                    <th width="10%">STYLE</th>
                                    <th width="10%">BRAND</th>
                                    <th width="6%">QUANTITY</th>
                                    <th width="6%">RATE</th>
                                    <th width="8%">SUB TOTAL</th>
                                    <th width="6%">DISC AMT</th>
                                    <th width="6%">TAXABLE AMT</th>
                                    <th width="6%">SGST%/AMT</th>
                                    <th width="6%">CGST%/AMT</th>
                                    <th width="6%">IGST%/AMT</th>
                                    <th width="8%">TOTAL</th>
                                    <th width="2%">ACTION</th>
    	                        </tr>
                            </thead>
                            <tbody id="sales_material_wrapper">
    	                        <?php
                                    $sales_cnt = 1;
                                    if(!empty($trans_data)):
                                        foreach ($trans_data as $key => $value):
                                            
                                ?>
                                        <tr id="rowid_<?php echo $sales_cnt; ?>" class="floating-form">
                                            <td class="floating-label">
                                                <input type="hidden" name="srt_id[]" id="srt_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_id'] ?>" />
                                                <input type="hidden" name="srt_user_id[]" id="srt_user_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_user_id'] ?>" />
                                                <input type="text" class="form-control floating-input" id="user_name_<?php echo $sales_cnt; ?>" value="<?php echo $value['user_name'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="hidden" name="srt_bm_id[]" id="srt_bm_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_bm_id'] ?>" />
                                                <input type="number" class="form-control floating-input" id="srt_bm_item_code_<?php echo $sales_cnt; ?>" value="<?php echo $value['bm_item_code'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_bill_no[]" id="srt_bill_no_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_bill_no'] ?>" readonly />
                                                <input type="hidden" name="srt_sm_id[]" id="srt_sm_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_sm_id'] ?>" />
                                                <input type="hidden" name="srt_st_id[]" id="srt_st_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_st_id'] ?>" />
                                                <input type="hidden" id="payment_mode_<?php echo $sales_cnt; ?>" value="<?php echo $value['sm_payment_mode'] ?>" />
                                            </td>
                                            <td class="floating-label">
                                                <input type="text" class="form-control floating-input" name="srt_bill_date[]" id="srt_bill_date_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_bill_date'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="text" class="form-control floating-input" id="style_name_<?php echo $sales_cnt; ?>" value="<?php echo $value['style_name'] ?>" readonly />
                                                <input type="hidden" name="srt_style_id[]" id="srt_style_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_style_id'] ?>" />
                                            </td>
                                            <td class="floating-label">
                                                <input type="text" class="form-control floating-input" id="brand_name_<?php echo $sales_cnt; ?>" value="<?php echo $value['brand_name'] ?>" readonly />
                                                <input type="hidden" name="srt_brand_id[]" id="srt_brand_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_brand_id'] ?>" />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_qty[]" id="srt_qty_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_qty'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_rate[]" id="srt_rate_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_rate'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_sub_total[]" id="srt_sub_total_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_sub_total'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_disc_amt[]" id="srt_disc_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_disc_amt'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_taxable_amt[]" id="srt_taxable_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_taxable_amt'] ?>" readonly />
                                            </td> 
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_sgst_per[]" id="srt_sgst_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_sgst_per'] ?>" readonly />
                                                <input type="number" class="form-control floating-input" name="srt_sgst_amt[]" id="srt_sgst_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_sgst_amt'] ?>" readonly />
                                            </td> 
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_cgst_per[]" id="srt_cgst_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_cgst_per'] ?>" readonly />
                                                <input type="number" class="form-control floating-input" name="srt_cgst_amt[]" id="srt_cgst_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_cgst_amt'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_igst_per[]" id="srt_igst_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_igst_per'] ?>" readonly />
                                                <input type="number" class="form-control floating-input" name="srt_igst_amt[]" id="srt_igst_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_igst_amt'] ?>" readonly />
                                            </td>  
                                            <td class="floating-label">
                                                <input type="number" class="form-control floating-input" name="srt_total_amt[]" id="srt_total_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['srt_total_amt'] ?>" readonly />
                                            </td>
                                            <td class="floating-label">
                                                <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_row(<?php echo $sales_cnt; ?>)"> 
                                                    <i class="text-danger fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                        $sales_cnt++;
                                        endforeach; 
                                    endif;
                                ?>
                            </tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/sales/sales_return.js?v1')?>"></script>
<?php 
    if(!empty($master_data))
    {
        echo "<script>";
        echo "sales_cnt = $sales_cnt;";
        echo "</script>";
    }
?>
</body>
</html>