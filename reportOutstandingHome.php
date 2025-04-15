<?php
include 'dbConn.php';

?>
<!DOCTYPE html>
<html lang="en">

<style>
    .select2-container {
        width: 100% !important;
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
                                            <h2 class="m-t-p5" style="text-align: center;">
                                                OUTSTANDING REPORT <br>
                                                <span id="group-name"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <!-- <button type="button" class="btn btn-success pull-right m-t-p5" onclick="window.location.href='chitGroupAdd.php'">
                                                <i class="fa fa-plus" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button> -->
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="group-name">Report Type: <span class="mandatory-astrick">*</span></label>
                                                <select class="form-control" id="report-type-select" onchange="reportTypeChanged(this)">
                                                    <option value=""> -- Select Report Type -- </option>
                                                    <option value="chit-wise"> Chit Group Wise </option>
                                                    <option value="member-wise"> Member Wise </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="chit-group-div" style="display: none;">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="group-name">Chit Group Name: <span class="mandatory-astrick">*</span></label></br>
                                                <select class="form-control" id="group-select">
                                                    <option value=""> -- Select Group -- </option>
                                                    <?php
                                                    $userId = $_SESSION['userId']; // decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);
                                                    $selectGroupSql = "SELECT DISTINCT GROUP_ID, GROUP_NAME FROM v_chit_accounts_details WHERE CREATED_BY_AGENT = " . $userId . " ORDER BY GROUP_NAME";
                                                    $result = mysqli_query($conn, $selectGroupSql);
                                                    while ($row = mysqli_fetch_array($result)) {
                                                    ?>
                                                        <option value="<?php echo $row['GROUP_ID']; ?>"><?php echo $row['GROUP_NAME']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="member-div" style="display: none;">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="member-name">Member Name: <span class="mandatory-astrick">*</span></label></br>
                                                <select class="form-control" id="member-select">
                                                    <option value=""> -- Select Member -- </option>
                                                    <?php
                                                    $userId = $_SESSION['userId']; // decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);
                                                    $selectGroupSql = "SELECT DISTINCT MEMBER_ID, CONCAT(MEMBER_NAME, ' - ', MEMBER_MOBILE) AS MEMBER_NAME FROM v_chit_accounts_details WHERE CREATED_BY_AGENT = " . $userId . " ORDER BY MEMBER_NAME";
                                                    $result = mysqli_query($conn, $selectGroupSql);
                                                    while ($row = mysqli_fetch_array($result)) {
                                                    ?>
                                                        <option value="<?php echo $row['MEMBER_ID']; ?>"><?php echo $row['MEMBER_NAME']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success m-45" onclick="showOutstandingDetails(this)">
                                            <i class="fa fa-podcast" aria-hidden="true"></i>
                                            Show Report
                                        </button>
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
<script src="./js/chits/date-option-appender.js"></script>
<script>
    let reportTypeChanged = function(select) {
        if ($(select).val() == "chit-wise") {
            $("#chit-group-div").show();
            $("#member-div").hide();
        } else {
            $("#chit-group-div").hide();
            $("#member-div").show();
        }
    };

    let showOutstandingDetails = function() {
        let reportType = $("#report-type-select").val();
        if (reportType == "chit-wise") {
            if ($("#group-select").val() != "") {
                window.location.href = "reportOutstandingChitWise.php?groupId=" + $("#group-select").val();
            } else {
                alert("❌ Please select the Group Name");
                return false;
            }
        } else {
            if ($("#member-select").val() != "") {
                window.location.href = "reportOutstandingMemberWise.php?memberId=" + $("#member-select").val();
            } else {
                alert("❌ Please select the Member Name");
                return false;
            }
        }
    };

    $(document).ready(function() {
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>