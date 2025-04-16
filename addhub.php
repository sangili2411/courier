

<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="./css/table-filter.css">


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
                                                <h2 class="m-t-p5 mb-0">HUB MANAGEMENT</h2>
                                            </div>

                                          
                                        </div>
                                    </div>
                                            <div class="panel-body">
                                                <div class="position-center">
                                                    <form action=" " role="form" method="post">
                                                        <div class="form-group">
                                                            <label for="hub">Hub Name<span class="mandatory-field text-danger">*</span></label>
                                                            <input type="text" required class="form-control" id="hub"
                                                                placeholder="Enter Hub Name" name="hub" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="hub">Hub Mobile<span class="mandatory-field text-danger">*</span></label>
                                                            <input type="number" required class="form-control" id="hubMobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                                placeholder="Enter Mobile number" name="hub" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="hub">Hub Address<span class="mandatory-field text-danger">*</span></label>
                                                          <textarea name="hubAddress" id="hubAddress" rows="3" class="form-control"></textarea>
                                                        </div>
                                                        <button class="btn btn-success mb-3" style="margin-left: 40%;" onclick="validateAndSaveRecords()">
                                                            <div class="fa fa-floppy-o font-medium menu-icon" aria-hidden="true"></div>
                                                            &nbsp; Save
                                                        </button>
                                                    </form>
                                                </div>

                                                <div id="table-data" class="table-responsive filterable max-30">
                                                    <table id="data-table" class="table table-striped tableFixHead">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Hub</th>
                                                                <th>Mobile</th>
                                                                <th>Address</th>
                                                                <th>Edit</th>
                                                                <th>Delete</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sql = "SELECT * FROM hub ORDER BY 2";
                                                            $result = mysqli_query($conn, $sql);
                                                            if (mysqli_num_rows($result) > 0) {
                                                                $i = 1;
                                                                while ($row = mysqli_fetch_array($result)) { // [{}]
                                                            ?>
                                                                    <tr>
                                                                        <td><?php echo $i++; ?></td>
                                                                        <td><?php echo $row['HUB_NAME']; ?></td>
                                                                        <td><?php echo $row['HUB_MOBILE']; ?></td>
                                                                        <td><?php echo $row['HUB_ADDRESS']; ?></td>
                                                                        <td>
                                                                            <a class="a-edit-icon"
                                                                                data-id="<?php echo $row['HUB_ID']; ?>"
                                                                                data-name="<?php echo htmlspecialchars($row['HUB_NAME']); ?>"
                                                                                data-mobile="<?php echo htmlspecialchars($row['HUB_MOBILE']); ?>"
                                                                                data-address="<?php echo htmlspecialchars($row['HUB_ADDRESS']); ?>"
                                                                                onclick="editData(this)">
                                                                                <i class="fa fa-pencil font-x-large" aria-hidden="true"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td>
                                                                            <a class="a-delete-icon"
                                                                                data-id="<?php echo $row['HUB_ID']; ?>"
                                                                                data-name="<?php echo htmlspecialchars($row['HUB_NAME']); ?>"
                                                                                onclick="deleteData(this)">
                                                                                <i class="fa fa-trash-o font-x-large" aria-hidden="true"></i>
                                                                            </a>
                                                                        </td>

                                                                    </tr>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>
                                        <!-- Edit Place Modal -->
                                        <div class="modal fade" id="editPlaceModal" tabindex="-1" role="dialog" aria-labelledby="editPlaceModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Hub</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="editHubId">
                                                        <div class="form-group">
                                                            <label for="editHubName">Hub Name</label>
                                                            <input type="text" class="form-control" id="editHubName">
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="editHubId">
                                                        <div class="form-group">
                                                            <label for="editHubName">Hub Mobile</label>
                                                            <input type="text" class="form-control" id="editHubMobile">
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="editHubId">
                                                        <div class="form-group">
                                                            <label for="editHubName">Hub Address</label>
                                                            <input type="text" class="form-control" id="editHubAddress">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-success" onclick="updateHub()">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
<script type="text/javascript">


    // validate and save record
    let validateAndSaveRecords = function() {
        let hub = $("#hub").val();
        let hubMobile = $("#hubMobile").val();
        let hubAddress = $("#hubAddress").val();
        if (hub == undefined || hub == null || hub == "") {
            alert("❌ Hub Name is mandatory!");
            return false;
        }


        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                addNewHub: 1,
                hub: hub,
                hubMobile: hubMobile,
                hubAddress: hubAddress
            },
            success: function(response) {
                console.log("Response from server:", response);
                if (response.startsWith("Insert Successful")) {
                    alert("✔️ Hub Added Successfully!");
                    window.location.reload();
                } else if (response.toString() == "HUB_NAME_ALREADY_EXISTS") {
                    alert("❌ Hub is Already Exists.");
                    return false;
                } else {
                    alert("🚨 Some error had occured. \nPlease try again");
                    return false;
                }
            }
        });
    }

    // delete data
    function deleteData(deleteButton) {
        let cnf = confirm("⚠️ Sure to delete?");
        if (cnf) {
            let hubId = $(deleteButton).attr('data-id');
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    deleteHub:1,
                    hubId: hubId
                },
                success: function(response) {
                    console.log("Response from server:", response);
                    if (response.toString().startsWith("Delete Successful")) {
                        alert('✔️ Hub Deleted Successfully!');
                        window.location.reload();
                    } else {
                        alert('❌ Error deleting place: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert("🚨 AJAX error: " + error);
                }

            });
        }
    }

    //edit data
    function editData(button) {
        let hubId = $(button).attr('data-id');
        let hubName = $(button).attr('data-name');
        let hubMobile = $(button).attr('data-mobile');
        let hubAddress = $(button).attr('data-address');

        $('#editHubId').val(hubId);
        $('#editHubName').val(hubName);
        $('#editHubMobile').val(hubMobile);
        $('#editHubAddress').val(hubAddress);
        $('#editPlaceModal').modal('show');
    }

    //Update
    function updateHub() {
        let hubId = $('#editHubId').val();
        let hubName = $('#editHubName').val();
        let hubMobile = $('#editHubMobile').val();
        let hubAddress = $('#editHubAddress').val();

        if (hubName.trim() !== "") {
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    editHub:1,
                    hubId: hubId,
                    hubName: hubName,
                    hubMobile: hubMobile,
                    hubAddress: hubAddress
                },
                success: function(response) {
                    console.log("Response from server:", response);
                    if (response.toString().startsWith("Update Successful")) {
                        alert('✔️ Hub Updated Successfully!');
                        $('#editPlaceModal').modal('hide');
                        window.location.reload();
                    }
                   else {
                        alert('❌ Error updating place: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert("🚨 AJAX error: " + error);
                }
            });
        } else {
            alert("⚠️ Place name cannot be empty.");
        }
    }


    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>


</html>