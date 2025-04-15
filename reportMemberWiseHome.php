<?php
include 'dbConn.php';

?>
<!DOCTYPE html>
<html lang="en">

<style>

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
                                                MEMBER WISE REPORT <br>
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
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="member-name">Member Name: <span class="mandatory-astrick">*</span></label>
                                                <select class="form-control" id="member-select" onchange="memberSelectChanged(this)">
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
                                        <div class="col-sm-6" id="chit-month-div">
                                            <div class="form-group">
                                                <label for="month-select">Chit Month: <span class="mandatory-astrick">*</span></label>
                                                <select class="form-control" id="month-select">
                                                    <option value=""> -- Select Month -- </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success m-45" onclick="showMemberDetails(this)">
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
    let showMemberDetails = function() {
        if ($("#member-select").val() != "") {
            window.location.href = "reportMemberWise.php?memberId=" + $("#member-select").val() + "&month=" + $("#month-select").val();
        } else {
            alert("❌ Please select the Member Name");
            return false;
        }
    };

    let memberSelectChanged = function(select) {
        let memberId = $(select).val();
        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                getChitDatesForSelectedMember: 1,
                memberId: memberId
            },
            success: function(response) {
                $.when(appendOptionsToMonthSelect(JSON.parse(response)))
                    .then($("#month-select").select2());

            }
        });
    };

    $(document).ready(function() {
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>