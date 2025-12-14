<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "db.php"; // Ensure this connection variable is $conn

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
            // Return path relative to DB storage needs (adjust '../' removal if needed)
            return str_replace('../', '', $destination);
        }
    }
    return null; // Return null so array_filter removes it if upload failed/empty
}

/* -------------------------------------------------
   GETTER HELPER
---------------------------------------------------*/
function getPost($key)
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : null;
}

/* ===========================================================
   MAIN PROCESSING
===========================================================*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: inventory.php");
    exit;
}

// 1. Get the ID (Hidden Input in Form)
$row_id = getPost('row_id');
 // Ensure <input type="hidden" name="row_id" value="<?= $id "> exists in your HTML
$current_step = isset($_POST['current_step']) ? (int) $_POST['current_step'] : 1;

/* -------------------------------------------------
   VEHICLE NUMBER NORMALIZATION
   (Scans all possible vehicle number fields)
---------------------------------------------------*/
$incoming_v_num = '';
$possible_inputs = ['vehicle_number', 'seller_vehicle_number', 'purchaser_vehicle_no', 'ot_vehicle_number'];

foreach ($possible_inputs as $field) {
    if (!empty($_POST[$field])) {
        $incoming_v_num = $_POST[$field];
        break; // Stop at first non-empty value
    }
}

$final_vehicle_number = null;
if (!empty(trim($incoming_v_num))) {
    $v = strtoupper(trim($incoming_v_num));
    if (strpos($v, 'WB') !== 0) $v = 'WB ' . $v; // Add WB if missing
    $v = preg_replace('/\s+/', ' ', $v); // Clean spaces
    $final_vehicle_number = $v;
}

