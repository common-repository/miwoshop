<style type="text/css">
    .column-css { border: 3px solid #EEE; padding: 10px; }
    .image-css { display: block; margin-left: auto; margin-right: auto; width: 128px; height: 128px; }
</style>
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
        <table align="center" width="600">
          <tr>
              <td class="column-css">
                  <a href="http://miwisoft.com/support/search" target="_blank"><img src="http://miwisoft.com/images/support/search.png" class="image-css" /></a>
                  <br />
                  <div style="text-align: center;"><a href="http://miwisoft.com/support/search" target="_blank"><strong>Find Solutions</strong></a></div>
              </td>
              <td class="column-css">
                  <a href="http://miwisoft.com/support/docs/miwoshop" target="_blank"><img src="http://miwisoft.com/images/support/documentation.png" class="image-css" /></a>
                  <br />
                  <div style="text-align: center;"><a href="http://miwisoft.com/support/docs/miwoshop" target="_blank"><strong>Documentation</strong></a></div>
              </td>
              <td class="column-css">
                  <a href="http://miwisoft.com/support/docs/miwoshop/video-tutorials" target="_blank"><img src="http://miwisoft.com/images/support/videos.png" class="image-css" /></a>
                  <br />
                  <div style="text-align: center;"><a href="http://miwisoft.com/support/docs/miwoshop/video-tutorials" target="_blank"><strong>Video Tutorials</strong></a></div>
              </td>
          </tr>
          <tr>
              <td class="column-css">
                  <a href="http://miwisoft.com/support/tickets" target="_blank"><img src="http://miwisoft.com/images/support/tickets.png" class="image-css" /></a>
                  <br />
                  <div style="text-align: center;"><a href="http://miwisoft.com/support/tickets" target="_blank"><strong>Tickets System</strong></a></div>
              </td>
              <td class="column-css">
                  <a href="http://miwisoft.com/services/new-extension-request" target="_blank"><img src="http://miwisoft.com/images/support/new-extension.png" class="image-css" /></a>
                  <br />
                  <div style="text-align: center;"><a href="http://miwisoft.com/services/new-extension-request" target="_blank"><strong>New Extension Request (Paid)</strong></a></div>
              </td>
              <td class="column-css">
                  <a href="http://miwisoft.com/services/support" target="_blank"><img src="http://miwisoft.com/images/support/paid-support.png" class="image-css" /></a>
                  <br />
                  <div style="text-align: center;"><a href="http://miwisoft.com/services/support" target="_blank"><strong>Support Service (Paid)</strong></a></div>
              </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>