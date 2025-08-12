# Shehroz ERP

A lightweight PHP-based Enterprise Resource Planning (ERP) system focused on procurement with white-labeling support and Pakistan tax compliance.

![Shehroz ERP Logo](/public/assets/img/shehroz-logo.svg)

## Overview

Shehroz ERP provides a complete procurement management solution with a focus on simplicity, compliance with Pakistan tax regulations, and customizable white-label capabilities. The system offers comprehensive features for purchase order management, supplier relationships, inventory control, and FBR integration.

## Key Features

- **Purchase Order Management:** Create, track, and manage purchase orders through a complete workflow
- **Approval System:** Multi-level approval process for purchase orders
- **Pakistan Tax Compliance:** Built-in support for Pakistan tax regulations and FBR integration
- **Supplier Management:** Maintain supplier database with comprehensive information
- **White-Label Support:** Easily rebrand the system with your company logo and colors
- **Responsive Design:** Works across desktop and mobile devices

## Technical Specifications

- PHP 7.4 or higher
- MySQL/MariaDB database
- Composer for dependency management
- Bootstrap 5 for responsive UI

## Getting Started

### Prerequisites
- PHP 7.4+
- MySQL/MariaDB
- Composer

### Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/shehroz-erp.git
   ```

2. Navigate to the project directory:
   ```
   cd shehroz-erp
   ```

3. Install dependencies:
   ```
   composer install
   ```

4. Copy the environment file:
   ```
   cp .env.example .env
   ```

5. Configure your environment variables in the `.env` file:
   - Database connection
   - White-labeling options
   - Email settings
   - FBR integration credentials

6. Import the database schema:
   ```
   mysql -u your_username -p your_database < database/schema.sql
   ```

7. Set up your web server (Apache, Nginx) to point to the `public` directory

8. Access the ERP through your web browser

## White-Labeling

Shehroz ERP can be customized with your own branding:

1. Update the company name and logo in `.env`:
   ```
   COMPANY_NAME="Your Company Name"
   COMPANY_LOGO="/images/Logo.jpg"
   ```

2. Customize colors:
   ```
   PRIMARY_COLOR="#your-primary-color"
   SECONDARY_COLOR="#your-secondary-color"
   ```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Contact

For support or inquiries, please contact:
- Email: info@shehroz-erp.com
- Website: https://shehroz-erp.com