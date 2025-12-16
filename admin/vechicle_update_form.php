<?php
// 1. SETUP JSON ENVIRONMENT
session_start();
header('Content-Type: application/json');
// ⭐ CRITICAL DEBUG STEP: Enable error reporting temporarily to see file upload failures.
// You must check your PHP error log file after attempting to upload.
// error_reporting(E_ALL); 
// ini_set('display_errors', 1);

// 2. DATABASE CONNECTION
include 'db.php';

// ---------------------------------------------------------
//  HELPER FUNCTIONS
// ---------------------------------------------------------

// Uploads file and returns unique filename (or null if no file)
function handleUpload($inputName) {
    // Ensure file array exists and there are no system errors (error code 0)
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        // Log the exact error code if a file was attempted but failed
        if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] != UPLOAD_ERR_NO_FILE) {
            error_log("Upload failed for $inputName. Error code: " . $_FILES[$inputName]['error']);
        }
        return null;
    }

    $target_dir = "../images/";
    
    // Safety Check 1: Ensure directory exists and is writable
    if (!is_dir($target_dir)) { 
        if (!mkdir($target_dir, 0777, true)) {
            error_log("Error creating upload directory: " . $target_dir);
            return null;
        }
    }
    
    $fileType = strtolower(pathinfo($_FILES[$inputName]["name"], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
    
    if (in_array($fileType, $allowed)) {
        // Generate Unique Name: use raw uniqid + extension
        $new_filename = uniqid('img_', true) . '.' . $fileType; 
        
        // Safety Check 2: Move file
        if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $target_dir . $new_filename)) {
            return $new_filename;
        } else {
            error_log("Error moving uploaded file for input: " . $inputName . ". Check permissions for $target_dir");
        }
    }
    return null;
}

function cleanInput($data) { global $conn; return mysqli_real_escape_string($conn, trim($data ?? '')); }
function checkVal($name) { return isset($_POST[$name]) ? 1 : 0; }

// Get Master Data to sync numbers across tables
function getMasterVehicleData($id) {
    global $conn;
    $sql = "SELECT vehicle_number, chassis_number, engine_number FROM vehicle WHERE id = '$id'";
    $result = $conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
}

// Sync Vehicle Number/Chassis/Engine to all sub-tables
function syncAllTables($vehicle_id) {
    global $conn;
    $master = getMasterVehicleData($vehicle_id);
    if (!$master) return;

    $v_no = $master['vehicle_number'];
    $chas = $master['chassis_number'];
    $eng  = $master['engine_number'];

    $conn->query("UPDATE vehicle_seller SET seller_vehicle_number='$v_no', seller_chassis_no='$chas', seller_engine_no='$eng' WHERE vehicle_id='$vehicle_id'");
    $conn->query("UPDATE vehicle_purchaser SET purchaser_vehicle_no='$v_no' WHERE vehicle_id='$vehicle_id'");
    $conn->query("UPDATE vehicle_ot SET ot_vehicle_number='$v_no' WHERE vehicle_id='$vehicle_id'");
}

