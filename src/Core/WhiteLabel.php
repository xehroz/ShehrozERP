<?php
/**
 * Shehroz ERP - White Label Class
 * 
 * Handles white-labeling configuration for the ERP system
 */

namespace App\Core;

class WhiteLabel {
    // White label configuration properties
    public static $companyName;
    public static $companyLogo;
    public static $primaryColor;
    public static $secondaryColor;
    public static $favicon;
    
    /**
     * Load white label configuration from environment variables
     */
    public static function load() {
        self::$companyName = $_ENV['COMPANY_NAME'] ?? 'Shehroz ERP';
        self::$companyLogo = $_ENV['COMPANY_LOGO'] ?? '/assets/img/shehroz-logo.svg';
        self::$primaryColor = $_ENV['PRIMARY_COLOR'] ?? '#0a5580';
        self::$secondaryColor = $_ENV['SECONDARY_COLOR'] ?? '#ffcc00';
        self::$favicon = $_ENV['FAVICON'] ?? 'assets/img/favicon.ico';
    }
    
    /**
     * Get the application name (company name or default)
     */
    public static function getAppName() {
        return $_ENV['APP_NAME'] ?? self::$companyName;
    }
    
    /**
     * Get CSS variables for white-labeling
     */
    public static function getCssVars() {
        return "
            :root {
                --primary-color: " . self::$primaryColor . ";
                --secondary-color: " . self::$secondaryColor . ";
                --company-name: '" . self::$companyName . "';
            }
        ";
    }
    
    /**
     * Generate HTML for including white-label assets
     */
    public static function getAssetTags() {
        $appName = htmlspecialchars(self::getAppName());
        $favicon = htmlspecialchars(self::$favicon);
        
        return "
            <title>{$appName}</title>
            <link rel='shortcut icon' href='{$favicon}'>
            <style>" . self::getCssVars() . "</style>
        ";
    }
}