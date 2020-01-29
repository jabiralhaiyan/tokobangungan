<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
*  ==============================================================================
 *  Author  : Mian Saleem
 *  Email   : saleem@tecdiary.com
 *  For     : ESC/POS Print Driver for PHP
 *  License : MIT License
 *  ==============================================================================
 */

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Escpos
{

    public $printer;
    public $char_per_line;

    public function __construct() {
        $this->load->helper('text');
        $this->char_per_line = get_printer_chars_per_line();
    }

    public function __get($var) {
        return get_instance()->$var;
    }

    function load($printer) {

        if ($printer->type == 'network') {
            set_time_limit(30);
            $connector = new NetworkPrintConnector($printer->ip_address, $printer->port);
        } elseif ($printer->type == 'linux') {
            $connector = new FilePrintConnector($printer->path);
        } else {
            $connector = new WindowsPrintConnector($printer->path);
        }

        $this->char_per_line = $printer->char_per_line;
        $profile = CapabilityProfile::load($printer->profile);
        $this->printer = new Printer($connector, $profile);

    }

    function print_img($img, $cd = false) {

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $img = EscposImage::load(FCPATH.'files'.DIRECTORY_SEPARATOR.'receipts'.DIRECTORY_SEPARATOR.$img, false);
        $this->printer->bitImageColumnFormat($img);
        $this->printer->feed(2);
        $this->printer->cut();
        if ($cd) {
            $this->printer->pulse();
        }
        $this->printer->close();

    }

    function print_data($data) {

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        if (isset($data->logo) && !empty($data->logo)) {
            $logo = EscposImage::load(FCPATH.'uploads'.DIRECTORY_SEPARATOR.$data->logo, false);
            $this->printer->bitImage($logo);
        }

        if (isset($data->heading) && !empty($data->heading)) {
            $this->printer->setEmphasis(true);
            $this->printer->setTextSize(2, 2);
            $this->printer->text($data->heading . "\n");
            $this->printer->setEmphasis(false);
            $this->printer->setTextSize(1, 1);
            $this->printer->feed();
        }
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);

        if (isset($data->info) && !empty($data->info)) {
            foreach ($data->info as $info) {
                $this->printer->text($info->label.': '.$info->value. "\n");
            }
            $this->printer->feed();
        }

        if (isset($data->items) && !empty($data->items)) {
            $r = 1;
            foreach ($data->items as $item) {
                $this->printer->text('#'.$r.' '.$this->product_name(addslashes($item->product_name)). "\n");
                $this->printer->text($this->printLine('   '.$item->quantity . " x " . $item->unit_price . ":  " . $item->subtotal). "\n");
                $r++;
            }
            $this->printer->feed();
        }

        if (isset($data->totals) && !empty($data->totals)) {
            foreach ($data->totals as $total) {
                if ($total->label == 'line') {
                    $this->printer->text($this->drawLine());
                } else {
                    $this->printer->text($this->printLine($total->label.': '.$total->value). "\n");
                }
            }
            $this->printer->feed();
        }

        if (isset($data->footer) && !empty($data->footer)) {
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->feed(2);
            $this->printer->text($data->footer . "\n");
            $this->printer->feed();
        }

        $this->printer->feed();
        $this->printer->cut();
        $this->printer->close();
    }

    function print_receipt($store, $sale, $items, $payments, $created_by, $open_drawer = false, $bill = false) {

        try {
            if ($open_drawer) {
                $this->printer->pulse();
                sleep(1);
            }

            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            if (isset($store->logo) && !empty($store->logo)) {
                $logo = EscposImage::load(FCPATH.'uploads'.DIRECTORY_SEPARATOR.$store->logo, false);
                $this->printer->bitImage($logo);
                $this->printer->feed();
            }
            $this->printer->setEmphasis(true);
            $this->printer->setTextSize(2, 2);
            $this->printer->text($store->name . "\n");
            $this->printer->setEmphasis(false);
            $this->printer->setTextSize(1, 1);
            $this->printer->text($store->address1 . "\n");
            $this->printer->text($store->address2 . "\n");
            $this->printer->text($store->city . "\n");
            $this->printer->text(lang('tel').': '.$store->phone . "\n");
            $this->printer->feed();
            $this->printer->text($store->receipt_header . "\n");
            $this->printer->feed();
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text(lang('date') . ': '.$this->tec->hrld($sale->date) . "\n");
            $this->printer->text(lang("sale_no_ref") . ": " . ($bill ? $sale->hold_ref :$sale->id) . "\n");
            $this->printer->text(lang("customer") . ": " . $sale->customer_name . "\n");
            $this->printer->text(lang("sales_person") . ": " . $created_by->first_name." ".$created_by->last_name . "\n");
            $this->printer->feed();

            $r = 1;
            foreach ($items as $item) {
                $this->printer->text('#'.$r.' '.$this->product_name(addslashes($item->product_name)) . "\n");
                $this->printer->text($this->printLine('   '.$this->tec->formatQuantity($item->quantity) . " x " . $this->tec->formatMoney($item->net_unit_price + ($item->item_tax / $item->quantity)) . ":  " . $this->tec->formatMoney($item->subtotal)) . "\n");
                $r++;
            }

            $this->printer->text($this->drawLine());
            $this->printer->text($this->printLine(lang("total").":".$this->tec->formatMoney($sale->total + $sale->product_tax)) . "\n");
            if ($sale->order_tax != 0) {
                $this->printer->text($this->printLine(lang("order_tax").":".$this->tec->formatMoney($sale->order_tax)) . "\n");
            }
            if ($sale->total_discount != 0) {
                $this->printer->text($this->printLine(lang("order_discount").":".$this->tec->formatMoney($sale->total_discount)) . "\n");
            }
            if ($this->Settings->rounding) {
                $round_total = $this->tec->roundNumber($sale->grand_total, $this->Settings->rounding);
                $rounding = $this->tec->formatMoney($round_total - $sale->grand_total);
                $this->printer->text($this->printLine(lang("rounding").":".$this->tec->formatMoney($rounding)) . "\n");
                $this->printer->text($this->printLine(lang("grand_total").":".$this->tec->formatMoney($sale->grand_total + $rounding)) . "\n");
            }
            else {
                $round_total = $sale->grand_total;
                $this->printer->text($this->printLine(lang("grand_total").":".$this->tec->formatMoney($sale->grand_total)) . "\n");
            }
            if ($sale->paid < $round_total && !$bill) {
                $this->printer->text($this->printLine(lang("paid_amount").":".$this->tec->formatMoney($sale->paid)) . "\n");
                $this->printer->text($this->printLine(lang("due_amount").":".$this->tec->formatMoney($sale->grand_total - $sale->paid)) . "\n");
            }

            if ( ! $bill) {
                $this->printer->text($this->drawLine());
                if($payments) {
                    foreach($payments as $payment) {
                        if ($payment->paid_by == 'cash' && $payment->pos_paid) {
                            $this->printer->text($this->printLine(lang("paid_by").":".lang($payment->paid_by)) . "\n");
                            $this->printer->text($this->printLine(lang("amount").":".$this->tec->formatMoney($payment->pos_paid)) . "\n");
                            $this->printer->text($this->printLine(lang("change").":".($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0)) . "\n");
                        }
                        elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                            $this->printer->text($this->printLine(lang("paid_by").":".lang($payment->paid_by)) . "\n");
                            $this->printer->text($this->printLine(lang("amount").":".$this->tec->formatMoney($payment->pos_paid)) . "\n");
                            $this->printer->text($this->printLine(lang("card_no").":xxxx xxxx xxxx " . substr($payment->cc_no, -4)) . "\n");
                        }
                        elseif ($payment->paid_by == 'gift_card') {
                            $this->printer->text($this->printLine(lang("paid_by").":".lang($payment->paid_by)) . "\n");
                            $this->printer->text($this->printLine(lang("amount").":".$this->tec->formatMoney($payment->pos_paid)) . "\n");
                            $this->printer->text($this->printLine(lang("card_no").":".$payment->gc_no) . "\n");
                        }
                        elseif ($payment->paid_by == 'Cheque' || $payment->paid_by == 'cheque' && $payment->cheque_no) {
                            $this->printer->text($this->printLine(lang("paid_by").":".lang($payment->paid_by)) . "\n");
                            $this->printer->text($this->printLine(lang("amount").":".$this->tec->formatMoney($payment->pos_paid)) . "\n");
                            $this->printer->text($this->printLine(lang("cheque_no").":".$payment->cheque_no) . "\n");
                        }
                        elseif ($payment->paid_by == 'other' && $payment->amount) {
                            $this->printer->text($this->printLine(lang("paid_by").":".lang($payment->paid_by)) . "\n");
                            $this->printer->text($this->printLine(lang("amount").":".$this->tec->formatMoney($payment->amount)) . "\n");
                            $this->printer->text($this->printLine(lang("payment_note").":".$payment->note) . "\n");
                        }
                    }
                }

                $this->printer->setJustification(Printer::JUSTIFY_CENTER);
                $this->printer->feed(2);
                $this->printer->text($store->receipt_footer . "\n");
                $this->printer->feed();
            }
            else {
                $this->printer->setJustification(Printer::JUSTIFY_CENTER);
                $this->printer->feed(2);
                $this->printer->text(lang('bill_note') . "\n");
            }

            $this->printer->feed();
            $this->printer->cut();
            $this->printer->close();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return 'Error: '. $e->getMessage()."\n";
        } finally {
            if ($this->printer) {
                $this->printer->close();
            }
        }
    }

    function open_drawer() {
        $this->printer->pulse();
        $this->printer->close();
    }

    function print_order($store, $sale, $items, $created_by) {
        try {
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            //      $logo = EscposImage::load(FCPATH.'uploads'.DIRECTORY_SEPARATOR.$store->logo, false);
            //      $this->printer->bitImage($logo);
            //      $this->printer->feed();
            $this->printer->setEmphasis(true);
            $this->printer->setTextSize(2, 2);
            $this->printer->text($store->name . "\n");
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            $this->printer->setTextSize(1, 1);
            $this->printer->text($store->name.' ('.$store->code.')' . "\n");
            if (!empty($store->address1)) {
                $this->printer->text($store->address1 . "\n");
            }
            if (!empty($store->address2)) {
                $this->printer->text($store->address2 . "\n");
            }
            if (!empty($store->city)) {
                $this->printer->text($store->city . "\n");
            }
            $this->printer->text(lang('tel').': '.$store->phone . "\n");
            $this->printer->feed();
            $this->printer->text($store->receipt_header . "\n");
            $this->printer->feed();

            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text('C: ' . $sale->customer_name . "\n");
            $this->printer->text('R: ' . $sale->hold_ref . "\n");
            $this->printer->text('U: ' . $created_by->first_name." ".$created_by->last_name . "\n");
            $this->printer->text('T: '.$this->tec->hrld($sale->date) . "\n");
            $this->printer->feed();

            $r = 1;
            foreach ($items as $item) {
                $item->quantity = $item->quantity - ($item->ordered ? $item->ordered : 0);
                $this->printer->text($this->printLine('#'.$r.' '.$this->product_name(addslashes($item->product_name)).' ('.$item->product_code.') : [ ' .
                            ($item->quantity > 0 ? $this->tec->formatQuantity($item->quantity) : 'xxxx')." ]"). "\n");
                if ( ! empty($item->comment)) {
                    $comments = explode(PHP_EOL, $item->comment);
                    foreach ($comments as $cmt) {
                        $this->printer->text(' * '.$cmt. "\n");
                    }
                }
                $this->printer->feed();
                $r++;
            }

            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->feed(2);
            $this->printer->text($sale->note . "\n");

            $this->printer->feed();
            $this->printer->cut();
            $this->printer->close();
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return 'Error: '. $e->getMessage()."\n";
        } finally {
            if ($this->printer) {
                $this->printer->close();
            }
        }
    }

    function product_name($name) {
        return character_limiter($name, ($this->char_per_line - 8));
    }

    function drawLine() {
        $new = '';
        for ($i = 1; $i < $this->char_per_line; $i++) {
            $new .= '-';
        }
        return $new . "\n";
    }

    function printLine($str, $sep = '', $space = null) {
        $size = $space ? $space : $this->char_per_line;
        $lenght = strlen($str);
        list($first, $second) = explode(":", $str, 2);
        $new = $first . $sep;
        for ($i = 1; $i < ($size - $lenght); $i++) {
            $new .= ' ';
        }
        $new .= $sep . $second;
        return $new;
    }

    function printText($text) {
        $new = wordwrap($text, $this->char_per_line, " \\n");
        return $new;
    }

    function taxLine($name, $code, $qty, $amt, $tax) {
        $new = $this->printLine(
            $this->printLine(
                $this->printLine(
                    $this->printLine ($name . ':' . $code, '', 18)
                    . ':' . $qty, '', 25
                    ) . ':' . $amt, '', 35
                ) . ':' . $tax
            );
        return $new;
    }

}
