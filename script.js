var ajaxurl = consentPopupAjax.ajaxurl;

// Function to set the cookie consent status
function setCookieConsent(consent) {
    document.cookie = "cookieConsent=" + consent + "; path=/;";
    location.reload(); // Refresh the page to apply the consent
}


function deleteAllCookies() {
    var cookies = document.cookie.split("; ");
    for (var c = 0; c < cookies.length; c++) {
        var d = window.location.hostname.split(".");
        while (d.length > 0) {
            var cookieBase = encodeURIComponent(cookies[c].split(";")[0].split("=")[0]) + '=; expires=Thu, 01-Jan-1970 00:00:01 GMT; domain=' + d.join('.') + ' ;path=';
            var p = location.pathname.split('/');
            document.cookie = cookieBase + '/';
            while (p.length > 0) {
                document.cookie = cookieBase + p.join('/');
                p.pop();
            }
            d.shift();
        }
    }
}


// Function to check if the cookie consent is given
function hasCookieConsent() {
    var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf("cookieConsent=") === 0) {
            return cookie.substring("cookieConsent=".length, cookie.length);
        }
    }
    return null;
}

var consent = hasCookieConsent();

// Show the cookie consent prompt if the consent is not given
if (consent !== "true") {
    // it was not given, kill all cookies
    deleteAllCookies();
}

if (consent === null) {
    // it has not been accepted or declined
    jQuery(document).ready(function($) {
        $("body").addClass("no-scroll");
        $.get(consentPopupAjax.ajaxurl, { action: 'get_consent_popup' }, function(response) {
            $("body").append(response);
            $("#cookieConsentPrompt").addClass("active");
        });
    });
}
if (consent === "true") {
    // do nothing, thank you!
}