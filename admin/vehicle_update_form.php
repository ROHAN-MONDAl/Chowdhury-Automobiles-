<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "db.php";

define('MAX_FILE_SIZE', 10 * 1024 * 1024);
$uploadDir = '../images/';

/* -------------------------------------------
   FILE UPLOAD FUNCTION
---------------------------------------------*/
function uploadFile($fileInput, $uploadDir)
{
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === 0) {

        if ($_FILES[$fileInput]['size'] > MAX_FILE_SIZE) {
            return ''; 
        }

        $ext = pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION);
        $newName = uniqid($fileInput . '_') . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $destination)) {
            return str_replace('../', '', $destination);
        }
    }
    return '';
}

/* -------------------------------------------------
   GETTER
---------------------------------------------------*/
function getPost($key)
{
    return $_POST[$key] ?? '';
}

/* ===========================================================
   MAIN PROCESSING
===========================================================*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$row_id = getPost('row_id');
$current_step = isset($_POST['current_step']) ? (int) $_POST['current_step'] : 1;

/* -------------------------------------------------
   VEHICLE NUMBER NORMALIZATION
---------------------------------------------------*/
$incoming_v_num = '';

$possible_inputs = [
    'vehicle_number',
    'seller_vehicle_number',
    'purchaser_vehicle_no',
    'ot_vehicle_number'
];

foreach ($possible_inputs as $field) {
    if (!empty($_POST[$field])) {
        $incoming_v_num = $_POST[$field];
        break;
    }
}

$final_vehicle_number = null;

if (!empty(trim($incoming_v_num))) {

    $v = strtoupper(trim($incoming_v_num));

    // If WB missing → add WB
    if (strpos($v, 'WB') !== 0) {
        $v = 'WB ' . $v;
    }

    // Remove extra spaces
    $v = preg_replace('/\s+/', ' ', $v);

    $final_vehicle_number = $v;
}

