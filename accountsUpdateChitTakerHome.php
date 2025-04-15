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
                                                UPDATE CHIT TAKER <br>
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
                                        <div class="col-sm-3">&nbsp;</div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="group-name">Chit Group Name: <span class="mandatory-astrick">*</span></label>
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
                                        <div class="col-sm-3">&nbsp;</div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary m-45" onclick="showGroupDetails(this)">
                                            <i class="fa fa-snowflake-o" aria-hidden="true"></i>
                                            Show Details
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
<script src="./js/chits/addEditChitGroup.js"></script>
<script>
    let showGroupDetails = function() {
        if ($("#group-select").val() != "") {
            window.location.href = "accountsUpdateChitTaker.php?groupId=" + $("#group-select").val();
        } else {
            alert("❌ Please select the Group Name");
            return false;
        }
    };

    $(document).ready(function() {
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>