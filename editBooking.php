<?php

include 'dbConn.php';

// Initialize all variables with empty values
$sender = $senderMobile = $receiver = $receiverMobile = $fromPlace = $fromMobile = $toPlace = $toMobile = '';
$quantity = $quantityDetails = $quantityDescription = $paymentType = $totalAmount = $transporationCost = $loadingCost = $additionalCost = $goodsValue = $deliveryType = '';
$quantityDetails = '{}'; // Initialize as empty JSON object
$bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : null;

// Get branch names
$branchNameArr = array();
if (isset($conn)) {
    $branchNameQry = "SELECT DISTINCT LOWER(BRANCH_NAME) AS BRANCH_NAME FROM branch_details ORDER BY 1";
    $result = mysqli_query($conn, $branchNameQry);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $branchNameArr[] = $row['BRANCH_NAME'];
        }
    }
}

// Get items
$itemsArr = array();
if (isset($conn)) {
    $itemsQry = "SELECT ITEM_NAME FROM items ORDER BY 1";
    $result = mysqli_query($conn, $itemsQry);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $itemsArr[] = $row['ITEM_NAME'];
        }
    }
}

// Get booking details if bookingId is set
if ($bookingId && is_numeric($bookingId)) {
    $bookingId = intval($bookingId);
    $selectQuery = "SELECT * FROM booking_details WHERE BOOKING_ID = $bookingId";
    
    if ($result = mysqli_query($conn, $selectQuery)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $sender = $row['CUSTOMER'];
            $senderMobile = $row['MOBILE'];
            $receiver = $row['DELIVERY_TO'];
            $receiverMobile = $row['DELIVERY_MOBILE'];
            $fromPlace = $row['FROM_PLACE'];
            $fromMobile = $row['FROM_MOBILE'];
            $toPlace = $row['TO_PLACE'];
            $toMobile = $row['TO_MOBILE'];
            $quantity = $row['QUANTITY'];
            $quantityDetails = $row['QUANTITY_DETAILS'];
            $quantityDescription = $row['QTY_DESCRIPTION'];
            $paymentType = $row['PAYMENT_TYPE'];
            $totalAmount = $row['TOTAL_AMOUNT'];
            $transporationCost = $row['TRANSPORTATION_COST'];
            $loadingCost = $row['LOADING_COST'];
            $additionalCost = $row['ADDITIONAL_COST'];
            $goodsValue = $row['GOODS_VALUE'];
            $deliveryType = $row['DELIVERY_TYPE'];
        }
    }

    // Ensure quantityDetails is valid JSON
    if (!empty($quantityDetails)) {
        $quantityDetails = json_decode($quantityDetails, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $quantityDetails = array();
        }
    } else {
        $quantityDetails = array();
    }
    $quantityDetails = json_encode($quantityDetails);
}

