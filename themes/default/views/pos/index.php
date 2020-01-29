<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
    <?= $Settings->rtl ? '<link href="'.$assets.'dist/css/rtl.css" rel="stylesheet" />' : ''; ?>
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
</head>
<body class="skin-<?= $Settings->theme_style; ?> sidebar-collapse sidebar-mini pos">
    <div class="wrapper rtl rtl-inv">

        <header class="main-header">
            <a href="<?= site_url(); ?>" class="logo">
                <?php if ($store) { ?>
                <span class="logo-mini"><?= $store->code; ?></span>
                <span class="logo-lg"><?= $store->name == 'SimplePOS' ? 'Simple<b>POS</b>' : $store->name; ?></span>
                <?php } else { ?>
                <span class="logo-mini">POS</span>
                <span class="logo-lg"><?= $Settings->site_name == 'SimplePOS' ? 'Simple<b>POS</b>' : $Settings->site_name; ?></span>
                <?php } ?>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <ul class="nav navbar-nav pull-left">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?= $assets; ?>images/<?= $Settings->selected_language; ?>.png" alt="<?= $Settings->selected_language; ?>"></a>
                        <ul class="dropdown-menu">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) { ?>
                            <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img
                                src="<?= $assets; ?>images/<?= $entry; ?>.png"
                                class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li><a href="#" class="clock"></a></li>
                            <li><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i></a></li>
                            <?php if ($Admin) { ?>
                            <li><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li>
                            <?php } ?>
                            <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                            <li><a href="<?= site_url('pos/view_bill'); ?>" target="_blank"><i class="fa fa-desktop"></i></a></li>
                            <?php } ?>
                            <li class="hidden-xs hidden-sm"><a href="<?= site_url('pos/shortcuts'); ?>" data-toggle="ajax"><i class="fa fa-key"></i></a></li>
                            <li><a href="<?= site_url('pos/register_details'); ?>" data-toggle="ajax"><?= lang('register_details'); ?></a></li>
                            <?php if ($Admin) { ?>
                            <li><a href="<?= site_url('pos/today_sale'); ?>" data-toggle="ajax"><?= lang('today_sale'); ?></a></li>
                            <?php } ?>
                            <li><a href="<?= site_url('pos/close_register'); ?>" data-toggle="ajax"><?= lang('close_register'); ?></a></li>
                            <?php if ($suspended_sales) { ?>
                            <li class="dropdown notifications-menu" id="suspended_sales">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning"><?=sizeof($suspended_sales);?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">
                                        <input type="text" autocomplete="off" data-list=".list-suspended-sales" name="filter-suspended-sales" id="filter-suspended-sales" class="form-control input-sm kb-text clearfix" placeholder="<?= lang('filter_by_reference'); ?>">
                                    </li>
                                    <li>
                                        <ul class="menu">
                                            <li class="list-suspended-sales">
                                                <?php
                                                foreach ($suspended_sales as $ss) {
                                                    echo '<a href="'.site_url('pos/?hold='.$ss->id).'" class="load_suspended">'.$this->tec->hrld($ss->date).' ('.$ss->customer_name.')<br><div class="bold">'.$ss->hold_ref.'</div></a>';
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="<?= site_url('sales/opened'); ?>"><?= lang('view_all'); ?></a></li>
                                </ul>
                            </li>
                            <?php } ?>
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?= base_url('uploads/avatars/thumbs/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="user-image" alt="Avatar" />
                                    <span><?= $this->session->userdata('first_name').' '.$this->session->userdata('last_name'); ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="<?= base_url('uploads/avatars/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="img-circle" alt="Avatar" />
                                        <p>
                                            <?= $this->session->userdata('email'); ?>
                                            <small><?= lang('member_since').' '.$this->session->userdata('created_on'); ?></small>
                                        </p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?= site_url('users/profile/'.$this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat"><?= lang('profile'); ?></a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat<?= $this->session->userdata('register_id') ? ' sign_out' : ''; ?>"><?= lang('sign_out'); ?></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" data-toggle="control-sidebar" class="sidebar-icon"><i class="fa fa-folder sidebar-icon"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu">
                        <li class="mm_welcome"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <span><?= lang('dashboard'); ?></span></a></li>
                        <?php if ($Settings->multi_store && !$this->session->userdata('store_id')) { ?>
                        <li class="mm_stores"><a href="<?= site_url('stores'); ?>"><i class="fa fa-building-o"></i> <span><?= lang('stores'); ?></span></a></li>
                        <?php } ?>
                        <li class="mm_pos"><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i> <span><?= lang('pos'); ?></span></a></li>

                        <?php if ($Admin) { ?>
                        <li class="treeview mm_products">
                            <a href="#">
                                <i class="fa fa-barcode"></i>
                                <span><?= lang('products'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="products_index"><a href="<?= site_url('products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_products'); ?></a></li>
                                <li id="products_add"><a href="<?= site_url('products/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_product'); ?></a></li>
                                <li id="products_import"><a href="<?= site_url('products/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_products'); ?></a></li>
                                <li class="divider"></li>
                                <li id="products_print_barcodes">
                                    <a onclick="window.open('<?= site_url('products/print_barcodes'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;" href="#"><i class="fa fa-circle-o"></i> <?= lang('print_barcodes'); ?></a>
                                </li>
                                <li id="products_print_labels">
                                    <a onclick="window.open('<?= site_url('products/print_labels'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;" href="#"><i class="fa fa-circle-o"></i> <?= lang('print_labels'); ?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview mm_categories">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span><?= lang('categories'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="categories_index"><a href="<?= site_url('categories'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_categories'); ?></a></li>
                                <li id="categories_add"><a href="<?= site_url('categories/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_category'); ?></a></li>
                                <li id="categories_import"><a href="<?= site_url('categories/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_categories'); ?></a></li>
                            </ul>
                        </li>
                        <?php if ($this->session->userdata('store_id')) { ?>
                        <li class="treeview mm_sales">
                            <a href="#">
                                <i class="fa fa-shopping-cart"></i>
                                <span><?= lang('sales'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>
                                <li id="sales_opened"><a href="<?= site_url('sales/opened'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>
                            </ul>
                        </li>
                        <li class="treeview mm_purchases">
                            <a href="#">
                                <i class="fa fa-plus"></i>
                                <span><?= lang('purchases'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="purchases_index"><a href="<?= site_url('purchases'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_purchases'); ?></a></li>
                                <li id="purchases_add"><a href="<?= site_url('purchases/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_purchase'); ?></a></li>
                                <li class="divider"></li>
                                <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                                <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <li class="treeview mm_gift_cards">
                            <a href="#">
                                <i class="fa fa-credit-card"></i>
                                <span><?= lang('gift_cards'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="gift_cards_index"><a href="<?= site_url('gift_cards'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_gift_cards'); ?></a></li>
                                <li id="gift_cards_add"><a href="<?= site_url('gift_cards/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_gift_card'); ?></a></li>
                            </ul>
                        </li>

                        <li class="treeview mm_auth mm_customers mm_suppliers">
                            <a href="#">
                                <i class="fa fa-users"></i>
                                <span><?= lang('people'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="auth_users"><a href="<?= site_url('users'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_users'); ?></a></li>
                                <li id="auth_add"><a href="<?= site_url('users/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_user'); ?></a></li>
                                <li class="divider"></li>
                                <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                                <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                                <li class="divider"></li>
                                <li id="suppliers_index"><a href="<?= site_url('suppliers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_suppliers'); ?></a></li>
                                <li id="suppliers_add"><a href="<?= site_url('suppliers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_supplier'); ?></a></li>
                            </ul>
                        </li>

                        <li class="treeview mm_settings">
                            <a href="#">
                                <i class="fa fa-cogs"></i>
                                <span><?= lang('settings'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="settings_index"><a href="<?= site_url('settings'); ?>"><i class="fa fa-circle-o"></i> <?= lang('settings'); ?></a></li>
                                <li class="divider"></li>
                                <?php if ($Settings->multi_store) { ?>
                                <li id="settings_stores"><a href="<?= site_url('settings/stores'); ?>"><i class="fa fa-circle-o"></i> <?= lang('stores'); ?></a></li>
                                <li id="settings_add_store"><a href="<?= site_url('settings/add_store'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_store'); ?></a></li>
                                <li class="divider"></li>
                                <?php } ?>
                                <li id="settings_printers"><a href="<?= site_url('settings/printers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('printers'); ?></a></li>
                                <li id="settings_add_printer"><a href="<?= site_url('settings/add_printer'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_printer'); ?></a></li>
                                <li class="divider"></li>
                                <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                                <li id="settings_backups"><a href="<?= site_url('settings/backups'); ?>"><i class="fa fa-circle-o"></i> <?= lang('backups'); ?></a></li>
                                <?php } ?>
                                <!-- <li id="settings_updates"><a href="<?= site_url('settings/updates'); ?>"><i class="fa fa-circle-o"></i> <?= lang('updates'); ?></a></li> -->
                            </ul>
                        </li>
                        <li class="treeview mm_reports">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span><?= lang('reports'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="reports_daily_sales"><a href="<?= site_url('reports/daily_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('daily_sales'); ?></a></li>
                                <li id="reports_monthly_sales"><a href="<?= site_url('reports/monthly_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('monthly_sales'); ?></a></li>
                                <li id="reports_index"><a href="<?= site_url('reports'); ?>"><i class="fa fa-circle-o"></i> <?= lang('sales_report'); ?></a></li>
                                <li class="divider"></li>
                                <li id="reports_payments"><a href="<?= site_url('reports/payments'); ?>"><i class="fa fa-circle-o"></i> <?= lang('payments_report'); ?></a></li>
                                <li class="divider"></li>
                                <li id="reports_registers"><a href="<?= site_url('reports/registers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('registers_report'); ?></a></li>
                                <li class="divider"></li>
                                <li id="reports_top_products"><a href="<?= site_url('reports/top_products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('top_products'); ?></a></li>
                                <li id="reports_products"><a href="<?= site_url('reports/products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('products_report'); ?></a></li>
                            </ul>
                        </li>
                        <?php } else { ?>
                        <li class="mm_products"><a href="<?= site_url('products'); ?>"><i class="fa fa-barcode"></i> <span><?= lang('products'); ?></span></a></li>
                        <li class="mm_categories"><a href="<?= site_url('categories'); ?>"><i class="fa fa-folder-open"></i> <span><?= lang('categories'); ?></span></a></li>
                        <?php if ($this->session->userdata('store_id')) { ?>
                        <li class="treeview mm_sales">
                            <a href="#">
                                <i class="fa fa-shopping-cart"></i>
                                <span><?= lang('sales'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>
                                <li id="sales_opened"><a href="<?= site_url('sales/opened'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>
                            </ul>
                        </li>
                        <li class="treeview mm_purchases">
                            <a href="#">
                                <i class="fa fa-plus"></i>
                                <span><?= lang('expenses'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                                <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <li class="treeview mm_gift_cards">
                            <a href="#">
                                <i class="fa fa-credit-card"></i>
                                <span><?= lang('gift_cards'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="gift_cards_index"><a href="<?= site_url('gift_cards'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_gift_cards'); ?></a></li>
                                <li id="gift_cards_add"><a href="<?= site_url('gift_cards/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_gift_card'); ?></a></li>
                            </ul>
                        </li>
                        <li class="treeview mm_customers">
                            <a href="#">
                                <i class="fa fa-users"></i>
                                <span><?= lang('customers'); ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                                <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                    </ul>
                </section>
            </aside>

            <div class="content-wrapper">

                <div class="col-lg-12 alerts">
                <?php if ($error)  { ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
                        <?= $error; ?>
                    </div>
                    <?php } if ($message) { ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                        <?= $message; ?>
                    </div>
                    <?php } ?>
                </div>

                <table style="width:100%;" class="layout-table">
                    <tr>
                        <td style="width: 460px;">

                            <div id="pos">
                                <?= form_open('pos', 'id="pos-sale-form"'); ?>
                                <div class="well well-sm" id="leftdiv">
                                    <div id="lefttop" style="margin-bottom:5px;">
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <div class="input-group">
                                                <?php foreach($customers as $customer){ $cus[$customer->id] = $customer->name; } ?>
                                                <?= form_dropdown('customer_id', $cus, set_value('customer_id', $Settings->default_customer), 'id="spos_customer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control select2" style="width:100%;position:absolute;"'); ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="#" id="add-customer" class="external" data-toggle="modal" data-target="#myModal"><i class="fa fa-2x fa-plus-circle" id="addIcon"></i></a>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <?php if ($eid && $Admin) { ?>
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <?= form_input('date', set_value('date', $sale->date), 'id="date" required="required" class="form-control"'); ?>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <input type="text" name="hold_ref" value="<?= $reference_note; ?>" id="hold_ref" class="form-control kb-text" placeholder="<?=lang('reference_note')?>" />
                                        </div>
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <input type="text" name="code" id="add_item" class="form-control" placeholder="<?=lang('search__scan')?>" />
                                        </div>
                                    </div>
                                    <div id="printhead" class="print">
                                        <?= $Settings->header; ?>
                                        <p><?= lang('date'); ?>: <?=date($Settings->dateformat)?></p>
                                    </div>
                                    <div id="print" class="fixed-table-container">
                                        <div id="list-table-div">
                                            <div class="fixed-table-header">
                                                <table class="table table-striped table-condensed table-hover list-table" style="margin:0;">
                                                    <thead>
                                                        <tr class="success">
                                                            <th><?=lang('product')?></th>
                                                            <th style="width: 15%;text-align:center;"><?=lang('price')?></th>
                                                            <th style="width: 15%;text-align:center;"><?=lang('qty')?></th>
                                                            <th style="width: 20%;text-align:center;"><?=lang('subtotal')?></th>
                                                            <th style="width: 20px;" class="satu"><i class="fa fa-trash-o"></i></th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <table id="posTable" class="table table-striped table-condensed table-hover list-table" style="margin:0px;" data-height="100">
                                                <thead>
                                                    <tr class="success">
                                                        <th><?=lang('product')?></th>
                                                        <th style="width: 15%;text-align:center;"><?=lang('price')?></th>
                                                        <th style="width: 15%;text-align:center;"><?=lang('qty')?></th>
                                                        <th style="width: 20%;text-align:center;"><?=lang('subtotal')?></th>
                                                        <th style="width: 20px;" class="satu"><i class="fa fa-trash-o"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div style="clear:both;"></div>
                                        <div id="totaldiv">
                                            <table id="totaltbl" class="table table-condensed totals" style="margin-bottom:10px;">
                                                <tbody>
                                                    <tr class="info">
                                                        <td width="25%"><?=lang('total_items')?></td>
                                                        <td class="text-right" style="padding-right:10px;"><span id="count">0</span></td>
                                                        <td width="25%"><?=lang('total')?></td>
                                                        <td class="text-right" colspan="2"><span id="total">0</span></td>
                                                    </tr>
                                                    <tr class="info">
                                                        <td width="25%"><a href="#" id="add_discount"><?=lang('discount')?></a></td>
                                                        <td class="text-right" style="padding-right:10px;"><span id="ds_con">0</span></td>
                                                        <td width="25%"><a href="#" id="add_tax"><?=lang('order_tax')?></a></td>
                                                        <td class="text-right"><span id="ts_con">0</span></td>
                                                    </tr>
                                                    <tr class="success">
                                                        <td colspan="2" style="font-weight:bold;">
                                                            <?=lang('total_payable')?>
                                                            <a role="button" data-toggle="modal" data-target="#noteModal">
                                                                <i class="fa fa-comment"></i>
                                                            </a>
                                                        </td>
                                                        <td class="text-right" colspan="2" style="font-weight:bold;"><span id="total-payable">0</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="botbuttons" class="col-xs-12 text-center">
                                        <div class="row">
                                            <div class="col-xs-4" style="padding: 0;">
                                                <div class="btn-group-vertical btn-block">
                                                    <button type="button" class="btn btn-warning btn-block btn-flat"
                                                    id="suspend"><?= lang('hold'); ?></button>
                                                    <button type="button" class="btn btn-danger btn-block btn-flat"
                                                    id="reset"><?= lang('cancel'); ?></button>
                                                </div>

                                            </div>
                                            <div class="col-xs-4" style="padding: 0 5px;">
                                                <div class="btn-group-vertical btn-block">
                                                    <button type="button" class="btn bg-purple btn-block btn-flat" id="print_order"><?= lang('print_order'); ?></button>

                                                    <button type="button" class="btn bg-navy btn-block btn-flat" id="print_bill"><?= lang('print_bill'); ?></button>
                                                </div>
                                            </div>
                                            <div class="col-xs-4" style="padding: 0;">
                                                <button type="button" class="btn btn-success btn-block btn-flat" id="<?= $eid ? 'submit-sale' : 'payment'; ?>" style="height:67px;"><?= $eid ? lang('submit') : lang('payment'); ?></button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <span id="hidesuspend"></span>
                                    <input type="hidden" name="spos_note" value="" id="spos_note">

                                    <div id="payment-con">
                                        <input type="hidden" name="amount" id="amount_val" value="<?= $eid ? $sale->paid : ''; ?>"/>
                                        <input type="hidden" name="balance_amount" id="balance_val" value=""/>
                                        <input type="hidden" name="paid_by" id="paid_by_val" value="cash"/>
                                        <input type="hidden" name="cc_no" id="cc_no_val" value=""/>
                                        <input type="hidden" name="paying_gift_card_no" id="paying_gift_card_no_val" value=""/>
                                        <input type="hidden" name="cc_holder" id="cc_holder_val" value=""/>
                                        <input type="hidden" name="cheque_no" id="cheque_no_val" value=""/>
                                        <input type="hidden" name="cc_month" id="cc_month_val" value=""/>
                                        <input type="hidden" name="cc_year" id="cc_year_val" value=""/>
                                        <input type="hidden" name="cc_type" id="cc_type_val" value=""/>
                                        <input type="hidden" name="cc_cvv2" id="cc_cvv2_val" value=""/>
                                        <input type="hidden" name="balance" id="balance_val" value=""/>
                                        <input type="hidden" name="payment_note" id="payment_note_val" value=""/>
                                    </div>
                                    <input type="hidden" name="customer" id="customer" value="<?=$Settings->default_customer?>" />
                                    <input type="hidden" name="order_tax" id="tax_val" value="" />
                                    <input type="hidden" name="order_discount" id="discount_val" value="" />
                                    <input type="hidden" name="count" id="total_item" value="" />
                                    <input type="hidden" name="did" id="is_delete" value="<?=$sid;?>" />
                                    <input type="hidden" name="eid" id="is_delete" value="<?=$eid;?>" />
                                    <input type="hidden" name="total_items" id="total_items" value="0" />
                                    <input type="hidden" name="total_quantity" id="total_quantity" value="0" />
                                    <input type="submit" id="submit" value="Submit Sale" style="display: none;" />
                                </div>
                                <?=form_close();?>
                            </div>

                        </td>
                        <td>
                            <div class="contents" id="right-col">
                                <div id="item-list">
                                    <div class="items">
                                        <?php echo $products; ?>
                                    </div>
                                </div>
                                <div class="product-nav">
                                    <div class="btn-group btn-group-justified">
                                        <div class="btn-group">
                                            <button style="z-index:10002;" class="btn btn-warning pos-tip btn-flat" type="button" id="previous"><i class="fa fa-chevron-left"></i></button>
                                        </div>
                                        <div class="btn-group">
                                            <button style="z-index:10003;" class="btn btn-success pos-tip btn-flat" type="button" id="sellGiftCard"><i class="fa fa-credit-card" id="addIcon"></i> <?= lang('sell_gift_card') ?></button>
                                        </div>
                                        <div class="btn-group">
                                            <button style="z-index:10004;" class="btn btn-warning pos-tip btn-flat" type="button" id="next"><i class="fa fa-chevron-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <aside class="control-sidebar control-sidebar-dark" id="categories-list">
            <div class="tab-content sb">
                <div class="tab-pane active sb" id="control-sidebar-home-tab">
                    <div id="filter-categories-con">
                        <input type="text" autocomplete="off" data-list=".control-sidebar-menu" name="filter-categories" id="filter-categories" class="form-control sb col-xs-12 kb-text" placeholder="<?= lang('filter_categories'); ?>" style="margin-bottom: 20px;">
                    </div>
                    <div class="clearfix sb"></div>
                    <div id="category-sidebar-menu">
                        <ul class="control-sidebar-menu">
                            <?php
                            foreach($categories as $category) {
                                echo '<li><a href="#" class="category'.($category->id == $Settings->default_category ? ' active' : '').'" id="'.$category->id.'">';
                                if ($category->image) {
                                    echo '<div class="menu-icon"><img src="'.base_url('uploads/thumbs/'.$category->image).'" alt="" class="img-thumbnail img-responsive"></div>';
                                } else {
                                    echo '<i class="menu-icon fa fa-folder-open bg-red"></i>';
                                }
                                echo '<div class="menu-info"><h4 class="control-sidebar-subheading">'.$category->code.'</h4><p>'.$category->name.'</p></div>
                            </a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
    <div class="control-sidebar-bg sb"></div>
</div>
</div>
<div id="order_tbl" style="display:none;"><span id="order_span"></span>
    <table id="order-table" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>
<div id="bill_tbl" style="display:none;"><span id="bill_span"></span>
    <table id="bill-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
    <table id="bill-total-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>
<div style="width:380px;background:#FFF;display:block">
    <div id="order-data" style="display:none;" class="text-center">
        <h1><?= $store->name; ?></h1>
        <h2><?= lang('order'); ?></h2>
        <div id="preo" class="text-left"></div>
    </div>
    <div id="bill-data" style="display:none;" class="text-center">
        <h1><?= $store->name; ?></h1>
        <h2><?= lang('bill'); ?></h2>
        <div id="preb" class="text-left"></div>
    </div>
</div>

<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>

<div class="modal" data-easein="flipYIn" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?= lang("card_no", "gccard_no"); ?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genNo"><i class="fa fa-cogs"></i></a></div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname"/>
                <div class="form-group">
                    <?= lang("value", "gcvalue"); ?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("price", "gcprice"); ?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("expiry_date", "gcexpiry"); ?>
                    <?php echo form_input('gcexpiry', '', 'class="form-control" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="dsModalLabel"><?= lang('discount_title'); ?></h4>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control input-sm kb-pad' id='get_ds' onClick='this.select();' value=''>

                <label class="checkbox" for="apply_to_order">
                    <input type="radio" name="apply_to" value="order" id="apply_to_order" checked="checked"/>
                    <?= lang('apply_to_order') ?>
                </label>
                <label class="checkbox" for="apply_to_products">
                    <input type="radio" name="apply_to" value="products" id="apply_to_products"/>
                    <?= lang('apply_to_products') ?>
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="updateDiscount" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="tsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="tsModalLabel"><?= lang('tax_title'); ?></h4>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control input-sm kb-pad' id='get_ts' onClick='this.select();' value=''>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="updateTax" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="noteModalLabel"><?= lang('note'); ?></h4>
            </div>
            <div class="modal-body">
                <textarea name="snote" id="snote" class="pa form-control kb-text"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="update-note" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="proModal" tabindex="-1" role="dialog" aria-labelledby="proModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="proModalLabel">
                    <?=lang('payment')?>
                </h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th style="width:25%;"><?= lang('net_price'); ?></th>
                        <th style="width:25%;"><span id="net_price"></span></th>
                        <th style="width:25%;"><?= lang('product_tax'); ?></th>
                        <th style="width:25%;"><span id="pro_tax"></span> <span id="pro_tax_method"></span></th>
                    </tr>
                </table>
                <input type="hidden" id="row_id" />
                <input type="hidden" id="item_id" />
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?=lang('unit_price', 'nPrice')?>
                            <input type="text" class="form-control input-sm kb-pad" id="nPrice" onClick="this.select();" placeholder="<?=lang('new_price')?>">
                        </div>
                        <div class="form-group">
                            <?=lang('discount', 'nDiscount')?>
                            <input type="text" class="form-control input-sm kb-pad" id="nDiscount" onClick="this.select();" placeholder="<?=lang('discount')?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?=lang('quantity', 'nQuantity')?>
                            <input type="text" class="form-control input-sm kb-pad" id="nQuantity" onClick="this.select();" placeholder="<?=lang('current_quantity')?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?=lang('comment', 'nComment')?>
                            <textarea class="form-control kb-text" id="nComment"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button class="btn btn-success" id="editItem"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="susModalLabel"><?= lang('suspend_sale'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('type_reference_note'); ?></p>

                <div class="form-group">
                    <?= lang("reference_note", "reference_note"); ?>
                    <?php echo form_input('reference_note', $reference_note, 'class="form-control kb-text" id="reference_note"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
                <button type="button" id="suspend_sale" class="btn btn-primary"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>



<div class="modal" data-easein="flipYIn" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="opModal" tabindex="-1" role="dialog" aria-labelledby="opModalLabel" aria-hidden="true"></div>

<div class="modal" data-easein="flipYIn" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="payModalLabel">
                    <?=lang('payment')?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-9">
                        <div class="font16">
                            <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                <tbody>
                                    <tr>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_items"); ?></td>
                                        <td width="25%" class="text-right"><span id="item_count">0.00</span></td>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_payable"); ?></td>
                                        <td width="25%" class="text-right"><span id="twt">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td style="border-right-color: #FFF !important;"><?= lang("total_paying"); ?></td>
                                        <td class="text-right"><span id="total_paying">0.00</span></td>
                                        <td style="border-right-color: #FFF !important;"><?= lang("balance"); ?></td>
                                        <td class="text-right"><span id="balance">0.00</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?= lang('note', 'note'); ?>
                                    <textarea name="note" id="note" class="pa form-control kb-text"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <?= lang("amount", "amount"); ?>
                                    <input name="amount" type="text" id="amount"
                                    class="pa form-control kb-pad amount"/>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <?= lang("paying_by", "paid_by"); ?>
                                    <select id="paid_by" class="form-control paid_by select2" style="width:100%;">
                                        <option value="cash"><?= lang("cash"); ?></option>
                                        <option value="CC"><?= lang("cc"); ?></option>
                                        <option value="cheque"><?= lang("cheque"); ?></option>
                                        <option value="gift_card"><?= lang("gift_card"); ?></option>
                                        <?= isset($Settings->stripe) ? '<option value="stripe">' . lang("stripe") . '</option>' : ''; ?>
                                        <option value="other"><?= lang("other"); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group gc" style="display: none;">
                                    <?= lang("gift_card_no", "gift_card_no"); ?>
                                    <input type="text" id="gift_card_no"
                                    class="pa form-control kb-pad gift_card_no gift_card_input"/>

                                    <div id="gc_details"></div>
                                </div>
                                <div class="pcc" style="display:none;">
                                    <div class="form-group">
                                        <input type="text" id="swipe" class="form-control swipe swipe_input"
                                        placeholder="<?= lang('focus_swipe_here') ?>"/>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <input type="text" id="pcc_no"
                                                class="form-control kb-pad"
                                                placeholder="<?= lang('cc_no') ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">

                                                <input type="text" id="pcc_holder"
                                                class="form-control kb-text"
                                                placeholder="<?= lang('cc_holder') ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                <select id="pcc_type"
                                                class="form-control pcc_type select2"
                                                placeholder="<?= lang('card_type') ?>">
                                                <option value="Visa"><?= lang("Visa"); ?></option>
                                                <option
                                                value="MasterCard"><?= lang("MasterCard"); ?></option>
                                                <option value="Amex"><?= lang("Amex"); ?></option>
                                                <option
                                                value="Discover"><?= lang("Discover"); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <input type="text" id="pcc_month"
                                            class="form-control kb-pad"
                                            placeholder="<?= lang('month') ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">

                                            <input type="text" id="pcc_year"
                                            class="form-control kb-pad"
                                            placeholder="<?= lang('year') ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">

                                            <input type="text" id="pcc_cvv2"
                                            class="form-control kb-pad"
                                            placeholder="<?= lang('cvv2') ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pcheque" style="display:none;">
                                <div class="form-group"><?= lang("cheque_no", "cheque_no"); ?>
                                    <input type="text" id="cheque_no"
                                    class="form-control cheque_no kb-text"/>
                                </div>
                            </div>
                            <div class="pcash">
                                <div class="form-group"><?= lang("payment_note", "payment_note"); ?>
                                    <input type="text" id="payment_note"
                                    class="form-control payment_note kb-text"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-3 text-center">
                    <!-- <span style="font-size: 1.2em; font-weight: bold;"><?= lang('quick_cash'); ?></span> -->

                    <div class="btn-group btn-group-vertical" style="width:100%;">
                        <button type="button" class="btn btn-info btn-block quick-cash" id="quick-payable">0.00
                        </button>
                        <?php
                        foreach (lang('quick_cash_notes') as $cash_note_amount) {
                            echo '<button type="button" class="btn btn-block btn-warning quick-cash">' . $cash_note_amount . '</button>';
                        }
                        ?>
                        <button type="button" class="btn btn-block btn-danger"
                        id="clear-cash-notes"><?= lang('clear'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
            <button class="btn btn-primary" id="<?= $eid ? '' : 'submit-sale'; ?>"><?=lang('submit')?></button>
        </div>
    </div>
</div>
</div>

<div class="modal" data-easein="flipYIn" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="cModalLabel">
                    <?=lang('add_customer')?>
                </h4>
            </div>
            <?= form_open('pos/add_customer', 'id="customer-form"'); ?>
            <div class="modal-body">
                <div id="c-alert" class="alert alert-danger" style="display:none;"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="code">
                                <?= lang("name"); ?>
                            </label>
                            <?= form_input('name', '', 'class="form-control input-sm kb-text" id="cname"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cemail">
                                <?= lang("email_address"); ?>
                            </label>
                            <?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemail"'); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="phone">
                                <?= lang("phone"); ?>
                            </label>
                            <?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphone"');?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cf1">
                                <?= lang("cf1"); ?>
                            </label>
                            <?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf1"'); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cf2">
                                <?= lang("cf2"); ?>
                            </label>
                            <?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf2"');?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="margin-top:0;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
                <button type="submit" class="btn btn-primary" id="add_customer"> <?=lang('add_customer')?> </button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="posModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>

<script type="text/javascript">
    var base_url = '<?=base_url();?>', assets = '<?= $assets ?>';
    var dateformat = '<?=$Settings->dateformat;?>', timeformat = '<?= $Settings->timeformat ?>';
    <?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
    var Settings = <?= json_encode($Settings); ?>;
    var sid = false, username = '<?=$this->session->userdata('username');?>', spositems = {};
    $(window).load(function () {
        $('#mm_<?=$m?>').addClass('active');
        $('#<?=$m?>_<?=$v?>').addClass('active');
    });
    var pro_limit = <?=$Settings->pro_limit?>, java_applet = 0, count = 1, total = 0, an = 1, p_page = 0, page = 0, cat_id = <?=$Settings->default_category?>, tcp = <?=$tcp?>;
    var gtotal = 0, order_discount = 0, order_tax = 0, protect_delete = <?= ($Admin) ? 0 : ($Settings->pin_code ? 1 : 0); ?>;
    var order_data = {}, bill_data = {};
    var csrf_hash = '<?= $this->security->get_csrf_hash(); ?>';
    <?php
    if ($Settings->remote_printing == 2) {

        ?>
        var ob_store_name = "<?= printText($store->name, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        order_data.store_name = ob_store_name;
        bill_data.store_name = ob_store_name;

        ob_header = "";
        ob_header += "<?= printText($store->name.' ('.$store->code.')', (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        <?php
        if ($store->address1) { ?>
            ob_header += "<?= printText($store->address1, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n";
            <?php
        }
        if ($store->address2) { ?>
            ob_header += "<?= printText($store->address2, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n";
            <?php
        }
        if ($store->city) { ?>
            ob_header += "<?= printText($store->city, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n";
            <?php
        } ?>
        ob_header += "<?= printText(lang('tel').': '.$store->phone, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n\r\n";
        ob_header += "<?= printText(str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), $store->receipt_header), (!empty($printer) ? $printer->char_per_line : ''));?>\r\n\r\n";

        order_data.header = ob_header + "<?= printText(lang('order'), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        bill_data.header = ob_header + "<?= printText(lang('bill'), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        order_data.totals = '';
        order_data.payments = '';
        bill_data.payments = '';
        order_data.footer = '';
        bill_data.footer = "<?= lang('merchant_copy'); ?> \n";
        <?php
    }
    ?>
    var lang = new Array();
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['please_add_product'] = '<?= lang('please_add_product'); ?>';
    lang['paid_less_than_amount'] = '<?= lang('paid_less_than_amount'); ?>';
    lang['x_suspend'] = '<?= lang('x_suspend'); ?>';
    lang['discount_title'] = '<?= lang('discount_title'); ?>';
    lang['update'] = '<?= lang('update'); ?>';
    lang['tax_title'] = '<?= lang('tax_title'); ?>';
    lang['leave_alert'] = '<?= lang('leave_alert'); ?>';
    lang['close'] = '<?= lang('close'); ?>';
    lang['delete'] = '<?= lang('delete'); ?>';
    lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
    lang['wrong_pin'] = '<?= lang('wrong_pin'); ?>';
    lang['file_required_fields'] = '<?= lang('file_required_fields'); ?>';
    lang['enter_pin_code'] = '<?= lang('enter_pin_code'); ?>';
    lang['incorrect_gift_card'] = '<?= lang('incorrect_gift_card'); ?>';
    lang['card_no'] = '<?= lang('card_no'); ?>';
    lang['value'] = '<?= lang('value'); ?>';
    lang['balance'] = '<?= lang('balance'); ?>';
    lang['unexpected_value'] = '<?= lang('unexpected_value'); ?>';
    lang['inclusive'] = '<?= lang('inclusive'); ?>';
    lang['exclusive'] = '<?= lang('exclusive'); ?>';
    lang['total'] = '<?= lang('total'); ?>';
    lang['total_items'] = '<?= lang('total_items'); ?>';
    lang['order_tax'] = '<?= lang('order_tax'); ?>';
    lang['order_discount'] = '<?= lang('order_discount'); ?>';
    lang['total_payable'] = '<?= lang('total_payable'); ?>';
    lang['rounding'] = '<?= lang('rounding'); ?>';
    lang['grand_total'] = '<?= lang('grand_total'); ?>';
    lang['register_open_alert'] = '<?= lang('register_open_alert'); ?>';
    lang['discount'] = '<?= lang('discount'); ?>';
    lang['order'] = '<?= lang('order'); ?>';
    lang['bill'] = '<?= lang('bill'); ?>';
    lang['merchant_copy'] = '<?= lang('merchant_copy'); ?>';

    $(document).ready(function() {
        <?php if ($this->session->userdata('rmspos')) { ?>
            if (get('spositems')) { remove('spositems'); }
            if (get('spos_discount')) { remove('spos_discount'); }
            if (get('spos_tax')) { remove('spos_tax'); }
            if (get('spos_note')) { remove('spos_note'); }
            if (get('spos_customer')) { remove('spos_customer'); }
            if (get('amount')) { remove('amount'); }
            <?php $this->tec->unset_data('rmspos'); } ?>

            if (get('rmspos')) {
                if (get('spositems')) { remove('spositems'); }
                if (get('spos_discount')) { remove('spos_discount'); }
                if (get('spos_tax')) { remove('spos_tax'); }
                if (get('spos_note')) { remove('spos_note'); }
                if (get('spos_customer')) { remove('spos_customer'); }
                if (get('amount')) { remove('amount'); }
                remove('rmspos');
            }
            <?php if ($sid) { ?>

                store('spositems', JSON.stringify(<?=$items;?>));
                store('spos_discount', '<?=$suspend_sale->order_discount_id;?>');
                store('spos_tax', '<?=$suspend_sale->order_tax_id;?>');
                store('spos_customer', '<?=$suspend_sale->customer_id;?>');
                $('#spos_customer').select2().select2('val', '<?=$suspend_sale->customer_id;?>');
                store('rmspos', '1');
                $('#tax_val').val('<?=$suspend_sale->order_tax_id;?>');
                $('#discount_val').val('<?=$suspend_sale->order_discount_id;?>');
                <?php } elseif ($eid) { ?>
                    $('#date').inputmask("y-m-d h:s:s", { "placeholder": "YYYY/MM/DD HH:mm:ss" });
                    store('spositems', JSON.stringify(<?=$items;?>));
                    store('spos_discount', '<?=$sale->order_discount_id;?>');
                    store('spos_tax', '<?=$sale->order_tax_id;?>');
                    store('spos_customer', '<?=$sale->customer_id;?>');
                    store('sale_date', '<?=$sale->date;?>');
                    $('#spos_customer').select2().select2('val', '<?=$sale->customer_id;?>');
                    $('#date').val('<?=$sale->date;?>');
                    store('rmspos', '1');
                    $('#tax_val').val('<?=$sale->order_tax_id;?>');
                    $('#discount_val').val('<?=$sale->order_discount_id;?>');
                    <?php } else { ?>
                        if (! get('spos_discount')) {
                            store('spos_discount', '<?=$Settings->default_discount;?>');
                            $('#discount_val').val('<?=$Settings->default_discount;?>');
                        }
                        if (! get('spos_tax')) {
                            store('spos_tax', '<?=$Settings->default_tax_rate;?>');
                            $('#tax_val').val('<?=$Settings->default_tax_rate;?>');
                        }
                        <?php } ?>

                        if (ots = get('spos_tax')) {
                            $('#tax_val').val(ots);
                        }
                        if (ods = get('spos_discount')) {
                            $('#discount_val').val(ods);
                        }
                        bootbox.addLocale('bl',{OK:'<?= lang('ok'); ?>',CANCEL:'<?= lang('no'); ?>',CONFIRM:'<?= lang('yes'); ?>'});
                        bootbox.setDefaults({closeButton:false,locale:"bl"});
                        <?php if ($eid) { ?>
                            $('#suspend').attr('disabled', true);
                            $('#print_order').attr('disabled', true);
                            $('#print_bill').attr('disabled', true);
                            <?php } ?>
                        });
                    </script>

                    <script type="text/javascript">
                        var socket = null;
                        <?php
                        if ($Settings->remote_printing == 2) {
                            ?>
                            try {
                                socket = new WebSocket('ws://127.0.0.1:6441');
                                socket.onopen = function () {
                                    console.log('Connected');
                                    return;
                                };
                                socket.onclose = function () {
                                    console.log('Connection closed');
                                    return;
                                };
                            } catch (e) {
                                console.log(e);
                            }
                            <?php
                        }
                        ?>
                        function printBill(bill) {
                            if (Settings.remote_printing == 1) {
                                Popup($('#bill_tbl').html());
                            } else if (Settings.remote_printing == 2) {
                                if (socket.readyState == 1) {
                                    if (Settings.print_img == 1) {
                                        $('#bill-data').show();
                                        $('#preb').html(
                                            '<pre style="background:#FFF;font-size:20px;margin:0;border:0;color:#000 !important;">' +
                                            bill_data.info +
                                            bill_data.items +
                                            '\n' +
                                            bill_data.totals +
                                            '</pre>'
                                            );
                                        var element = $('#bill-data').get(0);
                                        html2canvas(element, {scrollY: 0, scale: 1.7}).then(function(canvas) {
                                            var dataURL = canvas.toDataURL();
                                            var socket_data = {
                                                'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                                                'text': dataURL, 'cash_drawer': 0
                                            };
                                            socket.send(JSON.stringify({
                                                type: 'print-img',
                                                data: socket_data
                                            }));
                                            // return Canvas2Image.saveAsPNG(canvas);
                                        });
                                        setTimeout(function() {
                                            $('#bill-data').hide();
                                        }, 500);
                                    } else {
                                        var socket_data = {'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>, 'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>', 'text': bill};
                                        socket.send(JSON.stringify({
                                            type: 'print-receipt',
                                            data: socket_data
                                        }));
                                    }
                                    return false;
                                } else {
                                    bootbox.alert('<?= lang('pos_print_error'); ?>');
                                    return false;
                                }
                            }
                        }
                        var order_printers = <?= $Settings->local_printers ? "''" : json_encode($order_printers); ?>;
                        function printOrder(order) {
                            if (Settings.remote_printing == 1) {
                                Popup($('#order_tbl').html());
                            } else if (Settings.remote_printing == 2) {
                                if (socket.readyState == 1) {
                                    if (Settings.print_img == 1) {
                                        $('#order-data').show();
                                        $('#preo').html(
                                            '<pre style="background:#FFF;font-size:20px;margin:0;border:0;color:#000 !important;">' + order_data.info + order_data.items + '</pre>'
                                            );
                                        var element = $('#order-data').get(0);
                                        html2canvas(element, {scrollY: 0, scale: 1.7}).then(function(canvas) {
                                            var dataURL = canvas.toDataURL();
                                            var socket_data = {
                                                'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                                                'text': dataURL, 'order': 1, 'cash_drawer': 0
                                            };
                                            socket.send(JSON.stringify({
                                                type: 'print-img',
                                                data: socket_data
                                            }));
                                            // return Canvas2Image.saveAsPNG(canvas);
                                        });
                                        setTimeout(function() {
                                            $('#order-data').hide();
                                        }, 500);
                                    } else {
                                        if (order_printers == '') {
                                            var socket_data = { 'printer': false, 'order': true,
                                            'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>',
                                            'text': order };
                                            socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
                                        } else {
                                            $.each(order_printers, function() {
                                                var socket_data = {'printer': this, 'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>', 'text': order};
                                                socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
                                            });
                                        }
                                    }
                                    return false;
                                } else {
                                    bootbox.alert('<?= lang('pos_print_error'); ?>');
                                    return false;
                                }
                            }
                        }
                    </script>
                    <?php
                    if (isset($print) && !empty($print)) {
                        /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
                        include 'remote_printing.php';
                    }
                    ?>

                    <script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
                    <script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
                    <script src="<?= $assets ?>dist/js/pos.min.js" type="text/javascript"></script>
                    <?php if($Settings->remote_printing != 1 && $Settings->print_img) { ?>
                    <script src="<?= $assets ?>dist/js/htmlimg.js"></script>
                    <?php } ?>
                </body>
                </html>
