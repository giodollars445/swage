<?php
// Debug Email System
// This file helps diagnose email sending issues

include 'connessione.php';

// Check if email configuration exists
$config_exists = file_exists('email_config.php');
$config = null;

if ($config_exists) {
    $config = include 'email_config.php';
} else {
    echo "<h3>❌ File email_config.php non trovato</h3>";
    echo "<p>Il file di configurazione email non esiste. Creane uno basato su email_config.php di esempio.</p>";
}

// Check PHP mail function
$mail_function_available = function_exists('mail');

// Check server configuration
$sendmail_path = ini_get('sendmail_path');
$smtp_server = ini_get('SMTP');
$smtp_port = ini_get('smtp_port');

// Check email log table
$email_log_exists = false;
$recent_emails = [];

try {
    $result = $conn->query("SHOW TABLES LIKE 'email_log'");
    if ($result && $result->num_rows > 0) {
        $email_log_exists = true;
        
        // Get recent email attempts
        $recent_result = $conn->query("SELECT * FROM email_log ORDER BY timestamp DESC LIMIT 10");
        if ($recent_result) {
            while ($row = $recent_result->fetch_assoc()) {
                $recent_emails[] = $row;
            }
        }
    }
} catch (Exception $e) {
    // Table doesn't exist or other error
}

