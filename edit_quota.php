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




$id = $_REQUEST[$field_id];
if (!$ac->is_valid_id($id)) {
  //$errormsg = 'Invalid ID; must be a positive integer.';
} else {
  $quota = $ac->get_quota_by_id($id)[0];

  if (!is_array($quota)) {
    $errormsg = 'Quota does not exist; cannot find ID '.$id.' in the database.';
  } else {
    $quotaid = $quota[$field_userid];

  }
}



if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "update") {
  $errors = array();
  
	$v=toByteSize($_REQUEST[$field_quota_bytes_in_avail]);
	if($v===false)
		array_push($errors, 'Invalid value for Upload Bytes');
	else
		$_REQUEST[$field_quota_bytes_in_avail]=$v;
	
	$v=toByteSize($_REQUEST[$field_quota_bytes_out_avail]);
	if($v===false)
		array_push($errors, 'Invalid value for Download Bytes');
	else
		$_REQUEST[$field_quota_bytes_out_avail]=$v;
	
	$v=toByteSize($_REQUEST[$field_quota_bytes_xfer_avail]);
	if($v===false)
		array_push($errors, 'Invalid value for Transfer Bytes');
	else
		$_REQUEST[$field_quota_bytes_xfer_avail]=$v;

	$v=intval($_REQUEST[$field_quota_files_in_avail]);
	if($v<0)
		array_push($errors, 'Invalid value for Upload Files '.$v);
	else
		$_REQUEST[$field_quota_files_in_avail]=$v;
	
	$v=intval($_REQUEST[$field_quota_files_out_avail]);
	if($v<0)
		array_push($errors, 'Invalid value for Download Files '.$v);
	else
		$_REQUEST[$field_quota_files_out_avail]=$v;

	$v=intval($_REQUEST[$field_quota_files_xfer_avail]);
	if($v<0)
		array_push($errors, 'Invalid value for Transfer Files '.$v);
	else
		$_REQUEST[$field_quota_files_xfer_avail]=$v;
		
		
	if(isset($_REQUEST[$field_quota_per_session]))
		$_REQUEST[$field_quota_per_session]="true";
	else
		$_REQUEST[$field_quota_per_session]="false";


  if (count($errors) == 0) {
    /* update quota */
	
    $disabled = isset($_REQUEST[$field_disabled]) ? '1':'0';
    $userdata = array($field_id       => $_REQUEST[$field_id],
	
                      //$field_userid   => $_REQUEST[$field_userid],
                      //$field_uid      => $_REQUEST[$field_uid],
                      //$field_ugid     => $_REQUEST[$field_ugid],
                      //$field_passwd   => $_REQUEST[$field_passwd],
                      //$field_homedir  => $_REQUEST[$field_homedir],
					  
					  $cfg['field_quota_type']	=> $_REQUEST[$field_quota_type],
					  $field_quota_per_session	=> $_REQUEST[$field_quota_per_session],
					  $field_quota_limit_type	=> $_REQUEST[$field_quota_limit_type],
					  
                      $field_quota_bytes_in_avail	=> $_REQUEST[$field_quota_bytes_in_avail],
                      $field_quota_bytes_out_avail  => $_REQUEST[$field_quota_bytes_out_avail],
                      $field_quota_bytes_xfer_avail	=> $_REQUEST[$field_quota_bytes_xfer_avail],
                      $field_quota_files_in_avail	=> $_REQUEST[$field_quota_files_in_avail],
                      $field_quota_files_out_avail	=> $_REQUEST[$field_quota_files_out_avail],
                      $field_quota_files_xfer_avail	=> $_REQUEST[$field_quota_files_xfer_avail] );

	  $v=$ac->update_quota($userdata);
    if ($v===false) {
      $errormsg = 'Quota "'.$_REQUEST[$field_userid].'" update failed; check log files.';
    } else {
      /* update quota data */
      $quota = $ac->get_quota_by_id($id)[0];
    }
  } else {
    $errormsg = implode($errors, "<br />\n");
  }
  
}
else if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "remove")
{
	echo "Delete quota ".$quota[$field_quota_name]." ".$quota[$field_quota_type];
	$ac->delete_quota($quota[$field_quota_name],$quota[$field_quota_type]);
	
	header("Location: quotas.php");
	die();
}
else if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "new")
{
	//echo "NEW quota ".$quota[$field_quota_name]." ".$quota[$field_quota_type];
	
	$v=toByteSize($_REQUEST[$field_quota_bytes_in_avail]);
	if($v===false)
		array_push($errors, 'Invalid value for Upload Bytes');
	else
		$_REQUEST[$field_quota_bytes_in_avail]=$v;
	
	$v=toByteSize($_REQUEST[$field_quota_bytes_out_avail]);
	if($v===false)
		array_push($errors, 'Invalid value for Download Bytes');
	else
		$_REQUEST[$field_quota_bytes_out_avail]=$v;
	
	$v=toByteSize($_REQUEST[$field_quota_bytes_xfer_avail]);
	if($v===false)
		array_push($errors, 'Invalid value for Transfer Bytes');
	else
		$_REQUEST[$field_quota_bytes_xfer_avail]=$v;

	$v=intval($_REQUEST[$field_quota_files_in_avail]);
	if($v<0)
		array_push($errors, 'Invalid value for Upload Files '.$v);
	else
		$_REQUEST[$field_quota_files_in_avail]=$v;
	
	$v=intval($_REQUEST[$field_quota_files_out_avail]);
	if($v<0)
		array_push($errors, 'Invalid value for Download Files '.$v);
	else
		$_REQUEST[$field_quota_files_out_avail]=$v;

	$v=intval($_REQUEST[$field_quota_files_xfer_avail]);
	if($v<0)
		array_push($errors, 'Invalid value for Transfer Files '.$v);
	else
		$_REQUEST[$field_quota_files_xfer_avail]=$v;
	
	if(isset($_REQUEST[$field_quota_per_session]))
		$_REQUEST[$field_quota_per_session]="true";
	else
		$_REQUEST[$field_quota_per_session]="false";
	
	$name = explode("-",$_REQUEST["quotaname"])[1];
	$type = explode("-",$_REQUEST["quotaname"])[0];
	
	
	$userdata = array($cfg['field_quota_name']       => $name,
						  
					  $cfg['field_quota_type']	=> $type,
					  $field_quota_per_session	=> $_REQUEST[$field_quota_per_session],
					  $field_quota_limit_type	=> $_REQUEST[$field_quota_limit_type],
					  
                      $field_quota_bytes_in_avail	=> $_REQUEST[$field_quota_bytes_in_avail],
                      $field_quota_bytes_out_avail  => $_REQUEST[$field_quota_bytes_out_avail],
                      $field_quota_bytes_xfer_avail	=> $_REQUEST[$field_quota_bytes_xfer_avail],
					  
                      $field_quota_files_in_avail	=> $_REQUEST[$field_quota_files_in_avail],
                      $field_quota_files_out_avail	=> $_REQUEST[$field_quota_files_out_avail],
                      $field_quota_files_xfer_avail	=> $_REQUEST[$field_quota_files_xfer_avail] );
	
	$v=$ac->add_quota($userdata);
	
	//var_dump($_REQUEST);
	if ($v===false) {
      $errormsg = 'Quota "'.$_REQUEST[$field_userid].'" insert failed; check log files.';
    } else {
      /* update quota data */
		//var_dump($v);
	  $id=$v;
      $quota = $ac->get_quota_by_id($id)[0];
    }
	
}
else if( !$id ) {

	$quota=array();
	$quota[$field_quota_files_in_avail]=0;
	$quota[$field_quota_files_out_avail]=0;
	$quota[$field_quota_files_xfer_avail]=0;
	
	$all_quotas = $ac->get_quotas();
	$quotas = array();
	foreach($all_quotas as $key => $value)
		array_push($quotas,$value["quotatype"]."-".$value["quotaname"]);
	
	$groups = $ac->get_groups();
	$users=$ac->get_users();
	
	
}

