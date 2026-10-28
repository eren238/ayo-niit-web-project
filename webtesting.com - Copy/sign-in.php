<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'meta-link.php'; ?>
    <title>NIITITCORE | Sign in</title>
</head>

<body>
    <section class="auth-session">
        <div class="auth-background">
            <div class="overlay-bg">
                <div class="form-container">
                    <div class="form-content">

                        <div class="inner-form">

                            <div class="top-container">
                                <h2>Sign in</h2>
                                <div class="icon-container">
                                    <div class="icon">
                                        <i class="bi bi-facebook"></i>
                                    </div>
                                    <div class="icon">
                                        <i class="bi bi-twitter"></i>
                                    </div>

                                </div>
                            </div>
                            <div class="input-container">
                                <div class="input-wrapper">
                                    <label> Email Address<span>*</span></label>
                                    <input type="text" class="text-field" id="emailAddress"
                                        placeholder="Enter your email Adress" />
                                </div>
                                <div class="input-wrapper">
                                    <label> Password <span>*</span></label>
                                    <input type="text" class="text-field" id="emailAddress"
                                        placeholder="Enter your password" />
                                </div>

                                <button class="btn" title="sign-in">Sign-in</button>
                            </div>

                            <div class="bottom-container">

                                <div class="bottom-text">
                                    <label>
                                        <input type="checkbox" />
                                        <span>Remember Me</span>
                                    </label>
                                </div>

                                <div class="bottom-text">
                                    <a href="forgot-password.php">
                                        <span class="Forgot Password">Forgot Password?</span>
                                    </a>
                                </div>

                            </div>
                            <div class="bottom-left-container">
                                <p> Don't have an account?</p>
                                <a href="sign-up.php"> <span class="span" title="sign-up">Sign-up</span></a>

                            </div>

                        </div>

                        <div class="icon-container"></div>

                    </div>
                    <div class="form-content text-content">
                        <div class="text-wrapper">
                            <div class="logo-container">
                                <img src="all-images/icon-images/niit-logo.png" alt="niit-logo">
                            </div>
                            <div class="title">
                                <h1>Welcome to Student Portal</h1>
                                <p> Don't have an account?</p>
                            </div>

                            <a href="sign-up.php"> <button class="btn" title="sign-up">Sign-up</button></a>

                        </div>

                    </div>
                </div>


            </div>

        </div>
    </section>


</body>

</html>