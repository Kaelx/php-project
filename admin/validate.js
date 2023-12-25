function validatelogin() {
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;

    if (email === "" || password === "") {
        alert("Please fill in all fields.");
        return false;
    }

    return true;
}


function confirmAction(productId, message, actionPath) {
    var result = confirm(message);
    if (result) {
        window.location.href = actionPath + productId;
    }
}

function banUser(userId){
    confirmAction(userId, 'Ban this account?','ban.php?id=');
}