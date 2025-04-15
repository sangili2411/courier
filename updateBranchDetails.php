<?php
session_start();
include 'dbConn.php';




if (isset($_GET['branchOfficeId']) && !empty($_GET['branchOfficeId'])) {
    $branchOfficeId = $_GET['branchOfficeId'];
    if (isset($conn)) {
        $resultBranchOfficeQry = mysqli_query($conn, "SELECT * FROM branch_details WHERE BRANCH_OFFICE_ID = $branchOfficeId");
        while ($row = mysqli_fetch_array($resultBranchOfficeQry)) {
            $branchNameFromQry = $row['BRANCH_NAME'];
            $branchMobileFromQry = $row['BRANCH_MOBILE'];
            $branchAlternativeMobileFromQry = $row['ALTERNATIVE_MOBILE'];
            $addressFromQry = $row['ADDRESS'];
            $placeFromQry = $row['PLACE'];
            $userNameFromQry = $row['USER_NAME'];
            $passwordFromQry = $row['PASSWORD'];
            $comissionFromQry = $row['COMMISSION'];
            $paidComissionFromQry = $row['PAID_COMMISSION'];
            $totalComissionFromQry = $row['TOTAL_COMMISSION'];
            $isAgentFromQry = $row['ISAGENT'];
            $expenseAmountFromQry = $row['TOTAL_EXPENSE_AMOUNT'];

            $expenseFromQry = $row['EXPENSE'];
            $expenseDescriptions = json_decode($expenseFromQry, true);
        }
    }
    //    echo json_encode($row['BRANCH_NAME']);
}
?>


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

                                <header class="panel-heading" style="text-align: center; font-size: 20px; color: #0c1211;">
                                    Branch Office Update
                                </header>
                                <div class="panel-body">
                                    <div class="position-center">
                                        <form action=" " role="form" method="post" onsubmit="return validate()">
                                            <div class="form-group">
                                                <label for="branch-name">Branch Name<span
                                                        class="mandatory-field text-danger">*</span></label>
                                                <input type="text" required class="form-control" id="branch-name"
                                                    placeholder="Enter Name" name="branch-name" readonly />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-mobile">Mobile<span class="mandatory-field text-danger">*</span></label>
                                                <input type="number" required class="form-control" id="branch-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                    placeholder="Enter Employee Mobile No" name="branch-mobile" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-alternative-mobile">Alternative Mobile</label>
                                                <input type="number" class="form-control" id="branch-alternative-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                    placeholder="Enter Employee Mobile" name="branch-alternative-mobile" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-address">Address<span
                                                        class="mandatory-field text-danger">*</span></label>
                                                <textarea required class="form-control" id="branch-address" rows="4"
                                                    placeholder="Enter Employee Address" name="branch-address"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-place">Place<span
                                                        class="mandatory-field text-danger">*</span></label><br>
                                                <select class="form-control" id="branch-place" name="branch-place" required>
                                                    <option value="">-- SELECT PLACE --</option>
                                                    <?php
                                                    $selectCity = "SELECT DISTINCT CITY_NAME FROM city ORDER BY 1";
                                                    if ($result = mysqli_query($conn, $selectCity)) {
                                                        if (mysqli_num_rows($result) > 0) {
                                                            while ($row = mysqli_fetch_array($result)) {
                                                    ?>
                                                                <option value="<?php echo $row['CITY_NAME'] ?>"><?php echo $row['CITY_NAME'] ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="user-name">User Name<span class="mandatory-field text-danger">*</span></label>
                                                <input type="text" class="form-control" id="user-name" required
                                                    placeholder="Enter User Name" name="user-name" />
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password<span class="mandatory-field text-danger">*</span></label>
                                                <input type="text" class="form-control" id="password" required
                                                    placeholder="Enter Password" name="password" />
                                            </div>
                                            <div class="form-group ">
                                                <label for="is_agent">Is Agent <span class="mandatory-field text-danger"></span></label>&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" id="is_agent" />
                                            </div>
                                            <script>
                                                document.getElementById('is_agent').addEventListener('change', function() {
                                                    if (this.checked) {
                                                        document.getElementById('agent_details_box').style.display = 'block';
                                                        document.getElementById('expense_section').style.display = 'none';
                                                        document.getElementById('add_expense').style.display = 'none';
                                                    } else {
                                                        document.getElementById('agent_details_box').style.display = 'none';
                                                        document.getElementById('expense_section').style.display = 'block';
                                                        document.getElementById('add_expense').style.display = 'inline-block';
                                                    }
                                                });
                                            </script>


                                            <!-- Agent commission input -->
                                            <div id="agent_details_box" style="display: none;">
                                                <div class="form-group">
                                                    <label for="commission_percentage1">Commission Percentage %</label>
                                                    <input type="text" class="form-control" id="commission" name="commission" />
                                                </div>

                                                <div class="form-group">
                                                    <label for="commission_percentage2">Paid Commission Percentage %</label>
                                                    <input type="text" class="form-control" id="paid_commission" name="paid_commission" />
                                                </div>

                                                <div class="form-group">
                                                    <label for="commission_percentage3">ToPaid Commission Percentage %</label>
                                                    <input type="text" class="form-control" id="total_commission" name="total_commission" />
                                                </div>
                                            </div>

                                            <!-- Expense fields -->
                                            <div id="expense_section">
                                                <div class="form-group">
                                                    <label for="">Expense</label>
                                                    <input type="text" id="total_expense_amount" name="total_expense_amount" readonly class="form-control" />

                                                </div>

                                                <!-- Add More Button -->
                                                <div class="form-group text-center">
                                                    <button type="button" id="add_expense" class="btn btn-secondary">
                                                        <i class="fa fa-plus font-medium menu-icon"></i> Add Expense
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">DESCRIPTION</label>
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="">AMOUNT</label>
                                                    </div>
                                                </div>
                                            </div>
<!-- Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fill static fields
        document.getElementById('branch-name').value = "<?php echo $branchNameFromQry; ?>";
        document.getElementById('branch-mobile').value = "<?php echo $branchMobileFromQry; ?>";
        document.getElementById('branch-alternative-mobile').value = "<?php echo $branchAlternativeMobileFromQry; ?>";
        document.getElementById('branch-address').value = "<?php echo $addressFromQry; ?>";
        document.getElementById('branch-place').value = "<?php echo $placeFromQry; ?>";
        document.getElementById('user-name').value = "<?php echo $userNameFromQry; ?>";
        document.getElementById('password').value = "<?php echo $passwordFromQry; ?>";

        const isAgent = <?php echo $isAgentFromQry ? 'true' : 'false'; ?>;
        document.getElementById('is_agent').checked = isAgent;

        if (isAgent) {
            document.getElementById('agent_details_box').style.display = 'block';
            document.getElementById('expense_section').style.display = 'none';
            document.getElementById('add_expense').style.display = 'none';

            document.getElementById('commission').value = "<?php echo $comissionFromQry; ?>";
            document.getElementById('paid_commission').value = "<?php echo $paidComissionFromQry; ?>";
            document.getElementById('total_commission').value = "<?php echo $totalComissionFromQry; ?>";
        } else {
            document.getElementById('agent_details_box').style.display = 'none';
            document.getElementById('expense_section').style.display = 'block';
            document.getElementById('add_expense').style.display = 'inline-block';

            const expenseItems = <?php echo json_encode($expenseDescriptions ?? []); ?>;

            for (let i = 0; i < expenseItems.length; i++) {
                const description = expenseItems[i].description || "";
                const amount = expenseItems[i].amount || "";

                addExpenseRow(description, amount);
            }

            calculateTotalExpense();
        }

        // Add expense button
        const addExpenseBtn = document.getElementById('add_expense');
        if (addExpenseBtn) {
            addExpenseBtn.addEventListener('click', function () {
                addExpenseRow('', '');
            });
        }
    });

    function addExpenseRow(description = '', amount = '') {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'expense-row', 'mt-2', 'align-items-end');

        newRow.innerHTML = `
            <div class="col-md-5">
                <div class="form-group">
                    <input type="text" name="expense_description[]" value="${description}" placeholder="Enter description" class="form-control" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input type="number" name="expense_amount[]" value="${amount}" placeholder="Enter amount" class="form-control" />
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <button type="button" class="btn btn-warning btn-sm clear-expense">
                        <i class="fa fa-eraser font-medium menu-icon"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-sm delete-expense">
                        <i class="fa fa-minus font-medium menu-icon"></i>
                    </button>
                </div>
            </div>
        `;

        document.getElementById('expense_section').appendChild(newRow);

        // Add event listeners
        newRow.querySelector('.delete-expense').addEventListener('click', function () {
            newRow.remove();
            calculateTotalExpense();
        });

        newRow.querySelector('.clear-expense').addEventListener('click', function () {
            const row = this.closest('.expense-row');
            row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => input.value = '');
            calculateTotalExpense();
        });

        // Optional: Update total when typing in amount
        const amountInput = newRow.querySelector('input[name="expense_amount[]"]');
        amountInput.addEventListener('input', calculateTotalExpense);
    }

    function calculateTotalExpense() {
        let total = 0;
        document.querySelectorAll('input[name="expense_amount[]"]').forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value)) total += value;
        });

        const totalField = document.getElementById('total_expense_amount');
        if (totalField) {
            totalField.value = total.toFixed(2);
        }
    }
