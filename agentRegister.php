<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Litz Tech</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">

</head>

<style>
    label {
        color: black;
    }
</style>

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

                                <a class="text-center" href="">
                                    <h4>Litz Tech</h4>
                                </a>

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
                                <p class="mt-5 login-form__footer">Have account <a href="agentLogin.php" class="text-primary">Sign In </a> now</p>
                                </p>
                            </div>
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
    let validateAndSaveDetails = function() {
        let agentName = $("#agent-name").val();
        let mobile = $("#mobile").val();
        let alternateMobile = $("#alternate-mobile").val();
        let address = $("#address").val();
        let gstNum = $("#gst-no").val();
        let userName = $("#user-name").val();
        let password = $("#password").val();
        if (agentName == "") {
            alert("❌ Agent Name is mandatory!");
            return false;
        } else if (userName == "") {
            alert("❌ User Name is mandatory!");
            return false;
        } else if (password == "") {
            alert("❌ Password is mandatory!");
            return false;
        } else {
            $.ajax({
                url: "dataOperations.php",
                type: "POST",
                data: {
                    registerNewAgent: 1,
                    agentName: agentName,
                    mobile: mobile,
                    alternateMobile: alternateMobile,
                    address: address,
                    gstNum: gstNum,
                    userName: userName,
                    password: password
                },
                success: function(response) {
                    if (response.toString().includes("Insert Successful")) {
                        alert("✔️ Member added Successfully!");
                        window.location.href = 'agentLogin.php';
                    } else {
                        alert("🚨 Some error had occured. \nPlease try again")
                    }
                }
            });
        }
    };
</script>

</html>