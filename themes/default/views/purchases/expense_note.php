<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header modal-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
            <h4 class="modal-title" id="myModalLabel">
                <?= lang('expense').' # '.$expense->id; ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                <div class="well" style="margin-bottom:0;">
                        <div class="table-responsive">
                            <table class="table table-borderless" style="margin-bottom:0;">
                                <tbody>
                                    <tr>
                                        <td><strong><?= lang("date"); ?></strong></td>
                                        <td><strong class="text-right"><?php echo $this->tec->hrld($expense->date); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= lang("reference"); ?></strong></td>
                                        <td><strong class="text-right"><?php echo $expense->reference; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= lang("amount"); ?></strong></td>
                                        <td><strong class="text-right"><?php echo $this->tec->formatMoney($expense->amount); ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?php echo $expense->note; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


