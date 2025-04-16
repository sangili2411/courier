

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
                                                <h2 class="m-t-p5 mb-0">ADD CUSTOMER</h2>
                                            </div>

                                            
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="place">Customer Name<span class="mandatory-field text-danger">*</span></label>
                                        <input type="text" required class="form-control" id="name"
                                            placeholder="Enter Place Name" name="name" />
                                    </div>
                                    <div class="form-group">
                                        <label for="place">Customer Mobile<span class="mandatory-field text-danger">*</span></label>
                                        <input type="number" required class="form-control" id="mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                            placeholder="Enter Place Name" name="mobile" />
                                    </div>
                                    <button class="btn btn-success mb-4" style="margin-left: 40%" onclick="addCustomer()">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Submit
                                    </button>
                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Mobile</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            $selectCustomer = 'SELECT CUSTOMER_NAME, MOBILE, CUSTOMER_ID FROM customer_details ORDER BY 1';
                                            $i = 1;
                                            if ($result = mysqli_query($conn, $selectCustomer)) {
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                                        <tbody>
                                                            <tr>
                                                                <td style="color:#0c1211"><?php echo $i++; ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['CUSTOMER_NAME'] ?></td>
                                                                <td style="color:#0c1211"><?php echo $row['MOBILE'] ?></td>
                                                                <td>
                                                                    <a class="a-edit-icon"
                                                                        data-id="<?php echo $row['CUSTOMER_ID']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($row['CUSTOMER_NAME']); ?>"
                                                                        data-mobile="<?php echo htmlspecialchars($row['MOBILE']); ?>"
                                                                        onclick="EditRecords(this)">

                                                                        <i class="fa fa-pencil font-x-large" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a class="a-delete-icon"
                                                                        data-id="<?php echo $row['CUSTOMER_ID']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($row['CUSTOMER_NAME']); ?>"
                                                                        onclick="deleteRecords(this)">
                                                                        <i class="fa fa-trash-o font-x-large" aria-hidden="true"></i>
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
                                <div class="modal fade" id="editcustomerModal" tabindex="-1" role="dialog" aria-labelledby="editcustomerModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Customer</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" id="editcustomer-id">
                                                <div class="form-group">
                                                    <label for="customer-name">Customer Name</label>
                                                    <input type="text" class="form-control" id="editcustomer-name">
                                                </div>

                                                <div class="form-group">
                                                    <label for="Customer-mobile">Customer Mobile</label>
                                                    <input type="text" class="form-control" id="editcustomer-mobile">
                                                </div>

                                               
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-success" onclick="updateCustomer()">Save Changes</button>
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

    <!--**********************************
            Content body end
        ***********************************-->


    <?php include 'footer.php' ?>

</body>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Table Filter -->
<script src="./js/ddtf.js"></script>
<!-- Prevent Number Scrolling -->
<script src="./js/chits/numberInputPreventScroll.js"></script>

<script type="text/javascript">
    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();

    //add customer
    function addCustomer() {
        let name = $('#name').val();
        let mobile = $('#mobile').val();
        if (name == "" || mobile == "") {
            alert("⚠️ Please fill all the fields!");
            return false;
        } else {
            $.ajax({
                type: 'post',
                url: 'dataOperations.php',
                data: {
                    addCustomer: 1,
                    name: name,
                    mobile: mobile
                },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.toString().startsWith("Insert Successful")) {
                        alert("✔️ Customer Added Successfully!");
                        window.location.reload();
                    } else if (response.toString().startsWith("MOBILE_NUMBER_ALREADY_EXISTS")) {
                        alert("⚠️ Mobile Number Already Exists!");
                    } else if (response.toString().startsWith("Insert Failed")) {} else {
                        alert("⚠️ " + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert("⚠️ Error: " + error);
                }
            });
        }
    }
    //edit customer
    function EditRecords(button) {
        // Get the driver data from the button's data attributes
        let customerid = $(button).attr('data-id');
        let name = $(button).attr('data-name');
        let mobile = $(button).attr('data-mobile');

        // Set the values in the modal inputs
        $('#editcustomer-id').val(customerid);
        $('#editcustomer-name').val(name);
        $('#editcustomer-mobile').val(mobile);

        // Show the modal
        $('#editcustomerModal').modal('show');
    }
    // update customer
    function updateCustomer() {
        let customerid = $('#editcustomer-id').val();
        let name = $('#editcustomer-name').val();
        let mobile = $('#editcustomer-mobile').val();

        if (name.trim() !== "") {
            $.ajax({
                type: 'POST',
                url: 'dataOperations.php',
                data: {
                    editcustomer: 1,
                    customerid: customerid,
                    name: name,
                    mobile: mobile,
                },
                success: function(response) {
                    console.log("Response from server:", response);

                    if (response.toString().startsWith("Update Successful1")) {
                        alert('✔️ Customer Updated Successfully!');
                        window.location.reload();
                     } else {
                        alert('❌ Error updating customer: ' + response);
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
    //delete customer
    function deleteRecords(deleteButton) {
        let cnf = confirm("⚠️ Sure to delete!");
        if (cnf) {
            let customerId = $(deleteButton).attr('data-id');
            $.ajax({
                type: 'post',
                url: 'dataOperations.php',
                data: {
                    deleteCustomer: 1,
                    customerId: customerId,
                },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.toString().startsWith("Delete Successful")) {
                        alert("✔️ Customer Deleted Successfully!");
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