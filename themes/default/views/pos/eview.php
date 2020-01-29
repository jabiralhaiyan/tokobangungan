<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<?php
if ($modal) {
    ?>
    <style type="text/css" media="all">
        body { color: #000; }
        #wrapper { max-width: 500px; margin: 0 auto; padding-top: 20px; font-size: 28px; }
        .btn { margin-bottom: 5px; }
        .table { border-radius: 3px; }
        .table th { background: #f5f5f5; }
        .table th, .table td { vertical-align: middle !important; }
        h3 { margin: 5px 0; font-size: 36px; }

        @media print {
            .no-print { display: none; }
            #wrapper { max-width: 500px; width: 100%; min-width: 250px; margin: 0 auto; }
        }

        <?php if($Settings->rtl) { ?>
            .text-right { text-align: left; }
            .text-left { text-align: right; }
            tfoot tr th:first-child { text-align: left; }
        <?php } else { ?>
            tfoot tr th:first-child { text-align: right; }
        <?php } ?>
    </style>
    <div class="modal-dialog" role="document"<?= $Settings->rtl ? ' dir="rtl"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <?php
            } else {
                ?><!doctype html>
                <html<?= $Settings->rtl ? ' dir="rtl"' : ''; ?>>
                <head>
                    <meta charset="utf-8">
                    <title><?= $page_title . " " . lang("no") . " " . $inv->id; ?></title>
                    <base href="<?= base_url() ?>"/>
                    <meta http-equiv="cache-control" content="max-age=0"/>
                    <meta http-equiv="cache-control" content="no-cache"/>
                    <meta http-equiv="expires" content="0"/>
                    <meta http-equiv="pragma" content="no-cache"/>
                    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
                    <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
                    <style type="text/css" media="all">
                        body { color: #000; }
                        #wrapper { max-width: 500px; margin: 0 auto; padding-top: 20px; font-size: 28px; }
                        .btn { margin-bottom: 5px; }
                        .table { border-radius: 3px; }
                        .table th { background: #f5f5f5; }
                        .table th, .table td { vertical-align: middle !important; }
                        h3 { margin: 5px 0; font-size: 36px; }

                        @media print {
                            .no-print { display: none; }
                            #wrapper { max-width: 500px; width: 100%; min-width: 250px; margin: 0 auto; }
                        }

                        <?php if($Settings->rtl) { ?>
                            .text-right { text-align: left; }
                            .text-left { text-align: right; }
                            tfoot tr th:first-child { text-align: left; }
                        <?php } else { ?>
                            tfoot tr th:first-child { text-align: right; }
                        <?php } ?>
                    </style>
                </head>
                <body>
                    <?php
                }
                ?>
                <div id="wrapper">
                    <div id="receiptData" style="width: auto; max-width: 580px; min-width: 250px; margin: 0 auto;">
                        <div class="no-print">
                            <?php if ($message) { ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= is_array($message) ? print_r($message, true) : $message; ?>
                            </div>
                            <?php } ?>
                        </div>
                        <div id="receipt-data" style="background:#fff;">
                            <div>
                                <div style="text-align:center;">
                                    <?php
                                    if ($store) {
                                        echo '<img src="'.base_url('uploads/'.$store->logo).'" alt="'.$store->name.'">';
                                        echo '<p style="text-align:center;">';
                                        echo '<h3><strong>'.$store->name.'</strong></h3>';
                                        echo $store->address1.'<br>'.$store->address2;
                                        echo $store->city.'<br>'.$store->phone;
                                        echo '</p>';
                                        echo '<p>'.nl2br($store->receipt_header).'</p>';
                                    }
                                    ?>
                                </div>
                                <p>
                                    <?= lang("customer").': '. $inv->customer_name; ?> <br>
                                    <?= lang('sale_no_ref').': '.$inv->id; ?><br>
                                    <?= lang("sales_person").': '. $created_by->first_name." ".$created_by->last_name; ?> <br>
                                    <?= lang("date").': '.$this->tec->hrld($inv->date); ?> <br>
                                </p>
                                <div style="clear:both;"></div>
                                <table class="table table-striped table-condensed">
                                    <tbody>
                                        <?php
                                        $tax_summary = array();
                                        $r = 1;
                                        foreach ($rows as $row) {
                                            echo '<tr><td colspan="2">#'.$r.' '. $row->product_name .'</td></tr>';
                                            echo '<tr><td>' . $this->tec->formatQuantity($row->quantity) .' x '. $this->tec->formatMoney($row->net_unit_price + ($row->item_tax / $row->quantity)) . '</td>';
                                            echo '<td class="text-right">' . $this->tec->formatMoney($row->subtotal) . '</td></tr>';
                                            $r++;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-left" style="border-top:1px dashed #000;"><?= lang("total"); ?></th>
                                            <th class="text-right" style="border-top:1px dashed #000;"><?= $this->tec->formatMoney($inv->total + $inv->product_tax); ?></th>
                                        </tr>
                                        <?php
                                        if ($inv->order_tax != 0) {
                                            echo '<tr><th class="text-left">' . lang("order_tax") . '</th><th class="text-right">' . $this->tec->formatMoney($inv->order_tax) . '</th></tr>';
                                        }
                                        if ($inv->total_discount != 0) {
                                            echo '<tr><th class="text-left">' . lang("order_discount") . '</th><th class="text-right">' . $this->tec->formatMoney($inv->total_discount) . '</th></tr>';
                                        }

                                        if ($Settings->rounding) {
                                            $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
                                            $rounding = $this->tec->formatDecimal($round_total - $inv->grand_total);
                                            ?>
                                            <tr>
                                                <th class="text-left"><?= lang("rounding"); ?></th>
                                                <th class="text-right"><?= $this->tec->formatMoney($rounding); ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-left"><?= lang("grand_total"); ?></th>
                                                <th class="text-right"><?= $this->tec->formatMoney($inv->grand_total + $rounding); ?></th>
                                            </tr>
                                            <?php
                                        } else {
                                            $round_total = $inv->grand_total;
                                            ?>
                                            <tr>
                                                <th class="text-left"><?= lang("grand_total"); ?></th>
                                                <th class="text-right"><?= $this->tec->formatMoney($inv->grand_total); ?></th>
                                            </tr>
                                            <?php
                                        }
                                        if ($inv->paid < $round_total) { ?>
                                        <tr>
                                            <th class="text-left"><?= lang("paid_amount"); ?></th>
                                            <th class="text-right"><?= $this->tec->formatMoney($inv->paid); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-left"><?= lang("due_amount"); ?></th>
                                            <th class="text-right"><?= $this->tec->formatMoney($inv->grand_total - $inv->paid); ?></th>
                                        </tr>
                                        <?php } ?>
                                    </tfoot>
                                </table>
                                <?php
                                if ($payments) {
                                    echo '<table class="table table-striped table-condensed" style="margin-top:10px;"><tbody>';
                                    foreach ($payments as $payment) {
                                        echo '<tr>';
                                        if ($payment->paid_by == 'cash' && $payment->pos_paid) {
                                            echo '<td style="padding-left:15px;">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                                        }
                                        if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                                            echo '<td style="padding-left:15px;">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                                        }
                                        if ($payment->paid_by == 'Cheque' || $payment->paid_by == 'cheque' && $payment->cheque_no) {
                                            echo '<td style="padding-left:15px;">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                                        }
                                        if ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                                            echo '<td style="padding-left:15px;">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("no") . ': ' . $payment->gc_no . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</td>';
                                        }
                                        if ($payment->paid_by == 'other' && $payment->amount) {
                                            echo '<td style="padding-left:15px;">' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                                            echo '<td style="padding-left:15px;">' . lang("amount") . ': ' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                                            echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                                        }
                                        echo '</tr>';
                                    }
                                    echo '</tbody></table>';
                                }

                                ?>

                                <?= $inv->note ? '<p style="margin-top:10px; text-align: center;">' . $this->tec->decode_html($inv->note) . '</p>' : ''; ?>
                                <?php if (!empty($store->receipt_footer)) { ?>
                                <div class="well well-sm"  style="margin-top:10px;">
                                    <div style="text-align: center;"><?= nl2br($store->receipt_footer); ?></div>
                                </div>
                                <?php } ?>
                            </div>
                            <div style="clear:both;"></div>
                        </div>

                        <!-- start -->
                        <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
                            <hr>
                            <?php if ($modal) { ?>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group">
                                    <?php
                                    if ( ! $Settings->remote_printing) {
                                        echo '<button data-receipt="'.$inv->id.'" onclick="return print_receipt(0)" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    } elseif ($Settings->remote_printing == 1) {
                                        echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    } else {
                                        echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    }
                                    ?>
                                </div>
                                <div class="btn-group" role="group">
                                    <a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                                </div>
                            </div>
                            <?php } else { ?>
                            <span class="pull-right col-xs-12">
                                <?php
                                if ( ! $Settings->remote_printing) {
                                    echo '<button data-receipt="'.$inv->id.'" onclick="return print_receipt(1)" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    echo '<a href="'.site_url('pos/open_drawer/').'" class="btn btn-block btn-default">'.lang("open_cash_drawer").'</a>';
                                } elseif ($Settings->remote_printing == 1) {
                                    echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                } else {
                                    echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">'.lang("open_cash_drawer").'</button>';
                                }
                                ?>
                            </span>
                            <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>
                            <span class="col-xs-12">
                                <a class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
                            </span>
                            <?php } ?>
                            <div style="clear:both;"></div>
                        </div>
                        <!-- end -->
                    </div>
                </div>
                <!-- start -->
                <?php
                if (!$modal) {
                    ?>
                    <script type="text/javascript">
                        var base_url = '<?=base_url();?>';
                        var site_url = '<?=site_url();?>';
                        var dateformat = '<?=$Settings->dateformat;?>', timeformat = '<?= $Settings->timeformat ?>';
                        <?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
                        var Settings = <?= json_encode($Settings); ?>;
                    </script>
                    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
                    <script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
                    <script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
                    <?php
                }
                ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#email').click(function () {
                            bootbox.prompt({
                                title: "<?= lang("email_address"); ?>",
                                inputType: 'email',
                                value: "<?= $customer->email; ?>",
                                callback: function (email) {
                                    if (email != null) {
                                        $.ajax({
                                            type: "post",
                                            url: "<?= site_url('pos/email_receipt') ?>",
                                            data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv->id; ?>},
                                            dataType: "json",
                                            success: function (data) {
                                                bootbox.alert({message: data.msg, size: 'small'});
                                            },
                                            error: function () {
                                                bootbox.alert({message: '<?= lang('ajax_request_failed'); ?>', size: 'small'});
                                                return false;
                                            }
                                        });
                                    }
                                }
                            });
                            return false;
                        });
                    });
                </script>
                <?php /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */ ?>
                <?php include 'remote_printing.php'; ?>
                <?php
                if ($modal) {
                    ?>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
    <!-- end -->
    </body>
    </html>
    <?php
}
?>
