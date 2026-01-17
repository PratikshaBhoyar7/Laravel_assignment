# E-Commerce Platform - Admin Panel & Customer API

A complete Laravel 12 e-commerce platform with admin panel for product management and Sanctum-based API for customers with cart and checkout functionality.

## Features

### Admin Panel
- Admin authentication with session-based login
- Product CRUD operations (Create, Read, Update, Delete)
- Real-time product search by name or SKU with AJAX
- Toggle product active/inactive status
- Input validation using Form Requests
- Responsive UI with error handling

### Customer API (Sanctum)
- User registration and login with API tokens
- Cart management with duplicate product merging
- Real-time cart updates and total calculation
- Checkout with stock validation
- Stock deduction on successful checkout
- Cart clearing after checkout
- Proper HTTP status codes and JSON responses

### Database
- 20+ pre-seeded products
- Admin user for testing
- Relational data with constraints
- Migrations for all tables

### Testing
- 4 comprehensive feature tests
- Tests for cart operations
- Tests for checkout validation
- Tests for stock management

## Setup Instructions

### Prerequisites
- PHP 8.2+
- Composer
- SQLite (database)
- Node.js (optional, for frontend assets)

### Installation

1. **Clone/Copy the project**
   ```bash
   cd laravel-task
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment file**
   ```bash
   cp .env.example .env
   ```
   The project uses SQLite by default (configured in `.env`)

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Admin Credentials

**Email:** `admin@test.com`
**Password:** `password`

**Admin URL:** `http://localhost:8000/admin/login`

## Admin Panel Guide

### Accessing Admin Panel
1. Navigate to `http://localhost:8000/admin/login`
2. Login with credentials above
3. You'll be redirected to the products management page

### Product Management
- **View Products:** Click "Products" in admin panel
- **Search Products:** Use the search box to find products by name or SKU
- **Add Product:** Click "+ Add Product" button
  - Fill in: Name (min 3 chars), SKU (unique), Price (>0), Stock (≥0)
  - Check "Active" checkbox to make product immediately available
- **Edit Product:** Click "Edit" on any product
- **Delete Product:** Click "Delete" (will prompt for confirmation)
- **Toggle Status:** Click "Activate" or "Deactivate" to toggle product availability

### Search Functionality
- Real-time AJAX search by product name or SKU
- Automatic results update without page refresh
- Pagination support (10 products per page)

## API Endpoints

### Authentication Routes

#### Register
```
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}

Response: 201
{
  "message": "User registered successfully",
  "user": { ... },
  "token": "1|abc123..."
}
```

#### Login
```
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response: 200
{
  "message": "Logged in successfully",
  "user": { ... },
  "token": "1|abc123..."
}
```

#### Logout (Protected)
```
POST /api/logout
Authorization: Bearer {token}

Response: 200
{
  "message": "Logged out successfully"
}
```

### Cart Routes (All Protected)

#### Get Cart
```
GET /api/cart
Authorization: Bearer {token}

Response: 200
{
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "product_name": "Laptop Pro 15",
      "qty": 5,
      "price": 1299.99,
      "subtotal": 6499.95
    }
  ],
  "total": 6499.95
}
```

#### Add Item to Cart
```
POST /api/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "qty": 5
}

Response: 201
{
  "message": "Item added to cart",
  "cart": {
    "items": [ ... ],
    "total": 6499.95
  }
}
```

#### Update Cart Item
```
PATCH /api/cart/items/{product_id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "qty": 10
}

Response: 200
{
  "message": "Item updated",
  "cart": { ... }
}
```

#### Remove Item from Cart
```
DELETE /api/cart/items/{product_id}
Authorization: Bearer {token}

Response: 200
{
  "message": "Item removed from cart",
  "cart": { ... }
}
```

### Checkout Route (Protected)

#### Checkout
```
POST /api/checkout
Authorization: Bearer {token}
Content-Type: application/json

{}

Response: 200 (Success)
{
  "message": "Checkout successful",
  "total": 6499.95,
  "items_count": 0
}

Response: 400 (Empty Cart)
{
  "message": "Cart is empty"
}

Response: 422 (Insufficient Stock)
{
  "message": "Insufficient stock for Laptop Pro 15",
  "product": "Laptop Pro 15",
  "required": 10,
  "available": 5
}
```

## cURL Examples

### Register User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Login User
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Get Cart
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Add to Cart
```bash
curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "qty": 5
  }'
```

### Update Cart Item
```bash
curl -X PATCH http://localhost:8000/api/cart/items/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "qty": 10
  }'
```

