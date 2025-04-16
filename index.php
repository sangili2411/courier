<?php
session_start();
if (!isset($_SESSION['userName'])) {
    header("Location: agentLogin.php");
    exit(); // Ensure no further code is executed after the redirect
}
include 'dbConn.php';

date_default_timezone_set('Asia/Kolkata');
$firstDay = date('Y-m-01'); // hard-coded '01' for first day
$lastDay = date('Y-m-t');

$whereSql = "";
$userName = $_SESSION['userName'] ?? null;
$branchName = $_SESSION['admin'] ?? null;
if (strtolower($userName) == strtolower('admin')) {
    // Nothing
} else {
    $whereSql = " AND FROM_PLACE = '$branchName' ";
}

$bookingCount = 0;
$bookingCountSql = "SELECT COUNT(*) AS CNT FROM booking_details WHERE BOOKING_DATE BETWEEN '$firstDay' AND '$lastDay'";
// $bookingCountSql = "SELECT COUNT(*) AS CNT FROM booking_details WHERE BOOKING_DATE BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()";
if (!empty($whereSql)) {
    $bookingCountSql = $bookingCountSql . $whereSql;
}
// echo $bookingCountSql;
if ($result = mysqli_query($conn, $bookingCountSql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $bookingCount = $row['CNT'];
        }
    }
}

$totalAmount = 0;
$totalAmountSql = "SELECT SUM(TOTAL_AMOUNT) AS TOTAL_AMOUNT FROM booking_details WHERE BOOKING_DATE BETWEEN '$firstDay' AND '$lastDay'";
if (empty($whereSql)) {
    // Nothing
} else {
    $totalAmountSql = $totalAmountSql . $whereSql;
}
// echo $bookingCountSql;
if ($result = mysqli_query($conn, $totalAmountSql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $totalAmount = $row['TOTAL_AMOUNT'];
        }
    }
}
if (empty($totalAmount)) {
    $totalAmount = 0;
}

$shipingDetailsSql = "SELECT BD.BOOKING_ID, BD.CUSTOMER, BD.INVOICE_NUMBER, 
						BD.BOOKING_DATE, BD.FROM_PLACE, BD.TO_PLACE,
						CASE
							WHEN BD.BOOKING_STAUTS = 0 THEN 'Booked/Ready To Ship'
							WHEN BD.BOOKING_STAUTS = 1 THEN 'Ship Inward'
							WHEN BD.BOOKING_STAUTS = 2 THEN 'Shipped'
							WHEN BD.BOOKING_STAUTS = 3 THEN 'Delivered'
						END AS BOOKING_STAUTS 
						FROM booking_details BD
						WHERE 1 = 1
						AND BOOKING_DATE BETWEEN '$firstDay' AND '$lastDay'
						-- AND BOOKING_DATE BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
						";
if (strtolower($userName) == 'admin') {
} else {
    $shipingDetailsSql = $shipingDetailsSql . " AND FROM_PLACE = '$branchName' ";
}
$shipingDetailsSql = $shipingDetailsSql . " ORDER BY BOOKING_DATE DESC";


$currentMonth = date('m');
$currentYear = date('Y');

// ---------------- Booking Count ----------------
$sql1 = "SELECT PAYMENT_TYPE, COUNT(*) as count FROM booking_details WHERE MONTH(BOOKING_DATE) = ? AND YEAR(BOOKING_DATE) = ? GROUP BY PAYMENT_TYPE";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("ss", $currentMonth, $currentYear);
$stmt1->execute();
$result1 = $stmt1->get_result();

$countPaid = $countToPay = $countAccount = 0;
while ($row = $result1->fetch_assoc()) {
    switch (($row['PAYMENT_TYPE'])) {
        case 'PAID':
            $countPaid = $row['count'];
            break;
        case 'TO_PAY':
            $countToPay = $row['count'];
            break;
        case 'ACCOUNT':
            $countAccount = $row['count'];
            break;
    }
}
$stmt1->close();

