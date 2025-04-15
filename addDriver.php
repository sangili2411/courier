<!DOCTYPE html>
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
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1 text-center">
                                                <h2 class="m-t-p5 mb-0">ADD DRIVER</h2>
                                            </div>

                                            <button type="button" class="btn btn-info btn-md" onclick="window.location.href='memberView.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member-name">Driver Name: <span class="mandatory-astrick">*</span></label>
                                                <input type="text" class="form-control" id="driver-name" placeholder="Enter Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member-name">Mobile Number: <span class="mandatory-astrick">*</span></label>
                                                <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile No" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member-name">License Number: <span class="mandatory-astrick">*</span></label>
                                                <input type="text" class="form-control" id="license" placeholder="Enter License Number">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member-name">Vehicle Number: <span class="mandatory-astrick">*</span></label>
                                                <input type="text" class="form-control" id="vehicle-number" placeholder="Enter Vehicle Number">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member-name">Vehicle Description: <span class="mandatory-astrick">*</span></label>
                                                <input type="text" class="form-control" id="description" placeholder="Enter Vehicle Description">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="member-name">Advance Amount: <span class="mandatory-astrick">*</span></label>
                                                <input type="number" class="form-control" id="advance-amount" placeholder="Enter Advance Amount">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group flex-grow-1 text-center">
                                        <button type="button" class="btn btn-success" onclick="addDriver()">
                                            <i class="fa fa-floppy-o font-medium" aria-hidden="true"></i>
                                            Save
                                        </button>
                                    </div>
                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr>
                                                    <th class="w3-select">#</th>
                                                    <th class="w3-select">Name</th>
                                                    <th class="w3-select">Mobile</th>
                                                    <th class="w3-select">License</th>
                                                    <th class="w3-select">Vehicle Number</th>
                                                    <th class="w3-select">Vehicle description</th>
                                                    <th class="w3-select">Advance Amount</th>

                                                    <th class="skip-filter">Edit</th>
                                                    <th class="skip-filter">Delete</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            $driverDetailsSql = 'SELECT DRIVER_NAME, MOBILE, LICENSE, DRIVER_ID, VEHICLE_NUMBER, VEHICLE_DESCRIPTION, ADVANCE_AMOUNT    FROM driver_details ORDER BY 1';
                                            $i = 1;
                                            if ($result = mysqli_query($conn, $driverDetailsSql)) {
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                                        <tbody>

                                                            <tr>
                                                                <td><?php echo $i++; ?></td>
                                                                <td><?php echo $row['DRIVER_NAME'] ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['MOBILE'] ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['LICENSE'] ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['VEHICLE_NUMBER'] ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['VEHICLE_DESCRIPTION'] ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['ADVANCE_AMOUNT'] ?></td>



                                                                <td>
                                                                    <a class="a-edit-icon" data-id="<?php echo $row['DRIVER_ID']; ?>" data-name="<?php echo htmlspecialchars($row['DRIVER_NAME']); ?>" data-mobile="<?php echo htmlspecialchars($row['MOBILE']); ?>" data-license="<?php echo htmlspecialchars($row['LICENSE']); ?>" data-vehicleno="<?php echo htmlspecialchars($row['VEHICLE_NUMBER']); ?>" data-description="<?php echo htmlspecialchars($row['VEHICLE_DESCRIPTION']); ?>" data-advance="<?php echo htmlspecialchars($row['ADVANCE_AMOUNT']); ?>" onclick="EditRecords(this)">
                                                                        <i class="fa fa-pencil font-x-large" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a class="a-delete-icon" data-id="<?php echo $row['DRIVER_ID']; ?>" data-name="<?php echo htmlspecialchars($row['DRIVER_NAME']); ?>" onclick="deleteRecords(this)">
                                                                        <i class="fa fa-trash-o font-x-large" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>

                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </table>
                                        <!-- Edit driver Modal -->
                                        <div class="modal fade" id="editdriverModal" tabindex="-1" role="dialog" aria-labelledby="editPlaceModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Driver</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="editdriver-id">
                                                        <div class="form-group">
                                                            <label for="driver-name">Driver Name</label>
                                                            <input type="text" class="form-control" id="editdriver-name">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="driver-mobile">Driver Mobile</label>
                                                            <input type="text" class="form-control" id="editdriver-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="driver-license">License Number</label>
                                                            <input type="text" class="form-control" id="editdriver-license">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="driver-license">Vehicle Number</label>
                                                            <input type="text" class="form-control" id="edit-vehicleno">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="driver-license">Vehicle description</label>
                                                            <input type="text" class="form-control" id="edit-description">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="driver-license">Advance Amount</label>
                                                            <input type="number" class="form-control" id="edit-advance">
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-success" onclick="updateDriver()">Save Changes</button>
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
<script src="./js/chits/numberInputPreventScroll.js"></script>
<script type="text/javascript">
    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();

    function addDriver() {
        let driverName = $('#driver-name').val();
        let driverMobile = $('#mobile').val();
        let driverLicense = $('#license').val();
        let vehicleno = $('#vehicle-number').val();
        let description = $('#description').val();
        let advance = $('#advance-amount').val();

        if (driverName == "" || driverMobile == "" || driverLicense == "" || vehicleno == "" || description == "" || advance == "") {
            alert("⚠️  Please fill all the fields!");
            return false;
        } else {
            $.ajax({
                type: 'post',
                url: 'dataOperations.php',
                data: {
                    addDriver: 1,
                    driverName: driverName,
                    driverMobile: driverMobile,
                    driverLicense: driverLicense,
                    vehicleno: vehicleno,
                    description: description,
                    advance: advance
                },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.toString().startsWith("Insert Successful")) {
                        alert("✔️ Driver Added Successfully!");
                        window.location.reload();
                    } else if (response.toString().startsWith("MOBILE_NUMBER_ALREADY_EXISTS")) {
                        alert("⚠️  Mobile Number Already Exists!");
                    } else {
                        alert("⚠️  Something went wrong!");
                    }
                }
            });
        }
    }



    function EditRecords(button) {
        // Get the driver data from the button's data attributes
        let driverid = $(button).attr('data-id');
        let driverName = $(button).attr('data-name');
        let driverMobile = $(button).attr('data-mobile');
        let driverLicense = $(button).attr('data-license');
        let vehicleno = $(button).attr('data-vehicleno');
        let description = $(button).attr('data-description');
        let advance = $(button).attr('data-advance');


        // Set the values in the modal inputs
        $('#editdriver-id').val(driverid);
        $('#editdriver-name').val(driverName);
        $('#editdriver-mobile').val(driverMobile);
        $('#editdriver-license').val(driverLicense);
        $('#edit-vehicleno').val(vehicleno);
        $('#edit-description').val(description);
        $('#edit-advance').val(advance);

        // Show the modal
        $('#editdriverModal').modal('show');
    }

    // update driver
    function updateDriver() {
        let driverid = $('#editdriver-id').val();
        let driverName = $('#editdriver-name').val();
        let driverMobile = $('#editdriver-mobile').val();
        let driverLicense = $('#editdriver-license').val();
        let vehicleno = $('#edit-vehicleno').val();
        let description = $('#edit-description').val();
        let advance = $('#edit-advance').val();

        if (driverName.trim() !== "") {
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    editdriver: 1,
                    driverid: driverid,
                    driverName: driverName,
                    driverMobile: driverMobile,
                    driverLicense: driverLicense,
                    vehicleno: vehicleno,
                    description: description,
                    advance: advance


                },
                success: function(response) {
                    console.log("Response from server:", response);

                    if (response.toString().startsWith("Update Successful")) {
                        alert('✔️ Driver Updated Successfully!');
                        $('#editdriverModal').modal('hide');
                        window.location.reload();
                    } else if (response.toString() == "MOBILE_ALREADY_EXISTS") {
                        alert("❌ Mobile Number already exists.");
                    } else {
                        alert('❌ Error updating driver: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert("🚨 AJAX error: " + error);
                }
            });
        } else {
            alert("⚠️ Driver name cannot be empty.");
        }
    }



    // let deleteRecords = function (deleteButton) {
    function deleteRecords(deleteButton) {
        let cnf = confirm("⚠️ Sure to delete!");
        if (cnf) {
            let driverId = $(deleteButton).attr('data-id');
            $.ajax({
                type: 'post',
                url: 'dataOperations.php',
                data: {
                    deleteDriver: 1,
                    driverId: driverId,
                },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.toString().startsWith("Delete Successful")) {
                        alert("✔️ Driver Deleted Successfully!");
                        window.location.reload();
                    } else {
                        alert("⚠️  Something went wrong!");
                    }
                }


            });
        }
    }
</script>

</html>