### Remove from Cart
```bash
curl -X DELETE http://localhost:8000/api/cart/items/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Checkout
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Feature/CartTest.php
```

### Run Tests with Detailed Output
```bash
php artisan test --testdox
```

## Test Coverage

The project includes feature tests for:
1. **Add to Cart and Merge Duplicates**
   - Adding items to cart
   - Merging duplicate products
   - Verifying cart totals

2. **Checkout Fails with Insufficient Stock**
   - Attempting checkout with insufficient stock
   - Verifying stock is NOT deducted
   - Verifying cart items remain

3. **Checkout Success with Stock Deduction**
   - Successful checkout
   - Stock deduction verification
   - Cart clearing after checkout

4. **Empty Cart Checkout Failure**
   - Checkout attempt with empty cart
   - Proper error response

## Database Structure

### Products Table
- `id` (Primary Key)
- `name` (string, required, min 3 chars)
- `sku` (string, unique, required)
- `price` (decimal, required, > 0)
- `stock` (integer, required, >= 0)
- `is_active` (boolean, default: true)
- `timestamps`

### Admins Table
- `id` (Primary Key)
- `name` (string)
- `email` (string, unique)
- `password` (string, hashed)
- `remember_token`
- `timestamps`

### Users Table (Customers)
- `id` (Primary Key)
- `name` (string)
- `email` (string, unique)
- `email_verified_at` (timestamp, nullable)
- `password` (string, hashed)
- `remember_token`
- `timestamps`

### Carts Table
- `id` (Primary Key)
- `user_id` (Foreign Key, unique)
- `timestamps`

### Cart Items Table
- `id` (Primary Key)
- `cart_id` (Foreign Key)
- `product_id` (Foreign Key)
- `qty` (integer)
- `price_at_time` (decimal - stored price at time of adding)
- `timestamps`
- Unique constraint on `(cart_id, product_id)`

### Personal Access Tokens Table (Sanctum)
- Manages API tokens for users

## Key Features Implemented

✅ Admin authentication with guards and middleware
✅ Admin CRUD operations for products
✅ Database validation using Form Requests
✅ Client-side form validation with error display
✅ AJAX search functionality for products
✅ Customer user registration and authentication
✅ Sanctum API token authentication
✅ Shopping cart with duplicate merging
✅ Real-time cart totals calculation
✅ Stock validation on checkout
✅ Stock deduction on successful checkout
✅ Cart clearing after checkout
✅ Transaction-based checkout (atomic operations)
✅ Proper HTTP status codes and JSON responses
✅ Comprehensive error handling
✅ Database migrations with constraints
✅ Database seeders with 20 products and admin user
✅ Feature tests for cart and checkout
✅ RESTful API design

## Technology Stack

- **Framework:** Laravel 12
- **Authentication:** Laravel Sanctum (API), Session (Admin)
- **Database:** SQLite (default)
- **Testing:** PHPUnit
- **Frontend:** Blade templating
- **PHP Version:** 8.2+

## Validation Rules

### Product Validation
- **name:** required, string, min 3 characters
- **sku:** required, string, unique
- **price:** required, numeric, greater than 0
- **stock:** required, integer, >= 0
- **is_active:** boolean

### User Registration
- **name:** required, string, max 255
- **email:** required, email, unique
- **password:** required, string, min 6

### Login
- **email:** required, email
- **password:** required

### Cart Operations
- **product_id:** required, exists in products
- **qty:** required, integer, > 0

## API Response Format

All API responses follow this format:

### Success Response
```json
{
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response
```json
{
  "message": "Error description",
  "errors": { ... }
}
```

## Security Features

- CSRF protection on forms
- Password hashing with bcrypt
- SQL injection prevention via Eloquent
- API token authentication with Sanctum
- Input validation on all endpoints
- Database constraints for data integrity

## Notes

- The project uses SQLite by default for easy setup
- All sensitive operations use database transactions
- Stock is checked and deducted atomically during checkout
- Cart merges duplicate products automatically
- Admin and customer authentication are completely separate

## Troubleshooting

### Database Issues
If you encounter database errors, run:
```bash
php artisan migrate:fresh --seed
```

### Middleware Issues
Make sure the `.env` file has correct database configuration

### CSRF Tokens in Forms
All forms automatically include CSRF tokens via Blade

## Support

For issues or questions, refer to the code comments and Laravel documentation.

---

**Created with Laravel 12 | Complete E-Commerce Solution**
