<?php
include 'dbConn.php';
?>

<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="./css/table-filter.css">
<link rel="stylesheet" href="./css/style.css">

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
                                            <h2 class="m-50 m-t-p5">Items</h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-success pull-right m-t-p5" onclick="window.location.href='#'">
                                                <i class="fa fa-plus" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="member-name">Item Name: <span class="mandatory-astrick">*</span></label>
                                        <input type="text" class="form-control" id="Item-name" placeholder="Enter Item Name">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" name="submit" class="btn btn-success m-45" onclick="addItem()">
                                            <i class="fa fa-floppy-o font-medium menu-icon" aria-hidden="true"></i>
                                            Save
                                        </button>
                                    </div>

                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr>

                                                    <th class="w3-select">#</th>
                                                    <th class="w3-select">Name</th>
                                                    <th class="skip-filter">Edit</th>
                                                    <th class="skip-filter">Delete</th>

                                                </tr>
                                            </thead>
                                            <?php

                                            $driverDetailsSql = 'SELECT ITEM_NAME, ITEM_ID  FROM items ORDER BY 1';
                                            $i = 1;
                                            if ($result = mysqli_query($conn, $driverDetailsSql)) {
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                                        <tbody>

                                                            <tr>
                                                                <td><?php echo $i++; ?></td>
                                                                <td><?php echo $row['ITEM_NAME'] ?></td>

                                                                <td>
                                                                    <a class="a-edit-icon"
                                                                        data-id="<?php echo $row['ITEM_ID']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($row['ITEM_NAME']); ?>"
                                                                        onclick="EditRecords(this)"> <i class="fa fa-pencil font-x-large" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a class="a-delete-icon"
                                                                        data-id="<?php echo $row['ITEM_ID']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($row['ITEM_NAME']); ?>"
                                                                        onclick="deleteRecords(this)">
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
                                    </div>
                                </div>
                                <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Items</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" id="editItem-id">
                                                <div class="form-group">
                                                    <label for="item-name">Item Name</label>
                                                    <input type="text" class="form-control" id="editItem-name">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" onclick="updateItem()">Save Changes</button>
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
    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();

    function addItem() {
        let ItemName = $('#Item-name').val();

        if (ItemName == "" || ItemName == "" || ItemName == "") {
            alert("⚠️  Please fill all the Item Name!");
            return false;
        } else {
            $.ajax({
                type: 'post',
                url: 'dataOperations.php',
                data: {
                    addItem: 1,
                    ItemName: ItemName,

                },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.toString().startsWith("Insert Successful")) {
                        alert("✔️ Item Added Successfully!");
                        window.location.reload();
                    } else if (response.toString() == "ITEM_ALREADY_EXISTS") {
                        alert("❌ Item is Already Exists.");
                        return false;
                    } else {
                        alert("⚠️  Something went wrong!");
                    }
                }
            });
        }
    }



    // Function to populate the modal form and show it
    function EditRecords(button) {
        // Get the item data from the button's data attributes
        let ItemId = $(button).attr('data-id');
        let ItemName = $(button).attr('data-name');

        // Set the values in the modal form
        $('#editItem-id').val(ItemId);
        $('#editItem-name').val(ItemName);

        // Show the modal
        $('#editItemModal').modal('show');
    }

    // Function to send the update request
    function updateItem() {
        let ItemId = $('#editItem-id').val();
        let ItemName = $('#editItem-name').val();

        if (ItemName.trim() !== "") {
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    editItem: 1,
                    ItemId: ItemId,
                    ItemName: ItemName
                },
                success: function(response) {
                    console.log("Response from server:", response);

                    if (response.toString().startsWith("Update Successful")) {
                        alert('✔️ Item Updated Successfully!');
                        $('#editItemModal').modal('hide'); // Corrected modal ID
                        window.location.reload();
                    } else if (response.toString() === "ITEM_ALREADY_EXISTS") {
                        alert("❌ Item already exists.");
                    } else {
                        alert('❌ Error updating item: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert("🚨 AJAX error: " + error);
                }
            });
        } else {
            alert("⚠️ Item name cannot be empty.");
        }
    }




    // let deleteRecords = function (deleteButton) {
    function deleteRecords(deleteButton) {
        let cnf = confirm("⚠️ Sure to delete!");
        if (cnf) {
            let ItemId = $(deleteButton).attr('data-id');
            $.ajax({
                type: 'post',
                url: 'dataOperations.php',
                data: {
                    deleteItem: 1,
                    ItemId: ItemId,
                },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.toString().startsWith("Delete Successful")) {
                        alert("✔️ Item Deleted Successfully!");
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