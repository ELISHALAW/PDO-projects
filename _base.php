<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
session_regenerate_id(true);
}

try{
    $_db = new PDO('mysql:lhost=localhost;dbname=db;charset=utf8','root','',[
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}catch(PDOException $e){
    echo "Connection failed". $e->getMessage();
}


function countAllCustomer(){
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(*) - 1 AS total_customers FROM user");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_customers'];
}

function countAllOrder(){
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(*) AS total_order FROM orders");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_order'];
}

function inputNumber($type,$name,$min,$max,$value){
    return "<input type='$type' name='$name' min='$min' max='$max' value='$value'/>";
}

function countAllUnits() {
    global $_db;
    $stmt = $_db->prepare("SELECT SUM(unit) AS total_units FROM order_item");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_units'] ?? 0; // Return 0 if no units found
}


function countAllSubtotal() {
    global $_db;
    $stmt = $_db->prepare("SELECT SUM(subtotal) AS total_subtotal FROM order_item");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_subtotal'] ?? 0; // Return 0 if no units found
}



function checkbox($name,$checked = false){
    $isChecked = $checked ? 'checked' : '';
    return"<input type='checkbox' name='$name' $isChecked>";
}

function e($value){
    return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
}

function is_post(){
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function is_get(){
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function inputField($type,$name,$placeholder,$value = '',$class = ''){
    
    if ($type === 'file') {
        return "<input type='$type' name='$name' id='$name' accept='image/jpg, image/jpeg, image/png' class='$class' />";
    }

    $value = isset($_POST['name']) ? e($_POST[$name]) : $value;
    return "<input type='$type' name='$name' id='$name' required value='" . e($value) . "' placeholder='$placeholder' class='$class'/>";
}

function html_textarea($name,$id,$row,$col,$placeholder,$value){
    return "<textarea name='$name' id='$id' rows='$row' cols='$col' placeholder='$placeholder' required>$value</textarea>";
}

function html_select($name, $id, $options, $selected = null) {
    $html = "<select name='$name' id='$id' required>\n";
    $html .= "<option value=''>-- Select --</option>\n";

    foreach ($options as $value => $label) {
        $isSelected = ($value == $selected) ? 'selected' : '';
        $html .= "<option value='$value' $isSelected>$label</option>\n";
    }

    $html .= "</select>";
    return $html;
}

function html_selects($key, $items, $default = '- Select One -', $attr = '') {
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

function displayError($errors){
    if(!empty($errors)){
        echo "<div class='error-messages'>";
        foreach($errors as $error){
            echo "<p style='color:red;'>$error</p>";
        }
        echo "</div>";
    }
}

function html_submit($type,$name, $class='form-btn',$value=''){
    return "<input type='$type' name='$name' id='$name' class='$class' required value='" . e($value) . "'/>";
}

function html_delete($type,$name, $value=''){
    return "<input type='$type' name='$name' id='$name' class='delete-btn' required value='" . e($value) . "'/>";
}

function html_select_range($name,$id,$min,$max,$label){
    $html = "<select name='$name' id='$id' required>\n";
    $html .= "<option value=''>-- Select --</option>\n";

    for($i = $min;$i <= $max;$i++){
        $html .= "<option value='$i'>$i $label</option>\n";
    }

    $html .= "</select>";
    return $html;
}


function html_password($type, $name, $placeholder, $value = '', $class = '') {
    return "<input type='$type' name='$name' id='$name' value='" . e($value) . "' placeholder='$placeholder' class='$class'/>";
}

function html_search($type,$name,$placeholder,$value = '',$class = ''){
    $value = isset($_POST['name']) ? e($_POST[$name]) : $value;
    return "<input type='$type' name='$name' id='$name'  value='" . e($value) . "' placeholder='$placeholder' class='$class'/>";
}

function get_cart() {
    return $_SESSION['cart'] ?? [];
}

function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

function encode($value) {
    return htmlentities($value);
}

function html_hidden($key, $attr = '') {
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}

function update_cart($id, $unit) {
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

function cart_quantity() {
    $cart = get_cart();
    $total = 0;
    foreach ($cart as $quantity) {
        $total += (int)$quantity;
    }
    return $total;
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Login user
function login($user, $url = '/') {
    $_SESSION['user'] = $user;
    redirect($url);
}

// Logout user
function logout($url = '/') {
    unset($_SESSION['user']);
    redirect($url);
}

// Authorization
// This is my part please add into your base.php 
function auth(...$roles) {
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



function set_cart($cart = []) {
    $_SESSION['cart'] = $cart;
}



function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    if ($value === null) {
        return $value; // Return null if the value is null
    }
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

function get_mail(){

    require_once './vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once './vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once './vendor/phpmailer/phpmailer/src/Exception.php';


    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = "seongchunlaw050@gmail.com";
    $m->Password = 'gcve vrde klwr gvie';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username,'Admin');

    return $m;
}
?>