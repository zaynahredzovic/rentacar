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
            }
        });
    }
    
    // ============================================
    // TOGGLE NEW CATEGORY INPUT
    // ============================================
    $('#category_id').on('change', function() {
        if ($(this).val() === 'new') {
            $('.new-category-group').slideDown(200);
        } else {
            $('.new-category-group').slideUp(200);
        }
    });
    
    // ============================================
    // ADD NEW CATEGORY
    // ============================================
    $('#addCategoryBtn').on('click', function() {
        const categoryName = $('#new_category').val().trim();
        
        if (!categoryName) {
            $('.newCategoryError').text('Category name is required');
            return;
        }
        
        $('.newCategoryError').text('');
        
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.text('Adding...').prop('disabled', true);
        
        $.ajax({
            url: '/rentacar/api/categories',
            type: 'POST',
            data: { name: categoryName },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#carResponseMsg')
                        .removeClass('error')
                        .addClass('success')
                        .text('Category added successfully!')
                        .show();
                    
                    $('#new_category').val('');
                    loadCategories();
                    $('.new-category-group').slideUp(200);
                    $('#category_id').val('');
                    
                    setTimeout(function() {
                        $('#carResponseMsg').fadeOut();
                    }, 2000);
                } else {
                    $('.newCategoryError').text(response.message);
                }
            },
            error: function() {
                $('.newCategoryError').text('Failed to add category');
            },
            complete: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // ============================================
    // ADD CAR FORM
    // ============================================
    $('#addCarForm').on('submit', function(e) {
        e.preventDefault();
        
        const selectedCategory = $('#category_id').val();
        
        if (selectedCategory === 'new' || !selectedCategory) {
            $('.categoryError').text('Please select a valid category');
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
        
        const formData = new FormData(this);
        
        const $btn = $('#addCarBtn');
        const originalText = $btn.text();
        $btn.text('Adding Car...').prop('disabled', true);
        
        $.ajax({
            url: '/rentacar/api/cars',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
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
            error: function(xhr) {
                let errorMessage = 'Failed to add car';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch(e) {}
                
                $('#carResponseMsg')
                    .removeClass('success')
                    .addClass('error')
                    .text(errorMessage)
                    .show();
            },
            complete: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
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
    // INITIAL LOAD
    // ============================================
    loadCategories();
    loadUserCars();
});