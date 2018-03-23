<?php
class Popeyes_ReCaptcha_Helper_Data extends Mage_Core_Helper_Abstract
{
	const RECAPTCHA_STATUS_CONFIG = 'popeyes_recaptcha_sections/popeyes_recaptcha_group/popeyes_recaptcha_status';
	const SITE_KEY_CONFIG = 'popeyes_recaptcha_sections/popeyes_recaptcha_group/popeyes_recaptcha_site_key';
	const SECRET_KEY_CONFIG = 'popeyes_recaptcha_sections/popeyes_recaptcha_group/popeyes_recaptcha_secret_key';

	public function getStatusConfig()
	{
		return Mage::getStoreConfig(self::RECAPTCHA_STATUS_CONFIG);
	}

	public function getSiteKeyConfig()
	{
		return Mage::getStoreConfig(self::SITE_KEY_CONFIG);
	}

	public function getSecretConfig()
	{
		return Mage::getStoreConfig(self::SECRET_KEY_CONFIG);
	}

	public function getApiUrl(){
		return 'https://www.google.com/recaptcha/api/siteverify';
	}

	public function getIpCustomer(){
		return Mage::helper('core/http')->getRemoteAddr();
	}

	public function getConnectUrl($recaptcha_response){
		return $this->getApiUrl().'?secret='.$this->getSecretConfig().'&response='.$recaptcha_response.'&remoteip='.$this->getIpCustomer();
	}
}