<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace Wakers\ConversionModule\Component\Frontend\ContactForm;

interface IContactForm
{
    /**
     * @return ContactForm
     */
    public function create() : ContactForm;
}
