<?php

include 'dbConn.php';

$branchNameQry = "SELECT DISTINCT LOWER(BRANCH_NAME) AS BRANCH_NAME FROM branch_details ORDER BY 1";
$branchNameArr = array();
if (isset($conn) && $result = mysqli_query($conn, $branchNameQry)) {
    if (mysqli_num_rows($result) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_array($result)) {
            $branchNameArr[$i] = $row['BRANCH_NAME'];
            $i = $i + 1;
        }
    }
}

$itemsQry = "SELECT ITEM_NAME FROM items ORDER BY 1";
$itemsArr = array();
if (isset($conn) && $result = mysqli_query($conn, $itemsQry)) {
    if (mysqli_num_rows($result) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_array($result)) {
            $itemsArr[$i] = $row['ITEM_NAME'];
            $i = $i + 1;
        }
    }
}

if (isset($_POST['submit'])) {
    $branchName = trim($_POST['branch-name']);
    $branchMobile = $_POST['branch-mobile'];
    $branchAlternativeMobile = $_POST['branch-alternative-mobile'];
    $branchAlternativeMobile = empty($branchAlternativeMobile) ? "" : trim($branchAlternativeMobile);
    $branchAddress = trim($_POST['branch-address']);
    $place = trim($_POST['branch-place']);
    $userName = trim($_POST['user-name']);
    $password = $_POST['password'];

    $branchOfficeInsertQuery = "INSERT INTO `branch_details`
                                (`BRANCH_NAME`, `BRANCH_MOBILE`, `ALTERNATIVE_MOBILE`, `ADDRESS`,`PLACE`, `USER_NAME`, `PASSWORD`)
                                VALUES
                                ('$branchName', $branchMobile, '$branchAlternativeMobile', '$branchAddress', '$place', '$userName', '$password')
                                ";
    if (isset($conn) && mysqli_query($conn, $branchOfficeInsertQuery)) {
?>
        <script type="text/javascript">
            alert('✔️ Branch Details Saved Successfully!');
            window.location.href = 'branchOffice.php';
        </script>
<?php
    } else {
        if (isset($conn)) {
            echo "Error: " . $branchOfficeInsertQuery . "" . mysqli_error($conn);
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

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
</style>

<script>
    let branchNameArr = <?php echo json_encode($branchNameArr); ?>;
    let itemsNameArr = <?php echo json_encode($itemsArr); ?>;
    let itemsList = itemsNameArr;
    let customerNameMobileMap = {};
    let customerMobileNameMap = {};
    let branchAndPlaceMap = {};

    let customerNameSelectChanged = function(select) {
        let selectVal = $(select).val();
        $("#customer-mobile").val(customerNameMobileMap[selectVal]);
    };

    let mobileNumberChanged = function(select) {
        let selectVal = $(select).val();
        $("#customer-name").select2({
            tags: true
        }).val(customerMobileNameMap[selectVal]).trigger('change');
    };

    let branchSelectChanged = function(select, textAreaId) {
        let selectedVal = $(select).val();
        if (branchAndPlaceMap[selectedVal] == undefined || branchAndPlaceMap[selectedVal] == null || branchAndPlaceMap[selectedVal] == '') {
            $('#' + textAreaId).attr("readonly", false);
        } else {
            $('#' + textAreaId).val(branchAndPlaceMap[selectedVal]);
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
    };

    let parseCost = function(inputId) {
        let resultVal = $(inputId).val();
        return isNaN(parseInt(resultVal)) ? 0 : parseInt(resultVal);
    };

    let chargeChange = function() {
        let transportationCharge = parseCost('#transportation-amount');
        let loadingCharge = parseCost('#loading-amount');
        let additionalCharge = parseCost('#additional-amount');
        let totalAmount = transportationCharge + loadingCharge + additionalCharge;
        $('#total-amount').val(totalAmount);
    };

    let goToAddCustomer = function() {
        window.location.href = "viewBookingList.php";
    };

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
</script>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php include 'header.php'; ?>

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-validation">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1 text-center">
                                                <h2 class="m-t-p5 mb-4">NEW BOOKING</h2>
                                            </div>

                                            <button type="button" class="btn btn-info btn-md" onclick="window.location.href='viewBookingList.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        <div> <!--class="position-center"-->
                                            <div class="row">
                                                <br>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="customer-mobile">Sender Mobile<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="customer-mobile" name="customer-mobile" onchange="mobileNumberChanged(this)" required>
                                                            <option value="">-- SELECT MOBILE --</option>
                                                            <?php
                                                            $selectCity = "SELECT DISTINCT TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['MOBILE'] ?>"><?php echo $row['MOBILE'] ?></option>
                                                                        <script>
                                                                            customerMobileNameMap[<?php echo "'" . $row['MOBILE'] . "'"; ?>] = <?php echo "'" . $row['CUSTOMER_NAME'] . "'"; ?>
                                                                        </script>
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
                                                        <label for="branch-place">Sender Name<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="customer-name" name="customer-name">
                                                            <!--                                                    onchange="customerNameSelectChanged(this)" -->
                                                            <option value="">-- SELECT SENDER --</option>
                                                            <?php
                                                            $selectCity = "SELECT CUSTOMER_ID, TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['CUSTOMER_NAME'] ?>"><?php echo $row['CUSTOMER_NAME'] ?></option>
                                                                        <script>
                                                                            customerNameMobileMap[<?php echo "'" . $row['CUSTOMER_NAME'] . "'"; ?>] = <?php echo "'" . $row['MOBILE'] . "'"; ?>
                                                                        </script>
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
                                                        <input type="text" required class="form-control" id="delivery-to" placeholder="Enter Delivery Info" name="delivery-to" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="customer-mobile">Receiver Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="number" required class="form-control" id="delivery-mobile" placeholder="Enter Mobile No" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" name="delivery-mobile" />
                                                    </div>
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
                                                        $userName = $_SESSION['userName'];
                                                        $branchName = $_SESSION['admin'];
                                                        if (strtolower($userName) == strtolower("admin")) {
                                                            // Do Nothing
                                                        } else {
                                                            $selectCity = $selectCity . " WHERE BRANCH_NAME = '$branchName'";
                                                        }
                                                        $selectCity = $selectCity . '  ORDER BY BRANCH_NAME';
                                                        if ($result = mysqli_query($conn, $selectCity)) {
                                                            if (mysqli_num_rows($result) > 0) {
                                                                while ($row = mysqli_fetch_array($result)) {
                                                        ?>
                                                                    <option value="<?php echo $row['BRANCH_NAME'] ?>" selected><?php echo $row['BRANCH_NAME'] ?></option>
                                                                    <script>
                                                                        branchAndPlaceMap[<?php echo "'" . $row['BRANCH_NAME'] . "'"; ?>] = <?php echo "'" . str_replace('"', '', json_encode($row['BRANCH_MOBILE'])) . "'"; ?>
                                                                    </script>
                                                        <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- <?php echo $selectCity; ?> -->
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
                                                                    <option value="<?php echo $row['BRANCH_NAME'] ?>"><?php echo $row['BRANCH_NAME'] ?></option>
                                                                    <script>
                                                                        branchAndPlaceMap[<?php echo "'" . $row['BRANCH_NAME'] . "'"; ?>] = <?php echo "'" . str_replace('"', '', json_encode($row['BRANCH_MOBILE'])) . "'"; ?>
                                                                    </script>
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
                                                    <input required class="form-control" id="from-branch-mobile" readonly placeholder="From Branch Mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" name="from-branch-mobile" />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="to-branch-mobile">To Branch Mobile
                                                        <span class="mandatory-field text-danger">*</span></label>
                                                    <input required class="form-control" id="to-branch-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" readonly placeholder="To Branch Mobile" name="to-branch-mobile" />
                                                </div>
                                            </div>
                                        </div>
                                        <hr />
                                        <!--  Quantity Details  -->
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
                                                    <input type="number" required class="form-control" id="quantity-no" readonly placeholder="Enter Name" name="quantity-no" value="0" />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="quantity-description">Quantity Dec</label>
                                                    <!--<span class="mandatory-field">*</span></label>-->
                                                    <textarea required class="form-control" id="quantity-description" rows="2" name="quantity-description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="payment-type">Payment Type<span class="mandatory-field text-danger">*</span></label><br>
                                                    <select class="form-control" id="payment-type" name="to-branch-place" required>
                                                        <option value="">-- SELECT PAYMENT --</option>
                                                        <option value="PAID">Paid</option>
                                                        <!-- <option value="AMOUNT">Amount</option> -->
                                                        <option value="TO_PAY">To Pay</option>
                                                        <option value="ACCOUNT">Account</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="total-amount">Total Amount</label>
                                                    <input type="number" class="form-control" id="total-amount" name="total-amount" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="transportation-amount ">Transportation Charge <span class="text-danger">*</span> </label>
                                                    <input type="number" class="form-control" id="transportation-amount" name="transportation-amount" onchange="chargeChange()" />
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="loading-amount">Loading Charge</label>
                                                    <input type="number" class="form-control" id="loading-amount" name="loading-amount" onchange="chargeChange()" />
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="additional-amount">Additional Charge</label>
                                                    <input type="number" class="form-control" id="additional-amount" name="additional-amount" onchange="chargeChange()" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="goods-value">Value Of Goods</label>
                                                <input type="number" class="form-control" id="goods-value" name="goods-value" />
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 1em;">



                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="delivery-method">Delivery Method <span class="text-danger">*</span> </label> </br>
                                                    <select class="form-control" id="delivery-method" name="delivery-method">
                                                        <option value="">-- SELECT DELIVERY METHOD --</option>
                                                        <option value="Office">Office</option>
                                                        <option value="DD">DD</option>
                                                        <option value="Line">Line</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-success" style="margin-left: 45%" onclick="saveData()">
                                        <i class="fa fa-floppy-o font-medium menu-icon" aria-hidden="true"></i>
                                        <!--💾-->
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--main content end-->

    <script type="text/javascript">
        $('select').select2();
        // $('select').select2({dropdownAutoWidth: true});

        let appendOptionString = function() {
            let optionString = '';
            for (let i = 0; i < itemsList.length; i++) {
                let item = itemsList[i];
                optionString += '<option value="' + item + '">' + item + '</option>';
            }
            return optionString;
        };

        let addQuantityDetailsRow = function() {
            let rowNumber = $("#quantity-box").val();
            rowNumber = isNaN(parseInt(rowNumber)) ? 0 : parseInt(rowNumber);
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
            $("#item-select-" + rowNumber).select2({
                tags: true
            });
            rowNumber += 1;
            $("#quantity-box").val(rowNumber);

        };

        let itemSelectChanged = function(rowNum) {
            let selectVal = $("#item-select-" + rowNum).val();
            const elementIndex = itemsList.indexOf(selectVal);
            if (elementIndex > -1) {
                itemsList.splice(elementIndex, 1);
            }
            $("#item-select-" + rowNum).attr("disabled", true);
        };

        let quantityChanged = function(rowNum) {
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
                let totalQuantity = parseInt($("#quantity-no").val());
                totalQuantity += quantity;
                $("#quantity-no").val(totalQuantity);
                $("#item-quantity-" + rowNum).attr("disabled", true);
            }
        };

        let updateItemListAndQuantityDetails = function(rowNum) {
            let selectedItem = $("#item-select-" + rowNum).val();
            let enteredQuantity = $("#item-quantity-" + rowNum).val();
            if (selectedItem != "") {
                itemsList.push(selectedItem);
            }
            enteredQuantity = isNaN(parseInt(enteredQuantity)) ? 0 : parseInt(enteredQuantity);
            if (enteredQuantity > 0) {
                let totalQuantity = parseInt($("#quantity-no").val());
                totalQuantity -= enteredQuantity;
                $("#quantity-no").val(totalQuantity);
            }
        };

        let clearRow = function(rowNum) {
            updateItemListAndQuantityDetails(rowNum);
            $("#item-select-" + rowNum).select2().val('');
            $("#item-select-" + rowNum).select2().attr("disabled", false);
            $("#item-quantity-" + rowNum).val('');
            $("#item-quantity-" + rowNum).attr("disabled", false);
        };

        let deleteRow = function(rowNum) {
            updateItemListAndQuantityDetails(rowNum);
            $("#items-" + rowNum).remove();
        };

        let mobileNoChanged = function(input) {
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

                        }
                    })
                }
            }
        };

        let goToAddBranchDetails = function() {
            window.location.href = "branchOfficeView.php";
        };

        let getItemAndQuantityObject = function() {
            let resultObj = {};
            let noOfItems = parseInt($("#quantity-box").val());
            for (let i = 0; i < noOfItems; i++) {
                let isRowExists = $("#items-" + i);
                if (isRowExists.length > 0) {
                    let itemName = $("#item-select-" + i).val();
                    if (itemName == "") {
                        alert("❌ Some of the Items are not choosed. Please choose all before saving");
                        return null;
                    }
                    let quantity = $("#item-quantity-" + i).val();
                    if (quantity == "") {
                        alert("❌ Some of the Quantity box are not filled. Pleas enter all before saving");
                        return null;
                    }
                    resultObj[itemName] = quantity;
                }
            }
            return resultObj;
        };

        let validateAndFormData = function() {
            let resObj = {};
            let canInsert = true;
            let customer = $('#customer-name').val();
            if (customer === undefined || customer === null || customer === "") {
                canInsert = false;
                alert("❌ Customer Name is mandatory!");
                return null;
            }
            let mobile = $('#customer-mobile').val();
            if (mobile === undefined || mobile === null || mobile === "") {
                canInsert = false;
                alert("❌ Customer mobile is mandatory!");
                return null;
            }
            let deliveryTo = $('#delivery-to').val();
            if (deliveryTo === undefined || deliveryTo === null || deliveryTo === "") {
                canInsert = false;
                alert("❌ Delivery To is mandatory!");
                return null;
            }
            let deliveryMobile = $('#delivery-mobile').val();
            if (deliveryMobile === undefined || deliveryMobile === null || deliveryMobile === "") {
                canInsert = false;
                alert("❌ Delivery Mobile is mandatory!");
                return null;
            }
            let fromPlace = $('#from-branch-place').val();
            let fromMobile = $('#from-branch-mobile').val();
            if (fromPlace === undefined || fromPlace === null || fromPlace === "") {
                canInsert = false;
                alert("❌ From Place is mandatory!");
                return null;
            }
            let toPlace = $('#to-branch-place').val();
            let toMobile = $('#to-branch-mobile').val();
            if (toPlace === undefined || toPlace === null || toPlace === "") {
                canInsert = false;
                alert("❌ To Place is mandatory!");
                return null;
            }
            let noOfQty = $('#quantity-no').val();
            let qtyDescription = $('#quantity-description').val();
            if (noOfQty === undefined || noOfQty === null || noOfQty === "") {
                canInsert = false;
                alert("❌ Quantity is mandatory!");
                return null;
            }
            let paymentType = $('#payment-type').val();
            if (paymentType === undefined || paymentType === null || paymentType === "") {
                canInsert = false;
                alert("❌ Payment Type is mandatory!");
                return null;
            }
            let transportationCharge = $('#transportation-amount').val();

            let loadingCharge = $('#loading-amount').val();
            let additionalCharge = $('#additional-amount').val();
            let totalAmount = $('#total-amount').val();
            let goodsValue = $('#goods-value').val();
            if (goodsValue == "") {
                goodsValue = 0;
            }
            let deliveryMethod = $('#delivery-method').val();
            if (deliveryMethod === undefined || deliveryMethod === null || deliveryMethod === "") {
                canInsert = false;
                alert("❌ Delivery Method is mandatory!");
                return null;
            }
            if (canInsert === true) {
                resObj['customer'] = customer;
                resObj['mobile'] = mobile;
                resObj['deliveryTo'] = deliveryTo;
                resObj['deliveryMobile'] = deliveryMobile;
                resObj['fromPlace'] = fromPlace;
                resObj['fromMobile'] = fromMobile;
                resObj['toPlace'] = toPlace;
                resObj['toMobile'] = toMobile;
                resObj['noOfQty'] = noOfQty;
                resObj['qtyDescription'] = qtyDescription;
                resObj['paymentType'] = paymentType;
                resObj['transportationCharge'] = transportationCharge;
                resObj['loadingCharge'] = loadingCharge;
                resObj['additionalCharge'] = additionalCharge;
                resObj['totalAmount'] = totalAmount;
                resObj['goodsValue'] = goodsValue;
                resObj['deliveryMethod'] = deliveryMethod;
                return resObj;
            }
            return null;
        };

        let saveData = function() {
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
                                isNewBooking: 1,
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
    if (response.toString().includes("Error")) {
        alert("❌️ Some error occurred. Please try again!");
        window.location.reload();
    } else {
        alert("✔️ Booked Successfully!");
        
        // Extract only numbers (e.g., "Successful7" => "7")
        let id = response.match(/\d+/);
        if (id) {
            window.location.href = "createPPF2.php?lr_Id=" + id[0];
        } else {
            window.location.href = "createPPF2.php";
        }
    }
}

                        });
                    }
                }
            } else {
                return false;
            }
        };

        let deleteRecords = function(deleteButton) {
            let cnf = confirm("⚠️ Sure to delete!");
            if (cnf) {
                let city = $(deleteButton).attr('data-type');
                $.ajax({
                    type: 'post',
                    url: 'placeEntry.php',
                    data: {
                        city: city,
                    },
                    success: function() {
                        alert('✔️Place (' + city + ') Deleted Successfully!');
                        window.location.reload();
                    }
                });
            }
        }

        $(document).ready(function() {
            $("#from-branch-place").trigger("change");
            $("#to-branch-place").select2({
                tags: true
            });

            $("#customer-mobile").select2({
                tags: true
            }).on('select2:close', function() {
                let element = this;
                let mobileNumber = $.trim(element.value);
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
                                element.value = mobileNumber;
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
<!--**********************************
            Footer start
        ***********************************-->
<div class="footer">
    <div class="copyright">
        <p>Copyright &copy; Designed & Developed by <a href="#">ZENITH</a>
            2024</p>
    </div>
</div>
<!--**********************************
            Footer end
        ***********************************-->
</div>
<!--**********************************
        Main wrapper end
    ***********************************-->

<!--**********************************
        Scripts
    ***********************************-->
<!-- <script src="plugins/common/common.min.js"></script> -->
<script src="js/custom.min.js"></script>
<script src="js/settings.js"></script>
<script src="js/gleek.js"></script>
<script src="js/styleSwitcher.js"></script>

<!-- Chartjs -->
<script src="./plugins/chart.js/Chart.bundle.min.js"></script>
<!-- Circle progress -->
<script src="./plugins/circle-progress/circle-progress.min.js"></script>
<!-- Datamap -->
<script src="./plugins/d3v3/index.js"></script>
<script src="./plugins/topojson/topojson.min.js"></script>
<script src="./plugins/datamaps/datamaps.world.min.js"></script>
<!-- Morrisjs -->
<script src="./plugins/raphael/raphael.min.js"></script>
<script src="./plugins/morris/morris.min.js"></script>
<!-- Pignose Calender -->
<script src="./plugins/moment/moment.min.js"></script>
<script src="./plugins/pg-calendar/js/pignose.calendar.min.js"></script>
<!-- ChartistJS -->
<script src="./plugins/chartist/js/chartist.min.js"></script>
<script src="./plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js"></script>



<script src="./js/dashboard/dashboard-1.js"></script>
</body>



</html>