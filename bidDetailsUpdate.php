<?php
include 'dbOperations.php';

if (isset($_GET['bidId'])) {
    $bidId = $_GET['bidId'];
    $groupId = $_GET['groupId'];
    $selectSql = "SELECT * FROM bids_details BD, groups_info GI WHERE GI.GROUP_ID = BD.GROUP_ID AND BD.BID_ID = $bidId AND BD.GROUP_ID = $groupId";

    $dbOperator = new DBOperations();
    $row = $dbOperator->getRecords($selectSql);
    if ($row != "No records") {
    }
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
                                            <div class="col-sm-10">
                                                <h2 class="m-50 m-t-p5">UPDATE BID DETAILS</h2>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-info btn-md pull-right m-t-p5" onclick="window.location.href='memberView.php'">
                                                    <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                                </button>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="group-name">Group Name: <span class="mandatory-astrick">*</span></label>
                                            <input type="text" class="form-control" id="group-name" value=<?php echo $row['GROUP_NAME']; ?>>
                                        </div>

                                        <div class="form-group">
                                            <label for="bid-date">ஏலத்தேதி: <span class="mandatory-astrick">*</span></label>
                                            <input type="date" class="form-control" id="bid-date" value="<?php echo $row['START_DATE'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="member-taken">எடுத்தவர் பெயர்: <span class="mandatory-astrick">*</span></label>
                                            <select class="form-control" id="member-taken" onchange="memberTakenChanged(this)">
                                                <option value="">-- SELECT MEMBER --</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="member-mobile">எடுத்தவர் கைபேசி: <span class="mandatory-astrick">*</span></label>
                                            <input type="text" class="form-control" id="member-mobile">
                                        </div>

                                        <div class="form-group">
                                            <label for="total-chit-amount">மொத்த சீட்டு தொகை: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="total-chit-amount" value="<?php echo $row['TOTAL_CHIT_AMOUNT']; ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="bid-amount">ஏலத்தொகை: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="bid-amount" onchange="bidAmountChanged(this)">
                                        </div>

                                        <div class="form-group">
                                            <label for="amount-given">ஏலம் எடுத்த நபருக்கு வழங்கப்படும் தொகை: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="amount-given" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="agent-commision">கமிஷன்: </label>
                                            <input type="number" class="form-control" id="agent-commision" value="<?php echo $row['COMMISION_AMOUNT_PER_MONTH']; ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="bid-amount-commision">ஏலத்தொகை - கமிஷன்: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="bid-amount-commision" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="kasar-date">கசர்: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="kasar-amount" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="monthly-amount">மாதாந்திர சீட்டு தொகை: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="monthly-amount" value="<?php echo $row['CHIT_AMOUNT_PER_MONTH']; ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="amount-to-be-given">கொடுக்க வேண்டிய தொகை: <span class="mandatory-astrick">*</span></label>
                                            <input type="number" class="form-control" id="amount-to-be-given" readonly>
                                        </div>

                                        <div class="form-group">
                                            <button type="button" class="btn btn-success m-45" onclick="validateAndSaveRecords()">
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
        let memberDetails = undefined;

        let validateAndSaveRecords = function() {
            let memberName = $("#member-name").val();
            let memberMobile = $("#mobile").val();
            let memberAddress = $("#address").val();

            if (memberName == undefined || memberName == null || memberName == "") {
                alert("❌ Member Name is mandatory!");
                return false;
            }
            if (memberMobile == undefined || memberMobile == null || memberMobile == "") {
                alert("❌ Member Mobile is mandatory!");
                return false;
            }
            if (memberMobile != null && memberMobile != "" && memberMobile.length > 0) {
                if (memberMobile.length != 10) {
                    alert("⚠️ Mobile Number should have 10 digits");
                    return false;
                }
            }

            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    addNewMember: 1,
                    memberName: memberName,
                    memberMobile: memberMobile,
                    memberAddress: memberAddress
                },
                success: function(response) {
                    if (response.toString() == "Insert Successful") {
                        alert("✔️ Member Added Successfully!");
                        window.location.reload();
                    } else {
                        alert("🚨 Some error had occured. \nPlease try again")
                    }
                }
            });
        }

        let bidAmountChanged = function(input) {
            let bidAmount = $(input).val();
            if (isNaN(parseInt(bidAmount))) {
                alert("🚨 Please enter a valid number");
                $(input).val('');
                return false;
            } else {
                bidAmount = parseInt(bidAmount);
                let totalChitAmount = parseInt($("#total-chit-amount").val());
                let amountNeedToBeGiven = totalChitAmount - bidAmount;
                $("#amount-given").val(amountNeedToBeGiven);
                let commisionAmount = parseInt($("#agent-commision").val());
                let newBidAmount = bidAmount - commisionAmount;
                $("#bid-amount-commision").val(newBidAmount);
                let noOfMembers = memberDetails.length;
                let kasarAmount = parseInt(newBidAmount / noOfMembers);
                $("#kasar-amount").val(kasarAmount);
                let monthlyAmount = parseInt($("#monthly-amount").val());
                let monthlyAmountToBePaid = monthlyAmount - kasarAmount;
                $("#amount-to-be-given").val(monthlyAmountToBePaid);
            }
        };

        let memberTakenChanged = function (select) {
            $("#member-mobile").val($(select).val());
        };

        $(document).ready(function() {
            memberDetails = <?php echo $row['MEMBER_DETAILS']; ?>;
            if (memberDetails != undefined) {
                for (let i = 0; i < memberDetails.length; i++) {
                    let memberName = memberDetails[i]["MEMBER_NAME"];
                    let memberMobile = memberDetails[i]["MEMBER_MOBILE"];
                    $('#member-taken').append($('<option>', {
                        value: memberMobile,
                        text: memberName
                    }));
                }
                $("#member-taken").select2();
            }
        });
    </script>

    </html>
<?php
} else {
    echo "<script>alert('Something went wrong!')</script>";
}
?>