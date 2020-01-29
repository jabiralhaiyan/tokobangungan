<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
    $(document).ready(function() {

        function ptype(x) {
            if (x == 'standard') {
                return '<?= lang('standard'); ?>';
            } else if (x == 'combo') {
                return '<?= lang('combo'); ?>';
            } else if (x == 'service') {
                return '<?= lang('service'); ?>';
            } else {
                return x;
            }
        }

        function image(n) {
            if (n !== null) {
                return '<div style="width:32px; margin: 0 auto;"><a href="<?=base_url();?>uploads/'+n+'" class="open-image"><img src="<?=base_url();?>uploads/thumbs/'+n+'" alt="" class="img-responsive"></a></div>';
            }
            return '';
        }

        function method(n) {
            return (n == 0) ? '<span class="label label-primary"><?= lang('inclusive'); ?></span>' : '<span class="label label-warning"><?= lang('exclusive'); ?></span>';
        }

        var table = $('#prTables').DataTable({

            // 'ajax': '<?=site_url('products/get_products/'.$store->id);?>',
            'ajax' : { url: '<?=site_url('products/get_products/'.$store->id);?>', type: 'POST', "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
            }},
            "buttons": [
            { extend: 'copyHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] } },
            { extend: 'excelHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] } },
            { extend: 'csvHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': false,
            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] } },
            { extend: 'colvis', text: 'Columns'},
            ],
            "columns": [
            { "data": "pid", "visible": false },
            { "data": "image", "searchable": false, "orderable": false, "render": image },
            { "data": "code" },
            { "data": "pname" },
            { "data": "type", "render": ptype },
            { "data": "cname" },
            { "data": "quantity", "render": quantityFormat },
            { "data": "tax" },
            { "data": "tax_method", "render": method },
            <?php if ($Admin) { ?>
            { "data": "cost", "render": currencyFormat, "searchable": false },
            <?php } ?>
            { "data": "price", "render": currencyFormat, "searchable": false },
            { "data": "Actions", "searchable": false, "orderable": false }
            ]

        });

        // $('#prTables tfoot th:not(:last-child, :nth-last-child(2), :nth-last-child(3))').each(function () {
        //     var title = $(this).text();
        //     $(this).html( '<input type="text" class="text_filter" placeholder="'+title+'" />' );
        // });

        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

        table.columns().every(function () {
            var self = this;
            $( 'input', this.footer() ).on( 'keyup change', function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (((code == 13 && self.search() !== this.value) || (self.search() !== '' && this.value === ''))) {
                    self.search( this.value ).draw();
                }
            });
            $( 'select', this.footer() ).on( 'change', function (e) {
                self.search( this.value ).draw();
            });
        });

    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#prTables').on('click', '.image', function() {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });
        $('#prTables').on('click', '.barcode', function() {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });
        $('#prTables').on('click', '.open-image', function() {
            var a_href = $(this).attr('href');
            var code = $(this).closest('tr').find('.image').attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });
    });
</script>
<style type="text/css">
    .table td:first-child { padding: 1px; }
    .table td:nth-child(6), .table td:nth-child(7), .table td:nth-child(8) { text-align: center; }
    .table td:nth-child(9)<?= $Admin ? ', .table td:nth-child(10)' : ''; ?> { text-align: right; }
</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <?php if (!$this->session->userdata('has_store_id')) { ?>
                    <div class="dropdown pull-right">
                      <button class="btn btn-primary" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $store->name.' ('.$store->code.')'; ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <?php
                        foreach ($stores as $st) {
                            if ($store->id != $st->id) {
                                echo "<li><a href='".site_url('products/?store_id='.$st->id)."'>{$st->name} ({$st->code})</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php } ?>
                <h3 class="box-title"><?= lang('list_results'); ?></h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="prTables" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                        <thead>
                            <tr class="active">
                                <th style="max-width:30px;"><?= lang("id"); ?></th>
                                <th style="max-width:30px;"><?= lang("image"); ?></th>
                                <th class="col-xs-1"><?= lang("code"); ?></th>
                                <th><?= lang("name"); ?></th>
                                <th class="col-xs-1"><?= lang("type"); ?></th>
                                <th class="col-xs-1"><?= lang("category"); ?></th>
                                <th class="col-xs-1"><?= lang("quantity"); ?></th>
                                <th class="col-xs-1"><?= lang("tax"); ?></th>
                                <th class="col-xs-1"><?= lang("method"); ?></th>
                                <?php if ($Admin) { ?>
                                    <th class="col-xs-1"><?= lang("cost"); ?></th>
                                <?php } ?>
                                <th class="col-xs-1"><?= lang("price"); ?></th>
                                <th style="width:165px;"><?= lang("actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="12" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                <th style="max-width:30px;"><?= lang("image"); ?></th>
                                <th class="col-xs-1"><input type="text" class="text_filter" placeholder="[<?= lang('code'); ?>]"></th>
                                <th><input type="text" class="text_filter" placeholder="[<?= lang('name'); ?>]"></th>
                                <th class="col-xs-1"><input type="text" class="text_filter" placeholder="[<?= lang('type'); ?>]"></th>
                                <th class="col-xs-1"><input type="text" class="text_filter" placeholder="[<?= lang('category'); ?>]"></th>
                                <th class="col-xs-1"><input type="text" class="text_filter" placeholder="[<?= lang('quantity'); ?>]"></th>
                                <th class="col-xs-1"><input type="text" class="text_filter" placeholder="[<?= lang('tax'); ?>]"></th>
                                <th class="col-xs-1">
                                    <select class="select2 select_filter"><option value=""><?= lang("all"); ?></option><option value="0"><?= lang("inclusive"); ?></option><option value="1"><?= lang("exclusive"); ?></option></select>
                                </th>
                                <?php if ($Admin) { ?>
                                <th class="col-xs-1"><?= lang("cost"); ?></th>
                                <?php } ?>
                                <th class="col-xs-1"><?= lang("price"); ?></th>
                                <th style="width:165px;"><?= lang("actions"); ?></th>
                            </tr>
                            <tr>
                                <td colspan="12" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="modal fade" id="picModal" tabindex="-1" role="dialog" aria-labelledby="picModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                                <button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
                                <h4 class="modal-title" id="myModalLabel">title</h4>
                            </div>
                            <div class="modal-body text-center">
                                <img id="product_image" src="" alt="" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
</section>
