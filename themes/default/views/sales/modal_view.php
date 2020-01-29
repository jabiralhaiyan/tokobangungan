<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            <div class="no-print">
                <?php if ($message) { ?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <?= is_array($message) ? print_r($message, true) : $message; ?>
                </div>
                <?php } ?>
            </div>
            <div id="receipt-data">
                <div>
                    <div style="text-align:center;">
                        <?php
                        if ($store) {
                            echo '<img src="'.base_url('uploads/'.$store->logo).'" alt="'.$store->name.'">';
                            echo '<p style="text-align:center;">';
                            echo '<strong>'.$store->name.'</strong><br>';
                            echo $store->address1.'<br>'.$store->address2;
                            echo $store->city.'<br>'.$store->phone;
                            echo '</p>';
                            echo '<p>'.nl2br($store->receipt_header).'</p>';
                        }
                        ?>
                    </div>
                    <p>
                        <?= lang("date").': '.$this->tec->hrld($inv->date); ?> <br>
                        <?= lang('sale_no_ref').': '.$inv->id; ?><br>
                        <?= lang("customer").': '. $inv->customer_name; ?> <br>
                        <?= lang("sales_person").': '. $created_by->first_name." ".$created_by->last_name; ?> <br>
                    </p>
                    <div style="clear:both;"></div>
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th style="text-align:center; width: 50%; border-bottom: 2px solid #ddd;"><?=lang('description');?></th>
                                <th style="text-align:center; width: 12%; border-bottom: 2px solid #ddd;"><?=lang('quantity');?></th>
                                <th style="text-align:center; width: 24%; border-bottom: 2px solid #ddd;"><?=lang('price');?></th>
                                <th style="text-align:center; width: 26%; border-bottom: 2px solid #ddd;"><?=lang('subtotal');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tax_summary = array();
                            foreach ($rows as $row) {
                                echo '<tr><td style="text-align:left;">' . $row->product_name;
                                echo '</td>';
                                echo '<td style="text-align:center;">' . $this->tec->formatQuantity($row->quantity) . '</td>';
                                echo '<td style="text-align:right;">';
                                echo $this->tec->formatMoney($row->net_unit_price + ($row->item_tax / $row->quantity)) . '</td><td style="text-align:right;">' . $this->tec->formatMoney($row->subtotal) . '</td></tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align:left;"><?= lang("total"); ?></th>
                                <th colspan="2" style="text-align:right;"><?= $this->tec->formatMoney($inv->total + $inv->product_tax); ?></th>
                            </tr>
                            <?php
                            if ($inv->order_tax != 0) {
                                echo '<tr><th colspan="2" style="text-align:left;">' . lang("order_tax") . '</th><th colspan="2" style="text-align:right;">' . $this->tec->formatMoney($inv->order_tax) . '</th></tr>';
                            }
                            if ($inv->total_discount != 0) {
                                echo '<tr><th colspan="2" style="text-align:left;">' . lang("order_discount") . '</th><th colspan="2" style="text-align:right;">' . $this->tec->formatMoney($inv->total_discount) . '</th></tr>';
                            }

                            if ($Settings->rounding) {
                                $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
                                $rounding = $this->tec->formatMoney($round_total - $inv->grand_total);
                                ?>
                                <tr>
                                    <th colspan="2" style="text-align:left;"><?= lang("rounding"); ?></th>
                                    <th colspan="2" style="text-align:right;"><?= $rounding; ?></th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align:left;"><?= lang("grand_total"); ?></th>
                                    <th colspan="2" style="text-align:right;"><?= $this->tec->formatMoney($inv->grand_total + $rounding); ?></th>
                                </tr>
                                <?php
                            } else {
                                $round_total = $inv->grand_total;
                                ?>
                                <tr>
                                    <th colspan="2" style="text-align:left;"><?= lang("grand_total"); ?></th>
                                    <th colspan="2" style="text-align:right;"><?= $this->tec->formatMoney($inv->grand_total); ?></th>
                                </tr>
                                <?php
                            }
                            if ($inv->paid < $round_total) { ?>
                            <tr>
                                <th colspan="2" style="text-align:left;"><?= lang("paid_amount"); ?></th>
                                <th colspan="2" style="text-align:right;"><?= $this->tec->formatMoney($inv->paid); ?></th>
                            </tr>
                            <tr>
                                <th colspan="2" style="text-align:left;"><?= lang("due_amount"); ?></th>
                                <th colspan="2" style="text-align:right;"><?= $this->tec->formatMoney($inv->grand_total - $inv->paid); ?></th>
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
                            if ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
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
                    <div class="well well-sm"  style="margin-top:10px;">
                        <div style="text-align: center;"><?= nl2br($store->receipt_footer); ?></div>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>

            <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <div class="btn-group" role="group">
                    <?php
                    if ( ! $Settings->remote_printing) {
                        echo '<a href="'.site_url('pos/print_receipt/'.$inv->id.'/1').'" id="print" class="btn btn-block btn-primary">'.lang("print").'</a>';
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

                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#print').click(function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            $.get(link);
            return false;
        });
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

<?php include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; ?>
