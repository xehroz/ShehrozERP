<?php
/**
 * Procurement ERP - Pakistan Tax Regulations
 * 
 * Contains tax rates, rules, and compliance requirements for Pakistan
 */

namespace App\Modules\Tax;

class PakistanTaxRegulations {
    // Sales Tax Rates as per Sales Tax Act, 1990
    const STANDARD_SALES_TAX_RATE = 18.0;
    
    // Income Tax Rates based on Income Tax Ordinance, 2001
    const CORPORATE_TAX_RATE = 29.0; // For tax year 2023
    const MINIMUM_TAX_RATE = 1.25;   // Minimum tax on turnover
    
    // Withholding Tax Rates
    const WITHHOLDING_RATES = [
        'goods_general' => 4.5,
        'goods_company' => 4.0,
        'services_general' => 8.0, 
        'services_company' => 8.0,
        'utilities' => 7.5,
        'rent' => 15.0,
        'contract_execution' => 7.0,
        'imports' => 5.5
    ];
    
    // Federal Excise Duty Rates (select common ones)
    const FEDERAL_EXCISE_RATES = [
        'telecommunications' => 16.0,
        'banking_services' => 16.0,
        'insurance_services' => 16.0,
        'carbonated_beverages' => 13.0,
        'cement' => 2000 // PKR per ton
    ];
    
    // FBR Tax Periods
    const TAX_PERIODS = [
        'monthly' => [
            'name' => 'Monthly',
            'days' => 30,
            'filing_deadline_days' => 15 // 15th of next month
        ],
        'quarterly' => [
            'name' => 'Quarterly',
            'days' => 90,
            'filing_deadline_days' => 18 // 18th of the month following quarter end
        ],
        'annual' => [
            'name' => 'Annual',
            'days' => 365,
            'filing_deadline_days' => 30 // 30th of September
        ]
    ];
    
    /**
     * Get current sales tax rate based on product category
     * 
     * @param string $category Product/service category
     * @return float Tax rate percentage
     */
    public static function getSalesTaxRate($category = 'standard') {
        $exemptedCategories = self::getExemptedCategories();
        if (in_array($category, $exemptedCategories)) {
            return 0.0;
        }
        
        $reducedRateCategories = self::getReducedRateCategories();
        if (isset($reducedRateCategories[$category])) {
            return $reducedRateCategories[$category];
        }
        
        return self::STANDARD_SALES_TAX_RATE;
    }
    
    /**
     * Get withholding tax rate based on payment type
     * 
     * @param string $paymentType Type of payment
     * @return float Withholding tax rate percentage
     */
    public static function getWithholdingTaxRate($paymentType) {
        return self::WITHHOLDING_RATES[$paymentType] ?? 0.0;
    }
    
    /**
     * Get categories exempt from sales tax
     * 
     * @return array List of exempt categories
     */
    public static function getExemptedCategories() {
        return [
            'essential_food_items',
            'live_animals',
            'educational_books',
            'medical_devices',
            'exports'
        ];
    }
    
    /**
     * Get categories with reduced sales tax rates
     * 
     * @return array Map of categories to reduced rates
     */
    public static function getReducedRateCategories() {
        return [
            'fertilizers' => 5.0,
            'tractors' => 5.0,
            'local_supplies_of_zero_rated_sectors' => 10.0,
            'utility_bills' => 8.0
        ];
    }
    
    /**
     * Get FBR filing deadline for current period
     * 
     * @param string $periodType Type of filing period (monthly, quarterly, annual)
     * @param string $periodEndDate End date of the period (YYYY-MM-DD)
     * @return string Filing deadline date (YYYY-MM-DD)
     */
    public static function getFilingDeadline($periodType, $periodEndDate) {
        $endDate = new \DateTime($periodEndDate);
        
        if ($periodType === 'annual' && $endDate->format('m-d') === '06-30') {
            // For tax year ending June 30, deadline is Sept 30
            return $endDate->format('Y') . '-09-30';
        }
        
        if ($periodType === 'monthly') {
            // For monthly, it's 15th of next month
            $endDate->add(new \DateInterval('P1M'));
            return $endDate->format('Y-m') . '-15';
        }
        
        if ($periodType === 'quarterly') {
            // For quarterly, it's 18th of next month
            $endDate->add(new \DateInterval('P1M'));
            return $endDate->format('Y-m') . '-18';
        }
        
        // Default: add filing deadline days from period configuration
        $deadlineDays = self::TAX_PERIODS[$periodType]['filing_deadline_days'] ?? 15;
        $endDate->add(new \DateInterval('P' . $deadlineDays . 'D'));
        return $endDate->format('Y-m-d');
    }
    
    /**
     * Get FBR Digital Invoicing requirements
     * 
     * @return array Digital invoicing requirements
     */
    public static function getDigitalInvoicingRequirements() {
        return [
            'format_version' => '1.12',
            'required_fields' => [
                'invoice_number',
                'invoice_date',
                'seller_name',
                'seller_ntn',
                'buyer_name',
                'item_details',
                'quantity',
                'rate',
                'value_excluding_sales_tax',
                'sales_tax_rate',
                'sales_tax_amount',
                'value_including_sales_tax'
            ],
            'submission_deadline_hours' => 24, // Must submit within 24 hours of issuance
            'api_endpoint' => 'https://iris.fbr.gov.pk/api/v1.0/digital-invoicing',
            'documentation_url' => 'https://download1.fbr.gov.pk/Docs/20257301172130815TechnicalDocumentationforDIAPIV1.12.pdf'
        ];
    }
    
    /**
     * Check if a business is required to use FBR Digital Invoicing
     * 
     * @param string $businessType Type of business
     * @param float $annualTurnover Annual turnover in PKR
     * @return bool Is digital invoicing mandatory
     */
    public static function isDigitalInvoicingRequired($businessType, $annualTurnover) {
        $thresholds = [
            'tier1_retailer' => 0, // Always required
            'corporate' => 50000000, // 50 million PKR
            'manufacturer' => 100000000, // 100 million PKR
            'importer' => 100000000, // 100 million PKR
            'exporter' => 50000000, // 50 million PKR
            'wholesaler' => 100000000, // 100 million PKR
            'distributor' => 100000000 // 100 million PKR
        ];
        
        $threshold = $thresholds[$businessType] ?? PHP_FLOAT_MAX;
        return $annualTurnover >= $threshold;
    }
    
    /**
     * Get current tax laws reference information
     * 
     * @return array Tax laws information
     */
    public static function getTaxLawsReference() {
        return [
            'income_tax' => [
                'name' => 'Income Tax Ordinance',
                'year' => '2001',
                'description' => 'Governs taxation on income for individuals, companies, and associations.',
                'latest_amendment' => 'Tax Laws (Amendment) Ordinance 2025'
            ],
            'sales_tax' => [
                'name' => 'Sales Tax Act',
                'year' => '1990',
                'description' => 'Applies to the sale and import of goods with standard rate of 18%.',
                'latest_amendment' => 'Finance Act 2024'
            ],
            'federal_excise' => [
                'name' => 'Federal Excise Act',
                'year' => '2005',
                'description' => 'Covers excise duties on specific goods and services like tobacco and telecom.',
                'latest_amendment' => 'Tax Laws (Amendment) Ordinance 2025'
            ],
            'customs' => [
                'name' => 'Customs Act',
                'year' => '1969',
                'description' => 'Regulates import/export duties, tariffs, and customs procedures.',
                'latest_amendment' => 'Finance Act 2024'
            ]
        ];
    }
}