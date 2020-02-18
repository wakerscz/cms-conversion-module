# Conversion module
Modul umožňuje ukládat konverze do DB a zároveň obsahuje výchozí komponentu pro poptávkový formulář. 

## API
```php
// Pole hodnot array ['xyz' => 'abc']
$values = $form->getValues(TRUE);

// Uložení konverze do databáze
$conversion = $this->conversionManager->create('conversionName', new \Nette\Utils\DateTime, $values); // return Conversion;

// Přidej do values data z DB
$values = $values + [
    'name' => $conversion->getName(),
    'id' => $conversion->getId(),
    'createdAt' => $conversion->getCreatedAt('d.m.Y H:i:s')
];

// Odeslání notifikace na e-mail
$this->conversionManager->sendMail($values, 'Poptávkový formulář z Vašeho webu', "Dobrý den,\n\rz Vašeho webu byl právě odeslán kontaktní formulář.");

```

## Minimální konfigurace

### Připojíme soubory
1. V souboru app.neon:
`./../../vendor/wakers/cms-conversion-module/src/config/config.neon`

1. V souboru schema.xml:
`<external-schema filename="./../../vendor/wakers/cms-conversion-module/src/schema/conversion.xml" referenceOnly="FALSE"/>`

1. V souboru custom-frontend.js:
`./vendor/wakers/cms-conversion-module/src/assets/Frontend/conversion/conversion.js`

### Aktualizujeme databázi
1. `./sc propel model:build`
1. `./sc propel migration:diff`
1. `./sc propel migration:migrate`

### Připravíme komponentu
1. Zaregistrujeme v presenteru:
`use \Wakers\ConversionModule\Component\Frontend\ContactForm\Create;`

1. Vytoříme vlastní šablonu `./app/templates/form/contactForm.latte`
```latte
<form n:name="form">
    <label n:name="contact">Telefon / E-mail</label>
    <input n:name="contact">

    <button n:name="save" data-wakers-progress-button>
        CTA Text
    </button>
</form>
```

1. Vyrenderujeme vlastní šablonu
```latte
{php $presenter->getComponent('appContactForm')->render('./form/contactForm.latte')}
```

1. Nadefinujeme, co se má vykonat po odeslání formuláře:
```javascript
$.conversionAdd('conversionName', function (conversion) {
    // Send to Ads
    gtag('event', 'conversion', {
        'send_to': 'AW-000000000/xxxxxxxxxxxxxxxxxxxx',
        'value': conversion.value,
        'currency': conversion.currency
    });

    // Send goal to Analytics
    gtag('event', 'conversion', {
        'event_category' : 'form',
        'event_label' : 'contact',
        'value': conversion.value,
    });

    // Send to sklik
    $('body').append('<iframe width="0" height="0" frameborder="0" scrolling="no" src="//c.imedia.cz/checkConversion?c=000000000&amp;color=ffffff&amp;v=' + conversion.value +'" style="display: none !important; width: 1px !important; height: 1px !important; opacity: 0 !important; pointer-events: none !important;"></iframe>');

    // Send lead to Facebook
    fbq('track', 'Lead', {
        value: conversion.value,
        currency: conversion.currency,
    });
});
```


