<?php
session_start();
include 'dbConn.php';
$userName = $_SESSION['userName'];

if (isset($_POST['submit'])) {
    $userName = $_POST['user-name'];
    $password = $_POST['new-password'];
    if (mysqli_query($conn, "UPDATE `branch_details` SET `PASSWORD`='$password' WHERE `USER_NAME` = '$userName'")) {
    ?>
        <script type="text/javascript">
            alert('Password Update Successfull!');
            window.open('login.php','_blank');window.setTimeout(function(){this.close();},100); 
            // window.location.href = 'login.php';
        </script>
<?php
    } else {
        echo "Error: " . $sql . "" . mysqli_error($conn);
        $conn->close();
    }
}
?>

<!DOCTYPE html>

<head>
    <title>Employee Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
</head>

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
                <!-- page start-->
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading" style="text-align: center; font-size: 20px; color: #0c1211;">
                                Forgot Password
                            </header>
                            <div class="panel-body">
                                <div class="position-center">
                                    <form action=" " role="form" method="post">

                                        <div class="form-group">
                                            <label for="city">User Name</label>
                                            <input type="text" class="form-control" id="user-name" placeholder="User Name" name="user-name" value="<?php echo $userName; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="city">New Password</label>
                                            <input type="text" class="form-control" id="new-password" placeholder="Enter new password" name="new-password">
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-success">Submit</button>
                                    </form>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>

    <!--main content end-->

    <script type="text/javascript">
        var deleteRecords = function(deleteButton) {
            debugger;
            let cityName = $(deleteButton).attr('data-city-name');
            $.ajax({
                type: 'post',
                url: 'city1.php',
                data: {
                    CITY_NAME: cityName,
                },
                success: function(response) {
                    alert('City is deleted Successfully');
                    window.location.reload();
                    //window.location.href = "sales_customer_all.php"
                }
            });
        }
    </script>
        <?php include 'footer.php' ?>

</body>

</html>