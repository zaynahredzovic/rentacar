$(document).ready(function() {
    
    // ============================================
    // LOAD CATEGORIES FOR DROPDOWN
    // ============================================
    function loadCategories() {
        $.ajax({
            url: '/rentacar/api/categories',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const $select = $('#category_id');
                    const currentVal = $select.val();
                    
                    // Clear all options except first
                    $select.find('option').slice(1).remove();
                    
                    // Add categories
                    response.data.forEach(function(category) {
                        $select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                    
                    // Add the "Add New" option at the end
                    $select.append(`<option value="new">+ Add New Category</option>`);
                    
                    // Restore selection if it still exists
                    if (currentVal && currentVal !== 'new') {
                        const stillExists = response.data.some(c => c.id == currentVal);
                        if (stillExists) {
                            $select.val(currentVal);
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load categories:', error);
            }
        });
    }
    
    // ============================================
    // TOGGLE NEW CATEGORY INPUT
    // ============================================
    $('#category_id').on('change', function() {
        if ($(this).val() === 'new') {
            $('.new-category-group').slideDown(200);
            $('#new_category').focus();
        } else {
            $('.new-category-group').slideUp(200);
            $('#new_category').val('');
            $('.newCategoryError').text('');
            $('#new_category').css('border-color', '');
        }
    });
    
    // ============================================
    // REAL-TIME VALIDATION FOR CATEGORY INPUT
    // ============================================
    $('#new_category').on('input', function() {
        const value = $(this).val();
        const regex = /^[a-zA-Z0-9\s]*$/;
        
        if (value && !regex.test(value)) {
            $(this).css('border-color', '#dc3545');
            $('.newCategoryError').text('Special characters are not allowed. Only letters, numbers and spaces.');
        } else {
            $(this).css('border-color', '');
            $('.newCategoryError').text('');
        }
    });
    
    // ============================================
    // ADD NEW CATEGORY - USING FORM.JS
    // ============================================
    $('#addCategoryBtn').on('click', function(e) {
        e.preventDefault();
        
        const categoryName = $('#new_category').val().trim();
        
        // Clear previous errors
        $('.newCategoryError').text('');
        $('#new_category').css('border-color', '');
        
        // Validation
        if (!categoryName) {
            $('.newCategoryError').text('Category name is required');
            $('#new_category').css('border-color', '#dc3545');
            return;
        }
        
        if (categoryName.length < 2) {
            $('.newCategoryError').text('Category name must be at least 2 characters');
            $('#new_category').css('border-color', '#dc3545');
            return;
        }
        
        // REGEX VALIDATION - Block special characters
        const regex = /^[a-zA-Z0-9\s]+$/;
        if (!regex.test(categoryName)) {
            $('.newCategoryError').text('Category name cannot contain special characters. Only letters, numbers and spaces are allowed.');
            $('#new_category').css('border-color', '#dc3545');
            return;
        }
        
        // Disable button to prevent double submission
        $(this).prop('disabled', true);
        
        // Use the form.js helper function
        submitForm(
            'addCategoryForm',
            '/rentacar/api/categories',
            'POST',
            { name: categoryName },
            function(response) { // Success callback
                console.log('Category added:', response);
                
                // Re-enable button
                $('#addCategoryBtn').prop('disabled', false);
                
                if (response.status === 'success') {
                    $('#carResponseMsg')
                        .removeClass('error')
                        .addClass('success')
                        .text('Category added successfully!')
                        .show();
                    
                    $('#new_category').val('');
                    $('#new_category').css('border-color', '');
                    loadCategories();
                    $('.new-category-group').slideUp(200);
                    $('#category_id').val('');
                    
                    setTimeout(function() {
                        $('#carResponseMsg').fadeOut();
                    }, 2000);
                } else {
                    $('.newCategoryError').text(response.message || 'Failed to add category');
                    $('#new_category').css('border-color', '#dc3545');
                }
            },
            function(xhr, status, error) { // Error callback
                console.error('Error adding category:', error);
                
                // Re-enable button
                $('#addCategoryBtn').prop('disabled', false);
                
                let errorMessage = 'Failed to add category';
                
                // Try to get error message from response
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                } else {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch(e) {
                        if (xhr.status === 409) {
                            errorMessage = 'Category already exists';
                        } else if (xhr.status === 400) {
                            errorMessage = 'Invalid category name. Only letters, numbers and spaces are allowed.';
                        }
                    }
                }
                
                // DISPLAY ERROR MESSAGE IN UI
                $('.newCategoryError').text(errorMessage);
                $('#new_category').css('border-color', '#dc3545');
                
                // Also show in main response message
                $('#carResponseMsg')
                    .removeClass('success')
                    .addClass('error')
                    .text(errorMessage)
                    .show();
                
                setTimeout(function() {
                    $('#carResponseMsg').fadeOut();
                }, 3000);
            }
        );
    });
    
    // ============================================
    // ADD CAR FORM - USING FORM.JS
    // ============================================
    $('#addCarForm').on('submit', function(e) {
        e.preventDefault();
        
        const selectedCategory = $('#category_id').val();
        
        if (selectedCategory === 'new') {
            $('.categoryError').text('Please add the new category first or select an existing one');
            return false;
        }
        
        if (!selectedCategory) {
            $('.categoryError').text('Please select a category');
            return false;
        }
        
        $('#carResponseMsg').removeClass('success error').hide();
        $('.error-text').text('');
        
        const title = $('#title').val().trim();
        const description = $('#description').val().trim();
        const price = $('#price_per_day').val();
        const imageFile = $('#image')[0].files[0];
        
        let isValid = true;
        
        if (!title) {
            $('.titleError').text('Title is required');
            isValid = false;
        }
        
        if (!description) {
            $('.descriptionError').text('Description is required');
            isValid = false;
        }
        
        if (!price || price <= 0) {
            $('.priceError').text('Valid price is required');
            isValid = false;
        }
        
        if (!imageFile) {
            $('.imageError').text('Please select an image');
            isValid = false;
        }
        
        if (!isValid) return false;
        
        // Disable submit button
        $('#addCarBtn').prop('disabled', true);
        
        // Create FormData for file upload
        const formData = new FormData(this);
        
        // Convert FormData to plain object for form.js
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });
        
        // Use the form.js helper function
        submitForm(
            'addCarForm',
            '/rentacar/api/cars',
            'POST',
            formDataObj,
            function(response) { // Success callback
                console.log('Car added:', response);
                
                // Re-enable button
                $('#addCarBtn').prop('disabled', false);
                
                if (response.status === 'success') {
                    $('#carResponseMsg')
                        .removeClass('error')
                        .addClass('success')
                        .text('Car added successfully!')
                        .show();
                    
                    $('#addCarForm')[0].reset();
                    $('#imagePreview').empty();
                    loadUserCars();
                    
                    setTimeout(function() {
                        $('#carResponseMsg').fadeOut();
                    }, 3000);
                } else {
                    $('#carResponseMsg')
                        .removeClass('success')
                        .addClass('error')
                        .text(response.message || 'Failed to add car')
                        .show();
                }
            },
            function(xhr, status, error) { // Error callback
                console.error('Error adding car:', error);
                
                // Re-enable button
                $('#addCarBtn').prop('disabled', false);
                
                let errorMessage = 'Failed to add car';
                
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                    
                    if (xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.title) $('.titleError').text(xhr.responseJSON.errors.title[0]);
                        if (xhr.responseJSON.errors.description) $('.descriptionError').text(xhr.responseJSON.errors.description[0]);
                        if (xhr.responseJSON.errors.price_per_day) $('.priceError').text(xhr.responseJSON.errors.price_per_day[0]);
                        if (xhr.responseJSON.errors.category_id) $('.categoryError').text(xhr.responseJSON.errors.category_id[0]);
                        if (xhr.responseJSON.errors.image) $('.imageError').text(xhr.responseJSON.errors.image[0]);
                        return;
                    }
                } else {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch(e) {}
                }
                
                $('#carResponseMsg')
                    .removeClass('success')
                    .addClass('error')
                    .text(errorMessage)
                    .show();
            }
        );
    });
    
    // ============================================
    // LOAD USER CARS
    // ============================================
    function loadUserCars(filter = 'all') {
        $('#carsList').html('<div class="loading-spinner">Loading cars...</div>');
        
        $.ajax({
            url: '/rentacar/api/cars',
            type: 'GET',
            data: { filter: filter },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayCars(response.data);
                } else {
                    $('#carsList').html('<div class="no-cars">No cars found</div>');
                }
            },
            error: function() {
                $('#carsList').html('<div class="error-message">Failed to load cars</div>');
            }
        });
    }
    
    // ============================================
    // DISPLAY CARS
    // ============================================
    function displayCars(cars) {
        if (!cars || cars.length === 0) {
            $('#carsList').html('<div class="no-cars">No cars found</div>');
            return;
        }
        
        let html = '';
        
        cars.forEach(function(car) {
            const statusClass = car.active == 1 ? 'active' : 'inactive';
            const statusText = car.active == 1 ? 'Active' : 'Inactive';
            const imageUrl = car.image_path || '/rentacar/public/images/default-car.jpg';
            
            html += `
                <div class="car-card" data-id="${car.id}">
                    <div class="car-image">
                        <img src="${imageUrl}" alt="${escapeHtml(car.title)}">
                    </div>
                    <div class="car-details">
                        <div class="car-title">
                            <h3>${escapeHtml(car.title)}</h3>
                            <span class="car-status ${statusClass}">${statusText}</span>
                        </div>
                        <p class="car-description">${escapeHtml(car.description.substring(0, 100))}...</p>
                        <div class="car-meta">
                            <span class="car-price">$${car.price_per_day}/day</span>
                            <span class="car-category">${escapeHtml(car.category_name)}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#carsList').html(html);
    }
    
    // ============================================
    // FILTER BUTTONS
    // ============================================
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        loadUserCars($(this).data('filter'));
    });
    
    // ============================================
    // HELPER: ESCAPE HTML
    // ============================================
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // ============================================
    // IMAGE PREVIEW
    // ============================================
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                $('.imageError').text('Please select a valid image file (JPEG, PNG, GIF)');
                $(this).val('');
                $('#imagePreview').empty();
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                $('.imageError').text('Image size should be less than 5MB');
                $(this).val('');
                $('#imagePreview').empty();
                return;
            }
            
            $('.imageError').text('');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`<img src="${e.target.result}" style="max-width:100%; border-radius:8px;">`);
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').empty();
        }
    });
    
    // ============================================
    // PRESS ENTER IN CATEGORY INPUT
    // ============================================
    $('#new_category').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#addCategoryBtn').click();
        }
    });
    
    // ============================================
    // INITIAL LOAD
    // ============================================
    loadCategories();
    loadUserCars();
});