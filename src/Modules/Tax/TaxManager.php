<?php
/**
 * Procurement ERP - Tax Manager
 * 
 * Handles tax calculations, FBR integration, and compliance with Pakistan tax laws
 */

namespace App\Modules\Tax;

use App\Core\Database;

class TaxManager {
    private Database $db;
    
    // Tax rates and constants
    const STANDARD_GST_RATE = 18.0; // Standard GST rate as per Sales Tax Act
    const REDUCED_GST_RATES = [
        'essential_goods' => 8.5,
        'exported_goods' => 0.0
    ];
    
    // FBR Integration endpoints
    private $fbrApiEndpoint = 'https://iris.fbr.gov.pk/api/v1.0/';
    private $digitalInvoicingEndpoint = 'https://iris.fbr.gov.pk/api/v1.0/digital-invoicing/';
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Calculate sales tax for an invoice
     * 
     * @param float $amount Total amount before tax
     * @param string $category Product/service category for special rates
     * @param bool $exempted Whether the item is tax exempted
     * @return float Calculated tax amount
     */
    public function calculateSalesTax($amount, $category = 'standard', $exempted = false) {
        if ($exempted) {
            return 0.0;
        }
        
        if (isset(self::REDUCED_GST_RATES[$category])) {
            return $amount * (self::REDUCED_GST_RATES[$category] / 100);
        }
        
        return $amount * (self::STANDARD_GST_RATE / 100);
    }
    
    /**
     * Generate FBR-compliant digital invoice
     * 
     * @param array $invoiceData Invoice details
     * @return array Response with invoice ID and status
     */
    public function generateDigitalInvoice($invoiceData) {
        // Format invoice data according to FBR Digital Invoicing API spec
        $fbrInvoiceData = $this->formatInvoiceForFBR($invoiceData);
        
        // In production, this would make an actual API call to FBR
        // For now, we'll simulate the response
        $response = $this->simulateFBRInvoiceSubmission($fbrInvoiceData);
        
        // Store the FBR response in our database for audit trail
        $this->storeDigitalInvoiceRecord($invoiceData, $response);
        
        return $response;
    }
    
    /**
     * Format invoice data according to FBR Digital Invoicing API specifications
     * 
     * @param array $invoiceData Our internal invoice data
     * @return array FBR-formatted invoice data
     */
    private function formatInvoiceForFBR($invoiceData) {
        // Implementation based on FBR Technical Documentation v1.12
        // https://download1.fbr.gov.pk/Docs/20257301172130815TechnicalDocumentationforDIAPIV1.12.pdf
        
        return [
            'invoiceNumber' => $invoiceData['invoice_number'],
            'invoiceDate' => date('Y-m-d\TH:i:s', strtotime($invoiceData['invoice_date'])),
            'buyerNTN' => $invoiceData['buyer_ntn'] ?? '',
            'buyerCNIC' => $invoiceData['buyer_cnic'] ?? '',
            'buyerName' => $invoiceData['buyer_name'],
            'buyerPhoneNumber' => $invoiceData['buyer_phone'] ?? '',
            'items' => $this->formatInvoiceItemsForFBR($invoiceData['items']),
            'totalSaleValue' => $invoiceData['total_amount'],
            'totalTaxCharged' => $invoiceData['total_tax'],
            'totalBillAmount' => $invoiceData['total_amount'] + $invoiceData['total_tax'],
            'paymentMode' => $invoiceData['payment_mode'] ?? 'Credit',
            'invoiceType' => $invoiceData['invoice_type'] ?? 'Standard'
        ];
    }
    
    /**
     * Format invoice line items according to FBR specifications
     * 
     * @param array $items Our internal items data
     * @return array FBR-formatted items
     */
    private function formatInvoiceItemsForFBR($items) {
        $fbrItems = [];
        
        foreach ($items as $item) {
            $fbrItems[] = [
                'itemCode' => $item['item_code'],
                'itemName' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price'],
                'taxRate' => $item['tax_rate'] ?? self::STANDARD_GST_RATE,
                'taxAmount' => $item['tax_amount'],
                'totalAmount' => ($item['quantity'] * $item['unit_price']) + $item['tax_amount']
            ];
        }
        
        return $fbrItems;
    }
    
    /**
     * Simulate FBR API response for development purposes
     * In production, this would be replaced with actual API calls
     * 
     * @param array $fbrInvoiceData Formatted invoice data
     * @return array Simulated FBR API response
     */
    private function simulateFBRInvoiceSubmission($fbrInvoiceData) {
        // Generate a unique FBR Invoice Number (IRIN)
        $irin = 'IRIN' . date('Ymd') . rand(100000, 999999);
        
        return [
            'success' => true,
            'irin' => $irin,
            'fbr_response_code' => '200',
            'fbr_timestamp' => date('Y-m-d\TH:i:s'),
            'verification_url' => 'https://iris.fbr.gov.pk/verify-invoice/' . $irin
        ];
    }
    
    /**
     * Store digital invoice record in database for audit trail
     * 
     * @param array $invoiceData Original invoice data
     * @param array $fbrResponse FBR API response
     * @return bool Success status
     */
    private function storeDigitalInvoiceRecord($invoiceData, $fbrResponse) {
        $query = "INSERT INTO fbr_digital_invoices (
                    invoice_id, 
                    irin, 
                    submission_date, 
                    response_code, 
                    response_data,
                    verification_url
                  ) VALUES (?, ?, NOW(), ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $responseData = json_encode($fbrResponse);
        $stmt->bind_param(
            'isiss', 
            $invoiceData['id'],
            $fbrResponse['irin'],
            $fbrResponse['fbr_response_code'],
            $responseData,
            $fbrResponse['verification_url']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Validate NTN (National Tax Number) format
     * 
     * @param string $ntn NTN to validate
     * @return bool Is valid NTN
     */
    public function validateNTN($ntn) {
        // NTN is typically 7-9 digits
        return preg_match('/^[0-9]{7,9}$/', $ntn);
    }
    
    /**
     * Validate CNIC (Computerized National Identity Card) format
     * 
     * @param string $cnic CNIC to validate
     * @return bool Is valid CNIC
     */
    public function validateCNIC($cnic) {
        // CNIC is 13 digits, sometimes formatted as XXXXX-XXXXXXX-X
        $cnic = str_replace(['-', ' '], '', $cnic);
        return preg_match('/^[0-9]{13}$/', $cnic);
    }
    
    /**
     * Get tax compliance status report
     * 
     * @return array Status report
     */
    public function getTaxComplianceStatus() {
        // This would connect to FBR API to get actual compliance status
        // For now, returning simulated data
        return [
            'status' => 'compliant',
            'last_filing_date' => date('Y-m-d', strtotime('-1 month')),
            'next_filing_due' => date('Y-m-d', strtotime('+2 weeks')),
            'outstanding_amount' => 0,
            'compliance_score' => 95
        ];
    }
}