<?php

namespace opensrs\domains\transfer;

use OpenSRS\Base;
use OpenSRS\Exception;

/*
 *  Required object values:
 *  data -
 */

class TransferCancel extends Base {
	private $_dataObject;
	private $_formatHolder = "";
	public $resultFullRaw;
	public $resultRaw;
	public $resultFullFormatted;
	public $resultFormatted;

	public function __construct( $formatString, $dataObject ) {
		parent::__construct();
		$this->_dataObject = $dataObject;
		$this->_formatHolder = $formatString;
		$this->_validateObject();
	}

	public function __destruct() {
		parent::__destruct();
	}

	// Validate the object
	private function _validateObject() {
		// Command required values
		if(
			!isset( $this->_dataObject->data->reseller ) ||
			$this->_dataObject->data->reseller == ""
		) {
			throw new Exception( "oSRS Error - reseller is not defined." );
		}

		// Execute the command
		$this->_processRequest();
	}

	// Post validation functions
	private function _processRequest() {
		$cmd = array(
			'protocol' => 'XCP',
			'action' => 'cancel_transfer',
			'object' => 'transfer',
			'attributes' => array(
				'reseller' => $this->_dataObject->data->reseller
			)
		);

		// Command optional values
		if(
			isset( $this->_dataObject->data->domain ) &&
			$this->_dataObject->data->domain != ""
		) {
			$cmd['attributes']['domain'] = $this->_dataObject->data->domain;
		}
		if(
			isset( $this->_dataObject->data->order_id ) &&
			$this->_dataObject->data->order_id != ""
		) {
			$cmd['attributes']['order_id'] = $this->_dataObject->data->order_id;
		}

        $xmlCMD = $this->_opsHandler->encode( $cmd );
        // Send XML
        $XMLresult = $this->send_cmd( $xmlCMD );
        // Flip XML to Array
        $arrayResult = $this->_opsHandler->decode( $XMLresult );

		// Results
		$this->resultFullRaw = $arrayResult;
		$this->resultRaw = $arrayResult;
		$this->resultFullFormatted = $this->convertArray2Formatted( $this->_formatHolder, $this->resultFullRaw );
		$this->resultFormatted = $this->convertArray2Formatted( $this->_formatHolder, $this->resultRaw );
	}
}