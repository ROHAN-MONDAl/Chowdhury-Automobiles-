<?php
require "db.php";

define('MAX_FILE_SIZE', 10 * 1024 * 1024);
$uploadDir = '../images/';

/* FILE UPLOADER */
function uploadFile($name, $dir)
{
    if (!isset($_FILES[$name]) || $_FILES[$name]['error'] !== 0) return null;
    if ($_FILES[$name]['size'] > MAX_FILE_SIZE) return null;

    $ext = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
    $new = uniqid($name . "_") . "." . $ext;
    $path = $dir . $new;

    if (move_uploaded_file($_FILES[$name]['tmp_name'], $path)) {
        return str_replace("../", "", $path);
    }
    return null;
}

/* POST HELPER */
function post($k)
{
    return isset($_POST[$k]) ? trim($_POST[$k]) : null;
}

/* VALIDATION */
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: inventory.php");
    exit;
}

$id = isset($_POST['row_id']) ? (int)$_POST['row_id'] : 0;
if ($id <= 0) {
    die("Invalid record ID.");
}

$current_step = post("current_step");

/* VEHICLE NORMALIZATION */
$incoming = post("vehicle_number")
    ?: post("seller_vehicle_number")
    ?: post("purchaser_vehicle_no")
    ?: post("ot_vehicle_number");

$vehicle_no = null;
if ($incoming) {
    $vehicle_no = strtoupper($incoming);
    if (!str_starts_with($vehicle_no, "WB")) {
        $vehicle_no = "WB " . $vehicle_no;
    }
}

