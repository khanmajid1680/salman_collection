<?php 
    $this->load->view('templates/header'); 
    $disabled = empty($master_data) ? '' : "disabled";
    $bank_area      = !empty($master_data) && $master_data[0]['vm_constant'] == 'BANK'  ? ""  : "d-none";
    $wrapper_area   = empty($trans_data) ? "d-none" : "";
    $sales_area     = empty($master_data) ? "" : "d-none";
    $pur_sale_area  = !empty($master_data) && $master_data[0]['vm_group'] == 'SUPPLIER' ? "PURCHASE" : "SALES";
    $return_area    = "d-none";
?>
<script>
    let link = "voucher";
    let sub_link = "receipt";
</script>
<section class="d-flex justify-content-between sticky_top neu_flat_primary breadcrumb_pagination">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('voucher/receipt?action=view'); ?>">RECEIPT</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_receipt(<?php echo empty($master_data) ? 0 : $master_data[0]['vm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="9" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save"></i></button>
        </li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo base_url('voucher/receipt?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="10"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
    <div class="d-none d-sm-block">
        <?= $this->pagination->create_links(); ?>
    </div>
</section>
<section class="container-fluid my-3">
    <form class="form-horizontal" id="receipt_form">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-header">RECEIPT DETAIL</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <input type="hidden" name="vm_id" id="vm_id" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_id']; ?>">
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="vm_entry_no" name="vm_entry_no" value="<?php echo empty($master_data) ? $vm_entry_no : $master_data[0]['vm_entry_no'] ?>" placeholder=" " readonly="readonly" />   
                                <input type="hidden" name="vm_type" value="RECEIPT">
                                <label for="inputEmail3">ENTRY NO</label>
                                <small class="form-text text-muted helper-text" id="vm_entry_no_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="text" class="form-control floating-input" id="vm_entry_date" name="vm_entry_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['vm_entry_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY DATE</label>
                                <small class="form-text text-muted helper-text" id="vm_entry_date_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-6 floating-label">
                                <p for="inputEmail3">GROUP&nbsp;<span class="text-danger">*</span></p>
                                <?php echo form_dropdown('', $groups, empty($master_data) ? 'CUSTOMER' : $master_data[0]['vm_group'],'class="form-control floating-select" id="vm_group" name="vm_group" onchange="set_party()"'.$disabled);?>
                                <?php if(!empty($master_data)): ?>
                                    <input type="hidden" name="vm_group" value="<?php echo $master_data[0]['vm_group']?>" />
                                <?php endif; ?>
                                <input type="hidden" id="vm_constant" name="vm_constant" value="<?php echo !empty($master_data) ? $master_data[0]['account_constant'] : '';?>" />
                                <small class="form-text text-muted helper-text" id="vm_group_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-6 floating-label">
                                <p for="inputEmail3">ACCOUNT&nbsp;<span class="text-danger">*</span></p>
                                <?php echo form_dropdown('', $accounts, empty($master_data) ? 0 : $master_data[0]['vm_acc_id'],'class="form-control floating-select" id="vm_acc_id" name="vm_acc_id" onchange="validate_dropdown(this)" tabindex="1"'.$disabled);?>
                                <?php if(!empty($master_data)): ?>
                                    <input type="hidden" name="vm_acc_id" value="<?php echo $master_data[0]['vm_acc_id']?>" />
                                <?php endif; ?>
                                <small class="form-text text-muted helper-text" id="vm_acc_id_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <p for="inputEmail3">PARTY NAME&nbsp;<span class="text-danger">*</span></p>
                                <select class="form-control floating-select" id="vm_party_id" name="vm_party_id" placeholder="" tabindex="3" onchange="validate_dropdown(this)" <?php echo $disabled; ?>>
                                    <?php if(!empty($master_data)): ?>
                                        <option value="<?php echo $master_data[0]['vm_party_id'] ?>">
                                            <?php echo $master_data[0]['party_name']; ?>
                                        </option>
                                        <input type="hidden" name="vm_party_id" value="<?php echo $master_data[0]['vm_party_id'] ?>" />
                                    <?php endif; ?>                                                
                                </select>
                                <small class="form-text text-muted helper-text" id="vm_party_id_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label bank_area <?php echo $bank_area; ?>">
                                <input type="text" class="form-control floating-input" id="vm_cheque_no" name="vm_cheque_no" value="<?php echo empty($master_data) ? '' : $master_data[0]['vm_cheque_no'] ?>" placeholder=" " autocomplete="off" tabindex="4"/>
                                <label for="inputEmail3">CHEQUE NO.</label>
                                <small class="form-text text-muted helper-text" id="vm_cheque_no_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label bank_area <?php echo $bank_area; ?>">
                                <input type="text" class="form-control floating-input future-datepicker" id="vm_cheque_date" name="vm_cheque_date" value="<?php echo !empty($master_data) && $master_data[0]['vm_cheque_date'] != '0000-00-00' ? date('d-m-Y', strtotime($master_data[0]['vm_cheque_date'])) : ''; ?>" placeholder=" " autocomplete="off" tabindex="5"/>
                                <label for="inputEmail3">CHEQUE DATE</label>
                                <small class="form-text text-muted helper-text" id="vm_cheque_date_msg"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <span>AMOUNT DETAIL</span>
                        <?php if((!empty($master_data) && $master_data[0]['vm_group'] == 'CUSTOMER') && empty($trans_data)): ?>
                            <button type="button" class="btn btn-xs btn-secondary" onclick="get_bill_for_adjustment()">SHOW BILL FOR ADJUSTMENT</button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-4 col-lg-4 d-flex floating-label">
                                <div class="floating-label" style="width: 100%;">
                                    <?php if(!empty($master_data) && !empty($trans_data)): ?>
                                        <input type="number" class="form-control floating-input" id="vm_total_amt" name="vm_total_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_total_amt'] ?>" placeholder=" " value="0" min="0" oninput="this.value = Math.abs(this.value)" readonly/>
                                    <?php else: ?>
                                        <input type="number" class="form-control floating-input" id="vm_total_amt" name="vm_total_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_total_amt'] ?>" placeholder=" " value="0" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_balance()" tabindex="6" readonly/>
                                    <?php endif; ?>
                                    <label for="inputEmail3">AMT</label>
                                    <small class="form-text text-muted helper-text" id="vm_total_amt_msg"></small>
                                </div>
                                <?php if(empty($master_data) && empty($trans_data)): ?>
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-md btn-primary"
                                            onclick="toggle_payment_mode_popup()"
                                            data-toggle="tooltip" 
                                            data-placement="bottom" 
                                            title="PAYMENT MODE" 
                                        ><i class="text-success fa fa-rupee"></i></button>
                                    </div>
                                 <?php endif; ?>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 floating-label">
                                <?php if(!empty($master_data) && !empty($trans_data)): ?>
                                    <input type="number" class="form-control floating-input" id="vm_round_off" name="vm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_round_off'] ?>" placeholder=" " value="0" min="0" oninput="this.value = Math.abs(this.value)" readonly/>
                                <?php else: ?>
                                    <input type="number" class="form-control floating-input" id="vm_round_off" name="vm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_round_off'] ?>" placeholder=" " value="0" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_balance()" tabindex="7"/>
                                <?php endif; ?>
                                
                                <label for="inputEmail3">AMT ROUND OFF</label>
                                <small class="form-text text-muted helper-text" id="vm_round_off_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 floating-label">
                                <?php if(!empty($master_data)): ?>
                                    
                                <?php else: ?>
                                    <input type="text" class="form-control floating-input" id="vm_balance_show" value="" placeholder=" " readonly="readonly" />   
                                    <label for="inputEmail3">BALANCE</label>
                                <?php endif; ?>
                                <input type="hidden" id="vm_balance" value="0">
                                <input type="hidden" id="vm_balance_type" value="">
                                <small class="form-text text-muted helper-text" id="vm_balance_show_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 floating-label">
                                <textarea class="form-control floating-textarea" id="vm_notes" name="vm_notes" placeholder=" " tabindex="8" autocomplete="off"><?php echo empty($master_data) ? '' : $master_data[0]['vm_notes']; ?></textarea>
                                <label for="inputEmail3">NOTES</label>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label sales_area <?php echo $sales_area; ?>">
                                <input type="number" class="form-control floating-input" id="vm_bill_amt" name="vm_bill_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_bill_amt'] ?>" placeholder=" " readonly="readonly" />
                                <label for="inputEmail3"><span class="pur_sale_area"><?php echo $pur_sale_area; ?></span> AMT</label>
                                <small class="form-text text-muted helper-text" id="vm_bill_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label return_area <?php echo $return_area; ?>">
                                <input type="number" class="form-control floating-input" id="vm_return_amt" name="vm_return_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_return_amt'] ?>" placeholder=" " readonly="readonly" />
                                <label for="inputEmail3"><span class="pur_sale_area"><?php echo $pur_sale_area; ?></span> RETURN AMT</label>
                                <small class="form-text text-muted helper-text" id="vm_return_amt_msg"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card mb-3 wrapper_area <?php echo $wrapper_area; ?>">
                    <div class="card-header">SALES DETAIL</div>
                    <div class="card-body mt-2">
                        <table class="table table-hover table-sm table-responsive" id="voucher_material_wrapper">
                            <?php if(!empty($master_data)): ?>
                                <?php if(!empty($master_data) && $master_data[0]['vm_constant'] == 'CREDIT_NOTE'): ?>
                                    <?php $this->view('pages/voucher/receipt_trans_credit'); ?>
                                <?php else:?>
                                    <?php $this->view('pages/voucher/receipt_trans'); ?>
                                <?php endif;?>
                            <?php endif;?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="payment_mode_wrapper"><?php $this->load->view('templates/panel/right'); ?></div>

    </form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/voucher/receipt.js?v=1')?>"></script>
<script type="text/javascript">get_payment_mode_data();</script>
</body>
</html>