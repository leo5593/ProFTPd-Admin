<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 * @copyright Nicolas BERTRAND <n.bertrand@2isr.fr>
 *
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);

$field_userid   = $cfg['field_userid'];
$field_id       = $cfg['field_id'];
$field_uid      = $cfg['field_uid'];
$field_ugid     = $cfg['field_ugid'];
$field_homedir  = $cfg['field_homedir'];
$field_shell    = $cfg['field_shell'];
$field_title    = $cfg['field_title'];
$field_name     = $cfg['field_name'];
$field_company  = $cfg['field_company'];
$field_email    = $cfg['field_email'];
$field_disabled = $cfg['field_disabled'];

$field_login_count    = $cfg['field_login_count'];
$field_last_login     = $cfg['field_last_login'];
$field_bytes_in_used  = $cfg['field_bytes_in_used'];
$field_bytes_out_used = $cfg['field_bytes_out_used'];
$field_files_in_used  = $cfg['field_files_in_used'];
$field_files_out_used = $cfg['field_files_out_used'];


$field_quota_name=$cfg['field_quota_name'];
$field_quota_type=$cfg['field_quota_type'];
$field_quota_per_session=$cfg['field_quota_per_session'];
$field_quota_limit_type=$cfg['field_quota_limit_type'];
$field_quota_bytes_in_avail=$cfg['field_quota_bytes_in_avail'];
$field_quota_bytes_out_avail=$cfg['field_quota_bytes_out_avail'];
$field_quota_bytes_xfer_avail=$cfg['field_quota_bytes_xfer_avail'];
$field_quota_files_in_avail=$cfg['field_quota_files_in_avail'];
$field_quota_files_out_avail=$cfg['field_quota_files_out_avail'];
$field_quota_files_xfer_avail=$cfg['field_quota_files_xfer_avail'];
//quota tally
$field_quota_bytes_in_used=$cfg['field_quota_bytes_in_used'];
$field_quota_bytes_out_used=$cfg['field_quota_bytes_out_used'];
$field_quota_bytes_xfer_used=$cfg['field_quota_bytes_xfer_used'];
$field_quota_files_in_used=$cfg['field_quota_files_in_used'];
$field_quota_files_out_used=$cfg['field_quota_files_out_used'];
$field_quota_files_xfer_used=$cfg['field_quota_files_xfer_used'];

$all_quotas = $ac->get_quotas();

//var_dump($all_quotas);

/* parse filter  */
$userfilter = array();

function formatBytes($used,$bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0);
	
	if($bytes==0)
		return "Unlimited";
	
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
	$used /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($used, $precision) . ' / ' . round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function formatFile($u,$a){
	if(a==0)
		return "0";
	return $u." / ".$a;
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if(!is_array($all_quotas)) { ?>
<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <p>Currently there are no registered users.</p>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <a class="btn btn-primary pull-right" href="add_user.php" role="button">Add user &raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>
<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <?php if (count($userfilter) > 0) { ?>
          <!-- Filter toolbar -->
          <div class="form-group">
            <label>Prefix filter:</label>
            <div class="btn-group" role="group">
              <a type="button" class="btn btn-default" href="users.php">All users</a>
              <a type="button" class="btn btn-default" href="users.php?uf=None">No prefix</a>
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" id="idPrefix" data-toggle="dropdown" aria-expanded="false">Prefix <span class="caret"></span></button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="idPrefix">
                <?php foreach ($userfilter as $uf) { ?>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="users.php?uf=<?php echo $uf; ?>"><?php echo $uf; ?></a></li>
                <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <?php } ?>
          <!-- User table -->
          <div class="form-group">
            <table class="table table-striped table-condensed sortable">
              <thead>
                
				<th><span class="glyphicon glyphicon-tent" aria-hidden="true" title="Type"></span> NAME</th>
                 
                <th class="hidden-xs hidden-sm hidden-md" data-defaultsort="disabled">Per Session</th>
                <th class="hidden-xs hidden-sm hidden-md">Limit Type</th>
				
                <th class="hidden-xs"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true" title="Uploaded data"></th>
                <th class="hidden-xs"><span class="glyphicon glyphicon-cloud-download" aria-hidden="true" title="Downloaded data"></th>
				<th class="hidden-xs"><span class="glyphicon glyphicon-cloud" aria-hidden="true" title="Transferred data"></th>
				
                <th class="hidden-xs"><span class="glyphicon glyphicon-open-file" aria-hidden="true" title="Uploaded files"></th>
                <th class="hidden-xs"><span class="glyphicon glyphicon-save-file" aria-hidden="true" title="Downloaded files"></th>
				<th class="hidden-xs"><span class="glyphicon glyphicon-file" aria-hidden="true" title="Transferred files"></th>
				
				<th></th>

              </thead>
              <tbody>
                <?php foreach ($all_quotas as $quota) { ?>
                  <tr>
                    
                    <td class="pull-middle">
						<?php if($quota["quotatype"] == "user") { ?>
							<span class="glyphicon glyphicon-user" aria-hidden="true" title="User">
						<?php } else { ?>
							<span class="glyphicon glyphicon-tag" aria-hidden="true" title="Group">
						<?php } ?> </span>
						<a href="edit_quota.php?action=show&<?php echo $field_id; ?>=<?php echo $quota[$field_id]; ?>"><?php echo $quota["quotaname"]; ?></a>
					</td>
                	
					<td class="pull-middle hidden-xs hidden-sm hidden-md"><?php echo $quota[$field_quota_per_session]; ?></</td>
					<td class="pull-middle hidden-xs hidden-sm hidden-md"><?php echo $quota[$field_quota_limit_type]; ?></td>
					
                    <td class="pull-middle hidden-xs"><?php echo formatBytes($quota[$field_quota_bytes_in_used],$quota[$field_quota_bytes_in_avail]); ?></td>
                    <td class="pull-middle hidden-xs"><?php echo formatBytes($quota[$field_quota_bytes_out_used],$quota[$field_quota_bytes_out_avail]); ?></td>
					<td class="pull-middle hidden-xs"><?php echo formatBytes($quota[$field_quota_bytes_xfer_used],$quota[$field_quota_bytes_xfer_avail_avail]); ?></td>
					
                    <td class="pull-middle hidden-xs"><?php echo formatFile($quota[$field_quota_files_in_used],$quota[$field_quota_files_in_avail]); ?></td>
                    <td class="pull-middle hidden-xs"><?php echo formatFile($quota[$field_quota_files_out_used],$quota[$field_quota_files_out_avail]); ?></td>
					<td class="pull-middle hidden-xs"><?php echo formatFile($quota[$field_quota_files_xfer_used],$quota[$field_quota_files_xfer_avail]); ?></td>
						
                    <td class="pull-middle">
                      <div class="btn-toolbar pull-right" role="toolbar">
                        <a class="btn-group" role="group" href="edit_quota.php?action=show&<?php echo $field_id; ?>=<?php echo $quota[$field_id]; ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                        <a class="btn-group" role="group" href="edit_quota.php?action=remove&<?php echo $field_id; ?>=<?php echo $quota[$field_id]; ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <a class="btn btn-primary pull-right" href="edit_quota.php" role="button">Add quota &raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
