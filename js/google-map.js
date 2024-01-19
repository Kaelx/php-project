function initialize() {
    var input = document.getElementById('address');
    new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        componentRestrictions: {country: "ph"}
    });
}

window.initialize = initialize;