// ---------------- Total Amount ----------------
$sql2 = "SELECT PAYMENT_TYPE, SUM(TOTAL_AMOUNT) as total FROM booking_details WHERE MONTH(BOOKING_DATE) = ? AND YEAR(BOOKING_DATE) = ? GROUP BY PAYMENT_TYPE";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("ss", $currentMonth, $currentYear);
$stmt2->execute();
$result2 = $stmt2->get_result();



$amountPaid = $amountToPay = $amountAccount = 0;
while ($row = $result2->fetch_assoc()) {
    switch (($row['PAYMENT_TYPE'])) {
        case 'PAID':
            $amountPaid = $row['total'];
            break;
        case 'TO_PAY':
            $amountToPay = $row['total'];
            break;
        case 'ACCOUNT':
            $amountAccount = $row['total'];
            break;
    }
}
$stmt2->close();
$conn->close();
?>









<link rel="stylesheet" href="./css/table-filter.css">
<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>

<html>

<head>
    <style>
        .fa {
            font-size: xxx-large !important;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial;
        }

        #charts {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .chart-container {
            width: 45%;
            height: 200px;
        }

        .chart-wrapper {
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-top: 15px;
        }
    </style>

    </style>
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

        <?php
        include 'header.php';
        include 'dbConn.php';
        ?>

        <!--**********************************
            Content body start
        ***********************************-->




        <div class="content-body">
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-lg-6 col-sm-6">
                        <div class="card gradient-1">
                            <div class="card-body">
                                <h3 class="card-title text-white">Booking</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white"><?php echo $bookingCount; ?></h2>
                                    <p>No of parcels <br>booked this month</p>

                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6">
                        <div class="card gradient-2">
                            <div class="card-body">
                                <h3 class="card-title text-white">Amount</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white"><?php echo $totalAmount; ?></h2>
                                    <p>Total Revenue <br>generated this month</p>

                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">

                                <h3 style="text-align:center;">Total Booking Amount </h3>
                                <div id="charts">
                                    <div id="pieChart" class="chart-container" style="width: 90%; height: 300px;"></div>

                                    <script>
                                        anychart.onDocumentReady(function() {
                                            // --------- Pie Chart (Amount) ---------
                                            var pieData = [
                                                ["Paid", <?= $amountPaid ?>],
                                                ["To Pay", <?= $amountToPay ?>],
                                                ["Account", <?= $amountAccount ?>]
                                            ];

                                            var pieChart = anychart.pie(pieData);
                                            pieChart.title("Total Amount by Payment Type");
                                            pieChart.labels().format("₹{%Value}{groupsSeparator:','}");
                                            pieChart.tooltip().format("Payment Type: {%X}\nAmount: ₹{%Value}{groupsSeparator:','}");
                                            pieChart.container("pieChart");
                                            pieChart.draw();
                                            // Custom colors for the pie chart

                                            pieChart.palette(["#4CAF50", "#FFC107", "#F44336"]); // Green, Yellow, Red

                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 style="text-align:center;">Total Booking </h3>

                                <!-- Chart container placed inside its own wrapper -->
                                <div class="chart-wrapper">
                                    <div id="barChartContainer" style="width: 90%; height: 310px;"></div>
                                </div>

                                <script>
                                    // --------- Horizontal Bar Chart (Booking Count) ---------
                                    var barData = [{
                                            x: "PAID",
                                            value: <?= $countPaid ?>,
                                            normal: {
                                                fill: "#E91E63" // Pink
                                            }
                                        },
                                        {
                                            x: "To Pay",
                                            value: <?= $countToPay ?>,
                                            normal: {
                                                fill: "#FFC107" // Amber
                                            }
                                        },
                                        {
                                            x: "Account",
                                            value: <?= $countAccount ?>,
                                            normal: {
                                                fill: "#3F51B5" // Indigo
                                            }
                                        }
                                    ];

                                    var barChart = anychart.bar(); // Horizontal bar chart
                                    barChart.data(barData);
                                    barChart.title("Booking Count by Payment Type");

                                    // Axis labels
                                    barChart.xAxis().title("Payment Type");
                                    barChart.yAxis().title("Number of Bookings");

                                    // Bar labels & tooltip
                                    barChart.labels().enabled(true);
                                    barChart.labels().format("{%Value}");
                                    barChart.tooltip().format("Type: {%X}\nCount: {%Value}");

                                    // Use the container
                                    barChart.container("barChartContainer");
                                    barChart.draw();
                                </script>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="active-member" style="text-align: center;">
                                    <h4>Booking Details</h4>
                                    <div class="agileits-box-body clearfix">
                                        <?php
                                        if ($result = mysqli_query($conn, $shipingDetailsSql)) {
                                            if (mysqli_num_rows($result) > 0) {
                                        ?>
                                                <div class="table-responsive" style="max-height: 25em;">
                                                    <table id="data-table" class="table tableFixHead table-striped">
                                                        <thead>
                                                            <tr style="color:#0c1211" ;>
                                                                <th style="color:#0c1211" ;>S.No</th>
                                                                <th style="color:#0c1211" ;>Invoice No</th>
                                                                <th style="color:#0c1211" ;>Date</th>
                                                                <th style="color:#0c1211" ;>Customer Name</th>
                                                                <th style="color:#0c1211" ;>From&nbsp;Branch</th>
                                                                <th style="color:#0c1211" ;>To&nbsp;Branch</th>
                                                                <!-- <th style="color:#0c1211" ;>Driver</th>
															<th style="color:#0c1211" ;>Driver&nbsp;Mobile</th> -->
                                                                <th style="color:#0c1211" ;>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $i = 0;
                                                        while ($row = mysqli_fetch_array($result)) {
                                                            $i++;
                                                        ?>
                                                            <tbody>
                                                                <tr id="account-id-<?php echo $row['BOOKING_ID']; ?>">
                                                                    <td style="color:#0c1211" ;>
                                                                        <?php echo $i; ?>
                                                                    </td>
                                                                    <td style="color:#0c1211" ; id="custName-<?php echo $row['BOOKING_ID']; ?>">
                                                                        <a data-toggle="modal" class="refNo-info" id="refNo-<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                            <?php echo $row['INVOICE_NUMBER'] ?>
                                                                        </a>
                                                                    </td>
                                                                    <td style="color:#0c1211" ;><?php echo $row['BOOKING_DATE'] ?></td>
                                                                    <td style="color:#0c1211" ;><?php echo $row['CUSTOMER'] ?></td>
                                                                    <td style="color:#0c1211" ;><?php echo $row['FROM_PLACE'] ?></td>
                                                                    <td style="color:#0c1211" ;><?php echo $row['TO_PLACE'] ?></td>
                                                                    <!-- <td style="color:#0c1211" ;><?php echo $row['DRIVER_NAME'] ?></td>
																<td style="color:#0c1211" ;><?php echo $row['DRIVER_MOBILE'] ?></td> -->
                                                                    <td style="color:#0c1211" ;><?php echo $row['BOOKING_STAUTS'] ?></td>
                                                                </tr>
                                                            </tbody>
                                                        <?php
                                                        }
                                                        ?>
                                                    </table>
                                            <?php
                                                mysqli_free_result($result);
                                            } else {
                                                echo "No records found.";
                                            }
                                        }
                                            ?>
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
    </div>
    <!--**********************************
            Content body end
        ***********************************-->


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
    <script src="plugins/common/common.min.js"></script>
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

    <!-- Select2 Fileter -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Table Filter -->
    <script src="./js/ddtf.js"></script>

    <script>
        $(document).ready(function() {
            $("table").ddTableFilter();
            $('select').addClass('w3-select');
            $('select').select2();
        });
    </script>

</body>

</html>