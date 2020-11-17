[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ConnectHolland/cookie-consent-bundle/badges/quality-score.png?b=master&s=15b793ae2474fa313d343c43f30ce4f9aa594f00)](https://scrutinizer-ci.com/g/ConnectHolland/cookie-consent-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ConnectHolland/cookie-consent-bundle/badges/coverage.png?b=master&s=d8e84bcf2e3e5bed47d4c6aa4702f246de74dbdf)](https://scrutinizer-ci.com/g/ConnectHolland/cookie-consent-bundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ConnectHolland/cookie-consent-bundle/badges/build.png?b=master&s=bcccde957df75df8622fa346ba348dee002efebb)](https://scrutinizer-ci.com/g/ConnectHolland/cookie-consent-bundle/build-status/master)


# Cookie Consent bundle for Symfony
Symfony bundle to append Cookie Consent to your website to comply to AVG/GDPR for cookies.

## Installation

### Step 1: Download using composer
In a Symfony application run this command to install and integrate Cookie Consent bundle in your application:
```bash
composer require connectholland/cookie-consent-bundle
```

### Step 2: Enable the bundle
When not using symfony flex, enable the bundle in the kernel manually:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new ConnectHolland\CookieConsentBundle\CHCookieConsentBundle(),
        // ...
    );
}
```

### Step 3: Enable the routing
When not using symfony flex, enable the bundles routing manually:
```yaml
# app/config/routing.yml
ch_cookie_consent:
    resource: "@CHCookieConsentBundle/Resources/config/routing.yaml"
```

### Step 4: Configure to your needs
Configure your Cookie Consent with the following possible settings
```yaml
ch_cookie_consent:
    theme: 'light' # light, dark
    essentials: # Below are the default essential cookies
        - 'cookie_consent'
        - 'phpsessid'
        - 'sf_redirect'
    categories: # Below are the default supported categories
        - 'analytics'
        - 'tracking'
        - 'marketing'
        - 'social_media'
    use_logger: true # Logs user actions to database
    position: 'top' # top, bottom
```

### Step 5: Add a link on your site (optional) 
Add a link in your footer to review privacy settings, if you want.
```twig
<a href="{{ path('home', {'_cookie_consent_review': '1'}) }}">Review Privacy Settings</a>
``` 

## Usage
### Twig implementation
Load the cookie consent in Twig via render_esi ( to prevent caching ) at any place you like:
```twig
{{ render_esi(path('ch_cookie_consent.show')) }}
{{ render_esi(path('ch_cookie_consent.show_if_cookie_consent_not_set')) }}
```

If you want to load the cookie consent with a specific locale you can pass the locale as a parameter:
```twig
{{ render_esi(path('ch_cookie_consent.show', { 'locale' : 'en' })) }}
{{ render_esi(path('ch_cookie_consent.show_if_cookie_consent_not_set', { 'locale' : app.request.locale })) }}
```

### Cookies
When a user submits the form the preferences are saved as cookies. The cookies have a lifetime of 1 year. The following cookies are saved:
- **Cookie_Consent**: date of submit
- **Cookie_Consent_Key**: Generated key as identifier to the submitted Cookie Consent of the user
- **Cookie_Category_[CATEGORY]**: selected value of user (*true* or *false*)

### Logging
AVG/GDPR requires all given cookie preferences of users to be explainable by the webmasters. For this we log all cookie preferences to the database. IP addresses are anonymized. This option can be disabled in the config.

![Database logging](https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/log.png)

### Themes
![Dark Theme](https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/dark_theme.png)
![Light Theme](https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/light_theme.png)

### TwigExtension
The following TwigExtension functions are available:

**chcookieconsent_isCategoryAllowedByUser**
check if user has given it's permission for certain cookie categories
```twig
{% if chcookieconsent_isCategoryAllowedByUser('analytics') == true %}
    ...
{% endif %}
```

**chcookieconsent_isCookieConsentSavedByUser**
check if user has saved any cookie preferences
```twig
{% if chcookieconsent_isCookieConsentSavedByUser() == true %}
    ...
{% endif %}
```


## Customization
### Categories
You can add or remove any category by changing the config and making sure there are translations available for these categories.

### Translations
Translations are currently available for EN, NL and DE. All texts can be altered via Symfony translations by overwriting the CHCookieConsentBundle translation files.

### Styling

#### Option 1
CHCookieConsentBundle comes with a default styling. A sass file is available in Resources/assets/css/cookie_consent.scss and a build css file is available in Resources/public/css/cookie_consent.css. Colors can easily be adjusted by setting the variables available in the sass file.

To install these assets run:
```bash
bin/console assets:install
```

And include the styling in your template:
```twig
{% include "@CHCookieConsent/cookie_consent_styling.html.twig" %}
```

#### Option 2
If you want to integrate styling into your site's scss use an include in your scss (avoids style glitches):
```scss
/** configure colors here */
@import "vendor/connectholland/cookie-consent-bundle/Resources/assets/css/cookie_consent";
```

### Javascript
By loading Resources/public/js/cookie_consent.js the cookie consent will be submitted via ajax and the cookie consent will be shown on top of your website while pushing down the rest of the website.

### Events
When a form button is clicked, the event of cookie-consent-form-button-click is created. Use the following code to listen to the event and add your custom functionality.
```javascript
document.addEventListener('cookie-consent-form-button-click', function (e) {
    // ... your functionality
    // ... e.detail is available to see which button is clicked.
}, false);
```

### Template Themes
You can override the templates by placing templates inside your project:

```twig
# app/Resources/CHCookieConsentBundle/views/cookie_consent.html.twig
{% extends '@!CHCookieConsent/cookie_consent.html.twig' %}

{% block title %}
    Your custom title
{% endblock %}
```
