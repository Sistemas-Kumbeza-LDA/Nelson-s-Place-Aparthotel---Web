<?php
/**
 * Secure Booking Form Handler
 * Nelson's Place - Schedule Visit Request
 */

// ============================================
// CONFIGURAÇÃO - ALTERE ESTAS INFORMAÇÕES
// ============================================
$to = 'info@nelsonsplace.co.mz';  // ALTERE PARA O SEU EMAIL
$subject = 'Nova Solicitação de Visita - Nelson\'s Place';

// ============================================
// FUNÇÕES DE SEGURANÇA
// ============================================

/**
 * Sanitiza string removendo caracteres perigosos
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Remove caracteres de nova linha para prevenir header injection
 */
function sanitize_header($string) {
    return str_replace(array("\r", "\n", "%0a", "%0d"), '', $string);
}

/**
 * Valida formato de email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida formato de data (DD/MM/YYYY ou YYYY-MM-DD)
 */
function is_valid_date($date) {
    $patterns = [
        '/^\d{2}\/\d{2}\/\d{4}$/',  // DD/MM/YYYY
        '/^\d{4}-\d{2}-\d{2}$/'      // YYYY-MM-DD
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $date)) {
            return true;
        }
    }
    return false;
}

// ============================================
// RATE LIMITING (Proteção contra spam)
// ============================================

session_start();
$rate_limit_seconds = 60;  // Tempo mínimo entre envios (1 minuto)
$max_submissions = 5;      // Máximo de envios por sessão

// Verificar último envio
if (isset($_SESSION['last_booking_time'])) {
    $time_diff = time() - $_SESSION['last_booking_time'];
    if ($time_diff < $rate_limit_seconds) {
        echo 'failed';
        exit;
    }
}

// Verificar número de envios na sessão
if (!isset($_SESSION['booking_count'])) {
    $_SESSION['booking_count'] = 0;
}
if ($_SESSION['booking_count'] >= $max_submissions) {
    echo 'failed';
    exit;
}

// ============================================
// VERIFICAÇÕES DE SEGURANÇA
// ============================================

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'failed';
    exit;
}

// Verificar se todos os campos obrigatórios existem
$required_fields = ['name', 'email', 'message', 'date', 'time'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo 'failed';
        exit;
    }
}

// ============================================
// PROCESSAR E SANITIZAR DADOS
// ============================================

$name = sanitize_input($_POST['name']);
$email = sanitize_header($_POST['email']);
$msg = sanitize_input($_POST['message']);
$date = sanitize_input($_POST['date']);
$time = sanitize_input($_POST['time']);

// Validar email
if (!is_valid_email($email)) {
    echo 'failed';
    exit;
}

// Validar tamanho dos campos (prevenir spam/overflow)
if (strlen($name) > 100 || strlen($email) > 100 || strlen($msg) > 2000) {
    echo 'failed';
    exit;
}

// ============================================
// PREPARAR E ENVIAR EMAIL
// ============================================

// Headers seguros - usar email do servidor como remetente
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
$headers .= "From: Nelson's Place <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Corpo do email
$message = "===========================================\n";
$message .= "NOVA SOLICITAÇÃO DE VISITA\n";
$message .= "===========================================\n\n";
$message .= "Nome: " . $name . "\n";
$message .= "Email: " . $email . "\n";
$message .= "Data: " . $date . "\n";
$message .= "Hora: " . $time . "\n";
$message .= "Mensagem:\n" . $msg . "\n\n";
$message .= "===========================================\n";
$message .= "Enviado em: " . date('d/m/Y H:i:s') . "\n";
$message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Enviar email
if (mail($to, $subject, $message, $headers)) {
    // Atualizar rate limiting após envio bem-sucedido
    $_SESSION['last_booking_time'] = time();
    $_SESSION['booking_count']++;
    echo 'sent';
} else {
    error_log("Falha ao enviar email de booking - " . date('Y-m-d H:i:s'));
    echo 'failed';
}
?>