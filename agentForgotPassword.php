<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Litz Tech</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous"> -->
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="h-100">

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

    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                <a class="text-center" href="#">
                                    <h4>Litz Tech</h4>
                                </a>

                                <div class="form-group">
                                    <label for="user-name">User Name:</label>
                                    <input type="text" id="user-name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="user-name">New Password:</label><br>
                                    <input type="password" class="form-control" id="new-password">
                                </div>

                                <div class="form-group">
                                    <label for="user-name">Confirm Password:</label><br>
                                    <input type="password" class="form-control" id="confirm-password">
                                </div>

                                <div class="form-group">
                                    <button class="btn login-form__btn w-100" onclick="validateAndUpdate()">💾 Update</button>
                                </div>

                                <p class="mt-5 login-form__footer pull-left">Navigate to <a href="agentLogin.php" class="text-primary">Sign In</a> now</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="plugins/common/common.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/gleek.js"></script>
    <script src="js/styleSwitcher.js"></script>
</body>
<script>
    let validateAndUpdate = function() {
        let userName = $("#user-name").val();
        let newPassword = $("#new-password").val();
        let confirmPassword = $("#confirm-password").val();
        if (userName == "") {
            alert("❌ User Name is mandatory!");
            return false;
        } else if (newPassword == "") {
            alert("❌ New Password is mandatory!");
            return false;
        } else if (confirmPassword == "") {
            alert("❌ Confirm Password is mandatory!");
            return false;
        } else if (newPassword != confirmPassword) {
            alert("❌ New Password and Confirm Password is not matching!");
            return false;
        } else {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    isForUpdatePassword: 1,
                    userName: userName,
                    newPassword: newPassword
                },
                success: function(response) {
                    if (response.toString().includes("Update Successful")) {
                        alert("👍 Password Update Successfully!");
                        window.location.href = "agentLogin.php";
                    } else {
                        alert("😞 Some error occured \n Please try again");
                        window.location.reload();
                    }
                }
            });

        }
    };
    18008969999
</script>

</html>