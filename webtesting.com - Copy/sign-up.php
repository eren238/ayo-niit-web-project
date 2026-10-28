<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'meta-link.php'; ?>
    <title>NIITITCORE | Sign up</title>
</head>


  <body>
    <section class="auth-session">
      <div class="auth-background">
        <div class="overlay-bg">
          <div class="form-container">
            <div class="form-content">
              <div class="inner-form">
                <div class="top-container">
                  <h2>Sign up</h2>
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
                    <label>Full Name<span>*</span></label>
                    <input
                    type="text"
                    class="text-field"
                    id="FullName"
                    placeholder="Enter your Full Name">
                </div>
                  <div class="input-wrapper">
                    <label> Email Address<span>*</span></label>
                    <input
                      type="text"
                      class="text-field"
                      id="emailAddress"
                      placeholder="Enter your email Adress"
                    />
                  </div>
                  <div class="input-wrapper">
                    <label> Phone Number<span>*</span></label>
                    <input
                    type="text"
                    class="text-field"
                    id="Phone Number"
                    placeholder="Enter Phone Number">
                  </div>
                  <div class="input-wrapper">
                    <label> Password <span>*</span></label>
                    <input
                      type="text"
                      class="text-field"
                      id="emailAddress"
                      placeholder="Enter your password"
                    />
                  </div>

                  <button class="btn" title="sign-in">Sign-up</button>
                </div>

                <div class="bottom-container"></div>
              </div>

              <div class="icon-container"></div>
            </div>
            <div class="form-content text-content">
              <div class="text-wrapper">
                <div class="logo-container">
                  <img
                    src="all-images/icon-images/niit-logo.png"
                    alt="niit-logo"
                  />
                </div>
                <div class="title">
                  <h1>Welcome to Student Login</h1>
                  <p>Don't have an account?</p>
                </div>

                <a href="sign-in.php"><button class="btn" title="sign-up">Sign-in</button></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>