/* Form values */
if (empty($errormsg)) {
  
  $qname= $quota["quotaname"]; //$field_quota_name=cfg['field_quota_name'];
  $type= $quota["quotatype"]; //"$field_quota_type=$cfg['field_quota_type'];
  $per_session= ($quota[$field_quota_per_session]=="true");
  $limit= $quota[$field_quota_limit_type];
  
  $bia= $quota[$field_quota_bytes_in_avail];
  $boa= $quota[$field_quota_bytes_out_avail];
  $bxa= $quota[$field_quota_bytes_xfer_avail];
  $fia= $quota[$field_quota_files_in_avail];
  $foa= $quota[$field_quota_files_out_avail];
  $fxa= $quota[$field_quota_files_xfer_avail];
  
  
} else {
	  
  $type=$_REQUEST["quotatype"]; //"$field_quota_type=$cfg['field_quota_type'];
  $per_session= $_REQUEST[$field_quota_per_session]=="true";
  $limit= $_REQUEST[$field_quota_limit_type];
  
  $bia= $_REQUEST[$field_quota_bytes_in_avail];
  $boa= $_REQUEST[$field_quota_bytes_out_avail];
  $bxa= $_REQUEST[$field_quota_bytes_xfer_avail];
  $fia= $_REQUEST[$field_quota_files_in_avail];
  $foa= $_REQUEST[$field_quota_files_out_avail];
  $fxa= $_REQUEST[$field_quota_files_xfer_avail];
 
}

