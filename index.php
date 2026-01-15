<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero" style="background: linear-gradient(135deg, #f0f4f8 0%, #e1e9f0 100%); padding: 100px 0;">
    <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
        <div>
            <span
                style="display: inline-block; padding: 6px 16px; background: rgba(0, 78, 100, 0.1); color: var(--primary-color); border-radius: 30px; font-weight: 600; font-size: 0.9rem; margin-bottom: 20px;">
                🚀 #1 Education Consultancy
            </span>
            <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 25px; color: var(--primary-color);">
                Unlock Your Global <br> <span style="color: var(--secondary-color);">Education Dreams</span>
            </h1>
            <p style="font-size: 1.15rem; color: var(--text-muted); margin-bottom: 40px; max-width: 500px;">
                Expert guidance for university admissions, study visas, and career counseling. We help you navigate the
                complexities of studying abroad.
            </p>
            <div style="display: flex; gap: 15px;">
                <a href="<?php echo isLoggedIn() ? 'student/book_appointment.php' : 'register.php'; ?>"
                    class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
                    Get Started Free <i class="fas fa-arrow-right"></i>
                </a>
                <a href="services.php" class="btn btn-secondary" style="padding: 15px 30px; font-size: 1.1rem;">Explore
                    Services</a>
            </div>

            <div style="margin-top: 50px; display: flex; gap: 40px;">
                <div>
                    <h3 style="font-size: 2rem; color: var(--primary-color);">5k+</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Students Placed</p>
                </div>
                <div>
                    <h3 style="font-size: 2rem; color: var(--primary-color);">98%</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Visa Success</p>
                </div>
                <div>
                    <h3 style="font-size: 2rem; color: var(--primary-color);">50+</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Partner Unis</p>
                </div>
            </div>
        </div>
        <div style="position: relative;">
            <!-- Placeholder for a professional image, using a colored block or illustration placeholder -->
            <div
                style="background: white; border-radius: 30px; padding: 30px; box-shadow: 0 30px 60px rgba(0,0,0,0.1); transform: rotate(-2deg);">
                <div
                    style="background: var(--bg-body); height: 400px; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                    <i class="fas fa-user-graduate" style="font-size: 5rem;"></i>
                </div>
                <div
                    style="position: absolute; bottom: -20px; right: -20px; background: white; padding: 20px; border-radius: 15px; box-shadow: var(--shadow-lg);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div
                            style="background: #e5fff9; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--success);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p style="font-weight: 700; margin: 0;">Visa Approved</p>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Just now</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Services Preview -->
<div class="container" style="margin-top: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
        <div>
            <h2 style="font-size: 2.5rem; color: var(--primary-color);">Popular Services</h2>
            <p style="color: var(--text-muted);">Top-rated assistance chosen by students.</p>
        </div>
        <a href="services.php" class="btn btn-secondary"
            style="border: 1px solid var(--primary-color); color: var(--primary-color);">View All Services</a>
    </div>

    <div class="grid-3">
        <div class="card" style="border-top: 4px solid var(--secondary-color);">
            <i class="fas fa-university"
                style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 20px;"></i>
            <h3 style="margin-bottom: 10px;">University Admission</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">End-to-end support for applications to top global
                universities.</p>
            <a href="services.php?category=Application" style="color: var(--primary-color); font-weight: 600;">Learn
                More <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card" style="border-top: 4px solid var(--success);">
            <i class="fas fa-passport" style="font-size: 2rem; color: var(--success); margin-bottom: 20px;"></i>
            <h3 style="margin-bottom: 10px;">Visa Guidance</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Expert help with visa documentation and interview
                prep.</p>
            <a href="services.php?category=Visa" style="color: var(--primary-color); font-weight: 600;">Learn More <i
                    class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card" style="border-top: 4px solid var(--primary-light);">
            <i class="fas fa-comments" style="font-size: 2rem; color: var(--primary-light); margin-bottom: 20px;"></i>
            <h3 style="margin-bottom: 10px;">Career Counseling</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Personalized sessions to map your future career
                path.</p>
            <a href="services.php?category=Consultation" style="color: var(--primary-color); font-weight: 600;">Learn
                More <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- Process Section -->
<div class="container" style="padding: 100px 40px;">
    <div style="text-align: center; max-width: 700px; margin: 0 auto 60px;">
        <h2 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 20px;">How It Works</h2>
        <p style="color: var(--text-muted);">Our streamlined process ensures you get the best guidance without the
            hassle.</p>
    </div>

    <div class="grid-3">
        <div class="card" style="text-align: center; padding: 40px 30px;">
            <div
                style="width: 80px; height: 80px; background: #e0f4ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: var(--primary-color); font-size: 1.8rem;">
                1
            </div>
            <h3 style="margin-bottom: 15px;">Book Consultation</h3>
            <p style="color: var(--text-muted);">Choose a service and a consultant that matches your needs.</p>
        </div>
        <div class="card" style="text-align: center; padding: 40px 30px;">
            <div
                style="width: 80px; height: 80px; background: #fff4e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: var(--secondary-color); font-size: 1.8rem;">
                2
            </div>
            <h3 style="margin-bottom: 15px;">Get Expert Advice</h3>
            <p style="color: var(--text-muted);">Connect with experts via video call or in-person sessions.</p>
        </div>
        <div class="card" style="text-align: center; padding: 40px 30px;">
            <div
                style="width: 80px; height: 80px; background: #e5fff9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: var(--success); font-size: 1.8rem;">
                3
            </div>
            <h3 style="margin-bottom: 15px;">Achieve Goals</h3>
            <p style="color: var(--text-muted);">Secure your admission and visa with our continuous support.</p>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div
    style="background: var(--primary-color); padding: 80px 0 80px; color: white; text-align: center; margin-bottom: 0;">
    <div class="container">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to start your journey?</h2>
        <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 40px;">Join thousands of students who have
            successfully studied abroad.</p>
        <a href="register.php" class="btn"
            style="background: white; color: var(--primary-color); padding: 15px 40px; font-weight: 600;">Create Free
            Account</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>