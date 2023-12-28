function validateForm() {
    var email = document.getElementById('email').value;
    var firstname = document.getElementById('firstname').value;
    var lastname = document.getElementById('lastname').value;
    var address = document.getElementById('address').value;
    var contactNumber = document.getElementById('contact_number').value;


    var namex = /^[a-zA-Z ]+$/;
    var emailx = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    var addressx = /^[a-zA-Z0-9, .]+$/;


    if (!namex.test(firstname) || !namex.test(lastname)) {
        alert("Please enter a valid name without special characters.");
        return false;
    }

    if (!emailx.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (!addressx.test(address)) {
        alert("Please enter a valid address.");
        return false;
    }

    if (contactNumber.length < 11 || contactNumber.length > 11) {
        alert("Please enter a valid contact number.");
        return false;
    }


    return true;
}