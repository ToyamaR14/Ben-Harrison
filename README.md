# Tenant Management System

Ben Harrison Residence Management System developed as a capstone project.

# Features

- User registration and authentication
- Gmail Notification
- Admin & Tenant portals
- Tenant, room & reservation management
- Inventory & maintenance management
- Payment tracking with GCash/PayMongo integration
- Financial reports with charts and Excel export
- Tenant-to-admin messaging & notifications
- Activity/system logs
- PWA support for mobile access
- MySQL database management

# Test Accounts
For demonstration purposes, you can use the following accounts:

- **Tenant:** `quackers@gmail.com` password `1234`
- **Admin:** `brazzblood.m@gmail.com` password `12345`

# Requirements

> **Important:** This project is designed to run locally using **XAMPP** and is not currently configured for online hosting.

### Local Setup

- XAMPP
- Apache
- MySQL
- PHP 8.2.12

## Running the Project

1. Clone the repository.
2. Place the project inside the XAMPP `htdocs` directory.
3. Start Apache and MySQL through XAMPP.
4. Import `harrison_db.sql` into phpMyAdmin.
5. Install the required Composer dependencies.
6. Configure the required environment variables.
7. Open the project through `localhost`.

# Tech Stack

### Front-end
- HTML5
- CSS
- JavaScript
- Chart.js

### Back-end
- PHP
- Composer
- GuzzleHTTP
- PHPMailer
- PayMongo API

### Database
- MySQL

# Notes
- Email registration normally requires Gmail verification.
- Admin users can create accounts directly through the Admin page for demonstration purposes.
- Test payments from paymongo may or may not work.
