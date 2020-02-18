<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace Wakers\ConversionModule\Component\Frontend\ContactForm;

trait Create
{
    /**
     * @var IContactForm
     * @inject
     */
    public $IApp_ContactForm;

    /**
     * @return ContactForm
     */
    protected function createComponentAppContactForm() : ContactForm
    {
        return $this->IApp_ContactForm->create();
    }
}
