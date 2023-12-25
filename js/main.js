document.getElementById("addProductButton").addEventListener("click", showAddProductForm);
function showAddProductForm() {
    var floatingForm = document.getElementById("floatingForm");
    var overlay = document.getElementById("overlay");

    floatingForm.style.display = "block";
    overlay.style.display = "block";
}

function cancelAddProduct() {
    var floatingForm = document.getElementById("floatingForm");
    var overlay = document.getElementById("overlay");

    floatingForm.style.display = "none";
    overlay.style.display = "none";
}



function submitProductForm() {
    var form = document.getElementById("productForm");
    var formData = new FormData(form);

    var formx = /^[a-zA-Z ]+$/;
    if(!formx.test(formData.get("productName"))){
        alert("Please enter a valid name without special characters.");
        return false;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", form.action, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            alert("Product added successfully");
            location.reload();
        }
    };
    xhr.send(formData);
}






