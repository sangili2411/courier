

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
                                                <h2 class="m-t-p5 mb-0">ADD PLACE</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        <div class="position-center">
                                            <form action=" " role="form" method="post">
                                                <div class="form-group">
                                                    <label for="place">Place Name<span class="mandatory-field text-danger">*</span></label>
                                                    <input type="text" required class="form-control" id="place"
                                                        placeholder="Enter Place Name" name="place" />
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
                                                        <th>Place</th>
                                                        <th>Edit</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT * FROM city ORDER BY 2";
                                                    $result = mysqli_query($conn, $sql);
                                                    if (mysqli_num_rows($result) > 0) {
                                                        $i = 1;
                                                        while ($row = mysqli_fetch_array($result)) { // [{}]
                                                    ?>
                                                            <tr>
                                                                <td><?php echo $i++; ?></td>
                                                                <td><?php echo $row['CITY_NAME']; ?></td>
                                                                <td>
                                                                    <a class="a-edit-icon"
                                                                        data-id="<?php echo $row['CITY_ID']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($row['CITY_NAME']); ?>"
                                                                        onclick="editData(this)">
                                                                        <i class="fa fa-pencil font-x-large" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a class="a-delete-icon"
                                                                        data-id="<?php echo $row['CITY_ID']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($row['CITY_NAME']); ?>"
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

                                    <!-- Edit Place Modal -->
                                    <div class="modal fade" id="editPlaceModal" tabindex="-1" role="dialog" aria-labelledby="editPlaceModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Place</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" id="editPlaceId">
                                                    <div class="form-group">
                                                        <label for="editPlaceName">Place Name</label>
                                                        <input type="text" class="form-control" id="editPlaceName">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" onclick="updatePlace()">Save Changes</button>
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
        let place = $("#place").val();
        if (place == undefined || place == null || place == "") {
            alert("❌ Place Name is mandatory!");
            return false;
        }

        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                addNewPlace: 1,
                place: place

            },
            success: function(response) {
    console.log("Response from server:", response);
    response = response.toString().trim(); 

    if (response.startsWith("Insert Successful")) {
        alert("✔️ Place Added Successfully!");
        window.location.reload();
    } else if (response === "PLACE_ALREADY_EXISTS") {
        alert("❌ Place Already Exists.");
        return false;
    } else {
        alert("🚨 Some error had occurred. \nPlease try again");
        return false;
    }
}

        });
    }

    // delete data
    function deleteData(deleteButton) {
        let cnf = confirm("⚠️ Sure to delete?");
        if (cnf) {
            let placeId = $(deleteButton).attr('data-id');
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    deletePlace: 1,
                    place: placeId
                },
                success: function(response) {
                    console.log("Response from server:", response);
                    if (response.toString().startsWith("Delete Successful")) {
                        alert('✔️ Place Deleted Successfully!');
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
        let placeId = $(button).attr('data-id');
        let placeName = $(button).attr('data-name');

        $('#editPlaceId').val(placeId);
        $('#editPlaceName').val(placeName);

        $('#editPlaceModal').modal('show');
    }

    //Update
    function updatePlace() {
        let placeId = $('#editPlaceId').val();
        let newPlaceName = $('#editPlaceName').val();

        if (newPlaceName.trim() !== "") {
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    editPlace: 1,
                    placeId: placeId,
                    newPlaceName: newPlaceName
                },
                success: function(response) {
                    console.log("Response from server:", response);
                    if (response.toString().startsWith("Update Successful")) {
                        alert('✔️ Place Updated Successfully!');
                        $('#editPlaceModal').modal('hide');
                        window.location.reload();
                    } else if (response.toString() == "PLACE_ALREADY_EXISTS") {
                        alert("❌ Place is Already Exists.");
                    } else {
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