<style>
.smart-footer{
  background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
  color:#fff;
}

/* Brand icon */
.footer-icon{
  width:38px;
  height:38px;
  border-radius:10px;
  background:#0dcaf0;
  color:#000;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:18px;
}

/* Titles */
.footer-title{
  font-weight:600;
  margin-bottom:15px;
  position:relative;
}
.footer-title::after{
  content:'';
  width:40px;
  height:2px;
  background:#0dcaf0;
  display:block;
  margin-top:6px;
}

/* Text */
.footer-text{
  color:#d1d5db;
  font-size:14px;
  line-height:1.6;
}

/* Links */
.footer-links{
  list-style:none;
  padding:0;
}
.footer-links li{
  margin-bottom:8px;
}
.footer-links a{
  color:#d1d5db;
  text-decoration:none;
  transition:0.3s;
}
.footer-links a:hover{
  color:#0dcaf0;
  padding-left:4px;
}

/* Contact */
.footer-contact{
  list-style:none;
  padding:0;
}
.footer-contact li{
  margin-bottom:10px;
  color:#d1d5db;
  font-size:14px;
}
.footer-contact i{
  color:#0dcaf0;
  margin-right:8px;
}

/* Social */
.footer-social a{
  display:inline-flex;
  width:38px;
  height:38px;
  border-radius:50%;
  background:rgba(255,255,255,0.15);
  align-items:center;
  justify-content:center;
  color:#fff;
  margin-right:10px;
  transition:.3s;
}
.footer-social a:hover{
  background:#0dcaf0;
  color:#000;
  transform:translateY(-3px);
}

/* Divider */
.footer-divider{
  border-color:rgba(255,255,255,0.15);
  margin:30px 0 15px;
}

/* Bottom */
.footer-bottom{
  font-size:14px;
  color:#cbd5e1;
}
</style>

<footer class="smart-footer">
  <div class="container py-5">

    <div class="row g-4">

      <!-- BRAND -->
      <div class="col-md-4">
        <div class="d-flex align-items-center mb-3">
          <div class="footer-icon me-2">
            <i class="fas fa-city"></i>
          </div>
          <h5 class="mb-0">Ludhiana City</h5>
        </div>
        <p class="footer-text">
          A digital civic platform for Ludhiana citizens to report local issues
          and track resolutions. Making the city cleaner, safer, and smarter.
        </p>
      </div>

      <!-- QUICK LINKS -->
      <div class="col-md-2">
        <h6 class="footer-title">Quick Links</h6>
        <ul class="footer-links">
          <li><a href="index.php">Home</a></li>
          <li><a href="report.php">Report Issue</a></li>
          <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
      </div>

      <!-- CONTACT -->
      <div class="col-md-3">
        <h6 class="footer-title">Contact</h6>
        <ul class="footer-contact">
          <li><i class="fas fa-envelope"></i> nandni141144149@gmail.com</li>
          <li><i class="fas fa-phone"></i> +91 8198837746</li>
          <li><i class="fas fa-map-marker-alt"></i> Ludhiana, Punjab</li>
        </ul>
      </div>

      <!-- SOCIAL -->
      <div class="col-md-3">
  <h6 class="footer-title">Connect With Us</h6>
  <div class="footer-social">
    
    <!-- LinkedIn -->
    <a href="https://www.linkedin.com/in/nandni-kumari-a172302a5" target="_blank">
      <i class="fab fa-linkedin"></i>
    </a>

    <!-- GitHub -->
    <a href="https://github.com/YOUR_USERNAME" target="_blank">
      <i class="fab fa-github"></i>
    </a>

    <!-- Gmail -->
    <a href="mailto:nandni141144149@gmail.com">
      <i class="fas fa-envelope"></i>
    </a>

  </div>
</div>

    </div>

    <hr class="footer-divider">

<div class="text-center mt-4">
    <p>© 2026 <strong>Ludhiana City Portal</strong>. All Rights Reserved.</p>
    <p>Designed & Developed by <b>Code Crushers</b> </p>
</div>

  </div>
</footer>
