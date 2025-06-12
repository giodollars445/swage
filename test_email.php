<?php
// Test Email Functionality
// This file helps you test if email sending is working correctly

include 'connessione.php';
include 'send_email.php';

// Check if this is a POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_email = $_POST['test_email'] ?? '';
    
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Inserisci un indirizzo email valido.";
    } else {
        // Create test booking data
        $test_booking_data = [
            'id' => 999,
            'nome' => 'Mario Rossi',
            'email' => $test_email,
            'telefono' => '+39 123 456 7890',
            'servizio' => 'Taglio Classico',
            'data_prenotazione' => date('Y-m-d'),
            'orario' => '14:30:00',
            'operatore_nome' => 'Giuseppe Barbiere'
        ];
        
        // Try to send test email
        $emailService = new EmailService();
        $success = $emailService->sendBookingConfirmation($test_email, $test_booking_data);
        
        if ($success) {
            $message = "Email di test inviata con successo a: " . htmlspecialchars($test_email);
        } else {
            $error = "Errore nell'invio dell'email. Controlla la configurazione del server.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - Old School Barber</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #ffffff;
            padding: 1rem;
        }

        .container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #d4af37;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #a0a0a0;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255, 255, 255, 0.12);
        }

        .form-group input::placeholder {
            color: #a0a0a0;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            border: none;
            border-radius: 12px;
            color: #1a1a2e;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(212, 175, 55, 0.4);
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .message.success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .info-box {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 2rem;
            color: #60a5fa;
            font-size: 0.9rem;
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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

        @media (max-width: 480px) {
            .container {
                padding: 2rem 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Test Email</h1>
            <p>Testa la funzionalità di invio email</p>
        </div>

        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="test_email">Indirizzo Email di Test</label>
                <input type="email" id="test_email" name="test_email" placeholder="test@esempio.com" required>
            </div>

            <button type="submit" class="submit-btn">
                📧 Invia Email di Test
            </button>
        </form>

        <div class="info-box">
            <strong>ℹ️ Informazioni:</strong><br>
            Questo strumento invia un'email di test con dati fittizi per verificare che il sistema di invio email funzioni correttamente. 
            L'email conterrà tutti gli elementi di una normale conferma di prenotazione.
        </div>

        <div class="back-link">
            <a href="admin.php">← Torna al Dashboard</a>
        </div>
    </div>
</body>
</html>