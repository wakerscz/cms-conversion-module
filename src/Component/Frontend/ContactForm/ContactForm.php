<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace Wakers\ConversionModule\Component\Frontend\ContactForm;

use Nette\Application\UI\Form;
use Nette\Mail\SmtpMailer;
use Nette\Utils\DateTime;
use Wakers\BaseModule\Component\Frontend\BaseControl;
use Wakers\BaseModule\Util\AjaxValidate;
use Wakers\ConversionModule\Manager\ConversionManager;

class ContactForm extends BaseControl
{
    use AjaxValidate;

    /**
     * @var ConversionManager
     */
    protected $conversionManager;

    /**
     * AntiSpam: Your unique token
     * @var string
     */
    protected $FORM_TRUE_TOKEN = 'hlsrn252q69@gsahPP342dPT913UJ9GOFffd#01';

    /**
     * AntiSpam: Timeout before submitting the form
     * @var int
     */
    protected $FORM_TIMEOUT_SEC = 15;

    /**
     * ContactForm constructor.
     * @param ConversionManager $conversionManager
     */
    public function __construct(ConversionManager $conversionManager)
    {
        $this->conversionManager = $conversionManager;
    }

    /**
     * @param string $template
     */
    public function render(string $template = './form/contactForm.latte') : void
    {
        $templatePath = __DIR__ . '/../../../../../../../app/template/';
        $this->template->render($templatePath . $template);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    protected function createComponentForm() : Form
    {
        $form = new Form;
        $hash = md5((new \DateTime())->format('d.m.Y_H:i:s'));

        // Group other
        $form->addGroup('other');
        $form->addSubmit('save');
        $form->addHidden('token', $hash)
            ->setAttribute('data-token', $this->FORM_TRUE_TOKEN)
            ->setAttribute('data-token-timeout-sec', $this->FORM_TIMEOUT_SEC);
        $form->addHidden('tokenCheck', $hash);

        // Group core
        $form->addGroup('core');
        $form->addText('contact')
            ->setRequired('Telefon / E-mail je povinný.');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->token !== $this->FORM_TRUE_TOKEN || $values->tokenCheck !== '') {
                $form->addError("Prosím vyčkete {$this->FORM_TIMEOUT_SEC} vteřin a odešlete formulář znovu. 
                Tímto se bráníme proti spamu. Děkujeme za pochopení");
            }
        };

        $form->onError[] = function (Form $form) {
            $this->validate($form);
        };

        $form->onSuccess[] = function (Form $form) {

            // Get values by core group
            $values = [];
            foreach ($form->getGroup('core')->getControls() as $control) {
                $values[$control->getName()] = $control->getValue();
            }

            // Append own values
            $values = $values + [
                'currency' => 'CZK',
                'value' => 0.0,
                'ip' => $_SERVER['REMOTE_ADDR']
            ];

            // Save conversion
            $conversion = $this->conversionManager->create('contactForm', new DateTime, $values);

            // Append conversion values
            $values = $values + [
                'name' => $conversion->getName(),
                'id' => $conversion->getId(),
                'createdAt' => $conversion->getCreatedAt('d.m.Y H:i:s')
            ];

            // Send mail
            $this->conversionManager->sendMail($values, 'Poptávkový formulář z Vašeho webu', "Dobrý den,\n\rz Vašeho webu byl právě odeslán kontaktní formulář.");

            // Set conversion payload
            $this->presenter->payload->conversion = $values;
        };

        $form->onSuccess[] = function () {
            $this->presenter->sendPayload();
        };

        return $form;
    }
}