/* -------------------------------------------------
   DATA MAP (Keys updated to match HTML names)
---------------------------------------------------*/
$data_map = [
    // --- STEP 1: VEHICLE ---
    'vehicle_type'      => getPost('vehicle_type'),
    'name'              => getPost('name'),
    'vehicle_number'    => $final_vehicle_number, // Normalized
    'register_date'     => getPost('register_date'),
    'owner_serial'      => getPost('owner_serial'),
    'chassis_number'    => getPost('chassis_number'),
    'engine_number'     => getPost('engine_number'),
    'payment_type'      => getPost('payment_type'),
    'cash_price'        => getPost('cash_price'),
    'online_method'     => getPost('online_method'),
    'online_transaction_id' => getPost('online_transaction_id'), // FIXED NAME
    'online_price'      => getPost('online_price'),
    'police_challan'    => getPost('police_challan'),
    // Challans
    'challan1_number'   => getPost('challan1_number'),
    'challan1_amount'   => getPost('challan1_amount'),
    'challan1_status'   => getPost('challan1_status'),
    'challan2_number'   => getPost('challan2_number'),
    'challan2_amount'   => getPost('challan2_amount'),
    'challan2_status'   => getPost('challan2_status'),
    'challan3_number'   => getPost('challan3_number'),
    'challan3_amount'   => getPost('challan3_amount'),
    'challan3_status'   => getPost('challan3_status'),
    'sold_out'          => isset($_POST['sold_out']) ? 1 : 0,

    // --- STEP 2: SELLER ---
    'seller_date'       => getPost('seller_date'),
    'seller_vehicle_number' => $final_vehicle_number, // Normalized
    'seller_bike_name'  => getPost('seller_bike_name'), // FIXED
    'seller_chassis_no' => getPost('seller_chassis_no'), // FIXED
    'seller_engine_no'  => getPost('seller_engine_no'), // FIXED
    'seller_name'       => getPost('seller_name'),
    'seller_address'    => getPost('seller_address'),
    'seller_mobile1'    => getPost('seller_mobile1'), // FIXED
    'seller_mobile2'    => getPost('seller_mobile2'), // FIXED
    'seller_mobile3'    => getPost('seller_mobile3'), // FIXED
    
    // Checkboxes (Step 2)
    'pr_rc'             => isset($_POST['pr_rc']) ? 1 : 0, // FIXED
    'pr_tax'            => isset($_POST['pr_tax']) ? 1 : 0, // FIXED
    'pr_insurance'      => isset($_POST['pr_insurance']) ? 1 : 0, // FIXED
    'pr_pucct'          => isset($_POST['pr_pucct']) ? 1 : 0, // FIXED (Note: DB col is likely pr_pucct based on Schema)
    'pr_noc'            => isset($_POST['pr_noc']) ? 1 : 0, // FIXED
    
    'noc_status'        => getPost('noc_status'), // FIXED
    'seller_payment_type' => getPost('seller_payment_type'), // FIXED
    'seller_cash_price' => getPost('seller_cash_price'), // FIXED
    'seller_online_method' => getPost('seller_online_method'), // FIXED
    'seller_online_transaction_id' => getPost('seller_online_transaction_id'), // FIXED
    'seller_online_price' => getPost('seller_online_price'), // FIXED
    'exchange_showroom_name' => getPost('exchange_showroom_name'), // FIXED
    'staff_name'        => getPost('staff_name'),
    'total_amount'      => getPost('total_amount'),
    'paid_amount'       => getPost('paid_amount'),
    'due_amount'        => getPost('due_amount'),
    'due_reason'        => getPost('due_reason'),

    // --- STEP 3: PURCHASER ---
    'purchaser_date'    => getPost('purchaser_date'),
    'purchaser_name'    => getPost('purchaser_name'),
    'purchaser_address' => getPost('purchaser_address'),
    'purchaser_bike_name' => getPost('purchaser_bike_name'),
    'purchaser_vehicle_no' => $final_vehicle_number, // Normalized
    
    // Fees
    'purchaser_transfer_amount' => getPost('purchaser_transfer_amount'), // FIXED
    'purchaser_transfer_date'   => getPost('purchaser_transfer_date'), // FIXED
    'purchaser_transfer_status' => getPost('purchaser_transfer_status'), // FIXED
    'purchaser_hpa_amount'      => getPost('purchaser_hpa_amount'),
    'purchaser_hpa_date'        => getPost('purchaser_hpa_date'),
    'purchaser_hpa_status'      => getPost('purchaser_hpa_status'),
    'purchaser_hp_amount'       => getPost('purchaser_hp_amount'),
    'purchaser_hp_date'         => getPost('purchaser_hp_date'),
    'purchaser_hp_status'       => getPost('purchaser_hp_status'),
    
    // Insurance
    'purchaser_insurance_name' => getPost('purchaser_insurance_name'),
    'purchaser_insurance_payment_status' => getPost('purchaser_insurance_payment_status'),
    'purchaser_insurance_amount' => getPost('purchaser_insurance_amount'),
    'purchaser_insurance_issue_date' => getPost('purchaser_insurance_issue_date'),
    'purchaser_insurance_expiry_date' => getPost('purchaser_insurance_expiry_date'),
    
    // Totals
    'purchaser_total'   => getPost('purchaser_total'), // FIXED
    'purchaser_paid'    => getPost('purchaser_paid'), // FIXED
    'purchaser_due'     => getPost('purchaser_due'), // FIXED
    
    // Payment Mode
    'purchaser_payment_mode' => getPost('purchaser_payment_mode'),
    'purchaser_cash_amount' => getPost('purchaser_cash_amount'),
    'purchaser_cash_mobile1' => getPost('purchaser_cash_mobile1'), // FIXED
    'purchaser_cash_mobile2' => getPost('purchaser_cash_mobile2'), // FIXED
    'purchaser_cash_mobile3' => getPost('purchaser_cash_mobile3'), // FIXED
    
    'purchaser_fin_hpa_with' => getPost('purchaser_fin_hpa_with'), // FIXED
    'purchaser_fin_disburse_amount' => getPost('purchaser_fin_disburse_amount'), // FIXED
    'purchaser_fin_disburse_status' => getPost('purchaser_fin_disburse_status'), // FIXED
    'purchaser_fin_mobile1' => getPost('purchaser_fin_mobile1'), // FIXED
    'purchaser_fin_mobile2' => getPost('purchaser_fin_mobile2'), // FIXED
    'purchaser_fin_mobile3' => getPost('purchaser_fin_mobile3'), // FIXED
    'purchaser_payment_all_paid' => isset($_POST['purchaser_payment_all_paid']) ? 1 : 0, // FIXED

    // --- STEP 4: OT (Ownership Transfer) ---
    'ot_name_transfer'  => getPost('ot_name_transfer'),
    'ot_vehicle_number' => $final_vehicle_number, // Normalized
    'ot_rto_name'       => getPost('ot_rto_name'),
    'ot_vendor_name'    => getPost('ot_vendor_name'),
    
    'ot_transfer_amount' => getPost('ot_transfer_amount'),
    'ot_transfer_date'   => getPost('ot_transfer_date'),
    'ot_transfer_status' => getPost('ot_transfer_status'),
    'ot_hpa_amount'      => getPost('ot_hpa_amount'),
    'ot_hpa_date'        => getPost('ot_hpa_date'),
    'ot_hpa_status'      => getPost('ot_hpa_status'),
    'ot_hp_amount'       => getPost('ot_hp_amount'),
    'ot_hp_date'         => getPost('ot_hp_date'),
    'ot_hp_status'       => getPost('ot_hp_status'),
    
    'ot_insurance_name'  => getPost('ot_insurance_name'),
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
   CLEAN DATA (Remove Nulls/Empty to allow Partial Updates)
---------------------------------------------------*/
$data_to_save = array_filter($data_map, function($v) {
    return $v !== null && $v !== '';
});

/* -------------------------------------------------
   DATABASE OPERATION
---------------------------------------------------*/
try {
    if (empty($row_id)) {
        // --- INSERT NEW RECORD ---
        // Duplicate Check
        if ($final_vehicle_number) {
            $check = $conn->prepare("SELECT id FROM stock_vehicle_details WHERE vehicle_number = ?");
            $check->bind_param("s", $final_vehicle_number);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $_SESSION['msg'] = "Error: Vehicle Number already exists!";
                header("Location: inventory.php");
                exit;
            }
            $check->close();
        }

        $data_to_save['created_at'] = date('Y-m-d H:i:s');
        
        $cols = implode(",", array_keys($data_to_save));
        $vals = implode(",", array_fill(0, count($data_to_save), "?"));
        $types = str_repeat("s", count($data_to_save));
        $params = array_values($data_to_save);

        $sql = "INSERT INTO stock_vehicle_details ($cols) VALUES ($vals)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $new_id = $stmt->insert_id;

        $_SESSION['msg'] = "Vehicle Created Successfully!";
        // Redirect to edit page to continue
        header("Location: deal.php?id=" . $new_id); 
        exit;

    } else {
        // --- UPDATE EXISTING RECORD ---
        $set = [];
        $types = "";
        $params = [];

        foreach ($data_to_save as $col => $val) {
            $set[] = "$col = ?";
            $types .= "s";
            $params[] = $val;
        }

        // Append ID for WHERE clause
        $types .= "i";
        $params[] = $row_id;

        $sql = "UPDATE stock_vehicle_details SET " . implode(", ", $set) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $_SESSION['msg'] = "Updated Successfully!";
        // Stay on page
        header("Location: deal.php?id=" . $row_id);
        exit;
    }

} catch (Exception $e) {
    $_SESSION['msg'] = "Error: " . $e->getMessage();
    header("Location: inventory.php");
    exit;
}
?>