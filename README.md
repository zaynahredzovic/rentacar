
## Project Overview
A fully functional Rent-a-Car web application built with PHP OOP, MVC architecture, MySQL, and AJAX. Users can register, login, manage car categories, and add or display their cars through an intuitive dashboard interface.

## Features
- **User Authentication**: Secure registration and login system with password hashing
- **Protected Dashboard**: Personalized area accessible only to authenticated users
- **Category Management**: Dynamic addition of car categories during listing creation
- **Car Management**: Complete CRUD operations for car listings with image upload
- **AJAX Integration**: Seamless form submissions without page reloads
- **Image Upload**: Support for car images with preview functionality
- **Filter System**: Toggle between active and inactive car listings

## Technology Stack
- **Backend**: PHP 8.2 (Object-Oriented Programming, MVC Architecture)
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Database**: MySQL with PDO prepared statements
- **Routing**: FastRoute (nikic/fast-route) for clean URL routing
- **Server**: Apache (XAMPP development environment)
- **Version Control**: Git for source code management

## Project Structure
```
rentacar/
├── app/
│   ├── Controllers/        
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── CarController.php
│   │   └── DashboardController.php
│   ├── Models/             
│   │   ├── User.php
│   │   ├── Category.php
│   │   └── Car.php
│   ├── Views/              
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   ├── signup.php
│   │   │   └── dashboard.php
│   │   └── layouts/        
│   └── Core/               
│       ├── Database.php
│       ├── dbConfig.php
│       ├── Session.php
│       └── Env.php
├── public/
│   ├── css/
│   │   ├── style.css
│   │   └── dashboard.css
│   ├── js/
│   │   ├── jquery.min.js
│   │   ├── form.js
│   │   ├── login.js
│   │   ├── signup.js
│   │   └── dashboard.js
│   └── uploads/
│       └── cars/           
├── vendor/                  
├── .env                     
├── .htaccess                
├── index.php                
├── composer.json            
└── rentacar.sql             
```

## Database Schema

### Table Structure

#### Users Table
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PRIMARY KEY AUTO_INCREMENT | Unique user identifier |
| name | VARCHAR(100) | NOT NULL | User's full name |
| email | VARCHAR(100) | UNIQUE NOT NULL | User's email address |
| pwd | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation timestamp |

#### Categories Table
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PRIMARY KEY AUTO_INCREMENT | Unique category identifier |
| name | VARCHAR(100) | NOT NULL | Category name |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |

#### Cars Table
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PRIMARY KEY AUTO_INCREMENT | Unique car identifier |
| user_id | INT(11) | FOREIGN KEY (users.id) | Owner reference |
| category_id | INT(11) | FOREIGN KEY (categories.id) | Category reference |
| title | VARCHAR(255) | NOT NULL | Car listing title |
| description | TEXT | NULL | Detailed car description |
| image_path | VARCHAR(255) | NULL | Path to uploaded image |
| price_per_day | DECIMAL(10,2) | NOT NULL | Daily rental rate |
| active | TINYINT(1) | DEFAULT 1 | Listing status (1=active, 0=inactive) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Listing creation timestamp |

### Relationship Diagram
- One-to-Many: User → Cars
- One-to-Many: Category → Cars
- Foreign key constraints with ON DELETE CASCADE

## Installation Guide

### System Requirements
- XAMPP (Apache 2.4, MySQL 5.7+, PHP 8.2+)
- Composer (Dependency Manager for PHP)
- Modern web browser (Chrome, Firefox, Edge)

### Step-by-Step Installation

1. **Clone or Download the Project**
   ```bash
   git clone <repository-url>
   # Or download and extract the ZIP archive to your XAMPP htdocs folder
   ```

2. **Move to XAMPP Directory**
   ```bash
   # Ensure the project folder is located at:
   C:\xampp\htdocs\rentacar\
   ```

3. **Database Setup**
   - Launch XAMPP Control Panel
   - Start Apache and MySQL services
   - Access phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database named `rentacar`
   - Select the database and click the "Import" tab
   - Choose the `rentacar.sql` file from the project root
   - Click "Go" to import the database structure

4. **Environment Configuration**
   Create or modify the `.env` file in the project root:
   ```env
   DB_DRIVER=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=rentacar
   ```

5. **Install Dependencies**
   Open a terminal in the project directory:
   ```bash
   cd C:\xampp\htdocs\rentacar
   composer install
   ```

6. **Apache Configuration**
   Ensure the `.htaccess` file exists in the root directory:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule ^(.*)$ index.php [QSA,L]
   </IfModule>
   ```

7. **Start the Application**
   - Verify Apache and MySQL are running in XAMPP
   - Navigate to: `http://localhost/rentacar/`
   - The login page should load successfully

## User Guide

### Registration Process
1. Click the "Sign Up" link on the login page
2. Complete the registration form with:
   - Full name
   - Valid email address
   - Password (minimum 6 characters)
3. Submit the form via AJAX
4. Upon successful registration, you'll be redirected to the login page

### Authentication
1. Enter your registered email and password
2. Click the "Login" button
3. Valid credentials redirect you to the dashboard
4. Invalid credentials display an error message

### Dashboard Interface
The dashboard is divided into two primary sections:
- **Left Panel**: Car addition form
- **Right Panel**: Display of user's existing cars

### Category Management
1. From the category dropdown, select "+ Add New Category"
2. Enter the desired category name
3. Click the "Add" button
4. The new category immediately appears in the dropdown

