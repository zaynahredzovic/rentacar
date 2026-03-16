$(document).ready(function() {
    console.log('Dashboard initialized');
    
    // ============================================
    // LOAD CATEGORIES ON PAGE LOAD
    // ============================================
    CategoryAjax.loadCategories('#category_id');
    
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
    // REAL-TIME VALIDATION FOR CATEGORY
    // ============================================
    $('#new_category').on('input', function() {
        const value = $(this).val();
        const regex = /^[a-zA-Z0-9\s]*$/;
        
        if (value && !regex.test(value)) {
            $(this).css('border-color', '#dc3545');
            $('.newCategoryError').text('Only letters, numbers and spaces allowed');
        } else {
            $(this).css('border-color', '');
            $('.newCategoryError').text('');
        }
    });
    
    // ============================================
    // ADD CATEGORY BUTTON
    // ============================================
    $('#addCategoryBtn').on('click', function() {
        const name = $('#new_category').val().trim();
        
        // Clear previous errors
        $('.newCategoryError').text('');
        $('#new_category').css('border-color', '');
        
        // Validation
        if (!name) {
            $('.newCategoryError').text('Category name is required');
            $('#new_category').css('border-color', '#dc3545');
            return;
        }
        
        if (name.length < 2) {
            $('.newCategoryError').text('Category name must be at least 2 characters');
            $('#new_category').css('border-color', '#dc3545');
            return;
        }
        
        if (!/^[a-zA-Z0-9\s]+$/.test(name)) {
            $('.newCategoryError').text('No special characters allowed. Only letters, numbers and spaces.');
            $('#new_category').css('border-color', '#dc3545');
            return;
        }
        
        // Disable button
        $(this).prop('disabled', true).text('Adding...');
        
        // Add category via Ajax
        CategoryAjax.addCategory(name, 
            function(response) { // Success callback
                $('#addCategoryBtn').prop('disabled', false).text('Add Category');
                
                if (response.status === 'success') {
                    // Show success message
                    $('#carResponseMsg')
                        .removeClass('error')
                        .addClass('success')
                        .text('Category added successfully!')
                        .show();
                    
                    // Clear input and hide section
                    $('#new_category').val('');
                    $('#new_category').css('border-color', '');
                    $('.new-category-group').slideUp(200);
                    
                    // Reload categories dropdown
                    CategoryAjax.loadCategories('#category_id');
                    $('#category_id').val('');
                    
                    // Hide success message after 2 seconds
                    setTimeout(function() {
                        $('#carResponseMsg').fadeOut();
                    }, 2000);
                } else {
                    $('.newCategoryError').text(response.message || 'Failed to add category');
                    $('#new_category').css('border-color', '#dc3545');
                }
            },
            function(xhr) { // Error callback
                $('#addCategoryBtn').prop('disabled', false).text('Add Category');
                
                let errorMessage = 'Failed to add category';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('.newCategoryError').text(errorMessage);
                $('#new_category').css('border-color', '#dc3545');
                
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
    // ADD CAR FORM
    // ============================================
    $('#addCarForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get selected category
        const selectedCategory = $('#category_id').val();
        
        // Validate category
        if (selectedCategory === 'new') {
            $('.categoryError').text('Please add the new category first or select an existing one');
            return false;
        }
        
        if (!selectedCategory) {
            $('.categoryError').text('Please select a category');
            return false;
        }
        
        // Clear previous messages
        $('#carResponseMsg').removeClass('success error').hide();
        $('.error-text').text('');
        
        // Get form values
        const title = $('#title').val().trim();
        const description = $('#description').val().trim();
        const price = $('#price_per_day').val();
        const imageFile = $('#image')[0].files[0];
        
        // Validate form fields
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
        
        // Disable button
        $('#addCarBtn').prop('disabled', true).text('Adding Car...');
        
        // Add car via Ajax
        CarAjax.addCar(this, 
            function(response) { // Success callback
                $('#addCarBtn').prop('disabled', false).text('Add Car');
                
                if (response.status === 'success') {
                    // Show success message
                    $('#carResponseMsg')
                        .removeClass('error')
                        .addClass('success')
                        .text('Car added successfully!')
                        .show();
                    
                    // Reset form
                    $('#addCarForm')[0].reset();
                    $('#imagePreview').empty();
                    
                    // Reload cars list
                    CarAjax.loadCars('all', function(res) {
                        if (res.status === 'success') {
                            displayCars(res.data);
                        }
                    });
                    
                    // Hide success message after 3 seconds
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
            function(xhr) { // Error callback
                $('#addCarBtn').prop('disabled', false).text('Add Car');
                
                let errorMessage = 'Failed to add car';
                
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                    
                    // Display field-specific errors
                    if (xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.title) $('.titleError').text(xhr.responseJSON.errors.title[0]);
                        if (xhr.responseJSON.errors.description) $('.descriptionError').text(xhr.responseJSON.errors.description[0]);
                        if (xhr.responseJSON.errors.price_per_day) $('.priceError').text(xhr.responseJSON.errors.price_per_day[0]);
                        if (xhr.responseJSON.errors.category_id) $('.categoryError').text(xhr.responseJSON.errors.category_id[0]);
                        if (xhr.responseJSON.errors.image) $('.imageError').text(xhr.responseJSON.errors.image[0]);
                        return;
                    }
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
    // DISPLAY CARS FUNCTION
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
            
            // Fix the image path
            let imageUrl = car.image_path;
            if (imageUrl) {
                // If image path doesn't start with http or /, add /rentacar/
                if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/rentacar')) {
                    imageUrl = '/rentacar' + (imageUrl.startsWith('/') ? imageUrl : '/' + imageUrl);
                }
            } else {
                // Default image if no image path
                imageUrl = '/rentacar/public/images/default-car.jpg';
            }
            
            // Truncate description if too long
            const shortDescription = car.description.length > 100 
                ? car.description.substring(0, 100) + '...' 
                : car.description;
            
            html += `
                <div class="car-card" data-id="${car.id}">
                    <div class="car-image">
                        <img src="${imageUrl}" alt="${escapeHtml(car.title)}" 
                             onerror="this.src='/rentacar/public/images/default-car.jpg'">
                    </div>
                    <div class="car-details">
                        <div class="car-title">
                            <h3>${escapeHtml(car.title)}</h3>
                            <span class="car-status ${statusClass}">${statusText}</span>
                        </div>
                        <p class="car-description">${escapeHtml(shortDescription)}</p>
                        <div class="car-meta">
                            <span class="car-price">$${parseFloat(car.price_per_day).toFixed(2)}/day</span>
                            <span class="car-category">${escapeHtml(car.category_name || 'Uncategorized')}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#carsList').html(html);
    }
    
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
    // FILTER BUTTONS
    // ============================================
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        
        CarAjax.loadCars(filter, function(res) {
            if (res.status === 'success') {
                displayCars(res.data);
            }
        });
    });
    
    // ============================================
    // IMAGE PREVIEW
    // ============================================
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                $('.imageError').text('Please select a valid image file (JPEG, PNG, GIF)');
                $(this).val('');
                $('#imagePreview').empty();
                return;
            }
            
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                $('.imageError').text('Image size should be less than 5MB');
                $(this).val('');
                $('#imagePreview').empty();
                return;
            }
            
            $('.imageError').text('');
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`<img src="${e.target.result}" style="max-width:100%; max-height:200px; border-radius:8px; margin-top:10px;">`);
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
    // LOAD CARS ON PAGE LOAD
    // ============================================
    CarAjax.loadCars('all', function(res) {
        if (res.status === 'success') {
            displayCars(res.data);
        }
    });
});