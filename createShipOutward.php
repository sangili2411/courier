<?php
session_start();
include 'dbConn.php';

date_default_timezone_set('Asia/Kolkata');
$date_1 = date('d-m-Y H:i');
$date = date('Y-m-d', strtotime($date_1));

$sql = "SELECT BD.BOOKING_DATE, BD.CUSTOMER, BD.LR_NUMBER, BD.FROM_PLACE, BD.TO_PLACE,
        BD.BOOKING_ID, BD.FROM_MOBILE, BD.TO_MOBILE, BD.DELIVERY_TO
        FROM booking_details BD
        WHERE BD.BOOKING_STAUTS = 0" . " AND BD.IS_DELETE= 0 ";
$whereSql = "";
$userName = $_SESSION['userName'];
$branchName = $_SESSION['admin'];
if (strtolower($userName) == strtolower('admin')) {
    // Nothing
} else {
    $whereSql = " AND FROM_PLACE = '$branchName' ";
    $sql = $sql . $whereSql;
}
$sql = $sql . " ORDER BY BOOKING_DATE";
?>



<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="./css/table-filter.css">

<style>
    /* FOR TABLE FIXED HEADER */
    .tableFixHead {
        overflow-y: auto;
        max-height: 400px;
    }

    .tableFixHead table {
        border-collapse: collapse;
        width: 100%;
    }

    .tableFixHead th,
    .tableFixHead td {
        padding: 8px 16px;
    }

    .tableFixHead th {
        position: sticky;
        top: 0;
    }

    /* ENDS HERE */
    .m-1 {
        margin-top: 1em;
    }

    .m-1-em {
        margin: 1em;
    }

    .filterable {
        margin-top: 15px;
    }

    .filterable .panel-heading .pull-right {
        margin-top: -20px;
    }

    .filterable .filters input[disabled] {
        background-color: transparent;
        border: none;
        cursor: auto;
        box-shadow: none;
        padding: 0;
        height: auto;
    }

    .filterable .filters input[disabled]::-webkit-input-placeholder {
        color: #333;
    }

    .filterable .filters input[disabled]::-moz-placeholder {
        color: #333;
    }

    .filterable .filters input[disabled]:-ms-input-placeholder {
        color: #333;
    }

    .red {
        color: red !important;
    }

    .max-30 {
        max-height: 30em;
    }

    .gst-0 {
        background: #95e395 !important;
    }

    .gst-1 {
        background: white !important;
    }

    .move-success {
        background: #b7fbb7 !important;
    }

    .w3-select {
        background: white !important;
    }

    .mandatory-field {
        color: red;
    }
