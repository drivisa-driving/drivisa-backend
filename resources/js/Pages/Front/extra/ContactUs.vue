<template>
  <div>
    <v-app-bar
        dense
    >
      <v-toolbar-title>Contact Us</v-toolbar-title>

    </v-app-bar>
    <v-container>
      <v-row>
        <v-col class="mb-5">
          <div class="container bootstrap snippets bootdeys">
            <div class="row text-center">
              <div class="col-sm-4">
                <div class="contact-detail-box">
                  <i class="fa fa-th fa-3x text-colored"></i>
                  <h4>Get In Touch</h4>
                  <abbr title="Phone">P:</abbr> 123456789<br>
                  E: <a href="mailto:email@email.com" class="text-muted">test@gmail.com</a>
                </div>
              </div><!-- end col -->

              <div class="col-sm-4">
                <div class="contact-detail-box">
                  <i class="fa fa-map-marker fa-3x text-colored"></i>
                  <h4>Our Location</h4>

                  <address>
                    Dummy Location, Dummy City<br>
                    Dummy Country<br>
                  </address>
                </div>
              </div><!-- end col -->

              <div class="col-sm-4">
                <div class="contact-detail-box">
                  <i class="fa fa-book fa-3x text-colored"></i>
                  <h4>24x7 Support</h4>

                  <p>Support System Available on Working Hours Only.</p>
                  <h4 class="text-muted">123456789</h4>
                </div>
              </div><!-- end col -->

            </div>
            <!-- end row -->


            <div class="row">
              <!-- Contact form -->
              <div class="col-sm-12">
                <form role="form" name="ajax-form" id="ajax-form"
                      action=""
                      class="form-main">

                  <div class="form-group">
                    <label for="name2">Name</label>
                    <input class="form-control" id="name2" name="name"
                           onblur="if(this.value == '') this.value='Name'"
                           onfocus="if(this.value == 'Name') this.value=''" type="text"
                           value="Name">
                    <div class="error" id="err-name" style="display: none;">Please enter name</div>
                  </div> <!-- /Form-name -->

                  <div class="form-group">
                    <label for="email2">Email</label>
                    <input class="form-control" id="email2" name="email" type="text"
                           onfocus="if(this.value == 'E-mail') this.value='';"
                           onblur="if(this.value == '') this.value='E-mail';" value="E-mail">
                    <div class="error" id="err-emailvld" style="display: none;">E-mail is not a
                      valid
                      format
                    </div>
                  </div> <!-- /Form-email -->

                  <div class="form-group">
                    <label for="message2">Message</label>
                    <textarea class="form-control" id="message2" name="message" rows="5"
                              onblur="if(this.value == '') this.value='Message'"
                              onfocus="if(this.value == 'Message') this.value=''">Message</textarea>

                    <div class="error" id="err-message" style="display: none;">Please enter message
                    </div>
                  </div> <!-- /col -->

                  <div class="row">
                    <div class="col-xs-12">
                      <div id="ajaxsuccess" class="text-success">E-mail was successfully sent.
                      </div>
                      <div class="error" id="err-form" style="display: none;">There was a problem
                        validating the form please check!
                      </div>
                      <div class="error" id="err-timedout">The connection to the server timed out!
                      </div>
                      <div class="error" id="err-state"></div>
                      <button type="submit" class="btn btn-primary btn-shadow btn-rounded w-md"
                              id="send">Submit
                      </button>
                    </div> <!-- /col -->
                  </div> <!-- /row -->

                </form> <!-- /form -->
              </div> <!-- end col -->

            </div> <!-- end row -->

          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>

</template>

<script>
export default {
  name: "ContactUs"
}
</script>

<style scoped>

/*======= Contact ======*/

.form-control {
  box-shadow: none !important;
  outline: none !important;
  border: 2px solid #cecece;
  height: 38px;
}

.form-control:hover, .form-control:focus {
  border-color: #97a0af;
}


.error {
  margin: 8px 0px;
  display: none;
  color: red;
}

#ajaxsuccess {
  font-size: 16px;
  width: 100%;
  display: none;
  clear: both;
  margin: 8px 0px;
}

.con_sub_text {
  margin: 20px 0px;
  font-size: 15px;
}

.contact-detail-box {
  margin-bottom: 50px;
}

.contact-detail-box address {
  font-size: 14px;
}

.contact-map {
  background-color: #ededed;
}

.title-box .border, .btn-primary, .back-to-top,
.logo i, .question-q-box, .social-circle li a:hover {
  background-color: #f16000 !important;
}

.title-box .title-alt, .text-colored, .footer a:hover,
.navbar-custom .navbar-nav li a:hover,
.navbar-custom .navbar-nav li a:focus,
.navbar-custom .navbar-nav li a:active,
.navbar-custom .navbar-nav li.active a {
  color: #f16000;
}

.btn-primary {
  border: 1px solid #f16000 !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary.active,
.btn-primary.focus,
.btn-primary:active,
.btn-primary:focus,
.btn-primary:hover,
.open > .dropdown-toggle.btn-primary,
.btn-primary.active.focus, .btn-primary.active:focus,
.btn-primary.active:hover, .btn-primary:active.focus,
.btn-primary:active:focus, .btn-primary:active:hover,
.open > .dropdown-toggle.btn-primary.focus,
.open > .dropdown-toggle.btn-primary:focus,
.open > .dropdown-toggle.btn-primary:hover {
  background-color: #d85600 !important;
  border: 1px solid #d85600 !important;
}

.btn-shadow.btn-primary {
  box-shadow: 1px 5px 9px rgba(241, 96, 1, 0.32);
}


/*======= Responsive ======*/
@media (min-width: 768px) {
  .nav-custom-left {
    margin-left: 5%;
  }

  .navbar-nav > li > a {
    padding-top: 10px;
    padding-bottom: 10px;
  }

  .contact-page .col-sm-6 {
    padding-left: 30px;
    padding-right: 30px;
  }
}

@media (max-width: 768px) {
  .navbar-custom {
    -moz-box-shadow: 0 2px 2px rgba(0, 0, 0, .1);
    -webkit-box-shadow: 0 2px 2px rgba(0, 0, 0, .1);
    box-shadow: 0 2px 2px rgba(0, 0, 0, .1);
  }

  .screen-space {
    margin: 0px auto 50px auto;
  }

  .feature-detail {
    padding: 0px 0px 50px 30px !important;
  }

  .footer h5 {
    margin-top: 30px;
  }
}


@media only screen and (min-width: 768px) and (max-width: 991px) {
  .blog-wrapper .blog-item {
    width: 50%;
  }

  .navbar-custom .navbar-nav li a {
    font-size: 12px;
  }

  .blog-detail-box {
    padding-right: 0px;
  }
}

@media only screen and (min-width: 767px) and (max-width: 991px) {

}

@media (max-width: 767px) {
  .blog-wrapper .blog-item {
    width: 100%;
  }

  .logo {
    margin-top: 7px;
  }

  .blog-detail-box {
    padding-right: 0px;
  }
}
</style>
