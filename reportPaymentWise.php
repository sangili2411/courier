<?php

include 'dbConn.php';
?>

<!DOCTYPE html>
<script>

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


                                <div class="row">
                                    <div class="col-lg-12">
                                        <section class="panel">
                                            <header class="panel-heading" style="text-align: center; font-size: 20px; color: #0c1211;">
                                                Payment Report - Branch Wise
                                            </header>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <!-- Branch Name -->
                                                    <!-- <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="branch-place">Branch Name<span class="mandatory-field">*</span></label>
                                            <select class="form-control" id="branch-name" name="branch-name">
                                                <option value=''>-- SELECT BRANCH NAME --</option>
                                                <?php
                                                $selectCity = "SELECT BRANCH_NAME FROM branch_details ORDER BY 1";
                                                if ($result = mysqli_query($conn, $selectCity)) {
                                                    if (mysqli_num_rows($result) > 0) {
                                                        while ($row = mysqli_fetch_array($result)) {
                                                ?>
                                                            <option><?php echo $row['BRANCH_NAME'] ?></option>
                                                <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div> -->
                                                    <div class="col-sm-2">&nbsp;</div>
                                                    <!-- From Date -->
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="from-date">From Date:</label>
                                                            <input type="date" class="form-control" id="from-date">
                                                        </div>
                                                    </div>
                                                    <!-- To Date -->
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="to-date">To Date:</label>
                                                            <input type="date" class="form-control" id="to-date">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">&nbsp;</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <button type="button" class="btn btn-success" onclick="showReport()" style="margin-left: 45%">
                                                            <i class="fa fa-eye" style="font-size: large;"></i>
                                                            &nbsp;Show Report
                                                        </button>
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
<script>
    $("#branch-name").select2();

    // let showReport = function() {
    function showReport() {
        let fromDate = $("#from-date").val();
        let toDate = $("#to-date").val();
        if (fromDate == undefined || fromDate == null || fromDate == "") {
            alert("⚠️ From Date is mandatory!");
            return false;
        } else if (toDate == undefined || toDate == null || toDate == "") {
            alert("⚠️ To Date is mandatory!");
            return false;
        } else {
            window.location.href = "reportPaymentWiseView.php?fromDate=" + fromDate + "&toDate=" + toDate;
        }
    };
</script>