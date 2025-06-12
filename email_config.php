<?php
// Email Configuration File
// Copy this file and rename it to email_config.php, then update with your settings

return [
    // Basic email settings
    'from_email' => 'admin@verificaprezzo.eu',
    'from_name' => 'Old School Barber',
    'reply_to' => 'admin@verificaprezzo.eu',
    
    // Business information for emails
    'business_name' => 'Old School Barber',
    'business_tagline' => 'Tradizione, stile e passione dal 1985',
    'business_phone' => '+39 123 456 7890',
    'business_address' => 'Via Roma 123, 00100 Roma',
    'business_hours' => 'Mar-Sab 9:00-18:30',
    'website_url' => 'https://www.oldschoolbarber.com',
    
    // SMTP settings (optional - for advanced email sending)
    'smtp_enabled' => true, // Set to true to use SMTP instead of PHP mail()
    'smtp_host' => 'mail.verificaprezzo.eu',
    'smtp_port' => 465,
    'smtp_username' => 'admin@verificaprezzo.eu',
    'smtp_password' => 'Giovanni123!',
    'smtp_encryption' => 'ssl', // 'tls' or 'ssl'
    
    // Email templates settings
    'include_logo' => true,
    'logo_url' => '', // URL to your logo image (optional)
    'primary_color' => '#d4af37',
    'secondary_color' => '#ffd700',
    'text_color' => '#333333',
    'background_color' => '#f8fafc',
    
    // Email features
    'send_admin_notifications' => true, // Send notifications to admin when new booking is made
    'admin_email' => 'admin@oldschoolbarber.com',
    'include_cancellation_link' => true,
    'include_business_info' => true,
    
    // Email content customization
    'custom_footer_text' => '', // Additional text for email footer
    'important_notes' => [
        'Ti preghiamo di arrivare 5 minuti prima dell\'orario prenotato',
        'In caso di ritardo superiore a 15 minuti, la prenotazione potrebbe essere cancellata',
        'Per cancellazioni o modifiche, contattaci almeno 24 ore prima'
    ]
];
?>