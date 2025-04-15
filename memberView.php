<?php
include 'dbConn.php';
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
                                        <div class="col-sm-2">&nbsp;</div>
                                        <div class="col-sm-8">
                                            <h2 class="m-t-p5" style="text-align: center;">
                                                VIEW MEMBERS <br>
                                                <span id="group-name"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-success pull-right m-t-p5" onclick="window.location.href='memberAdd.php'">
                                                <i class="fa fa-plus" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <br>

                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <!-- Responsive Table  -->
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Mobile</th>
                                                    <th>Address</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $userId = $_SESSION['userId']; // decryptAgentId(preg_replace('/[^0-9]/', '', $_GET['userId']), 3);
                                                $sql = "SELECT * FROM members WHERE CREATED_BY_AGENT = " . $userId . " ORDER BY MEMBER_NAME";
                                                $result = mysqli_query($conn, $sql);
                                                if (mysqli_num_rows($result) > 0) {
                                                    $i = 1;
                                                    while ($row = mysqli_fetch_array($result)) { // [{}]
                                                ?>
                                                        <tr>
                                                            <td><?php echo $i++; ?></td>
                                                            <td><?php echo $row['MEMBER_NAME']; ?></td>
                                                            <td><?php echo $row['MEMBER_MOBILE']; ?></td>
                                                            <td><?php echo $row['MEMBER_ADDRESS']; ?></td>
                                                            <td>
                                                                <a class="a-edit-icon" data-member-name="<?php echo $row['MEMBER_NAME']; ?>" data-member-mobile="<?php echo $row['MEMBER_MOBILE']; ?>" data-member-address="<?php echo trim($row['MEMBER_ADDRESS']); ?>" onclick="editRecord(<?php echo $row['MEMBER_ID']; ?>, this)">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a class="a-delete-icon" onclick="deleteRecord(<?php echo $row['MEMBER_ID'] . ',' . '\'' . $row['MEMBER_NAME'] . '\''; ?>)">
                                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
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

                                    <!-- Modal For Edit -->
                                    <div id="edit-member-modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title pull-left">Edit Member</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group" style="display: none;">
                                                        <label for="member-id">Id:</label>
                                                        <input type="text" class="form-control" id="member-id">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="member-name">Name:</label>
                                                        <input type="text" class="form-control" id="member-name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="member-mobile">Mobile:</label>
                                                        <input type="text" class="form-control" id="member-mobile">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="member-address">Address:</label>
                                                        <textarea class="form-control" rows="3" id="member-address"></textarea>
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
    let editRecord = function(memberId, button) {
        $("#member-id").val(memberId);
        $("#member-name").val($(button).attr("data-member-name"));
        $("#member-mobile").val($(button).attr("data-member-mobile"));
        $("#member-address").val($(button).attr("data-member-address"));
        $('#edit-member-modal').modal('show');
    };

    let updateRecord = function() {
        let memberId = $("#member-id").val();
        let memberName = $("#member-name").val();
        let memberMobile = $("#member-mobile").val();
        let memberAddress = $("#member-address").val();

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
                updateExistingMember: memberId,
                memberName: memberName,
                memberMobile: memberMobile,
                memberAddress: memberAddress
            },
            success: function(response) {
                if (response.toString().includes("Update Successful")) {
                    alert("✔️ Member Updated Successfully!");
                    window.location.reload();
                } else {
                    alert("🚨 Some error had occured. \nPlease try again")
                }
            }
        });
    };

    let deleteRecord = function(memberId, memberName) {
        if (confirm("⚠️ Are you sure to delete the memeber " + memberName)) {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    deleteMemberId: memberId
                },
                success: function(response) {
                    if (response.toString().includes("Delete Successful")) {
                        alert("✔️ Member Deleted Successfully!");
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