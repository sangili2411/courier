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
                                                ADD CHIT CONFIGURATION <br>
                                                <span id="group-name"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-info pull-right m-t-p5" onclick="window.location.href='chitMasterView.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="chit-amount">Chit Name: <span class="mandatory-astrick">*</span></label>
                                        <input type="text" class="form-control" id="chit-amount" placeholder="Enter Chit Name">
                                    </div>

                                    <div class="form-group">
                                        <label for="no-of-chits">No of Chits: <span class="mandatory-astrick">*</span></label></label>
                                        <input type="number" class="form-control" id="no-of-chits" name="no-of-chits" placeholder="Enter No Of chits" onchange="noOfChitsChanged(this)">
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
                                                        <div class="col-sm-4">
                                                            <span>தொகை</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>கசர்</span>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <span>பட்டுவாடா</span>
                                                        </div>
                                                    </div>

                                                    <div id="chit-details-body"></div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success m-45 m-t-4" onclick="validateAndSaveRecord(this)">
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
    let validateAndSaveRecord = function(button) {
        $(button).attr("disabled", true);
        $(button).html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="font-size: small;"></i> <span>Saving</span>')
        let chitAmount = $("#chit-amount").val();
        let noOfChits = $("#no-of-chits").val();
        let chitAmountDetails = getChitAmountDetailsForChitsMaster();
        /* Validations */
        if (chitAmount == undefined || chitAmount == null || chitAmount == "") {
            alert("❌ Chit Amount is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (noOfChits == undefined || noOfChits == null || noOfChits == "" || isNaN(parseInt(noOfChits))) {
            alert("❌ No Of Chits is mandatory!");
            $(button).attr("disabled", false).html('Save');
            return false;
        } else if (chitAmountDetails == false) {
            // Chit Amount Details Not Fully Entered
            // Alert Already Displayed.. 
        } else {
            /* All validation passed - Save the data */
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    saveNewChitMaster: 1,
                    chitAmount: chitAmount,
                    noOfChits: noOfChits,
                    chitAmountDetails: JSON.stringify(chitAmountDetails),
                    agentId: <?php echo $_SESSION['userId']; ?>
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

    $(document).ready(function() {});
</script>

</html>