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
                                            <h2 class="m-t-p5" style="text-align: center;" style="text-align: center; font-size: 20px; color: #0c1211;">
                                                ADD MEMBER <br>
                                                <span id="group-name"></span>
                                            </h2>

                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary pull-right m-t-p5" onclick="window.location.href='memberView.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="member-name">Member Name: <span class="mandatory-astrick">*</span></label>
                                        <input type="text" class="form-control" id="member-name" placeholder="Enter Name">
                                    </div>

                                    <div class="form-group">
                                        <label for="mobile">Mobile: <span class="mandatory-astrick">*</span></label>
                                        <input type="number" class="form-control" id="mobile" placeholder="Enter Mobile">
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Address:</label>
                                        <textarea class="form-control" id="address">
                                        </textarea>
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

<script src="./js/validateInput.js"></script>
<script src="./js/chits/numberInputPreventScroll.js"></script>
<script>
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
                memberAddress: memberAddress,
                agentId: <?php echo $_SESSION['userId']; ?>
            },
            success: function(response) {
                if (response.toString() == "Insert Successful") {
                    alert("✔️ Member Added Successfully!");
                    window.location.reload();
                } else if (response.toString() == "MOB_ALREADY_EXISTS") {
                    alert("❌ Mobile Number Already Exists.");
                    return false;
                } else {
                    alert("🚨 Some error had occured. \nPlease try again");
                    return false;
                }
            }
        });
    }
</script>

</html>