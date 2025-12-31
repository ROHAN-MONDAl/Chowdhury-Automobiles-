<?php
// ==========================================
// 1. GLOBAL CONFIGURATION (Run for all requests)
// ==========================================
// Increase script execution time to 5 minutes
set_time_limit(300);
// Increase time allowed to receive input data (critical for slow networks)
ini_set('max_input_time', 300);

// Hide errors from screen to prevent JSON breakage
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Increase memory limit for image processing
ini_set('memory_limit', '256M');

// Ensure the server accepts larger POST requests
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
// Start Session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// 2. SERVER LIMIT CHECK (NO LIMITS)
// ==========================================
if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $server_limit = ini_get('post_max_size');

    echo json_encode([
        'status' => 'error',
        'message' => "Upload crashed the server! The total size sent was " . round($_SERVER['CONTENT_LENGTH'] / 1024 / 1024, 2) . "MB. Server Limit is: $server_limit.",
        'error_type' => 'overflow'
    ]);
    exit;
}

// ==========================================
// 3. DATABASE & HELPERS
// ==========================================

// Connect to Database
if (file_exists('db.php')) {
    include 'db.php';
} else {
    // If DB is missing during a POST, return JSON error
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'db.php missing']);
        exit;
    } else {
        die("Database connection file (db.php) missing.");
    }
}

// Global Helpers (Available for both Saving and HTML View)
function cleanInput($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data ?? ''));
}

function checkVal($name)
{
    return isset($_POST[$name]) ? 1 : 0;
}

