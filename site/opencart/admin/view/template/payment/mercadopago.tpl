<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-amazon-checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="button button-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="button btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-amazon-checkout" class="form-horizontal">
		  <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-order-status" data-toggle="tab"><?php echo $tab_order_status; ?></a></li>
          </ul>
		  <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
				<div class="form-group">
				 <label class="col-sm-2 control-label" for="input-points"></label>
				 <div class="col-sm-10">
					<?php echo $entry_ipn;?>
				 </div>
				</div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-client-id"><?php echo $entry_client_id; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="mercadopago_client_id" value="<?php echo $mercadopago_client_id; ?>" placeholder="<?php echo $entry_client_id_text; ?>" id="input-client-id" class="form-control" />
				  <?php if ($error_client_id) { ?>
				  <div class="text-danger"><?php echo $error_client_id; ?></div>
				  <?php } ?>           
			   </div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-client-secret"><?php echo $entry_client_secret; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="mercadopago_client_secret" value="<?php echo $mercadopago_client_secret; ?>" placeholder="<?php echo $entry_client_secret_text; ?>" id="input-client-secret" class="form-control" />
				  <?php if ($error_client_secret) { ?>
				  <div class="text-danger"><?php echo $error_client_secret; ?></div>
				  <?php } ?>           
			   </div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-category"><?php echo $entry_category; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_category_id" id="input-category" class="form-control">
					  <?php foreach ($category_list as $category) { ?>
						<?php if ($category['id'] == $mercadopago_category_id) { ?>
						  <option value="<?php echo $category['id']; ?>" selected="selected" id="<?php echo $category['description']; ?>"><?php echo $category['description']; ?></option>
						<?php } else { ?>
						  <option value="<?php echo $category['id']; ?>" id="<?php echo $category['description']; ?>"><?php echo $category['description']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-url"><?php echo $entry_url; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="mercadopago_url" value="<?php echo $mercadopago_url; ?>" placeholder="<?php echo $entry_url_text; ?>" id="input-url" class="form-control" />
				  <?php if ($error_mercadopago_url) { ?>
				  <div class="text-danger"><?php echo $error_mercadopago_url; ?></div>
				  <?php } ?>           
			   </div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-debug"><?php echo $entry_debug; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_debug" id="input-debug" class="form-control">
					  <?php if ($mercadopago_debug) { ?>
					  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					  <option value="0"><?php echo $text_disabled; ?></option>
					  <?php } else { ?>
					  <option value="1"><?php echo $text_enabled; ?></option>
					  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-sandbox"><?php echo $entry_sandbox; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_sandbox" id="input-sandbox" class="form-control">
					  <?php if ($mercadopago_sandbox) { ?>
						<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
						<option value="0"><?php echo $text_disabled; ?></option>
					  <?php } else { ?>
						<option value="1"><?php echo $text_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-type-checkout"><?php echo $entry_type_checkout; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_type_checkout" id="input-type-checkout" class="form-control">
					  <?php foreach ($type_checkout as $type) { ?>
						<?php if ($type == $mercadopago_type_checkout) { ?>
						  <option value="<?php echo $type; ?>" selected="selected" id="<?php echo $type; ?>"><?php echo $type; ?></option>
						<?php } else { ?>
						  <option value="<?php echo $type; ?>" id="<?php echo $type; ?>"><?php echo $type; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id" id="input-order-status" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_country" id="input-country" class="form-control">
					  <?php foreach ($countries as $country) { ?>
						<?php if ($country['id'] == $mercadopago_country) { ?>
					  <option value="<?php echo $country['id']; ?>" selected="selected" id="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $country['id']; ?>" id="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-installments"><?php echo $entry_installments; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_installments" id="input-installments" class="form-control">
					  <?php foreach ($installments as $installment) { ?>
						<?php if ($installment['id'] == $mercadopago_installments) { ?>
					  <option value="<?php echo $installment['id']; ?>" selected="selected" id="<?php echo $installment['id']; ?>"><?php echo $installment['value']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $installment['id']; ?>" id="<?php echo $installment['id']; ?>"><?php echo $installment['value']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
		<?php if(isset($methods)){?>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-payments_not_accept"><?php echo $entry_payments_not_accept; ?></label>
				<div class="col-sm-10">
					<table>         
						<?php foreach ($methods as $method) : ?>
							<?php if($method['id'] != 'account_money'){ ?>
								<?php if($mercadopago_methods != null && in_array($method['id'], $mercadopago_methods)){ ?>
								<tr>
									<td>
										<label><?php echo $method['name'];?></td><td><input name="mercadopago_methods[]" type="checkbox" checked="yes" value="<?php echo $method['id'];?>"></label>
									</td>
								</tr>    
								<?php } else { ?>
								<tr>
									<td>
										<label><?php echo $method['name'];?></td><td><input name="mercadopago_methods[]" type="checkbox" value="<?php echo $method['id'];?>"></label>
									</td>
								</tr>       
						<?php }} endforeach; ?>
					</table>
				</div>
			  </div>
		<?php } ?>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_status" id="input-status" class="form-control">
					  <?php if ($mercadopago_status) { ?>
					  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					  <option value="0"><?php echo $text_disabled; ?></option>
					  <?php } else { ?>
					  <option value="1"><?php echo $text_enabled; ?></option>
					  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="mercadopago_sort_order" value="<?php echo $mercadopago_sort_order; ?>" id="input-sort-order" class="form-control" />
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab-order-status">
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status-completed"><?php echo $entry_order_status_completed; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id_completed" id="input-order-status-completed" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id_completed) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status-pending"><?php echo $entry_order_status_pending; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id_pending" id="input-order-status-pending" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id_pending) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status-canceled"><?php echo $entry_order_status_canceled; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id_canceled" id="input-order-status-canceled" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id_canceled) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status-in-process"><?php echo $entry_order_status_in_process; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id_in_process" id="input-order-status-in-process" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id_in_process) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status-rejected"><?php echo $entry_order_status_rejected; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id_rejected" id="input-order-status-rejected" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id_rejected) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status-in-mediation"><?php echo $entry_order_status_in_mediation; ?></label>
				<div class="col-sm-10">
				  <select name="mercadopago_order_status_id_in_mediation" id="input-order-status-in-mediation" class="form-control">
					  <?php foreach ($order_statuses as $order_status) { ?>
						<?php if ($order_status['order_status_id'] == $mercadopago_order_status_id_in_mediation) { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
						<?php } else { ?>
					  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
						<?php } ?>
					  <?php } ?>
				  </select>
				</div>
			  </div>
		  </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>