<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if (isset($error_warning)) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if (isset($success)) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" form="form-backup" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-refresh"></i><?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
          <script type="text/javascript">
              submitForm = function() {
                  var form = document.getElementById('upgradeFromUpload');

                  // do field validation
                  if (form.install_package.value == ""){
                      alert("<?php echo MText::_('No file selected', true); ?>");
                  }
                  else {
                      form.submit();
                  }
              }
          </script>
          <fieldset class="adminform">
              <legend><?php echo MText::_('COM_MIWOSHOP_UPGRADE_VERSION_INFO'); ?></legend>
              <table class="adminform">
                  <tr>
                      <th>
                          <?php echo MText::_('COM_MIWOSHOP_INSTALLED_VERSION'); ?> : <?php echo Miwoshop::get('base')->getMiwoshopVersion();?>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <?php echo MText::_('COM_MIWOSHOP_LATEST_VERSION'); ?> : <?php echo Miwoshop::get('base')->getLatestMiwoshopVersion();?>
                      </th>
                  </tr>
              </table>
          </fieldset>
          <br/><br/>
          <div id="tabs" class="htabs">
              <a href="#tab-automatic"></a>
              <a href="#tab-manual"></a>
          </div>
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-automatic" data-toggle="tab"><?php echo MText::_('COM_MIWOSHOP_UPGRADE_FROM_SERVER'); ?></a></li>
            <li><a href="#tab-manual" data-toggle="tab"><?php echo MText::_('COM_MIWOSHOP_UPGRADE_FROM_FILE'); ?></a></li>
          </ul>
          <div class="tab-content">
              <div class="tab-pane active in" id="tab-automatic">
				  <form action="update-core.php" method="post" enctype="multipart/form-data" id="upgradeFromServer">
					  <table class="form">
						  <tr>
                              <td>
                                  <input class="button btn button-primary" type="button" value="<?php echo $text_auto_btn; ?>" onclick="form.submit()"/>
                              </td>
                          </tr>
                      </table>
                  </form>
              </div>
              <div class="tab-pane" id="tab-manual">
              <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="upgradeFromUpload">
                  <table class="form">
                      <tr>
                          <td><?php echo $text_upload_pkg; ?></td>
                          <td>
                              <input class="input_box" id="install_package" name="install_package" type="file" size="57" />
                          </td>
                      </tr>
                      <tr>
                          <td><br/><input class="button btn button-primary" type="button" value="<?php echo $text_upload_upgrade; ?>" onclick="submitForm()" /></td>
                      </tr>
                      <input type="hidden" name="option" value="com_miwoshop" />
                      <!--<input type="hidden" name="route" value="upgrade/upgrade" />-->
                      <input type="hidden" name="task" value="upgrade" />
                      <input type="hidden" name="type" value="upload" />
                      <input type="hidden" name="<?php echo $token; ?>" value="1" />
                      <?php //echo MHtml::_('form.token'); ?>
                  </table>
              </form>
          </div>
          </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>