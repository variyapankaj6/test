<style type="text/css">
   .alert p{
      margin-bottom: 0px;
   }
   .alert{
      padding: 0.5rem 0.5rem;
   }
</style>
<section class="pxp-hero vh-100 pt-5">
   <div class="row align-items-center pxp-sign-hero-container">
      <div class="col-xl-6 pxp-column">
         <div class="pxp-sign-hero-fig text-center pb-100 pt-100">
            <img src="<?php echo WEBASSETS; ?>images/signin-big-fig.png" alt="Sign In">
         </div>
      </div>
      <div class="col-xl-6 pxp-column pxp-is-light">
         <div class="pxp-sign-hero-form pb-100 pt-100">
            <div class="row justify-content-center">
               <div class="col-lg-6 col-xl-7 col-xxl-5">
                  <div class="pxp-sign-hero-form-content">
                     <h5 class="text-center">Login</h5>
                     <?php if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php } ?>
                     <form class="mt-4" method="post" autocomplete="off">
                        <div class="form-floating mb-3">
                           <input type="email" class="form-control" id="username" name="username" placeholder="Email address" value="<?php if ($this->session->flashdata('username')){ echo $this->session->flashdata('username'); } ?>" required>
                           <label for="username">Email address</label>
                           <span class="fa fa-envelope-o"></span>
                        </div>
                        <div class="form-floating mb-3">
                           <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?php if ($this->session->flashdata('password')){ echo $this->session->flashdata('password'); } ?>" required>
                           <label for="password">Password</label>
                           <span class="fa fa-lock"></span>
                        </div>
                        <div class="d-grid gap-2">
                           <button type="submit" name="login" class="btn btn-block rounded-pill pxp-sign-hero-form-cta">Continue</button>
                        </div>
                        <div class="mt-4 text-center pxp-sign-hero-form-small">
                           <a href="<?php echo base_url('forgot_password'); ?>" class="pxp-modal-link">Forgot password</a>
                        </div>
                        <div class="mt-4 text-center pxp-sign-hero-form-small">
                           New to education.cv? <a href="<?php echo base_url('register'); ?>">Create an account</a>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>