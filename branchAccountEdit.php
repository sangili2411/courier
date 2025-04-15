<!DOCTYPE html>
<?php
include 'dbConn.php';

// Correctly capture the id from the URL query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the SQL query using a prepared statement
    $stmt = $conn->prepare("SELECT * FROM branch_account WHERE BRANCH_ACCOUNT_ID = ?");

    $stmt->bind_param("i", $id); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result(); // get the result set

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // fetch row as associative array
    } else {
        echo "No record found.";
        exit;
    }
} else {
    // Optionally handle the case where no id is passed
    echo "No ID provided.";
    exit;
}



if (isset($_POST['submit'])) {
   

    $BRANCH_ACCOUNT_ID = intval($_POST['BRANCH_ACCOUNT_ID']);
    $BOOKING_AMOUNT = floatval($_POST['BOOKING_AMOUNT']);
    $RECEIVED_AMOUNT = floatval($_POST['RECEIVED_AMOUNT']);
    $BOOKING_PERCENTAGE = floatval($_POST['BOOKING_PERCENTAGE']);
    $RECEIVED_PERCENTAGE = floatval($_POST['RECEIVED_PERCENTAGE']);
    $COMMISSION_AMOUNT = floatval($_POST['COMMISSION_AMOUNT']);
    $ADMIN_OUTSTANDING_AMOUNT = floatval($_POST['ADMIN_OUTSTANDING_AMOUNT']);
    $PAYMENT_TYPE = mysqli_real_escape_string($conn, $_POST['PAYAMENT_TYPE']);
    $PAID_AMOUNT = floatval($_POST['PAID_AMOUNT']);
    $NOTES = mysqli_real_escape_string($conn, $_POST['NOTES']);


    $update = "
        UPDATE branch_account SET 
            BOOKING_AMOUNT = '$BOOKING_AMOUNT',
            RECEIVED_AMOUNT = '$RECEIVED_AMOUNT',
            BOOKING_PERCENTAGE = '$BOOKING_PERCENTAGE',
            RECEIVED_PERCENTAGE = '$RECEIVED_PERCENTAGE',
            COMMISSION_AMOUNT = '$COMMISSION_AMOUNT',
            ADMIN_OUTSTANDING_AMOUNT = '$ADMIN_OUTSTANDING_AMOUNT',
            PAYMENT_TYPE = '$PAYMENT_TYPE',
            PAID_AMOUNT = '$PAID_AMOUNT',
            NOTES = '$NOTES',
            IS_REQUEST = 1
        WHERE BRANCH_ACCOUNT_ID = '$BRANCH_ACCOUNT_ID'
    ";

    if (mysqli_query($conn, $update)) {

        $insert = "
            INSERT INTO transactions (BOOKING_ID, REQUEST_AMOUNT, PAYMENT_TYPE, NOTES, STATUS)
            VALUES ('$BRANCH_ACCOUNT_ID', '$PAID_AMOUNT', '$PAYMENT_TYPE', '$NOTES', 0)
        ";

        if (mysqli_query($conn, $insert)) {
            echo "<script>
            alert('✔️ Update successful!');
            window.location.href = 'BranchAccount.php';
        </script>";
        
        } else {
            echo "Insert failed: " . mysqli_error($conn);
        }
    } else {
        echo "Update failed: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Branch Account</title>
    <!-- Your CSS and other header elements -->
</head>

<body>
    <!-- Preloader start -->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!-- Preloader end -->

    <!-- Main wrapper start -->
    <div id="main-wrapper">
        <?php include 'header.php'; ?>

        <!-- Content body start -->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-validation">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1 text-center mb-5">
                                                <h2 class="m-t-p5 mb-0">EDIT BRANCH ACCOUNT</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        <div class="position-center">
                                            <form action="" role="form" method="post">
                                                <!-- It's good practice to include the id as a hidden field -->
                                                <input type="hidden" name="BRANCH_ACCOUNT_ID" value="<?php echo htmlspecialchars($row['BRANCH_ACCOUNT_ID']); ?>">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="BOOKING_AMOUNT">Booking Amount</label>
                                                            <input type="text" class="form-control" id="BOOKING_AMOUNT" name="BOOKING_AMOUNT" value="<?php echo htmlspecialchars($row['BOOKING_AMOUNT']); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="RECEIVED_AMOUNT">Received Amount</label>
                                                            <input type="text" class="form-control" id="RECEIVED_AMOUNT" name="RECEIVED_AMOUNT" value="<?php echo htmlspecialchars($row['RECEIVED_AMOUNT']); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="BOOKING_PERCENTAGE">Booking Percentage</label>
                                                            <input type="text" class="form-control" id="BOOKING_PERCENTAGE" name="BOOKING_PERCENTAGE" value="<?php echo htmlspecialchars($row['BOOKING_PERCENTAGE']); ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="RECEIVED_PERCENTAGE">Received Percentage</label>
                                                            <input type="text" class="form-control" id="RECEIVED_PERCENTAGE" name="RECEIVED_PERCENTAGE" value="<?php echo htmlspecialchars($row['RECEIVED_PERCENTAGE']); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="COMMISSION_AMOUNT">Commission Amount</label>
                                                            <input type="number" step="0.01" class="form-control" id="COMMISSION_AMOUNT" name="COMMISSION_AMOUNT" value="<?php echo htmlspecialchars($row['COMMISSION_AMOUNT']); ?>">
                                                        </div>
                                                    </div>



                                                    <!-- Admin Outstanding Amount -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="ADMIN_OUTSTANDING_AMOUNT">Admin Outstanding Amount</label>
                                                            <input type="number" class="form-control" id="ADMIN_OUTSTANDING_AMOUNT" name="ADMIN_OUTSTANDING_AMOUNT" step="0.01" min="0"
                                                                value="<?php
                                                                        if (isset($row['ADMIN_OUTSTANDING_AMOUNT'])) {
                                                                            // Remove any special characters except numbers and decimal point
                                                                            $cleanAmount = preg_replace('/[^0-9.]/', '', $row['ADMIN_OUTSTANDING_AMOUNT']);
                                                                            // Cast to float and ensure it's non-negative
                                                                            echo floatval($cleanAmount) >= 0 ? $cleanAmount : '0';
                                                                        } else {
                                                                            echo '0';
                                                                        }
                                                                        ?>">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="PAYAMENT_TYPE">Payment Type <span class="text-danger">*</span></label>
                                                            <select name="PAYAMENT_TYPE" id="PAYMENT_TYPE" class="form-control">
                                                           
                                                                <option value="CASH" <?php echo ($row['PAYMENT_TYPE'] === 'CASE') ? 'selected' : 'CASE'; ?>>Cash</option>
                                                                <option value="ONLINE" <?php echo ($row['PAYMENT_TYPE'] === 'ONLINE') ? 'selected' : 'ONLINE'; ?>>Online</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Paid Amount -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="PAID_AMOUNT">Paid Amount <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" id="PAID_AMOUNT" name="PAID_AMOUNT" step="0.01"
                                                                max="<?php
                                                                        if (isset($row['ADMIN_OUTSTANDING_AMOUNT'])) {
                                                                            // Remove special characters except numbers and decimal point
                                                                            $cleanAmount = preg_replace('/[^0-9.]/', '', $row['ADMIN_OUTSTANDING_AMOUNT']);
                                                                            // Convert to float
                                                                            $cleanAmount = floatval($cleanAmount);
                                                                            // Ensure it's non-negative and safe for HTML
                                                                            echo $cleanAmount >= 0 ? htmlspecialchars($cleanAmount) : '0';
                                                                        } else {
                                                                            echo '0';
                                                                        }
                                                                        ?>"
                                                                value="<?php echo isset($row['PAID_AMOUNT']) ? htmlspecialchars($row['PAID_AMOUNT']) : ''; ?>">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="PAID_AMOUNT">Notes <span class="text-danger">*</span></label>
                                                            <textarea name="NOTES" class="form-control" id="NOTES" rows="3"><?php echo htmlspecialchars($row['NOTES']); ?></textarea>

                                                        </div>
                                                    </div>


                                                </div>

                                                <!-- Add a submit button -->
                                                <div class="text-center">
                                                    <button type="submit" name="submit" class="btn btn-primary">Update Account</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- #/ container -->
                <!-- Content body end -->
                <?php include 'footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- External Scripts (like Select2 & your table filter) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="./js/ddtf.js"></script>
    <script>
        $(document).ready(function() {
            // Init plugins here if needed
        });


        document.getElementById('PAID_AMOUNT').addEventListener('input', function() {
            const paid = parseFloat(this.value) || 0;
            const adminOutstanding = Math.max(0, parseFloat(document.getElementById('ADMIN_OUTSTANDING_AMOUNT').value) || 0);

            if (paid > adminOutstanding) {
                alert("❌ Paid Amount cannot be greater than Admin Outstanding Amount.");
                this.value = '';
            }
        });
    </script>


</body>

</html>