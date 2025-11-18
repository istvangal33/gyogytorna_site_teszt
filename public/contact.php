<?php
/**
 * Contact Form Handler
 * 
 * This script processes contact form submissions with:
 * - reCAPTCHA validation
 * - File upload handling
 * - Email sending via SMTP
 * - Security measures (honeypot, validation)
 */

// Load configuration
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Server configuration error. Please contact the administrator.'
    ]));
}
require_once $configFile;

// Set header for JSON response
header('Content-Type: application/json; charset=utf-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

/**
 * Validate reCAPTCHA token
 */
function validateRecaptcha($token) {
    if (empty($token)) {
        return false;
    }
    
    $secret = RECAPTCHA_SECRET_KEY;
    $response = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $token
    );
    
    $responseData = json_decode($response);
    return $responseData->success ?? false;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate Hungarian phone number
 */
function validateHungarianPhone($phone) {
    $cleaned = preg_replace('/[\s-]/', '', $phone);
    $patterns = [
        '/^\+3620\d{7}$/',
        '/^\+3630\d{7}$/',
        '/^\+3631\d{7}$/',
        '/^\+3670\d{7}$/',
        '/^\+361\d{7}$/',
        '/^\+36\d{2}\d{6}$/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $cleaned)) {
            return true;
        }
    }
    return false;
}

/**
 * Handle file uploads
 */
function handleFileUploads() {
    $uploadedFiles = [];
    
    if (!isset($_FILES['documents']) || empty($_FILES['documents']['name'][0])) {
        return $uploadedFiles;
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    $fileCount = count($_FILES['documents']['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['documents']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        $fileName = $_FILES['documents']['name'][$i];
        $fileSize = $_FILES['documents']['size'][$i];
        $fileTmp = $_FILES['documents']['tmp_name'][$i];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Validate file type
        if (!in_array($fileExt, ALLOWED_FILE_TYPES)) {
            continue;
        }
        
        // Validate file size
        if ($fileSize > MAX_FILE_SIZE) {
            continue;
        }
        
        // Generate unique filename
        $uniqueFileName = uniqid() . '_' . time() . '.' . $fileExt;
        $destination = UPLOAD_DIR . $uniqueFileName;
        
        if (move_uploaded_file($fileTmp, $destination)) {
            $uploadedFiles[] = [
                'original_name' => $fileName,
                'saved_name' => $uniqueFileName,
                'path' => $destination,
                'size' => $fileSize
            ];
        }
    }
    
    return $uploadedFiles;
}

try {
    // Get form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $honeypot = $_POST[HONEYPOT_FIELD] ?? '';
    $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
    
    // Honeypot check - if filled, it's likely a bot
    if (ENABLE_HONEYPOT && !empty($honeypot)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid submission.']);
        exit;
    }
    
    // Validate required fields
    if (empty($name) || strlen($name) < 2) {
        throw new Exception('A név legalább 2 karakter hosszú legyen.');
    }
    
    if (!validateEmail($email)) {
        throw new Exception('Érvényes email cím szükséges.');
    }
    
    if (!validateHungarianPhone($phone)) {
        throw new Exception('Érvényes magyar telefonszám szükséges.');
    }
    
    // Validate reCAPTCHA
    if (!validateRecaptcha($recaptchaToken)) {
        throw new Exception('reCAPTCHA ellenőrzés sikertelen. Kérjük, próbálja újra.');
    }
    
    // Handle file uploads
    $uploadedFiles = handleFileUploads();
    
    // Use PHPMailer if available, otherwise use mail()
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = MAIL_CHARSET;
        
        // Email settings
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL);
        $mail->addReplyTo($email, $name);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Új kapcsolatfelvételi űrlap: ' . $name;
        
        $mailBody = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #004A6D; color: white; padding: 20px; text-align: center; }
                .content { background-color: #f9f9f9; padding: 20px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #004A6D; }
                .value { margin-top: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Új kapcsolatfelvételi üzenet</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Név:</div>
                        <div class='value'>{$name}</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>{$email}</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Telefon:</div>
                        <div class='value'>{$phone}</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Üzenet:</div>
                        <div class='value'>" . nl2br($message) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Időpont:</div>
                        <div class='value'>" . date('Y-m-d H:i:s') . "</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $mailBody;
        $mail->AltBody = "Név: {$name}\nEmail: {$email}\nTelefon: {$phone}\nÜzenet: {$message}";
        
        // Attach uploaded files
        foreach ($uploadedFiles as $file) {
            $mail->addAttachment($file['path'], $file['original_name']);
        }
        
        $mail->send();
    } else {
        // Fallback to basic mail() function
        $to = ADMIN_EMAIL;
        $subject = 'Új kapcsolatfelvételi űrlap: ' . $name;
        $mailMessage = "Név: {$name}\nEmail: {$email}\nTelefon: {$phone}\nÜzenet: {$message}";
        $headers = "From: " . SMTP_FROM_EMAIL . "\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        if (!mail($to, $subject, $mailMessage, $headers)) {
            throw new Exception('Email küldése sikertelen.');
        }
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Köszönjük a megkeresést! Hamarosan felvesszük Önnel a kapcsolatot.',
        'files_uploaded' => count($uploadedFiles)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
