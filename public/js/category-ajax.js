// Category Ajax
const CategoryAjax = {
    loadCategories: function(selectElement) {
        $.ajax({
            url: '/rentacar/api/categories',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const $select = $(selectElement);
                    const currentVal = $select.val();
                    
                    $select.find('option').slice(1).remove();
                    
                    response.data.forEach(function(category) {
                        $select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                    
                    $select.append(`<option value="new">+ Add New Category</option>`);
                    
                    if (currentVal && currentVal !== 'new') {
                        $select.val(currentVal);
                    }
                }
            }
        });
    },
    
    addCategory: function(name, successCallback, errorCallback) {
        $.ajax({
            url: '/rentacar/api/categories',
            type: 'POST',
            data: { name: name },
            dataType: 'json',
            success: successCallback,
            error: errorCallback
        });
    }
};