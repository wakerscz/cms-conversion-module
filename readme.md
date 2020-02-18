# Conversion module
Modul umožňuje ukládat konverze do DB a zároveň obsahuje výchozí komponentu pro poptávkový formulář. 

## Instalace
**Připojíme soubory**

1. V souboru app.neon:
`./../../vendor/wakers/cms-conversion-module/src/config/config.neon`

1. V souboru schema.xml:
`<external-schema filename="./../../vendor/wakers/cms-conversion-module/src/schema/conversion.xml" referenceOnly="FALSE"/>`

2. V souboru custom-frontend.js:
`./vendor/wakers/cms-conversion-module/src/assets/Frontend/conversion/conversion.js`

**Aktuaizujeme DB:**
1. `./sc propel model:build`
1. `./sc propel migration:diff`
1. `./sc propel migration:migrate`

**V presenteru vytoříme / podědíme komponentu:**
1. `use \Wakers\ConversionModule\Component\Frontend\ContactForm\Create;`
2. `{php $presenter->getComponent('appContactForm')->render('./form/contactForm.latte')}`

**Zaregistrujeme konverzní callback:**
```javascript
$.conversionAdd('contactForm', function (conversion) {
    // Send to Ads
    gtag('event', 'conversion', {
        'send_to': 'AW-677388384/pg6dCMaTirkBEODAgMMC',
        'value': conversion.value,
        'currency': 'CZK'
    });

    // Send goal to Analytics
    gtag('event', 'conversion', {
        'event_category' : 'form',
        'event_label' : 'contact',
        'value': conversion.value,
    });

    // Send to sklik
    $('body').append('<iframe width="0" height="0" frameborder="0" scrolling="no" src="//c.imedia.cz/checkConversion?c=100059016&amp;color=ffffff&amp;v=' + conversion.value +'" style="display: none !important; width: 1px !important; height: 1px !important; opacity: 0 !important; pointer-events: none !important;"></iframe>');

    // Send lead to Facebook
    fbq('track', 'Lead', {
        value: conversion.value,
        currency: 'CZK',
    });
});
```
