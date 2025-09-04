<!DOCTYPE html>
<!-- saved from url=(0049)<?php echo base_url(); ?>auth/login -->
<html lang="en" class="wf-robotocondensed-n4-active wf-robotocondensed-n7-active wf-roboto-n3-active wf-roboto-n7-active wf-roboto-n5-active wf-roboto-n4-active wf-active"><!-- begin::Head -->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <title>PII | Login</title>
  <meta name="description" content="Latest updates and statistic charts">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

  <!--begin::Web font -->
  <script src="<?php echo base_url(); ?>assets/pii/webfont.js.download"></script>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/pii/css" media="all">
  <script>
    WebFont.load({
      google: {
        "families": ["Roboto+Condensed:400,700", "Roboto:300,400,500,700"]
      },
      active: function() {
        sessionStorage.fonts = true;
      }
    });
  </script>
  <!--end::Web font -->

  <!--begin::Global Theme Styles -->
  <link href="<?php echo base_url(); ?>assets/pii/vendors.bundle.css" rel="stylesheet" type="text/css">
  <link href="<?php echo base_url(); ?>assets/pii/style.bundle.min.css" rel="stylesheet" type="text/css">
  <!--end::Global Theme Styles -->

  <!--begin::Custom Styles -->
  <link href="<?php echo base_url(); ?>assets/pii/style.custom.css" rel="stylesheet" type="text/css">
  <!--end::Custom Styles -->

  <link rel="shortcut icon" href="<?php echo base_url(); ?>/assets/images/favicon_16.png">

  <style>
    .m-login.m-login--2 .m-login__wrapper .m-login__container .m-login__logo {
      text-align: center;
      margin: 0 auto 3rem auto;
    }

    .m-login.m-login--2 .m-login__wrapper .m-login__container .m-login__form {
      margin: 2rem auto 3rem;
    }

    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__head .m-login__title {
      color: #222;
    }

    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__head .m-login__desc,
    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__account .m-login__account-msg {
      color: #666;
      margin-top: 0;
    }

    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__form .m-login__form-sub .m-checkbox,
    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__form .m-login__form-sub .m-link,
    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__form .form-control:focus,
    .m-login.m-login--2.m-login-2--skin-2 .m-login__container .m-login__account .m-login__account-link {
      color: #444;
    }

    .m-checkbox.m-checkbox--focus>input:checked~span,
    .m-checkbox.m-checkbox--focus>span::after {
      border: 1px solid #ed7623;
    }

    .m-radio>input:checked~span {
      border: solid #ed7623;
      background: #ed7623;
    }

    .m-radio>span::after {
      border: solid #ffffff;
      background: #ed7623;
    }

    .m-login.m-login--2 .m-login__wrapper .m-login__container .m-login__form .m-form__group .form-control {
      border-radius: 0;
      border: solid 1px #dedede !important;
      padding: 1.5rem 1.5rem;
      margin-top: 1.2rem;
      background-color: #fff;
    }

    #m_login_signin_submit,
    #m_login_signup_submit,
    #m_login_forget_password_submit {
      border-radius: 0;
      padding: 0.8rem 3rem;
      background-color: #ed7623;
      border-color: #ed7623;
      box-shadow: 0px 5px 10px 2px rgba(0, 0, 0, 0.19) !important;
    }

    #m_login_signin_submit:hover,
    #m_login_signup_submit:hover,
    #m_login_forget_password_submit:hover {
      box-shadow: 0px 5px 10px 2px rgba(0, 0, 0, 0.38) !important;
    }

    #m_login_forget_password_cancel,
    #m_login_signup_cancel {
      border-radius: 0;
      padding: 0.8rem 3rem;
      background-color: transparent;
      color: #ed7623;
      border-color: #ed7623;
    }

    #m_login_forget_password_cancel:hover,
    #m_login_signup_cancel:hover {
      color: #999999 !important;
      border-color: #999999 !important;
    }

    .has-danger .form-control-feedback {
      color: #ed7623;
      font-size: 1rem !important;
    }
  </style>

  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/pii/jquery-ui-1.8.13.custom.css">


  <script src="<?php echo base_url(); ?>assets/pii/jquery.min.js.download" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/pii/jquery-ui-1.10.3.min.js.download" type="text/javascript"></script>


