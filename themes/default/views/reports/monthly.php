<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<style>
.table td { width: 8.333%; }
.table tr:first-child td { text-align:center; }
.table tr:last-child td { text-align:right; }
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <p><?= lang('review_report'); ?></p>
                </div>
                <div class="box-body">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?= lang('sales_value'); ?></span>
                                        <span class="info-box-number"><?= $this->tec->formatMoney($total_sales->total_amount) ?></span>
                                        <div class="progress">
                                            <div style="width: 100%" class="progress-bar"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $total_sales->total .' ' . lang('sales'); ?> |
                                            <?= $this->tec->formatMoney($total_sales->paid) . ' ' . lang('received') ?> |
                                            <?= $this->tec->formatMoney($total_sales->tax) . ' ' . lang('tax') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-plus"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?= lang('purchases_value'); ?></span>
                                        <span class="info-box-number"><?= $this->tec->formatMoney($total_purchases->total_amount) ?></span>
                                        <div class="progress">
                                            <div style="width: 0%" class="progress-bar"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $total_purchases->total ?> <?= lang('purchases'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-circle-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?= lang('expenses_value'); ?></span>
                                        <span class="info-box-number"><?= $this->tec->formatMoney($total_expenses->total_amount) ?></span>
                                        <div class="progress">
                                            <div style="width: 0%" class="progress-bar"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $total_expenses->total ?> <?= lang('expenses'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-dollar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?= lang('profit_loss'); ?></span>
                                        <span class="info-box-number"><?= $this->tec->formatMoney($total_sales->total_amount-$total_purchases->total_amount-$total_expenses->total_amount) ?></span>
                                        <div class="progress">
                                            <div style="width: 100%" class="progress-bar"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $total_sales->total_amount.' - '.($total_purchases->total_amount ? $total_purchases->total_amount : 0).' - '.($total_expenses->total_amount ? $total_expenses->total_amount : 0); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="calendar table-responsive">

                            <table class="table table-bordered" style="min-width:522px;">
                                <thead>
                                    <tr class="active">
                                        <th><div class="text-center"> <a href="<?= site_url('reports/monthly_sales/'.($year-1)); ?>">&lt;&lt;</a> </div></th>
                                        <th colspan="10"><div class="text-center"> <?php echo $year; ?> </div></td>
                                            <th><div class="text-center"> <a href="<?= site_url('reports/monthly_sales/'.($year+1)); ?>">&gt;&gt;</a> </div></th>
                                        </th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td><?= lang('cal_january'); ?></td>
                                    <td><?= lang('cal_february'); ?></td>
                                    <td><?= lang('cal_march'); ?></td>
                                    <td><?= lang('cal_april'); ?></td>
                                    <td><?= lang('cal_may'); ?></td>
                                    <td><?= lang('cal_june'); ?></td>
                                    <td><?= lang('cal_july'); ?></td>
                                    <td><?= lang('cal_august'); ?></td>
                                    <td><?= lang('cal_september'); ?></td>
                                    <td><?= lang('cal_october'); ?></td>
                                    <td><?= lang('cal_november'); ?></td>
                                    <td><?= lang('cal_december'); ?></td>
                                </tr>
                                <tr>
                                    <?php
                                    if (!empty($sales)) {

                                        foreach ($sales as $value) {
                                            $value->date = intval($value->date);
                                            $array[$value->date] = "<table class='table table-condensed table-striped table-calendar' style='margin-bottom:0;'><tr><td style='text-align:left;font-weight:bold;'>".lang('total').

                                            "<br><span style='float:right;font-weight:bold;'>{$this->tec->formatMoney($value->total)}</span></td></tr><tr><td style='text-align:left;font-weight:bold;'><span style='font-weight:normal;'>".lang('product_tax').
                                            "<br><span style='float:right;'>{$this->tec->formatMoney($value->product_tax)}</span><br>".lang('order_tax').
                                            "<br><span style='float:right;'>{$this->tec->formatMoney($value->order_tax)}</span></span><br><span style='font-weight:bold;'>".lang('tax')."</span>".
                                            "<br><span style='float:right;font-weight:bold;'>{$this->tec->formatMoney($value->tax)}</span></td></tr><tr><td style='text-align:left;font-weight:bold;' class='violet'>".lang('discount').
                                            "<br><span style='float:right;font-weight:bold;'>{$this->tec->formatMoney($value->discount)}</span></td></tr><tr><td style='text-align:left;font-weight:bold;' class='violet'>".lang('grand_total').
                                            "<br><span style='float:right;font-weight:bold;' class='violet'>{$this->tec->formatMoney($value->grand_total)}</span></td></tr><tr><td style='text-align:left;font-weight:bold;' class='green'>".lang('paid').
                                            "<br><span style='float:right;font-weight:bold;' class='green'>{$this->tec->formatMoney($value->paid)}</span></td></tr><tr><td style='text-align:left;font-weight:bold;' class='orange'>".lang('balance').
                                            "<br><span style='float:right;font-weight:bold;' class='orange'>{$this->tec->formatMoney($value->grand_total - $value->paid)}</span></td></tr></table>";
                                        }

                                        for ($i = 1; $i <= 12; $i++) {
                                            echo "<td>";
                                            if (isset($array[$i])) {
                                                echo $array[$i];
                                            }
                                            echo "</td>";
                                        }

                                    } else {
                                        for ($i=1; $i<=12; $i++) {
                                            echo "<td>&nbsp;</td>";
                                        }
                                    }
                                    ?>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
