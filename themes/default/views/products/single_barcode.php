<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header modal-primary no-print">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
			<h4 class="modal-title" id="myModalLabel">
				<?= $page_title; ?>
			</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="barcode">

						<?=$html?>

					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer no-print">
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close'); ?></button>
			<button class="btn btn-primary" href="javascript:void();" onclick="window.print();"><i class="fa fa-print"></i> <?= lang('print'); ?></button>
		</div>
	</div>
</div>