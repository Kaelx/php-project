function initialize() {
    var input = document.getElementById('address');
    var options = {
        componentRestrictions: {country: "ph"}
    };
    var autocomplete = new google.maps.places.Autocomplete(input, options);
}

window.initialize = initialize;