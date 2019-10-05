var APP_ID = "php-serverless-unique-guid"; // Your APP unique ID
var WEBSITE_URL = "{{ $websiteUrl }}"; // Your website root URL
var API_URL = "{{ $apiUrl }}"; // Your website API URL
if ( location.hostname === "localhost" || location.hostname === "127.0.0.1" || location.hostname === "") {
WEBSITE_URL = window.location.protocol + "//" + window.location.hostname + ":" + window.location.port;
}