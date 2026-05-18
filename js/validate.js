function showError(msg, text){
    msg.innerHTML = text;
    msg.style.display = 'block';
}

function clearError(msg){
    msg.innerHTML = '';
    msg.style.display = 'none';
}

function validateCategoryForm(){
    var name = document.getElementById('name').value.trim();
    var msg  = document.getElementById('msg');
    if(name == ""){
        showError(msg, 'Please enter a category name.');
        return false;
    }
    if(name.length > 100){
        showError(msg, 'Category name too long (max 100).');
        return false;
    }
    clearError(msg);
    return true;
}

function validateBrandForm(){
    var name = document.getElementById('name').value.trim();
    var cat  = document.getElementById('category_id').value;
    var msg  = document.getElementById('msg');
    if(name == ""){
        showError(msg, 'Please enter a brand name.');
        return false;
    }
    if(cat == ""){
        showError(msg, 'Please select a category.');
        return false;
    }
    clearError(msg);
    return true;
}

function validateProductForm(){
    var name  = document.getElementById('name').value.trim();
    var price = document.getElementById('price').value;
    var cat   = document.getElementById('category_id').value;
    var brand = document.getElementById('brand_id').value;
    var stock = document.getElementById('stock').value;
    var img   = document.getElementById('image');
    var msg   = document.getElementById('msg');

    if(name == ""){
        showError(msg, 'Product name is required.');
        return false;
    }
    if(cat == ""){
        showError(msg, 'Please select a category.');
        return false;
    }
    if(brand == ""){
        showError(msg, 'Please select a brand.');
        return false;
    }
    if(price == "" || isNaN(price) || parseFloat(price) <= 0){
        showError(msg, 'Price must be a positive number.');
        return false;
    }
    if(stock == "" || isNaN(stock) || parseInt(stock) < 0){
        showError(msg, 'Stock must be a non-negative number.');
        return false;
    }
    // image is optional, but if a file is selected, check size and type
    if(img && img.files.length > 0){
        var f = img.files[0];
        if(f.type != 'image/jpeg' && f.type != 'image/png'){
            showError(msg, 'Image must be JPEG or PNG.');
            return false;
        }
        if(f.size > 2 * 1024 * 1024){
            showError(msg, 'Image must be 2MB or less.');
            return false;
        }
    }
    clearError(msg);
    return true;
}

function loadBrandsByCategory(){
    var cat   = document.getElementById('category_id').value;
    var brand = document.getElementById('brand_id');
    brand.innerHTML = '<option value="">-- Loading... --</option>';

    if(cat == ""){
        brand.innerHTML = '<option value="">-- Select Category First --</option>';
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.open('GET', '../ajax/getBrands.php?category_id=' + cat, true);
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var res = JSON.parse(this.responseText);
            var html = '<option value="">-- Select Brand --</option>';
            if(res.status == 'ok'){
                for(var i = 0; i < res.brands.length; i++){
                    html += '<option value="' + res.brands[i].id + '">'
                          + res.brands[i].name + '</option>';
                }
            }
            brand.innerHTML = html;
        }
    };
    xhttp.send();
}
