<?php
/**
 * Diagnosis script: Test CSRF token flow
 * Akses via: http://localhost/aksi_kebal_kemenag/test_csrf_debug.php
 */

session_start();

echo "<h2>CSRF Token Debug</h2>";

// 1. Cek apakah ada CSRF token di session
echo "<h3>1. Session State</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "CSRF token in session: " . ($_SESSION['csrf_token'] ?? '<em>EMPTY/NOT SET</em>') . "<br>";

// 2. Generate token
require_once __DIR__ . '/core/Middleware.php';
$token = Middleware::generateCsrfToken();
echo "<br><h3>2. After generateCsrfToken()</h3>";
echo "Generated token: " . $token . "<br>";
echo "Session csrf_token: " . ($_SESSION['csrf_token'] ?? '<em>EMPTY</em>') . "<br>";
echo "Match: " . ($token === ($_SESSION['csrf_token'] ?? '') ? 'YES' : 'NO') . "<br>";

// 3. Test validation
echo "<br><h3>3. Test validateCsrfToken()</h3>";
$valid = Middleware::validateCsrfToken($token);
echo "Validation result: " . ($valid ? 'VALID' : 'INVALID') . "<br>";
echo "Session csrf_token after validation: " . ($_SESSION['csrf_token'] ?? '<em>EMPTY (unset)</em>') . "<br>";

// 4. Test form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<br><h3>4. POST Data Received</h3>";
    echo "csrf_token from POST: " . ($_POST['csrf_token'] ?? '<em>NOT FOUND</em>') . "<br>";
    echo "Session csrf_token: " . ($_SESSION['csrf_token'] ?? '<em>EMPTY</em>') . "<br>";
    
    // Regenerate for display
    $newToken = Middleware::generateCsrfToken();
    echo "New token generated: " . $newToken . "<br>";
    
    $isValid = Middleware::validateCsrfToken($_POST['csrf_token'] ?? '');
    echo "Validation: " . ($isValid ? '<span style="color:green">VALID</span>' : '<span style="color:red">INVALID</span>') . "<br>";
}

// 5. Show test form
$freshToken = Middleware::generateCsrfToken();
echo "<br><h3>5. Test Form</h3>";
echo '<form method="POST">';
echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($freshToken) . '">';
echo '<button type="submit">Test CSRF Submit</button>';
echo '</form>';

echo "<br><h3>6. Session dump</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
