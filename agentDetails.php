<!DOCTYPE html>
<html lang="en">

<!-- <link rel="stylesheet" href="./css/table-filter.css"> -->

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
                                            <h2 class="m-50 m-t-p5">AGENT DETAILS</h2>
                                        </div>
                                        <div class="col-sm-2">

                                        </div>
                                    </div>
                                    <div class="mt-5 mb-5">
                                        <div class="form-group">
                                            <label>Name:</label>
                                            <input type="text" id="agent-name" class="form-control" placeholder="Enter Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile:</label>
                                            <input type="text" id="mobile" class="form-control" placeholder="Enter Mobile Number" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Alternate Mobile:</label>
                                            <input type="text" id="alternate-mobile" class="form-control" placeholder="Enter Alternate Mobile Number" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Address:</label>
                                            <input type="text" id="address" class="form-control" placeholder="Enter Address" required>
                                        </div>
                                        <div class="form-group">
                                            <label>GST NO:</label>
                                            <input type="text" id="gst-no" class="form-control" placeholder="Enter GST No" required>
                                        </div>
                                        <div class="form-group">
                                            <label>User Name:</label>
                                            <input type="text" id="user-name" class="form-control" placeholder="User Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password:</label>
                                            <input type="password" id="password" class="form-control" placeholder="Password" required>
                                        </div>
                                        <button class="btn login-form__btn w-100" onclick="validateAndSaveDetails()">Register</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success m-45" onclick="validateAndRecords()">
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
    $(document).ready(function() {});
</script>

</html>