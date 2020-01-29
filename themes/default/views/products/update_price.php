<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div id="form">
    <h1><?= $page_title; ?></h1>

    <p><strong><?= lang("update_price"); ?></strong>: <a href="<?= $this->config->base_url(); ?>smlib/lib/sample_product_price.csv">Download CSV file Sample</a></p>

    <p><span style="color: #F60;"><?= lang("csv1"); ?></span><br /><span style="color: #060;"><?= lang("csv2"); ?> (<?= lang("product_code"); ?>, <?= lang("product_price"); ?>)</span> <?= lang("csv3"); ?></p>

    <?= form_open_multipart("module=products&view=update_price");?>

    <p>
      <div>
          <label><?= lang("upload_file"); ?>:</label>

          <div class="uploader" id="uniform-undefined"><input type="file" name="userfile" class="i-format" style="opacity: 0; "><span class="filename"><?= lang("no_file_selected"); ?></span><span class="action"><?= lang("choose_file"); ?></span></div>
          <span class="input_tips"><?= lang("csv_file_tip"); ?></span>
      </div>

      <div class="clear"></div>
  </p>

  <p><?= form_submit('submit', lang("update_price"), 'class="submitInput" style="margin-left: 110px;"');?></p>

  <?= form_close();?>
</div>