/* MASTER DATA MAP */
$data = [

    // STEP 1
    'vehicle_type' => post('vehicle_type'),
    'name' => post('name'),
    'vehicle_number' => $vehicle_no,
    'register_date' => post('register_date'),
    'owner_serial' => post('owner_serial'),
    'chassis_number' => strtoupper(post('chassis_number')),
    'engine_number' => strtoupper(post('engine_number')),
    'payment_type' => post('payment_type'),
    'cash_price' => post('cash_price'),
    'online_method' => post('online_method'),
    'online_transaction_id' => post('online_transaction_id'),
    'online_price' => post('online_price'),
    'police_challan' => post('police_challan'),

    'challan1_number' => post('challan1_number'),
    'challan1_amount' => post('challan1_amount'),
    'challan1_status' => post('challan1_status'),

    'challan2_number' => post('challan2_number'),
    'challan2_amount' => post('challan2_amount'),
    'challan2_status' => post('challan2_status'),

    'challan3_number' => post('challan3_number'),
    'challan3_amount' => post('challan3_amount'),
    'challan3_status' => post('challan3_status'),

    'sold_out' => isset($_POST['sold_out']) ? 1 : 0,

    // STEP 2 SELLER
    'seller_date' => post('seller_date'),
    'seller_vehicle_number' => $vehicle_no,
    'seller_chassis_no' => strtoupper(post('seller_chassis_no')),
    'seller_engine_no' => strtoupper(post('seller_engine_no')),
    'seller_name' => post('seller_name'),
    'seller_address' => post('seller_address'),
    'seller_mobile1' => post('seller_mobile1'),
    'seller_mobile2' => post('seller_mobile2'),
    'seller_mobile3' => post('seller_mobile3'),

    'noc_status' => post('noc_status'),
    'seller_payment_type' => post('seller_payment_type'),
    'seller_cash_price' => post('seller_cash_price'),
    'seller_online_method' => post('seller_online_method'),
    'seller_online_transaction_id' => post('seller_online_transaction_id'),
    'seller_online_price' => post('seller_online_price'),
    'exchange_showroom_name' => post('exchange_showroom_name'),
    'staff_name' => post('staff_name'),
    'total_amount' => post('total_amount'),
    'paid_amount' => post('paid_amount'),
    'due_amount' => post('due_amount'),
    'due_reason' => post('due_reason'),

    // STEP 3 PURCHASER
    'purchaser_date' => post('purchaser_date'),
    'purchaser_name' => post('purchaser_name'),
    'purchaser_address' => post('purchaser_address'),
    'purchaser_bike_name' => post('purchaser_bike_name'),
    'purchaser_vehicle_no' => $vehicle_no,

    'purchaser_transfer_amount' => post('purchaser_transfer_amount'),
    'purchaser_transfer_date' => post('purchaser_transfer_date'),
    'purchaser_transfer_status' => post('purchaser_transfer_status'),

    'purchaser_hpa_amount' => post('purchaser_hpa_amount'),
    'purchaser_hpa_date' => post('purchaser_hpa_date'),
    'purchaser_hpa_status' => post('purchaser_hpa_status'),

    'purchaser_hp_amount' => post('purchaser_hp_amount'),
    'purchaser_hp_date' => post('purchaser_hp_date'),
    'purchaser_hp_status' => post('purchaser_hp_status'),

    'purchaser_insurance_name' => post('purchaser_insurance_name'),
    'purchaser_insurance_payment_status' => post('purchaser_insurance_payment_status'),
    'purchaser_insurance_amount' => post('purchaser_insurance_amount'),
    'purchaser_insurance_issue_date' => post('purchaser_insurance_issue_date'),
    'purchaser_insurance_expiry_date' => post('purchaser_insurance_expiry_date'),

    'purchaser_total' => post('purchaser_total'),
    'purchaser_paid' => post('purchaser_paid'),
    'purchaser_due' => post('purchaser_due'),
    'purchaser_payment_mode' => post('purchaser_payment_mode'),

    // STEP 4 OT
    'ot_name_transfer' => post('ot_name_transfer'),
    'ot_vehicle_number' => $vehicle_no,
    'ot_rto_name' => post('ot_rto_name'),
    'ot_vendor_name' => post('ot_vendor_name'),

    'ot_transfer_amount' => post('ot_transfer_amount'),
    'ot_transfer_date' => post('ot_transfer_date'),
    'ot_transfer_status' => post('ot_transfer_status'),

    'ot_hpa_amount' => post('ot_hpa_amount'),
    'ot_hpa_date' => post('ot_hpa_date'),
    'ot_hpa_status' => post('ot_hpa_status'),

    'ot_hp_amount' => post('ot_hp_amount'),
    'ot_hp_date' => post('ot_hp_date'),
    'ot_hp_status' => post('ot_hp_status'),

    'ot_insurance_name' => post('ot_insurance_name'),
    'ot_insurance_payment_status' => post('ot_insurance_payment_status'),
    'ot_insurance_amount' => post('ot_insurance_amount'),
    'ot_insurance_start_date' => post('ot_insurance_start_date'),
    'ot_insurance_end_date' => post('ot_insurance_end_date'),

    'ot_purchaser_sign_status' => post('ot_purchaser_sign_status'),
    'ot_purchaser_sign_date' => post('ot_purchaser_sign_date'),
    'ot_seller_sign_status' => post('ot_seller_sign_status'),
    'ot_seller_sign_date' => post('ot_seller_sign_date')
];

/* FILE UPLOADS */
$fileInputs = [
    "photo1",
    "photo2",
    "photo3",
    "photo4",
    "doc_aadhar_front",
    "doc_aadhar_back",
    "doc_voter_front",
    "doc_voter_back",
    "rc_front",
    "rc_back",
    "noc_front",
    "noc_back",
    "purchaser_doc_aadhar_front",
    "purchaser_doc_aadhar_back",
    "purchaser_doc_voter_front",
    "purchaser_doc_voter_back"
];

foreach ($fileInputs as $f) {
    $upload = uploadFile($f, $uploadDir);
    if ($upload) $data[$f] = $upload;
}

/* CLEAN EMPTY VALUES */
$clean = [];
foreach ($data as $col => $val) {
    if ($val !== null && $val !== "") {
        $clean[$col] = $val;
    }
}

/* UPDATE QUERY */
if (count($clean) > 0) {
    $cols = [];
    $types = "";
    $params = [];

    foreach ($clean as $col => $val) {
        $cols[] = "$col = ?";
        $types .= "s";
        $params[] = $val;
    }

    $types .= "i";
    $params[] = $id;

    $sql = "UPDATE stock_vehicle_details SET " . implode(", ", $cols) . " WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
}

/* REDIRECT */
header("Location: edit_inventory.php?id=" . $id);
exit;
