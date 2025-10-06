<?php 
    $this->load->view('templates/header'); 
    $trial_mobile_length = empty($master_data) ? (10 - strlen($trial_mobile)) : (10 - strlen($master_data[0]['mm_trial_mobile']));
?>
<script>
    let link = "message";
    let sub_link = "message";
</script>
<section class="d-flex justify-content-between sticky_top neu_flat_primary breadcrumb_pagination">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('message?action=view'); ?>">SMS</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary master_block_btn" href="<?php echo base_url('message?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="5"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
	<form class="form-horizontal" id="message_form">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">MESSAGE DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="mm_entry_no" name="mm_entry_no" value="<?php echo empty($master_data) ? $mm_entry_no : $master_data[0]['mm_entry_no'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY NO</label>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="text" class="form-control floating-input" id="mm_entry_date" name="mm_entry_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['mm_entry_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY DATE</label>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-6 floating-label">
                                <p for="inputEmail3"> SENDER TYPE</p>
                                <?php echo form_dropdown('', $groups, empty($master_data) ? '' : $master_data[0]['mm_account_type'],'class="form-control floating-select" id="mm_account_type" name="mm_account_type" tabindex="1"');?>
                            </div>
                            <div class="col-12 mt-2 floating-label">
                                <textarea class="form-control floating-textarea" id="mm_description" name="mm_description" placeholder=" " tabindex="3" autocomplete="off" rows="3" onkeyup="validate_textfield(this)"><?php echo empty($master_data) ? '' : $master_data[0]['mm_description']; ?></textarea>
                                <label for="inputEmail3">MESSAGE&nbsp;<span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="mm_description_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <input type="number" class="form-control floating-input" id="mm_trial_mobile" name="mm_trial_mobile" value="<?php echo empty($master_data) ? $trial_mobile : $master_data[0]['mm_trial_mobile'] ?>" placeholder=" "  onkeyup="set_mobile_no(this)" onfocusout="validate_mobile_no(this)" autocomplete="off"/>   
                                <label for="inputEmail3">TRIAL MOBILE NO.&nbsp;
                                    <span id="mm_trial_mobile_length">(<?php echo $trial_mobile_length ?>)</span>
                                </label>
                                <small class="form-text text-muted helper-text" id="mm_trial_mobile_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 floating-label">
                                <button type="button" class="btn btn-primary" onclick="send_trial_message()" id="trial_message_btn">
                                     <span id="trial_message_btn_text">
                                        <i>SEND TRIAL MESSAGE</i>
                                    </span>
                                        <i class="text-dark fa fa-send"></i>
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 floating-label">
                                <input type="number" class="form-control floating-input" id="mm_total_qty" name="mm_total_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['mm_total_qty'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL SENDER</label>
                                <!-- <small class="form-text text-muted helper-text" id="mm_total_qty_msg"></small> -->
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 floating-label">
                                <input type="number" class="form-control floating-input" id="mm_total_sent_qty" name="mm_total_sent_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['mm_total_sent_qty'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SENT COUNT</label>
                                <!-- <small class="form-text text-muted helper-text" id="mm_total_sent_qty_msg"></small> -->
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 floating-label">
                                <input type="number" class="form-control floating-input" id="mm_total_failed_qty" name="mm_total_failed_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['mm_total_failed_qty'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">FAILED COUNT</label>
                                <!-- <small class="form-text text-muted helper-text" id="mm_total_failed_qty_msg"></small> -->
                            </div>
                            <div class="col-6 floating-label">
                            </div>
                            <div class="col-6 floating-label">
                                <button type="button" class="btn btn-primary" onclick="add_update(<?php echo empty($master_data) ? 0 : $master_data[0]['mm_id']; ?>)" id="bulk_message_btn" <?php echo empty($master_data) ? 'disabled' : ''; ?>>
                                     <span id="bulk_message_btn_text">
                                        <i>SEND BULK MESSAGE</i>
                                    </span>
                                        <i class="text-dark fa fa-send"></i>
                                </button>
                            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">
                        SENDER DETAIL
                        &nbsp;
                        <span><a style="cursor: pointer;" onclick="sender_popup()" data-toggle="tooltip" data-placement="top" title="ADD SENDER"><i class="text-success fa fa-plus"></i></a></span>
                    </div>
					<div class="card-body">
						<table class="table table-hover table-sm table-responsive">
                            <thead>
                                <tr style="font-size: 15px;">
                                    <th width="2%">
                                        <input type="checkbox" id="parent_checkbox" checked="checked" onchange="select_deselect()">
                                    </th>
                                    <th width="40%">NAME</th>
                                    <th width="40%">MOBILE NO.</th>
                                    <th width="18%">STATUS</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-hover table-sm table-responsive" style="height: 51vh;">
                            <tbody id="message_wrapper">
                                <?php 
                                    if(!empty($trans_data)): 
                                        $message_cnt = 1;
                                        foreach ($trans_data as $key => $value):
                                ?>
                                            <tr id="rowid_<?php echo $message_cnt; ?>">
                                                <td width="2%">
                                                    <?php if($value['mt_status'] == 0): ?>
                                                        <input type="checkbox" class="checkboxes" id="child_checkbox_<?php echo $message_cnt; ?>" checked onchange="select_deselect(<?php echo $message_cnt; ?>)"/>
                                                        <input type="hidden" id="send_sms_<?php echo $message_cnt; ?>" name="send_sms[]" value="YES"/>

                                                    <?php else: ?>
                                                        <input type="hidden" name="send_sms[]" value="YES"/>
                                                    <?php endif; ?>
                                                    <input type="hidden" id="mt_id_<?php echo $message_cnt; ?>" name="mt_id[]" value="<?php echo $value['mt_id'] ?>"/>
                                                </td>
                                                <td width="40%">
                                                    <?php echo strtoupper($value['mt_account_name']); ?>
                                                    <input type="hidden" id="mt_account_name_<?php echo $message_cnt; ?>" name="mt_account_name[]" value="<?php echo $value['mt_account_name'] ?>" />
                                                </td>
                                                <td width="40%">
                                                    <?php echo $value['mt_account_mobile'] ?>
                                                    <input type="hidden" id="mt_account_mobile_<?php echo $message_cnt; ?>" name="mt_account_mobile[]" value="<?php echo $value['mt_account_mobile'] ?>" />
                                                </td>
                                                <td width="18%">
                                                    <span class="font-weight-bold <?php echo $value['mt_status'] == 1 ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo $value['mt_status'] == 1 ? 'SENT' : 'FAILED'; ?>
                                                    </span>
                                                    <input type="hidden" id="mt_status_<?php echo $message_cnt; ?>" name="mt_status[]" value="<?php echo $value['mt_status'] ?>" />
                                                </td>
                                            </tr>
                                <?php 
                                        $message_cnt++;
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
<script src="<?php echo assets('dist/js/utility/message.js')?>"></script>
<?php 
    if(!empty($master_data))
    {
        echo "<script>";
        echo "message_cnt = $message_cnt;";
        echo "</script>";
    }
?>
</body>
</html>