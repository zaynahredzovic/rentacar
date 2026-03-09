<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/rentacar/public/css/style.css">
    <link rel="stylesheet" href="/rentacar/public/css/dashboard.css">
    <title>Rent a Car - Dashboard</title>
</head>
<body class="dashboard-body">
    
    <!-- Navbar -->
    <nav class="dashboard-nav">
        <div class="nav-container">
            <div class="logo">
                <h2>Rent-a-Car</h2>
            </div>
            <div class="nav-links">
                <span class="welcome-text">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</span>
                <a href="/rentacar/logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Dashboard Container -->
    <div class="dashboard-container">
        
        <!-- Header -->
        <div class="dashboard-header">
            <h1>My Dashboard</h1>
            <p>Manage your cars and rentals</p>
        </div>

        <!-- Two Column Layout -->
        <div class="dashboard-grid">
            
            <!-- Left Column - Add Car Form -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Add New Car</h2>
                </div>
                <div class="card-body">
                    <form id="addCarForm" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for="title">Car Title</label>
                            <input type="text" id="title" name="title" placeholder="e.g., Toyota Corolla 2024" required>
                            <span class="error-text titleError"></span>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" placeholder="Describe your car..." rows="4" required></textarea>
                            <span class="error-text descriptionError"></span>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label for="price_per_day">Price Per Day ($)</label>
                                <input type="number" id="price_per_day" name="price_per_day" placeholder="50" step="0.01" min="0" required>
                                <span class="error-text priceError"></span>
                            </div>

                            <div class="form-group half">
                                <label for="category_id">Category</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <!-- Categories will be loaded via AJAX -->
                                </select>
                                <span class="error-text categoryError"></span>
                            </div>
                        </div>

                        <!-- New Category Section - NO NESTED FORM -->
                        <div class="form-group new-category-group" style="display: none;">
                            <label for="new_category">New Category Name</label>
                            <div class="input-with-button">
                                <input type="text" id="new_category" name="new_category" placeholder="Enter new category">
                                <button type="button" id="addCategoryBtn" class="btn-small">Add Category</button>
                            </div>
                            <span class="error-text newCategoryError"></span>
                        </div>

                        <div class="form-group">
                            <label for="image">Car Image</label>
                            <input type="file" id="image" name="image" accept="image/*" required>
                            <span class="error-text imageError"></span>
                            <div class="image-preview" id="imagePreview"></div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-container">
                                <input type="checkbox" id="active" name="active" value="1" checked>
                                <span class="checkmark"></span>
                                Available for rent
                            </label>
                        </div>

                        <button type="submit" id="addCarBtn" class="btn-primary">Add Car</button>
                        <div id="carResponseMsg" class="response-message"></div>
                    </form>
                </div>
            </div>

            <!-- Right Column - My Cars List -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>My Cars</h2>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="active">Active</button>
                        <button class="filter-btn" data-filter="inactive">Inactive</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="carsList" class="cars-grid">
                        <div class="loading-spinner">Loading cars...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/rentacar/public/js/jquery.min.js"></script>
    <script src="/rentacar/public/js/form.js"></script>
    <script src="/rentacar/public/js/dashboard.js"></script>
</body>
</html>