</head>

<!-- end::Head -->

<!-- Start of Qontak Webchat Script -->
<script>
  const qchatInit = document.createElement('script');
  qchatInit.src = "https://webchat.qontak.com/qchatInitialize.js";
  const qchatWidget = document.createElement('script');
  qchatWidget.src = "https://webchat.qontak.com/js/app.js";
  document.head.prepend(qchatInit);
  document.head.prepend(qchatWidget);
  qchatInit.onload = function() {
    qchatInitialize({
      id: "a75913ee-3fa8-462b-9ebd-4511cb9e3aa4",
      code: "1a6b14mZs5YBYGwJYAolKA"
    })
  };
</script>
<!-- End of Qontak Webchat Script -->

<!-- begin::Body -->

<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

  <!-- begin:: Page -->
  <div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2" id="m_login" style="background-image: url(<?php echo base_url() ?>assets/images/bg-login.jpg);">
      <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
        <div class="m-login__container">
          <!-- ----------------------------------------------------------------------------- Tambahan by P' Budi -->
          <!-- RUNNING TEXT / MARQUEE -->

          <div style="margin-bottom: 15px; text-align: center;">
            <marquee behavior="scroll" direction="left" scrollamount="6" style="color: blue; font-weight: bold; background-color: rgba(255, 255, 255, 0.7); padding: 5px; border-radius: 4px;">
              Untuk sementara menu PKB sedang dalam perbaikan ... dan sebagai bagian dari transformasi PII, maka mulai 06 Desember 2024, SKIP dan STRI tersedia dalam format digital (pdf), dan
              dapat diunduh melalui SIMPoNI.
            </marquee>
          </div>

          <!--

<div style="margin-bottom: 35px; text-align: center;">
    <marquee behavior="scroll" direction="left" scrollamount="6" style="color: blue; font-weight: bold; background-color: rgba(255, 255, 255, 0.7); padding: 5px; border-radius: 4px;">
	Saat ini Simponi sedang dalam MAINTENACE dan untuk sementara tidak dapat digunakan. Mohon Maaf atas ketidaknyamanan ini. Terima kasih.
    </marquee>
</div>

