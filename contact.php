<?php
/**
 * Secure Contact Form Handler
 * Nelson's Place - Contact Request
 */

// ============================================
// CONFIGURAÇÃO - ALTERE ESTAS INFORMAÇÕES
// ============================================
$to = 'info@nelsonsplace.co.mz';  // Seu email para receber as mensagens
$subject = 'Nova Mensagem de Contato - Nelson\'s Place';

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
$required_fields = ['name', 'email', 'phone', 'message'];
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
$phone = sanitize_input($_POST['phone']);
$msg = sanitize_input($_POST['message']);

// Validar email
if (!is_valid_email($email)) {
    echo 'failed';
    exit;
}

// Validar tamanho dos campos (prevenir spam/overflow)
if (strlen($name) > 100 || strlen($email) > 100 || strlen($phone) > 30 || strlen($msg) > 2000) {
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
$message .= "NOVA MENSAGEM DE CONTATO\n";
$message .= "===========================================\n\n";
$message .= "Nome: " . $name . "\n";
$message .= "Email: " . $email . "\n";
$message .= "Telefone: " . $phone . "\n";
$message .= "Mensagem:\n" . $msg . "\n\n";
$message .= "===========================================\n";
$message .= "Enviado em: " . date('d/m/Y H:i:s') . "\n";
$message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Enviar email
if (mail($to, $subject, $message, $headers)) {
    echo 'sent';
} else {
    error_log("Falha ao enviar email de contato - " . date('Y-m-d H:i:s'));
    echo 'failed';
}
?>
