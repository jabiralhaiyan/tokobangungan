<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<?php
if ($Settings->remote_printing == 2) {
    if ($Settings->rounding) {
        $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
        $rounding = $this->tec->formatDecimal($round_total - $inv->grand_total);
    }
    ?>

    <script type="text/javascript">
        function receiptData() {

            receipt = {};
            receipt.store_name = "<?= printText($store->name, $printer->char_per_line);?>\n";

            receipt.header = "";
            receipt.header += "<?= printText($store->name.' ('.$store->code.')', $printer->char_per_line);?>\n";
            <?php
            if ($store->address1) { ?>
                receipt.header += "<?= printText($store->address1, $printer->char_per_line);?>\n";
                <?php
            }
            if ($store->address2) { ?>
                receipt.header += "<?= printText($store->address2, $printer->char_per_line);?>\n";
                <?php
            }
            if ($store->city) { ?>
                receipt.header += "<?= printText($store->city, $printer->char_per_line);?>\n";
                <?php
            } ?>
            receipt.header += "<?= printText(lang('tel').': '.$store->phone, $printer->char_per_line);?>\n\n";
            receipt.header += "<?= printText(str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), $store->receipt_header), $printer->char_per_line);?>\n\n";

            receipt.info = "";
            receipt.info += "<?= lang("date") . ": " . $this->tec->hrld($inv->date); ?>" + "\n";
            receipt.info += "<?= lang("sale_no_ref") . ": " . $inv->id; ?>" + "\n";
            receipt.info += "<?= lang("customer") . ": " . $inv->customer_name; ?>" + "\n";
            receipt.info += "<?= lang("sales_person") . ": " . $created_by->first_name." ".$created_by->last_name; ?>" + "\n\n";

            receipt.items = "";
            receipt.items += "<?php $r = 1; foreach ($rows as $row): ?>";
            receipt.items += "<?= "#" . $r ." "; ?>";
            receipt.items += "<?= product_name(addslashes($row->product_name), $printer->char_per_line); ?>" + "\n";
            receipt.items += "<?= printLine("   ".$this->tec->formatQuantity($row->quantity)." x ".$this->tec->formatMoney($row->net_unit_price+($row->item_tax/$row->quantity)) . ":  ". $this->tec->formatMoney($row->subtotal), $printer->char_per_line, ' ') . ""; ?>" + "\n";
            receipt.items += "<?php $r++; endforeach; ?>";

            receipt.totals = "";
            receipt.totals += "<?= printLine(lang("total") . ": " . $this->tec->formatMoney($inv->total+$inv->product_tax), $printer->char_per_line); ?>" + "\n";
            <?php
            if ($inv->order_tax != 0) { ?>
                receipt.totals += "<?= printLine(lang("tax") . ": " . $this->tec->formatMoney($inv->order_tax), $printer->char_per_line); ?>" + "\n";
                <?php
            }
            if ($inv->total_discount != 0) { ?>
                receipt.totals += "<?= printLine(lang("discount") . ": " . $this->tec->formatMoney($inv->total_discount), $printer->char_per_line); ?>" + "\n";
                <?php
            }
            if ($Settings->rounding) { ?>
                receipt.totals += "<?= printLine(lang("rounding") . ": " . $this->tec->formatMoney($rounding), $printer->char_per_line); ?>" + "\n";
                receipt.totals += "<?= printLine(lang("grand_total") . ": " . $this->tec->formatMoney($inv->grand_total + $rounding), $printer->char_per_line); ?>" + "\n";
                <?php
            } else { ?>
                receipt.totals += "<?= printLine(lang("grand_total") . ": " . $this->tec->formatMoney($inv->grand_total), $printer->char_per_line); ?>" + "\n";
                <?php
            }
            if ($inv->paid < $inv->grand_total) { ?>
                receipt.totals += "<?= printLine(lang("paid_amount") . ": " . $this->tec->formatMoney($inv->paid), $printer->char_per_line); ?>" + "\n";
                receipt.totals += "<?= printLine(lang("due_amount") . ": " . $this->tec->formatMoney($inv->grand_total-$inv->paid), $printer->char_per_line); ?>" + "\n\n";
                <?php
            } ?>

            <?php
            if ($payments) {
                ?>
                receipt.payments = '';
                <?php
                foreach($payments as $payment) {
                    if ($payment->paid_by == 'cash' && $payment->pos_paid) { ?>
                        receipt.payments += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("change") . ": " . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0), $printer->char_per_line); ?>" + "\n";
                        <?php
                    } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) { ?>
                        receipt.payments += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("card_no") . ": xxxx xxxx xxxx " . substr($payment->cc_no, -4), $printer->char_per_line); ?>" + "\n";
                        <?php
                    } elseif ($payment->paid_by == 'gift_card') { ?>
                        receipt.payments += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("card_no") . ": " . $payment->gc_no, $printer->char_per_line); ?>" + "\n";
                        <?php
                    } elseif (($payment->paid_by == 'cheque' || $payment->paid_by == 'Cheque') && $payment->cheque_no) { ?>
                        receipt.payments += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->pos_paid), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("cheque_no") . ": " . $payment->cheque_no, $printer->char_per_line); ?>" + "\n";
                        <?php
                    } elseif ($payment->paid_by == 'other' && $payment->amount) { ?>
                        receipt.payments += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoney($payment->amount), $printer->char_per_line); ?>" + "\n";
                        receipt.payments += "<?= printText(lang("payment_note") . ": " . $payment->note, $printer->char_per_line); ?>" + "\n";
                        <?php
                    }
                }
            }
            ?>

            receipt.footer = "";
            <?php
            if ($inv->note) { ?>
                receipt.footer += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $this->tec->decode_html($inv->note))), $printer->char_per_line); ?>" + "\r\n\r\n";
                <?php
            } ?>
            receipt.footer += "<?= printText(str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), $store->receipt_footer), $printer->char_per_line);?>\r\n\r\n";

            return receipt;
        }

    </script>

    <?php
    if ($Settings->print_img) { ?>
    <script src="<?= $assets ?>dist/js/htmlimg.js"></script>
    <?php
    } ?>

    <script type="text/javascript">
        var socket = null;
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

        function openCashDrawer() {
            var ocddata = {
                'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>
            };

            if (socket.readyState == 1) {
                socket.send(JSON.stringify({
                    type: 'open-cashdrawer',
                    data: ocddata
                }));
                return false;
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }

        function printReceipt() {
            if (socket.readyState == 1) {
                <?php
                if ($Settings->print_img) { ?>
                    var element = $('#receipt-data').get(0);
                    html2canvas(element, {
                        scrollY: 0,
                        useCORS: true,
                    }).then(function(canvas) {
                        var dataURL = canvas.toDataURL();
                        var socket_data = {
                            'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                            'text': dataURL,
                            'cash_drawer': <?= isset($modal) ? 0 : 1; ?>, 'drawer_code': '<?= $Settings->cash_drawer_codes; ?>'
                        };
                        socket.send(JSON.stringify({
                            type: 'print-img',
                            data: socket_data
                        }));
                        // return Canvas2Image.saveAsPNG(canvas);
                    });
                    return false;
                    <?php
                } else { ?>
                    var receipt_data = receiptData();
                    var socket_data = {
                        'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>,
                        'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>',
                        'text': receipt_data,
                        'cash_drawer': <?= isset($modal) ? 0 : 1; ?>, 'drawer_code': '<?= $Settings->cash_drawer_codes; ?>'
                    };
                    socket.send(JSON.stringify({
                        type: 'print-receipt',
                        data: socket_data
                    }));
                    return false;
                    <?php
                } ?>
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }
        <?php
        if ($Settings->auto_print && (!isset($modal) || empty($modal))) {
            ?>
            $(document).ready(function() {
                setTimeout(printReceipt, 1000);
            });
            <?php
        }
        ?>

    </script>
    <?php
} else {
?>
<?php
    if ( ! $Settings->remote_printing) { ?>
    <?php
    if ($Settings->print_img) { ?>
    <script src="<?= $assets ?>dist/js/htmlimg.js"></script>
    <?php
    } ?>
    <script>
        function print_receipt(cd) {
            <?php
            if ($Settings->print_img) { ?>
                var element = $('#receipt-data').get(0);
                // var width = element.clientWidth;
                // var height = element.clientHeight;
                html2canvas(element, {
                    scrollY: 0,
                    useCORS: true,
                }).then(function(canvas) {
                    var img = canvas.toDataURL().split(',')[1];
                    $.post('<?= site_url('pos/receipt_img'); ?>', {img: img, cd: cd, <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>'});
                    // return Canvas2Image.saveAsPNG(canvas);
                });
                return false;
                <?php
            } else { ?>
                var rid = $(this).attr('data-receipt');
                $.get('<?= site_url('pos/print_receipt'); ?>/'+rid+'/'+cd);
                return false;
                <?php
            } ?>
        }
    </script>
    <?php
    } ?>
<?php
}
?>
