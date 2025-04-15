<?php
session_start();
include 'dbConn.php';

date_default_timezone_set('Asia/Kolkata');
$date_1 = date('d-m-Y H:i');
$date = date('Y-m-d', strtotime($date_1));

if (isset($_GET['fromDate'])) {
    $fromDate = $_GET['fromDate'];
    $toDate = $_GET['toDate'];
    $sql = "SELECT FROM_PLACE, COUNT(*) AS COUNT FROM booking_details WHERE BOOKING_DATE BETWEEN '$fromDate' AND '$toDate' GROUP BY FROM_PLACE ORDER BY FROM_PLACE";
}
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

    .select2-selection__rendered {
        background: #428bca;
        border-bottom: 1px solid #5796cd !important;
        border-color: #428bca;
        color: #0a0a0a !important;
    }

    .select2-selection {
        border: aliceblue !important;
    }

    .select2-results__option {
        background: white;
    }

    select {
        background: #428bca;
        border-bottom: 1px solid #5796cd !important;
        border-color: #428bca;
    }

    option {
        background: white;
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
        include 'header.php';
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
                                <!-- page start-->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <section class="panel">
                                            <header class="panel-heading" style="text-align: center; font-size: 20px; color: #0c1211;">
                                                COUNT Report (<?php echo $fromDate . " - " . $toDate ?>)
                                                <button type="button" class="btn btn-default btn-md pull-right" style="margin-top: 0.75em;" onclick="window.location.href='reportCountWise.php';">
                                                    <i class="fa fa-backward" aria-hidden="true" style="color: tomato;"></i>
                                                </button>
                                            </header>
                                            <br>
                                            <?php
                                            if (isset($conn) && $result = mysqli_query($conn, $sql)) {
                                                if (mysqli_num_rows($result) > 0) {
                                            ?>
                                                    <div class="panel-body">
                                                        <div class="">
                                                            
                                                <div id="table-data" class="table-responsive filterable max-30">
                                                    <table id="data-table" class="table table-striped tableFixHead">
                                                                    <thead>
                                                                        <tr class="filters" style="color:#0c1211;">
                                                                            <th style="color:#0c1211">#</th>
                                                                            <th style="color:#0c1211; text-align: center;">Branch Name</th>
                                                                            <th style="color:#0c1211">Booking Count</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <?php
                                                                    $i = 1;
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                                    ?>
                                                                        <tbody>
                                                                            <?php
                                                                            ?>
                                                                            <tr>
                                                                                <td style="color:#0c1211;">
                                                                                    <?php echo $i++; ?>
                                                                                </td>
                                                                                <td style="color:#0c1211"><?php echo $row['FROM_PLACE']; ?></td>
                                                                                <td style="color:#0c1211"><?php echo $row['COUNT']; ?></td>
                                                                            </tr>
                                                                        <?php
                                                                    }
                                                                        ?>
                                                                        </tbody>
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
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();
</script>

</html>