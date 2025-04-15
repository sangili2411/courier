<?php
session_start();
include 'dbConn.php';

date_default_timezone_set('Asia/Kolkata');
$date_1 = date('d-m-Y H:i');
$date = date('Y-m-d', strtotime($date_1));

$sql = "SELECT BD.BOOKING_DATE, BD.CUSTOMER, BD.LR_NUMBER, BD.FROM_PLACE, BD.TO_PLACE,
        BD.BOOKING_ID, BD.FROM_MOBILE, BD.TO_MOBILE, BD.DELIVERY_TO, BD.SHIPMENT_VIA
        FROM booking_details BD
        WHERE BD.BOOKING_STAUTS = 2";
$whereSql = "";
$userName = $_SESSION['userName'];
$branchName = $_SESSION['admin'];
if (strtolower($userName) == strtolower('admin')) {
    // Nothing
} else {
    $whereSql = " AND TO_PLACE = '$branchName' ";
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
        background: #fbb7b7 !important;
    }

    .w3-select {
        background: white !important;
    }
</style>

<script>
    let driverNameMobileMap = {};

    let driverNameSelectChanged = function() {
        let selectedVal = $(this).val();
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

        <?php include 'header2.php'; ?>

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
                                                <h2 class="m-t-p5 mb-0">SHIPINWARD LIST</h2>
                                            </div>

                                            <button type="button" class="btn btn-danger btn-sm pull-right"
                                                style="margin-top: 1em;" onclick="window.location.reload();">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($conn) && $result = mysqli_query($conn, $sql)) {
                                        if (mysqli_num_rows($result) > 0) {
                                    ?>
                                            <div id="table-data" class="table-responsive filterable max-30">
                                                <table id="data-table" class="table table-striped tableFixHead">
                                                    <thead>
                                                        <tr class="filters" style="color:#0c1211;">
                                                            <th>S. NO</th>
                                                            <th>Invoice&nbsp;No</th>
                                                            <th>Customer</th>
                                                            <th>Delivery</th>
                                                            <th>From</th>
                                                            <th>From Address</th>
                                                            <th>To</th>
                                                            <th>To&nbsp;Address</th>
                                                            <th>Date</th>
                                                            <th>ShipOut</th>
                                                            <th>Cancel</th>
                                                        </tr>
                                                    </thead>
                                                    <?php
                                                    $i = 1;
                                                    while ($row = mysqli_fetch_array($result)) {
                                                    ?>
                                                        <tbody>
                                                            <tr class="invoice-id-<?php echo $row['BOOKING_ID']; ?>">
                                                                <td>
                                                                    <?php echo $i; ?>
                                                                </td>
                                                                <td class="max-30">
                                                                    <a data-toggle="modal" class="booking-id text-info" id="booking-id-<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                        <?php echo $row['LR_NUMBER'] ?? 'NO LR NUMBER';?>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo $row['CUSTOMER']; ?></td>
                                                                <td><?php echo $row['DELIVERY_TO']; ?></td>
                                                                <td><?php echo $row['FROM_PLACE']; ?></td>
                                                                <td><?php echo $row['FROM_MOBILE']; ?></td>
                                                                <td><?php echo $row['TO_PLACE']; ?></td>
                                                                <td><?php echo $row['TO_MOBILE']; ?></td>
                                                                <td><?php echo $row['BOOKING_DATE']; ?></td>
                                                                <td class="max-30">
                                                                    <a data-toggle="modal"
                                                                        onclick="updateToShipin(<?php echo $row['BOOKING_ID']; ?>)"
                                                                        id="move-booking-id-<?php echo $row['BOOKING_ID']; ?>"
                                                                        data-booking-id="<?php echo $row['BOOKING_ID']; ?>"
                                                                        data-to-place="<?php echo $row['TO_PLACE'] ?>"
                                                                        href="">
                                                                        <i class="fa fa-random text-success" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                                <td class="max-30" style="color:#0c1211">
                                                                    <a data-toggle="modal" onclick="updateToShipinCancel(<?php echo $row['BOOKING_ID'] . ', \'' . $row['SHIPMENT_VIA'] . '\''; ?>)" id="move-booking-id-<?php echo $row['BOOKING_ID']; ?>" data-booking-id="<?php echo $row['BOOKING_ID']; ?>" href="">
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
                                            echo '    <strong>No record found 😥</strong>';
                                            echo '</div>';
                                        }
                                    }
                                        ?>
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
                                        <h4 class="modal-title">Booking Details</h4>

                                            <button type="button" class="close text-danger" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="row modal-body">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="lr-no">LR No</label>
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
    <!-- #/ container -->
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
<script src="ddtf.js"></script>

<script>
    let updateToShipinCancel = function(bookingId, shipmentVia) {
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                revertShipinward: 1,
                bookingId: bookingId,
                shipmentVia: shipmentVia
            },
            success: function(response) {
                if (response.toString().includes("Success")) {
                    $('.invoice-id-' + bookingId).addClass("move-success");
                }
            }
        });
    };

    let updateToShipin = function(bookingId) {
        console.log(bookingId);
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                moveToShipInward: 1,
                bookingId: bookingId
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
                $('#lr-no').val(res['LR_NUMBER'] ?? 'NO LR NUMBER');
                $('#invoice-status').val(res['BOOKING_STAUTS'])
                $('#customer').val(res['CUSTOMER'])
                $('#mobile').val(res['MOBILE'])
                $('#delivery-to').val(res['DELIVERY_TO'])
                $('#delivery-mobile').val(res['DELIVERY_MOBILE'])
                $('#from').val(res['FROM_PLACE'])
                $('#from-address').val(res['FROM_MOBILE'])
                $('#to').val(res['TO_PLACE'])
                $('#to-address').val(res['TO_MOBILE'])
                $('#quantity').val(res['QUANTITY'])
                $('#quantity-desc').val(res['QTY_DESCRIPTION'])
                $('#transportation-amount').val(res['TRANSPORTATION_COST'])
                $('#loading-amount').val(res['LOADING_COST'])
                $('#additional-amount').val(res['ADDITIONAL_COST'])
                $('#total-amount').val(res['TOTAL_AMOUNT'])
                $('#goods-value').val(res['GOODS_VALUE'])
                $('#notes').val(res['DELIVERY_TYPE'])
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

    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();
    /*    $("#place").select2('destroy');
        $("#driver-name").select2('destroy');*/
</script>


</html>