// Escape all variables for JavaScript
function js_escape($str) {
    return addslashes(htmlspecialchars($str ?? '', ENT_QUOTES));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Management</title>
    <!-- Include your CSS files here -->
</head>

<style>
    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .m-t-3 {
        margin-top: 0.5em;
    }

    .w-100 {
        width: 100%
    }

    .mandatory-field {
        color: red;
    }
</style>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <?php include 'header.php'; ?>

        <!-- Content body -->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <section class="panel">
                                    <header class="panel-heading">
                                        BOOK NEW
                                        <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top: 0.75em;" onclick="goToAddCustomer()" data-toggle="tooltip" title="View Booking List">
                                            <i class="material-icons">visibility</i>
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm pull-right" style="margin-top: 0.75em; margin-right: 1em;" data-toggle="tooltip" title="Reload the page" onclick="window.location.reload();">
                                            <i class="fa fa-refresh fa-spin" style="font-size: 2em;"></i>
                                        </button>
                                    </header>

                                    <div class="panel-body">
                                        <div>
                                            <div class="row">
                                                <br>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="customer-mobile">Sender Mobile<span class="mandatory-field">*</span></label><br>
                                                        <select class="form-control" id="customer-mobile" name="customer-mobile" onchange="mobileNumberChanged(this)" required>
                                                            <option value="">-- SELECT MOBILE --</option>
                                                            <?php
                                                            $selectCity = "SELECT DISTINCT TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                                        $selected = ($row['MOBILE'] == $senderMobile) ? 'selected' : '';
                                                            ?>
                                                                        <option value="<?php echo htmlspecialchars($row['MOBILE']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($row['MOBILE']); ?></option>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="customer-name">Sender Name<span class="mandatory-field">*</span></label><br>
                                                        <select class="form-control" id="customer-name" name="customer-name" onchange="customerNameSelectChanged(this)">
                                                            <option value="">-- SELECT SENDER --</option>
                                                            <?php
                                                            $selectCity = "SELECT CUSTOMER_ID, TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                                        $selected = ($row['CUSTOMER_NAME'] == $sender) ? 'selected' : '';
                                                            ?>
                                                                        <option value="<?php echo htmlspecialchars($row['CUSTOMER_NAME']); ?>" <?php echo $selected; ?> data-mobile="<?php echo htmlspecialchars($row['MOBILE']); ?>"><?php echo htmlspecialchars($row['CUSTOMER_NAME']); ?></option>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="delivery-to">Receiver Name<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="delivery-to" placeholder="Enter Delivery Info" name="delivery-to" value="<?php echo htmlspecialchars($receiver); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="customer-mobile">Receiver Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="number" required class="form-control" id="delivery-mobile" placeholder="Enter Mobile No" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" name="delivery-mobile" value="<?php echo htmlspecialchars($receiverMobile); ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="branch-place">From<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="from-branch-place" name="from-branch-place" onchange="branchSelectChanged(this, 'from-branch-mobile')" required>
                                                            <option value=''>-- SELECT FROM PLACE --</option>
                                                            <?php
                                                            $selectCity = "SELECT BRANCH_NAME, BRANCH_MOBILE FROM branch_details";
                                                            $userName = $_SESSION['userName'] ?? '';
                                                            $branchName = $_SESSION['admin'] ?? '';
                                                            if (strtolower($userName) != strtolower("admin")) {
                                                                $selectCity .= " WHERE BRANCH_NAME = '$branchName'";
                                                            }
                                                            $selectCity .= ' ORDER BY BRANCH_NAME';
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo htmlspecialchars($row['BRANCH_NAME']); ?>" <?php echo ($row['BRANCH_NAME'] == $fromPlace) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['BRANCH_NAME']); ?></option>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="branch-place">To<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="to-branch-place" name="to-branch-place" onchange="branchSelectChanged(this, 'to-branch-mobile')" required>
                                                            <option value="">-- SELECT DELIVERY PLACE --</option>
                                                            <?php
                                                            $selectCity = "SELECT BRANCH_NAME, BRANCH_MOBILE FROM branch_details ORDER BY BRANCH_NAME";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo htmlspecialchars($row['BRANCH_NAME']); ?>" <?php echo ($row['BRANCH_NAME'] == $toPlace) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['BRANCH_NAME']); ?></option>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="from-branch-mobile">From Branch Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <input required class="form-control" id="from-branch-mobile" readonly placeholder="From Branch Mobile" name="from-branch-mobile" value="<?php echo htmlspecialchars($fromMobile); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="to-branch-mobile">To Branch Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <input required class="form-control" id="to-branch-mobile" readonly placeholder="To Branch Mobile" name="to-branch-mobile" value="<?php echo htmlspecialchars($toMobile); ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Quantity Details -->
                                            <input type="hidden" id="quantity-box" value="0" />
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <button type="button" class="btn btn-success btn-md" style="margin-top: 0.75em; margin-left: 50%" onclick="addQuantityDetailsRow()">
                                                        <i class="material-icons">add</i>
                                                    </button>
                                                </div>

                                                <div class="col-sm-2">&nbsp;</div>
                                                <div class="col-sm-4"><label>ITEMS</label></div>
                                                <div class="col-sm-3"><label>QUANTITY</label></div>
                                                <div class="col-sm-3">&nbsp;</div>

                                                <div class="col-sm-12" id="quantity-details-div">
                                                </div>
                                            </div>
                                            <hr />
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="quantity-no">No. Of Qty.<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="number" required class="form-control" id="quantity-no" readonly placeholder="Enter Name" name="quantity-no" value="<?php echo htmlspecialchars($quantity); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="quantity-description">Quantity Dec</label>
                                                        <textarea class="form-control" id="quantity-description" rows="2" name="quantity-description"><?php echo htmlspecialchars($quantityDescription); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="payment-type">Payment Type<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="payment-type" name="payment-type" required>
                                                            <option value="">-- SELECT PAYMENT --</option>
                                                            <option value="PAID" <?php echo ($paymentType == 'PAID') ? 'selected' : ''; ?>>Paid</option>
                                                            <option value="TO_PAY" <?php echo ($paymentType == 'TO_PAY') ? 'selected' : ''; ?>>To Pay</option>
                                                            <option value="ACCOUNT" <?php echo ($paymentType == 'ACCOUNT') ? 'selected' : ''; ?>>Account</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="total-amount">Total Amount</label>
                                                        <input type="number" class="form-control" id="total-amount" name="total-amount" readonly value="<?php echo htmlspecialchars($totalAmount); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="transportation-amount">Transportation Charge <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control" id="transportation-amount" name="transportation-amount" onchange="chargeChange()" value="<?php echo htmlspecialchars($transporationCost); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="loading-amount">Loading Charge</label>
                                                        <input type="number" class="form-control" id="loading-amount" name="loading-amount" onchange="chargeChange()" value="<?php echo htmlspecialchars($loadingCost); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="additional-amount">Additional Charge</label>
                                                        <input type="number" class="form-control" id="additional-amount" name="additional-amount" onchange="chargeChange()" value="<?php echo htmlspecialchars($additionalCost); ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="goods-value">Value Of Goods</label>
                                                    <input type="number" class="form-control" id="goods-value" name="goods-value" value="<?php echo htmlspecialchars($goodsValue); ?>" />
                                                </div>
                                            </div>

                                            <div class="row" style="margin-top: 1em;">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="delivery-method">Delivery Method <span class="text-danger">*</span> </label> </br>
                                                        <select class="form-control" id="delivery-method" name="delivery-method">
                                                            <option value="">-- SELECT DELIVERY METHOD --</option>
                                                            <option value="Office" <?php echo ($deliveryType == 'Office') ? 'selected' : ''; ?>>Office</option>
                                                            <option value="DD" <?php echo ($deliveryType == 'DD') ? 'selected' : ''; ?>>DD</option>
                                                            <option value="Line" <?php echo ($deliveryType == 'Line') ? 'selected' : ''; ?>>Line</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-info" style="margin-left: 45%" onclick="saveData()">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            Submit
                                        </button>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php' ?>
    </div>

    <script src="ddtf.js"></script>
    <script type="text/javascript">
        let branchNameArr = <?php echo json_encode($branchNameArr); ?>;
        let itemsNameArr = <?php echo json_encode($itemsArr); ?>;
        let itemsList = [...itemsNameArr]; // Create a copy of the original array
        let customerNameMobileMap = {};
        let customerMobileNameMap = {};
        let branchAndPlaceMap = {};

        // Initialize customer name-mobile mappings
        $(document).ready(function() {
            $('#customer-name option').each(function() {
                if ($(this).val()) {
                    customerNameMobileMap[$(this).val()] = $(this).data('mobile');
                }
            });

            $('#customer-mobile option').each(function() {
                if ($(this).val()) {
                    let mobile = $(this).val();
                    let name = $('#customer-name option[data-mobile="' + mobile + '"]').val();
                    if (name) {
                        customerMobileNameMap[mobile] = name;
                    }
                }
            });
        });

        function customerNameSelectChanged(select) {
            let selectVal = $(select).val();
            if (selectVal && customerNameMobileMap[selectVal]) {
                $("#customer-mobile").val(customerNameMobileMap[selectVal]).trigger('change');
            }
        }

        function mobileNumberChanged(select) {
            let selectVal = $(select).val();
            if (selectVal && customerMobileNameMap[selectVal]) {
                $("#customer-name").val(customerMobileNameMap[selectVal]).trigger('change');
            }
        }

        function branchSelectChanged(select, textAreaId) {
            let selectedVal = $(select).val();
            if (branchAndPlaceMap[selectedVal] == undefined || branchAndPlaceMap[selectedVal] == null || branchAndPlaceMap[selectedVal] == '') {
                $('#' + textAreaId).attr("readonly", false);
            } else {
                $('#' + textAreaId).val(branchAndPlaceMap[selectedVal]);
                $('#' + textAreaId).attr("readonly", true);
            }
            
            if (textAreaId.toString().startsWith("to-")) {
                let fromBranch = $("#from-branch-place").val();
                if (fromBranch === "") {
                    alert("❌ Please choose from address first!");
                    $("#to-branch-place").val('').trigger('change');
                    $("#" + textAreaId).val('');
                    return false;
                } else if (fromBranch === selectedVal) {
                    alert("❌ From Branch & To Branch can't be same!");
                    $("#to-branch-place").val('').trigger('change');
                    $("#" + textAreaId).val('');
                    return false;
                }
            }
            return true;
        }

        function parseCost(inputId) {
            let resultVal = $(inputId).val();
            return isNaN(parseInt(resultVal)) ? 0 : parseInt(resultVal);
        }

        function chargeChange() {
            let transportationCharge = parseCost('#transportation-amount');
            let loadingCharge = parseCost('#loading-amount');
            let additionalCharge = parseCost('#additional-amount');
            let totalAmount = transportationCharge + loadingCharge + additionalCharge;
            $('#total-amount').val(totalAmount);
        }

        function goToAddCustomer() {
            window.location.href = "viewBookingList.php";
        }

        function validate() {
            let branchNewName = $('#branch-name').val();
            if (branchNewName !== "" && branchNewName != null) {
                if (branchNameArr.indexOf(branchNewName.trim().toLowerCase()) > -1) {
                    alert("❌ Given Branch Name already added!");
                    $('#branchNewName').val('');
                    return false;
                }
            }
            return true;
        }

        function appendOptionString() {
            let optionString = '';
            for (let i = 0; i < itemsList.length; i++) {
                let item = itemsList[i];
                optionString += '<option value="' + item + '">' + item + '</option>';
            }
            return optionString;
        }

        function addQuantityDetailsRow() {
            let rowNumber = parseInt($("#quantity-box").val()) || 0;
            let htmlString = '' +
                '<div class="row m-t-3" id="items-' + rowNumber + '">' +
                '<div class="col-sm-2">&nbsp;</div>' +
                '    <div class="col-sm-4">' +
                '        <div class="form-group">' +
                '            <select class="form-control w-100" id="item-select-' + rowNumber + '" onchange="itemSelectChanged(' + rowNumber + ')">' +
                '                <option value=""> -- SELECT ITEM --</option>' + appendOptionString() +
                '            </select>' +
                '        </div>' +
                '    </div>' +
                '    <div class="col-sm-3">' +
                '        <div class="form-group">' +
                '            <input type="number" class="form-control" id="item-quantity-' + rowNumber + '" onchange="quantityChanged(' + rowNumber + ')" />' +
                '        </div>' +
                '    </div>' +
                '    <div class="col-sm-1">' +
                '        <button type="button" class="btn btn-warning" id="clear-btn-' + rowNumber + '" onclick="clearRow(' + rowNumber + ')">' +
                '            <span class="material-icons">' +
                '                layers_clear' +
                '            </span>' +
                '        </button>' +
                '    </div>' +
                '    <div class="col-sm-1">' +
                '        <button type="button" class="btn btn-danger" id="delete-btn-' + rowNumber + '" onclick="deleteRow(' + rowNumber + ')">' +
                '            <span class="material-icons">' +
                '                remove' +
                '            </span>' +
                '        </button>' +
                '    </div>' +
                '<div class="col-sm-2">&nbsp;</div>' +
                '</div>' +
                '';
            $("#quantity-details-div").append(htmlString);
            rowNumber += 1;
            $("#quantity-box").val(rowNumber);
            
            // Initialize select2 for the new select element
            $('#item-select-' + (rowNumber - 1)).select2();
        }

        function itemSelectChanged(rowNum) {
            let selectVal = $("#item-select-" + rowNum).val();
            const elementIndex = itemsList.indexOf(selectVal);
            if (elementIndex > -1) {
                itemsList.splice(elementIndex, 1);
            }
            $("#item-select-" + rowNum).attr("disabled", true);
        }

        function quantityChanged(rowNum) {
            let quantity = $("#item-quantity-" + rowNum).val();
            if (isNaN(parseInt(quantity))) {
                alert("⚠️ The Quantity entered is incorrect. Please correct it.");
                $("#item-quantity-" + rowNum).val('');
                return false;
            } else if (parseInt(quantity) <= 0) {
                alert("⚠️ Quantity must be greater than 0.");
                $("#item-quantity-" + rowNum).val('');
                return false;
            } else {
                quantity = parseInt(quantity);
                let totalQuantity = parseInt($("#quantity-no").val()) || 0;
                totalQuantity += quantity;
                $("#quantity-no").val(totalQuantity);
                $("#item-quantity-" + rowNum).attr("disabled", true);
            }
        }

        function updateItemListAndQuantityDetails(rowNum) {
            let selectedItem = $("#item-select-" + rowNum).val();
            let enteredQuantity = $("#item-quantity-" + rowNum).val();
            if (selectedItem != "") {
                itemsList.push(selectedItem);
            }
            enteredQuantity = isNaN(parseInt(enteredQuantity)) ? 0 : parseInt(enteredQuantity);
            if (enteredQuantity > 0) {
                let totalQuantity = parseInt($("#quantity-no").val()) || 0;
                totalQuantity -= enteredQuantity;
                $("#quantity-no").val(totalQuantity);
            }
        }

        function clearRow(rowNum) {
            updateItemListAndQuantityDetails(rowNum);
            $("#item-select-" + rowNum).val('').trigger('change');
            $("#item-select-" + rowNum).attr("disabled", false);
            $("#item-quantity-" + rowNum).val('');
            $("#item-quantity-" + rowNum).attr("disabled", false);
        }

        function deleteRow(rowNum) {
            updateItemListAndQuantityDetails(rowNum);
            $("#items-" + rowNum).remove();
        }

        function mobileNoChanged(input) {
            let inputVal = $(input).val();

            let customerName = $('#customer-name').val();
            if (customerName !== undefined && customerName !== null && customerName !== "") {
                if (inputVal !== undefined && inputVal !== null && inputVal !== "") {
                    $.ajax({
                        type: 'post',
                        url: 'bookingDataOperations.php',
                        data: {
                            updateMobileNumber: 1,
                            customerName: customerName,
                            mobileNumber: inputVal
                        },
                        success: function(response) {
                            // Handle response if needed
                        }
                    });
                }
            }
        }

        function goToAddBranchDetails() {
            window.location.href = "branchOfficeView.php";
        }

        function getItemAndQuantityObject() {
            let resultObj = {};
            let noOfItems = parseInt($("#quantity-box").val()) || 0;
            for (let i = 0; i < noOfItems; i++) {
                let isRowExists = $("#items-" + i).length;
                if (isRowExists > 0) {
                    let itemName = $("#item-select-" + i).val();
                    if (itemName == "") {
                        alert("❌ Some of the Items are not choosed. Please choose all before saving");
                        return null;
                    }
                    let quantity = $("#item-quantity-" + i).val();
                    if (quantity == "") {
                        alert("❌ Some of the Quantity box are not filled. Please enter all before saving");
                        return null;
                    }
                    resultObj[itemName] = quantity;
                }
            }
            return resultObj;
        }

        function validateAndFormData() {
            let resObj = {};
            let canInsert = true;
            let customer = $('#customer-name').val();
            if (!customer) {
                canInsert = false;
                alert("❌ Customer Name is mandatory!");
                return null;
            }
            let mobile = $('#customer-mobile').val();
            if (!mobile) {
                canInsert = false;
                alert("❌ Customer mobile is mandatory!");
                return null;
            }
            let deliveryTo = $('#delivery-to').val();
            if (!deliveryTo) {
                canInsert = false;
                alert("❌ Delivery To is mandatory!");
                return null;
            }
            let deliveryMobile = $('#delivery-mobile').val();
            if (!deliveryMobile) {
                canInsert = false;
                alert("❌ Delivery Mobile is mandatory!");
                return null;
            }
            let fromPlace = $('#from-branch-place').val();
            let fromMobile = $('#from-branch-mobile').val();
            if (!fromPlace) {
                canInsert = false;
                alert("❌ From Place is mandatory!");
                return null;
            }
            let toPlace = $('#to-branch-place').val();
            let toMobile = $('#to-branch-mobile').val();
            if (!toPlace) {
                canInsert = false;
                alert("❌ To Place is mandatory!");
                return null;
            }
            let noOfQty = $('#quantity-no').val();
            let qtyDescription = $('#quantity-description').val();
            if (!noOfQty) {
                canInsert = false;
                alert("❌ Quantity is mandatory!");
                return null;
            }
            let paymentType = $('#payment-type').val();
            if (!paymentType) {
                canInsert = false;
                alert("❌ Payment Type is mandatory!");
                return null;
            }
            let transportationCharge = $('#transportation-amount').val() || 0;
            let loadingCharge = $('#loading-amount').val() || 0;
            let additionalCharge = $('#additional-amount').val() || 0;
            let totalAmount = $('#total-amount').val() || 0;
            let goodsValue = $('#goods-value').val() || 0;
            let deliveryMethod = $('#delivery-method').val();
            if (!deliveryMethod) {
                canInsert = false;
                alert("❌ Delivery Method is mandatory!");
                return null;
            }
            
            if (canInsert) {
                return {
                    'customer': customer,
                    'mobile': mobile,
                    'deliveryTo': deliveryTo,
                    'deliveryMobile': deliveryMobile,
                    'fromPlace': fromPlace,
                    'fromMobile': fromMobile,
                    'toPlace': toPlace,
                    'toMobile': toMobile,
                    'noOfQty': noOfQty,
                    'qtyDescription': qtyDescription,
                    'paymentType': paymentType,
                    'transportationCharge': transportationCharge,
                    'loadingCharge': loadingCharge,
                    'additionalCharge': additionalCharge,
                    'totalAmount': totalAmount,
                    'goodsValue': goodsValue,
                    'deliveryMethod': deliveryMethod
                };
            }
            return null;
        }

        function saveData() {
            let cnf = confirm("✨ Sure to save!");
            if (cnf) {
                let itemsAndQuantityDetails = getItemAndQuantityObject();
                if (itemsAndQuantityDetails != null) {
                    let validateAndGetValues = validateAndFormData();
                    if (validateAndGetValues) {
                        $.ajax({
                            type: 'post',
                            url: 'bookingDataOperations.php',
                            data: {
                                isEditBooking: 1,
                                bookingId: <?php echo isset($_GET['bookingId']) ? intval($_GET['bookingId']) : 'null'; ?>,
                                customer: validateAndGetValues['customer'],
                                mobile: validateAndGetValues['mobile'],
                                deliveryTo: validateAndGetValues['deliveryTo'],
                                deliveryMobile: validateAndGetValues['deliveryMobile'],
                                fromPlace: validateAndGetValues['fromPlace'],
                                fromMobile: validateAndGetValues['fromMobile'],
                                toPlace: validateAndGetValues['toPlace'],
                                toMobile: validateAndGetValues['toMobile'],
                                noOfQty: validateAndGetValues['noOfQty'],
                                qtyDescription: validateAndGetValues['qtyDescription'],
                                paymentType: validateAndGetValues['paymentType'],
                                transportationCharge: validateAndGetValues['transportationCharge'],
                                loadingCharge: validateAndGetValues['loadingCharge'],
                                additionalCharge: validateAndGetValues['additionalCharge'],
                                totalAmount: validateAndGetValues['totalAmount'],
                                goodsValue: validateAndGetValues['goodsValue'],
                                deliveryMethod: validateAndGetValues['deliveryMethod'],
                                itemsAndQuantityDetails: JSON.stringify(itemsAndQuantityDetails)
                            },
                            success: function(response) {
                                console.log(response);
                                if (response.toString().includes("Error")) {
                                    alert("❌️ Some error occurred. Please try again!");
                                    window.location.reload();
                                } else {
                                    alert("✔️ Updated Successfully!");
                                    window.location.href = "viewBookingList.php";
                                }
                            },
                            error: function(xhr, status, error) {
                                alert("❌️ Error occurred: " + error);
                                console.error(xhr.responseText);
                            }
                        });
                    }
                }
            }
            return false;
        }

        function setQuantityDetails(quantityJson) {
            console.log("Setting quantity details:", quantityJson);
            if (!quantityJson) return;

            try {
                // If quantityJson is a string, parse it
                if (typeof quantityJson === 'string') {
                    quantityJson = JSON.parse(quantityJson);
                }

                let keys = Object.keys(quantityJson);
                for (let i = 0; i < keys.length; i++) {
                    addQuantityDetailsRow();
                    $("#item-select-" + i).val(keys[i]).trigger('change');
                    $("#item-quantity-" + i).val(quantityJson[keys[i]]);

                    // Trigger the change events to update state
                    itemSelectChanged(i);
                    quantityChanged(i);
                }
            } catch (e) {
                console.error("Error setting quantity details:", e);
            }
        }

        $(document).ready(function() {
            $('select').select2();

            <?php if (isset($_GET['bookingId']) && !empty($quantityDetails)): ?>
                try {
                    let quantityJson = <?php echo $quantityDetails; ?>;
                    if (quantityJson && typeof quantityJson === 'object') {
                        setQuantityDetails(quantityJson);
                    }
                } catch (e) {
                    console.error("Error parsing quantity details:", e);
                }
            <?php endif; ?>

            // Initialize branch mobile numbers
            $("#from-branch-place").trigger("change");
            $("#to-branch-place").trigger("change");

            // Customer select2 initialization
            $("#customer-mobile").select2({
                tags: true
            }).on('select2:close', function() {
                let element = $(this);
                let mobileNumber = $.trim(element.val());
                if (mobileNumber !== '') {
                    $.ajax({
                        type: 'post',
                        url: 'bookingDataOperations.php',
                        data: {
                            updateMobileNumber: 1,
                            mobileNumber: mobileNumber
                        },
                        success: function(response) {
                            if (response === "inserted") {
                                element.append('<option value="' + mobileNumber + '">' + mobileNumber + '</option>');
                                element.val(mobileNumber).trigger('change');
                            }
                        }
                    });
                }
            });

            $("#customer-name").select2({
                tags: true
            }).on('select2:close', function() {
                let element = this;
                let newName = $.trim(element.value);
                let mobileNumber = $("#customer-mobile").val();
                if (newName !== '') {
                    $.ajax({
                        type: 'post',
                        url: 'bookingDataOperations.php',
                        data: {
                            newCustomerName: newName,
                            mobileNumber: mobileNumber
                        },
                        success: function(response) {
                            if (response === "inserted") {
                                element.append('<option value="' + newName + '">' + newName + '</option>');
                                element.value = newName;
                            }
                        }
                    });
                }
            });
        });
    </script>

</html>