function initialize() {
    var input = document.getElementById('address');
    var autocomplete = new google.maps.places.Autocomplete(input);
}

// Ensure the initialize function is in the global scope
window.initialize = initialize;