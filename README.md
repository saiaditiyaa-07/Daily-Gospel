# Daily Gospel 📖

**Daily Gospel** is a clean, beautiful, and responsive web application designed for browsing Catholic daily Mass readings and liturgical calendar information. It features a modern theme inspired by [Catholic Gallery](https://www.catholicgallery.org/) with robust fallbacks, offline support, search capabilities, and a fully interactive reading customizer.

---

## Key Features

- **Daily Mass Readings**: Fetches and renders the First Reading, Responsorial Psalm, Second Reading, and Gospel dynamically from [Universalis](https://universalis.com).
- **Interactive Reading Customizer**: Allows users to customize the scripture font style (choice of Serif/Sans-Serif) and dynamically adjust font size to their reading preference. User choices are saved in `localStorage`.
- **Liturgical Calendar**: Renders a complete calendar showing liturgical colors, seasons, feasts, and saint names.
- **API Resilience & connection monitoring**: Incorporates fail-fast connection checks and downtime caching (short-circuiting). If the external Church Calendar API is offline, the site instantly displays fallback basic calendar days instead of freezing or timing out.
- **Search Capabilities**: Search daily scripture by exact date, Bible reference (e.g. John 3:16), or saint name.
- **Offline Mode**: Automatically detects connectivity status and displays a custom offline page if the network goes down.
- **Admin Dashboard**: Secure administrative interface for managing app settings, user bookmarks, prayer requests, and user feedback.

---

## Tech Stack

- **Backend**: PHP (8.0+)
- **Database**: MySQL (8.0+)
- **Frontend**: HTML5, Vanilla JavaScript, CSS3
- **CSS Framework**: Bootstrap (5.3.3)
- **Icons**: Bootstrap Icons (1.11.3)
- **External Data Providers**: 
  - Scripture readings: Universalis API
  - Liturgical details: Church Calendar API (`calapi.inadiutorium.cz`)

---

## Installation & Local Setup

### Prerequisites
- PHP (8.0 or later)
- MySQL / MariaDB
- Apache Web Server (or PHP built-in server)

### 1. Database Setup
1. Open your database administration tool (e.g., phpMyAdmin) and create a database named `daily_gospel`.
2. Import the `database.sql` file into the database.

### 2. Configuration
1. Copy `config.example.php` to a new file named `config.local.php` to configure your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'daily_gospel');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   ```
2. Update `APP_URL` or configuration overrides if necessary (the system dynamically resolves the host and port for local development).

### 3. Run Locally

#### Using XAMPP / Apache:
Move the directory into your server's root directory (e.g. `htdocs/Gospell`), start Apache and MySQL, and access:
👉 `http://localhost/Gospell`

#### Using PHP Built-in Server:
Open terminal in the project directory and run:
```bash
php -S localhost:8000
```
Then visit:
👉 `http://localhost:8000`

### 4. Admin Setup
1. Navigate to `http://localhost:8000/admin/setup.php?password=YourDesiredSecurePassword` to configure your administrator password.
2. For safety, **delete** `admin/setup.php` after the configuration is complete.

---

## License

This project is open-source. Mass readings content is provided courtesy of [Universalis Publishing Ltd](https://universalis.com).
