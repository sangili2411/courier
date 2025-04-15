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
$bookingCountSql = "SELECT COUNT(*) AS CNT FROM booking_details WHERE BOOKING_DATE AND IS_DELETE=0 BETWEEN '$firstDay' AND '$lastDay'";
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

$shipingDetailsSql = "SELECT BD.BOOKING_ID, BD.CUSTOMER, BD.LR_NUMBER, 
						BD.BOOKING_DATE, BD.FROM_PLACE, BD.TO_PLACE,
						CASE
							WHEN BD.BOOKING_STAUTS = 0 THEN 'Booked/Ready To Ship'
							WHEN BD.BOOKING_STAUTS = 1 THEN 'Ship Inward'
							WHEN BD.BOOKING_STAUTS = 2 THEN 'Shipped'
							WHEN BD.BOOKING_STAUTS = 3 THEN 'Delivered'
						END AS BOOKING_STAUTS 
						FROM booking_details BD
						WHERE 1 = 1 AND IS_DELETE = 0
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
    switch (strtolower($row['PAYMENT_TYPE'])) {
        case 'paid': $countPaid = $row['count']; break;
        case 'to pay': $countToPay = $row['count']; break;
        case 'account': $countAccount = $row['count']; break;
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
    switch (strtolower($row['PAYMENT_TYPE'])) {
        case 'paid': $amountPaid = $row['total']; break;
        case 'to pay': $amountToPay = $row['total']; break;
        case 'account': $amountAccount = $row['total']; break;
    }
}
$stmt2->close();
$conn->close();


?>

<!DOCTYPE html>
<html lang="en">

<?php
function formatToIndianCurrency($amount)
{
    // Format the amount according to Indian currency format
    return number_format($amount, 0, '.', ',');
}
?>

