<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <!-- Your head content here -->
    <link rel="stylesheet" href="./css/table-filter.css">
    <style>
        /* Your styles here */
        .max-30 {
            max-height: 30vh;
            overflow-y: auto;
        }

        .a-edit-icon {
            cursor: pointer;
        }

        .font-x-large {
            font-size: 1.5em;
        }
    </style>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

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
                                                <h2 class="m-t-p5 mb-0 mb-5">TRANSACTION</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Sno</th>
                                                    <th>Date</th>
                                                    <th>Branch Name</th>
                                                    <th>Request Amount</th>
                                                    <th>Payment Type</th>
                                                    <th>Notes</th>
                                                    <!-- <th>Edit</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                error_reporting(E_ALL);
                                                ini_set('display_errors', 1);
                                                include 'dbConn.php';

                                                if (!$conn) {
                                                    die("❌ Database connection failed: " . mysqli_connect_error());
                                                }

                                                $query = "SELECT 
                t.*, 
                bd.BRANCH_NAME
              FROM 
                transactions t
              LEFT JOIN 
                branch_account ba ON t.BRANCH_ACCOUNT_ID = ba.BRANCH_ACCOUNT_ID
              LEFT JOIN 
                branch_details bd ON ba.BRANCH_ID = bd.BRANCH_OFFICE_ID
              ORDER BY 
                t.CREATED_AT DESC";

                                                $result = mysqli_query($conn, $query);
                                                $sno = 1;

                                                if ($result && mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo "<tr class='text-center'>";
                                                        echo "<td>" . $sno++ . "</td>";
                                                        echo "<td>" . strtolower(date('d-M-Y', strtotime($row['CREATED_AT']))) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['BRANCH_NAME'] ?? 'N/A') . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['REQUEST_AMOUNT'] ?? '-') . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['PAYMENT_TYPE'] ?? '-') . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['NOTES'] ?? '-') . "</td>";
                                                        // echo "<td><a href='editTransaction.php?id=" . $row['TRANSACTION_ID'] . "'><i class='fa fa-edit'></i></a></td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                                                }

                                                mysqli_close($conn);
                                                ?>
                                            </tbody>
                                        </table>



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

        <?php include 'footer.php'; ?>

    </div>

    <!-- Required JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Select2 Filter -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Table Filter -->
    <script src="./js/ddtf.js"></script>
    <!-- Prevent Number Scrolling -->
    <script src="./js/chits/numberInputPreventScroll.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize table filter
            $("#data-table").ddTableFilter();
            $('select').addClass('w3-select').select2();

            // Handle branch modal show event
            $('#branchModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                // Set values in the modal fields
                modal.find('#branchName').val(button.data('branchname'));
                modal.find('#branchMobile').val(button.data('mobile'));
                modal.find('#alternativeMobile').val(button.data('altmobile'));
                modal.find('#place').val(button.data('place'));
                modal.find('#username').val(button.data('username'));
                // modal.find('#password').val(button.data('password'));
                modal.find('#paidCommission').val(button.data('paidcommission'));
                modal.find('#toPaidCommission').val(button.data('topaidcommission'));
                modal.find('#address').val(button.data('address'));
                modal.find('#totalExpense').val(button.data('totalexpense'));
            });


        });



        // Helper function to get month name
        function getMonthName(month) {
            var months = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return months[parseInt(month) - 1] || '';
        }
    </script>
</body>

</html>
