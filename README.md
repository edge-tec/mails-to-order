# Server Ordering & Provisioning System

Production-ready, high-performance Server Ordering & Provisioning Portal built using native PHP 8.2+, MySQL 8+, Bootstrap 5, AJAX, PHPMailer, and Composer.

The system empowers customers to order server packages with customizable daily/monthly POP limits, submit payment details via bKash, Nagad, or Cryptocurrency (USDT TRC20/ERC20, BTC, ETH, LTC), track POP usage metrics, and receive AES-256-GCM encrypted server login credentials automatically via email upon administrator verification.

---

## Key Features

- **User Authentication & RBAC**:
  - Secure registration, login, logout, password resets, profile updates, and rate limiting.
  - Role-based permissions (`super_admin`, `admin`, `user`).
- **Server Packages & Dynamic Pricing**:
  - Admin-configurable standard packages (Daily 500 POP, Daily 1,000 POP) and Enterprise Custom package requests.
  - Store package prices at time of order for historical accuracy.
- **Multi-Step Checkout & Payment Processing**:
  - Step 1: Customer Information.
  - Step 2: Payment Method (bKash, Nagad, Crypto USDT, BTC, ETH) with secure screenshot validation (JPG, PNG, WEBP).
  - Step 3: Confirmation and order generation (`SRV-YYYYMMDD-XXXXX`).
- **Admin Order Review & Transactional Provisioning**:
  - Review orders, inspect payment receipts via secure stream routing, assign available servers or provision new server nodes, encrypt credentials, and automatically trigger PHPMailer credential emails.
- **AES-256-GCM Credential Security**:
  - Sensitive server SSH/login passwords are encrypted at rest using application-level AES-256-GCM.
  - Authenticated AJAX "Show Password" and one-click clipboard copying.
- **POP Usage Tracking**:
  - Real-time daily & monthly POP usage tracking and remaining balance widgets.
- **Audit Logs & Email Notifications**:
  - Detailed administrative action logging (IP, User Agent, Timestamp, Action).
  - Centralized PHPMailer HTML email engine with customizable templates and admin notification toggles.
- **Interactive Web Installer (`install.php`)**:
  - 7-step setup wizard checking PHP extensions, testing MySQL connectivity, running migrations & seeders, creating admin accounts, and configuring SMTP.

---

## Requirements

- PHP >= 8.2 with PDO, OpenSSL, cURL, JSON, Mbstring extensions
- MySQL >= 8.0
- Composer
- Web Server (Apache with `mod_rewrite` enabled or Nginx)

---

## Fast Installation Wizard

1. Clone or extract repository into your web root.
2. Ensure file permissions allow writing to `storage/` and `.env`.
3. Open your browser and navigate to:
   ```
   http://your-domain.com/install.php
   ```
4. Follow the interactive 7-step wizard:
   - **Step 1**: System Requirements Check
   - **Step 2**: Database Credentials
   - **Step 3**: Execute Database Migrations & Seeders
   - **Step 4**: Setup Super Admin Account
   - **Step 5**: Website Details Configuration
   - **Step 6**: SMTP Mail Configuration
   - **Step 7**: Complete & Lock Installer

---

## Default Super Admin Login

After installation or seeding, log in using:
- **URL**: `http://your-domain.com/login`
- **Email**: `admin@example.com`
- **Password**: `Admin@123456`

---

## Web Server Configuration

### Apache (`.htaccess` in `public/`)
Point DocumentRoot to `public/` directory:
```apache
<VirtualHost *:80>
    ServerName server-portal.local
    DocumentRoot "/path/to/server-ordering-system/public"

    <Directory "/path/to/server-ordering-system/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx
```nginx
server {
    listen 80;
    server_name server-portal.local;
    root /path/to/server-ordering-system/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Cron Job Setup

Add the following entries to your system crontab (`crontab -e`):

```bash
# Check expired servers every 15 minutes
*/15 * * * * php /path/to/server-ordering-system/cron/server-expiration.php >> /dev/null 2>&1

# Reset Daily POP Counters at Midnight
0 0 * * * php /path/to/server-ordering-system/cron/daily-usage.php >> /dev/null 2>&1

# Reset Monthly POP Counters on the 1st of every month
0 0 1 * * php /path/to/server-ordering-system/cron/monthly-usage.php >> /dev/null 2>&1
```

---

## GitHub Setup & Workflow

```bash
git init
git add .
git commit -m "Initial server ordering & provisioning system"
git branch -M main
git remote add origin https://github.com/edge-tec/mails-to-order.git
git push -u origin main
```

---

## Directory Structure

```
server-ordering-system/
├── app/
│   ├── Controllers/
│   │   └── Admin/
│   ├── Helpers/
│   ├── Middleware/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   ├── install.php
│   └── index.php
├── resources/
│   ├── emails/
│   └── views/
├── routes/
├── storage/
│   ├── logs/
│   └── uploads/
├── cron/
├── .env.example
├── .gitignore
├── composer.json
└── README.md
```

---

## License

Developed by **Edge-Tec Software**. Distributed under the MIT License.