</script>




                                            <button class="btn btn-success" style="margin-left: 40%" onclick="updateBranchDetails()">
                                                💾 Submit
                                            </button>
                                        </form>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#branch-name').val(<?php echo "'" . $branchNameFromQry . "'"; ?>);
        $('#branch-mobile').val(<?php echo "'" . $branchMobileFromQry . "'"; ?>);
        $('#branch-alternative-mobile').val(<?php echo "'" . $branchAlternativeMobileFromQry . "'"; ?>);
        $('#branch-address').val(<?php echo "'" . str_replace('"', '', json_encode($addressFromQry)) . "'"; ?>);
        $('#branch-place').val(<?php echo "'" . $placeFromQry . "'"; ?>);
        $('#user-name').val(<?php echo "'" . $userNameFromQry . "'"; ?>);
        $('#password').val(<?php echo "'" . $passwordFromQry . "'"; ?>);
    });


    function updateBranchDetails() {
        var branchOfficeId = <?php echo $branchOfficeId; ?>;
        var branchName = $('#branch-name').val();
        var branchMobile = $('#branch-mobile').val();
        var branchEmail = $('#branch-email').val();
        var branchAlternativeMobile = $('#branch-alternative-mobile').val();
        var branchAddress = $('#branch-address').val();
        var branchPlace = $('#branch-place').val();
        var branchCity = $('#branch-city').val();
        var userName = $('#user-name').val();
        var password = $('#password').val();

        if (branchName == "" || branchMobile == "" || branchAddress == "" || branchPlace == "" || userName == "" || password == "") {
            alert("Please fill all required fields.");
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: "dataOperations.php",
                data: {
                    updateBranchDetails: 1,
                    branchOfficeId: branchOfficeId,
                    branchName: branchName,
                    branchMobile: branchMobile,
                    branchEmail: branchEmail,
                    branchAlternativeMobile: branchAlternativeMobile,
                    branchAddress: branchAddress,
                    branchPlace: branchPlace,
                    branchCity: branchCity,
                    userName: userName,
                    password: password,
                    commission: $("#commission").val(),
                    paidCommission: $("#paid_commission").val(),
                    totalCommission: $("#total_commission").val(),
                    expense: $("input[name='total_expense_amount']").map(function() {
                        return $(this).val();
                    }).get(),

                    'expense_description[]': $("input[name='expense_description[]']").map(function() {
                        return $(this).val();
                    }).get(),
                    'expense_amount[]': $("input[name='expense_amount[]']").map(function() {
                        return $(this).val();
                    }).get(),
                    isAgent: $("#is_agent").is(":checked") ? 1 : 0
                },
                success: function(response) {

                    if (response.toString().startsWith("Update Successful")) {
                        alert('✔️ Branch Details Updated Successfully!');
                        window.location.href = 'branchOfficeView.php';
                    } else {
                        alert('❌ Error updating Branch Details: ' + response);
                    }

                },
                error: function() {
                    alert("Error updating branch details.");
                }
            });
        }
    }
</script>


</html>