</style>
<script>
    let driverNameMobileMap = {};
    let invoiceNoToPlaceMap = {};
    let branchNameMobileMap = {};

    let driverNameSelectChanged = function(select) {
        let selectedVal = $(select).val();
        $('#driver-mobile').val(driverNameMobileMap[selectedVal]);
    };
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

        <?php
        include 'header2.php';
        ?>

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
                                                <h2 class="m-t-p5 mb-0">BOOKING LIST</h2>
                                            </div>

                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm pull-right" style="margin-top: 1em;" onclick="window.location.reload();">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm pull-right" style="margin-top: 1em; margin-right: 1em;" onclick="printDiv()">
                                        <i class="material-icons" style="font-size: initial;">print</i>
                                    </button>
                                    </header>
                                    <br>
                                    <div class="row" style="margin: 0.2em;">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="customer-mobile">Vehicle No<span class="mandatory-field">*</span></label>
                                                <input type="text" required class="form-control" id="customer-mobile" placeholder="Enter Vehicle No" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" name="customer-mobile" />
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="customer-mobile">Driver Name
                                                            <span class="mandatory-field">*</span>
                                                        </label><br>
                                                        <select class="form-control" id="driver-name" name="driver-name" onchange="driverNameSelectChanged(this)">
                                                            <option value="">-- SELECT DRIVER --</option>
                                                            <?php
                                                            $selectCity = "SELECT DRIVER_NAME, MOBILE FROM driver_details ORDER BY 1";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['DRIVER_NAME'] ?>"><?php echo $row['DRIVER_NAME'] ?></option>
                                                                        <script>
                                                                            driverNameMobileMap[<?php echo "'" . $row['DRIVER_NAME'] . "'"; ?>] = <?php echo "'" . $row['MOBILE'] . "'"; ?>
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
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="customer-mobile">
                                                    Driver Mobile <span class="mandatory-field">*</span>
                                                </label>
                                                <input type="number" required class="form-control" id="driver-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" placeholder="Mobile No" name="driver-mobile" />
                                            </div>
                                        </div>
                                        <?php
                                        $getHub = "SELECT * FROM hub";
                                        $result = $conn->query($getHub);
                                        ?>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="shipment-via">
                                                    Shipment Via <span class="mandatory-field">*</span>
                                                </label>
                                                <select class="form-control" id="shipment-via" name="shipment_via">
                                                    <option value="">-- SELECT SHIPMENT ROUTE --</option>
                                                    <option value="Direct">Direct</option>
                                                    <?php
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            $hubName = htmlspecialchars($row['HUB_NAME']);
                                                            echo "<option value='Via_{$hubName}'>Via {$hubName}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div id="print-div">
                                        <div class="row m-1-em" id="print-out-details" style="display: none;">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="print-driver-name">Driver Name:</label>
                                                    <span id="print-driver-name"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="print-driver-mobile">Driver Mobile:</label>
                                                    <span id="print-driver-mobile"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="print-driver-vehicle">Vehicle:</label>
                                                    <span id="print-driver-vehicle"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="shipment-via">Shipment:</label>
                                                    <span id="print-shipment-via"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="print-destination-place">Destination Place:</label>
                                                    <span id="print-destination-place"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="print-destination-mobile">Destination Mobile:</label>
                                                    <span id="print-destination-mobile"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        if (isset($conn) && $result = mysqli_query($conn, $sql)) {
                                            if (mysqli_num_rows($result) > 0) {
                                        ?>
                                                <div id="table-data" class="table-responsive filterable max-30">

                                                    <button type="button" class="btn btn-info btn-xs pull-right mb-3" onclick="updateAllValues()">
                                                        Check All <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                                    </button>
                                                    <table id="data-table" class="table table-striped tableFixHead">
                                                        <thead>
                                                            <tr class="filters" style="color:#0c1211;">
                                                                <th class='skip-filter' style="color:#0c1211">#</th>
                                                                <th class='skip-filter' style="color:#0c1211; text-align: center;">Invoice&nbsp;No</th>
                                                                <th class='skip-filter' style="color:#0c1211">Customer</th>
                                                                <th class='skip-filter' style="color:#0c1211">Delivery</th>
                                                                <th class='skip-filter' style="color:#0c1211">From</th>
                                                                <th class='skip-filter' style="color:#0c1211">From&nbsp;Mobile</th>
                                                                <th id='to-place' class='hideout-row' style="color:#0c1211">To</th>
                                                                <th class='skip-filter hideout-row' style="color:#0c1211">To&nbsp;Mobile</th>
                                                                <th class='skip-filter' class='skip-filter' style="color:#0c1211">Date</th>
                                                                <th class='hideout-row' style="color:#0c1211">ShipOut</th>
                                                                <th class='hideout-row' style="color:#0c1211">Cancel</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $i = 1;
                                                        $invoiceNoToPlaceMap = array();
                                                        while ($row = mysqli_fetch_array($result)) {
                                                            $invoiceNoToPlaceMap[$row['BOOKING_ID']] = $row['TO_PLACE'];
                                                            $branchNameMobileMap[$row['TO_PLACE']] = $row['TO_MOBILE'];
                                                        ?>
                                                            <tbody>
                                                                <tr class="invoice-id-<?php echo $row['BOOKING_ID']; ?>">
                                                                    <td style="color:#0c1211;">
                                                                        <?php echo $i; ?>
                                                                    </td>
                                                                    <td class="max-30" style="color:#0c1211">
                                                                        <a data-toggle="modal" class="booking-id"
                                                                            id="booking-id-<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                            <?php echo $row['LR_NUMBER'] ?? 'NO LR NUMBER'; ?>
                                                                        </a>
                                                                    </td>
                                                                    <td style="color:#0c1211"><?php echo $row['CUSTOMER']; ?></td>
                                                                    <td style="color:#0c1211"><?php echo $row['DELIVERY_TO']; ?></td>
                                                                    <td style="color:#0c1211"><?php echo $row['FROM_PLACE']; ?></td>
                                                                    <td style="color:#0c1211"><?php echo $row['FROM_MOBILE']; ?></td>
                                                                    <td style="color:#0c1211"><?php echo $row['TO_PLACE']; ?></td>
                                                                    <td style="color:#0c1211"><?php echo $row['TO_MOBILE']; ?></td>
                                                                    <td style="color:#0c1211"><?php echo $row['BOOKING_DATE']; ?></td>
                                                                    <td class="max-30" style="color:#0c1211">
                                                                        <a data-toggle="modal" onclick="updateToShipOut(<?php echo $row['BOOKING_ID']; ?>)" id="move-booking-id-<?php echo $row['BOOKING_ID']; ?>" data-booking-id="<?php echo $row['BOOKING_ID']; ?>" data-to-place="<?php echo $row['TO_PLACE'] ?>" href="">
                                                                            <i class="fa fa-random text-success" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>
                                                                    <td class="max-30" style="color:#0c1211">
                                                                        <a data-toggle="modal" onclick="updateToShipOutCancel(<?php echo $row['BOOKING_ID']; ?>)" id="move-booking-id-<?php echo $row['BOOKING_ID']; ?>" data-booking-id="<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                            <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        <?php
                                                            ++$i;
                                                        }
                                                        ?>
                                                    </table>
                                            <?php
                                                mysqli_free_result($result);
                                            } else {
                                                echo '<div class="alert alert-info" style="margin: 1em;">';
                                                echo '    <strong>No record found 😥 to create ship outward</strong>';
                                                echo '</div>';
                                                // echo "No records matching your query were found.";
                                            }
                                        }
                                            ?>
                                                </div>
                                                <script>
                                                    invoiceNoToPlaceMap = <?php print_r(json_encode($invoiceNoToPlaceMap)); ?>;
                                                    branchNameMobileMap = <?php print_r(json_encode($branchNameMobileMap)); ?>;
                                                </script>
                                                <br><br>
                                    </div>

                                </div>
                            </div>
                            <!-- Trigger the modal with a button -->
                            <div style="display: none;">
                                <button type="button" class="btn btn-info btn-lg" id="hsn-btn" data-toggle="modal" data-target="#hsn-model">Open Modal
                                </button>
                                <input type="text" id="invoiceIdToCreate" />
                            </div>
                            <!-- Modal -->
                            <div id="details-model" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-lg">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="text-center">Booking Details</h4>
                                            <button type="button" class="close text-danger" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="row modal-body">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="invoice-no">Invoice No</label>
                                                    <input type="text" class="form-control" id="lr-no" name="lr-no" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="invoice-no">Status </label>
                                                    <input type="text" class="form-control" id="invoice-status" name="invoice-status" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">Customer</label>
                                                    <input type="text" class="form-control" id="customer" name="customer" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">Mobile</label>
                                                    <input type="text" class="form-control" id="mobile" name="mobile" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">Delivery To</label>
                                                    <input type="text" class="form-control" id="delivery-to" name="delivery-to" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">Delivery Mobile</label>
                                                    <input type="text" class="form-control" id="delivery-mobile" name="delivery-mobile" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">From</label>
                                                    <input type="text" class="form-control" id="from" name="from" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">From Address</label>
                                                    <input type="text" class="form-control" id="from-address" name="from-address" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">To</label>
                                                    <input type="text" class="form-control" id="to" name="to" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">To Address</label>
                                                    <input type="text" class="form-control" id="to-address" name="to-address" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">Quantity</label>
                                                    <input type="text" class="form-control" id="quantity" name="quantity" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="customer">Quantity Description</label>
                                                    <input type="text" class="form-control" id="quantity-desc" name="quantity-desc" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="total-amount">Transportation Amount</label>
                                                    <input type="number" class="form-control" id="transportation-amount" name="transportation-amount" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="total-amount">Loading Amount</label>
                                                    <input type="number" class="form-control" id="loading-amount" name="loading-amount" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="total-amount">Additional Amount</label>
                                                    <input type="number" class="form-control" id="additional-amount" name="additional-amount" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="total-amount">Total Amount</label>
                                                    <input type="number" class="form-control" id="total-amount" name="total-amount" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="total-amount">Goods Value</label>
                                                    <input type="text" class="form-control" id="goods-value" name="goods-value" readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="total-amount">Notes</label>
                                                    <input type="text" class="form-control" id="notes" name="notes" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <!--                            <button type="button" class="btn btn-success" onclick="createPdf()">Create PDF</button>-->
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <!--**********************************
            Content body end
        ***********************************-->

    <?php include 'footer.php' ?>

