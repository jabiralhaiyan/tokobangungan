<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('update_info'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?= form_open_multipart("settings", 'class="validation"'); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang("site_name", 'site_name'); ?>
                                    <?= form_input('site_name', $settings->site_name, 'class="form-control" id="site_name" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("tel", 'tel'); ?>
                                    <?= form_input('tel', $settings->tel, 'class="form-control" id="tel" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('language', 'language'); ?>
                                    <?php $available_langs = array(
                                        'arabic' => 'Arabic',
                                        'english' => 'English',
                                        'indonesian' => 'Indonesian'
                                    ); ?>
                                    <?= form_dropdown('language', $available_langs, $settings->language, 'class="form-control tip select2" id="language" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('theme', 'theme'); ?>
                                    <?php $th = array(
                                        'default' => 'Default'
                                    ); ?>
                                    <?= form_dropdown('theme', $th, $settings->theme, 'class="form-control tip select2" id="theme" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('theme_style', 'theme_style'); ?>
                                    <?php $ths = array(
                                        'black' => 'Black',
                                        'black-light' => 'Black Light',
                                        'blue' => 'Blue',
                                        'blue-light' => 'Blue Light',
                                        'green' => 'Green',
                                        'green-light' => 'Green Light',
                                        'purple' => 'Purple',
                                        'purple-light' => 'Purple Light',
                                        'red' => 'Red',
                                        'red-light' => 'Red Light',
                                        'yellow' => 'Yellow',
                                        'yellow-light' => 'Yellow Light',
                                    ); ?>
                                    <?= form_dropdown('theme_style', $ths, $settings->theme_style, 'class="form-control tip select2" id="theme_style" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("overselling", 'overselling'); ?>
                                    <?php $asp = array(0 => lang('disable'), 1 => lang('enable')); ?>
                                    <?= form_dropdown('overselling', $asp, $settings->overselling, 'class="form-control select2" id="overselling" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("multi_store", 'multi_store'); ?>
                                    <?php $asp = array(0 => lang('disable'), 1 => lang('enable')); ?>
                                    <?= form_dropdown('multi_store', $asp, $settings->multi_store, 'class="form-control select2" id="multi_store" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("currency_code", 'currency_code'); ?>
                                    <?= form_input('currency_prefix', $settings->currency_prefix, 'class="form-control" id="currency_code" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("auto_print", 'auto_print'); ?>
                                    <?= form_dropdown('auto_print', $asp, $settings->auto_print, 'class="form-control select2" id="auto_print" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("after_sale_page", 'after_sale_page'); ?>
                                    <?php $asp = array(0 => lang('receipt'), 1 => lang('pos')); ?>
                                    <?= form_dropdown('after_sale_page', $asp, $settings->after_sale_page, 'class="form-control select2" id="after_sale_page" required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("default_discount", 'default_discount'); ?>
                                    <?= form_input('default_discount', $settings->default_discount, 'class="form-control" id="default_discount" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("default_order_tax", 'default_tax_rate'); ?>
                                    <?= form_input('tax_rate', $settings->default_tax_rate, 'class="form-control" id="default_tax_rate" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('row_per_page', 'rows_per_page') ?>
                                    <?php
                                    $rw = array('10' => '10', '25' => '25', '50' => '50', '100' => '100');
                                    echo form_dropdown('rows_per_page', $rw, $settings->rows_per_page, 'class="form-control select2" id="rows_per_page" style="width:100%;" required="required"') ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('delete_code', 'pin_code'); ?>
                                    <?php echo form_password('pin_code', $settings->pin_code, 'class="form-control" pattern="[0-9]{4,8}"id="pin_code"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('rounding', 'rounding'); ?>
                                    <?php
                                    $rnd = array('0' => lang('disable'), '1' => lang('to_nearest_005'), '2' => lang('to_nearest_050'), '3' => lang('to_nearest_number'), '4' => lang('to_next_number'));
                                    echo form_dropdown('rounding', $rnd, $settings->rounding, 'class="form-control select2" id="rounding" required="required" style="width:100%;"');
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('display_product', 'display_product') ?>
                                    <?php
                                    $dprv = array('1' => 'Name', '2' => 'Photo', '3' => 'Both');
                                    echo form_dropdown('display_product', $dprv, $settings->bsty, 'class="form-control select2" id="display_product" style="width:100%;" required="required"') ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('pro_limit', 'pro_limit') ?>
                                    <?= form_input('pro_limit', $settings->pro_limit, 'class="form-control" id="pro_limit" required="required"') ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('display_kb', 'display_kb') ?>
                                    <?php
                                    $dtime = array('1' => lang('yes'), '0' => lang('no'));
                                    echo form_dropdown('display_kb', $dtime, $settings->display_kb, 'class="form-control select2" id="display_kb" style="width:100%;" required="required"') ?>
                                </div>
                                <div class="form-group">
                                    <?= lang("item_addition", "item_addition"); ?>
                                    <?php
                                    $ia = array(0 => lang('add_new_item'), 1 => lang('increase_quantity_if_item_exist'));
                                    echo form_dropdown('item_addition', $ia, $Settings->item_addition, 'id="item_addition" class="form-control tip select2" required="required" style="width:100%;"');
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('default_category', 'default_category') ?>
                                    <?php
                                    $ct[0] = lang('select').' '.lang('default_category');
                                    foreach ($categories as $catrgory) {
                                        $ct[$catrgory->id] = $catrgory->name;
                                    }
                                    echo form_dropdown('default_category', $ct, $settings->default_category, 'class="form-control select2" style="width:100%;" id="default_category"') ?>
                                </div>

                                <div class="form-group">
                                    <?= lang("default_customer", 'default_customer'); ?>
                                    <?php
                                    foreach ($customers as $customer) {
                                        $cu[$customer->id] = $customer->name;
                                    }
                                    echo form_dropdown('default_customer', $cu, $settings->default_customer, 'class="form-control select2" style="width:100%;" id="default_customer" required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('timezone', 'timezone'); ?> <!-- <a href="http://php.net/manual/en/timezones.php" target="_blank"><i class="fa fa-external-link"></i></a> -->
                                    <?= form_dropdown('timezone', $timezones, TIMEZONE, 'class="form-control select2" style="width:100%;" id="timezone" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('dateformat', 'dateformat'); ?> <a href="http://php.net/manual/en/function.date.php" target="_blank"><i class="fa fa-external-link"></i></a>
                                    <?= form_input('dateformat', $settings->dateformat, 'class="form-control tip" id="dateformat" required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('timeformat', 'timeformat'); ?>
                                    <?= form_input('timeformat', $settings->timeformat, 'class="form-control tip" id="timeformat" required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('default_email', 'default_email'); ?>
                                    <?= form_input('default_email', $settings->default_email, 'class="form-control tip" id="default_email" required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('rtl_support', 'rtl'); ?>
                                    <?php $yn = array(0 => lang('disable'), 1 => lang('enable')); ?>
                                    <?= form_dropdown('rtl', $yn, $settings->rtl, 'class="form-control select2" style="width:100%;" id="rtl"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="well well-sm">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("email_protocol", 'protocol'); ?>
                                <div class="controls">
                                    <?php
                                    $popt = array('mail' => 'PHP Mail Function', 'sendmail' => 'Send Mail', 'smtp' => 'SMTP');
                                    echo form_dropdown('protocol', $popt, ($this->db->dbdriver == 'sqlite3' ? 'smtp' : $Settings->protocol), 'class="form-control tip select2" id="protocol" style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row" id="sendmail_config" style="display: none;">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("mailpath", 'mailpath'); ?>
                                        <div class="controls"> <?php echo form_input('mailpath', $Settings->mailpath, 'class="form-control tip" id="mailpath"'); ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row" id="smtp_config" style="display: none;">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("smtp_host", 'smtp_host'); ?>
                                        <div class="controls"> <?php echo form_input('smtp_host', $Settings->smtp_host, 'class="form-control tip" id="smtp_host"'); ?> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("smtp_user", 'smtp_user'); ?>
                                        <div class="controls"> <?php echo form_input('smtp_user', $Settings->smtp_user, 'class="form-control tip" id="smtp_user"'); ?> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("smtp_pass", 'smtp_pass'); ?>
                                        <div class="controls"> <?php echo form_password('smtp_pass', $Settings->smtp_pass, 'class="form-control tip" id="smtp_pass"'); ?> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("smtp_port", 'smtp_port'); ?>
                                        <div class="controls"> <?php echo form_input('smtp_port', $Settings->smtp_port, 'class="form-control tip" id="smtp_port"'); ?> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("smtp_crypto", 'smtp_crypto'); ?>
                                        <?php
                                        $crypto_opt = array('' => lang('none'), 'tls' => 'TLS', 'ssl' => 'SSL');
                                        echo form_dropdown('smtp_crypto', $crypto_opt, $Settings->smtp_crypto, 'class="form-control tip select2" id="smtp_crypto" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-lg-12">
                            <div class="well well-sm">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="decimals"><?= lang("decimals"); ?></label>

                                        <div class="controls"> <?php
                                            $decimals = array(0 => lang('disable'), 1 => '1', 2 => '2', 3 => '3', 4 => '4');
                                            echo form_dropdown('decimals', $decimals, $Settings->decimals, 'class="form-control tip select2" id="decimals"  style="width:100%;" required="required"');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="qty_decimals"><?= lang("qty_decimals"); ?></label>

                                        <div class="controls"> <?php
                                            $qty_decimals = array(0 => lang('disable'), 1 => '1', 2 => '2');
                                            echo form_dropdown('qty_decimals', $qty_decimals, $Settings->qty_decimals, 'class="form-control tip select2" id="qty_decimals" style="width:100%;" required="required"');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('sac', 'sac'); ?>
                                        <?php $ed = array('0' => lang('disable'), '1' => lang('enable')); ?>
                                        <?= form_dropdown('sac', $ed, set_value('sac', $Settings->sac), 'class="form-control tip select2" style="width:100%;" id="sac" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="nsac">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="decimals_sep"><?= lang("decimals_sep"); ?></label>

                                            <div class="controls"> <?php
                                                $dec_point = array('.' => lang('dot'), ',' => lang('comma'));
                                                echo form_dropdown('decimals_sep', $dec_point, $Settings->decimals_sep, 'class="form-control tip select2" id="decimals_sep" style="width:100%;" required="required"');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="thousands_sep"><?= lang("thousands_sep"); ?></label>
                                            <div class="controls"> <?php
                                                $thousands_sep = array('.' => lang('dot'), ',' => lang('comma'), '0' => lang('space'));
                                                echo form_dropdown('thousands_sep', $thousands_sep, $Settings->thousands_sep, 'class="form-control tip select2" id="thousands_sep" style="width:100%;" required="required"');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('display_currency_symbol', 'display_symbol'); ?>
                                        <?php $opts = array(0 => lang('disable'), 1 => lang('before'), 2 => lang('after')); ?>
                                        <?= form_dropdown('display_symbol', $opts, $Settings->display_symbol, 'class="form-control select2" id="display_symbol" style="width:100%;" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('currency_symbol', 'symbol'); ?>
                                        <?= form_input('symbol', $Settings->symbol, 'class="form-control" id="symbol" style="width:100%;"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="well well-sm">
                                        <?php
                                        if (isset($stripe_balance)) {
                                            echo '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><h2>' . lang('stripe_balance') . '</h2>';
                                            echo '<p>' . lang('pending_amount') . ': ' . $stripe_balance['pending_amount'] . ' (' . $stripe_balance['pending_currency'] . ')';
                                            echo ', ' . lang('available_amount') . ': ' . $stripe_balance['available_amount'] . ' (' . $stripe_balance['available_currency'] . ')</p>';
                                            echo '</div>';
                                        }
                                        ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('stripe', 'stripe'); ?>
                                                <?php $ed = array('0' => lang('disable'), '1' => lang('enable')); ?>
                                                <?= form_dropdown('stripe', $ed, $Settings->stripe, 'class="form-control select2" style="width:100%;" id="stripe" required="required"'); ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div id="stripe_con">
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <?= lang('stripe_secret_key', 'stripe_secret_key'); ?>
                                                    <?php echo form_input('stripe_secret_key', $Settings->stripe_secret_key, 'class="form-control tip" id="stripe_secret_key"'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <?= lang('stripe_publishable_key', 'stripe_publishable_key'); ?>
                                                    <?php echo form_input('stripe_publishable_key', $Settings->stripe_publishable_key, 'class="form-control tip" id="stripe_publishable_key"'); ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="well well-sm">
                                        <p><?= lang('shortcut_heading') ?></p>

                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('focus_add_item', 'focus_add_item'); ?>
                                                <?php echo form_input('focus_add_item', $Settings->focus_add_item, 'class="form-control tip" id="focus_add_item"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('add_customer', 'add_customer'); ?>
                                                <?php echo form_input('add_customer', $Settings->add_customer, 'class="form-control tip" id="add_customer"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('toggle_category_slider', 'toggle_category_slider'); ?>
                                                <?php echo form_input('toggle_category_slider', $Settings->toggle_category_slider, 'class="form-control tip" id="toggle_category_slider"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('cancel_sale', 'cancel_sale'); ?>
                                                <?php echo form_input('cancel_sale', $Settings->cancel_sale, 'class="form-control tip" id="cancel_sale"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('suspend_sale', 'suspend_sale'); ?>
                                                <?php echo form_input('suspend_sale', $Settings->suspend_sale, 'class="form-control tip" id="suspend_sale"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('print_order', 'print_order'); ?>
                                                <?php echo form_input('print_order', $Settings->print_order, 'class="form-control tip" id="print_order"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('print_bill', 'print_bill'); ?>
                                                <?php echo form_input('print_bill', $Settings->print_bill, 'class="form-control tip" id="print_bill"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('finalize_sale', 'finalize_sale'); ?>
                                                <?php echo form_input('finalize_sale', $Settings->finalize_sale, 'class="form-control tip" id="finalize_sale"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('today_sale', 'today_sale'); ?>
                                                <?php echo form_input('today_sale', $Settings->today_sale, 'class="form-control tip" id="today_sale"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('open_hold_bills', 'open_hold_bills'); ?>
                                                <?php echo form_input('open_hold_bills', $Settings->open_hold_bills, 'class="form-control tip" id="open_hold_bills"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <?= lang('close_register', 'close_register'); ?>
                                                <?php echo form_input('close_register', $Settings->close_register, 'class="form-control tip" id="close_register"'); ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('login_logo', 'logo'); ?>
                                        <input type="file" name="userfile" id="logo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('printing', 'remote_printing'); ?>
                                        <?php
                                        $opts = array(0 => lang('local_install'), 1 => lang('web_browser_print'), 2 => lang('php_pos_print_app'));
                                        ?>
                                        <?= form_dropdown('remote_printing', $opts, $settings->remote_printing, 'class="form-control select2" id="remote_printing" style="width:100%;"'); ?>
                                        <span class="help-block"><?= lang('print_recommandations'); ?></span>
                                        <span class="help-block ppp">You even can purchase <a href="http://tecdiary.com/products/php-pos-print-server-windows-installer" target="_blank">PHP POS Print Server (Windows Installer)</a>.</span>
                                        <?php if (DEMO) { ?>
                                        <span class="help-block">On demo, you can test web printing only.</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                <div class="well well-sm printers">

                                    <div class="ppp">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('use_local_printers', 'local_printers'); ?>
                                                <?php $yn = array(1 => lang('yes'), 0 => lang('no')); ?>
                                                <?= form_dropdown('local_printers', $yn, set_value('local_printers', $settings->local_printers), 'class="form-control select2" style="width:100%;" id="local_printers" required="required"'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lp">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('receipt_printer', 'receipt_printer'); ?> <strong>*</strong>
                                                <?php
                                                $printer_opts = array();
                                                if (!empty($printers)) {
                                                    foreach ($printers as $printer) {
                                                        $printer_opts[$printer->id] = $printer->title;
                                                    }
                                                }
                                                ?>
                                                <?= form_dropdown('receipt_printer', $printer_opts, $settings->printer, 'class="form-control select2" id="receipt_printer" style="width:100%;"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('order_printers', 'order_printers'); ?> <strong>*</strong>
                                                <?= form_dropdown('order_printers[]', $printer_opts, '', 'multiple class="form-control select2" id="order_printers" style="width:100%;"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('cash_drawer_codes', 'cash_drawer_codes'); ?>
                                                <?= form_input('cash_drawer_codes', $settings->cash_drawer_codes, 'class="form-control" id="cash_drawer_codes" placeholder="\x1C"'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('send_print_as', 'print_img'); ?>
                                                <?php $yn = array(0 => lang('text'), 1 => lang('image')); ?>
                                                <?= form_dropdown('print_img', $yn, set_value('print_img', $settings->print_img), 'class="form-control select2" style="width:100%;" id="print_img" required="required"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                                </div>
                            </div>
                            <?= form_submit('update', lang('update_settings'), 'class="btn btn-primary"'); ?>
                            <?= form_close(); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#order_printers").select2().select2('val', <?= $settings->order_printers; ?>);
            if ($('#protocol').val() == 'smtp') {
                $('#smtp_config').slideDown();
            } else if ($('#protocol').val() == 'sendmail') {
                $('#sendmail_config').slideDown();
            }
            $('#protocol').change(function () {
                if ($(this).val() == 'smtp') {
                    $('#sendmail_config').slideUp();
                    $('#smtp_config').slideDown();
                } else if ($(this).val() == 'sendmail') {
                    $('#smtp_config').slideUp();
                    $('#sendmail_config').slideDown();
                } else {
                    $('#smtp_config').slideUp();
                    $('#sendmail_config').slideUp();
                }
            });
            if ($('#stripe').val() == 0) {
                $('#stripe_con').slideUp();
            } else {
                $('#stripe_con').slideDown();
            }
            $('#stripe').change(function () {
                if ($(this).val() == 0) {
                    $('#stripe_con').slideUp();
                } else {
                    $('#stripe_con').slideDown();
                }
            });
            if ($('#remote_printing').val() == 1) {
                $('.printers').slideUp();
                $('.ppp').slideUp();
            } else if ($('#remote_printing').val() == 0) {
                $('.printers').slideDown();
                $('.ppp').slideUp();
                $('.lp').slideDown();
            } else {
                $('.printers').slideDown();
                $('.ppp').slideDown();
                if ($('#local_printers').val() == 1) {
                    $('.lp').slideUp();
                } else {
                    $('.lp').slideDown();
                }
            }
            $('#remote_printing').change(function () {
                if ($(this).val() == 1) {
                    $('.printers').slideUp();
                    $('.ppp').slideUp();
                } else if ($(this).val() == 0) {
                    $('.printers').slideDown();
                    $('.ppp').slideUp();
                    $('.lp').slideDown();
                } else {
                    $('.printers').slideDown();
                    $('.ppp').slideDown();
                    if ($('#local_printers').val() == 1) {
                        $('.lp').slideUp();
                    } else {
                        $('.lp').slideDown();
                    }
                }
            });
            $('#local_printers').change(function () {
                if ($(this).val() == 1) {
                    $('.lp').slideUp();
                } else {
                    $('.lp').slideDown();
                }
            });
            // $.ajax({
            //     type: 'get',
            //     url: '<?= site_url('settings/timezone'); ?>',
            //     dataType: 'json',
            //     success: function(data) {
            //         if (data !== null) {
            //             $('#timezone').select2().select2('val', data.timezone);
            //             $('#timezone').val(data.timezone);
            //         }
            //     },
            // });
        });
    </script>
