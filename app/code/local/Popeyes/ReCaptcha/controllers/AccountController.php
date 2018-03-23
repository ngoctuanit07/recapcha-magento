<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class Popeyes_ReCaptcha_AccountController extends Mage_Customer_AccountController
{
    /**
     * Login post action
     */
    public function loginPostAction()
    {
        if (Mage::helper('recaptcha')->getStatusConfig()) {
            if (!$this->_validateFormKey()) {
                $this->_redirect('*/*/');
                return;
            }

            if ($this->_getSession()->isLoggedIn()) {
                $this->_redirect('*/*/');
                return;
            }
            $session = $this->_getSession();

            if ($this->getRequest()->isPost()) {
                $login = $this->getRequest()->getPost('login');
                $recaptcha_response = $this->getRequest()->getPost('g-recaptcha-response');

                if (!empty($login['username']) && !empty($login['password']) && !empty($recaptcha_response)) {
                    $_response = file_get_contents(Mage::helper('recaptcha')->getConnectUrl($recaptcha_response));
                    $response = Mage::helper('core')->jsonDecode($_response);

                    try {
                        if (isset($response['success']) && $response['success']) {
                            $session->login($login['username'], $login['password']);
                            if ($session->getCustomer()->getIsJustConfirmed()) {
                                $this->_welcomeCustomer($session->getCustomer(), true);
                            }
                        } else {
                            $session->addError($this->__('The reCAPTCHA wasn\'t entered correctly. Try it again.'));
                        }

                    } catch (Mage_Core_Exception $e) {
                        switch ($e->getCode()) {
                            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                                $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                                $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                                break;
                            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                                $message = $e->getMessage();
                                break;
                            default:
                                $message = $e->getMessage();
                        }
                        $session->addError($message);
                        $session->setUsername($login['username']);
                    } catch (Exception $e) {
                        // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                    }
                } else {
                    if (empty($recaptcha_response)) {
                        $session->addError($this->__('The reCAPTCHA wasn\'t entered correctly. Try it again.'));
                    } else {
                        $session->addError($this->__('Login and password are required.'));
                    }
                }
            }

            $this->_loginPostRedirect();
        } else {
            parent::loginPostAction();
        }

    }

}