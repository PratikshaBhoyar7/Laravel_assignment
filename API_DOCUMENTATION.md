# E-Commerce API Documentation

Complete API documentation with curl examples for the E-Commerce platform.

## Base URL
```
http://localhost:8000
```

## Table of Contents
1. [Authentication](#authentication)
2. [Cart Operations](#cart-operations)
3. [Checkout](#checkout)
4. [Admin Panel](#admin-panel)
5. [Response Formats](#response-formats)

---

## Authentication

### Register User

Create a new customer account.

**Endpoint:** `POST /api/register`

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-01-15T10:00:00.000000Z",
    "updated_at": "2026-01-15T10:00:00.000000Z"
  },
  "token": "1|ABC123DEF456GHI789JKL"
}
```

**Validation Rules:**
- `name`: required, string, max 255
- `email`: required, email, unique
- `password`: required, string, min 6

---

### Login User

Authenticate and receive API token.

**Endpoint:** `POST /api/login`

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Response (200):**
```json
{
  "message": "Logged in successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|ABC123DEF456GHI789JKL"
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Invalid credentials"]
  }
}
```

---

### Logout User

Revoke current API token.

**Endpoint:** `POST /api/logout`

**Request:**
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

## Cart Operations

All cart endpoints require authentication. Add `Authorization: Bearer YOUR_TOKEN` header to all requests.

### Get Cart

Retrieve current user's cart with all items and total.

**Endpoint:** `GET /api/cart`

**Request:**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response (200):**
```json
{
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "product_name": "Laptop Pro 15",
      "qty": 5,
      "price": "1299.99",
      "subtotal": 6499.95
    },
    {
      "id": 2,
      "product_id": 2,
      "product_name": "USB-C Cable",
      "qty": 2,
      "price": "12.99",
      "subtotal": 25.98
    }
  ],
  "total": 6525.93
}
```

**Empty Cart Response (200):**
```json
{
  "message": "Cart is empty",
  "items": [],
  "total": 0
}
```

---

### Add Item to Cart

Add a product to cart or increase quantity if already in cart.

**Endpoint:** `POST /api/cart/items`

**Request:**
```bash
curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "qty": 5
  }'
```

**Response (201):**
```json
{
  "message": "Item added to cart",
  "cart": {
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Laptop Pro 15",
        "qty": 5,
        "price": "1299.99",
        "subtotal": 6499.95
      }
    ],
    "total": 6499.95
  }
}
```

**Validation Rules:**
- `product_id`: required, must exist in products table
- `qty`: required, integer, > 0

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "product_id": ["The selected product_id is invalid."],
    "qty": ["The qty must be greater than 0."]
  }
}
```

---

### Update Cart Item

Change the quantity of an item already in the cart.

**Endpoint:** `PATCH /api/cart/items/{product_id}`

**Request:**
```bash
curl -X PATCH http://localhost:8000/api/cart/items/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "qty": 10
  }'
```

**Response (200):**
```json
{
  "message": "Item updated",
  "cart": {
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Laptop Pro 15",
        "qty": 10,
        "price": "1299.99",
        "subtotal": 12999.90
      }
    ],
    "total": 12999.90
  }
}
```

**Validation Rules:**
- `qty`: required, integer, > 0

---

### Remove Item from Cart

Delete an item from the cart.

**Endpoint:** `DELETE /api/cart/items/{product_id}`

**Request:**
```bash
curl -X DELETE http://localhost:8000/api/cart/items/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response (200):**
```json
{
  "message": "Item removed from cart",
  "cart": {
    "items": [],
    "total": 0
  }
}
```

---

## Checkout

### Checkout Cart

Complete the purchase and clear the cart.

**Endpoint:** `POST /api/checkout`

**Features:**
- Validates all items have sufficient stock
- Uses database transactions for atomicity
- Deducts stock from products
- Clears cart after successful checkout

**Request:**
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Success Response (200):**
```json
{
  "message": "Checkout successful",
  "total": 6525.93,
  "items_count": 0
}
```

**Empty Cart Error (400):**
```json
{
  "message": "Cart is empty"
}
```

**Insufficient Stock Error (422):**
```json
{
  "message": "Insufficient stock for Laptop Pro 15",
  "product": "Laptop Pro 15",
  "required": 10,
  "available": 5
}
```

---

## Admin Panel

### Admin Login

Access the admin panel at: `http://localhost:8000/admin/login`

