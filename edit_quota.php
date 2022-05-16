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
$field_ad_gid   = 'ad_gid';
$field_passwd   = $cfg['field_passwd'];
$field_homedir  = $cfg['field_homedir'];
$field_shell    = $cfg['field_shell'];
$field_title    = $cfg['field_title'];
$field_name     = $cfg['field_name'];
$field_company  = $cfg['field_company'];
$field_email    = $cfg['field_email'];
$field_comment  = $cfg['field_comment'];
$field_disabled = $cfg['field_disabled'];

$field_login_count    = $cfg['field_login_count'];
$field_last_login     = $cfg['field_last_login'];
$field_last_modified  = $cfg['field_last_modified'];
$field_bytes_in_used  = $cfg['field_bytes_in_used'];
$field_bytes_out_used = $cfg['field_bytes_out_used'];
$field_files_in_used  = $cfg['field_files_in_used'];
$field_files_out_used = $cfg['field_files_out_used'];

$field_groupname = $cfg['field_groupname'];
$field_gid = $cfg['field_gid'];

$field_quota_name="quotaname";//$cfg['field_quota_name'];
$field_quota_xid=$cfg['field_quota_xid'];
$field_quota_type="quotatype";//$cfg['field_quota_type'];
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


if (empty($_REQUEST[$field_id])) {
  //header("Location: users.php");
  //die();
  //add_quota
}



$groups = $ac->get_groups();

$id = $_REQUEST[$field_id];
if (!$ac->is_valid_id($id)) {
  $errormsg = 'Invalid ID; must be a positive integer.';
} else {
  $quota = $ac->get_quota_by_id($id)[0];

  if (!is_array($quota)) {
    $errormsg = 'Quota does not exist; cannot find ID '.$id.' in the database.';
  } else {
    $quotaid = $quota[$field_userid];

  }
}

var_dump($_REQUEST);

if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "update") {
  $errors = array();
  /* user id validation */
  if (empty($_REQUEST[$field_userid])
      || !preg_match($cfg['userid_regex'], $_REQUEST[$field_userid])
      || strlen($_REQUEST[$field_userid]) > $cfg['max_userid_length']) {
    array_push($errors, 'Invalid user name; user name must contain only letters, numbers, hyphens, and underscores with a maximum of '.$cfg['max_userid_length'].' characters.');
  }
  /* uid validation */
  if (empty($_REQUEST[$field_uid]) || !$ac->is_valid_id($_REQUEST[$field_uid])) {
    array_push($errors, 'Invalid UID; must be a positive integer.');
  }
  if ($cfg['max_uid'] != -1 && $cfg['min_uid'] != -1) {
    if ($_REQUEST[$field_uid] > $cfg['max_uid'] || $_REQUEST[$field_uid] < $cfg['min_uid']) {
      array_push($errors, 'Invalid UID; UID must be between ' . $cfg['min_uid'] . ' and ' . $cfg['max_uid'] . '.');
    }
  } else if ($cfg['max_uid'] != -1 && $_REQUEST[$field_uid] > $cfg['max_uid']) {
    array_push($errors, 'Invalid UID; UID must be at most ' . $cfg['max_uid'] . '.');
  } else if ($cfg['min_uid'] != -1 && $_REQUEST[$field_uid] < $cfg['min_uid']) {
    array_push($errors, 'Invalid UID; UID must be at least ' . $cfg['min_uid'] . '.');
  }
  /* gid validation */
  if (empty($_REQUEST[$field_ugid]) || !$ac->is_valid_id($_REQUEST[$field_ugid])) {
    array_push($errors, 'Invalid main group; GID must be a positive integer.');
  }
  /* password length validation */
  if (strlen($_REQUEST[$field_passwd]) > 0 && strlen($_REQUEST[$field_passwd]) < $cfg['min_passwd_length']) {
    array_push($errors, 'Password is too short; minimum length is '.$cfg['min_passwd_length'].' characters.');
  }
  /* home directory validation */
  if (strlen($_REQUEST[$field_homedir]) <= 1) {
    array_push($errors, 'Invalid home directory; home directory cannot be empty.');
  }
  /* shell validation */
  if (strlen($_REQUEST[$field_shell]) <= 1) {
    array_push($errors, 'Invalid shell; shell cannot be empty.');
  }
  /* user name uniqueness validation */
  if ($userid != $_REQUEST[$field_userid] && $ac->check_username($_REQUEST[$field_userid])) {
    array_push($errors, 'User name already exists; name must be unique.');
  }
  /* gid existance validation */
  if (!$ac->check_gid($_REQUEST[$field_ugid])) {
    array_push($errors, 'Main group does not exist; GID '.$_REQUEST[$field_ugid].' cannot be found in the database.');
  }
  /* data validation passed */
  if (count($errors) == 0) {
    /* remove all groups */
    while (list($g_gid, $g_group) = each($groups)) {
      if (!$ac->remove_user_from_group($userid, $g_gid)) {
        array_push($errors, 'Cannot remove user "'.$userid.'" from group "'.$g_group.'"; see log files for more information.');
        break;
      }
    }
  }
  if (count($errors) == 0) {
    /* update quota */
	
    $disabled = isset($_REQUEST[$field_disabled]) ? '1':'0';
    $userdata = array($field_id       => $_REQUEST[$field_id],
                      $field_userid   => $_REQUEST[$field_userid],
                      $field_uid      => $_REQUEST[$field_uid],
                      $field_ugid     => $_REQUEST[$field_ugid],
                      $field_passwd   => $_REQUEST[$field_passwd],
                      $field_homedir  => $_REQUEST[$field_homedir],
                      $field_shell    => $_REQUEST[$field_shell],
                      $field_title    => $_REQUEST[$field_title],
                      $field_name     => $_REQUEST[$field_name],
                      $field_email    => $_REQUEST[$field_email],
                      $field_company  => $_REQUEST[$field_company],
                      $field_comment  => $_REQUEST[$field_comment],
                      $field_disabled => $disabled);
    if (!$ac->update_quota($userdata)) {
      $errormsg = 'User "'.$_REQUEST[$field_userid].'" update failed; check log files.';
    } else {
      /* update user data */
      $quota = $ac->get_quota_by_id($id);
    }
  } else {
    $errormsg = implode($errors, "<br />\n");
  }
  
}