// ---------------------------------------------------------
//  MAIN LOGIC
// ---------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
    $vehicle_id   = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;

    // ======================================================
    // 1️⃣ STEP 1: VEHICLE DETAILS (Photos)
    // ======================================================
    if ($current_step == 1) {
        
        $v_no = cleanInput($_POST['vehicle_number']);

        // Duplicate Check (Only if inserting new or changing number)
        $dup_sql = "SELECT id FROM vehicle WHERE vehicle_number = '$v_no'";
        if ($vehicle_id > 0) { $dup_sql .= " AND id != '$vehicle_id'"; }
        
        $dup_res = $conn->query($dup_sql);
        if ($dup_res->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => '⚠️ Vehicle Number Already Exists!']);
            exit();
        }

        // Collect Inputs
        $v_type = cleanInput($_POST['vehicle_type']);
        $name = cleanInput($_POST['name']); 
        $reg_date = cleanInput($_POST['register_date']);
        $own_ser = cleanInput($_POST['owner_serial']);
        $chas_no = cleanInput($_POST['chassis_number']); 
        $eng_no = cleanInput($_POST['engine_number']); 
        $pay_type = cleanInput($_POST['payment_type']);
        $c_price = cleanInput($_POST['cash_price']) ?: 0;
        $on_method = cleanInput($_POST['online_method']);
        $on_txn = cleanInput($_POST['online_transaction_id']);
        $on_price = cleanInput($_POST['online_price']) ?: 0;
        $pol_chal = cleanInput($_POST['police_challan']);
        $c1_n = cleanInput($_POST['challan1_number']); $c1_a = cleanInput($_POST['challan1_amount']) ?: 0; $c1_s = cleanInput($_POST['challan1_status']);
        $c2_n = cleanInput($_POST['challan2_number']); $c2_a = cleanInput($_POST['challan2_amount']) ?: 0; $c2_s = cleanInput($_POST['challan2_status']);
        $c3_n = cleanInput($_POST['challan3_number']); $c3_a = cleanInput($_POST['challan3_amount']) ?: 0; $c3_s = cleanInput($_POST['challan3_status']);
        $sold_out = checkVal('sold_out');

        // Handle Images
        $p1 = handleUpload('photo1');
        $p2 = handleUpload('photo2');
        $p3 = handleUpload('photo3');
        $p4 = handleUpload('photo4');

        if ($vehicle_id > 0) {
            // UPDATE LOGIC
            $sql = "UPDATE vehicle SET 
                    vehicle_type='$v_type', name='$name', vehicle_number='$v_no', register_date='$reg_date', 
                    owner_serial='$own_ser', chassis_number='$chas_no', engine_number='$eng_no',
                    payment_type='$pay_type', cash_price='$c_price', online_method='$on_method', 
                    online_transaction_id='$on_txn', online_price='$on_price', police_challan='$pol_chal',
                    challan1_number='$c1_n', challan1_amount='$c1_a', challan1_status='$c1_s',
                    challan2_number='$c2_n', challan2_amount='$c2_a', challan2_status='$c2_s',
                    challan3_number='$c3_n', challan3_amount='$c3_a', challan3_status='$c3_s',
                    sold_out='$sold_out'";
            
            // Append image columns ONLY if handleUpload returned a filename
            if ($p1) $sql .= ", photo1='$p1'";
            if ($p2) $sql .= ", photo2='$p2'";
            if ($p3) $sql .= ", photo3='$p3'";
            if ($p4) $sql .= ", photo4='$p4'";

            $sql .= " WHERE id=$vehicle_id";

        } else {
            // INSERT LOGIC
            $p1 = $p1 ?? ''; $p2 = $p2 ?? ''; $p3 = $p3 ?? ''; $p4 = $p4 ?? '';
            $sql = "INSERT INTO vehicle (
                vehicle_type, name, vehicle_number, register_date, owner_serial, chassis_number, engine_number,
                payment_type, cash_price, online_method, online_transaction_id, online_price,
                police_challan, challan1_number, challan1_amount, challan1_status,
                challan2_number, challan2_amount, challan2_status,
                challan3_number, challan3_amount, challan3_status, sold_out,
                photo1, photo2, photo3, photo4
            ) VALUES (
                '$v_type', '$name', '$v_no', '$reg_date', '$own_ser', '$chas_no', '$eng_no',
                '$pay_type', '$c_price', '$on_method', '$on_txn', '$on_price',
                '$pol_chal', '$c1_n', '$c1_a', '$c1_s',
                '$c2_n', '$c2_a', '$c2_s',
                '$c3_n', '$c3_a', '$c3_s', '$sold_out',
                '$p1', '$p2', '$p3', '$p4'
            )";
        }

        if ($conn->query($sql) === TRUE) {
            if ($vehicle_id == 0) $vehicle_id = $conn->insert_id;
            syncAllTables($vehicle_id);
            echo json_encode(['status' => 'success', 'id' => $vehicle_id, 'message' => 'Vehicle Details Saved']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]);
        }
        exit();
    }

    // ======================================================
    // 2️⃣ STEP 2: SELLER (Docs Fixed)
    // ======================================================
    elseif ($current_step == 2) {
        if ($vehicle_id == 0) { echo json_encode(['status' => 'error', 'message' => 'Missing ID']); exit(); }

        $masterData = getMasterVehicleData($vehicle_id);
        $s_vno = cleanInput($masterData['vehicle_number']); 
        $s_chas = cleanInput($masterData['chassis_number']); 
        $s_eng = cleanInput($masterData['engine_number']);   
        $s_bike = cleanInput($_POST['seller_bike_name']); 

        // Collect inputs
        $s_date = cleanInput($_POST['seller_date']);
        $s_name = cleanInput($_POST['seller_name']);
        $s_addr = cleanInput($_POST['seller_address']);
        $s_m1 = cleanInput($_POST['seller_mobile1']); $s_m2 = cleanInput($_POST['seller_mobile2']); $s_m3 = cleanInput($_POST['seller_mobile3']);
        $pr_rc = checkVal('pr_rc'); $pr_tax = checkVal('pr_tax'); $pr_ins = checkVal('pr_insurance'); 
        $pr_puc = checkVal('pr_pucc'); $pr_noc = checkVal('pr_noc'); $noc_st = cleanInput($_POST['noc_status']);
        $s_ptype = cleanInput($_POST['seller_payment_type']);
        $s_cprice = cleanInput($_POST['seller_cash_price']) ?: 0;
        $s_omethod = cleanInput($_POST['seller_online_method']);
        $s_otxn = cleanInput($_POST['seller_online_transaction_id']);
        $s_oprice = cleanInput($_POST['seller_online_price']) ?: 0;
        $ex_show = cleanInput($_POST['exchange_showroom_name']); $st_name = cleanInput($_POST['staff_name']);
        $tot_amt = cleanInput($_POST['total_amount']) ?: 0; $pd_amt = cleanInput($_POST['paid_amount']) ?: 0; $due_amt = cleanInput($_POST['due_amount']) ?: 0; $due_rsn = cleanInput($_POST['due_reason']);

        // Upload Docs
        $doc_af = handleUpload('doc_aadhar_front'); $doc_ab = handleUpload('doc_aadhar_back');
        $doc_vf = handleUpload('doc_voter_front'); $doc_vb = handleUpload('doc_voter_back');
        $rc_f = handleUpload('rc_front'); $rc_b = handleUpload('rc_back');
        $noc_f = handleUpload('noc_front'); $noc_b = handleUpload('noc_back');

        $check = $conn->query("SELECT id FROM vehicle_seller WHERE vehicle_id = $vehicle_id");
        
        if ($check->num_rows > 0) {
            // UPDATE
            $sql = "UPDATE vehicle_seller SET 
                    seller_vehicle_number='$s_vno', seller_chassis_no='$s_chas', seller_engine_no='$s_eng', seller_bike_name='$s_bike',
                    seller_date='$s_date', seller_name='$s_name', seller_address='$s_addr', seller_mobile1='$s_m1', seller_mobile2='$s_m2', seller_mobile3='$s_m3',
                    pr_rc='$pr_rc', pr_tax='$pr_tax', pr_insurance='$pr_ins', pr_pucc='$pr_puc', pr_noc='$pr_noc', noc_status='$noc_st',
                    seller_payment_type='$s_ptype', seller_cash_price='$s_cprice', seller_online_method='$s_omethod', seller_online_transaction_id='$s_otxn', seller_online_price='$s_oprice',
                    exchange_showroom_name='$ex_show', staff_name='$st_name', total_amount='$tot_amt', paid_amount='$pd_amt', due_amount='$due_amt', due_reason='$due_rsn'";
            
            // Only update files if they exist
            if($doc_af) $sql .= ", doc_aadhar_front='$doc_af'";
            if($doc_ab) $sql .= ", doc_aadhar_back='$doc_ab'";
            if($doc_vf) $sql .= ", doc_voter_front='$doc_vf'";
            if($doc_vb) $sql .= ", doc_voter_back='$doc_vb'";
            if($rc_f)   $sql .= ", rc_front='$rc_f'";
            if($rc_b)   $sql .= ", rc_back='$rc_b'";
            if($noc_f)  $sql .= ", noc_front='$noc_f'";
            if($noc_b)  $sql .= ", noc_back='$noc_b'";

            $sql .= " WHERE vehicle_id=$vehicle_id";
        } else {
            // INSERT
            $doc_af = $doc_af ?? ''; $doc_ab = $doc_ab ?? ''; $doc_vf = $doc_vf ?? ''; $doc_vb = $doc_vb ?? '';
            $rc_f = $rc_f ?? ''; $rc_b = $rc_b ?? ''; $noc_f = $noc_f ?? ''; $noc_b = $noc_b ?? '';

            $sql = "INSERT INTO vehicle_seller (
                vehicle_id, seller_date, seller_vehicle_number, seller_bike_name, seller_chassis_no, seller_engine_no,
                seller_name, seller_address, seller_mobile1, seller_mobile2, seller_mobile3,
                pr_rc, pr_tax, pr_insurance, pr_pucc, pr_noc, noc_status,
                seller_payment_type, seller_cash_price, seller_online_method, seller_online_transaction_id, seller_online_price,
                exchange_showroom_name, staff_name, total_amount, paid_amount, due_amount, due_reason,
                doc_aadhar_front, doc_aadhar_back, doc_voter_front, doc_voter_back, rc_front, rc_back, noc_front, noc_back
            ) VALUES (
                '$vehicle_id', '$s_date', '$s_vno', '$s_bike', '$s_chas', '$s_eng',
                '$s_name', '$s_addr', '$s_m1', '$s_m2', '$s_m3',
                '$pr_rc', '$pr_tax', '$pr_ins', '$pr_puc', '$pr_noc', '$noc_st',
                '$s_ptype', '$s_cprice', '$s_omethod', '$s_otxn', '$s_oprice',
                '$ex_show', '$st_name', '$tot_amt', '$pd_amt', '$due_amt', '$due_rsn',
                '$doc_af', '$doc_ab', '$doc_vf', '$doc_vb', '$rc_f', '$rc_b', '$noc_f', '$noc_b'
            )";
        }

        if ($conn->query($sql) === TRUE) { echo json_encode(['status' => 'success', 'id' => $vehicle_id, 'message' => 'Seller Info Saved']); } 
        else { echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]); }
        exit();
    }

    // ======================================================
    // 3️⃣ STEP 3: PURCHASER (Docs Fixed)
    // ======================================================
    elseif ($current_step == 3) {
        if ($vehicle_id == 0) { echo json_encode(['status' => 'error', 'message' => 'Missing ID']); exit(); }

        $masterData = getMasterVehicleData($vehicle_id);
        $p_vno  = cleanInput($masterData['vehicle_number']); 
        $p_bike = cleanInput($_POST['purchaser_bike_name']); 

        // Inputs
        $p_date = cleanInput($_POST['purchaser_date']);
        $p_name = cleanInput($_POST['purchaser_name']);
        $p_addr = cleanInput($_POST['purchaser_address']);
        $pt_amt = cleanInput($_POST['purchaser_transfer_amount']) ?: 0; $pt_date = cleanInput($_POST['purchaser_transfer_date']); $pt_stat = cleanInput($_POST['purchaser_transfer_status']);
        $ph_amt = cleanInput($_POST['purchaser_hpa_amount']) ?: 0; $ph_date = cleanInput($_POST['purchaser_hpa_date']); $ph_stat = cleanInput($_POST['purchaser_hpa_status']);
        $php_amt = cleanInput($_POST['purchaser_hp_amount']) ?: 0; $php_date = cleanInput($_POST['purchaser_hp_date']); $php_stat = cleanInput($_POST['purchaser_hp_status']);
        $pi_name = cleanInput($_POST['purchaser_insurance_name']); $pi_stat = cleanInput($_POST['purchaser_insurance_payment_status']);
        $pi_amt = cleanInput($_POST['purchaser_insurance_amount']) ?: 0; $pi_iss = cleanInput($_POST['purchaser_insurance_issue_date']); $pi_exp = cleanInput($_POST['purchaser_insurance_expiry_date']);
        $p_tot = cleanInput($_POST['purchaser_total']) ?: 0; $p_pd = cleanInput($_POST['purchaser_paid']) ?: 0; $p_due = cleanInput($_POST['purchaser_due']) ?: 0;
        $p_mode = cleanInput($_POST['purchaser_payment_mode']);
        $pc_amt = cleanInput($_POST['purchaser_cash_amount']) ?: 0;
        $pc_m1 = cleanInput($_POST['purchaser_cash_mobile1']); $pc_m2 = cleanInput($_POST['purchaser_cash_mobile2']); $pc_m3 = cleanInput($_POST['purchaser_cash_mobile3']);
        $pf_hpa = cleanInput($_POST['purchaser_fin_hpa_with']); $pf_dis = cleanInput($_POST['purchaser_fin_disburse_amount']) ?: 0; $pf_stat = cleanInput($_POST['purchaser_fin_disburse_status']);
        $pf_m1 = cleanInput($_POST['purchaser_fin_mobile1']); $pf_m2 = cleanInput($_POST['purchaser_fin_mobile2']); $pf_m3 = cleanInput($_POST['purchaser_fin_mobile3']);
        $all_paid = checkVal('purchaser_payment_all_paid');
        
        // Uploads
        $p_af = handleUpload('purchaser_doc_aadhar_front'); $p_ab = handleUpload('purchaser_doc_aadhar_back');
        $p_vf = handleUpload('purchaser_doc_voter_front'); $p_vb = handleUpload('purchaser_doc_voter_back');

        $check = $conn->query("SELECT id FROM vehicle_purchaser WHERE vehicle_id = $vehicle_id");
        
        if ($check->num_rows > 0) {
            // UPDATE
            $sql = "UPDATE vehicle_purchaser SET 
                    purchaser_vehicle_no='$p_vno', purchaser_bike_name='$p_bike',
                    purchaser_date='$p_date', purchaser_name='$p_name', purchaser_address='$p_addr',
                    purchaser_transfer_amount='$pt_amt', purchaser_transfer_date='$pt_date', purchaser_transfer_status='$pt_stat',
                    purchaser_hpa_amount='$ph_amt', purchaser_hpa_date='$ph_date', purchaser_hpa_status='$ph_stat',
                    purchaser_hp_amount='$php_amt', purchaser_hp_date='$php_date', purchaser_hp_status='$php_stat',
                    purchaser_insurance_name='$pi_name', purchaser_insurance_payment_status='$pi_stat', purchaser_insurance_amount='$pi_amt', purchaser_insurance_issue_date='$pi_iss', purchaser_insurance_expiry_date='$pi_exp',
                    purchaser_total='$p_tot', purchaser_paid='$p_pd', purchaser_due='$p_due',
                    purchaser_payment_mode='$p_mode', purchaser_cash_amount='$pc_amt', purchaser_cash_mobile1='$pc_m1', purchaser_cash_mobile2='$pc_m2', purchaser_cash_mobile3='$pc_m3',
                    purchaser_fin_hpa_with='$pf_hpa', purchaser_fin_disburse_amount='$pf_dis', purchaser_fin_disburse_status='$pf_stat', purchaser_fin_mobile1='$pf_m1', purchaser_fin_mobile2='$pf_m2', purchaser_fin_mobile3='$pf_m3',
                    purchaser_payment_all_paid='$all_paid'";

            // Update docs only if new file exists
            if($p_af) $sql .= ", purchaser_doc_aadhar_front='$p_af'";
            if($p_ab) $sql .= ", purchaser_doc_aadhar_back='$p_ab'";
            if($p_vf) $sql .= ", purchaser_doc_voter_front='$p_vf'";
            if($p_vb) $sql .= ", purchaser_doc_voter_back='$p_vb'";

            $sql .= " WHERE vehicle_id=$vehicle_id";
        } else {
            // INSERT
            $p_af = $p_af ?? ''; $p_ab = $p_ab ?? ''; $p_vf = $p_vf ?? ''; $p_vb = $p_vb ?? '';

            $sql = "INSERT INTO vehicle_purchaser (
                vehicle_id, purchaser_date, purchaser_name, purchaser_address, purchaser_bike_name, purchaser_vehicle_no,
                purchaser_transfer_amount, purchaser_transfer_date, purchaser_transfer_status,
                purchaser_hpa_amount, purchaser_hpa_date, purchaser_hpa_status,
                purchaser_hp_amount, purchaser_hp_date, purchaser_hp_status,
                purchaser_insurance_name, purchaser_insurance_payment_status, purchaser_insurance_amount, purchaser_insurance_issue_date, purchaser_insurance_expiry_date,
                purchaser_total, purchaser_paid, purchaser_due,
                purchaser_payment_mode, purchaser_cash_amount, purchaser_cash_mobile1, purchaser_cash_mobile2, purchaser_cash_mobile3,
                purchaser_fin_hpa_with, purchaser_fin_disburse_amount, purchaser_fin_disburse_status, purchaser_fin_mobile1, purchaser_fin_mobile2, purchaser_fin_mobile3,
                purchaser_doc_aadhar_front, purchaser_doc_aadhar_back, purchaser_doc_voter_front, purchaser_doc_voter_back, purchaser_payment_all_paid
            ) VALUES (
                '$vehicle_id', '$p_date', '$p_name', '$p_addr', '$p_bike', '$p_vno',
                '$pt_amt', '$pt_date', '$pt_stat', '$ph_amt', '$ph_date', '$ph_stat', '$php_amt', '$php_date', '$php_stat',
                '$pi_name', '$pi_stat', '$pi_amt', '$pi_iss', '$pi_exp',
                '$p_tot', '$p_pd', '$p_due',
                '$p_mode', '$pc_amt', '$pc_m1', '$pc_m2', '$pc_m3',
                '$pf_hpa', '$pf_dis', '$pf_stat', '$pf_m1', '$pf_m2', '$pf_m3',
                '$p_af', '$p_ab', '$p_vf', '$p_vb', '$all_paid'
            )";
        }
        if ($conn->query($sql) === TRUE) { echo json_encode(['status' => 'success', 'id' => $vehicle_id, 'message' => 'Purchaser Info Saved']); } 
        else { echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]); }
        exit();
    }

    // ======================================================
    // 4️⃣ STEP 4: TRANSFER
    // ======================================================
    elseif ($current_step == 4) {
        if ($vehicle_id == 0) { echo json_encode(['status' => 'error', 'message' => 'Missing ID']); exit(); }

        $masterData = getMasterVehicleData($vehicle_id);
        $ot_vno = cleanInput($masterData['vehicle_number']); 
        $ot_name = cleanInput($_POST['ot_name_transfer']); 

        $ot_rto = cleanInput($_POST['ot_rto_name']);
        $ot_vend = cleanInput($_POST['ot_vendor_name']);
        $ot_t_amt = cleanInput($_POST['ot_transfer_amount']) ?: 0; $ot_t_date = cleanInput($_POST['ot_transfer_date']); $ot_t_stat = cleanInput($_POST['ot_transfer_status']);
        $ot_h_amt = cleanInput($_POST['ot_hpa_amount']) ?: 0; $ot_h_date = cleanInput($_POST['ot_hpa_date']); $ot_h_stat = cleanInput($_POST['ot_hpa_status']);
        $ot_hp_amt = cleanInput($_POST['ot_hp_amount']) ?: 0; $ot_hp_date = cleanInput($_POST['ot_hp_date']); $ot_hp_stat = cleanInput($_POST['ot_hp_status']);
        $ot_i_name = cleanInput($_POST['ot_insurance_name']); $ot_i_stat = cleanInput($_POST['ot_insurance_payment_status']);
        $ot_i_amt = cleanInput($_POST['ot_insurance_amount']) ?: 0; $ot_i_start = cleanInput($_POST['ot_insurance_start_date']); $ot_i_end = cleanInput($_POST['ot_insurance_end_date']);
        $ot_p_stat = cleanInput($_POST['ot_purchaser_sign_status']); $ot_p_date = cleanInput($_POST['ot_purchaser_sign_date']);
        $ot_s_stat = cleanInput($_POST['ot_seller_sign_status']); $ot_s_date = cleanInput($_POST['ot_seller_sign_date']);

        $check = $conn->query("SELECT id FROM vehicle_ot WHERE vehicle_id = $vehicle_id");
        if ($check->num_rows > 0) {
            $sql = "UPDATE vehicle_ot SET 
                    ot_vehicle_number='$ot_vno', ot_name_transfer='$ot_name', ot_rto_name='$ot_rto', ot_vendor_name='$ot_vend',
                    ot_transfer_amount='$ot_t_amt', ot_transfer_date='$ot_t_date', ot_transfer_status='$ot_t_stat',
                    ot_hpa_amount='$ot_h_amt', ot_hpa_date='$ot_h_date', ot_hpa_status='$ot_h_stat',
                    ot_hp_amount='$ot_hp_amt', ot_hp_date='$ot_hp_date', ot_hp_status='$ot_hp_stat',
                    ot_insurance_name='$ot_i_name', ot_insurance_payment_status='$ot_i_stat', ot_insurance_amount='$ot_i_amt', ot_insurance_start_date='$ot_i_start', ot_insurance_end_date='$ot_i_end',
                    ot_purchaser_sign_status='$ot_p_stat', ot_purchaser_sign_date='$ot_p_date', ot_seller_sign_status='$ot_s_stat', ot_seller_sign_date='$ot_s_date'
                    WHERE vehicle_id=$vehicle_id";
        } else {
            $sql = "INSERT INTO vehicle_ot (
                vehicle_id, ot_name_transfer, ot_vehicle_number, ot_rto_name, ot_vendor_name,
                ot_transfer_amount, ot_transfer_date, ot_transfer_status,
                ot_hpa_amount, ot_hpa_date, ot_hpa_status,
                ot_hp_amount, ot_hp_date, ot_hp_status,
                ot_insurance_name, ot_insurance_payment_status, ot_insurance_amount, ot_insurance_start_date, ot_insurance_end_date,
                ot_purchaser_sign_status, ot_purchaser_sign_date, ot_seller_sign_status, ot_seller_sign_date
            ) VALUES (
                '$vehicle_id', '$ot_name', '$ot_vno', '$ot_rto', '$ot_vend',
                '$ot_t_amt', '$ot_t_date', '$ot_t_stat',
                '$ot_h_amt', '$ot_h_date', '$ot_h_stat',
                '$ot_hp_amt', '$ot_hp_date', '$ot_hp_stat',
                '$ot_i_name', '$ot_i_stat', '$ot_i_amt', '$ot_i_start', '$ot_i_end',
                '$ot_p_stat', '$ot_p_date', '$ot_s_stat', '$ot_s_date'
            )";
        }

        if ($conn->query($sql) === TRUE) {
            if ($action == 'finish') { $_SESSION['success_message'] = "Vehicle Deal #$vehicle_id Saved Successfully!"; }
            echo json_encode(['status' => 'success', 'id' => $vehicle_id, 'message' => 'Deal Completed']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $conn->error]);
        }
        exit();
    }
}
?>