<?php
/**
 * Procurement ERP - FBR Digital Invoicing Integration
 * 
 * Handles Pakistan FBR Digital Invoicing integration requirements
 */

namespace App\Modules\Tax;

use App\Core\Database;
use App\Core\WhiteLabel;

class FBRDigitalInvoicing {
    private Database $db;
    private $apiEndpoint = 'https://iris.fbr.gov.pk/api/v1.0/digital-invoicing';
    private $apiKey;
    private $posid;
    private $businessNTN;
    
    public function __construct() {
        $this->db = new Database();
        $this->apiKey = $_ENV['FBR_API_KEY'] ?? null;
        $this->posid = $_ENV['FBR_POS_ID'] ?? null;
        $this->businessNTN = $_ENV['FBR_BUSINESS_NTN'] ?? null;
    }
    
    /**
     * Submit invoice to FBR Digital Invoicing system
     * 
     * @param array $invoice Invoice data
     * @return array Response from FBR
     */
    public function submitInvoice($invoice) {
        if (!$this->apiKey || !$this->posid || !$this->businessNTN) {
            throw new \Exception("FBR API credentials are not configured");
        }
        
        $requestData = $this->prepareInvoiceData($invoice);
        
        // In production, this would make a real HTTP request
        // Here we'll simulate the response for development
        if ($_ENV['APP_ENV'] === 'production') {
            return $this->sendToFBR($requestData);
        } else {
            return $this->simulateResponse($requestData);
        }
    }
    
    /**
     * Prepare invoice data according to FBR API specifications
     * 
     * @param array $invoice Internal invoice data
     * @return array Formatted data for FBR API
     */
    private function prepareInvoiceData($invoice) {
        // Company information
        $companyInfo = [
            'ntn' => $this->businessNTN,
            'name' => WhiteLabel::$companyName,
            'address' => WhiteLabel::$companyAddress
        ];
        
        // Buyer information
        $buyerInfo = [
            'name' => $invoice['buyer_name'],
            'ntn' => $invoice['buyer_ntn'] ?? '',
            'cnic' => $invoice['buyer_cnic'] ?? '',
            'phoneNumber' => $invoice['buyer_phone'] ?? ''
        ];
        
        // Format items according to FBR requirements
        $items = [];
        foreach ($invoice['items'] as $item) {
            $items[] = [
                'itemCode' => $item['item_code'],
                'itemName' => $item['item_name'],
                'quantity' => $item['quantity'],
                'pctCode' => $item['pct_code'] ?? '0000-0000',
                'unitPrice' => $item['unit_price'],
                'salesTaxRate' => $item['tax_rate'] ?? PakistanTaxRegulations::STANDARD_SALES_TAX_RATE,
                'salesTaxAmount' => $item['tax_amount'],
                'discount' => $item['discount'] ?? 0,
                'totalAmount' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0) + $item['tax_amount'],
                'furtherTaxAmount' => $item['further_tax'] ?? 0,
                'thirdScheduleRate' => $item['third_schedule_rate'] ?? 0,
                'thirdScheduleAmount' => $item['third_schedule_amount'] ?? 0
            ];
        }
        