if($quota[$field_quota_type]=="user")
{
	$user=$ac->get_user_by_uid($quota[$field_quota_xid]);
	$userid = $user[$field_userid];
	//$ugid = $user[$field_ugid];
	//$group = $ac->get_group_by_gid($ugid);
	$uid      = $user[$field_uid];
}

if($quota[$field_quota_type]=="group")
{
	$user=$ac->get_group_by_gid($quota[$field_quota_xid]);
	
	$userid = $user[$field_groupname];
	$uid    = $user[$field_gid];
	
}

/* Form values */
if (empty($errormsg)) {
  /* Default values */
  //$uid      = $user[$field_uid];
  /*
  $ugid     = $user[$field_ugid];
  $passwd   = '';
  $homedir  = $user[$field_homedir];
  $shell    = $user[$field_shell];
  $title    = $user[$field_title];
  $name     = $user[$field_name];
  $email    = $user[$field_email];
  $company  = $user[$field_company];
  $comment  = $user[$field_comment];
  $disabled = $user[$field_disabled];
  */
  $qname= $quota["quotaname"]; //$field_quota_name=cfg['field_quota_name'];
  $type= $quota["quotatype"]; //"$field_quota_type=$cfg['field_quota_type'];
  $session= $quota[$field_quota_per_session];
  $limit= $quota[$field_quota_limit_type];
  
  $bia= $quota[$field_quota_bytes_in_avail];
  $boa= $quota[$field_quota_bytes_out_avail];
  $bxa= $quota[$field_quota_bytes_xfer_avail];
  $fia= $quota[$field_quota_files_in_avail];
  $foa= $quota[$field_quota_files_out_avail];
  $fxa= $quota[$field_quota_files_xfer_avail];
  
  /*
  $biu= $quota[$field_quota_bytes_in_used];
  $bou= $quota[$field_quota_bytes_out_used];
  $bxu= $quota[$field_quota_bytes_xfer_used];
  $fiu= $quota[$field_quota_files_in_used];
  $fou= $quota[$field_quota_files_out_used];
  $fxu= $quota[$field_quota_files_xfer_used];
  */
  
} else {
  /* This is a failed attempt */
  //$userid   = $_REQUEST[$field_userid];
  //$uid      = $_REQUEST[$field_uid];
  //$ugid     = $_REQUEST[$field_ugid];
  $ad_gid   = $_REQUEST[$field_ad_gid];
  $passwd   = $_REQUEST[$field_passwd];
  $homedir  = $_REQUEST[$field_homedir];
  $shell    = $_REQUEST[$field_shell];
  $title    = $_REQUEST[$field_title];
  $name     = $_REQUEST[$field_name];
  $email    = $_REQUEST[$field_email];
  $company  = $_REQUEST[$field_company];
  $comment  = $_REQUEST[$field_comment];
  $disabled = isset($_REQUEST[$field_disabled]) ? '1' : '0';
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0);
	
	if($bytes==0)
		return "Unlimited";
	
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if (is_array($quota)) { ?>

<!-- User metadata panel -->
<div class="col-xs-12 col-sm-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        <a data-toggle="collapse" href="#userstats" aria-expanded="true" aria-controls="userstats">Quota Usage</a>
      </h3>
    </div>
    <div class="panel-body collapse in" id="userstats" aria-expanded="true">
      <div class="col-sm-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_in_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true" title="Uploaded data"></span> Uploaded Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_in_used; ?>" name="<?php echo $field_quota_bytes_in_used; ?>" value="<?php echo formatBytes($quota[$field_quota_bytes_out_used]); ?>" readonly />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_out_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud-download" aria-hidden="true" title="Downloaded data"></span> Downloaded Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_out_used; ?>" name="<?php echo $field_quota_bytes_out_used; ?>" value="<?php echo formatBytes($quota[$field_quota_bytes_out_used]); ?>" readonly />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_xfer_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud" aria-hidden="true" title="Transferred data"></span> Transferred Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_xfer_used; ?>" name="<?php echo $field_quota_bytes_xfer_used; ?>" value="<?php echo formatBytes($quota[$field_quota_bytes_xfer_used]); ?>" readonly />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_in_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-open-file" aria-hidden="true" title="Uploaded files"></span> Uploaded Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_in_used; ?>" name="<?php echo $field_quota_files_in_used; ?>" value="<?php echo $quota[$field_quota_files_in_used]; ?>" readonly />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_out_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-save-file" aria-hidden="true" title="Downloaded files"></span> Downloaded Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_out_used; ?>" name="<?php echo $field_quota_files_out_used; ?>" value="<?php echo $quota[$field_quota_files_out_used]; ?>" readonly />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_xfer_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-file" aria-hidden="true" title="Transferred files"></span> Transferred Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_xfer_used; ?>" name="<?php echo $field_quota_files_xfer_used; ?>" value="<?php echo $quota[$field_quota_files_xfer_used]; ?>" readonly />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php if ($type == 'user') { ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        <a data-toggle="collapse" href="#userstats" aria-expanded="true" aria-controls="userstats">User statistics</a>
      </h3>
    </div>
    <div class="panel-body collapse in" id="userstats" aria-expanded="true">
      <div class="col-sm-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!-- Login count (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_login_count; ?>" class="col-sm-4 control-label">Login count</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_login_count; ?>" name="<?php echo $field_login_count; ?>" value="<?php echo $user[$field_login_count]; ?>" readonly />
            </div>
          </div>
          <!-- Last login (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_last_login; ?>" class="col-sm-4 control-label">Last login</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_last_login; ?>" name="<?php echo $field_last_login; ?>" value="<?php echo $user[$field_last_login]; ?>" readonly />
            </div>
          </div>
          <!-- Last modified (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_last_modified; ?>" class="col-sm-4 control-label">Last modified</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_last_modified; ?>" name="<?php echo $field_last_modified; ?>" value="<?php echo $user[$field_last_modified]; ?>" readonly />
            </div>
          </div>
          <!-- Bytes in (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_bytes_in_used; ?>" class="col-sm-4 control-label">Bytes uploaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_bytes_in_used; ?>" name="<?php echo $field_bytes_in_used; ?>" value="<?php echo sprintf("%2.1f", $user[$field_bytes_in_used] / 1048576); ?> MB" readonly />
            </div>
          </div>
          <!-- Bytes out (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_bytes_out_used; ?>" class="col-sm-4 control-label">Bytes downloaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_bytes_out_used; ?>" name="<?php echo $field_bytes_out_used; ?>" value="<?php echo sprintf("%2.1f", $user[$field_bytes_out_used] / 1048576); ?> MB" readonly />
            </div>
          </div>
          <!-- Files in (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_files_in_used; ?>" class="col-sm-4 control-label">Files uploaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_files_in_used; ?>" name="<?php echo $field_files_in_used; ?>" value="<?php echo $user[$field_files_in_used]; ?>" readonly />
            </div>
          </div>
          <!-- Files out (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_files_out_used; ?>" class="col-sm-4 control-label">Files downloaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_files_out_used; ?>" name="<?php echo $field_files_out_used; ?>" value="<?php echo $user[$field_files_out_used]; ?>" readonly />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
<!-- Edit panel -->
<div class="col-xs-12 col-sm-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        <a data-toggle="collapse" href="#userprops" aria-expanded="true" aria-controls="userprops">Quota properties</a>
      </h3>
    </div>
    <div class="panel-body collapse in" id="userprops" aria-expanded="true">
      <div class="col-sm-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
		  <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_in_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true" title="Uploaded data"></span> Upload Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_in_avail; ?>" name="<?php echo $field_quota_bytes_in_avail; ?>" value="<?php echo $bia; ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_out_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud-download" aria-hidden="true" title="Downloaded data"></span> Download Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_out_used; ?>" name="<?php echo $field_quota_bytes_out_used; ?>" value="<?php echo $boa; ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_xfer_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud" aria-hidden="true" title="Transferred data"></span> Transfer Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_xfer_used; ?>" name="<?php echo $field_quota_bytes_xfer_used; ?>" value="<?php echo $bxa; ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_in_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-open-file" aria-hidden="true" title="Uploaded files"></span> Upload Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_in_used; ?>" name="<?php echo $field_quota_files_in_used; ?>" value="<?php echo $fia; ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_out_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-save-file" aria-hidden="true" title="Downloaded files"></span> Download Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_out_used; ?>" name="<?php echo $field_quota_files_out_used; ?>" value="<?php echo $foa; ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_xfer_used; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-file" aria-hidden="true" title="Transferred files"></span> Transfer Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_xfer_used; ?>" name="<?php echo $field_quota_files_xfer_used; ?>" value="<?php echo $fxa ?>" />
            </div>
          </div>
		
		 <!--  -->
		<div class="form-group">
			<label for="<?php echo $field_quota_type; ?>" class="col-sm-4 control-label">Type</label>
			<div class="col-sm-8">
				<select class="form-control" id="<?php echo $field_quota_type; ?>" name="<?php echo $field_quota_type; ?>" required>
					<option value="m" <?php if ($type == 'user') { echo 'selected="selected"'; } ?>>User</option>
					<option value="f" <?php if ($type == 'group') { echo 'selected="selected"'; } ?>>Group</option>
					<!--
					<option value="f" <?php if ($type == 'class') { echo 'selected="selected"'; } ?>>Class</option>
					<option value="f" <?php if ($type == 'all') { echo 'selected="selected"'; } ?>>All</option>
					-->
				</select>
			</div>
		</div>
		
          <!-- User name -->
          <div class="form-group">
            <label for="<?php echo $field_userid; ?>" class="col-sm-4 control-label">User name</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_userid; ?>" name="<?php echo $field_userid; ?>" value="<?php echo $userid; ?>" placeholder="Enter a user name" maxlength="<?php echo $cfg['max_userid_length']; ?>" pattern="<?php echo substr($cfg['userid_regex'], 2, -3); ?>" readonly />
            </div>
          </div>
          <!-- UID -->
          <div class="form-group">
            <label for="<?php echo $field_uid; ?>" class="col-sm-4 control-label"><?php if ($type == 'user') echo U; else echo G;?>ID</label>
            <div class="controls col-sm-8">
              <input type="number" class="form-control" id="<?php echo $field_uid; ?>" name="<?php echo $field_uid; ?>" value="<?php echo $uid; ?>" min="1" placeholder="Enter a UID" readonly />
            </div>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <input type="hidden" name="<?php echo $field_id; ?>" value="<?php echo $id; ?>" />
              <a class="btn btn-danger" href="remove_user.php?action=remove&<?php echo $field_id; ?>=<?php echo $id; ?>">Remove quota</a>
              <button type="submit" class="btn btn-primary pull-right" name="action" value="update2">Update quota</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
