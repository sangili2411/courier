<?php
include 'dbConn.php';
include 'CaesarCipher.php';

$memberId = $_GET['memberId'];
$userId = decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);

$sql = "SELECT 
            ac.GROUP_ID,
            ac.GROUP_NAME,
            ac.CHIT_MONTH,
            ac.MEMBER_ID,
            ac.MEMBER_NAME,
            ac.MEMBER_MOBILE,
            SUM(ac.MONTHLY_AMOUNT) AS MONTHLY_AMOUNT,
            COALESCE(SUM(ac.PAID_AMOUNT), 0) AS PAID_AMOUNT,
            (SUM(ac.MONTHLY_AMOUNT) - COALESCE(SUM(ac.PAID_AMOUNT), 0)) AS BALANCE
        FROM v_chit_accounts_details ac
        WHERE 1 = 1
            AND ac.MEMBER_ID = $memberId
            AND ac.CREATED_BY_AGENT = $userId
            AND ac.CHIT_MONTH < CURDATE()
        GROUP BY ac.GROUP_ID, ac.CHIT_MONTH, ac.MEMBER_ID
        HAVING BALANCE > 0
        ORDER BY GROUP_NAME, CHIT_MONTH";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="./css/table-filter.css">
<link rel="stylesheet" href="./css/chits/add-edit-group.css">

<style>
    .backgroud-red {
        color: red !important;
    }

    .table-container {
        max-height: 80%;
        /* Set the height of the container to the full viewport height */
        overflow-y: auto !important;
        /* Add scroll if the table exceeds the container height */
    }

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

    .max-30 {
        max-height: 30em;
    }
</style>

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
                                        <div class="col-sm-2">&nbsp;</div>
                                        <div class="col-sm-8">
                                            <h2 class="m-50 m-t-p5">
                                                OUTSTANDING REPORT <br>
                                                <span id="hdr-txt"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-success pull-right m-t-p5" onclick="window.location.href='reportOutstandingHome.php'">
                                                <i class="fa fa-backward" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <br>

                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <!-- Responsive Table  -->
                                        <table id="data-table" class="table table-striped table-container table-hover tableFixHead">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Group Name</th>
                                                    <th>Chit Month</th>
                                                    <th>Monthly Amount</th>
                                                    <th>Paid Amount</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $memberName = "";
                                                $memberMobile = "";
                                                if (mysqli_num_rows($result) > 0) {
                                                    $i = 1;
                                                    while ($row = mysqli_fetch_array($result)) {
                                                        $memberName = $row['MEMBER_NAME'];
                                                        $memberMobile = $row['MEMBER_MOBILE'];
                                                ?>
                                                        <tr>
                                                            <td><?php echo $i++; ?></td>
                                                            <td><?php echo $row['GROUP_NAME']; ?></td>
                                                            <td><?php echo $row['CHIT_MONTH']; ?></td>
                                                            <td><?php echo $row['MONTHLY_AMOUNT']; ?></td>
                                                            <td>
                                                                <a href="" onclick="event.preventDefault(); getPaidAmountDetails(<?php echo $row['GROUP_ID'] . ',\'' . $row['CHIT_MONTH'] . '\', ' . $row['MEMBER_ID']; ?>)">
                                                                    <?php echo $row['PAID_AMOUNT']; ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo $row['BALANCE']; ?></td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Modal For View -->
                                    <div id="view-payment-modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog modal-xxl" style="max-width: 80%;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title pull-left">View Payment Info</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <table id="dataTable" class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Group Name</th>
                                                                        <th>Chit Month</th>
                                                                        <th>Paid Amount</th>
                                                                        <th>Paid Date</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Table rows will be dynamically inserted here -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">&times; Close</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Modal For Edit -->
                                    <div id="edit-member-modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog modal-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title pull-left">Chit Taker Update</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-sm-6" style="display: none;">
                                                            <div class="form-group">
                                                                <label for="chit-id">Chit Id:</label>
                                                                <input type="text" class="form-control" id="chit-id" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6" style="display: none;">
                                                            <div class="form-group">
                                                                <label for="group-id">Group Id:</label>
                                                                <input type="text" class="form-control" id="group-id" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="member-select">Chit Taker:</label>
                                                                <select class="form-control" id="member-select">
                                                                    <option value=""> -- Select Member -- </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" onclick="updateRecord()">
                                                        <i class="fa fa-floppy-o" aria-hidden="true" style="font-size: medium !important;"></i>
                                                        Update
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">&times; Close</button>
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

        </div> <!-- #/ container -->
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
<!-- Custom JS -->
<script src="./js/chits/addEditChitGroup.js"></script>
<script>
    function populateTable(data) {
        $('#dataTable tbody').empty(); // Clear existing rows
        let totalPaidAmount = 0;

        data.forEach(function(item) {
            totalPaidAmount += parseInt(item.PAID_AMOUNT) || 0;
            var row = `<tr>
            <td>${item.GROUP_NAME}</td>
            <td>${item.CHIT_MONTH}</td>
            <td>${item.PAID_AMOUNT}</td>
            <td>${item.PAID_DATE}</td>
        </tr>`;
            $('#dataTable tbody').append(row);
        });

        var totalRow = `<tr>
                            <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                            <td>${totalPaidAmount}</td>
                            <td></td> <!-- Empty cell for the last column -->
                        </tr>`;
        $('#dataTable tbody').append(totalRow);
    }

    let getPaidAmountDetails = function(groupId, chitMonth, memberId) {
        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                isForOutstandingReportBasedOnMember: 1,
                groupId: groupId,
                chitMonth: chitMonth,
                memberId: memberId
            },
            success: function(response) {
                populateTable(JSON.parse(response));
                $('#view-payment-modal').modal('show');
            }
        });
    };

    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
        $("#hdr-txt").text('<?php echo $memberName . " - " . $memberMobile; ?>')
    });
</script>

</html>