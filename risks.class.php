<?php /* CONTACTS $Id: risks.class.php,v 1.2 2007/05/21 01:57:50 caseydk Exp $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
##
## Risks Class
##

class dotProject_AddOn_Risks {
	var $risk_id = NULL;
	var $risk_project = NULL;
	var $risk_task = NULL;
	var $risk_owner = NULL;
	var $risk_name = NULL;
	var $risk_description = NULL;
	var $risk_probability = NULL;
	var $risk_status = NULL;
	var $risk_impact = NULL;
	var $risk_notes = NULL;

	function CRisk($riskId = 0) {
		$this->risk_id = $riskId;
	}

	function load( $oid ) {
		$q = new DBQuery();
		$q->addQuery('*');
		$q->addTable('risks');
		$q->addWhere('risk_id = ' . $oid);
		return db_loadObject( $q->prepare(), $this );
	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return get_class( $this )."::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
	
		return NULL; 
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed";
		}
		if( $this->risk_id ) {
			$ret = db_updateObject( 'risks', $this, 'risk_id' );
		} else {
			$ret = db_insertObject( 'risks', $this, 'risk_id' );
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete() {
		$q = new DBQuery();
		$q->setDelete('risks');
		$q->addWhere('risk_id = ' . $this->risk_id);
		if (!$q->exec()) {
			return db_error();
		} else { 
			return null;
		}
	}
	
	function saveNote($userId, $riskDescription) {
		$q = new DBQuery();
		$q->addTable('risk_notes');
		$q->addInsert('risk_note_risk', $this->risk_id);
		$q->addInsert('risk_note_creator', $userId);
		$q->addInsert('risk_note_date', 'NOW()', false, true);
		$q->addInsert('risk_note_description', $riskDescription);

print_r($q);

		if (!$q->exec()) {
			return db_error();
		} else { 
			return true;
		}
	}
}
?>
