<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 1);

// Load environment variables from .env file if it exists (local development)
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

if (session_status() === PHP_SESSION_NONE) {
    @session_start();

    session_regenerate_id(true);
}
// Use $pdo consistently
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_port = $_ENV['DB_PORT'] ?? '3306';
$db_name = $_ENV['DB_DATABASE'] ?? 'db';
$db_user = $_ENV['DB_USERNAME'] ?? 'root';
$db_pass = $_ENV['DB_PASSWORD'] ?? 'secret';

try {
    // Dynamically build the DSN using the variables above
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=db;charset=utf8";

    // Assign to $pdo, not $_db
    $_db = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Now, in index.php line 11:
// $stmt = $pdo->prepare("..."); // This will now work!


function countAllCustomer()
{
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(*) - 1 AS total_customers FROM user");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_customers'];
}

function countAllOrder()
{
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(*) AS total_order FROM orders");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_order'];
}

function inputNumber($type, $name, $min, $max, $value)
{
    return "<input type='$type' name='$name' min='$min' max='$max' value='$value'/>";
}

function countAllUnits()
{
    global $_db;
    $stmt = $_db->prepare("SELECT SUM(unit) AS total_units FROM order_item");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_units'] ?? 0; // Return 0 if no units found
}


function countAllSubtotal()
{
    global $_db;
    $stmt = $_db->prepare("SELECT SUM(subtotal) AS total_subtotal FROM order_item");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_subtotal'] ?? 0; // Return 0 if no units found
}



function checkbox($name, $checked = false)
{
    $isChecked = $checked ? 'checked' : '';
    return "<input type='checkbox' name='$name' $isChecked>";
}

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function is_get()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function inputField($type, $name, $placeholder, $value = '', $class = '')
{

    if ($type === 'file') {
        return "<input type='$type' name='$name' id='$name' accept='image/jpg, image/jpeg, image/png' class='$class' />";
    }

    $value = isset($_POST['name']) ? e($_POST[$name]) : $value;
    return "<input type='$type' name='$name' id='$name' required value='" . e($value) . "' placeholder='$placeholder' class='$class'/>";
}

function html_textarea($name, $id, $row, $col, $placeholder, $value)
{
    return "<textarea name='$name' id='$id' rows='$row' cols='$col' placeholder='$placeholder' required>$value</textarea>";
}

function html_select($name, $id, $options, $selected = null)
{
    $html = "<select name='$name' id='$id' required>\n";
    $html .= "<option value=''>-- Select --</option>\n";

    foreach ($options as $value => $label) {
        $isSelected = ($value == $selected) ? 'selected' : '';
        $html .= "<option value='$value' $isSelected>$label</option>\n";
    }

    $html .= "</select>";
    return $html;
}

function html_selects($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

function displayError($errors)
{
    if (!empty($errors)) {
        echo "<div class='error-messages'>";
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        echo "</div>";
    }
}

function html_submit($type, $name, $class = 'form-btn', $value = '')
{
    return "<input type='$type' name='$name' id='$name' class='$class' required value='" . e($value) . "'/>";
}

function html_delete($type, $name, $value = '')
{
    return "<input type='$type' name='$name' id='$name' class='delete-btn' required value='" . e($value) . "'/>";
}

function html_select_range($name, $id, $min, $max, $label)
{
    $html = "<select name='$name' id='$id' required>\n";
    $html .= "<option value=''>-- Select --</option>\n";

    for ($i = $min; $i <= $max; $i++) {
        $html .= "<option value='$i'>$i $label</option>\n";
    }

    $html .= "</select>";
    return $html;
}


function html_password($type, $name, $placeholder, $value = '', $class = '')
{
    return "<input type='$type' name='$name' id='$name' value='" . e($value) . "' placeholder='$placeholder' class='$class'/>";
}

function html_search($type, $name, $placeholder, $value = '', $class = '')
{
    $value = isset($_POST['name']) ? e($_POST[$name]) : $value;
    return "<input type='$type' name='$name' id='$name'  value='" . e($value) . "' placeholder='$placeholder' class='$class'/>";
}

function get_cart()
{
    return $_SESSION['cart'] ?? [];
}

function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

function encode($value)
{
    return htmlentities($value);
}

function html_hidden($key, $attr = '')
{
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}

function update_cart($id, $unit)
{
    $cart = get_cart();

    // Validation
    if (!is_exists($id, 'product', 'product_id')) {
        return "Product does not exist.";
    }

    if ($unit < 1 || $unit > 10) {
        return "Unit must be between 1 and 10.";
    }

    // Update cart
    $cart[$id] = $unit;
    ksort($cart);
    set_cart($cart);
    return null; // no error
}

function cart_quantity()
{
    $cart = get_cart();
    $total = 0;
    foreach ($cart as $quantity) {
        $total += (int)$quantity;
    }
    return $total;
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Login user
function login($user, $url = '/')
{
    $_SESSION['user'] = $user;
    redirect($url);
}

// Logout user
function logout($url = '/')
{
    unset($_SESSION['user']);
    redirect($url);
}

// Authorization
// This is my part please add into your base.php 
function auth(...$roles)
{
    global $_user;

    // (1) Must be logged in
    if (empty($_user)) {
        redirect('/login.php');
        exit;
    }

    // (2) Get user status (could be '' or 'admin')
    $userStatus = $_user->status ?? '';

    // (3) If no roles specified, allow any logged-in user
    if (empty($roles)) {
        return;
    }

    // (4) Check if user status matches allowed roles
    if (in_array($userStatus, $roles, true)) {
        return;
    }

    // (5) If no match, deny access
    temp('info', 'You do not have permission to access this page. Allowed roles: ' . implode(', ', $roles));
    redirect('/index.php');
    exit;
}



function set_cart($cart = [])
{
    $_SESSION['cart'] = $cart;
}



function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    if ($value === null) {
        return $value; // Return null if the value is null
    }
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

function get_mail()
{

    require_once './vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once './vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once './vendor/phpmailer/phpmailer/src/Exception.php';

    // Mail configuration from environment or defaults


    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = "seongchunlaw050@gmail.com";
    $m->Password = 'voro vlvz zpat osmd';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, 'Admin');

    return $m;
}

// ============================================================================
// CART FUNCTIONS (Additional)
// ============================================================================

function remove_from_cart($id)
{
    $cart = get_cart();
    unset($cart[$id]);
    set_cart($cart);
}

// ============================================================================
// WISHLIST FUNCTIONS (Database-backed)
// ============================================================================

/**
 * Add product to user's wishlist
 */
function add_to_wishlist($user_id, $product_id)
{
    global $_db;
    
    // Validation
    if (!is_exists($product_id, 'product', 'product_id')) {
        return "Product does not exist.";
    }
    
    if (!is_exists($user_id, 'user', 'user_id')) {
        return "User does not exist.";
    }
    
    // Check if already in wishlist
    $stm = $_db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stm->execute([$user_id, $product_id]);
    if ($stm->fetchColumn() > 0) {
        return "Product already in wishlist.";
    }
    
    // Add to wishlist
    $stm = $_db->prepare("INSERT INTO wishlist (user_id, product_id, added_at) VALUES (?, ?, NOW())");
    if ($stm->execute([$user_id, $product_id])) {
        return null; // no error
    } else {
        return "Failed to add to wishlist.";
    }
}

/**
 * Remove product from user's wishlist
 */
function remove_from_wishlist($user_id, $product_id)
{
    global $_db;
    
    $stm = $_db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    if ($stm->execute([$user_id, $product_id])) {
        return null; // no error
    } else {
        return "Failed to remove from wishlist.";
    }
}

/**
 * Get all wishlist items for a user
 */
function get_wishlist($user_id)
{
    global $_db;
    
    $stm = $_db->prepare("
        SELECT w.wishlist_id, w.user_id, w.product_id, w.added_at, 
               p.product_id, p.Product_name, p.price, p.image, p.quantity, p.detail, p.category_id
        FROM wishlist w
        JOIN product p ON w.product_id = p.product_id
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC
    ");
    $stm->execute([$user_id]);
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

/**
 * Get total count of items in user's wishlist
 */
function wishlist_quantity($user_id)
{
    global $_db;
    
    $stm = $_db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stm->execute([$user_id]);
    return (int)$stm->fetchColumn();
}

/**
 * Check if a product is in user's wishlist
 */
function is_in_wishlist($user_id, $product_id)
{
    global $_db;
    
    $stm = $_db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stm->execute([$user_id, $product_id]);
    return $stm->fetchColumn() > 0;
}
