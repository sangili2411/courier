<?php
include 'dbConn.php';
include 'CaesarCipher.php';
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

        <?php
        include 'header.php';

        $userId = $_SESSION['userId']; // decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);
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
                                <div class="form-validation">
                                    <div class="row">
                                        <div class="col-sm-2">&nbsp;</div>
                                        <div class="col-sm-8">
                                            <h2 class="m-t-p5" style="text-align: center;">
                                                DATE WISE REPORT <br>
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
                                                <label for="month-select">Start Date: <span class="mandatory-astrick">*</span></label>
                                                <input type="date" class="form-control" id="start-date" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="month-select">End Date: <span class="mandatory-astrick">*</span></label>
                                                <input type="date" class="form-control" id="end-date" onchange="endDateChanged(this)" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="chit-group-select">Chit Group:</label>
                                                <select class="form-control" id="chit-group-select" disabled>
                                                    <option value=""> -- Select Chit -- </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="member-name">Member Name:</label>
                                                <select class="form-control" id="member-select" disabled>
                                                    <option value=""> -- Select Member -- </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success m-45" onclick="showReport(this)">
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
    let appendOptionsToSelect = function(optionArray, selectId) {
        $('#' + selectId + ' option:not(:first)').remove();
        for (let i = 0; i < optionArray.length; i++) {
            $('#' + selectId)
                .append($("<option></option>")
                    .attr("value", optionArray[i])
                    .text(optionArray[i]));
        }
        $("#" + selectId).attr("disabled", false);
    };

    let endDateChanged = function(input) {
        if ($(input).val() == "") {
            alert("❌ Please Choose Start Date first");
            $(input).val('');
            return false;
        } else {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    getChitAndMemberNamesForGivenDate: 1,
                    startDate: $("#start-date").val(),
                    endDate: $(input).val(),
                    agentId: <?php echo $userId; ?>
                },
                success: function(response) {
                    let responseArray = JSON.parse(response);

                    const groupNames = Array.from(
                        new Set(responseArray.map(item => item.GROUP_NAME))
                    ).sort();

                    const memberNames = Array.from(
                        new Set(responseArray.map(item => item.MEMBER_NAME))
                    ).sort();

                    $.when(appendOptionsToSelect(groupNames, "chit-group-select"))
                        .then($("#chit-group-select").select2());
                    $.when(appendOptionsToSelect(memberNames, "member-select"))
                        .then($("#member-select").select2());

                }
            });
        }
    };

    let showReport = function() {
        let startDate = $("#start-date").val();
        let endDate = $("#end-date").val();
        let chitGroupName = $("#chit-group-select").val();
        let memberName = $("#member-select").val();

        if (startDate == "") {
            alert("❌ Start Date is mandatory!");
            return false;
        } else if (endDate == "") {
            alert("❌ End Date is mandatory!");
            return false;
        } else {
            window.location.href = "reportDateWise.php?startDate=" + startDate + "&endDate=" + endDate + "&groupName=" + chitGroupName + "&memberName=" + memberName;
        }
    };

    $(document).ready(function() {
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>