if($quota[$field_quota_type]=="user")
{
	$user=$ac->get_user_by_userid($qname);//$quota[$field_quota_xid]);
	$userid = $user[$field_userid];
	$xid      = $user[$field_uid];
}



if($quota[$field_quota_type]=="group")
{
	$xid="";
	$groups = $ac->get_groups();
	foreach ($groups as $key => $value)
	{
		if($value==$qname)
			$xid.=$key;
	}
	
	
	if($xid=="")
		$xid="No Match";
	
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

function floatvalue($val){
            $val = str_replace(",",".",$val);
            $val = preg_replace('/\.(?=.*\.)/', '', $val);
            return floatval($val);
}

function toByteSize($p_sFormatted) {
	if($p_sFormatted == "Unlimited")
		return 0;
    $aUnits = array('B'=>0, 'KB'=>1, 'MB'=>2, 'GB'=>3, 'TB'=>4, 'PB'=>5, 'EB'=>6, 'ZB'=>7, 'YB'=>8);
    $sUnit = strtoupper(trim(substr($p_sFormatted, -2)));
    if (intval($sUnit) !== 0 || $sUnit==="0" || $sUnit==="00") {
		$p_sFormatted = $p_sFormatted." B";
        $sUnit = 'B';
    }
    if (!in_array($sUnit, array_keys($aUnits))) {
        return false;
    }
    $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
    if (!intval($iUnits) == $iUnits) {
        return false;
    }
    return intval(floatval($iUnits) * pow(1024, $aUnits[$sUnit]));
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if (is_array($quota)) { ?>

<!-- User metadata panel -->
<div class="col-xs-12 col-sm-6">
	<?php if ($id) { ?>
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
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_in_used; ?>" name="<?php echo $field_quota_bytes_in_used; ?>" value="<?php echo formatBytes($quota[$field_quota_bytes_in_used]); ?>" readonly />
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
  <?php } ?>
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
        <a data-toggle="collapse" href="#userprops" aria-expanded="true" aria-controls="userprops">Quota limit</a>
      </h3>
    </div>
    <div class="panel-body collapse in" id="userprops" aria-expanded="true">
      <div class="col-sm-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
		  <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_in_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true" title="Uploaded data"></span> Upload Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_in_avail; ?>" name="<?php echo $field_quota_bytes_in_avail; ?>" value="<?php echo formatBytes($bia); ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_out_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud-download" aria-hidden="true" title="Downloaded data"></span> Download Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_out_avail; ?>" name="<?php echo $field_quota_bytes_out_avail; ?>" value="<?php echo formatBytes($boa); ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_bytes_xfer_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-cloud" aria-hidden="true" title="Transferred data"></span> Transfer Bytes</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_bytes_xfer_avail; ?>" name="<?php echo $field_quota_bytes_xfer_avail; ?>" value="<?php echo formatBytes($bxa); ?>" />
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_in_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-open-file" aria-hidden="true" title="Uploaded files"></span> Upload Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_in_avail; ?>" name="<?php echo $field_quota_files_in_avail; ?>" value="<?php echo $fia; ?>" />
			  <p class="help-block"><small>0 as unlimited.</small></p>
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_out_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-save-file" aria-hidden="true" title="Downloaded files"></span> Download Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_out_avail; ?>" name="<?php echo $field_quota_files_out_avail; ?>" value="<?php echo $foa; ?>" />
			  <p class="help-block"><small>0 as unlimited.</small></p>
            </div>
          </div>
          <!--  -->
          <div class="form-group">
            <label for="<?php echo $field_quota_files_xfer_avail; ?>" class="col-sm-4 control-label"><span class="glyphicon glyphicon-file" aria-hidden="true" title="Transferred files"></span> Transfer Files</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_quota_files_xfer_avail; ?>" name="<?php echo $field_quota_files_xfer_avail; ?>" value="<?php echo $fxa ?>" />
			  <p class="help-block"><small>0 as unlimited.</small></p>
            </div>
          </div>
		
			<!--  -->
			<?php if ($id) { ?>
			<div class="form-group">
				<label for="<?php echo $field_quota_type; ?>" class="col-sm-4 control-label">Type</label>
				<div class="col-sm-8">
					<select class="form-control" id="<?php echo $field_quota_type; ?>" name="<?php echo $field_quota_type; ?>" required>
						<option value="user" <?php if ($type == 'user') { echo 'selected="selected"'; } ?>>User</option>
						<option value="group" <?php if ($type == 'group') { echo 'selected="selected"'; } ?>>Group</option>
						<!--
						<option value="f" <?php if ($type == 'class') { echo 'selected="selected"'; } ?>>Class</option>
						<option value="f" <?php if ($type == 'all') { echo 'selected="selected"'; } ?>>All</option>
						-->
					</select>
				</div>
			</div>
			<?php } ?>
			<!--  -->
			
			<div class="form-group">
				<label for="<?php echo $field_quota_limit_type; ?>" class="col-sm-4 control-label">Limit</label>
				<div class="col-sm-8">
					<select class="form-control" id="<?php echo $field_quota_limit_type; ?>" name="<?php echo $field_quota_limit_type; ?>" required>
						<option value="soft" <?php if ($limit == 'soft') { echo 'selected="selected"'; } ?>>Soft</option>
						<option value="hard" <?php if ($limit == 'hard') { echo 'selected="selected"'; } ?>>Hard</option>
					</select>
					<p class="help-block"><small>Hard limit type means that a user's tally will never be allowed to exceed the limit</small></p>
				</div>
			</div>
			
			<!--  -->
			<div class="form-group">
				<label for="<?php echo $field_quota_per_session; ?>" class="col-sm-4 control-label">Per Session</label>
				<div class="col-sm-8">
					<div class="checkbox">
						<label>
							<input type="checkbox" id="<?php echo $field_quota_per_session; ?>" name="<?php echo $field_quota_per_session; ?>" <?php if ($per_session) { echo 'checked="checked"'; } ?> />
						</label>
					</div>
				</div>
			</div>
		
          <!-- User name -->
		  
          <div class="form-group">
            <label for="<?php echo $field_quota_name; ?>" class="col-sm-4 control-label">Quota name</label>
			
            <div class="controls col-sm-8">
			<?php if($id) { ?>
              <input type="text" class="form-control" id="<?php echo $field_quota_name; ?>" name="<?php echo $field_quota_name; ?>" value="<?php echo $qname; ?>" placeholder="Enter a user name" maxlength="<?php echo $cfg['max_userid_length']; ?>" pattern="<?php echo substr($cfg['userid_regex'], 2, -3); ?>" readonly />
            
			<?php } else { ?>
				<select class="form-control" id="<?php echo $field_quota_name; ?>" name="<?php echo $field_quota_name; ?>" required>
				    <option value="" ></option>
					<?php foreach ($groups as $key => $value){
							if(!in_array("group-".$value,$quotas)) {
						?>
					<option value="group-<?php echo $value ?>" ><?php echo "Group> $value" ?></option>
						<?php } } ?>
					<option value="" ></option>
					<?php foreach ($users as $key => $value){
							if(!in_array("user-".$value[$field_userid],$quotas)) {						
							?>
					<option value="user-<?php echo $value[$field_userid] ?>" ><?php echo "User> ".$value[$field_userid] ?></option>
						<?php } } ?>
				</select>
			<?php } ?>
			</div>
          </div>
		  		  
          <!-- UID -->
		  <?php if ($id) { ?>
          <div class="form-group">
            <label for="xid" class="col-sm-4 control-label"><?php if ($type == 'user') echo U; else echo G;?>ID Match</label>
            <div class="controls col-sm-8">
              <input class="form-control" id="xid" name="xid" value="<?php echo $xid; ?>" min="1" placeholder="Enter a UID" readonly />
            </div>
          </div>
		  <?php } ?>
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
			<?php if ($id) { ?>
              <input type="hidden" name="<?php echo $field_id; ?>" value="<?php echo $id; ?>" />
              <a class="btn btn-danger" href="edit_quota.php?action=remove&<?php echo $field_id; ?>=<?php echo $id; ?>">Remove quota</a>
              <button type="submit" class="btn btn-primary pull-right" name="action" value="update">Update quota</button>
			<?php } else { ?>
			  <button type="submit" class="btn btn-primary pull-right" name="action" value="new">New quota</button>
			
			<?php } ?>
				
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
