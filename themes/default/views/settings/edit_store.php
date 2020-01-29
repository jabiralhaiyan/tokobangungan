<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('update_info'); ?></h3>
                </div>
                <div class="box-body">
                    <?php echo form_open_multipart("settings/edit_store/".$store->id);?>
                    <?php if ( ! empty($store->logo)) { ?>
                    <img src="<?= base_url('uploads/'.$store->logo); ?>" class="img-thumbnail pull-right" style="margin-bottom: 10px;">
                    <?php } ?>
                    <div class="col-md-6">
                        <div class="form-group" style="display:block;">
                            <label class="control-label" for="name"><?= $this->lang->line("name"); ?></label>
                            <?= form_input('name', set_value('name', $store->name), 'class="form-control input-sm" id="name"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="code"><?= $this->lang->line("code"); ?></label>
                            <?= form_input('code', set_value('code', $store->code), 'class="form-control input-sm" id="code"'); ?>
                        </div>

                        <div class="form-group">
                            <?= lang('logo', 'logo'); ?>
                            <input type="file" name="userfile" id="logo">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
                            <?= form_input('email', set_value('email', $store->email), 'class="form-control input-sm" id="email_address"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
                            <?= form_input('phone', set_value('phone', $store->phone), 'class="form-control input-sm" id="phone"');?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="address1"><?= $this->lang->line("address1"); ?></label>
                            <?= form_input('address1', set_value('address1', $store->address1), 'class="form-control input-sm" id="address1"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="address2"><?= $this->lang->line("address2"); ?></label>
                            <?= form_input('address2', set_value('address2', $store->address2), 'class="form-control input-sm" id="address2"');?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="city"><?= $this->lang->line("city"); ?></label>
                            <?= form_input('city', set_value('city', $store->city), 'class="form-control input-sm" id="city"');?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="state"><?= $this->lang->line("state"); ?></label>
                            <?= form_input('state', set_value('state', $store->state), 'class="form-control input-sm" id="state"');?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="postal_code"><?= $this->lang->line("postal_code"); ?></label>
                            <?= form_input('postal_code', set_value('postal_code', $store->postal_code), 'class="form-control input-sm" id="postal_code"');?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="country"><?= $this->lang->line("country"); ?></label>
                            <?= form_input('country', set_value('country', $store->country), 'class="form-control input-sm" id="country"');?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="receipt_header"><?= $this->lang->line("receipt_header"); ?></label>
                            <?= form_textarea('receipt_header', set_value('receipt_header', $store->receipt_header), 'class="form-control tip" id="receipt_header" style="height:100px;"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="receipt_footer"><?= $this->lang->line("receipt_footer"); ?></label>
                            <?= form_textarea('receipt_footer', set_value('receipt_footer', $store->receipt_footer), 'class="form-control tip" id="receipt_footer" style="height:100px;"'); ?>
                        </div>

                        <div class="form-group">
                            <?php echo form_submit('edit_store', $this->lang->line("edit_store"), 'class="btn btn-primary"');?>
                        </div>
                    </div>
                    <?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</section>