-->
          <!-- ------------------------------------------------------------------------------ -->
          <div class="m-login__logo">
            <img src="<?php echo base_url(); ?>assets/pii/logo-pii-login.png" class="img-fluid">
          </div>

          <div class="m-login__signin">
            <div class="m-login__head" style="background: rgba(255, 255, 255, 0.7); padding: 10px">
              <h3 class="m-login__title">Sign In</h3>
              <div class="m-login__desc">Silakan isi (Nomor KTA atau Email) &amp; Password untuk masuk</div>
            </div>
          </div>

          <form class="m-login__form m-form" method="post" name="loginform">
            <?php
            $login = array(
              'name'  => 'login',
              'id'  => 'login',
              'value' => set_value('login'),
              'maxlength'  => 80,
              'size'  => 30,
            );
            if ($login_by_username and $login_by_email) {
              $login_label = 'Email or login';
            } else if ($login_by_username) {
              $login_label = 'Login';
            } else {
              $login_label = 'Email';
            }
            $password = array(
              'name'  => 'password',
              'id'  => 'password',
              'size'  => 30,
            );
            $remember = array(
              'name'  => 'remember',
              'id'  => 'remember',
              'value'  => 1,
              'checked'  => set_value('remember'),
              'style' => 'margin:0;padding:0',
            );
            $captcha = array(
              'name'  => 'captcha',
              'id'  => 'captcha',
              'class' => "form-control m-input",
              'placeholder' => "Confirmation Code",
              'maxlength'  => 8,
            );
            ?>

            <div style="font-size: 10px;color: red;">
              <?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']]) ? $errors[$login['name']] : ''; ?>
              <?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']]) ? $errors[$password['name']] : ''; ?>

            </div>

            <div class="form-group m-form__group">
              <input type="text" name="login" id="email" class="form-control m-input" placeholder="No. KTA atau Email Anda" value="<?php echo set_value('login'); ?>" required="required">
            </div>
            <div class="form-group m-form__group">
              <input type="password" name="password" id="password" class="form-control m-input m-login__form-input--last" placeholder="Password" value="" required="required">
            </div>
            <div class="row m-login__form-sub">
              <div class="col m--align-left m-login__form-left">
                <!--
										<label class="m-checkbox  m-checkbox--focus">
											<input type="checkbox" name="remember"> Simpan data
											<span></span>
										</label>
										-->


                <?php if ($show_captcha) {
                  if ($use_recaptcha) { ?>
                    <tr>
                      <td colspan="2">
                        <div id="recaptcha_image"></div>
                      </td>
                      <td>
                        <a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
                        <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
                        <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="recaptcha_only_if_image">Enter the words above</div>
                        <div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
                      </td>
                      <td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
                      <td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
                      <?php echo $recaptcha_html; ?>
                    </tr>
                  <?php } else { ?>
                    <div class="form-group m-form__group">
                      <p>Enter the code exactly as it appears:</p>
                      <?php echo $captcha_html; ?>
                    </div>
                    <div class="form-group m-form__group">
                      <?php //echo form_label('Confirmation Code', $captcha['id']); 
                      ?>
                      <?php echo form_input($captcha); ?>
                      <span style="color: red;"><?php echo form_error($captcha['name']); ?></span>
                    </div>
                <?php }
                } ?>




              </div>
            </div>

            <div class="m-login__form-action">

              <button id="m_login_signin_submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">Sign In</button>

              <input type="hidden" name="JavaScriptEnabled" value="N">
            </div>

            <br />
            <div class="m--align-right m-login__form-right">
              <!--			<a href="<?php echo base_url(); ?>auth/check_kta">Login dengan No. KTA </a><br />  -->
              <!--			<a href="<?php echo base_url(); ?>auth/check_name">Lupa No. KTA?</a><br />         -->
              <a href="<?php echo base_url(); ?>auth/forgot_password">Lupa Password?</a>

            </div>


            <input type="hidden" name="JavaScriptEnabled" value="N">

          </form>
        </div>


        <script language="JavaScript" type="text/javascript">
          document.loginform.JavaScriptEnabled.value = "Y";
          document.loginform.email.focus();



          var kta_noKta = $('#email');

          kta_noKta.focusout(function() {
            if (kta_noKta.val().indexOf("@") == -1) {
              if (!isNaN(kta_noKta.val()) && kta_noKta.val() != "") {
                var valInt_kta = isNaN(parseInt(kta_noKta.val())) ? 0 : parseInt(kta_noKta.val());
                var str_kta = pad(valInt_kta + "", 9);
                kta_noKta.val(str_kta);
                $('#email').html(str_kta);
              }
            }
          });

          $('#email').html(kta_noKta.val());

          function pad(str, max) {
            str = str.toString();
            if (str.length < max) {
              return str.length < max ? pad("0" + str, max) : str;
            } else {
              return str.length > max ? pad(str.substr(1), max) : str;
            }
          }
        </script>

        <div class="m-login__account">
          <span class="m-login__account-msg">
            Belum memiliki akun ?
          </span>&nbsp;- &nbsp;

          <a href="<?php echo base_url(); ?>auth/register" id="m_login_signup" class="m--align-right m-login__form-right">Silahkan daftar di sini</a>

        </div>

      </div>
    </div>
  </div>
  </div>

  <!-- end:: Page -->



  <!-- end::Body -->

  <!-- End Template component://pii/template/focus/Login.ftl -->
  <!-- End Screen component://pii/widget/CommonScreens.xml#login -->
</body>

</html>