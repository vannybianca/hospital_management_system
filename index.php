<?php require_once "config/database.php"; ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MEDICORE | Smarter Management. Better Care.</title>
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="home-page">
<header class="site-header">
	<a class="site-logo" href="index.php" aria-label="MEDICORE home"><span class="logo-mark">+</span><span><strong>MEDI<span>CORE</span></strong><small>Healthcare solutions</small></span></a>
	<nav class="site-nav" aria-label="Main navigation">
		<a class="active" href="index.php">Home</a>
		<a href="doctors/index.php">Doctors</a>
		<a href="#services">Services</a>
		<a href="patients/index.php">Patients</a>
	</nav>
	<div class="header-actions"><a class="outline-button" href="login.php">Staff sign in</a><a class="button header-button" href="patients/index.php">Get started <span aria-hidden="true">&#8594;</span></a></div>
</header>

<main>
	<section class="hero-section" aria-labelledby="hero-title">
		<div class="hero-copy">
			<p class="home-kicker">Trusted care, connected simply</p>
			<h1 id="hero-title">Better health starts with <em>better care.</em></h1>
			<p class="hero-text">MEDICORE brings your hospital team, patients and daily operations together in one calm, secure workspace.</p>
			<div class="hero-actions"><a class="button hero-button" href="patients/index.php">Explore patient care <span aria-hidden="true">&#8594;</span></a><a class="text-link" href="doctors/index.php">Meet our doctors <span aria-hidden="true">&#8594;</span></a></div>
			<div class="hero-proof"><span class="proof-icon">&#10003;</span><span><strong>Care you can count on</strong><small>Secure tools for informed teams</small></span></div>
		</div>
		<div class="hero-visual">
  <div class="image-frame">
    <div class="slideshow">
      <img class="slide active" src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1000&q=85" alt="Doctor in clinic">
      <img class="slide" src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=1000&q=85" alt="Doctor holding tablet">
      <img class="slide" src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=1000&q=85" alt="Healthcare professional smiling">
    </div>
    <div class="availability-badge">
      <span class="status-dot"></span>
      <span>
        <strong>Care team online</strong>
        <small>Here when you need us</small>
      </span>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slideshow .slide');
    let currentIndex = 0;

    if (slides.length > 0) {
      setInterval(() => {
        slides[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % slides.length;
        slides[currentIndex].classList.add('active');
      }, 30000);
    }
  });
</script>
	</section>

	<section class="trust-strip" aria-label="MEDICORE highlights">
		<div><strong>500+</strong><span>Care professionals</span></div><div><strong>24/7</strong><span>Connected support</span></div><div><strong>100%</strong><span>Patient focused</span></div><div><strong>Secure</strong><span>Protected records</span></div>
	</section>

	<section class="services-section" id="services" aria-labelledby="services-title">
		<div class="section-heading"><p class="home-kicker">Everything in one place</p><h2 id="services-title">A healthier way to manage care</h2><p>From the first appointment to the latest record, every detail stays close at hand.</p></div>
		<div class="service-grid"><a class="service-card" href="patients/index.php"><span class="service-icon">&#9829;</span><strong>Patient care</strong><span>Manage profiles, visits and records.</span><span class="card-arrow">&#8594;</span></a><a class="service-card" href="doctors/index.php"><span class="service-icon">&#10010;</span><strong>Our specialists</strong><span>Connect with your clinical team.</span><span class="card-arrow">&#8594;</span></a><a class="service-card" href="admin/dashboard.php"><span class="service-icon">&#9673;</span><strong>Operations hub</strong><span>Keep your hospital moving smoothly.</span><span class="card-arrow">&#8594;</span></a></div>
	</section>
</main>
<footer class="home-footer"><span>MEDICORE</span> &copy; 2026. Smarter management. Better care.</footer>
</body>
</html>