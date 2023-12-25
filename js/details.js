const idElement = document.querySelector("#productId");
const nameElement = document.querySelector("#productName");
const quantityElement = document.querySelector("#productQuantity");
const priceElement = document.querySelector("#productPrice");
const sellerElement = document.querySelector("#sellerName");
const addressElement = document.querySelector("#address");
const contactNumElement = document.querySelector("#contactNumber");
const emailElement = document.querySelector("#email");


function showDetailsForm(productId, productName, productQuantity, productPrice, sellerName, address, contactNumber, email) {
    idElement.innerHTML = productId;
    nameElement.innerHTML = productName;
    quantityElement.innerHTML = productQuantity;
    priceElement.innerHTML = productPrice;
    sellerElement.innerHTML = sellerName;
    addressElement.innerHTML = address;
    contactNumElement.innerHTML = contactNumber;
    emailElement.innerHTML = email;



    detailsForm.style.display = 'block';
    overlay.style.display = 'block';
    document.body.classList.add('darken-bg');
}






function hideDetailsForm() {


    detailsForm.style.display = 'none';
    overlay.style.display = 'none';
    document.body.classList.remove('darken-bg');
}