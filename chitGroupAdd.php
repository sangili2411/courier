<!DOCTYPE html>
<html lang="en">

<?php
include 'dbConn.php';
?>

<style>
    .m-42 {
        margin-left: 42%;
    }

    .m-t-4 {
        margin-top: 4%;
    }

    .add-border-bottom {
        border-bottom: 1px solid #78cc0c;
        max-width: 98%;
        margin-left: 1% !important;
    }

    .panel {
        color: black;
    }

    .bdr-btm {
        border-bottom: 1px solid red;
        max-width: 98%;
        margin-left: 1% !important;
    }

    .bdr-btm-orange {
        border-bottom: 1px solid #ff8400;
        max-width: 98%;
        margin-left: 1% !important;
    }

    .panel-body {
        margin-top: 1.5%;
    }

    .t-1-5 {
        margin-top: 1.5%;
    }

    .select2-container {
        width: 100% !important;
    }

    .top-1 {
        margin-top: 1%;
    }

    .hide {
        display: none;
    }

    .arrow {
        margin: -1% 1% 1% 1%;
        font-size: xx-large;
    }
</style>

<script>
    let chitAmountDetails = [];
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
                                                ADD CHIT GROUP <br>
                                                <span id="group-name"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-info pull-right m-t-p5" onclick="window.location.href='chitGroupView.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="group-name">Group Name: <span class="mandatory-astrick">*</span></label>
                                        <input type="text" class="form-control" id="group-name" placeholder="Enter Group Name">
                                    </div>

                                    <div class="form-group">
                                        <label for="no-of-members">No of Members:</label>
                                        <input type="number" class="form-control" id="no-of-members" name="no-of-members" placeholder="Enter No Of Members"> <!-- onchange="noOfMembersChanged(this)" -->
                                    </div>

                                    <div class="form-group">
                                        <label for="chit-name">Chit Name:</label>
                                        <select class="form-control" id="chit-name" onchange="chitNameSelectChanged(this)">
                                            <option value=""> -- Select Chit -- </option>
                                            <?php
                                            $userId = $_SESSION['userId']; // decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);
                                            echo $selectMemberSql = "SELECT * FROM chit_details WHERE CREATED_BY_AGENT = " . $userId . " ORDER BY CHIT_NAME";
                                            $result = mysqli_query($conn, $selectMemberSql);
                                            while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                                <option value="<?php echo $row['CHIT_MASTER_ID']; ?>"><?php echo $row['CHIT_NAME'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="no-of-chits">No of Chits:</label>
                                        <input type="number" class="form-control" id="no-of-chits" name="no-of-chits" placeholder="Enter No Of chits" onchange="noOfMembersChanged(this, chitAmountDetails)">
                                    </div>

                                    <div class="form-group">
                                        <label for="start-date">Start Date:</label>
                                        <input type="date" class="form-control" id="start-date" name="start-date" required onchange="calculateChitDates(this)">
                                    </div>

                                    <div class="form-group">
                                        <label for="end-date">End Date:</label>
                                        <input type="date" class="form-control" id="end-date" name="end-date" required>
                                    </div>

                                    <input type="hidden" id="row-num" value="0" />

                                    <div class="panel-group">
                                        <div class="panel panel-default m-t-4" style="background-color: aliceblue;">
                                            <br>
                                            <div class="panel-heading a-hover" id="accordion" data-toggle="collapse" data-parent="#accordion" href="#chit-details">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#chit-details" style="color: #ffac00;">Chit Amount Details</a>
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#chit-details" style="color: #ffac00;" class="arrow pull-right">
                                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="chit-details" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    <div class="row bdr-btm">
                                                        <div class="col-sm-1">
                                                            <span>வ.எண்</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>சீட்டு மாதம்</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>தொகை</span>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <span>கசர்</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>பட்டுவாடா</span>
                                                        </div>
                                                    </div>

                                                    <div id="chit-details-body"></div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default m-t-4" style="background-color: antiquewhite;">
                                            <br>
                                            <div class="panel-heading a-hover" data-toggle="collapse" data-parent="#accordion" href="#member-details">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#member-details" style="color: brown;">Member Details</a>
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#member-details" style="color: brown;" id="a-mem-details"
                                                        class="arrow pull-right caret-down">
                                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="member-details" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <div class="form-group">
                                                        <div class="row bdr-btm">
                                                            <div class="col-sm-2">&nbsp;</div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="member-select" onchange="memberSelectChanged(this)">
                                                                        <option value=""> -- Select Member -- </option>
                                                                        <?php
                                                                        $userId = decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);
                                                                        echo $selectMemberSql = "SELECT * FROM members WHERE CREATED_BY_AGENT = " . $userId . " ORDER BY MEMBER_NAME";
                                                                        $result = mysqli_query($conn, $selectMemberSql);
                                                                        while ($row = mysqli_fetch_array($result)) {
                                                                        ?>
                                                                            <option value="<?php echo $row['MEMBER_ID']; ?>"><?php echo $row['MEMBER_NAME'] . " - " . $row['MEMBER_MOBILE'] ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <button type="button" class="btn btn-primary btn-sm" onclick="addMembers()">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                            <div class="col-sm-2">&nbsp;</div>
                                                        </div>
                                                        <div id="members-div">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success m-45" onclick="validateAndSaveRecord(this)">
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
<!-- Cutom JS File -->
<script src="./js/chits/addEditChitGroup.js"></script>
<script src="./js/chits/numberInputPreventScroll.js"></script>

<script>
    let changeCaret = function(ahref) {
        if ($(ahref).hasClass("arrow-down")) {
            $(ahref).text('<i class="fa fa-caret-up" aria-hidden="true"></i>');
        } else {
            $(ahref).text('<i class="fa fa-caret-down" aria-hidden="true"></i>')
        }
    };

    let validateAndSaveRecord = function(button) {
        $(button).attr("disabled", true);
        $(button).html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="font-size: small;"></i> <span>Saving</span>')
        let groupName = $("#group-name").val();
        let noOfMembers = $("#no-of-members").val();
        let noOfChits = $("#no-of-chits").val();
        let startDate = $("#start-date").val();
        let endDate = $("#end-date").val();
        let membersArrayObj = getMembersObject();
        let chitAmountDetails = getChitAmountDetails();
        /* Validations */
        if (groupName == undefined || groupName == null || groupName == "") {
            alert("❌ Group Name is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (noOfMembers == undefined || noOfMembers == null || noOfMembers == "" || isNaN(parseInt(noOfMembers))) {
            alert("❌ No Of Members is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (noOfChits == undefined || noOfChits == null || noOfChits == "" || isNaN(parseInt(noOfChits))) {
            alert("❌ No Of Chits is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (startDate == undefined || startDate == null || startDate == "") {
            alert("❌ Start Date is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (endDate == undefined || endDate == null || endDate == "") {
            alert("❌ End Date is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (membersArrayObj.length != noOfMembers) {
            alert("❌ No Of Memebers is not equal to " + noOfMembers);
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (chitAmountDetails == false) {
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (chitAmountDetails && chitAmountDetails.length != noOfChits) {
            alert("❌ Number Of Chits Amounts is not equal to " + noOfChits);
            $(button).attr("disabled", false).html('Save');
            return false;
        } else {
            /* All validation passed - Save the data */
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    saveNewGroup: 1,
                    groupName: groupName,
                    noOfMembers: noOfMembers,
                    noOfChits: noOfChits,
                    startDate: startDate,
                    endDate: endDate,
                    membersArrayObj: JSON.stringify(membersArrayObj),
                    chitAmountDetails: JSON.stringify(chitAmountDetails),
                    agentId: <?php echo decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3); ?>
                },
                success: function(responce) {
                    if (responce.toString().includes("Insert Error")) {
                        alert("❌ Some error while saving \n Please try again after sometime");
                    } else {
                        alert("✔️ Data Saved Successfully!");
                        window.location.reload();
                    }
                }
            });
        }
    };

    let chitNameSelectChanged = function(select) {
        let chitMasterId = $(select).val();
        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                viewChitMasterDetails: 1,
                groupId: chitMasterId
            },
            success: function(response) {
                let resultJson = JSON.parse(response);
                chitAmountDetails = JSON.parse(resultJson["CHIT_AMOUNT"]);
                $("#no-of-chits").val(chitAmountDetails[0]["NO_OF_CHITS"]).trigger('change');
            }
        });
    };

    $(document).ready(function() {
        $("#member-select").select2();
        $("#chit-name").select2();
    });
</script>

</html>