// Test basic email sending
$test_result = null;
if (isset($_POST['test_basic_email'])) {
    $test_email = $_POST['test_email'] ?? '';
    
    if (filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $subject = "Test Email - " . date('Y-m-d H:i:s');
        $message = "Questo è un test email di base inviato da " . $_SERVER['HTTP_HOST'] . " alle " . date('Y-m-d H:i:s');
        $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $test_result = mail($test_email, $subject, $message, $headers);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Email System - Old School Barber</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: #ffffff;
            padding: 2rem;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #d4af37;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #a0a0a0;
            font-size: 1.1rem;
        }

        .section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section h2 {
            color: #d4af37;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin: 0.5rem 0;
        }

        .status.success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status.error {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status.warning {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-item strong {
            color: #e0e0e0;
            display: block;
            margin-bottom: 0.5rem;
        }

        .info-item span {
            color: #a0a0a0;
            font-family: monospace;
        }

        .test-form {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            max-width: 300px;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.9rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4ade80;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #4ade80, #22c55e);
            border: none;
            border-radius: 8px;
            color: #1a1a2e;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .log-table th,
        .log-table td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
        }

        .log-table th {
            background: rgba(255, 255, 255, 0.05);
            color: #d4af37;
            font-weight: 600;
        }

        .log-table td {
            color: #e0e0e0;
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link a {
            color: #a0a0a0;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #d4af37;
        }

        .code-block {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.9rem;
            color: #e0e0e0;
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .section {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Debug Email System</h1>
            <p>Diagnostica problemi di invio email</p>
        </div>

        <!-- PHP Mail Function Check -->
        <div class="section">
            <h2>📧 Funzione Mail PHP</h2>
            <?php if ($mail_function_available): ?>
                <div class="status success">✅ Funzione mail() disponibile</div>
            <?php else: ?>
                <div class="status error">❌ Funzione mail() non disponibile</div>
                <p>La funzione mail() di PHP non è disponibile su questo server. Contatta il provider hosting.</p>
            <?php endif; ?>
        </div>

        <!-- Server Configuration -->
        <div class="section">
            <h2>⚙️ Configurazione Server</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Sendmail Path:</strong>
                    <span><?php echo $sendmail_path ?: 'Non configurato'; ?></span>
                </div>
                <div class="info-item">
                    <strong>SMTP Server:</strong>
                    <span><?php echo $smtp_server ?: 'Non configurato'; ?></span>
                </div>
                <div class="info-item">
                    <strong>SMTP Port:</strong>
                    <span><?php echo $smtp_port ?: 'Non configurato'; ?></span>
                </div>
                <div class="info-item">
                    <strong>Server:</strong>
                    <span><?php echo $_SERVER['HTTP_HOST']; ?></span>
                </div>
            </div>
        </div>

        <!-- Email Configuration -->
        <div class="section">
            <h2>📋 Configurazione Email</h2>
            <?php if ($config_exists): ?>
                <div class="status success">✅ File email_config.php trovato</div>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>From Email:</strong>
                        <span><?php echo htmlspecialchars($config['from_email'] ?? 'Non configurato'); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>From Name:</strong>
                        <span><?php echo htmlspecialchars($config['from_name'] ?? 'Non configurato'); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>SMTP Enabled:</strong>
                        <span><?php echo ($config['smtp_enabled'] ?? false) ? 'Sì' : 'No'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Admin Notifications:</strong>
                        <span><?php echo ($config['send_admin_notifications'] ?? false) ? 'Attive' : 'Disattive'; ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="status error">❌ File email_config.php non trovato</div>
                <p>Crea il file email_config.php per configurare le impostazioni email.</p>
                <div class="code-block">
&lt;?php
return [
    'from_email' => 'noreply@<?php echo $_SERVER['HTTP_HOST']; ?>',
    'from_name' => 'Old School Barber',
    'reply_to' => 'info@<?php echo $_SERVER['HTTP_HOST']; ?>',
    'business_name' => 'Old School Barber',
    'business_phone' => '+39 123 456 7890',
    'business_address' => 'Via Roma 123, 00100 Roma',
    'send_admin_notifications' => true,
    'admin_email' => 'admin@<?php echo $_SERVER['HTTP_HOST']; ?>'
];
?&gt;
                </div>
            <?php endif; ?>
        </div>

        <!-- Email Log -->
        <div class="section">
            <h2>📊 Log Email</h2>
            <?php if ($email_log_exists): ?>
                <div class="status success">✅ Tabella email_log trovata</div>
                <?php if (!empty($recent_emails)): ?>
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>ID Prenotazione</th>
                                <th>Email</th>
                                <th>Stato</th>
                                <th>Timestamp</th>
                                <th>Errore</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_emails as $log): ?>
                            <tr>
                                <td><?php echo $log['booking_id'] ?? 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($log['email']); ?></td>
                                <td>
                                    <span class="status <?php echo $log['status'] === 'sent' ? 'success' : 'error'; ?>">
                                        <?php echo $log['status'] === 'sent' ? 'Inviata' : 'Fallita'; ?>
                                    </span>
                                </td>
                                <td><?php echo $log['timestamp']; ?></td>
                                <td><?php echo htmlspecialchars($log['error_message'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="status warning">⚠️ Nessun tentativo di invio email registrato</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="status error">❌ Tabella email_log non trovata</div>
                <p>La tabella per il logging delle email non esiste. Verrà creata automaticamente al primo invio.</p>
            <?php endif; ?>
        </div>

        <!-- Basic Email Test -->
        <div class="section">
            <h2>🧪 Test Email Base</h2>
            <p>Testa l'invio di una email base senza il sistema di prenotazione:</p>
            
            <?php if (isset($test_result)): ?>
                <?php if ($test_result): ?>
                    <div class="status success">✅ Email di test inviata con successo!</div>
                <?php else: ?>
                    <div class="status error">❌ Invio email di test fallito</div>
                    <p>Possibili cause:</p>
                    <ul style="margin: 1rem 0; padding-left: 2rem; color: #a0a0a0;">
                        <li>Server mail non configurato</li>
                        <li>Funzione mail() disabilitata</li>
                        <li>Firewall che blocca l'invio</li>
                        <li>Dominio non configurato per l'invio email</li>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

            <div class="test-form">
                <form method="POST">
                    <div class="form-group">
                        <label for="test_email">Email di test:</label>
                        <input type="email" id="test_email" name="test_email" placeholder="test@esempio.com" required>
                    </div>
                    <button type="submit" name="test_basic_email" class="btn">
                        📧 Invia Test Email Base
                    </button>
                </form>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="section">
            <h2>💡 Raccomandazioni</h2>
            <div style="color: #a0a0a0; line-height: 1.6;">
                <h3 style="color: #e0e0e0; margin-bottom: 0.5rem;">Per risolvere problemi di email:</h3>
                <ol style="padding-left: 2rem;">
                    <li><strong>Verifica configurazione server:</strong> Assicurati che il server supporti l'invio email</li>
                    <li><strong>Configura SPF record:</strong> Aggiungi record SPF nel DNS del dominio</li>
                    <li><strong>Usa SMTP:</strong> Configura SMTP nel file email_config.php per maggiore affidabilità</li>
                    <li><strong>Controlla spam:</strong> Le email potrebbero finire nella cartella spam</li>
                    <li><strong>Test con provider diversi:</strong> Prova con Gmail, Yahoo, Outlook</li>
                </ol>

                <h3 style="color: #e0e0e0; margin: 1.5rem 0 0.5rem 0;">Configurazione SMTP consigliata:</h3>
                <div class="code-block">
'smtp_enabled' => true,
'smtp_host' => 'smtp.tuodominio.com',
'smtp_port' => 587,
'smtp_username' => 'noreply@tuodominio.com',
'smtp_password' => 'la-tua-password',
'smtp_encryption' => 'tls'
                </div>
            </div>
        </div>

        <div class="back-link">
            <a href="admin.php">← Torna al Dashboard</a>
        </div>
    </div>
</body>
</html>