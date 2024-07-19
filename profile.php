<style type="text/css">
   .pxp-dashboard-content-details form #pxp-candidate-cover-choose-file + label, .pxp-dashboard-content-details form #pxp-candidate-photo-choose-file + label{
      border-radius: 50%;
   }
</style>
<div class="pxp-dashboard-content-details">
   <ul class="nav nav-pills nav-fill nav-tabs mb-5">
      <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="<?php echo base_url('profile'); ?>"><i class="fa fa-user"></i> Update Profile</a>
      </li>
      <li class="nav-item">
         <a class="nav-link" style="color: #002745;" href="<?php echo base_url('profile/education_higher'); ?>"><i class="fa fa-graduation-cap"></i> Higher Education</a>
      </li>
      <li class="nav-item">
         <a class="nav-link" style="color: #002745;" href="<?php echo base_url('profile/graduate_degrees'); ?>"><i class="fa fa-graduation-cap"></i> Graduate degrees</a>
      </li>
      <li class="nav-item">
         <a class="nav-link" style="color: #002745;" href="<?php echo base_url('profile/non_award_program'); ?>"><i class="fa fa-tasks"></i> Non-award programs</a>
      </li>
      <li class="nav-item">
         <a class="nav-link" style="color: #002745;" href="<?php echo base_url('profile/school'); ?>"><i class="fa fa-graduation-cap"></i> School</a>
      </li>
   </ul>
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
   <form method="post" autocomplete="off" enctype="multipart/form-data">
      <div class="row mt-4 mt-lg-4">
         <div class="col-xxl-8">
            <div class="row">
               <div class="col-sm-6">
                  <div class="mb-3">
                     <label for="name" class="form-label">Full Name</label>
                     <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" value="<?php echo $records[0]['name']; ?>" required>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="mb-3">
                     <label for="location" class="form-label">Location (City, Country)</label>
                     <input type="text" name="location" id="location" class="form-control" placeholder="Location" value="<?php echo $records[0]['location']; ?>" required>
                  </div>
               </div>
               <div class="col-sm-4">
                  <div class="mb-3">
                     <label for="profession" class="form-label">Profession</label>
                     <input type="text" name="profession" id="profession" class="form-control" placeholder="Profession" value="<?php echo $records[0]['profession']; ?>" required>
                  </div>
               </div>
               <div class="col-sm-4">
                  <div class="mb-3">
                     <label for="email_id" class="form-label">Email (<b style="color:green;">Verified</b>)</label>
                     <input type="email" class="form-control" placeholder="Email" required style="background-color: #e9ecef;" value="<?php echo $records[0]['email_id']; ?>" disabled>
                  </div>
               </div>
               <div class="col-sm-4" style="display: none;">
                  <div class="mb-3">
                     <label for="mobile_no" class="form-label">Mobile Number</label>
                     <input type="number" name="mobile_no" id="mobile_no" class="form-control" placeholder="Mobile Number" value="<?php echo $records[0]['mobile_no']; ?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="col-xxl-4">
            <div class="form-label">Profile Photo</div>
            <div class="pxp-candidate-photo mb-3">
               <input type="file" name="photo" id="pxp-candidate-photo-choose-file" accept="image/*" <?php if(empty($records[0]['photo'])){ echo "required"; } ?>>
               <label for="pxp-candidate-photo-choose-file" style="background-image:url(<?php if(!empty($records[0]['photo'])){ echo IMAGE.'user/'.$records[0]['photo']; } ?>);" class="pxp-cover">
                  <span style="display: <?php if(!empty($records[0]['photo'])){ echo 'none'; } ?>;">Upload<br>Photo</span>
               </label>
               <small>Click photo to update</small>
            </div>
         </div>
      </div>
      <div class="mb-3">
         <label for="about_us" class="form-label">About you</label>
         <textarea class="form-control" name="about_us" id="about_us" placeholder="About you" required><?php echo $records[0]['about_us']; ?></textarea>
      </div>
      <div class="mt-4 mt-lg-5">
         <h2 style="margin-bottom: 0.5rem;">Follow Me</h2>
         <div class="row">
            <div class="col-md-6 mt-3 mt-lg-3">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 15px;"><i class="fa fa-facebook fa-2x"></i></div>
                  <input type="url" name="faceboook_link" class="form-control" placeholder="Facebook Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['faceboook_link']; } ?>">
               </div>
            </div>
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center"><img src="<?php echo WEBASSETS; ?>images/X.svg" style="width: 2.5rem;"></div>
                  <input type="url" name="twitter_link" class="form-control" placeholder="Twitter Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['twitter_link']; } ?>">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;"><i class="fa fa-instagram fa-2x"></i></div>
                  <input type="url" name="instagram_link" class="form-control" placeholder="Instagram Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['instagram_link']; } ?>">
               </div>
            </div>
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;"><i class="fa fa-linkedin fa-2x"></i></div>
                  <input type="url" name="linkedin_link" class="form-control" placeholder="Linkedin Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['linkedin_link']; } ?>">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;"><i class="fa fa-youtube fa-2x"></i></div>
                  <input type="url" name="youtube_link" class="form-control" placeholder="Youtube Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['youtube_link']; } ?>">
               </div>
            </div>
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;"><i class="fa fa-pinterest fa-2x"></i></div>
                  <input type="url" name="pinterest_link" class="form-control" placeholder="Pinterest Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['pinterest_link']; } ?>">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;"><i class="fa fa-snapchat fa-2x"></i></div>
                  <input type="url" name="snapchat_link" class="form-control" placeholder="Snapchat Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['snapchat_link']; } ?>">
               </div>
            </div>
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;"><i class="fa fa-whatsapp fa-2x"></i></div>
                  <input type="url" name="whatsapp_link" class="form-control" placeholder="Whatsapp Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['whatsapp_link']; } ?>">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center"style="margin-right: 5px;"><img src="<?php echo WEBASSETS; ?>images/threads.png" style="width: 2.0rem;"></div>
                  <input type="url" name="threads_link" class="form-control" placeholder="Threads Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['threads_link']; } ?>">
               </div>
            </div>
            <div class="col-md-6 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 5px;"><img src="<?php echo WEBASSETS; ?>images/tiktok.svg" style="width: 2.1rem;"></div>
                  <input type="url" name="tiktok_link" class="form-control" placeholder="Tiktok Link" value="<?php if(!empty($social_media)){ echo $social_media[0]['tiktok_link']; } ?>">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12 mt-4 mt-lg-4">
               <div class="d-flex">
                  <div class="mr-3 align-self-center" style="margin-right: 10px;">Any other link</div>
                  <input type="url" name="others_link" class="form-control" placeholder="Any other link" value="<?php if(!empty($social_media)){ echo $social_media[0]['others_link']; } ?>">
               </div>
            </div>
         </div>
      </div>
      <div class="mt-4 mt-lg-5">
         <button type="submit" name="submit" class="btn rounded-pill pxp-section-cta">Update Profile</button>
      </div>
   </form>
   <form method="post" autocomplete="off" enctype="multipart/form-data">
      <div class="mt-4 mt-lg-5">
         <h2>Websites</h2>
         <p>Add your  website links here and they will appear on your Vanity page!</p>
         <div class="pxp-candidate-dashboard-skills mb-3">
            <ul class="list-unstyled">
               <?php foreach ($websites as $key => $value) { ?>
               <li><?php echo $value['website_name']; ?><a onclick="return confirm('Are you sure want to delete?')" href="<?php echo base_url('profile/delete_websites/').$value['_id']; ?>"><span class="fa fa-trash-o"></span></a></li>
               <?php } ?>
            </ul>
         </div>
         <div class="input-group mb-3">
            <input type="url" name="website_name" class="form-control" placeholder="Link" required>
            <button type="submit" name="website_submit" class="btn">Add Websites</button>
         </div>
      </div>
   </form>
</div>
<script type="text/javascript">
  $(document).ready(function (e) {
      $(".m_profile").addClass('pxp-active');  
  });
</script>