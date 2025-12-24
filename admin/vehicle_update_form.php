<?php
// 1. Silent Error Handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');
require_once 'db.php';

$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    // 2. GET ID FROM FORM DATA
    $id = isset($_POST['vehicle_id']) ? intval($_POST['vehicle_id']) : 0;

    // 3. STRICT GUARD: If ID is missing, STOP. Do not create new data.
    if ($id <= 0) {
        throw new Exception("CRITICAL ERROR: Vehicle ID is missing. Cannot update.");
    }

    $currentStep = isset($_POST['step_number']) ? intval($_POST['step_number']) : 1;

    // --- Helper Functions ---
    function clean($key)
    {
        global $conn;
        return isset($_POST[$key]) ? $conn->real_escape_string(trim($_POST[$key])) : '';
    }

    function check($key)
    {
        return isset($_POST[$key]) ? 1 : 0;
    }

    function ensureRowExists($table, $vid)
    {
        global $conn;
        $check = $conn->query("SELECT id FROM $table WHERE vehicle_id = $vid");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO $table (vehicle_id) VALUES ($vid)");
        }
    }

    function handleUpload($inputName, $existingFile)
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== 0) {
            return $existingFile;
        }
        $targetDir = "../images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
        if (in_array($ext, $allowed)) {
            $newName = $inputName . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetDir . $newName)) {
                if (!empty($existingFile) && $existingFile !== 'default.jpg' && file_exists($targetDir . $existingFile)) {
                    @unlink($targetDir . $existingFile);
                }
                return $newName;
            }
        }
        return $existingFile;
    }

    // Fetch existing data for file logic
    $sql_fetch = "SELECT * FROM vehicle 
                  LEFT JOIN vehicle_seller ON vehicle.id = vehicle_seller.vehicle_id 
                  LEFT JOIN vehicle_purchaser ON vehicle.id = vehicle_purchaser.vehicle_id 
                  WHERE vehicle.id = $id";
    $res = $conn->query($sql_fetch);
    $row = ($res->num_rows > 0) ? $res->fetch_assoc() : [];

    // --- SWITCH STATEMENT (UPDATES ONLY) ---
    switch ($currentStep) {
        case 1:
            $p1 = handleUpload('photo1', $row['photo1'] ?? '');
            $p2 = handleUpload('photo2', $row['photo2'] ?? '');
            $p3 = handleUpload('photo3', $row['photo3'] ?? '');
            $p4 = handleUpload('photo4', $row['photo4'] ?? '');

            $sql = "UPDATE vehicle SET 
                vehicle_type = '" . clean('vehicle_type') . "', name = '" . clean('name') . "', 
                vehicle_number = '" . clean('vehicle_number') . "', register_date = '" . clean('register_date') . "', 
                owner_serial = '" . clean('owner_serial') . "', chassis_number = '" . clean('chassis_number') . "', 
                engine_number = '" . clean('engine_number') . "', payment_type = '" . clean('payment_type') . "', 
                cash_price = '" . clean('cash_price') . "', online_method = '" . clean('online_method') . "', 
                online_transaction_id = '" . clean('online_transaction_id') . "', online_price = '" . clean('online_price') . "', 
                police_challan = '" . clean('police_challan') . "',
                challan1_number = '" . clean('challan1_number') . "', challan1_amount = '" . clean('challan1_amount') . "', challan1_status = '" . clean('challan1_status') . "',
                challan2_number = '" . clean('challan2_number') . "', challan2_amount = '" . clean('challan2_amount') . "', challan2_status = '" . clean('challan2_status') . "',
                challan3_number = '" . clean('challan3_number') . "', challan3_amount = '" . clean('challan3_amount') . "', challan3_status = '" . clean('challan3_status') . "',
                sold_out = '" . check('sold_out') . "',
                photo1 = '$p1', photo2 = '$p2', photo3 = '$p3', photo4 = '$p4'
                WHERE id = $id";

            if (!$conn->query($sql)) throw new Exception("Step 1 Error: " . $conn->error);
            break;

        case 2:
            ensureRowExists('vehicle_seller', $id);
            $d_af = handleUpload('doc_aadhar_front', $row['doc_aadhar_front'] ?? '');
            $d_ab = handleUpload('doc_aadhar_back', $row['doc_aadhar_back'] ?? '');
            $d_vf = handleUpload('doc_voter_front', $row['doc_voter_front'] ?? '');
            $d_vb = handleUpload('doc_voter_back', $row['doc_voter_back'] ?? '');
            $rc_f = handleUpload('rc_front', $row['rc_front'] ?? '');
            $rc_b = handleUpload('rc_back', $row['rc_back'] ?? '');
            $noc_f = handleUpload('noc_front', $row['noc_front'] ?? '');
            $noc_b = handleUpload('noc_back', $row['noc_back'] ?? '');

            $sql = "UPDATE vehicle_seller SET 
                seller_date='" . clean('seller_date') . "', seller_vehicle_number='" . clean('seller_vehicle_number') . "', 
                seller_bike_name='" . clean('seller_bike_name') . "', seller_chassis_no='" . clean('seller_chassis_no') . "', 
                seller_engine_no='" . clean('seller_engine_no') . "', seller_name='" . clean('seller_name') . "', 
                seller_address='" . clean('seller_address') . "', seller_mobile1='" . clean('seller_mobile1') . "', 
                seller_mobile2='" . clean('seller_mobile2') . "', seller_mobile3='" . clean('seller_mobile3') . "',
                pr_rc='" . check('pr_rc') . "', pr_tax='" . check('pr_tax') . "', pr_insurance='" . check('pr_insurance') . "', 
                pr_pucc='" . check('pr_pucc') . "', pr_noc='" . check('pr_noc') . "', noc_status='" . clean('noc_status') . "',
                seller_payment_type='" . clean('seller_payment_type') . "', seller_cash_price='" . clean('seller_cash_price') . "',
                seller_online_method='" . clean('seller_online_method') . "', seller_online_transaction_id='" . clean('seller_online_transaction_id') . "',
                seller_online_price='" . clean('seller_online_price') . "', exchange_showroom_name='" . clean('exchange_showroom_name') . "',
                staff_name='" . clean('staff_name') . "', total_amount='" . clean('total_amount') . "', 
                paid_amount='" . clean('paid_amount') . "', due_amount='" . clean('due_amount') . "', due_reason='" . clean('due_reason') . "',
                doc_aadhar_front='$d_af', doc_aadhar_back='$d_ab', doc_voter_front='$d_vf', doc_voter_back='$d_vb',
                rc_front='$rc_f', rc_back='$rc_b', noc_front='$noc_f', noc_back='$noc_b'
                WHERE vehicle_id = $id";

            if (!$conn->query($sql)) throw new Exception("Step 2 Error: " . $conn->error);
            break;

        case 3:
            ensureRowExists('vehicle_purchaser', $id);
            $p_af = handleUpload('purchaser_doc_aadhar_front', $row['purchaser_doc_aadhar_front'] ?? '');
            $p_ab = handleUpload('purchaser_doc_aadhar_back', $row['purchaser_doc_aadhar_back'] ?? '');
            $p_vf = handleUpload('purchaser_doc_voter_front', $row['purchaser_doc_voter_front'] ?? '');
            $p_vb = handleUpload('purchaser_doc_voter_back', $row['purchaser_doc_voter_back'] ?? '');

            $sql = "UPDATE vehicle_purchaser SET 
                purchaser_date='" . clean('purchaser_date') . "', purchaser_name='" . clean('purchaser_name') . "', 
                purchaser_address='" . clean('purchaser_address') . "', purchaser_bike_name='" . clean('purchaser_bike_name') . "', 
                purchaser_vehicle_no='" . clean('purchaser_vehicle_no') . "', purchaser_transfer_amount='" . clean('purchaser_transfer_amount') . "', 
                purchaser_transfer_date='" . clean('purchaser_transfer_date') . "', purchaser_transfer_status='" . clean('purchaser_transfer_status') . "',
                purchaser_hpa_amount='" . clean('purchaser_hpa_amount') . "', purchaser_hpa_date='" . clean('purchaser_hpa_date') . "', 
                purchaser_hpa_status='" . clean('purchaser_hpa_status') . "', purchaser_hp_amount='" . clean('purchaser_hp_amount') . "', 
                purchaser_hp_date='" . clean('purchaser_hp_date') . "', purchaser_hp_status='" . clean('purchaser_hp_status') . "',
                purchaser_insurance_name='" . clean('purchaser_insurance_name') . "', purchaser_insurance_payment_status='" . clean('purchaser_insurance_payment_status') . "',
                purchaser_insurance_amount='" . clean('purchaser_insurance_amount') . "', purchaser_insurance_issue_date='" . clean('purchaser_insurance_issue_date') . "',
                purchaser_insurance_expiry_date='" . clean('purchaser_insurance_expiry_date') . "', purchaser_total='" . clean('purchaser_total') . "', 
                purchaser_paid='" . clean('purchaser_paid') . "', purchaser_due='" . clean('purchaser_due') . "', 
                purchaser_payment_mode='" . clean('purchaser_payment_mode') . "', purchaser_cash_amount='" . clean('purchaser_cash_amount') . "',
                purchaser_cash_mobile1='" . clean('purchaser_cash_mobile1') . "', purchaser_cash_mobile2='" . clean('purchaser_cash_mobile2') . "', 
                purchaser_cash_mobile3='" . clean('purchaser_cash_mobile3') . "', purchaser_fin_hpa_with='" . clean('purchaser_fin_hpa_with') . "', 
                purchaser_fin_disburse_amount='" . clean('purchaser_fin_disburse_amount') . "', purchaser_fin_disburse_status='" . clean('purchaser_fin_disburse_status') . "', 
                purchaser_fin_mobile1='" . clean('purchaser_fin_mobile1') . "', purchaser_fin_mobile2='" . clean('purchaser_fin_mobile2') . "', 
                purchaser_fin_mobile3='" . clean('purchaser_fin_mobile3') . "', purchaser_payment_all_paid='" . check('purchaser_payment_all_paid') . "',
                purchaser_doc_aadhar_front='$p_af', purchaser_doc_aadhar_back='$p_ab',
                purchaser_doc_voter_front='$p_vf', purchaser_doc_voter_back='$p_vb'
                WHERE vehicle_id = $id";

            if (!$conn->query($sql)) throw new Exception("Step 3 Error: " . $conn->error);
            break;

        case 4:
            ensureRowExists('vehicle_ot', $id);
            $sql = "UPDATE vehicle_ot SET 
                ot_name_transfer='" . clean('ot_name_transfer') . "', ot_vehicle_number='" . clean('ot_vehicle_number') . "', 
                ot_rto_name='" . clean('ot_rto_name') . "', ot_vendor_name='" . clean('ot_vendor_name') . "', 
                ot_transfer_amount='" . clean('ot_transfer_amount') . "', ot_transfer_date='" . clean('ot_transfer_date') . "', 
                ot_transfer_status='" . clean('ot_transfer_status') . "', ot_hpa_amount='" . clean('ot_hpa_amount') . "', 
                ot_hpa_date='" . clean('ot_hpa_date') . "', ot_hpa_status='" . clean('ot_hpa_status') . "', 
                ot_hp_amount='" . clean('ot_hp_amount') . "', ot_hp_date='" . clean('ot_hp_date') . "', ot_hp_status='" . clean('ot_hp_status') . "',
                ot_insurance_name='" . clean('ot_insurance_name') . "', ot_insurance_payment_status='" . clean('ot_insurance_payment_status') . "', 
                ot_insurance_amount='" . clean('ot_insurance_amount') . "', ot_insurance_start_date='" . clean('ot_insurance_start_date') . "', 
                ot_insurance_end_date='" . clean('ot_insurance_end_date') . "', ot_purchaser_sign_status='" . clean('ot_purchaser_sign_status') . "', 
                ot_purchaser_sign_date='" . clean('ot_purchaser_sign_date') . "', ot_seller_sign_status='" . clean('ot_seller_sign_status') . "', 
                ot_seller_sign_date='" . clean('ot_seller_sign_date') . "'
                WHERE vehicle_id = $id";

            if (!$conn->query($sql)) throw new Exception("Step 4 Error: " . $conn->error);
            break;

        default:
            throw new Exception("Invalid Step Number: " . $currentStep);
    }

    $response['status'] = 'success';
    $response['message'] = 'Saved successfully!';
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

ob_clean();
echo json_encode($response);
exit;
