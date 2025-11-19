# SiKasir - Point of Sale System

A comprehensive Point of Sale (POS) system built with Laravel 12, designed for cafe and restaurant management. This system handles product management, transactions, inventory, reporting, and multi-role user access.

## 🚀 Features

### Core Functionality

- **Multi-Role System**: Admin, Owner, Kasir (Cashier), Bar, Kitchen roles with permission-based access
- **Product Management**: Complete CRUD operations for products with categories and subcategories
- **Transaction Processing**: Real-time transaction handling with invoice generation
- **Inventory Management**: Track ingredients, stock levels, and product-ingredient relationships
- **Reporting & Analytics**: Comprehensive reports with date filtering and statistics
- **Table Management**: Manage restaurant tables and seating
- **User Management**: Admin panel for managing users and their roles

### Recent Enhancements (Latest Update)

✅ **Product Management Improvements**
- Live image preview before saving products
- Enhanced form validation (name, price, description, image)
- Description field added to products
- Business logic validation (selling price ≥ cost price)
- Improved error handling and user feedback

✅ **Optimized Routing**
- Organized route groups with proper middleware
- Named routes for better maintainability
- Role-based route protection
- RESTful resource routes

✅ **Enhanced Sidebar**
- Active menu item highlighting based on current route
- Expandable/collapsible nested menus
- Role-based menu visibility
- Smooth transitions and visual feedback

✅ **New Pages**
- **Reports Dashboard**: Transaction statistics, revenue analytics, top products
- **Settings Page**: Application configuration and system information
- **Profile Page**: User profile management (improved navigation)

## 🛠️ Technology Stack

- **Backend**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL/SQLite
- **Frontend**: 
  - Blade Templates
  - TailwindCSS
  - Bootstrap Icons
  - jQuery & DataTables
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **QR Code**: SimpleSoftwareIO/simple-qrcode

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 5.7+ or SQLite
- Web Server (Apache/Nginx) or PHP Built-in Server

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone -b Yanhai https://github.com/yanhaizhou21-a11y/sikasir2.git
   cd sikasir2
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**
   
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sikasir
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run Migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed Database (Optional)**
   ```bash
   php artisan db:seed
   ```

8. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

9. **Build Assets**
   ```bash
   npm run build
   # or for development:
   npm run dev
   ```

10. **Start Development Server**
    ```bash
    php artisan serve
    ```

    Visit `http://localhost:8000` in your browser.

## 👥 User Roles & Permissions

The system supports multiple roles with different access levels:

| Role | Access Level | Features |
|------|--------------|----------|
| **Admin** | Full Access | All features including user management, settings, reports |
| **Owner** | Business Owner | Products, reports, transactions (no user management) |
| **Kasir** | Cashier | Transaction processing, POS interface |
| **Bar** | Bar Staff | Bar orders, ingredient stock management |
| **Kitchen** | Kitchen Staff | Kitchen orders, ingredient stock management |

## 📁 Project Structure

```
sikasir/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Application controllers
│   │   └── Middleware/        # Custom middleware
│   ├── Models/                # Eloquent models
│   └── Providers/             # Service providers
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── public/                    # Public assets
│   └── storage/               # Storage symlink
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── admin/             # Admin views
│   │   ├── products/          # Product management views
│   │   ├── reports/           # Reports views
│   │   ├── settings/          # Settings views
│   │   └── components/        # Reusable components
│   ├── css/                   # CSS files
│   └── js/                    # JavaScript files
├── routes/
│   └── web.php                # Web routes
└── storage/
    ├── app/                   # Application storage
    └── logs/                  # Log files
```

## 🎯 Key Features Documentation

### Product Management

**Create/Edit Products:**
- Navigate to **Produk** in the sidebar
- Fill in product details:
  - Name (required)
  - Description (optional)
  - Barcode (required, unique)
  - Cost Price (required)
  - Selling Price (required, must be ≥ cost price)
  - Category & Subcategory
  - Product Image (with live preview)
  - Ingredients and quantities

**Image Upload:**
- Supported formats: JPEG, PNG, JPG, GIF, WEBP
- Maximum size: 2MB
- Live preview before saving
- Images stored in `storage/app/public/products`

### Reports Dashboard

**Access:** Admin & Owner → **Laporan**

**Features:**
- Date range filtering
- Transaction statistics (total transactions, revenue, average)
- Top products listing
- Revenue analytics (ready for chart integration)

### Settings Page

**Access:** Admin → **Pengaturan**

**Configuration Options:**
- Application name
- Timezone settings
- Currency symbol
- Date/time format preferences
- System information display

## 🔐 Authentication

The application uses Laravel Breeze for authentication. Default features:

- User registration
- Email verification
- Password reset
- Profile management
- Secure session handling

## 📊 Database Schema

### Main Tables

- `users` - User accounts with roles
- `products` - Product catalog
- `categories` - Product categories
- `subcategories` - Product subcategories
- `transactions` - Sales transactions
- `transaction_items` - Transaction line items
- `ingredients` - Inventory ingredients
- `product_ingredients` - Product-ingredient relationships
- `tables` - Restaurant table management
- `orders` - Kitchen/Bar orders

## 🚀 Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
php artisan pint
```

### Clearing Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Queue Processing (if using queues)
```bash
php artisan queue:work
```

## 📝 Recent Updates

### Version 1.1.0 (Latest)
- ✅ Added product description field
- ✅ Implemented live image preview
- ✅ Enhanced form validation
- ✅ Created Reports dashboard
- ✅ Added Settings page
- ✅ Improved sidebar with active states
- ✅ Optimized route structure
- ✅ Role-based menu visibility

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

Developed for Ali Akbar Shisa & Coffee Shop

## 🆘 Support

For support, email support@example.com or create an issue in the repository.

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Breeze Documentation](https://laravel.com/docs/breeze)
- [Spatie Permissions Documentation](https://spatie.be/docs/laravel-permission)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)

---

**Note:** Make sure to configure your `.env` file properly before running the application. Update database credentials, app URL, and other environment-specific settings.
