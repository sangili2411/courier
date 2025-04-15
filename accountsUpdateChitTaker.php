<?php
include 'dbConn.php';

$groupId = $_GET['groupId'];
$sql = "SELECT * FROM v_chit_taker WHERE GROUP_ID = $groupId ORDER BY CHIT_MONTH";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="./css/table-filter.css">
<link rel="stylesheet" href="./css/chits/add-edit-group.css">

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
                                                UPDATE CHIT TAKER <br>
                                                <span id="group-name"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-2">
                                            <!-- <button type="button" class="btn btn-success pull-right m-t-p5" onclick="window.location.href='chitGroupAdd.php'">
                                                <i class="fa fa-plus" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button> -->
                                        </div>
                                    </div>
                                    <br>

                                    <div id="table-data">
                                        <!-- Responsive Table  -->
                                        <table id="data-table" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Chit Month</th>
                                                    <th>Amount</th>
                                                    <th>Kasar</th>
                                                    <th>Amount Given</th>
                                                    <th>Member Taken</th>
                                                    <th>View</th>
                                                    <th>Edit</th>
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
                                                            <td><?php echo date('M-Y', strtotime($row['CHIT_MONTH'])); ?></td>
                                                            <td><?php echo $row['MONTHLY_AMOUNT']; ?></td>
                                                            <td><?php echo $row['KASAR']; ?></td>
                                                            <td><?php echo $row['TOTAL_AMOUNT']; ?></td>
                                                            <td><?php echo $row['MEMBER_NAME']; ?></td>
                                                            <td>
                                                                <a class="a-view-icon" onclick="viewRecord(<?php echo $row['GROUP_ID']; ?>, this)">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a class="a-edit-icon" onclick="editRecord(<?php echo $row['CHIT_ID'] . ', ' . $row['GROUP_ID']; ?>, this)">
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

                                    <!-- Modal For View -->
                                    <div id="view-group-modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog modal-xxl" style="max-width: 80%;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title pull-left">View Detailed Info</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="group-name">Group Name:</label>
                                                                <input type="text" class="form-control" id="group-name" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="no-of-members">No Of Members:</label>
                                                                <input type="text" class="form-control" id="no-of-members" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="no-of-chits">No Of Chits:</label>
                                                                <input type="text" class="form-control" id="no-of-chits" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="start-date">Start Date:</label>
                                                                <input type="text" class="form-control" id="start-date" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="end-date">End Date:</label>
                                                                <input type="text" class="form-control" id="end-date" disabled>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row top-2">
                                                        <div class="col-sm-12">
                                                            <label>Chit Amount Details</label>
                                                        </div>
                                                    </div>

                                                    <div class="row bdr-btm">
                                                        <div class="col-sm-1">
                                                            <span>வ.எண்</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>சீட்டு மாதம்</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>தொகை</span>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <span>கசர்</span>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <span>பட்டுவாடா</span>
                                                        </div>
                                                    </div>

                                                    <div id="chit-details-body" class="row"></div>

                                                    <div class="row top-2">
                                                        <div class="col-sm-12">
                                                            <label>Member Details</label>
                                                        </div>
                                                    </div>

                                                    <div id="members-div"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">&times; Close</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Modal For Edit -->
                                    <div id="edit-member-modal" class="modal fade" role="dialog">
                                        <div class="modal-dialog modal-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title pull-left">Chit Taker Update</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-sm-6" style="display: none;">
                                                            <div class="form-group">
                                                                <label for="chit-id">Chit Id:</label>
                                                                <input type="text" class="form-control" id="chit-id" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6" style="display: none;">
                                                            <div class="form-group">
                                                                <label for="group-id">Group Id:</label>
                                                                <input type="text" class="form-control" id="group-id" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="member-select">Chit Taker:</label>
                                                                <select class="form-control" id="member-select">
                                                                    <option value=""> -- Select Member -- </option>
                                                                </select>
                                                            </div>
                                                        </div>
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
<!-- Custom JS -->
<script src="./js/chits/addEditChitGroup.js"></script>
<script>
    let viewRecord = function(groupId) {
        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                viewGroupAndMemberDetails: 1,
                groupId: groupId
            },
            success: function(response) {
                let resultJson = JSON.parse(response);
                let chitAmountDetails = JSON.parse(resultJson["CHIT_AMOUNT"]);
                let memberDetails = JSON.parse(resultJson['MEMBER_INFO']);
                $("#chit-details-body").empty();
                for (let i = 0; i < chitAmountDetails.length; i++) {
                    let htmlString = '<div class="row bdr-btm-orange t-1-5" id="chit-row-' + i + '">' +
                        '<div class="col-sm-1">' +
                        '<span>' + (i + 1) + '</span>' +
                        '</div>' +
                        '<div class="col-sm-3">' +
                        '<div class="form-group">' +
                        '<span>' + chitAmountDetails[i]["CHIT_MONTH"] + '</span>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-sm-3">' +
                        '<div class="form-group">' +
                        '<span>' + chitAmountDetails[i]["MONTHLY_AMOUNT"] + '</span>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-sm-2">' +
                        '<div class="form-group">' +
                        '<span>' + chitAmountDetails[i]["KASAR"] + '</span>' + '</div>' +
                        '</div>' +
                        '<div class="col-sm-3">' +
                        '<div class="form-group">' +
                        '<span>' + chitAmountDetails[i]["TOTAL_AMOUNT"] + '</span>' + '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $("#chit-details-body").append(htmlString);
                }

                $("#members-div").empty();
                for (let rowNum = 0; rowNum < memberDetails.length; rowNum++) {
                    let htmlString = '<div class="row add-border-bottom" id="member-row-' + rowNum + '"> ' +
                        '<div class="col-sm-1 top-1">' +
                        '<span id="member-no-' + rowNum + '">' + (rowNum + 1) + '</span>' +
                        '</div>' +
                        '<div class="col-sm-4 top-1">' +
                        '<span id="member-name-' + rowNum + '">' + memberDetails[rowNum]["MEMBER_NAME"] + '</span>' +
                        '</div>' +
                        '<div class="col-sm-4 top-1">' +
                        '<span id="member-mobile-' + rowNum + '"> ' + memberDetails[rowNum]["MEMBER_MOBILE"] + '</span>' +
                        '</div>' +
                        '</div>';
                    $("#members-div").append(htmlString);
                }

                $("#group-name").val(memberDetails[0]["GROUP_NAME"]);
                $("#no-of-members").val(memberDetails[0]["NO_OF_MEMBERS"]);
                $("#no-of-chits").val(memberDetails[0]["NO_OF_CHITS"]);
                $("#start-date").val(memberDetails[0]["START_DATE"]);
                $("#end-date").val(memberDetails[0]["END_DATE"]);

                $('#view-group-modal').modal('show');
            }
        })
    };

    let editRecord = function(chitId, groupId, button) {
        $.ajax({
            url: "dataOperations.php",
            type: "POST",
            data: {
                getMemberDetailsForSelectedGroup: 1,
                groupId: groupId
            },
            success: function(response) {
                response = JSON.parse(response);
                $("#chit-id").val(chitId);
                $("#group-id").val(groupId);
                $('#member-select option:not(:first)').remove();
                for (let i = 0; i < response.length; i++) {
                    $('#member-select')
                        .append($("<option></option>")
                            .attr("value", response[i]["MEMBER_ID"])
                            .text(response[i]["MEMBER_NAME"]));
                }
                $('#edit-member-modal').modal('show');
            }
        });

    };

    let updateRecord = function() {
        let chitId = $("#chit-id").val();
        let groupId = $("#group-id").val();
        let memberId = $("#member-select").val();

        if (memberId == undefined || memberId == null || memberId == "") {
            alert("❌ Member Name is mandatory!");
            return false;
        } else {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    updateChitTaker: 1,
                    chitId: chitId,
                    groupId: groupId,
                    memberId: memberId
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
        }
    };

    let deleteRecord = function(groupId, groupName) {
        if (confirm("⚠️ Are you sure to delete the Chit Group " + groupName)) {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    deleteGroupId: groupId
                },
                success: function(response) {
                    if (response.toString().includes("Delete Successful")) {
                        alert("✔️ Group Deleted Successfully!");
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