document.addEventListener("DOMContentLoaded", function () {
    var cookieConsent = document.querySelector('.ch-cookie-consent');
    var cookieConsentForm = document.querySelector('.ch-cookie-consent__form');
    var cookieConsentFormBtn = document.querySelectorAll('.ch-cookie-consent__btn');
    var cookieConsentCategoryDetails = document.querySelector('.ch-cookie-consent__category-group');
    var cookieConsentCategoryDetailsToggle = document.querySelector('.ch-cookie-consent__toggle-details');
    var cookieConsentStandalone = document.querySelector('.ch-cookie-consent--standalone');
    var cookieConsentCookieInfoDetailsToggle = document.querySelectorAll('.ch-cookie-consent__toggle-cookie-information');
    var cookieConsentCookieInfoDetails = document.querySelectorAll('.ch-cookie-consent__category-description-details');

    if (cookieConsentForm) {
        // Submit form via ajax
        cookieConsentFormBtn.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                var xhr = new XMLHttpRequest();
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (cookieConsentStandalone) {
                            window.location.assign("/");
                        } else {
                            cookieConsent.style.display = 'none';
                        }
                    }
                };
                xhr.open('POST', cookieConsentForm.action);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(serializeForm(cookieConsentForm, event.target));

                var buttonEvent = new CustomEvent('cookie-consent-form-button-click', {
                    detail: event.target
                });
                document.dispatchEvent(buttonEvent);

                // Clear all styles from body to prevent the white margin at the end of the page
		document.body.style.marginBottom = null;
		document.body.style.marginTop  = null;
            }, false);
        });
    }

    // main toggle
    if (!cookieConsentStandalone) {
        cookieConsentCategoryDetailsToggle.addEventListener('click', function () {
            var detailsIsHidden = cookieConsentCategoryDetails.style.display !== 'block';
            cookieConsentCategoryDetails.style.display = detailsIsHidden ? 'block' : 'none';
            cookieConsentCategoryDetailsToggle.querySelector('.ch-cookie-consent__toggle-details-hide').style.display = detailsIsHidden ? 'block' : 'none';
            cookieConsentCategoryDetailsToggle.querySelector('.ch-cookie-consent__toggle-details-show').style.display = detailsIsHidden ? 'none' : 'block';
            cookieConsentCookieInfoDetails.forEach(function (container) {
                container.style.display = 'none';
                document.querySelectorAll('.ch-cookie-consent__toggle-cookie-information').forEach(function (el) {
                    el.querySelector('.ch-cookie-consent__toggle-cookie-information-hide').style.display = 'none';
                    el.querySelector('.ch-cookie-consent__toggle-cookie-information-show').style.display = 'inline-block';
                });
            });
        });
    }

    // category details toggle
    cookieConsentCookieInfoDetailsToggle.forEach(function (element) {
        element.addEventListener('click', function (event) {
            var rel = event.target.getAttribute("rel");
            var currentCookieConsentCookieInfoDetails = document.querySelector('#' + rel);
            var detailsIsHidden = currentCookieConsentCookieInfoDetails.style.display !== 'block';
            cookieConsentCookieInfoDetails.forEach(function (container) {
                container.style.display = detailsIsHidden ? 'none' : container.style.display;
                document.querySelectorAll('.ch-cookie-consent__toggle-cookie-information').forEach(function (el) {
                    el.querySelector('.ch-cookie-consent__toggle-cookie-information-hide').style.display = 'none';
                    el.querySelector('.ch-cookie-consent__toggle-cookie-information-show').style.display = 'inline-block';
                });
            });
            currentCookieConsentCookieInfoDetails.style.display = detailsIsHidden ? 'block' : 'none';
            element.querySelector('.ch-cookie-consent__toggle-cookie-information-hide').style.display = detailsIsHidden ? 'inline-block' : 'none';
            element.querySelector('.ch-cookie-consent__toggle-cookie-information-show').style.display = detailsIsHidden ? 'none' : 'inline-block';
        });
    });

});

function serializeForm(form, clickedButton) {
    var serialized = [];
    for (var i = 0; i < form.elements.length; i++) {
        var field = form.elements[i];

        if ((field.type !== 'checkbox' && field.type !== 'radio' && field.type !== 'button') || field.checked) {
            serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
        }
    }
    serialized.push(encodeURIComponent(clickedButton.getAttribute('name')) + "=");
    return serialized.join('&');
}