// ==========================================
// 4. SAVE HANDLER (RUNS ONLY ON POST REQUEST)
// ==========================================
// This 'if' block ensures the code only runs when you click Save/Next
// It protects the page from crashing when you simply try to view it.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Start Buffer to catch any PHP Warnings
    ob_start();
    header('Content-Type: application/json');
    $response = array();

    try {
        // --- IMAGE COMPRESSION FUNCTION ---
        function compressImage($source, $destination, $quality)
        {
            // Check if GD is available
            if (!extension_loaded('gd') || !function_exists('imagecreatefromjpeg')) {
                // Fallback: Just move the file
                if (move_uploaded_file($source, $destination)) return true;
                throw new Exception("Server missing GD Library and failed to move file.");
            }

            $info = getimagesize($source);
            if ($info === false) throw new Exception("Invalid image file.");

            $mime = $info['mime'];

            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($source);
                    // Fix Rotation (EXIF)
                    if (function_exists('exif_read_data')) {
                        $exif = @exif_read_data($source);
                        if ($exif && isset($exif['Orientation'])) {
                            $ort = $exif['Orientation'];
                            if ($ort == 3) $image = imagerotate($image, 180, 0);
                            if ($ort == 6) $image = imagerotate($image, -90, 0);
                            if ($ort == 8) $image = imagerotate($image, 90, 0);
                        }
                    }
                    imagejpeg($image, $destination, $quality);
                    break;

                case 'image/png':
                    $image = imagecreatefrompng($source);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagepng($image, $destination, 9);
                    break;

                case 'image/webp':
                    $image = imagecreatefromwebp($source);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagewebp($image, $destination, $quality);
                    break;

                default:
                    if (!move_uploaded_file($source, $destination)) {
                        throw new Exception("Failed to move uploaded file.");
                    }
                    return true;
            }

            if (isset($image)) imagedestroy($image);
            return true;
        }

        // --- UPLOAD HANDLER ---
        function handleUpload($inputName)
        {
            if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] != 0) {
                return "";
            }

            // Validate Size (Max 5MB)
            $maxSize = 5 * 1024 * 1024;
            if ($_FILES[$inputName]['size'] > $maxSize) {
                throw new Exception("File '$inputName' exceeds 5MB limit.");
            }

            $target_dir = "../images/";
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    throw new Exception("Server cannot create directory '$target_dir'. Check Permissions.");
                }
            }

            $fileType = strtolower(pathinfo($_FILES[$inputName]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            if (!in_array($fileType, $allowed)) {
                throw new Exception("Invalid file type '$fileType'.");
            }

            $cleanName = preg_replace("/[^a-zA-Z0-9]/", "", pathinfo($_FILES[$inputName]["name"], PATHINFO_FILENAME));
            $new_filename = time() . "_" . rand(100, 999) . "_" . $cleanName . "." . $fileType;
            $target_file = $target_dir . $new_filename;

            if ($fileType === 'pdf') {
                if (!move_uploaded_file($_FILES[$inputName]["tmp_name"], $target_file)) {
                    throw new Exception("Failed to upload PDF.");
                }
            } else {
                compressImage($_FILES[$inputName]["tmp_name"], $target_file, 75);
            }

            return $new_filename;
        }

        // --- NEW HELPER: GET MASTER DATA ---
        // Retrieves Step 1 data to auto-fill other steps if blank
        function getMasterData($vehicle_id)
        {
            global $conn;
            $sql = "SELECT vehicle_number, chassis_number, engine_number, name FROM vehicle WHERE id = '$vehicle_id'";
            $res = $conn->query($sql);
            return ($res && $row = $res->fetch_assoc()) ? $row : null;
        }

        // --- SYNC FUNCTION (Only used for Step 1) ---
        // Forces all tables to match Step 1 when Step 1 is saved.
        function syncAllTables($vehicle_id)
        {
            global $conn;
            $master = getMasterData($vehicle_id);
            if ($master) {
                $v = cleanInput($master['vehicle_number']);
                $c = cleanInput($master['chassis_number']);
                $e = cleanInput($master['engine_number']);
                $n = cleanInput($master['name']);

                $conn->query("UPDATE vehicle_seller SET seller_vehicle_number='$v', seller_chassis_no='$c', seller_engine_no='$e', seller_bike_name='$n' WHERE vehicle_id='$vehicle_id'");
                $conn->query("UPDATE vehicle_purchaser SET purchaser_vehicle_no='$v', purchaser_bike_name='$n' WHERE vehicle_id='$vehicle_id'");
                $conn->query("UPDATE vehicle_ot SET ot_vehicle_number='$v' WHERE vehicle_id='$vehicle_id'");
            }
        }

        // --- PROCESS INPUTS ---
        $action = $_POST['formAction'] ?? $_POST['action'] ?? 'save_next';
        $current_step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
        $vehicle_id   = isset($_POST['vehicle_id']) && is_numeric($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;

        // --- STEP 1: VEHICLE MASTER ---
        if ($current_step == 1) {
            $v_no = cleanInput($_POST['vehicle_number']);

            if ($action !== 'save_draft') {
                $checkSql = "SELECT id FROM vehicle WHERE vehicle_number = '$v_no'";
                if ($vehicle_id > 0) $checkSql .= " AND id != '$vehicle_id'";
                $dup = $conn->query($checkSql);
                if ($dup->num_rows > 0) throw new Exception("Vehicle Number Already Exists");
            }

            $v_type = cleanInput($_POST['vehicle_type']);
            $name = cleanInput($_POST['name']);
            $reg_date = cleanInput($_POST['register_date']);
            $own_ser = cleanInput($_POST['owner_serial']);
            $chas_no = cleanInput($_POST['chassis_number']);
            $eng_no = cleanInput($_POST['engine_number']);
            $pay_type = cleanInput($_POST['payment_type']);
            $c_price = cleanInput($_POST['cash_price']) ?: 0;
            $o_method = cleanInput($_POST['online_method']);
            $o_txn = cleanInput($_POST['online_transaction_id']);
            $o_price = cleanInput($_POST['online_price']) ?: 0;
            $pol_chal = cleanInput($_POST['police_challan']);
            $c1_n = cleanInput($_POST['challan1_number']);
            $c1_a = cleanInput($_POST['challan1_amount']) ?: 0;
            $c1_s = cleanInput($_POST['challan1_status']);
            $c2_n = cleanInput($_POST['challan2_number']);
            $c2_a = cleanInput($_POST['challan2_amount']) ?: 0;
            $c2_s = cleanInput($_POST['challan2_status']);
            $c3_n = cleanInput($_POST['challan3_number']);
            $c3_a = cleanInput($_POST['challan3_amount']) ?: 0;
            $c3_s = cleanInput($_POST['challan3_status']);
            $sold_out = checkVal('sold_out');

            $p1 = handleUpload('photo1');
            $p2 = handleUpload('photo2');
            $p3 = handleUpload('photo3');
            $p4 = handleUpload('photo4');

            if ($vehicle_id > 0) {
                $sql = "UPDATE vehicle SET vehicle_type='$v_type', name='$name', vehicle_number='$v_no', register_date='$reg_date', 
                    owner_serial='$own_ser', chassis_number='$chas_no', engine_number='$eng_no', payment_type='$pay_type', cash_price='$c_price', 
                    online_method='$o_method', online_transaction_id='$o_txn', online_price='$o_price', police_challan='$pol_chal',
                    challan1_number='$c1_n', challan1_amount='$c1_a', challan1_status='$c1_s',
                    challan2_number='$c2_n', challan2_amount='$c2_a', challan2_status='$c2_s',
                    challan3_number='$c3_n', challan3_amount='$c3_a', challan3_status='$c3_s', sold_out='$sold_out',
                    photo1=IF('$p1'!='','$p1',photo1), photo2=IF('$p2'!='','$p2',photo2), photo3=IF('$p3'!='','$p3',photo3), photo4=IF('$p4'!='','$p4',photo4)
                    WHERE id='$vehicle_id'";
            } else {
                $sql = "INSERT INTO vehicle (vehicle_type, name, vehicle_number, register_date, owner_serial, chassis_number, engine_number,
                    payment_type, cash_price, online_method, online_transaction_id, online_price,
                    police_challan, challan1_number, challan1_amount, challan1_status, challan2_number, challan2_amount, challan2_status,
                    challan3_number, challan3_amount, challan3_status, sold_out, photo1, photo2, photo3, photo4) 
                    VALUES ('$v_type', '$name', '$v_no', '$reg_date', '$own_ser', '$chas_no', '$eng_no',
                    '$pay_type', '$c_price', '$o_method', '$o_txn', '$o_price',
                    '$pol_chal', '$c1_n', '$c1_a', '$c1_s', '$c2_n', '$c2_a', '$c2_s',
                    '$c3_n', '$c3_a', '$c3_s', '$sold_out', '$p1', '$p2', '$p3', '$p4')";
            }

            if ($conn->query($sql)) {
                if ($vehicle_id == 0) $vehicle_id = $conn->insert_id;
                // Force sync only on Step 1 Save
                syncAllTables($vehicle_id);
            } else {
                throw new Exception("SQL Error: " . $conn->error);
            }
        }
        // --- STEP 2: SELLER ---
        elseif ($current_step == 2) {
            if ($vehicle_id == 0) {
                // If trying to save Step 2 without an ID, check if Step 1 data is present in POST to create it first
                if (!empty($_POST['vehicle_number']) && !empty($_POST['name'])) {
                    // Logic could go here to auto-create step 1, but for now we throw error
                    throw new Exception("Please save Step 1 details first before adding Seller info.");
                } else {
                    throw new Exception("Vehicle ID missing.");
                }
            }

            // SMART FILL: Get Master Data
            $master = getMasterData($vehicle_id);

            // If POST is empty, use Master. If POST has value, use POST (Manual Override).
            $s_veh_no = !empty($_POST['seller_vehicle_number']) ? cleanInput($_POST['seller_vehicle_number']) : ($master['vehicle_number'] ?? '');
            $s_chas   = !empty($_POST['seller_chassis_no'])     ? cleanInput($_POST['seller_chassis_no'])     : ($master['chassis_number'] ?? '');
            $s_eng    = !empty($_POST['seller_engine_no'])      ? cleanInput($_POST['seller_engine_no'])      : ($master['engine_number'] ?? '');
            $s_b_name = !empty($_POST['seller_bike_name'])      ? cleanInput($_POST['seller_bike_name'])      : ($master['name'] ?? '');

            $s_date = cleanInput($_POST['seller_date']);
            $s_name = cleanInput($_POST['seller_name']);
            $s_addr = cleanInput($_POST['seller_address']);
            $s_m1 = cleanInput($_POST['seller_mobile1']);
            $s_m2 = cleanInput($_POST['seller_mobile2']);
            $s_m3 = cleanInput($_POST['seller_mobile3']);
            $pr_rc = checkVal('pr_rc');
            $pr_tax = checkVal('pr_tax');
            $pr_ins = checkVal('pr_insurance');
            $pr_puc = checkVal('pr_pucc');
            $pr_noc = checkVal('pr_noc');
            $noc_st = cleanInput($_POST['noc_status']);
            $s_ptype = cleanInput($_POST['seller_payment_type']);
            $s_cprice = cleanInput($_POST['seller_cash_price']) ?: 0;
            $s_omethod = cleanInput($_POST['seller_online_method']);
            $s_otxn = cleanInput($_POST['seller_online_transaction_id']);
            $s_oprice = cleanInput($_POST['seller_online_price']) ?: 0;
            $ex_show = cleanInput($_POST['exchange_showroom_name']);
            $st_name = cleanInput($_POST['staff_name']);
            $tot_amt = cleanInput($_POST['total_amount']) ?: 0;
            $pd_amt = cleanInput($_POST['paid_amount']) ?: 0;
            $due_amt = cleanInput($_POST['due_amount']) ?: 0;
            $due_rsn = cleanInput($_POST['due_reason']);

            $doc_af = handleUpload('doc_aadhar_front');
            $doc_ab = handleUpload('doc_aadhar_back');
            $doc_vf = handleUpload('doc_voter_front');
            $doc_vb = handleUpload('doc_voter_back');
            $rc_f = handleUpload('rc_front');
            $rc_b = handleUpload('rc_back');
            $noc_f = handleUpload('noc_front');
            $noc_b = handleUpload('noc_back');

            $check = $conn->query("SELECT id FROM vehicle_seller WHERE vehicle_id = '$vehicle_id'");
            if ($check->num_rows > 0) {
                $sql = "UPDATE vehicle_seller SET 
                    seller_vehicle_number='$s_veh_no', seller_chassis_no='$s_chas', seller_engine_no='$s_eng', seller_bike_name='$s_b_name',
                    seller_date='$s_date', seller_name='$s_name', seller_address='$s_addr',
                    seller_mobile1='$s_m1', seller_mobile2='$s_m2', seller_mobile3='$s_m3',
                    pr_rc='$pr_rc', pr_tax='$pr_tax', pr_insurance='$pr_ins', pr_pucc='$pr_puc', pr_noc='$pr_noc', noc_status='$noc_st',
                    seller_payment_type='$s_ptype', seller_cash_price='$s_cprice', seller_online_method='$s_omethod', seller_online_transaction_id='$s_otxn', seller_online_price='$s_oprice',
                    exchange_showroom_name='$ex_show', staff_name='$st_name', total_amount='$tot_amt', paid_amount='$pd_amt', due_amount='$due_amt', due_reason='$due_rsn',
                    doc_aadhar_front=IF('$doc_af'!='','$doc_af',doc_aadhar_front), doc_aadhar_back=IF('$doc_ab'!='','$doc_ab',doc_aadhar_back),
                    doc_voter_front=IF('$doc_vf'!='','$doc_vf',doc_voter_front), doc_voter_back=IF('$doc_vb'!='','$doc_vb',doc_voter_back),
                    rc_front=IF('$rc_f'!='','$rc_f',rc_front), rc_back=IF('$rc_b'!='','$rc_b',rc_back),
                    noc_front=IF('$noc_f'!='','$noc_f',noc_front), noc_back=IF('$noc_b'!='','$noc_b',noc_back)
                    WHERE vehicle_id='$vehicle_id'";
            } else {
                $sql = "INSERT INTO vehicle_seller (vehicle_id, seller_vehicle_number, seller_chassis_no, seller_engine_no, seller_bike_name,
                    seller_date, seller_name, seller_address, seller_mobile1, seller_mobile2, seller_mobile3,
                    pr_rc, pr_tax, pr_insurance, pr_pucc, pr_noc, noc_status, seller_payment_type, seller_cash_price, seller_online_method, seller_online_transaction_id, seller_online_price,
                    exchange_showroom_name, staff_name, total_amount, paid_amount, due_amount, due_reason,
                    doc_aadhar_front, doc_aadhar_back, doc_voter_front, doc_voter_back, rc_front, rc_back, noc_front, noc_back)
                    VALUES ('$vehicle_id', '$s_veh_no', '$s_chas', '$s_eng', '$s_b_name', 
                    '$s_date', '$s_name', '$s_addr', '$s_m1', '$s_m2', '$s_m3',
                    '$pr_rc', '$pr_tax', '$pr_ins', '$pr_puc', '$pr_noc', '$noc_st', '$s_ptype', '$s_cprice', '$s_omethod', '$s_otxn', '$s_oprice',
                    '$ex_show', '$st_name', '$tot_amt', '$pd_amt', '$due_amt', '$due_rsn',
                    '$doc_af', '$doc_ab', '$doc_vf', '$doc_vb', '$rc_f', '$rc_b', '$noc_f', '$noc_b')";
            }
            if (!$conn->query($sql)) throw new Exception("SQL Error Step 2: " . $conn->error);
        }
        // --- STEP 3: PURCHASER ---
        elseif ($current_step == 3) {
            if ($vehicle_id == 0) throw new Exception("Vehicle ID is missing for Step 3");

            // SMART FILL: Get Master Data
            $master = getMasterData($vehicle_id);

            // Manual Override or Auto Fill
            $p_veh_no = !empty($_POST['purchaser_vehicle_no']) ? cleanInput($_POST['purchaser_vehicle_no']) : ($master['vehicle_number'] ?? '');
            $p_b_name = !empty($_POST['purchaser_bike_name'])  ? cleanInput($_POST['purchaser_bike_name'])  : ($master['name'] ?? '');

            $p_date = cleanInput($_POST['purchaser_date']);
            $p_name = cleanInput($_POST['purchaser_name']);
            $p_addr = cleanInput($_POST['purchaser_address']);
            $pt_amt = cleanInput($_POST['purchaser_transfer_amount']) ?: 0;
            $pt_date = cleanInput($_POST['purchaser_transfer_date']);
            $pt_stat = cleanInput($_POST['purchaser_transfer_status']);
            $ph_amt = cleanInput($_POST['purchaser_hpa_amount']) ?: 0;
            $ph_date = cleanInput($_POST['purchaser_hpa_date']);
            $ph_stat = cleanInput($_POST['purchaser_hpa_status']);
            $php_amt = cleanInput($_POST['purchaser_hp_amount']) ?: 0;
            $php_date = cleanInput($_POST['purchaser_hp_date']);
            $php_stat = cleanInput($_POST['purchaser_hp_status']);
            $pi_name = cleanInput($_POST['purchaser_insurance_name']);
            $pi_stat = cleanInput($_POST['purchaser_insurance_payment_status']);
            $pi_amt = cleanInput($_POST['purchaser_insurance_amount']) ?: 0;
            $pi_iss = cleanInput($_POST['purchaser_insurance_issue_date']);
            $pi_exp = cleanInput($_POST['purchaser_insurance_expiry_date']);
            $p_tot = cleanInput($_POST['purchaser_total']) ?: 0;
            $p_pd = cleanInput($_POST['purchaser_paid']) ?: 0;
            $p_due = cleanInput($_POST['purchaser_due']) ?: 0;
            $p_mode = cleanInput($_POST['purchaser_payment_mode']);
            $all_pd = checkVal('purchaser_payment_all_paid');
            $pc_amt = cleanInput($_POST['purchaser_cash_amount']) ?: 0;
            $pc_m1 = cleanInput($_POST['purchaser_cash_mobile1']);
            $pc_m2 = cleanInput($_POST['purchaser_cash_mobile2']);
            $pc_m3 = cleanInput($_POST['purchaser_cash_mobile3']);
            $pf_hpa = cleanInput($_POST['purchaser_fin_hpa_with']);
            $pf_dis = cleanInput($_POST['purchaser_fin_disburse_amount']) ?: 0;
            $pf_stat = cleanInput($_POST['purchaser_fin_disburse_status']);
            $pf_m1 = cleanInput($_POST['purchaser_fin_mobile1']);
            $pf_m2 = cleanInput($_POST['purchaser_fin_mobile2']);
            $pf_m3 = cleanInput($_POST['purchaser_fin_mobile3']);
            $p_af = handleUpload('purchaser_doc_aadhar_front');
            $p_ab = handleUpload('purchaser_doc_aadhar_back');
            $p_vf = handleUpload('purchaser_doc_voter_front');
            $p_vb = handleUpload('purchaser_doc_voter_back');

            $check = $conn->query("SELECT id FROM vehicle_purchaser WHERE vehicle_id = '$vehicle_id'");
            if ($check->num_rows > 0) {
                $sql = "UPDATE vehicle_purchaser SET 
                    purchaser_vehicle_no='$p_veh_no', purchaser_bike_name='$p_b_name',
                    purchaser_date='$p_date', purchaser_name='$p_name', purchaser_address='$p_addr',
                    purchaser_transfer_amount='$pt_amt', purchaser_transfer_date='$pt_date', purchaser_transfer_status='$pt_stat',
                    purchaser_hpa_amount='$ph_amt', purchaser_hpa_date='$ph_date', purchaser_hpa_status='$ph_stat',
                    purchaser_hp_amount='$php_amt', purchaser_hp_date='$php_date', purchaser_hp_status='$php_stat',
                    purchaser_insurance_name='$pi_name', purchaser_insurance_payment_status='$pi_stat', purchaser_insurance_amount='$pi_amt', purchaser_insurance_issue_date='$pi_iss', purchaser_insurance_expiry_date='$pi_exp',
                    purchaser_total='$p_tot', purchaser_paid='$p_pd', purchaser_due='$p_due', purchaser_payment_mode='$p_mode', purchaser_payment_all_paid='$all_pd',
                    purchaser_cash_amount='$pc_amt', purchaser_cash_mobile1='$pc_m1', purchaser_cash_mobile2='$pc_m2', purchaser_cash_mobile3='$pc_m3',
                    purchaser_fin_hpa_with='$pf_hpa', purchaser_fin_disburse_amount='$pf_dis', purchaser_fin_disburse_status='$pf_stat',
                    purchaser_fin_mobile1='$pf_m1', purchaser_fin_mobile2='$pf_m2', purchaser_fin_mobile3='$pf_m3',
                    purchaser_doc_aadhar_front=IF('$p_af'!='','$p_af',purchaser_doc_aadhar_front), purchaser_doc_aadhar_back=IF('$p_ab'!='','$p_ab',purchaser_doc_aadhar_back),
                    purchaser_doc_voter_front=IF('$p_vf'!='','$p_vf',purchaser_doc_voter_front), purchaser_doc_voter_back=IF('$p_vb'!='','$p_vb',purchaser_doc_voter_back)
                    WHERE vehicle_id='$vehicle_id'";
            } else {
                $sql = "INSERT INTO vehicle_purchaser (vehicle_id, purchaser_vehicle_no, purchaser_bike_name, purchaser_date, purchaser_name, purchaser_address,
                    purchaser_transfer_amount, purchaser_transfer_date, purchaser_transfer_status,
                    purchaser_hpa_amount, purchaser_hpa_date, purchaser_hpa_status, purchaser_hp_amount, purchaser_hp_date, purchaser_hp_status,
                    purchaser_insurance_name, purchaser_insurance_payment_status, purchaser_insurance_amount, purchaser_insurance_issue_date, purchaser_insurance_expiry_date,
                    purchaser_total, purchaser_paid, purchaser_due, purchaser_payment_mode, purchaser_payment_all_paid,
                    purchaser_cash_amount, purchaser_cash_mobile1, purchaser_cash_mobile2, purchaser_cash_mobile3,
                    purchaser_fin_hpa_with, purchaser_fin_disburse_amount, purchaser_fin_disburse_status, purchaser_fin_mobile1, purchaser_fin_mobile2, purchaser_fin_mobile3,
                    purchaser_doc_aadhar_front, purchaser_doc_aadhar_back, purchaser_doc_voter_front, purchaser_doc_voter_back)
                    VALUES ('$vehicle_id', '$p_veh_no', '$p_b_name', '$p_date', '$p_name', '$p_addr',
                    '$pt_amt', '$pt_date', '$pt_stat', '$ph_amt', '$ph_date', '$ph_stat', '$php_amt', '$php_date', '$php_stat',
                    '$pi_name', '$pi_stat', '$pi_amt', '$pi_iss', '$pi_exp',
                    '$p_tot', '$p_pd', '$p_due', '$p_mode', '$all_pd',
                    '$pc_amt', '$pc_m1', '$pc_m2', '$pc_m3',
                    '$pf_hpa', '$pf_dis', '$pf_stat', '$pf_m1', '$pf_m2', '$pf_m3',
                    '$p_af', '$p_ab', '$p_vf', '$p_vb')";
            }
            if (!$conn->query($sql)) throw new Exception("SQL Error Step 3: " . $conn->error);
        }
        // --- STEP 4: OT ---
        elseif ($current_step == 4) {
            if ($vehicle_id == 0) throw new Exception("Vehicle ID is missing for Step 4");

            // SMART FILL: Get Master Data
            $master = getMasterData($vehicle_id);

            // Manual Override or Auto Fill
            $ot_v_no = !empty($_POST['ot_vehicle_number']) ? cleanInput($_POST['ot_vehicle_number']) : ($master['vehicle_number'] ?? '');

            $ot_name = cleanInput($_POST['ot_name_transfer']);
            $ot_rto = cleanInput($_POST['ot_rto_name']);
            $ot_vend = cleanInput($_POST['ot_vendor_name']);
            $ot_t_amt = cleanInput($_POST['ot_transfer_amount']) ?: 0;
            $ot_t_date = cleanInput($_POST['ot_transfer_date']);
            $ot_t_stat = cleanInput($_POST['ot_transfer_status']);
            $ot_h_amt = cleanInput($_POST['ot_hpa_amount']) ?: 0;
            $ot_h_date = cleanInput($_POST['ot_hpa_date']);
            $ot_h_stat = cleanInput($_POST['ot_hpa_status']);
            $ot_hp_amt = cleanInput($_POST['ot_hp_amount']) ?: 0;
            $ot_hp_date = cleanInput($_POST['ot_hp_date']);
            $ot_hp_stat = cleanInput($_POST['ot_hp_status']);
            $ot_i_name = cleanInput($_POST['ot_insurance_name']);
            $ot_i_stat = cleanInput($_POST['ot_insurance_payment_status']);
            $ot_i_amt = cleanInput($_POST['ot_insurance_amount']) ?: 0;
            $ot_i_start = cleanInput($_POST['ot_insurance_start_date']);
            $ot_i_end = cleanInput($_POST['ot_insurance_end_date']);
            $ot_p_stat = cleanInput($_POST['ot_purchaser_sign_status']);
            $ot_p_date = cleanInput($_POST['ot_purchaser_sign_date']);
            $ot_s_stat = cleanInput($_POST['ot_seller_sign_status']);
            $ot_s_date = cleanInput($_POST['ot_seller_sign_date']);

            $check = $conn->query("SELECT id FROM vehicle_ot WHERE vehicle_id = '$vehicle_id'");
            if ($check->num_rows > 0) {
                $sql = "UPDATE vehicle_ot SET 
                    ot_vehicle_number='$ot_v_no',
                    ot_name_transfer='$ot_name', ot_rto_name='$ot_rto', ot_vendor_name='$ot_vend',
                    ot_transfer_amount='$ot_t_amt', ot_transfer_date='$ot_t_date', ot_transfer_status='$ot_t_stat',
                    ot_hpa_amount='$ot_h_amt', ot_hpa_date='$ot_h_date', ot_hpa_status='$ot_h_stat',
                    ot_hp_amount='$ot_hp_amt', ot_hp_date='$ot_hp_date', ot_hp_status='$ot_hp_stat',
                    ot_insurance_name='$ot_i_name', ot_insurance_payment_status='$ot_i_stat', ot_insurance_amount='$ot_i_amt', ot_insurance_start_date='$ot_i_start', ot_insurance_end_date='$ot_i_end',
                    ot_purchaser_sign_status='$ot_p_stat', ot_purchaser_sign_date='$ot_p_date', ot_seller_sign_status='$ot_s_stat', ot_seller_sign_date='$ot_s_date'
                    WHERE vehicle_id='$vehicle_id'";
            } else {
                $sql = "INSERT INTO vehicle_ot (vehicle_id, ot_vehicle_number, ot_name_transfer, ot_rto_name, ot_vendor_name,
                    ot_transfer_amount, ot_transfer_date, ot_transfer_status, ot_hpa_amount, ot_hpa_date, ot_hpa_status,
                    ot_hp_amount, ot_hp_date, ot_hp_status, ot_insurance_name, ot_insurance_payment_status, ot_insurance_amount, ot_insurance_start_date, ot_insurance_end_date,
                    ot_purchaser_sign_status, ot_purchaser_sign_date, ot_seller_sign_status, ot_seller_sign_date)
                    VALUES ('$vehicle_id', '$ot_v_no', '$ot_name', '$ot_rto', '$ot_vend',
                    '$ot_t_amt', '$ot_t_date', '$ot_t_stat', '$ot_h_amt', '$ot_h_date', '$ot_h_stat',
                    '$ot_hp_amt', '$ot_hp_date', '$ot_hp_stat', '$ot_i_name', '$ot_i_stat', '$ot_i_amt', '$ot_i_start', '$ot_i_end',
                    '$ot_p_stat', '$ot_p_date', '$ot_s_stat', '$ot_s_date')";
            }
            if (!$conn->query($sql)) throw new Exception("SQL Error Step 4: " . $conn->error);
            // syncAllTables($vehicle_id); // <--- REMOVED TO PREVENT OVERWRITING MANUAL EDITS
        }

        // ==========================================
        // 5. SUCCESS RESPONSE
        // ==========================================
        $redirect_url = "";

        if ($action === 'save_draft') {
            $redirect_url = "";
        } elseif ($action === 'save_next') {
            $nextStep = $current_step + 1;
            // Redirect back to dashboard where the modal actually lives
            $redirect_url = "dashboard.php?step=$nextStep&id=$vehicle_id";
        } elseif ($action === 'finish') {
            $redirect_url = "dashboard.php";
        }

        $response['status'] = 'success';
        $response['id'] = $vehicle_id;
        $response['message'] = 'Data Saved Successfully';
        $response['redirect_url'] = $redirect_url;

        // CLEAN BUFFER AND SEND SUCCESS
        ob_clean();
        echo json_encode($response);
        exit; // EXIT HERE SO NO HTML RUNS

    } catch (Exception $e) {
        // ==========================================
        // 6. ERROR RESPONSE
        // ==========================================
        ob_clean();
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit; // EXIT HERE
    }
}
?>