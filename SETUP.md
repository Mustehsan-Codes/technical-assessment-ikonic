# Technical Assessment - E-Commerce App

## Overview
A simple e-commerce application with Laravel (API) + React (Frontend).

## Features
- User authentication (register/login)
- Product listing with category filtering and search
- Shopping cart management
- Checkout with fake payment flow
- Order history

---

## Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm

---

## Backend Setup (Laravel)

```bash
# From project root
composer install
cp .env.example .env
php artisan key:generate

# Database (SQLite)
touch database/database.sqlite
php artisan migrate --seed

# Start the API server
php artisan serve
```

The API will be available at `http://localhost:8000/api`

### Swagger API Documentation

After starting the server, visit:
```
http://localhost:8000/api/docs
```

To regenerate docs after changes:
```bash
php artisan l5-swagger:generate
```

### Test Users (from seeder)
| Email | Password |
|---|---|
| user1@ikonicdev.com | password123 |
| user2@ikonicdev.com | password123 |
| admin@ikonicdev.com | admin12345 |

---

## Frontend Setup (React)

```bash
cd frontend
npm install
npm start
```

The frontend will be available at `http://localhost:3000`

---

## API Endpoints

### Public
- `POST /api/register` - Register new user
- `POST /api/login` - Login
- `GET /api/products` - List products (supports `?category_id=` and `?search=`)
- `GET /api/products/{id}` - Product detail
- `GET /api/categories` - List categories
- `GET /api/categories/{id}` - Category detail

### Protected (requires Bearer token)
- `POST /api/logout` - Logout
- `GET /api/me` - Current user
- `GET /api/cart` - Get cart
- `POST /api/cart/items` - Add item to cart (`product_id`, `quantity`)
- `POST /api/cart/items/{id}` - Update cart item quantity
- `DELETE /api/cart/items/{id}` - Remove cart item
- `POST /api/checkout` - Place order (`shipping_address`, `payment_method`)
- `GET /api/checkout/pay/{orderId}` - Process payment
- `GET /api/orders` - List orders
- `GET /api/orders/{id}` - Order detail

### Sample API Responses

**POST /api/login**
```json
{
  "user": {
    "id": 1,
    "name": "User 1",
    "email": "user1@ikonicdev.com"
  },
  "token": "1|abc123..."
}
```

**GET /api/products**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Wireless Bluetooth Headphones",
      "slug": "wireless-bluetooth-headphones",
      "description": "...",
      "price": "79.99",
      "stock": 50,
      "image_url": null,
      "category": "Electronics",
      "category_id": 1
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 22
  }
}
```

**GET /api/products/{id}**
```json
{
  "id": 1,
  "name": "Wireless Bluetooth Headphones",
  "slug": "wireless-bluetooth-headphones",
  "description": "...",
  "price": "79.99",
  "stock": 50,
  "image_url": null,
  "category_id": 1,
  "is_active": true,
  "created_at": "...",
  "updated_at": "..."
}
```

**GET /api/orders**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "total": "79.99",
    "status": "paid",
    "payment_method": "credit_card",
    "shipping_address": "123 Main St",
    "items": [...]
  }
]
```

---

## Project Structure

```
technical-assessment/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── CartController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderController.php
│   │   │   └── ProductController.php
│   │   └── Middleware/
│   │       └── Cors.php
│   └── Models/
│       ├── Cart.php
│       ├── CartItem.php
│       ├── Category.php
│       ├── Order.php
│       ├── OrderItem.php
│       ├── Product.php
│       └── User.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── frontend/
│   └── src/
│       ├── components/
│       │   └── Navbar.js
│       ├── context/
│       │   ├── AuthContext.js
│       │   └── CartContext.js
│       ├── pages/
│       │   ├── Cart.js
│       │   ├── Checkout.js
│       │   ├── Login.js
│       │   ├── Orders.js
│       │   ├── Products.js
│       │   └── Register.js
│       ├── services/
│       │   └── api.js
│       ├── App.js
│       └── index.js
└── SETUP.md
```

---

## Task

Review the codebase, identify bugs and issues, and fix them. Pay attention to:
- Backend logic and security
- API design and consistency
- Frontend state management
- Authentication flow
- Data validation
- Performance
