# Installation Guide for Shehroz ERP on Windows

Here's a step-by-step guide to install Shehroz ERP on your Windows PC:

## Prerequisites
1. **Web Server**: Install [XAMPP](https://www.apachefriends.org/download.html) (includes Apache, MySQL, PHP)
2. **Database Tool**: PHPMyAdmin (included in XAMPP)
3. **Text Editor**: Any code editor like [Visual Studio Code](https://code.visualstudio.com/download) (optional)

## Installation Steps

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
2. Run the installer and follow the installation wizard
3. Select at minimum: Apache, MySQL, PHP, and PHPMyAdmin
4. Complete the installation

### Step 2: Extract Shehroz ERP
1. Extract the downloaded `shehroz-erp.tar.gz` file using 7-Zip or WinRAR
2. You should now have a folder containing the Shehroz ERP files

### Step 3: Move Files to Web Server
1. Start XAMPP Control Panel and start Apache and MySQL services
2. Navigate to the XAMPP installation directory (usually `C:\xampp`)
3. Copy the extracted Shehroz ERP folder to `C:\xampp\htdocs\`
4. Rename the folder to something simple like `shehroz-erp`

### Step 4: Create Database
1. Open your web browser and navigate to `http://localhost/phpmyadmin`
2. Create a new database named `shehroz_erp`
3. Select the new database and go to the "Import" tab
4. Import the database schema file from `database/schema.sql` in your extracted files
5. Import the sample data (optional) from `database/sample_data.sql`

### Step 5: Configure Environment
1. In your Shehroz ERP folder, locate the `.env.example` file
2. Create a copy of this file and rename it to `.env`
3. Open the `.env` file in a text editor and update the following:
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=shehroz_erp
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   (Note: Default XAMPP MySQL username is "root" with no password)

### Step 6: Install Dependencies
1. Open Command Prompt as Administrator
2. Navigate to your Shehroz ERP directory:
   ```
   cd C:\xampp\htdocs\shehroz-erp
   ```
3. Run Composer to install dependencies:
   ```
   composer install
   ```
   (If you don't have Composer, [download and install it](https://getcomposer.org/download/))

### Step 7: Access the System
1. Open your web browser
2. Navigate to `http://localhost/shehroz-erp/public/`
3. The login screen should appear
4. Use the following default credentials:
   - Username: admin
   - Password: admin123

### Step 8: Change Default Credentials
1. After logging in, go to the user profile section
2. Change the default password to a secure one

## Troubleshooting
- **500 Server Error**: Check your Apache error logs at `C:\xampp\apache\logs\error.log`
- **Database Connection Error**: Verify your `.env` file has correct database credentials
- **Missing PHP Extensions**: In XAMPP Control Panel, click "Config" for Apache, select php.ini, and make sure extensions like php_pdo_mysql are enabled

If you encounter any specific errors during installation, please let me know the exact error message and I can provide more targeted assistance.