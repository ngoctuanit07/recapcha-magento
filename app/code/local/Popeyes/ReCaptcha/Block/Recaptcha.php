<?php
/**
* Popeyes Block ReCaptcha Google
*/
class Popeyes_ReCaptcha_Block_Recaptcha extends Mage_Core_Block_Template
{
	public function getHelperReCaptcha(){
		return Mage::helper('recaptcha');
	}
}