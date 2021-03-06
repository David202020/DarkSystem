<?php

namespace pocketmine\network\protocol\v120;

use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;

class ModalFormResponsePacket extends PEPacket{

	const NETWORK_ID = Info120::MODAL_FORM_RESPONSE_PACKET;
	const PACKET_NAME = "MODAL_FORM_RESPONSE_PACKET";

	public $formId;
	public $data;
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
		$this->formId = $this->getVarInt();
		$this->data = $this->getString();
	}
	
	public function encode($playerProtocol){
		
	}
	
}
