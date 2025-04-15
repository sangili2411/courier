<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Courier</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous"> -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="./js/encryptNum.js"></script>
</head>

<style>
    .logo-title {
        margin-top: 5%;
        font-size: xxx-large;
        color: mediumpurple;
        font-family: initial;
        font-weight: 600;
    }

    .logo-img {
        margin-left: 45%;
        width: 70%;
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
                                <a class="text-center" href="#">
                                    <div class="row">
                                        <div class="col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- <b class="logo-abbr"><img class="logo-img" src="images/Zenith_Logo_Compact.png" alt=""> </b> -->

                                        </div>
                                        <div class="col-sm-5">
                                            <h4 class="logo-title pull-left">COURIER</h4>
                                        </div>
                                        <div class="col-sm-3">
                                            &nbsp;
                                        </div>
                                    </div>
                                    <!-- <span class="brand-title">
                                        <img class="logo" src="images/ZENITH_LOGO_1.png" alt="">
                                    </span> -->
                                </a>

                                <div class="form-group">
                                    <label for="user-name">User Name:</label>
                                    <input type="text" id="user-name" class="form-control" placeholder="User Name">
                                </div>

                                <div class="form-group">
                                    <label for="user-name">Password:</label><br>
                                    <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                                </div>

                                <div class="form-group">
                                    <button class="btn login-form__btn w-100" onclick="validateLogin()">Sign In</button>
                                </div>

                                <!-- <div class="mt-5 mb-5 login-input">
                                    <div class="form-group">
                                        <input type="text" id="user-name" class="form-control" placeholder="User Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" id="password" class="form-control" placeholder="Password">
                                    </div>
                                    <button class="btn login-form__btn w-100" onclick="validateLogin()">Sign In</button>
                                </div> -->
                                <div class="row">
                                    <div class="col-sm-2">&nbsp;</div>
                                    <div class="col-sm-8">
                                        <p class="mt-5 login-form__footer pull-left">Reaching The Highest Point Of Financial Success</p>
                                    </div>
                                    <div class="col-sm-2">&nbsp;</div>
                                    <!-- <p class="mt-5 login-form__footer pull-left">Dont have account? <a href="agentRegister.php" class="text-primary">Sign Up</a> now</p>
                                    <p class="mt-5 login-form__footer pull-right"><a href="agentForgotPassword.php" class="text-primary">Forgot Password</a></p> -->
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
    let validateLogin = function() {
        let userName = $("#user-name").val();
        let password = $("#password").val();
        if (userName == "") {
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
                    isForLogIn: 1,
                    userName: userName,
                    password: password

                },
                success: function(response) {
                    try {
                        console.log(response);
                        let result = JSON.parse(response);
                        if (result.status === "success") {
                            alert("👍 LogIn Successfull!");
                            window.location.href = "index.php";
                        } else {
                            alert("😞 User Name or Password is incorrect");
                        }
                    } catch (e) {
                        alert("😞 User Name or Password is incorrect");
                        return false;
                    }

                }
            });

        }
    };
    18008969999
</script>

</html>