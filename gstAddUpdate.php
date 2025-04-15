<!DOCTYPE html>

<?php
include 'dbConn.php';
?>

<html lang="en">

<link rel="stylesheet" href="./css/table-filter.css">

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
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1 text-center">
                                                <h2 class="m-t-p5 mb-0">GST</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="form-group">
                                        <label for="gst">GST: <span class="mandatory-astrick">*</span></label>
                                        <input type="number" class="form-control" id="gst" placeholder="Enter GST Value">
                                    </div>

                                    <div class="form-group flex-grow-1 text-center">
                                        <button type="button" class="btn btn-success" onclick="validateAndSaveRecords()">
                                            <i class="fa fa-floppy-o font-medium" aria-hidden="true"></i>
                                            Save
                                        </button>
                                    </div>

                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>GST</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                          
                                        </table>
                                    </div>

                                    <!-- Modal For Edit -->
                                    <div id="edit-gst-modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title pull-left">Edit GST</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group" style="display: none;">
                                                        <label for="gst-id">Id:</label>
                                                        <input type="text" class="form-control" id="gst-id">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-gst">GST:</label>
                                                        <input type="number" class="form-control" id="edit-gst">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" onclick="updateRecord()">
                                                        <i class="fa fa-floppy-o" aria-hidden="true" style="font-size: medium !important;"></i>
                                                        Update
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">&times; Close</button>
                                                </div>
                                            </div>

                                        </div>
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
<!-- Prevent Number Scrolling -->
<script src="./js/chits/numberInputPreventScroll.js"></script>
<script>
    let validateAndSaveRecords = function() {
        let gst = $("#gst").val();

        if (gst == undefined || gst == null || gst == "") {
            alert("❌ GST Value is mandatory!");
            return false;
        } else {
            $.ajax({
                url: "dataOperationsAdmin.php",
                type: "POST",
                data: {
                    addGst: 1,
                    gst: gst
                },
                success: function(response) {
                    if (response.toString() == "Insert Successful") {
                        alert("✔️ GST Added Successfully!");
                        window.location.reload();
                    } else if (response.toString() == "GST_ALREADY_EXISTS") {
                        alert("❌ GST Already Exists.");
                        return false;
                    } else {
                        alert("🚨 Some error had occured. \nPlease try again");
                        return false;
                    }
                }
            });
        }
    };

    let editRecord = function(gstId, button) {
        $("#gst-id").val(gstId);
        $("#edit-gst").val($(button).attr("data-gst"));
        $('#edit-gst-modal').modal('show');
    };

    let updateRecord = function() {
        let gstId = $("#gst-id").val();
        let gst = $("#edit-gst").val();

        if (gst == undefined || gst == null || gst == "") {
            alert("❌ GST is mandatory!");
            return false;
        } else {
            $.ajax({
                url: "dataOperationsAdmin.php",
                type: "POST",
                data: {
                    updateExistingGst: gstId,
                    gst: gst
                },
                success: function(response) {
                    if (response.toString().includes("Update Successful")) {
                        alert("✔️ GST Updated Successfully!");
                        window.location.reload();
                    } else {
                        alert("🚨 Some error had occured. \nPlease try again")
                    }
                }
            });
        }

    };

    let deleteRecord = function(gstId, gst) {
        if (confirm("⚠️ Are you sure to delete GST " + gst)) {
            $.ajax({
                url: "dataOperationsAdmin.php",
                type: "POST",
                data: {
                    deleteGstId: gstId
                },
                success: function(response) {
                    if (response.toString().includes("Delete Successful")) {
                        alert("✔️ GST Deleted Successfully!");
                        window.location.reload();
                    }
                }
            });
        }
    };

    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>