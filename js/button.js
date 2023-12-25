function confirmAction(productId, message, actionPath) {
    var result = confirm(message);
    if (result) {
        window.location.href = actionPath + productId;
    }
}

function confirmEdit(productId){
    confirmAction(productId, 'Are you sure?','edit.php?id=');
}
function confirmDelete(productId){
    confirmAction(productId, "Please Confirm to delete!", 'controller/delete.php?id=');
}

function confirmSold(productId){
    confirmAction(productId, "Mark this product sold?", 'controller/sold.php?id=');
}

function buyProduct(productId){
    confirmAction(productId, "Please Confirm!", 'controller/buy.php?id=');
}

function confirmCancel(productId){
    confirmAction(productId, "Please Confirm!", 'controller/cancel.php?id=');
}