### Adding a Car Listing
1. Select an existing category from the dropdown
2. Complete all required fields:
   - Car title
   - Detailed description
   - Daily rental price
   - Car image (JPEG, PNG, or GIF, max 5MB)
3. Toggle the "Available for rent" checkbox as needed
4. Click "Add Car" to submit the listing
5. The new car appears in the right panel without page refresh

### Managing Car Listings
The right panel provides filtering options:
- **All**: Displays all user cars
- **Active**: Shows only cars marked as available
- **Inactive**: Shows only unavailable cars

Each car card displays:
- Car image thumbnail
- Title and truncated description
- Daily rental price
- Category name
- Current status badge (Active/Inactive)

### Session Management
- Click the "Logout" button in the navigation bar to end your session
- Session data is destroyed and you're redirected to the login page

## Security Implementation

### Authentication Security
- Passwords hashed using PHP's `password_hash()` with BCRYPT algorithm
- Session-based authentication with secure session handling
- Protected routes inaccessible to unauthenticated users

### Database Security
- All database queries use PDO prepared statements
- Protection against SQL injection attacks
- Environment variables for sensitive configuration

### Input Validation
- Client-side validation for immediate user feedback
- Server-side validation for data integrity
- Email format verification
- Password strength requirements

### File Upload Security
- File type validation (restricted to image formats)
- File size limitation (maximum 5MB)
- Unique filename generation to prevent overwrites
- Secure file storage outside web root

## AJAX Implementation

All forms utilize AJAX for enhanced user experience:

| Form Type | Endpoint | Method | Data Type |
|-----------|----------|--------|-----------|
| Login | `/api/login` | POST | URL-encoded |
| Registration | `/api/signup` | POST | URL-encoded |
| Add Category | `/api/categories` | POST | URL-encoded |
| Add Car | `/api/cars` | POST | FormData |

The reusable `form.js` utility handles:
- Form data serialization
- Loading state management
- Success and error callbacks
- File upload support via FormData

## MVC Architecture Explanation

### Model Layer
Located in `app/Models/`, models are responsible for:
- Database interactions and queries
- Data validation and business logic
- Returning structured data to controllers
- Examples: User authentication, category management, car CRUD operations

### View Layer
Located in `app/Views/`, views are responsible for:
- Presenting data to users
- HTML structure and CSS styling
- Including JavaScript assets
- No direct database access

### Controller Layer
Located in `app/Controllers/`, controllers are responsible for:
- Handling HTTP requests
- Processing form submissions
- Coordinating between models and views
- Session management
- Returning JSON responses for API endpoints

### Front Controller
The `index.php` file serves as the entry point:
- Routes all requests using FastRoute
- Initializes core components (Database, Session)
- Loads environment variables
- Dispatches to appropriate controllers

## Troubleshooting Guide

### Common Issues and Solutions

**404 Page Not Found**
- Verify `.htaccess` file exists in root directory
- Ensure Apache's mod_rewrite module is enabled
- Check the base path configuration in `index.php`
- Confirm the requested URL matches defined routes

**Database Connection Errors**
- Verify MySQL service is running in XAMPP
- Check database credentials in `.env` file
- Ensure the `rentacar` database exists
- Confirm database user has proper permissions

**Image Upload Failures**
- Verify the upload directory exists: `public/uploads/cars/`
- Check directory write permissions (775 or 777 for development)
- Ensure file size is under 5MB
- Confirm file type is JPEG, PNG, or GIF

**AJAX Request Failures**
- Open browser developer tools (F12)
- Check console for JavaScript errors
- Verify API endpoints in network tab
- Ensure jQuery loads before custom scripts
- Confirm proper FormData handling for file uploads

**Session Issues**
- Verify `session_start()` is called in `index.php`
- Check session save path permissions
- Clear browser cookies and cache
- Ensure no output before session_start()

## Performance Considerations

- Optimized database queries with proper indexing
- Lazy loading of car images
- Minified CSS and JavaScript for production
- AJAX calls for seamless user experience
- Prepared statements for query efficiency

## Browser Compatibility

Tested and verified on:
- Google Chrome (latest)
- Mozilla Firefox (latest)
- Microsoft Edge (latest)
- Safari (latest)

## Development Notes

### Code Standards
- PSR-4 autoloading standards
- PSR-12 coding style guidelines
- Consistent naming conventions
- Comprehensive inline documentation

### Version Control
- Git for source control
- Meaningful commit messages
- .gitignore configured for sensitive files
- vendor directory excluded from repository

## Deployment Checklist

Before deploying to production:
- [ ] Update `.env` with production database credentials
- [ ] Disable error display in PHP configuration
- [ ] Enable error logging for debugging
- [ ] Set appropriate file permissions
- [ ] Configure production virtual host
- [ ] Enable HTTPS with SSL certificate
- [ ] Test all functionality in production environment
- [ ] Create database backup
- [ ] Update composer dependencies for production

## Support and Maintenance

For issues or questions:
- Review this documentation thoroughly
- Check PHP error logs for specific errors
- Verify browser console for JavaScript issues
- Ensure all dependencies are up to date
- Contact the development team for persistent issues

---

**Version**: 1.0.0  
**Release Date**: March 2026  
**Development Environment**: XAMPP 8.2.12  
**PHP Version**: 8.2.12  
**Database**: MySQL 8.0  
**License**: MIT
