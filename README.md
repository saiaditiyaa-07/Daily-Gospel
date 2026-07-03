# Daily Gospel 📖

**Daily Gospel** is a clean, beautiful, and responsive web application designed for browsing Catholic daily Mass readings and liturgical calendar information. It features a premium, church-inspired visual aesthetic with robust fallbacks, offline support, search capabilities, and a fully interactive reading customizer.

---

## 🛠️ Key Features

- **Tamil-Primary Default Environment**:
  - Automatically configured with Tamil (`ta`) as the primary system and database default language.
  - Adapted interface: hides features not supported in Tamil (such as the language switcher, Saint calendar database listings, and Saint search tabs) to ensure a highly focused, distraction-free Tamil-language devotional experience.
- **Cathedral Theme & Visual Styling**:
  - **Warm Ivory Background**: Elegant primary light mode page color (`#FAF6EE`) representing a sacred, editorial book layout.
  - **Candlelight Parchment Dark Theme**: A deep, warm parchment dark mode (`#1B1816`) that prevents glare and eye strain for comfortable reading in low-light environments.
  - **Dynamic Liturgical Colors**: Automatically adapts the primary/accent color schemes of headers, buttons, borders, and progress indicators depending on the **Liturgical Season** of the loaded date:
    - 🟢 *Ordinary Time*: Deep Forest Green
    - 🟣 *Lent / Advent*: Liturgical Purple
    - ⚪🟡 *Christmas / Easter / Feasts*: Liturgical Gold
    - 🔴 *Pentecost / Martyrs*: Sacred Red
    - 💗 *Gaudete / Laetare*: Soft Rose
    - ⚫ *All Souls*: Charcoal Black
- **Cathedral-Ambience Motion & Animations**:
  - **Scroll Progress Bar**: A sleek progress line at the top of the viewport indicating reading progress.
  - **Current Paragraph Highlighting**: Gently highlights the paragraph in center view while scrolling through scripture.
  - **Scroll Reveal**: Uses a clean, scroll-triggered fade/slide-up transition (`IntersectionObserver`) for cards and widgets.
  - **Page-Load Transitions**: Sequence of soft, slow fade-in motions (800ms) that fade the layout elements into view upon entering.
- **Bible Page Reading Customizer**:
  - Rendered inside a premium text accordion that mimics standard columns in physical bibles.
  - Includes text sizing adjustment buttons (80% to 150%) and Serif / Sans-serif typography toggles saved in `localStorage`.
- **Liturgical Calendar**:
  - A compact, centered monthly grid layout that fits perfectly inside screen viewports without requiring vertical scrolling.
  - Displays dates with liturgical color indicators and handles API downtime gracefully using a basic fallback month grid.
- **Search Capabilities**: Search daily scripture by exact date or Bible reference (e.g. John 3:16) with immediate redirect to the localized readings.
- **Church-Inspired Highlights**:
  - Integrates a custom SVG-mask Christian cross icon (`.bi-cross`) for branding.
  - Includes a bilingual Bible quotation from **Psalm 119:105** (*"Your word is a lamp for my feet..."*) in the footer.
- **Offline & API Resilience**:
  - Local caching (24h TTL) for Tamil Mass readings from the Catholic Gallery WordPress REST API.
  - Detects network downtime and shows a tailored offline banner or template.

---

## 💻 Tech Stack

- **Backend**: PHP (8.0+)
- **Database**: MySQL (8.0+)
- **Frontend**: HTML5, Vanilla JavaScript (ES6+), CSS3
- **CSS Framework**: Bootstrap (5.3.3)
- **Icons**: Bootstrap Icons (1.11.3) + Inline Custom SVGs
- **External Data Providers**: 
  - English Mass readings: Universalis API
  - Tamil Mass readings: Catholic Gallery WP REST API (`bible.catholicgallery.org`)
  - Liturgical details: Church Calendar API (`calapi.inadiutorium.cz`)

---

## 🚀 Installation & Local Setup

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

## 📄 License

This project is open-source. Mass readings content is provided courtesy of [Universalis Publishing Ltd](https://universalis.com) (English) and [Catholic Gallery](https://www.catholicgallery.org) (Tamil).