        // Format according to FBR Digital Invoice API v1.12
        return [
            'invoiceNumber' => $invoice['invoice_number'],
            'invoiceDate' => date('Y-m-d\TH:i:s', strtotime($invoice['invoice_date'])),
            'posID' => $this->posid,
            'buyerNTN' => $buyerInfo['ntn'],
            'buyerCNIC' => $buyerInfo['cnic'],
            'buyerName' => $buyerInfo['name'],
            'buyerPhoneNumber' => $buyerInfo['phoneNumber'],
            'items' => $items,
            'totalSaleValue' => $invoice['subtotal'],
            'totalTaxCharged' => $invoice['total_tax'],
            'totalBillAmount' => $invoice['total_amount'],
            'paymentMode' => $invoice['payment_mode'] ?? 'Credit',
            'invoiceType' => $invoice['invoice_type'] ?? 'Standard'
        ];
    }
    
    /**
     * Send data to FBR Digital Invoicing API
     * 
     * @param array $data Prepared invoice data
     * @return array API response
     */
    private function sendToFBR($data) {
        // This would be implemented with actual HTTP request to FBR API
        // Using cURL or Guzzle HTTP client
        
        $ch = curl_init($this->apiEndpoint);
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
            'Accept: application/json'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($response === false) {
            throw new \Exception("FBR API request failed");
        }
        
        $responseData = json_decode($response, true);
        if ($httpCode != 200) {
            $this->logApiError($httpCode, $responseData, $data);
            throw new \Exception("FBR API error: " . ($responseData['message'] ?? 'Unknown error'));
        }
        
        // Log successful submission
        $this->logSuccessfulSubmission($data, $responseData);
        
        return $responseData;
    }
    
    /**
     * Simulate FBR API response for development environment
     * 
     * @param array $data Invoice data sent to FBR
     * @return array Simulated response
     */
    private function simulateResponse($data) {
        // Generate an Invoicing Receipt Identification Number (IRIN)
        $irin = 'DEV-' . date('YmdHis') . '-' . substr(md5($data['invoiceNumber']), 0, 8);
        
        return [
            'success' => true,
            'irin' => $irin,
            'invoiceNumber' => $data['invoiceNumber'],
            'dateTime' => date('Y-m-d\TH:i:s'),
            'verificationURL' => 'https://iris.fbr.gov.pk/verify/' . $irin,
            'code' => '100',
            'response' => 'Invoice has been successfully processed and saved'
        ];
    }
    
    /**
     * Log API error for debugging and compliance
     * 
     * @param int $httpCode HTTP response code
     * @param array $response API response
     * @param array $requestData Data sent in the request
     */
    private function logApiError($httpCode, $response, $requestData) {
        $query = "INSERT INTO fbr_api_logs (
                    invoice_number, 
                    http_code, 
                    request_data, 
                    response_data, 
                    error_message,
                    created_at
                  ) VALUES (?, ?, ?, ?, ?, NOW())";
                  
        $stmt = $this->db->prepare($query);
        $requestJson = json_encode($requestData);
        $responseJson = json_encode($response);
        $errorMessage = $response['message'] ?? 'Unknown error';
        
        $stmt->bind_param(
            'sisss',
            $requestData['invoiceNumber'],
            $httpCode,
            $requestJson,
            $responseJson,
            $errorMessage
        );
        
        $stmt->execute();
    }
    
    /**
     * Log successful API submission for compliance
     * 
     * @param array $requestData Data sent in the request
     * @param array $response API response
     */
    private function logSuccessfulSubmission($requestData, $response) {
        $query = "INSERT INTO fbr_digital_invoices (
                    invoice_number,
                    irin,
                    invoice_date,
                    buyer_name,
                    buyer_ntn,
                    total_amount,
                    total_tax,
                    request_data,
                    response_data,
                    created_at
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                  
        $stmt = $this->db->prepare($query);
        $requestJson = json_encode($requestData);
        $responseJson = json_encode($response);
        $invoiceDate = date('Y-m-d H:i:s', strtotime($requestData['invoiceDate']));
        
        $stmt->bind_param(
            'sssssddss',
            $requestData['invoiceNumber'],
            $response['irin'],
            $invoiceDate,
            $requestData['buyerName'],
            $requestData['buyerNTN'],
            $requestData['totalBillAmount'],
            $requestData['totalTaxCharged'],
            $requestJson,
            $responseJson
        );
        
        $stmt->execute();
    }
    
    /**
     * Verify invoice with FBR
     * 
     * @param string $irin FBR Invoice Receipt Identification Number
     * @return array Verification response
     */
    public function verifyInvoice($irin) {
        $verifyEndpoint = $this->apiEndpoint . '/verify/' . $irin;
        
        if ($_ENV['APP_ENV'] === 'production') {
            // This would make a real HTTP request to FBR verification endpoint
            $ch = curl_init($verifyEndpoint);
            
            $headers = [
                'Authorization: Bearer ' . $this->apiKey,
                'Accept: application/json'
            ];
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response, true);
        } else {
            // Simulate verification response
            return [
                'success' => true,
                'irin' => $irin,
                'verified' => true,
                'invoiceNumber' => 'INV-' . substr($irin, -8),
                'invoiceDate' => date('Y-m-d\TH:i:s', strtotime('-1 day')),
                'buyerName' => 'Test Buyer',
                'totalAmount' => 5000.00,
                'status' => 'Active'
            ];
        }
    }
    
    /**
     * Generate a QR code for invoice verification
     * 
     * @param string $irin FBR Invoice Receipt Identification Number
     * @return string Base64 encoded QR code image
     */
    public function generateVerificationQR($irin) {
        // In a real implementation, this would use a QR code library
        // For now, we'll return a placeholder URL
        $verificationUrl = 'https://iris.fbr.gov.pk/verify/' . $irin;
        
        return [
            'verification_url' => $verificationUrl,
            'qr_data' => $verificationUrl,
            // In real implementation: return base64_encode(QRCodeGenerator::generate($verificationUrl))
            'qr_image_placeholder' => 'base64_encoded_qr_image_would_be_here'
        ];
    }
    
    /**
     * Get Digital Invoicing compliance status
     * 
     * @return array Compliance status information
     */
    public function getComplianceStatus() {
        // Calculate compliance metrics from our database
        $metrics = $this->calculateComplianceMetrics();
        
        return [
            'status' => $metrics['compliance_rate'] >= 95 ? 'Compliant' : 'Non-Compliant',
            'compliance_rate' => $metrics['compliance_rate'],
            'total_invoices_issued' => $metrics['total_invoices'],
            'total_invoices_reported' => $metrics['reported_invoices'],
            'missing_invoices' => $metrics['total_invoices'] - $metrics['reported_invoices'],
            'last_submission_date' => $metrics['last_submission_date'],
            'errors_last_30_days' => $metrics['recent_errors']
        ];
    }
    
    /**
     * Calculate compliance metrics from database records
     * 
     * @return array Compliance metrics
     */
    private function calculateComplianceMetrics() {
        // In a real implementation, this would query the database
        // For now, returning placeholder values
        return [
            'total_invoices' => 120,
            'reported_invoices' => 118,
            'compliance_rate' => 98.33,
            'last_submission_date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'recent_errors' => 1
        ];
    }
}