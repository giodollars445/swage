<?php
// Enhanced Email Service with Configuration Support
class EmailService {
    private $config;
    
    public function __construct() {
        // Load configuration
        if (file_exists('email_config.php')) {
            $this->config = include 'email_config.php';
        } else {
            // Default configuration
            $this->config = [
                'from_email' => 'noreply@oldschoolbarber.com',
                'from_name' => 'Old School Barber',
                'reply_to' => 'info@oldschoolbarber.com',
                'business_name' => 'Old School Barber',
                'business_tagline' => 'Tradizione, stile e passione dal 1985',
                'business_phone' => '+39 123 456 7890',
                'business_address' => 'Via Roma 123, 00100 Roma',
                'business_hours' => 'Mar-Sab 9:00-18:30',
                'website_url' => '',
                'smtp_enabled' => false,
                'include_cancellation_link' => true,
                'important_notes' => [
                    'Ti preghiamo di arrivare 5 minuti prima dell\'orario prenotato',
                    'In caso di ritardo superiore a 15 minuti, la prenotazione potrebbe essere cancellata',
                    'Per cancellazioni o modifiche, contattaci almeno 24 ore prima'
                ]
            ];
        }
    }
    
    public function sendBookingConfirmation($to_email, $booking_data) {
        if (empty($to_email) || !filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        $subject = "Conferma Prenotazione - " . $this->config['business_name'];
        $html_body = $this->generateBookingEmailHTML($booking_data);
        $text_body = $this->generateBookingEmailText($booking_data);
        
        $result = $this->sendEmail($to_email, $subject, $html_body, $text_body);
        
        // Send admin notification if enabled
        if ($result && !empty($this->config['send_admin_notifications']) && !empty($this->config['admin_email'])) {
            $this->sendAdminNotification($booking_data);
        }
        
        return $result;
    }
    
    public function sendAdminNotification($booking_data) {
        if (empty($this->config['admin_email'])) {
            return false;
        }
        
        $subject = "Nuova Prenotazione - " . $this->config['business_name'];
        $html_body = $this->generateAdminNotificationHTML($booking_data);
        $text_body = $this->generateAdminNotificationText($booking_data);
        
        return $this->sendEmail($this->config['admin_email'], $subject, $html_body, $text_body);
    }
    
    private function generateBookingEmailHTML($data) {
        $formatted_date = date('d/m/Y', strtotime($data['data_prenotazione']));
        $formatted_time = date('H:i', strtotime($data['orario']));
        $current_url = $this->getCurrentUrl();
        
        $html = '
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Conferma Prenotazione</title>
            <style>
                body {
                    font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
                    line-height: 1.6;
                    color: ' . ($this->config['text_color'] ?? '#333333') . ';
                    background-color: ' . ($this->config['background_color'] ?? '#f8fafc') . ';
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, ' . ($this->config['primary_color'] ?? '#d4af37') . ' 0%, ' . ($this->config['secondary_color'] ?? '#ffd700') . ' 100%);
                    color: #1a1a2e;
                    padding: 2rem;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 1.8rem;
                    font-weight: 700;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5rem;
                }
                .content {
                    padding: 2rem;
                }
                .booking-details {
                    background-color: ' . ($this->config['background_color'] ?? '#f8fafc') . ';
                    border-radius: 8px;
                    padding: 1.5rem;
                    margin: 1.5rem 0;
                    border-left: 4px solid ' . ($this->config['primary_color'] ?? '#d4af37') . ';
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0.5rem 0;
                    border-bottom: 1px solid #e2e8f0;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #4a5568;
                }
                .detail-value {
                    font-weight: 500;
                    color: #1a202c;
                }
                .important-info {
                    background-color: #fef3cd;
                    border: 1px solid #fbbf24;
                    border-radius: 8px;
                    padding: 1rem;
                    margin: 1.5rem 0;
                }
                .footer {
                    background-color: #1a1a2e;
                    color: #ffffff;
                    padding: 1.5rem;
                    text-align: center;
                    font-size: 0.9rem;
                }
                .contact-info {
                    margin-top: 1rem;
                    padding-top: 1rem;
                    border-top: 1px solid #e2e8f0;
                }
                .btn {
                    display: inline-block;
                    background: linear-gradient(135deg, ' . ($this->config['primary_color'] ?? '#d4af37') . ' 0%, ' . ($this->config['secondary_color'] ?? '#ffd700') . ' 100%);
                    color: #1a1a2e;
                    padding: 0.8rem 1.5rem;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    margin: 1rem 0;
                }
                @media (max-width: 600px) {
                    .container {
                        margin: 0;
                        border-radius: 0;
                    }
                    .header, .content, .footer {
                        padding: 1.5rem 1rem;
                    }
                    .detail-row {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 0.3rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✂️ ' . htmlspecialchars($this->config['business_name']) . '</h1>
                    <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem;">Prenotazione Confermata</p>
                </div>
                
                <div class="content">
                    <h2 style="color: #1a202c; margin-bottom: 1rem;">Ciao ' . htmlspecialchars($data['nome']) . '!</h2>
                    
                    <p>La tua prenotazione è stata confermata con successo. Ecco i dettagli del tuo appuntamento:</p>
                    
                    <div class="booking-details">
                        <h3 style="margin-top: 0; color: ' . ($this->config['primary_color'] ?? '#d4af37') . ';">📋 Dettagli Prenotazione</h3>
                        
                        <div class="detail-row">
                            <span class="detail-label">👤 Nome:</span>
                            <span class="detail-value">' . htmlspecialchars($data['nome']) . '</span>
                        </div>';
                        
        if (!empty($data['email'])) {
            $html .= '
                        <div class="detail-row">
                            <span class="detail-label">📧 Email:</span>
                            <span class="detail-value">' . htmlspecialchars($data['email']) . '</span>
                        </div>';
        }
        
        if (!empty($data['telefono'])) {
            $html .= '
                        <div class="detail-row">
                            <span class="detail-label">📞 Telefono:</span>
                            <span class="detail-value">' . htmlspecialchars($data['telefono']) . '</span>
                        </div>';
        }
        
        $html .= '
                        <div class="detail-row">
                            <span class="detail-label">✂️ Servizio:</span>
                            <span class="detail-value">' . htmlspecialchars($data['servizio']) . '</span>
                        </div>';
                        
        if (!empty($data['operatore_nome'])) {
            $html .= '
                        <div class="detail-row">
                            <span class="detail-label">👨‍💼 Operatore:</span>
                            <span class="detail-value">' . htmlspecialchars($data['operatore_nome']) . '</span>
                        </div>';
        }
        
        $html .= '
                        <div class="detail-row">
                            <span class="detail-label">📅 Data:</span>
                            <span class="detail-value">' . $formatted_date . '</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">🕐 Orario:</span>
                            <span class="detail-value">' . $formatted_time . '</span>
                        </div>
                    </div>';
        
        // Add important information if configured
        if (!empty($this->config['important_notes'])) {
            $html .= '
                    <div class="important-info">
                        <h4 style="margin-top: 0; color: #92400e;">⚠️ Informazioni Importanti</h4>
                        <ul style="margin: 0; padding-left: 1.2rem;">';
            
            foreach ($this->config['important_notes'] as $note) {
                $html .= '<li>' . htmlspecialchars($note) . '</li>';
            }
            
            $html .= '
                        </ul>
                    </div>';
        }
        
        // Add cancellation link if enabled
        if (!empty($this->config['include_cancellation_link'])) {
            $html .= '
                    <div style="text-align: center;">
                        <a href="' . $current_url . '/cancel_booking.php" class="btn">
                            Cancella Prenotazione
                        </a>
                    </div>';
        }
        
        // Add contact information
        $html .= '
                    <div class="contact-info">
                        <h4 style="color: #1a202c;">📞 Contatti</h4>
                        <p><strong>Telefono:</strong> ' . htmlspecialchars($this->config['business_phone']) . '</p>
                        <p><strong>Indirizzo:</strong> ' . htmlspecialchars($this->config['business_address']) . '</p>
                        <p><strong>Orari:</strong> ' . htmlspecialchars($this->config['business_hours']) . '</p>
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong>' . htmlspecialchars($this->config['business_name']) . '</strong></p>
                    <p>' . htmlspecialchars($this->config['business_tagline']) . '</p>';
        
        if (!empty($this->config['custom_footer_text'])) {
            $html .= '<p>' . htmlspecialchars($this->config['custom_footer_text']) . '</p>';
        }
        
        $html .= '
                    <p style="font-size: 0.8rem; margin-top: 1rem; opacity: 0.8;">
                        Questa è una email automatica, non rispondere a questo messaggio.
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generateBookingEmailText($data) {
        $formatted_date = date('d/m/Y', strtotime($data['data_prenotazione']));
        $formatted_time = date('H:i', strtotime($data['orario']));
        
        $text = strtoupper($this->config['business_name']) . " - PRENOTAZIONE CONFERMATA\n\n";
        $text .= "Ciao " . $data['nome'] . "!\n\n";
        $text .= "La tua prenotazione è stata confermata con successo.\n\n";
        $text .= "DETTAGLI PRENOTAZIONE:\n";
        $text .= "- Nome: " . $data['nome'] . "\n";
        
        if (!empty($data['email'])) {
            $text .= "- Email: " . $data['email'] . "\n";
        }
        
        if (!empty($data['telefono'])) {
            $text .= "- Telefono: " . $data['telefono'] . "\n";
        }
        
        $text .= "- Servizio: " . $data['servizio'] . "\n";
        
        if (!empty($data['operatore_nome'])) {
            $text .= "- Operatore: " . $data['operatore_nome'] . "\n";
        }
        
        $text .= "- Data: " . $formatted_date . "\n";
        $text .= "- Orario: " . $formatted_time . "\n\n";
        
        if (!empty($this->config['important_notes'])) {
            $text .= "INFORMAZIONI IMPORTANTI:\n";
            foreach ($this->config['important_notes'] as $note) {
                $text .= "- " . $note . "\n";
            }
            $text .= "\n";
        }
        
        $text .= "CONTATTI:\n";
        $text .= "Telefono: " . $this->config['business_phone'] . "\n";
        $text .= "Indirizzo: " . $this->config['business_address'] . "\n";
        $text .= "Orari: " . $this->config['business_hours'] . "\n\n";
        
        $text .= "Grazie per aver scelto " . $this->config['business_name'] . "!\n";
        $text .= $this->config['business_tagline'];
        
        return $text;
    }
    
    private function generateAdminNotificationHTML($data) {
        $formatted_date = date('d/m/Y', strtotime($data['data_prenotazione']));
        $formatted_time = date('H:i', strtotime($data['orario']));
        
        $html = '
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nuova Prenotazione</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #d4af37; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .booking-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .detail-row { padding: 5px 0; border-bottom: 1px solid #eee; }
                .detail-row:last-child { border-bottom: none; }
                .label { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Nuova Prenotazione Ricevuta</h1>
                </div>
                <div class="content">
                    <p>È stata ricevuta una nuova prenotazione:</p>
                    <div class="booking-details">
                        <div class="detail-row">
                            <span class="label">Nome:</span> ' . htmlspecialchars($data['nome']) . '
                        </div>';
        
        if (!empty($data['email'])) {
            $html .= '<div class="detail-row">
                            <span class="label">Email:</span> ' . htmlspecialchars($data['email']) . '
                        </div>';
        }
        
        if (!empty($data['telefono'])) {
            $html .= '<div class="detail-row">
                            <span class="label">Telefono:</span> ' . htmlspecialchars($data['telefono']) . '
                        </div>';
        }
        
        $html .= '<div class="detail-row">
                            <span class="label">Servizio:</span> ' . htmlspecialchars($data['servizio']) . '
                        </div>';
        
        if (!empty($data['operatore_nome'])) {
            $html .= '<div class="detail-row">
                            <span class="label">Operatore:</span> ' . htmlspecialchars($data['operatore_nome']) . '
                        </div>';
        }
        
        $html .= '<div class="detail-row">
                            <span class="label">Data:</span> ' . $formatted_date . '
                        </div>
                        <div class="detail-row">
                            <span class="label">Orario:</span> ' . $formatted_time . '
                        </div>
                    </div>
                    <p>Accedi al pannello di amministrazione per gestire questa prenotazione.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generateAdminNotificationText($data) {
        $formatted_date = date('d/m/Y', strtotime($data['data_prenotazione']));
        $formatted_time = date('H:i', strtotime($data['orario']));
        
        $text = "NUOVA PRENOTAZIONE RICEVUTA\n\n";
        $text .= "Nome: " . $data['nome'] . "\n";
        
        if (!empty($data['email'])) {
            $text .= "Email: " . $data['email'] . "\n";
        }
        
        if (!empty($data['telefono'])) {
            $text .= "Telefono: " . $data['telefono'] . "\n";
        }
        
        $text .= "Servizio: " . $data['servizio'] . "\n";
        
        if (!empty($data['operatore_nome'])) {
            $text .= "Operatore: " . $data['operatore_nome'] . "\n";
        }
        
        $text .= "Data: " . $formatted_date . "\n";
        $text .= "Orario: " . $formatted_time . "\n\n";
        $text .= "Accedi al pannello di amministrazione per gestire questa prenotazione.";
        
        return $text;
    }
    
    private function sendEmail($to_email, $subject, $html_body, $text_body) {
        // Prepare headers
        $headers = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>';
        $headers[] = 'Reply-To: ' . $this->config['reply_to'];
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'X-Priority: 3';
        
        // Try to send HTML email first
        $success = mail($to_email, $subject, $html_body, implode("\r\n", $headers));
        
        // If HTML fails, try plain text
        if (!$success) {
            $headers_text = array();
            $headers_text[] = 'MIME-Version: 1.0';
            $headers_text[] = 'Content-Type: text/plain; charset=UTF-8';
            $headers_text[] = 'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>';
            $headers_text[] = 'Reply-To: ' . $this->config['reply_to'];
            $headers_text[] = 'X-Mailer: PHP/' . phpversion();
            
            $success = mail($to_email, $subject, $text_body, implode("\r\n", $headers_text));
        }
        
        return $success;
    }
    
    private function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        return $protocol . '://' . $host . $path;
    }
}

// Function to send booking confirmation email
function sendBookingConfirmationEmail($booking_data) {
    if (empty($booking_data['email']) || !filter_var($booking_data['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $emailService = new EmailService();
    return $emailService->sendBookingConfirmation($booking_data['email'], $booking_data);
}

// Function to log email attempts
function logEmailAttempt($email, $success, $booking_id = null) {
    global $conn;
    
    $status = $success ? 'sent' : 'failed';
    $timestamp = date('Y-m-d H:i:s');
    
    // Create email log table if it doesn't exist
    $conn->query("CREATE TABLE IF NOT EXISTS email_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT,
        email VARCHAR(255),
        status ENUM('sent', 'failed') DEFAULT 'failed',
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        error_message TEXT,
        INDEX idx_booking_id (booking_id),
        INDEX idx_email (email),
        INDEX idx_timestamp (timestamp)
    )");
    
    $error_message = $success ? null : 'Email sending failed';
    
    $stmt = $conn->prepare("INSERT INTO email_log (booking_id, email, status, timestamp, error_message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $booking_id, $email, $status, $timestamp, $error_message);
    $stmt->execute();
    $stmt->close();
}
?>