/* -------------------------------------------------
   DATA MAP
---------------------------------------------------*/
$data_map = [

    // VEHICLE
    'vehicle_type' => getPost('vehicle_type'),
    'name' => getPost('name'),
    'vehicle_number' => $final_vehicle_number,
    'register_date' => getPost('register_date'),
    'owner_serial' => getPost('owner_serial'),
    'chassis_number' => getPost('chassis_number'),
    'engine_number' => getPost('engine_number'),
    'payment_type' => getPost('payment_type'),
    'cash_price' => getPost('cash_price'),
    'online_method' => getPost('online_method'),
    'online_transaction_id' => getPost('upi_ref'),
    'online_price' => getPost('online_price'),
    'police_challan' => getPost('police_challan'),

    'challan1_number' => getPost('challan1_number'),
    'challan1_amount' => getPost('challan1_amount'),
    'challan1_status' => getPost('challan1_status'),
    'challan2_number' => getPost('challan2_number'),
    'challan2_amount' => getPost('challan2_amount'),
    'challan2_status' => getPost('challan2_status'),
    'challan3_number' => getPost('challan3_number'),
    'challan3_amount' => getPost('challan3_amount'),
    'challan3_status' => getPost('challan3_status'),
    'sold_out' => isset($_POST['sold_out']) ? 1 : 0,

    // SELLER
    'seller_date' => getPost('seller_date'),
    'seller_vehicle_number' => $final_vehicle_number,
    'seller_bike_name' => getPost('bike_name'),
    'seller_chassis_no' => getPost('chassis_number'),
    'seller_engine_no' => getPost('engine_number'),
    'seller_name' => getPost('seller_name'),
    'seller_address' => getPost('seller_address'),
    'seller_mobile1' => getPost('mobile1'),
    'seller_mobile2' => getPost('mobile2'),
    'seller_mobile3' => getPost('mobile3'),
    'pr_rc' => isset($_POST['papers_rc']) ? 1 : 0,
    'pr_tax' => isset($_POST['papers_tax']) ? 1 : 0,
    'pr_insurance' => isset($_POST['papers_insurance']) ? 1 : 0,
    'pr_pucc' => isset($_POST['papers_pucc']) ? 1 : 0,
    'pr_noc' => isset($_POST['papers_noc']) ? 1 : 0,
    'noc_status' => getPost('noc_payment'),
    'seller_payment_type' => getPost('pay_type'),
    'seller_cash_price' => getPost('cash_price'),
    'seller_online_method' => getPost('online_method'),
    'seller_online_transaction_id' => getPost('upi_ref'),
    'seller_online_price' => getPost('online_price'),
    'exchange_showroom_name' => getPost('showroom_name'),
    'staff_name' => getPost('staff_name'),
    'total_amount' => getPost('total_amount'),
    'paid_amount' => getPost('paid_amount'),
    'due_amount' => getPost('due_amount'),
    'due_reason' => getPost('due_reason'),

    // PURCHASER
    'purchaser_date' => getPost('purchaser_date'),
    'purchaser_name' => getPost('purchaser_name'),
    'purchaser_address' => getPost('purchaser_address'),
    'purchaser_bike_name' => getPost('purchaser_bike_name'),
    'purchaser_vehicle_no' => $final_vehicle_number,
    'purchaser_transfer_amount' => getPost('transfer_amount'),
    'purchaser_transfer_date' => getPost('transfer_date'),
    'purchaser_transfer_status' => getPost('transfer_status'),
    'purchaser_hpa_amount' => getPost('hpa_amount'),
    'purchaser_hpa_date' => getPost('hpa_date'),
    'purchaser_hpa_status' => getPost('hpa_status'),
    'purchaser_hp_amount' => getPost('hp_amount'),
    'purchaser_hp_date' => getPost('hp_date'),
    'purchaser_hp_status' => getPost('hp_status'),
    'purchaser_insurance_name' => getPost('insurance_name'),
    'purchaser_insurance_payment_status' => getPost('insurance_status'),
    'purchaser_insurance_amount' => getPost('insurance_amount'),
    'purchaser_insurance_issue_date' => getPost('insurance_issue_date'),
    'purchaser_insurance_expiry_date' => getPost('insurance_expiry_date'),
    'purchaser_total' => getPost('total_price'),
    'purchaser_paid' => getPost('paid_price'),
    'purchaser_due' => getPost('due_price'),
    'purchaser_payment_mode' => getPost('payment_mode'),
    'purchaser_cash_amount' => getPost('cash_amount'),
    'purchaser_cash_mobile1' => getPost('cash_mobile1'),
    'purchaser_cash_mobile2' => getPost('cash_mobile2'),
    'purchaser_cash_mobile3' => getPost('cash_mobile3'),
    'purchaser_fin_hpa_with' => getPost('finance_company'),
    'purchaser_fin_disburse_amount' => getPost('finance_disburse_amount'),
    'purchaser_fin_disburse_status' => getPost('finance_disburse_status'),
    'purchaser_fin_mobile1' => getPost('finance_mobile1'),
    'purchaser_fin_mobile2' => getPost('finance_mobile2'),
    'purchaser_fin_mobile3' => getPost('finance_mobile3'),
    'purchaser_payment_all_paid' => isset($_POST['all_paid']) ? 1 : 0,

    // OT
    'ot_name_transfer' => getPost('ot_name_transfer'),
    'ot_vehicle_number' => $final_vehicle_number,
    'ot_rto_name' => getPost('ot_rto_name'),
    'ot_vendor_name' => getPost('ot_vendor_name'),
    'ot_transfer_amount' => getPost('ot_transfer_amount'),
    'ot_transfer_date' => getPost('ot_transfer_date'),
    'ot_transfer_status' => getPost('ot_transfer_status'),
    'ot_hpa_amount' => getPost('ot_hpa_amount'),
    'ot_hpa_date' => getPost('ot_hpa_date'),
    'ot_hpa_status' => getPost('ot_hpa_status'),
    'ot_hp_amount' => getPost('ot_hp_amount'),
    'ot_hp_date' => getPost('ot_hp_date'),
    'ot_hp_status' => getPost('ot_hp_status'),
    'ot_insurance_name' => getPost('ot_insurance_name'),
    'ot_insurance_payment_status' => getPost('ot_insurance_payment_status'),
    'ot_insurance_amount' => getPost('ot_insurance_amount'),
    'ot_insurance_start_date' => getPost('ot_insurance_start_date'),
    'ot_insurance_end_date' => getPost('ot_insurance_end_date'),
    'ot_purchaser_sign_status' => getPost('ot_purchaser_sign_status'),
    'ot_purchaser_sign_date' => getPost('ot_purchaser_sign_date'),
    'ot_seller_sign_status' => getPost('ot_seller_sign_status'),
    'ot_seller_sign_date' => getPost('ot_seller_sign_date')
];