</body>

<!-- Select2 Fileter -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Table Filter -->
<script src="./js/ddtf.js"></script>
<!-- Prevent Number Scrolling -->
<script src="./js/chits/numberInputPreventScroll.js"></script>
<script>
    var createPdf = function(invoiceId) {
        window.location.href = 'createPPF2.php?invoiceId=' + invoiceId;
    };

    function getKeyByValue(object, value) {
        // return Object.keys(object).find(key => object[key] === value);
        var resArr = [];
        for (var key in object) {
            if (object.hasOwnProperty(key) && object[key] === value) {
                resArr.push(key);
            }
        }
        return resArr;
    }

    let updateAllValues = function() {
        /* Validation */
        let vehicle = $('#customer-mobile').val();
        if (vehicle === undefined || vehicle === null || vehicle === "") {
            alert("❌ Vehicle No is mandatory!");
            return false;
        }
        let driverName = $('#driver-name').val();
        if (driverName === undefined || driverName === null || driverName === "") {
            alert("❌ Driver Name is mandatory!");
            return false;
        }
        let driverMobile = $('#driver-mobile').val();
        if (driverMobile === undefined || driverMobile === null || driverMobile === "") {
            alert("❌ Driver Mobile No is mandatory!");
            return false;
        }
        let shipmentVia = $("#shipment-via").val();
        if (shipmentVia === undefined || shipmentVia === null || shipmentVia === "") {
            alert("❌ Shipment Via is mandatory!");
            return false;
        }

        /* Filtering To Place */
        // let toPlace = decodeURI($('.select2-hidden-accessible').val());
        let toPlace = $('select[data-select2-id="5"]').val();
        if (toPlace != undefined && toPlace != null && toPlace != "") {
            toPlace = toPlace.replace("%20", " ");
        } else { // Only one Place is there
            let branchKey = Object.keys(branchNameMobileMap)[0];
            toPlace = branchKey;
        }

        if (toPlace.includes('-all-') && Object.keys(branchNameMobileMap).length != 1) {
            alert('❌ Please choose To Place before submitting!');
            return false;
        } else {
            if (confirm("❗ Shipment Mode - " + shipmentVia + "\n💡 Kindly Confirm the shipment before submitting")) {
                let invoiceNoArr = getKeyByValue(invoiceNoToPlaceMap, toPlace);
                console.log(invoiceNoArr);
                for (let i = 0; i < invoiceNoArr.length; i++) {
                    updateToShipOut(invoiceNoArr[i]);
                }
                printDiv();
            }
        }
    };

    // let updateToShipOutCancel = function(bookingId) {
    function updateToShipOutCancel(bookingId) {
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                revertShipOutward: 1,
                bookingId: bookingId
            },
            success: function(response) {
                console.log(response);
                if (response.toString().includes("Success")) {
                    $('.invoice-id-' + bookingId).removeClass("move-success");
                }
            }
        });
    };

    let updateToShipOut = function(bookingId) {
        console.log(bookingId);
        let vehicle = $('#customer-mobile').val();
        if (vehicle === undefined || vehicle === null || vehicle === "") {
            alert("❌ Vehicle No is mandatory!")
            return false;
        }
        let driverName = $('#driver-name').val();
        if (driverName === undefined || driverName === null || driverName === "") {
            alert("❌ Driver Name is mandatory!")
            return false;
        }
        let driverMobile = $('#driver-mobile').val();
        if (driverMobile === undefined || driverMobile === null || driverMobile === "") {
            alert("❌ Driver Mobile No is mandatory!")
            return false;
        }
        let shipmentVia = $("#shipment-via").val();
        if (shipmentVia === undefined || shipmentVia === null || shipmentVia === "") {
            alert("❌ Shipment Via is mandatory!");
            return false;
        }
        let driverDetailsObj = {};
        driverDetailsObj["DRIVER_NAME"] = driverName;
        driverDetailsObj["DRIVER_MOBILE"] = driverMobile;
        driverDetailsObj["VECHILE"] = vehicle;

        let tdId = $("#invoice-id-" + bookingId);
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                moveToShipOutward: 1,
                bookingId: bookingId,
                driverDetails: JSON.stringify(driverDetailsObj),
                shipmentVia: shipmentVia
            },
            success: function(response) {
                if (response.toString().includes("Success")) {
                    $('.invoice-id-' + bookingId).addClass("move-success");
                }
            }
        });
    };

    $('.booking-id').click(function() {
        var id = this.id;
        var splitid = id.split('-');
        var bookingId = splitid[2];
        // AJAX request
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                forBookingList: 1,
                bookingId: bookingId
            },
            success: function(response) {
                // Add response in Modal body
                let res = JSON.parse(response);
                $('#lr-no').val(res['INVOICE_NUMBER'] ?? 'NO LR NUMBER');
                $('#invoice-status').val(res['BOOKING_STAUTS'] ?? 'NO BOOKING_STAUTS');
                $('#customer').val(res['CUSTOMER'] ?? 'NO CUSTOMER');
                $('#mobile').val(res['MOBILE'] ?? 'NO MOBILE');
                $('#delivery-to').val(res['DELIVERY_TO'] ?? 'NO DELIVERY TO');
                $('#delivery-mobile').val(res['DELIVERY_MOBILE'] ?? 'NO DELIVERY MOBILE');
                $('#from').val(res['FROM_PLACE'] ?? 'NO FROM PLACE');
                $('#from-address').val(res['FROM_MOBILE'] ?? 'NO FROM MOBILE');
                $('#to').val(res['TO_PLACE'] ?? 'NO TO PLACE');
                $('#to-address').val(res['TO_MOBILE'] ?? 'NO TO MOBILE');
                $('#quantity').val(res['QUANTITY'] ?? 'NO QUANTITY');
                $('#quantity-desc').val(res['QTY_DESCRIPTION'] ?? 'NO QTY DESCRIPTION');
                $('#transportation-amount').val(res['TRANSPORTATION_COST'] ?? 'NO TRANSPORTATION COST');
                $('#loading-amount').val(res['LOADING_COST'] ?? 'NO LOADING COST');
                $('#additional-amount').val(res['ADDITIONAL_COST'] ?? 'NO ADDITIONAL COST');
                $('#total-amount').val(res['TOTAL_AMOUNT'] ?? 'NO TOTAL AMOUNT');
                $('#goods-value').val(res['GOODS_VALUE'] ?? 'NO GOODS VALUE');
                $('#notes').val(res['DELIVERY_TYPE'] ?? 'NO DELIVERY TYPE');
                // Display Modal
                $('#details-model').modal('show');
            }
        });
        $('#details-model').modal('show');
    });

    var updateDetails = function(id) {
        var conform = confirm("Sure to create?");
        if (!conform) {
            return;
        } else {
            $("#invoiceIdToCreate").val('');
            $("#invoiceIdToCreate").val(id);
            $("#hsn-btn").click();
        }

    };

    let printDiv = function() {
        $("#print-out-details").show();

        let toPlace = $('select[data-select2-id="5"]').val();
        if (toPlace != undefined && toPlace != null && toPlace != "") {
            toPlace = toPlace.replace("%20", " ");
        } else { // Only one Place is there
            let branchKey = Object.keys(branchNameMobileMap)[0];
            toPlace = branchKey;
        }
        let toMobile = branchNameMobileMap[toPlace];

        $("#print-driver-name").text($("#driver-name").val());
        $("#print-driver-mobile").text($("#driver-mobile").val());
        $("#print-driver-vehicle").text($("#customer-mobile").val());
        $("#print-shipment-via").text($("#shipment-via").val());
        $("#print-destination-place").text(toPlace);
        $("#print-destination-mobile").text(toMobile);

        $('td:nth-child(1),th:nth-child(1)').hide();
        $('td:nth-child(7),th:nth-child(7)').hide();
        $('td:nth-child(8),th:nth-child(8)').hide();
        $('td:nth-child(10),th:nth-child(10)').hide();
        $('td:nth-child(11),th:nth-child(11)').hide();

        let printContents = document.getElementById('print-div').innerHTML;
        let originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        $("#print-out-details").hide();
        $('td:nth-child(1),th:nth-child(1)').show();
        $('td:nth-child(7),th:nth-child(7)').show();
        $('td:nth-child(8),th:nth-child(8)').show();
        $('td:nth-child(10),th:nth-child(10)').show();
        $('td:nth-child(11),th:nth-child(11)').show();
        $('select[data-select2-id="5"]').select();

        window.location.reload();
    };

    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();
    $('#driver-name').select2({
        tags: true
    });
    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
    /*    $("#place").select2('destroy'); */
    // $("#driver-name").select2('destroy');
</script>

</html>