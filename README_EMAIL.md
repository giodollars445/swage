# Sistema Email per Old School Barber

## Panoramica
Il sistema email invia automaticamente email di conferma ai clienti quando effettuano una prenotazione. Include anche notifiche per l'amministratore.

## File Principali

### 1. `send_email.php`
Contiene la classe `EmailService` che gestisce l'invio delle email:
- Email di conferma per i clienti
- Notifiche per l'amministratore
- Logging degli invii
- Supporto per HTML e testo semplice

### 2. `email_config.php`
File di configurazione per personalizzare:
- Informazioni aziendali
- Impostazioni SMTP (opzionale)
- Colori e stile delle email
- Note importanti da includere

### 3. `test_email.php`
Strumento per testare il funzionamento del sistema email.

## Configurazione

### Configurazione Base
1. Copia `email_config.php` e personalizza i valori:
   ```php
   'business_name' => 'Il Tuo Barbiere',
   'business_phone' => '+39 123 456 7890',
   'business_address' => 'Via Roma 123, 00100 Roma',
   // ... altri parametri
   ```

### Configurazione Server Email
Il sistema utilizza la funzione `mail()` di PHP per default. Per server di produzione, configura SMTP:

1. Nel file `email_config.php`, imposta:
   ```php
   'smtp_enabled' => true,
   'smtp_host' => 'smtp.tuodominio.com',
   'smtp_username' => 'noreply@tuodominio.com',
   'smtp_password' => 'la-tua-password',
   ```

### Configurazione Server Web
Assicurati che il server web sia configurato per inviare email:

#### Per Apache/cPanel:
- La funzione `mail()` dovrebbe funzionare automaticamente
- Verifica che il dominio abbia record SPF configurati

#### Per server VPS/dedicati:
- Installa e configura un server SMTP (es. Postfix)
- Configura i record DNS (SPF, DKIM, DMARC)

## Funzionalità

### Email di Conferma Cliente
Include:
- ✅ Dettagli completi della prenotazione
- ✅ Informazioni di contatto del barbiere
- ✅ Note importanti (orari, cancellazioni, etc.)
- ✅ Link per cancellare la prenotazione
- ✅ Design responsive per mobile

### Notifiche Amministratore
- 📧 Email automatica quando arriva una nuova prenotazione
- 📋 Tutti i dettagli del cliente e servizio richiesto

### Logging
- 📊 Tracciamento di tutti i tentativi di invio
- 🔍 Identificazione di email fallite
- 📈 Statistiche accessibili dal database

## Test del Sistema

### Test Manuale
1. Vai su `test_email.php`
2. Inserisci il tuo indirizzo email
3. Clicca "Invia Email di Test"
4. Controlla la tua casella di posta

### Test Automatico
Il sistema testa automaticamente l'invio ad ogni prenotazione e registra il risultato.

## Risoluzione Problemi

### Email non arrivano
1. **Controlla spam/junk**: Le email potrebbero finire nello spam
2. **Verifica configurazione server**: Usa `test_email.php` per diagnosticare
3. **Controlla log email**: Verifica la tabella `email_log` nel database
4. **DNS Records**: Assicurati che SPF/DKIM siano configurati

### Email arrivano ma sembrano spam
1. **Configura SPF record**: `v=spf1 include:tuoserver.com ~all`
2. **Aggiungi DKIM**: Configura firma digitale
3. **Imposta DMARC**: Policy per autenticazione email
4. **Usa dominio dedicato**: Evita indirizzi generici come @gmail.com

### Errori di invio
1. **Controlla log PHP**: Verifica errori nel log del server
2. **Testa connessione SMTP**: Se usi SMTP, verifica credenziali
3. **Limiti server**: Alcuni hosting limitano invii email

## Personalizzazione

### Modificare Template Email
Modifica i metodi in `send_email.php`:
- `generateBookingEmailHTML()` - Template HTML
- `generateBookingEmailText()` - Template testo

### Aggiungere Nuovi Tipi Email
1. Crea nuovo metodo nella classe `EmailService`
2. Aggiungi template HTML e testo
3. Implementa logica di invio

### Modificare Stile
Personalizza CSS nel template HTML o usa il file di configurazione per colori.

## Sicurezza

### Best Practices
- ✅ Validazione indirizzi email
- ✅ Escape di tutti i dati utente
- ✅ Rate limiting (implementato a livello server)
- ✅ Log di sicurezza

### Protezione Spam
- ✅ Validazione lato server
- ✅ Controllo formato email
- ✅ Logging tentativi

## Manutenzione

### Pulizia Log
Periodicamente pulisci la tabella `email_log`:
```sql
DELETE FROM email_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Monitoraggio
Controlla regolarmente:
- Tasso di successo invii email
- Email finite nello spam
- Performance del server email

## Supporto

Per problemi o domande:
1. Controlla i log di sistema
2. Usa `test_email.php` per diagnosticare
3. Verifica configurazione DNS del dominio
4. Contatta il provider hosting per supporto SMTP