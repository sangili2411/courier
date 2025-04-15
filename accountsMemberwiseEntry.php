<?php
include 'dbConn.php';
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="./css/chits/add-edit-group.css">
<style>
    .mrg-btm-1 {
        margin-bottom: 1em;
    }
</style>

<script>
    let noOfChits = 0;
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
                                                MEMBER WISE ENTRY <br>
                                                <span id="group-name"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-warning btn-sm pull-right m-t-p5" onclick="window.location.reload()">
                                                <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
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
                                        <button type="button" class="btn btn-primary m-45" onclick="generateAmountGrid(this)">
                                            <i class="fa fa-snowflake-o" aria-hidden="true"></i>
                                            Show
                                        </button>
                                    </div>

                                    <div id="data-div" style="display: none;">
                                        <div class="row bdr-btm">
                                            <div class="col-sm-1">
                                                <label>#</label>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Group Name</label>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Chit Amount</label>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Advance Amount</label>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Existing Balance</label>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Amount Paid</label>
                                            </div>
                                            <div class="col-sm-1">
                                                <label>New Balance</label>
                                            </div>
                                        </div>

                                        <div id="details-div"></div>

                                        <div class="row t-1-5">
                                            <button type="button" class="btn btn-success m-45" onclick="saveFieldValues(this)">
                                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                Save
                                            </button>
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

