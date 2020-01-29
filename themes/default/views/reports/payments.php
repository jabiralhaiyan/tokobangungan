<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<?php
$v = "?v=1";

if ($this->input->post('payment_ref')) {
    $v .= "&payment_ref=" . $this->input->post('payment_ref');
}
if ($this->input->post('sale_no')) {
    $v .= "&sale_no=" . $this->input->post('sale_no');
}
if ($this->input->post('customer')) {
    $v .= "&customer=" . $this->input->post('customer');
}
if ($this->input->post('paid_by')) {
    $v .= "&paid_by=" . $this->input->post('paid_by');
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
?>

<script type="text/javascript">
    $(document).ready(function() {

        var pb = ['<?=lang('cash')?>', '<?=lang('CC')?>', '<?=lang('Cheque')?>', '<?=lang('stripe')?>', '<?=lang('gift_card')?>'];

        function paid_by(x) {
            if (x == 'cash') {
                return pb[0];
            } else if (x == 'CC') {
                return pb[1];
            } else if (x == 'Cheque') {
                return pb[2];
            } else if (x == 'stripe') {
                return pb[3];
            } else if (x == 'gift_card') {
                return pb[4];
            } else {
                return x;
            }
        }

        var table = $('#PayRData').DataTable({

            'ajax' : { url: '<?=site_url('reports/get_payments/'. $v);?>', type: 'POST', "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
            }},
            "buttons": [
            { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
            { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
            { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
            { extend: 'colvis', text: 'Columns'},
            ],
            "columns": [
            { "data": "id", "visible": false },
            { "data": "date", "render": hrld },
            { "data": "ref" },
            { "data": "sale_no" },
            { "data": "paid_by", "render": paid_by },
            { "data": "amount", "render": currencyFormat }
            ],
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
                $(api.column(5).footer()).html( cf(api.column(5).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
            }

        });

        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

        table.columns().every(function () {
            var self = this;
            $( 'input.datepicker', this.footer() ).on('dp.change', function (e) {
                self.search( this.value ).draw();
            });
            $( 'input:not(.datepicker)', this.footer() ).on('keyup change', function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (((code == 13 && self.search() !== this.value) || (self.search() !== '' && this.value === ''))) {
                    self.search( this.value ).draw();
                }
            });
            $( 'select', this.footer() ).on('change', function (e) {
                self.search( this.value ).draw();
            });
        });

    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('#form').hide();
        $('.toggle_form').click(function(){
            $("#form").slideToggle();
            return false;
        });
    });
</script>
<style type="text/css">
    .table td:nth-child(3) { text-align: center; }
</style>
<section class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header">
                    <a href="#" class="btn btn-default btn-sm toggle_form pull-right"><?= lang("show_hide"); ?></a>
                    <h3 class="box-title"><?= lang('customize_report'); ?><?php
                        if ($this->input->post('start_date')) {
                            echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
                        }
                        ?></h3>
                    </div>
                    <div class="box-body">
                        <div id="form" class="panel panel-warning">
                            <div class="panel-body">

                                <?= form_open("reports/payments"); ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <?= lang("payment_ref", "payment_ref"); ?>
                                            <?= form_input('payment_ref', (isset($_POST['payment_ref']) ? $_POST['payment_ref'] : ""), 'class="form-control tip" id="payment_ref"'); ?>

                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <?= lang("sale_no", "sale_no"); ?>
                                            <?= form_input('sale_no', (isset($_POST['sale_no']) ? $_POST['sale_no'] : ""), 'class="form-control tip" id="sale_no"'); ?>

                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="customer"><?= lang("customer"); ?></label>
                                            <?php
                                            $cu[0] = lang("select")." ".lang("customer");
                                            foreach($customers as $customer){
                                                $cu[$customer->id] = $customer->name;
                                            }
                                            echo form_dropdown('customer', $cu, set_value('customer'), 'class="form-control select2" style="width:100%" id="customer"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="user"><?= lang("created_by"); ?></label>
                                            <?php
                                            $us[""] = "";
                                            foreach ($users as $user) {
                                                $us[$user->id] = $user->first_name . " " . $user->last_name;
                                            }
                                            echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control select2" id="user" data-placeholder="' . lang("select") . " " . lang("user") . '" style="width:100%;"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <?= lang("paid_by", "paid_by"); ?>
                                            <select name="paid_by" id="paid_by" class="form-control paid_by select2" style="width:100%"
                                            required="required">
                                            <option value="cash"><?= lang("cash"); ?></option>
                                            <option value="CC"><?= lang("cc"); ?></option>
                                            <option value="Cheque"><?= lang("cheque"); ?></option>
                                            <option value="gift_card"><?= lang("gift_card"); ?></option>
                                            <?= isset($Settings->stripe) ? '<option value="stripe">' . lang("stripe") . '</option>' : ''; ?>
                                            <option value="other"><?= lang("other"); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang("start_date", "start_date"); ?>
                                        <?= form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control datetimepicker" id="start_date"'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <?= lang("end_date", "end_date"); ?>
                                        <?= form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control datetimepicker" id="end_date"'); ?>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary"><?= lang("submit"); ?></button>
                                </div>
                            </div>
                            <?= form_close(); ?>

                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="table-responsive">
                        <table id="PayRData" class="table table-bordered table-hover table-striped table-condensed reports-table">
                            <thead>
                                <tr>
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-3"><?= lang("date"); ?></th>
                                    <th class="col-xs-3"><?= lang("payment_ref"); ?></th>
                                    <th class="col-xs-2"><?= lang("sale_no"); ?></th>
                                    <th class="col-xs-2"><?= lang("paid_by"); ?></th>
                                    <th class="col-xs-2"><?= lang("amount"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                    <th class="col-sm-3"><span class="datepickercon"><input type="text" class="text_filter datepicker" placeholder="[<?= lang('date'); ?>]"></span></th>
                                    <th class="col-sm-3"><input type="text" class="text_filter" placeholder="[<?= lang('payment_ref'); ?>]"></th>
                                    <th class="col-xs-2"><?= lang("sale_no"); ?></th>
                                    <th class="col-xs-2"><?= lang("paid_by"); ?></th>
                                    <th class="col-xs-2"><?= lang("amount"); ?></th>
                                </tr>
                                <tr>
                                    <td colspan="6" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php if ($this->input->post('customer')) { ?>
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn bg-purple btn-lg btn-block" style="cursor:default;">
                                <strong><?= $this->tec->formatMoney($total_sales->number, 0); ?></strong>
                                <?= lang("sales"); ?>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary btn-lg btn-block" style="cursor:default;">
                                <strong><?= $this->tec->formatMoney($total_sales->amount); ?></strong>
                                <?= lang("amount"); ?>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success btn-lg btn-block" style="cursor:default;">
                                <strong><?= $this->tec->formatMoney($total_sales->paid); ?></strong>
                                <?= lang("paid"); ?>
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-warning btn-lg btn-block" style="cursor:default;">
                                <strong><?= $this->tec->formatMoney($total_sales->amount-$total_sales->paid); ?></strong>
                                <?= lang("due"); ?>
                            </button>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</section>

<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('.datepicker').datetimepicker({format: 'YYYY-MM-DD', showClear: true, showClose: true, useCurrent: false, widgetPositioning: {horizontal: 'auto', vertical: 'bottom'}, widgetParent: $('.dataTable tfoot')});
    });
</script>
