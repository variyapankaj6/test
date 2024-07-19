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
            <img src="<?php echo WEBASSETS; ?>images/signup-big-fig.png" alt="Sign up">
         </div>
      </div>
      <div class="col-xl-6 pxp-column pxp-is-light">
         <div class="pxp-sign-hero-form pb-100 pt-100">
            <div class="row justify-content-center">
               <div class="col-lg-6 col-xl-7 col-xxl-5">
                  <div class="pxp-sign-hero-form-content">
                     <h5 class="text-center">Verify OTP</h5>
                     <?php if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                     <?php } ?>
                     <?php if ($this->session->flashdata('success')) { ?>
                        <div class="alert alert-success">
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                     <?php } ?>
                     <form class="mt-4" method="post" autocomplete="off">
                        <div class="form-floating mb-3">
                           <input type="number" class="form-control" id="otp" name="otp" placeholder="Name" value="<?php if ($this->session->flashdata('otp')){ echo $this->session->flashdata('otp'); } ?>" required>
                           <label for="name">OTP</label>
                           <span class="fa fa-lock"></span>
                        </div>
                        <div class="d-grid gap-2">
                           <button type="submit" name="verify" class="btn btn-block rounded-pill pxp-sign-hero-form-cta">Verify Now</button>
                        </div>
                        <div class="mt-4 text-center pxp-sign-hero-form-small">
                           Did not receive OTP? <a href="<?php echo base_url('login/resend_otp'); ?>">Resend</a>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>