<script>
    function formatDate(strDate) {
        const date = new Date(strDate);
        if (isNaN(date.getTime())) { // Check if the date is valid
            return "Invalid Date";
        }
        const options = {
            year: 'numeric',
            month: 'short'
        };
        return date.toLocaleDateString('en-US', options);
    }

    let appendOptionsToMonthSelect = function(dateArray) {
        $('#month-select option:not(:first)').remove();
        for (let i = 0; i < dateArray.length; i++) {
            $('#month-select')
                .append($("<option></option>")
                    .attr("value", dateArray[i]["CHIT_MONTH"])
                    .text(formatDate(dateArray[i]["CHIT_MONTH"])));
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

    let nullCheck = function(val) {
        return val == null ? '' : val;
    }

    function loadScript(src) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = () => {
            console.log(`${src} has been loaded`);
        };
        script.onerror = () => {
            console.error(`Failed to load ${src}`);
        };
        document.body.appendChild(script);
    }

    let generateGrid = function(accountDetailsJson) {
        let totalMonthlyAmount = 0;
        let totalPaidAmount = 0;
        let totalExistingBalance = 0;

        for (let i = 0; i < accountDetailsJson.length; i++) {
            let monthlyAmount = parseFloat(accountDetailsJson[i]["MONTHLY_AMOUNT"]) || 0;
            let paidAmount = parseFloat(nullCheckForFloat(accountDetailsJson[i]["PAID_AMOUNT"])) || 0;
            let existingBalance = monthlyAmount - paidAmount;

            totalMonthlyAmount += monthlyAmount;
            totalPaidAmount += paidAmount;
            totalExistingBalance += existingBalance;

            let htmlString = '<div class="row bdr-btm-orange t-1-5" id=row-"' + i + '">' +
                '<div class="col-sm-1">' +
                '    <label>' + (i + 1) + '</label>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '    <label id="group-name-' + i + '" data-account-id="' + accountDetailsJson[i]["ACCOUNT_ID"] + '" data-group-id="' + accountDetailsJson[i]["GROUP_ID"] + '">' + accountDetailsJson[i]["GROUP_NAME"] + '</label>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '    <label id="monthly-amount-' + i + '">' + accountDetailsJson[i]["MONTHLY_AMOUNT"] + '</label>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '    <label id="advance-amount-' + i + '">' + nullCheckForFloat(accountDetailsJson[i]["PAID_AMOUNT"]) + '</label>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '    <label id="existing-balance-' + i + '">' + nullCheckForFloat(accountDetailsJson[i]["MONTHLY_AMOUNT"] - accountDetailsJson[i]["PAID_AMOUNT"]) + '</label>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '    <input type="number" class="form-control mrg-btm-1" id="input-amount-paid-' + i + '" onchange="dataChange(' + i + ', this)"/>' +
                '</div>' +
                '<div class="col-sm-1">' +
                '    <label id="current-balance-' + i + '"></label>' +
                '</div>' +
                '</div>';
            $("#details-div").append(htmlString);
            loadScript('./js/chits/numberInputPreventScroll.js');
        }
        // Append the summary row
        let summaryRowHtml = '<div class="row bdr-btm-orange t-1-5 summary-row">' +
            '<div class="col-sm-1">' +
            '    <label><b>Total</b></label>' +
            '</div>' +
            '<div class="col-sm-2">' +
            '</div>' +
            '<div class="col-sm-2">' +
            '    <label><b>' + totalMonthlyAmount.toFixed(2) + '</b></label>' +
            '</div>' +
            '<div class="col-sm-2">' +
            '    <label><b>' + totalPaidAmount.toFixed(2) + '</b></label>' +
            '</div>' +
            '<div class="col-sm-2">' +
            '    <label><b>' + totalExistingBalance.toFixed(2) + '</b></label>' +
            '</div>' +
            '<div class="col-sm-2">' +
            '</div>' +
            '<div class="col-sm-1">' +
            '</div>' +
            '</div>';
        $("#details-div").append(summaryRowHtml);

        noOfChits = accountDetailsJson.length;
    };

    function nullCheckForFloat(value) {
        return value != null ? parseFloat(value) : 0;
    }

    let generateAmountGrid = function(btn) {
        let memberId = $("#member-select").val();
        let month = $("#month-select").val();
        if (memberId == "") {
            alert("❌ Member Name is mandatory");
            return false;
        } else if (month == "") {
            alert("❌ Month is mandatory");
            return false;
        } else {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    getChitPaymentDetailsForSelectedMember: 1,
                    memberId: memberId,
                    month: month
                },
                success: function(response) {
                    generateGrid(JSON.parse(response));
                    $("#data-div").show();
                    $(btn).attr("disabled", true);
                    $("#member-select").attr("disabled", true);
                    $("#month-select").attr("disabled", true);
                }
            });
        }
    };

    let dataChange = function(rowId, input) {
        let monthlyAmount = parseInt($("#monthly-amount-" + rowId).text());
        let advanceAmount = parseInt($("#advance-amount-" + rowId).text());
        let existingAmount = parseInt($("#existing-balance-" + rowId).text());
        let paidAmount = $(input).val();
        if (isNaN(parseInt(paidAmount))) {
            alert("❌ Please enter a valid number for Paid Amount");
            $(input).val('');
            return false;
        }
        if (isNaN(advanceAmount)) {
            advanceAmount = 0;
        }
        if (isNaN(existingAmount)) {
            existingAmount = 0;
        }
        paidAmount = parseInt(paidAmount);
        let totalPaidAmount = advanceAmount + paidAmount;
        let balanceAmount = monthlyAmount - totalPaidAmount;
        if (balanceAmount < 0) {
            alert("❌ New Balance is exceeding Chit Amount \n Please enter the correct amount");
            $(input).val('');
            return false;
        } else {
            $("#current-balance-" + rowId).text(balanceAmount);
        }
    };

    let saveFieldValues = function(btn) {
        if (confirm("🚨 Once the changes are saved it can't be updated \nAre you sure to proceed")) {
            $(btn).attr("disabled", true);
            $(btn).html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="font-size: small;"></i> <span>Saving</span>')

            let dataArray = [];
            let canSave = false;
            for (let i = 0; i < noOfChits; i++) {
                let dataObj = {};
                let accountId = $("#group-name-" + i).attr("data-account-id");
                let groupId = $("#group-name-" + i).attr("data-group-id");
                let monthlyChitAmount = $("#monthly-amount-" + i).text();
                let advanceAmount = $("#advance-amount-" + i).text();
                let existingBalance = $("#existing-balance-" + i).text();
                let paidAmount = $("#input-amount-paid-" + i).val();
                let currentBalance = $("#current-balance-" + i).text();
                if (isNaN(parseInt(paidAmount)) && paidAmount != "") {
                    alert("⚠️ Please enter the valid amount for Paid Amount");
                    canSave = false;
                    return false;
                } else {
                    dataObj["ACCOUNT_ID"] = accountId;
                    dataObj["GROUP_ID"] = groupId;
                    dataObj["MEMBER_ID"] = $("#member-select").val();
                    dataObj["CHIT_MONTH"] = $("#month-select").val();
                    dataObj["MONTHLY_AMOUNT"] = monthlyChitAmount;
                    dataObj["ADVANCE_AMOUNT"] = advanceAmount;
                    dataObj["EXISTING_BALANCE"] = existingBalance;
                    dataObj["PAID_AMOUNT"] = paidAmount;
                    dataObj["CURRENT_BALANCE"] = currentBalance;
                    dataArray.push(dataObj);
                    canSave = true;
                }
            }
            if (canSave) {
                $.ajax({
                    url: "dataOperations.php",
                    type: "POST",
                    data: {
                        updateChitWisePayment: 1,
                        dataArray: JSON.stringify(dataArray)
                    },
                    success: function(response) {
                        if (response.toString().includes("Update Successful")) {
                            alert("👍 Payment Updated Successfully");
                            window.location.reload();
                        } else {
                            alert("😥 Some error occured while updating \n Please try again sometime");
                            window.location.reload();
                        }
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        $("#member-select").select2();
    });
</script>

</html>