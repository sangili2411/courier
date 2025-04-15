<?php
include 'dbConn.php';

$sql = "SELECT * FROM vbiddetails ORDER BY GROUP_NAME";
$result = mysqli_query($conn, $sql);

?>
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
                                        <div class="col-sm-10">
                                            <h2 class="m-50 m-t-p5">BID'S DETAILS INFO</h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <!-- <button type="button" class="btn btn-success pull-right m-t-p5" onclick="window.location.href='#'">
                                                <i class="fa fa-plus" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button> -->
                                        </div>
                                    </div>
                                    
                                    <!-- Responsive Table  -->
                                    <table id="data-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Group Name</th>
                                                <!-- <th>Bid Date</th> -->
                                                <th>ஏலத்தேதி</th>
                                                <!-- <th>Member Taken</th> -->
                                                <th>எடுத்தவர் பெயர்</th>
                                                <!-- <th>Bid Amount</th> -->
                                                <th>ஏலத்தொகை</th>
                                                <!-- <th>Amount Given</th> -->
                                                <th>கொடுக்கப்பட்ட தொகை</th>
                                                <!-- <th>Kasar Amount</th> -->
                                                <th>கசர்</th>
                                                <!-- <th>Monthly Amount</th> -->
                                                <th>மாதாந்திர தொகை</th>
                                                <th>Update</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($result) > 0) {
                                                $i = 1;
                                                while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td><?php echo $row['GROUP_NAME']; ?></td>
                                                        <td><?php echo $row['BID_DATE']; ?></td>
                                                        <td><?php echo $row['MEMBER_TAKEN'] == '0' ? '' : $row['MEMBER_TAKEN']; ?></td>
                                                        <td><?php echo $row['BID_AMOUNT']  == '0' ? '' : $row['BID_AMOUNT']; ?></td>
                                                        <td><?php echo $row['AMOUNT_GIVEN_FOR_BID_PERSON'] == '0' ? '' :  $row['AMOUNT_GIVEN_FOR_BID_PERSON']; ?></td>
                                                        <td><?php echo $row['KASAR_AMOUNT']  == '0' ? '' : $row['KASAR_AMOUNT']; ?></td>
                                                        <td><?php echo $row['AMOUNT_TO_BE_PAID']  == '0' ? '' : $row['AMOUNT_TO_BE_PAID']; ?></td>
                                                        <td>
                                                            <a class="a-edit-icon" data-bid-id="<?php echo $row['BID_ID']; ?>" data-group-id="<?php echo $row['GROUP_ID']; ?>" onclick="editRecord(this)">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
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
    let editRecord = function (button) {
        let bidId = $(button).attr("data-bid-id");
        let groupId = $(button).attr("data-group-id");
        window.location.href = 'bidDetailsUpdate.php?bidId=' + bidId + '&groupId=' + groupId;
    };

    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>