**Credentials:**
- Email: `admin@test.com`
- Password: `password`

### Login via cURL

```bash
curl -X POST http://localhost:8000/admin/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -c cookies.txt \
  -d 'email=admin@test.com&password=password'
```

---

### Product Management

#### View Products

**Endpoint:** `GET /admin/products`

**Request:**
```bash
curl http://localhost:8000/admin/products
```

#### Search Products (AJAX)

**Endpoint:** `GET /admin/products?search=QUERY`

**Request:**
```bash
curl -X GET "http://localhost:8000/admin/products?search=Laptop" \
  -H "X-Requested-With: XMLHttpRequest"
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Laptop Pro 15",
      "sku": "LAPTOP-001",
      "price": "1299.99",
      "stock": 25,
      "is_active": true,
      "created_at": "2026-01-15T10:00:00.000000Z",
      "updated_at": "2026-01-15T10:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/admin/products?page=1",
    "last": "http://localhost:8000/admin/products?page=2",
    "prev": null,
    "next": "http://localhost:8000/admin/products?page=2"
  }
}
```

---

#### Create Product

**Endpoint:** `POST /admin/products`

**Request:**
```bash
curl -X POST http://localhost:8000/admin/products \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -c cookies.txt \
  -b cookies.txt \
  -d 'name=New Product&sku=NEW-001&price=99.99&stock=100&is_active=1'
```

**Validation Rules:**
- `name`: required, string, min 3 characters
- `sku`: required, string, unique
- `price`: required, numeric, > 0
- `stock`: required, integer, >= 0
- `is_active`: optional, boolean

---

#### Edit Product

**Endpoint:** `PUT /admin/products/{id}`

**Request:**
```bash
curl -X PUT http://localhost:8000/admin/products/1 \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -c cookies.txt \
  -b cookies.txt \
  -d 'name=Updated Product&sku=NEW-001&price=119.99&stock=50&is_active=1'
```

---

#### Delete Product

**Endpoint:** `DELETE /admin/products/{id}`

**Request:**
```bash
curl -X DELETE http://localhost:8000/admin/products/1 \
  -c cookies.txt \
  -b cookies.txt
```

---

#### Toggle Product Status

**Endpoint:** `PATCH /admin/products/{id}/toggle`

**Request:**
```bash
curl -X PATCH http://localhost:8000/admin/products/1/toggle \
  -c cookies.txt \
  -b cookies.txt
```

---

## Response Formats

### Success Response Format

```json
{
  "message": "Operation successful",
  "data": {},
  "token": "optional_token_if_applicable"
}
```

### Error Response Format

```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request data |
| 401 | Unauthorized - Missing or invalid authentication |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error |

---

## Complete Workflow Example

### 1. Register a User

```bash
RESPONSE=$(curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }')

# Extract token from response
TOKEN=$(echo $RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Token: $TOKEN"
```

### 2. Add Items to Cart

```bash
curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "qty": 2
  }'

curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 2,
    "qty": 3
  }'
```

### 3. View Cart

```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer $TOKEN"
```

### 4. Checkout

```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### 5. Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer $TOKEN"
```

---

## Testing the API

### Using Postman

1. Import the `postman_collection.json` file into Postman
2. Update the `token` variable after login
3. Run requests with the authentication token

### Using curl

All examples are provided above. Make sure to replace `YOUR_TOKEN` with the actual token received from login.

### Running PHPUnit Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CartTest.php

# Run with detailed output
php artisan test --testdox
```

---

## Common Issues and Solutions

### 401 Unauthorized
- Make sure you're including the `Authorization: Bearer TOKEN` header
- Token might have expired (tokens don't expire in this implementation)
- Token format should be: `Bearer YOUR_ACTUAL_TOKEN`

### 422 Unprocessable Entity
- Check validation error messages in response
- Ensure all required fields are provided
- Verify data types match validation rules

### 404 Not Found
- Product ID might not exist
- Check if the resource has been deleted

### Insufficient Stock Error
- Cart has more items than available stock
- Reduce quantity or choose different products
- Stock is deducted only on successful checkout

---

## Database

The application uses **MySQL** by default (configured in your `.env` file).

**Database Name:** `laravel_task`

To recreate the database with fresh data:

```bash
php artisan migrate:fresh --seed
```

---

## Support

For more information, refer to the main README.md file.