<link rel="stylesheet" href="./css/table-filter.css">
<style>
    .fa {
        font-size: xxx-large !important;
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

        <?php 
        include 'header2.php';
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
                    <div class="col-lg-6 col-sm-6">
                        <div class="card">
                            <div class="card-body">

                                <title>Booking Payment Type - Horizontal Bar</title>
                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                </head>

                                <body>
                                    <h3>Payment Type Summary - <?php echo date("F Y"); ?></h3>
                                    <canvas id="barChart" width="600" height="400"></canvas>

                                    <script>
                                        const barCtx = document.getElementById('barChart').getContext('2d');
                                        const barChart = new Chart(barCtx, {
                                            type: 'bar',
                                            data: {
                                                labels: ['Paid', 'To Pay', 'Account'],
                                                datasets: [{
                                                    label: 'No. of Bookings',
                                                    data: [
                                                        <?php echo $countPaid; ?>,
                                                        <?php echo $countToPay; ?>,
                                                        <?php echo $countAccount; ?>
                                                    ],
                                                    backgroundColor: ['#4CAF50', '#FFC107', '#2196F3']
                                                }]
                                            },
                                            options: {
                                                indexAxis: 'y', // 👉 this makes it horizontal
                                                responsive: true,
                                                scales: {
                                                    x: {
                                                        beginAtZero: true
                                                    }
                                                }
                                            }
                                        });
                                    </script>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                        <div class="card">
                            <div class="card-body">

                                <title>Booking Payment Type - Pie chart</title>

                                <h3>Total Amount - <?php echo date("F Y"); ?></h3>
                                <canvas id="pieChart" width="100" height="100"></canvas>
                                <script>
                                    const pieCtx = document.getElementById('pieChart').getContext('2d');
                                    const pieChart = new Chart(pieCtx, {
                                        type: 'pie',
                                        data: {
                                            labels: ['Paid', 'To Pay', 'Account'],
                                            datasets: [{
                                                label: 'Total Amount (₹)',
                                                data: [
                                                    <?php echo $amountPaid ?? 0; ?>,
                                                    <?php echo $amountToPay ?? 0; ?>,
                                                    <?php echo $amountAccount ?? 0; ?>
                                                ],
                                                backgroundColor: ['#4CAF50', '#FFC107', '#2196F3'],
                                                hoverOffset: 10
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const value = context.parsed || 0;
                                                            return `${context.label}: ₹${value.toLocaleString('en-IN')}`;
                                                        }
                                                    }
                                                },
                                                legend: {
                                                    position: 'bottom'
                                                }
                                            }
                                        }
                                    });
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
															<th style="color:#0c1211" ;>LR No</th>
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
																		<?php echo $row['LR_NUMBER'] ?? "NO LR NUMBER"; ?>
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
		var buildTable = function(sizeArray) {
			var columns = addAllColumnHeaders(sizeArray);
			for (var i = 0; i < sizeArray.length; i++) {
				var row$ = $('<tr/>');
				for (var colIndex = 0; colIndex < columns.length; colIndex++) {
					var cellValue = sizeArray[i][columns[colIndex]];

					if (cellValue == null) {
						cellValue = "";
					}

					row$.append($('<td/>').html(cellValue));
				}
				$("#report-table").append(row$);
			}
		}

		var addAllColumnHeaders = function(sizeArray) {
			var columnSet = [];
			var headerTr$ = $('<tr/>');

			for (var i = 0; i < sizeArray.length; i++) {
				var rowHash = sizeArray[i];
				for (var key in rowHash) {
					if ($.inArray(key, columnSet) == -1) {
						columnSet.push(key);
						headerTr$.append($('<th/>').html(key));
					}
				}
			}

			$("#report-table").append(headerTr$);
			return columnSet;
		};

		$(document).ready(function() {
			let userType = <?php echo "'" . $_SESSION['admin'] . "'"; ?>;
			console.log('userType: ' + userType);
			let userName = <?php echo "'" . $_SESSION['admin'] . "'"; ?>;

			$("#data-table").ddTableFilter();
			$('select').addClass('w3-select');
			$('select').select2();

			$('.refNo-info').click(function() {
				var id = this.id;
				var splitid = id.split('-');
				var samplePiId = splitid[1];
                console.log("samplePiId: ", samplePiId);
				// AJAX request
				$.ajax({
					// url: 'account_transaction.php',
                    url:'dataOperations.php',
					type: 'post',
					data: {
                        getBookingDetails:1,
						samplePiId: samplePiId
					},
					success: function(response) {
						console.log(response);
						$("#modal-title").html('');
						$("#report-table tr").detach();
						let res = JSON.parse(response);
						console.log(res[1]);
						res = res[0][0];
						$("#modal-title-refNo").html(res["REFERENCE_NO"]);

						$("#customer-name").val(res["CUSTOMER_NAME"]);
						$("#customer-mobile").val(res["MOBILE"]);
						$("#customer-date").val(res["DATE"]);
						$("#customer-productAmount").val(res["PRODUCT_AMOUNT"]);
						$("#customer-gstAmount").val(res["GST_AMOUNT"]);
						$("#customer-total").val(res["TOTAL_AMOUNT"]);
						$("#customer-advance").val(res["ADVANCE"]);
						$("#customer-balance").val(res["BALANCE"]);

						// Display Modal
						$('#refNo-modal').modal('show');
					}
				});
			});

			$('.transaction-info').click(function() {
				var id = this.id;
				var splitid = id.split('-');
				var userid = splitid[1];

				// AJAX request
				$.ajax({
					url: 'account_transaction.php',
					type: 'post',
					data: {
						userid: userid
					},
					success: function(response) {
						// Add response in Modal body
						$("#modal-title").html('');
						$("#report-table tr").detach();
						let res = JSON.parse(response);
						$("#modal-title").html($("#custName-" + res[1]).text());
						console.log(res[1]);
						buildTable(res[0]);

						// Display Modal
						$('#myModal').modal('show');
					}
				});
			});

			$('.color-condition').each(function() {
				var $this = $(this);
				var value = $this.text().trim();
				if (value == "Pending") {
					$(this).children().removeClass("fa-check").addClass("fa-times");
					$this.addClass('red');
				} else {

				}
				console.log(value);
			});


			//BOX BUTTON SHOW AND CLOSE
			jQuery('.small-graph-box').hover(function() {
				jQuery(this).find('.box-button').fadeIn('fast');
			}, function() {
				jQuery(this).find('.box-button').fadeOut('fast');
			});
			jQuery('.small-graph-box .box-close').click(function() {
				jQuery(this).closest('.small-graph-box').fadeOut(200);
				return false;
			});

			//CHARTS
			function gd(year, day, month) {
				return new Date(year, month - 1, day).getTime();
			}

			graphArea2 = Morris.Area({
				element: 'hero-area',
				padding: 10,
				behaveLikeLine: true,
				gridEnabled: false,
				gridLineColor: '#dddddd',
				axes: true,
				resize: true,
				smooth: true,
				pointSize: 0,
				lineWidth: 0,
				fillOpacity: 0.85,
				data: [{
						period: '2015 Q1',
						iphone: 2668,
						ipad: null,
						itouch: 2649
					},
					{
						period: '2015 Q2',
						iphone: 15780,
						ipad: 13799,
						itouch: 12051
					},
					{
						period: '2015 Q3',
						iphone: 12920,
						ipad: 10975,
						itouch: 9910
					},
					{
						period: '2015 Q4',
						iphone: 8770,
						ipad: 6600,
						itouch: 6695
					},
					{
						period: '2016 Q1',
						iphone: 10820,
						ipad: 10924,
						itouch: 12300
					},
					{
						period: '2016 Q2',
						iphone: 9680,
						ipad: 9010,
						itouch: 7891
					},
					{
						period: '2016 Q3',
						iphone: 4830,
						ipad: 3805,
						itouch: 1598
					},
					{
						period: '2016 Q4',
						iphone: 15083,
						ipad: 8977,
						itouch: 5185
					},
					{
						period: '2017 Q1',
						iphone: 10697,
						ipad: 4470,
						itouch: 2038
					},

				],
				lineColors: ['#eb6f6f', '#926383', '#eb6f6f'],
				xkey: 'period',
				redraw: true,
				ykeys: ['iphone', 'ipad', 'itouch'],
				labels: ['All Visitors', 'Returning Visitors', 'Unique Visitors'],
				pointSize: 2,
				hideHover: 'auto',
				resize: true
			});
		});
	</script>
	<!-- calendar -->
	<script type="text/javascript" src="js/monthly.js"></script>
	<script type="text/javascript">
		var deleteQuotation = function(span) {
			if ($(span).hasClass("quo-0")) {
				return false;
			} else {
				console.log("quotationId: ", span);
				$.ajax({
					type: 'post',
					url: 'index_backend.php',
					data: {
						quotationId: $(span).attr("data-quoId")
					},
					success: function(response) {
						$("#quotation-" + $(span).attr("data-quoId")).hide();
					}
				});
			}

		};

		$(window).load(function() {
			// var $table = $('table.max-10');
			// $table.floatThead();

			$('#mycalendar').monthly({
				mode: 'event',

			});

			$('#mycalendar2').monthly({
				mode: 'picker',
				target: '#mytarget',
				setWidth: '250px',
				startHidden: true,
				showTrigger: '#mytarget',
				stylePast: true,
				disablePast: true
			});

			switch (window.location.protocol) {
				case 'http:':
				case 'https:':
					// running on a server, should be good.
					break;
				case 'file:':
					alert('Just a heads-up, events will not work when run locally.');
			}

		});
	</script>
    <script>
        $(document).ready(function() {
            $("table").ddTableFilter();
            $('select').addClass('w3-select');
            $('select').select2();
        });
    </script>

</body>

</html>