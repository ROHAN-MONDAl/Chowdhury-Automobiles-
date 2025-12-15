 <form action="vehicle_form.php" id="dealForm" method="POST" class="app-form"
                        enctype="multipart/form-data" novalidate>

                        <input type="hidden" name="row_id" value="<?php echo $row_id; ?>">

                        <input type="hidden" name="current_step" value="<?php echo $current_step; ?>">


                        <div id="step-1" class="wizard-step fade-in-animation">
                            <div class="card steps-id p-4 border-0 shadow-sm position-relative sold-wrapper rounded-4">
                                <div class="sold-stamp">SOLD OUT</div>
                                <div class="sold-overlay"></div>
                                <div>
                                    <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Vehicle Details</h6>
                                    <label class="mb-2">Vehicle Photos</label>
                                    <div class="row g-3 mb-4">
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo1" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo2" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo3" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo4" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label for="vehicleType" class="form-label">Vehicle Type</label>
                                            <select id="vehicleType" name="vehicle_type" class="form-select fw-bold">
                                                <option selected disabled>Choose Vehicle Type</option>
                                                <option>Scooters</option>
                                                <option>Mopeds</option>
                                                <option>Dirt / Off-road Bikes</option>
                                                <option>Electric Bikes</option>
                                                <option>Cruiser Bikes</option>
                                                <option>Sport Bikes</option>
                                                <option>Touring Bikes</option>
                                                <option>Adventure / Dual-Sport Bikes</option>
                                                <option>Naked / Standard Bikes</option>
                                                <option>Cafe Racers</option>
                                                <option>Bobbers</option>
                                                <option>Choppers</option>
                                                <option>Pocket Bikes / Mini Bikes</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="fw-bold">Name</label>
                                            <input type="text" id="nameField" name="name" class="form-control" placeholder="Enter Name">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Vehicle Number</label>
                                            <input type="text" name="vehicle_number" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="WB ">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Register Date</label>
                                            <input type="date" name="register_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Owner Serial</label>
                                            <select name="owner_serial" class="form-select">
                                                <option>1st</option>
                                                <option>2nd</option>
                                                <option>3rd</option>
                                                <option>4th</option>
                                                <option>5th</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Chassis Number</label>
                                            <input type="text" name="chassis_number" class="form-control text-uppercase">
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label>Engine Number</label>
                                            <input type="text" name="engine_number" class="form-control text-uppercase">
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="fw-bold mb-2">Payment Type</label>
                                            <div class="d-flex gap-2 mb-3">
                                                <input type="radio" class="btn-check" name="payment_type" id="sp_cash" value="Cash" checked data-bs-toggle="collapse" data-bs-target="#cashBox" aria-controls="cashBox">
                                                <label class="btn btn-outline-success" for="sp_cash">Cash</label>

                                                <input type="radio" class="btn-check" name="payment_type" id="sp_online" value="Online" data-bs-toggle="collapse" data-bs-target="#onlineBox" aria-controls="onlineBox">
                                                <label class="btn btn-outline-primary" for="sp_online">Online</label>
                                            </div>

                                            <div id="payBoxes">
                                                <div id="cashBox" class="collapse show" data-bs-parent="#payBoxes">
                                                    <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                        <label class="fw-bold small mb-1">Bike Price</label>
                                                        <input type="number" name="cash_price" class="form-control form-control-sm mb-3" placeholder="Enter Amount">
                                                    </div>
                                                </div>

                                                <div id="onlineBox" class="collapse" data-bs-parent="#payBoxes">
                                                    <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                        <label class="fw-bold small mb-2">Select Online Method</label>
                                                        <div class="d-flex flex-wrap gap-3 mb-2">
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input" name="online_method" id="om_gpay" value="Google Pay">
                                                                <label class="form-check-label small fw-bold" for="om_gpay">Google Pay</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input" name="online_method" id="om_paytm" value="Paytm">
                                                                <label class="form-check-label small fw-bold" for="om_paytm">Paytm</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input" name="online_method" id="om_phonepe" value="PhonePe">
                                                                <label class="form-check-label small fw-bold" for="om_phonepe">PhonePe</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input" name="online_method" id="om_bharatpe" value="BharatPe">
                                                                <label class="form-check-label small fw-bold" for="om_bharatpe">BharatPe</label>
                                                            </div>
                                                        </div>

                                                        <input type="text" name="online_transaction_id" class="form-control form-control-sm mb-3 text-uppercase" placeholder="Transaction / UPI Reference ID">

                                                        <label class="fw-bold small mb-1">Bike Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white border-end-0">₹</span>
                                                            <input type="number" name="online_price" class="form-control border-start-0 ps-0" placeholder="Enter Price">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-4 bg-light border">
                                        <label>Police Challan</label>
                                        <div class="d-flex gap-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="police_challan" value="No" checked data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                <label class="form-check-label fw-bold">No</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="police_challan" value="Yes" data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                <label class="form-check-label fw-bold">Yes</label>
                                            </div>
                                        </div>

                                        <div class="collapse mt-3" id="challan-section">
                                            <div class="border rounded p-2 mb-2 bg-white">
                                                <label class="fw-bold small">Challan 1</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="challan1_number" class="form-control text-uppercase" placeholder="Challan Number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="challan1_amount" class="form-control" placeholder="Amount">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="btn-group w-100 btn-group-sm">
                                                            <input type="radio" class="btn-check" name="challan1_status" id="pen1" value="Pending" checked>
                                                            <label class="btn btn-outline-danger" for="pen1">Pending</label>

                                                            <input type="radio" class="btn-check" name="challan1_status" id="paid1" value="Paid">
                                                            <label class="btn btn-outline-success" for="paid1">Paid</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="border rounded p-2 mb-2 bg-white">
                                                <label class="fw-bold small">Challan 2</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="challan2_number" class="form-control text-uppercase" placeholder="Challan Number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="challan2_amount" class="form-control" placeholder="Amount">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="btn-group w-100 btn-group-sm">
                                                            <input type="radio" class="btn-check" name="challan2_status" id="pen2" value="Pending" checked>
                                                            <label class="btn btn-outline-danger" for="pen2">Pending</label>

                                                            <input type="radio" class="btn-check" name="challan2_status" id="paid2" value="Paid">
                                                            <label class="btn btn-outline-success" for="paid2">Paid</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="border rounded p-2 mb-2 bg-white">
                                                <label class="fw-bold small">Challan 3</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="challan3_number" class="form-control text-uppercase" placeholder="Challan Number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="challan3_amount" class="form-control" placeholder="Amount">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="btn-group w-100 btn-group-sm">
                                                            <input type="radio" class="btn-check" name="challan3_status" id="pen3" value="Pending" checked>
                                                            <label class="btn btn-outline-danger" for="pen3">Pending</label>

                                                            <input type="radio" class="btn-check" name="challan3_status" id="paid3" value="Paid">
                                                            <label class="btn btn-outline-success" for="paid3">Paid</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                                        <label class="form-check-label fw-bold text-danger mb-0" for="soldToggle">Mark as Sold Out</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="soldToggle" name="sold_out" value="1" style="width: 3em; height: 1.5em;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step-2" class="wizard-step d-none">
                            <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Seller Details</h6>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-4">
                                        <label>Date</label>
                                        <input type="date" name="seller_date" class="form-control">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Vehicle No</label>
                                        <input type="text" name="seller_vehicle_number"
                                            class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000"
                                            value="WB ">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Bike Name</label>
                                        <input type="text" name="seller_bike_name" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label>Chassis No</label>
                                        <input type="text" name="seller_chassis_no" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label>Engine No</label>
                                        <input type="text" name="seller_engine_no" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12">
                                        <label>Seller Name</label>
                                        <input type="text" name="seller_name" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12">
                                        <label>Address</label>
                                        <textarea name="seller_address" class="form-control text-uppercase" rows="2"></textarea>
                                    </div>
                                </div>

                                <label class="mb-2">Mobile Numbers</label>
                                <div class="row g-2 mb-3">
                                    <div class="col-12"><input type="tel" name="seller_mobile1" class="form-control" placeholder="Mob 1"></div>
                                    <div class="col-12"><input type="tel" name="seller_mobile2" class="form-control" placeholder="Mob 2"></div>
                                    <div class="col-12"><input type="tel" name="seller_mobile3" class="form-control" placeholder="Mob 3"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2">Purchaser Documents (In Seller Step)</label>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Front</span>
                                                <img src="">
                                                <input type="file" name="doc_aadhar_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Back</span>
                                                <img src="">
                                                <input type="file" name="doc_aadhar_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Front</span>
                                                <img src="">
                                                <input type="file" name="doc_voter_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Back</span>
                                                <img src="">
                                                <input type="file" name="doc_voter_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-4 border mb-3">
                                    <label class="mb-2 fw-bold">Papers Received</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="pr_rc" class="form-check-input" id="pr_rc" data-bs-toggle="collapse" data-bs-target="#rcUploadBox">
                                            <label class="fw-bold" for="pr_rc">RC</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="pr_tax" class="form-check-input" id="pr_tax">
                                            <label class="fw-bold" for="pr_tax">Tax Token</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="pr_insurance" class="form-check-input" id="pr_ins">
                                            <label class="fw-bold" for="pr_ins">Insurance</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="pr_pucc" class="form-check-input" id="pr_puc">
                                            <label class="fw-bold" for="pr_puc">PUCC</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="pr_noc" class="form-check-input" id="pr_noc" data-bs-toggle="collapse" data-bs-target="#nocUploadBox">
                                            <label class="fw-bold" for="pr_noc">NOC</label>
                                        </div>
                                    </div>

                                    <div class="collapse mt-3" id="rcUploadBox">
                                        <label class="fw-bold small">RC Upload</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="border rounded p-2 text-center bg-white">
                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">RC FRONT</small>
                                                    <input type="file" name="rc_front" class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-2 text-center bg-white">
                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">RC BACK</small>
                                                    <input type="file" name="rc_back" class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="collapse mt-3" id="nocUploadBox">
                                        <label class="fw-bold small">NOC Status</label>
                                        <div class="d-flex justify-content-center">
                                            <div class="btn-group w-75 btn-group-sm mb-3 mx-auto" role="group">
                                                <input type="radio" name="noc_status" class="btn-check" id="noc_paid" value="paid" checked>
                                                <label class="btn btn-outline-success" for="noc_paid">Paid</label>

                                                <input type="radio" name="noc_status" class="btn-check" id="noc_due" value="due">
                                                <label class="btn btn-outline-danger" for="noc_due">Due</label>
                                            </div>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="border rounded small-box text-center p-2">
                                                    <span class="small text-muted fw-bold">NOC Front</span>
                                                    <input type="file" name="noc_front" class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded small-box text-center p-2">
                                                    <span class="small text-muted fw-bold">NOC Back</span>
                                                    <input type="file" name="noc_back" class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label class="fw-bold mb-2">Payment Type</label>
                                        <div class="d-flex gap-2 mb-3">
                                            <input type="radio" name="seller_payment_type" class="btn-check" id="pay_cash" value="cash" checked data-bs-toggle="collapse" data-bs-target="#cashBox">
                                            <label class="btn btn-outline-success" for="pay_cash">Cash</label>

                                            <input type="radio" name="seller_payment_type" class="btn-check" id="pay_online" value="online" data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                            <label class="btn btn-outline-primary" for="pay_online">Online</label>
                                        </div>

                                        <div id="payAccordion">
                                            <div id="cashBox" class="collapse show" data-bs-parent="#payAccordion">
                                                <div class="p-3 bg-white border rounded shadow-sm">
                                                    <label class="fw-bold small mb-1">Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0">₹</span>
                                                        <input type="number" name="seller_cash_price" class="form-control border-start-0 ps-0" placeholder="Enter Price">
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="onlineBox" class="collapse" data-bs-parent="#payAccordion">
                                                <div class="p-3 bg-white border rounded shadow-sm">
                                                    <label class="fw-bold small mb-2">Online Method</label>
                                                    <div class="d-flex flex-wrap gap-3 mb-3">
                                                        <label class="form-check">
                                                            <input type="radio" name="seller_online_method" class="form-check-input" value="gpay">
                                                            <span class="form-check-label fw-bold">Google Pay</span>
                                                        </label>
                                                        <label class="form-check">
                                                            <input type="radio" name="seller_online_method" class="form-check-input" value="paytm">
                                                            <span class="form-check-label fw-bold">Paytm</span>
                                                        </label>
                                                        <label class="form-check">
                                                            <input type="radio" name="seller_online_method" class="form-check-input" value="phonepe">
                                                            <span class="form-check-label fw-bold">PhonePe</span>
                                                        </label>
                                                        <label class="form-check">
                                                            <input type="radio" name="seller_online_method" class="form-check-input" value="bharatpe">
                                                            <span class="form-check-label fw-bold">BharatPe</span>
                                                        </label>
                                                    </div>

                                                    <input type="text" name="seller_online_transaction_id" class="form-control form-control-sm mb-3 text-uppercase" placeholder="Transaction / UPI Reference ID">

                                                    <label class="fw-bold small mb-1">Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0">₹</span>
                                                        <input type="number" name="seller_online_price" class="form-control border-start-0 ps-0" placeholder="Enter Price">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label>Exchange Showroom Name</label>
                                        <input type="text" name="exchange_showroom_name" class="form-control text-uppercase" placeholder="Showroom Name">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Staff Name</label>
                                        <input type="text" name="staff_name" class="form-control text-uppercase" placeholder="Staff Name">
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-4 border">
                                    <label class="text-primary">Payment Calculation</label>
                                    <div class="row g-2">
                                        <div class="col-12"><input type="number" name="total_amount" class="form-control" placeholder="Total" id="s_total"></div>
                                        <div class="col-12"><input type="number" name="paid_amount" class="form-control" placeholder="Paid" id="s_paid"></div>
                                        <div class="col-12"><input type="number" name="due_amount" class="form-control bg-white fw-bold text-danger" placeholder="Due" id="s_due" readonly></div>
                                        <div class="col-12"><input type="text" name="due_reason" class="form-control d-none mt-1" id="s_due_reason" placeholder="Reason for due amount..."></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step-3" class="wizard-step d-none">
                            <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Purchaser Details</h6>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label>Date</label>
                                        <input type="date" name="purchaser_date" class="form-control" value="2025-11-26">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Purchaser Name</label>
                                        <input type="text" name="purchaser_name" class="form-control text-uppercase">
                                    </div>
                                    <div class="col-12">
                                        <label>Address</label>
                                        <textarea name="purchaser_address" class="form-control text-uppercase" rows="2"></textarea>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Bike Name</label>
                                        <input type="text" name="purchaser_bike_name" class="form-control text-uppercase">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Vehicle No</label>
                                        <input type="text" name="purchaser_vehicle_no" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="WB ">
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-4 border mb-4">
                                    <label class="text-dark mb-3 d-block">Purchaser Paper Payment Fees</label>

                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>Transfer Amount</label>
                                            <input type="number" name="purchaser_transfer_amount" class="form-control" placeholder="Amount">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="purchaser_transfer_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Status</label>
                                            <select name="purchaser_transfer_status" class="form-select">
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>HPA</label>
                                            <input type="number" name="purchaser_hpa_amount" class="form-control" placeholder="Amount">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="purchaser_hpa_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Status</label>
                                            <select name="purchaser_hpa_status" class="form-select">
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>HP</label>
                                            <input type="number" name="purchaser_hp_amount" class="form-control" placeholder="Amount">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="purchaser_hp_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Status</label>
                                            <select name="purchaser_hp_status" class="form-select">
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-3">
                                            <label class="fw-bold">Insurance Name</label>
                                            <select name="purchaser_insurance_name" class="form-control text-uppercase" required>
                                                <option value="">-- Select Insurance --</option>
                                                <option value="Tata AIG Insurance">Tata AIG Insurance</option>
                                                <option value="Bharti AXA">Bharti AXA</option>
                                                <option value="Bajaj Allianz">Bajaj Allianz</option>
                                                <option value="ICICI Lombard">ICICI Lombard</option>
                                                <option value="IFFCO Tokio">IFFCO Tokio</option>
                                                <option value="National Insurance">National Insurance</option>
                                                <option value="New India Assurance">New India Assurance</option>
                                                <option value="Oriental Insurance">Oriental Insurance</option>
                                                <option value="United India Insurance">United India Insurance</option>
                                                <option value="Reliance General Insurance">Reliance General Insurance</option>
                                                <option value="Royal Sundaram Insurance">Royal Sundaram Insurance</option>
                                                <option value="Chola MS Insurance">Chola MS Insurance</option>
                                                <option value="HDFC ERGO">HDFC ERGO</option>
                                                <option value="ECGC">ECGC</option>
                                                <option value="Agriculture Insurance Company of India (AIC)">Agriculture Insurance Company of India (AIC)</option>
                                                <option value="Star Health Insurance">Star Health Insurance</option>
                                                <option value="Future Generali">Future Generali</option>
                                                <option value="Universal Sompo">Universal Sompo</option>
                                                <option value="Shriram General Insurance">Shriram General Insurance</option>
                                                <option value="Raheja QBE">Raheja QBE</option>
                                                <option value="SBI General Insurance">SBI General Insurance</option>
                                                <option value="Niva Bupa Health Insurance">Niva Bupa Health Insurance</option>
                                                <option value="L&T Insurance">L&T Insurance</option>
                                                <option value="Care Health Insurance">Care Health Insurance</option>
                                                <option value="Magma HDI">Magma HDI</option>
                                                <option value="Liberty General Insurance">Liberty General Insurance</option>
                                                <option value="Manipal Cigna">Manipal Cigna</option>
                                                <option value="Kotak General Insurance">Kotak General Insurance</option>
                                                <option value="Aditya Birla Capital Health Insurance">Aditya Birla Capital Health Insurance</option>
                                                <option value="Digit Insurance">Digit Insurance</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Payment Status</label>
                                            <select name="purchaser_insurance_payment_status" class="form-control" required>
                                                <option value="">-- Select Status --</option>
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Amount</label>
                                            <input type="number" name="purchaser_insurance_amount" class="form-control" placeholder="Enter Amount" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Issue Date</label>
                                            <input type="date" name="purchaser_insurance_issue_date" class="form-control" id="issueDate" value="2025-11-26" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Expiry Date</label>
                                            <input type="date" name="purchaser_insurance_expiry_date" class="form-control" id="expiryDate" readonly required>
                                        </div>

                                        <span class="fw-bold text-primary">Validity:<span id="expiryText">--</span></span>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-4 border mb-3">
                                    <label class="mb-2">Price Breakdown</label>
                                    <div class="row g-2 mb-3">
                                        <div class="col-12">
                                            <input type="number" name="purchaser_total" id="p_total" class="form-control" placeholder="Total">
                                        </div>
                                        <div class="col-12">
                                            <input type="number" name="purchaser_paid" id="p_paid" class="form-control" placeholder="Paid">
                                        </div>
                                        <div class="col-12">
                                            <input type="number" name="purchaser_due" id="p_due" class="form-control bg-white" placeholder="Due" readonly>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="purchaser_payment_mode" id="rad_cash" value="cash" checked>
                                            <label class="fw-bold" for="rad_cash" data-bs-toggle="collapse" data-bs-target="#sec_cash" role="button">
                                                Cash
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="purchaser_payment_mode" id="rad_fin" value="finance">
                                            <label class="fw-bold" for="rad_fin" data-bs-toggle="collapse" data-bs-target="#sec_finance" role="button">
                                                Finance
                                            </label>
                                        </div>
                                    </div>

                                    <div id="payment_details_group">
                                        <div id="sec_cash" class="collapse show border-top pt-2 mt-2" data-bs-parent="#payment_details_group">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label>Amount</label>
                                                    <input type="number" name="purchaser_cash_amount" class="form-control" placeholder="Enter Amount">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 1</label>
                                                    <input type="tel" name="purchaser_cash_mobile1" class="form-control" placeholder="Enter Mobile Number">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 2</label>
                                                    <input type="tel" name="purchaser_cash_mobile2" class="form-control" placeholder="Enter Mobile Number">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 3</label>
                                                    <input type="tel" name="purchaser_cash_mobile3" class="form-control" placeholder="Enter Mobile Number">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="sec_finance" class="collapse border-top pt-2 mt-2" data-bs-parent="#payment_details_group">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label>HPA With</label>
                                                    <input type="text" name="purchaser_fin_hpa_with" class="form-control text-uppercase" placeholder="Finance Company">
                                                </div>
                                                <div class="col-12">
                                                    <label>Disburse Amount</label>
                                                    <div class="input-group">
                                                        <input type="number" name="purchaser_fin_disburse_amount" class="form-control" placeholder="Amt">
                                                        <select name="purchaser_fin_disburse_status" class="form-select" style="max-width:100px;">
                                                            <option value="paid">Paid</option>
                                                            <option value="due">Due</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 1</label>
                                                    <input type="tel" name="purchaser_fin_mobile1" class="form-control" placeholder="Mobile 1">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 2</label>
                                                    <input type="tel" name="purchaser_fin_mobile2" class="form-control" placeholder="Mobile 2">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 3</label>
                                                    <input type="tel" name="purchaser_fin_mobile3" class="form-control" placeholder="Mobile 3">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2">Purchaser Documents</label>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Front</span>
                                                <img src="">
                                                <input type="file" name="purchaser_doc_aadhar_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Back</span>
                                                <img src="">
                                                <input type="file" name="purchaser_doc_aadhar_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Front</span>
                                                <img src="">
                                                <input type="file" name="purchaser_doc_voter_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Back</span>
                                                <img src="">
                                                <input type="file" name="purchaser_doc_voter_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="purchaser_payment_all_paid" id="all_paid">
                                    <label class="form-check-label fw-bold text-success" for="all_paid">Payment All Paid</label>
                                </div>
                            </div>
                        </div>

                        <div id="step-4" class="wizard-step d-none">
                            <div class="card steps-id border-0 shadow-sm rounded-4">
                                <h6 class="fw-bold text-primary m-4 mb-3 text-uppercase ls-1">Ownership Transfer</h6>

                                <div class="p-3 border rounded-4 mb-4">
                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>Name Transfer</label>
                                            <input type="text" class="form-control text-uppercase" placeholder="Enter Name" name="ot_name_transfer">
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label>Vehicle Number</label>
                                            <input type="text" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="WB " name="ot_vehicle_number">
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label>RTO Name</label>
                                            <select class="form-select" name="ot_rto_name">
                                                <option>Bankura</option>
                                                <option>Bishnupur</option>
                                                <option>Durgapur</option>
                                                <option>Manbazar</option>
                                                <option>Suri</option>
                                                <option>Asansol</option>
                                                <option>Kalimpong</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-12 col-md-6">
                                            <label>Vendor Name</label>
                                            <input type="text" class="form-control text-uppercase" placeholder="Vendor Name" name="ot_vendor_name">
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-4 border mb-4">
                                        <label class="text-dark mb-3 d-block">Vendor Payment</label>

                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>Transfer Amount</label>
                                                <input type="number" class="form-control" placeholder="Amount" name="ot_transfer_amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" class="form-control" value="2025-11-26" name="ot_transfer_date">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Status</label>
                                                <select class="form-select" name="ot_transfer_status">
                                                    <option>Paid</option>
                                                    <option>Due</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>HPA</label>
                                                <input type="number" class="form-control" placeholder="Amount" name="ot_hpa_amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" class="form-control" value="2025-11-26" name="ot_hpa_date">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Status</label>
                                                <select class="form-select" name="ot_hpa_status">
                                                    <option>Paid</option>
                                                    <option>Due</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>HP</label>
                                                <input type="number" class="form-control" placeholder="Amount" name="ot_hp_amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" class="form-control" value="2025-11-26" name="ot_hp_date">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Status</label>
                                                <select class="form-select" name="ot_hp_status">
                                                    <option>Paid</option>
                                                    <option>Due</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label class="fw-bold">Insurance Name</label>
                                                <select class="form-control text-uppercase" required name="ot_insurance_name">
                                                    <option value="">-- Select Insurance --</option>
                                                    <option value="Tata AIG Insurance">Tata AIG Insurance</option>
                                                    <option value="Bharti AXA">Bharti AXA</option>
                                                    <option value="Bajaj Allianz">Bajaj Allianz</option>
                                                    <option value="ICICI Lombard">ICICI Lombard</option>
                                                    <option value="IFFCO Tokio">IFFCO Tokio</option>
                                                    <option value="National Insurance">National Insurance</option>
                                                    <option value="New India Assurance">New India Assurance</option>
                                                    <option value="Oriental Insurance">Oriental Insurance</option>
                                                    <option value="United India Insurance">United India Insurance</option>
                                                    <option value="Reliance General Insurance">Reliance General Insurance</option>
                                                    <option value="Royal Sundaram Insurance">Royal Sundaram Insurance</option>
                                                    <option value="Chola MS Insurance">Chola MS Insurance</option>
                                                    <option value="HDFC ERGO">HDFC ERGO</option>
                                                    <option value="ECGC">ECGC</option>
                                                    <option value="Agriculture Insurance Company of India (AIC)">Agriculture Insurance Company of India (AIC)</option>
                                                    <option value="Star Health Insurance">Star Health Insurance</option>
                                                    <option value="Future Generali">Future Generali</option>
                                                    <option value="Universal Sompo">Universal Sompo</option>
                                                    <option value="Shriram General Insurance">Shriram General Insurance</option>
                                                    <option value="Raheja QBE">Raheja QBE</option>
                                                    <option value="SBI General Insurance">SBI General Insurance</option>
                                                    <option value="Niva Bupa Health Insurance">Niva Bupa Health Insurance</option>
                                                    <option value="L&T Insurance">L&T Insurance</option>
                                                    <option value="Care Health Insurance">Care Health Insurance</option>
                                                    <option value="Magma HDI">Magma HDI</option>
                                                    <option value="Liberty General Insurance">Liberty General Insurance</option>
                                                    <option value="Manipal Cigna">Manipal Cigna</option>
                                                    <option value="Kotak General Insurance">Kotak General Insurance</option>
                                                    <option value="Aditya Birla Capital Health Insurance">Aditya Birla Capital Health Insurance</option>
                                                    <option value="Digit Insurance">Digit Insurance</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Payment Status</label>
                                                <select class="form-control" required name="ot_insurance_payment_status">
                                                    <option value="">-- Select Status --</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="due">Due</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Amount</label>
                                                <input type="number" class="form-control" placeholder="Enter Amount" required name="ot_insurance_amount">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Start Date</label>
                                                <input type="date" class="form-control" id="startDate" value="2025-11-26" required name="ot_insurance_start_date">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">End Date</label>
                                                <input type="date" class="form-control" id="endDate" readonly required name="ot_insurance_end_date">
                                            </div>

                                            <span class="fw-bold text-primary">Duration:<span id="durationText">--</span></span>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-12 col-md-6">
                                            <div class="border rounded-3">
                                                <label class="form-label fw-bold">Purchaser Sign</label>
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <label class="form-label small">Status</label>
                                                        <select class="form-select" name="ot_purchaser_sign_status">
                                                            <option>Yes</option>
                                                            <option>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Date</label>
                                                        <input type="date" class="form-control" value="2025-11-26" name="ot_purchaser_sign_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="border rounded-3">
                                                <label class="form-label fw-bold">Seller Sign</label>
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <label class="form-label small">Status</label>
                                                        <select class="form-select" name="ot_seller_sign_status">
                                                            <option>Yes</option>
                                                            <option>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Date</label>
                                                        <input type="date" class="form-control" value="2025-11-26" name="ot_seller_sign_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex align-items-center">
                            <?php if ($current_step < 4): ?>
                                <button type="submit" name="action" value="save_next" class="btn btn-success fw-bold shadow-sm px-4 ms-auto">
                                    <i class="ph-bold ph-floppy-disk me-1"></i> Save
                                </button>
                            <?php else: ?>
                                <button type="submit" name="action" value="finish" class="btn btn-success px-4 ms-auto text-white shadow-lg">
                                    Finish <i class="ph-bold ph-check-circle ms-1"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>