/* -------------------------------------------------
   FILE UPLOADS
---------------------------------------------------*/
$file_inputs = [
    'photo1', 'photo2', 'photo3', 'photo4',
    'rc_front', 'rc_back',
    'noc_front', 'noc_back',
    'doc_aadhar_front', 'doc_aadhar_back',
    'doc_voter_front', 'doc_voter_back',
    'purchaser_doc_aadhar_front', 'purchaser_doc_aadhar_back',
    'purchaser_doc_voter_front', 'purchaser_doc_voter_back'
];

foreach ($file_inputs as $input) {
    if (!empty($_FILES[$input]['name'])) {
        $uploadedPath = uploadFile($input, $uploadDir);
        if ($uploadedPath) {
            $data_map[$input] = $uploadedPath;
        }
    }
}

/* -------------------------------------------------
   REMOVE EMPTY VALUES TO PREVENT OVERWRITE
---------------------------------------------------*/
$data_to_save = array_filter($data_map, fn($v) => $v !== '' && $v !== null);

/* -------------------------------------------------
   INSERT OR UPDATE
---------------------------------------------------*/

try {

    /* -----------------------------------
       INSERT
    -------------------------------------*/
    if (empty($row_id)) {

        // Duplicate check only on INSERT
        if ($final_vehicle_number) {
            $check = $conn->prepare("SELECT id FROM stock_vehicle_details WHERE vehicle_number = ?");
            $check->bind_param("s", $final_vehicle_number);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $_SESSION['update_msg'] = "Error: Vehicle Number $final_vehicle_number already exists!";
                $_SESSION['update_type'] = 'error';
                header("Location: dashboard.php");
                exit;
            }
            $check->close();
        }

        $data_to_save['created_at'] = date('Y-m-d H:i:s');

        $columns = implode(",", array_keys($data_to_save));
        $placeholders = implode(",", array_fill(0, count($data_to_save), "?"));
        $types = str_repeat("s", count($data_to_save));
        $values = array_values($data_to_save);

        $sql = "INSERT INTO stock_vehicle_details ($columns) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        $_SESSION['update_msg'] = "Vehicle Added Successfully!";
        $_SESSION['update_type'] = 'success';
        header("Location: dashboard.php");
        exit;
    }

    /* -----------------------------------
       UPDATE (using id AND vehicle_number)
    -------------------------------------*/

    $set_clauses = [];
    foreach ($data_to_save as $col => $val) {
        $set_clauses[] = "$col = ?";
    }

    $sql_set = implode(", ", $set_clauses);
    $types = str_repeat("s", count($data_to_save)) . "i";
    $values = array_values($data_to_save);
    $values[] = $row_id;

    $sql = "UPDATE stock_vehicle_details SET $sql_set WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    $stmt->execute();

    $_SESSION['update_msg'] = "Step $current_step Saved!";
    $_SESSION['update_type'] = 'success';
    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {

    $_SESSION['update_msg'] = "Error: " . $e->getMessage();
    $_SESSION['update_type'] = 'error';
    header("Location: dashboard.php");
    exit;
}
?>
