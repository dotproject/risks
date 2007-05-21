<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
GLOBAL $AppUI;

$risk_id = intval( dPgetParam( $_REQUEST, 'risk_id', 0 ) );
$riskDescription = dPgetParam($_POST, 'risk_note_description', '');
$note = dPgetParam($_POST, 'note', false);

// check permissions
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem( 'risks', 'edit', $risk_id );
if (! $canEdit)
	$AppUI->redirect("m=public&a=access_denied");

$viewNotes = false;
$addNotes = false;

echo 'vw_notes.php - '.$risk_id;

if ($note) {
	$risk = new dotProject_AddOn_Risks($risk_id);
	if ($risk->saveNote($AppUI->user_id, $riskDescription)) {
		$AppUI->setMsg('Note added', UI_MSG_OK);
	}
	$AppUI->redirect('m=risks&a=view&risk_id=' . $risk_id);
}

$q = new DBQuery();
$q->clear();
$q->addQuery('risk_notes.*');
$q->addQuery("CONCAT(contact_first_name, ' ', contact_last_name) as risk_note_owner");
$q->addTable('risk_notes');
$q->leftJoin('users', 'u', 'risk_note_creator = user_id');
$q->leftJoin('contacts', 'c', 'user_contact = contact_id');
$q->addWhere('risk_note_risk = ' . $risk_id);
$notes = $q->loadList();
	
echo '
<table cellpadding="5" width="100%" class="tbl">
<tr>
	<th>Date</th>
	<th>User</th>
	<th>Note</th>
</tr>';
foreach($notes as $n)
{
	echo '
<tr>
	<td nowrap>' . $n['risk_note_date'] . '</td>
	<td nowrap>' . $n['risk_note_owner'] . '</td>
	<td width="100%">' . $n['risk_note_description'] . '</td>
</tr>';
}
echo '</table>';
?>