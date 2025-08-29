<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../db/connect.php'; // Adjust path

    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $preferredLocation = htmlspecialchars(trim($_POST['preferredLocation']));
    $additionalInfo = htmlspecialchars(trim($_POST['additionalInfo']));
    $appointmentDate = trim($_POST['appointmentDate']);
    $appointmentTime = trim($_POST['appointmentTime']);

    $errors = [];

    if (empty($firstName) || empty($lastName) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($phone) || empty($preferredLocation) || empty($appointmentDate) || empty($appointmentTime)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $scheduledDate = $appointmentDate . ' ' . $appointmentTime;
        $endDate = date('Y-m-d H:i:s', strtotime($scheduledDate) + 3600); // 1 hour window

        // Check for existing bookings in the time window
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE scheduled_date BETWEEN ? AND ?");
        $stmt->execute([$scheduledDate, $endDate]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors[] = "This time slot is already booked. Please choose another.";
        } else {
            // Insert the booking
            $stmt = $conn->prepare("INSERT INTO bookings (franchisee_id, scheduled_date, status, notes) VALUES (NULL, ?, 'Pending', ?)");
            $stmt->execute([$scheduledDate, $additionalInfo]);
            echo '<div class="alert alert-success">Appointment confirmed! We will contact you soon.</div>';
        }
    }

    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
    }
}
?>

<?php include 'includes/guest_header.php'; ?>
<?php include 'includes/guest_navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section text-center book-appointment-hero">
    <div class="container">
        <h1 class="display-4 font-weight-bold mb-4">Schedule Your Franchise Consultation</h1>
        <p class="lead mb-5">Take the first step toward owning your JaniKing commercial cleaning franchise.</p>
    </div>
</section>

<!-- Appointment Booking Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <!-- Left Column: Why Book Consultation? -->
            <div class="col-md-5">
                <div class="why-book mb-4">
                    <h3 class="font-weight-bold mb-4">Why Book a Consultation?</h3>
                    <div class="feature-box bg-white mb-3">
                        <i class="fas fa-user-check feature-icon"></i>
                        <h4>Personalized Guidance</h4>
                        <p>Get answers to your specific questions about the JaniKing franchise opportunity.</p>
                    </div>
                    <div class="feature-box bg-white mb-3">
                        <i class="fas fa-dollar-sign feature-icon"></i>
                        <h4>Investment Details</h4>
                        <p>Learn about startup costs, ongoing fees, and potential return on investment.</p>
                    </div>
                    <div class="feature-box bg-white mb-3">
                        <i class="fas fa-map-marked-alt feature-icon"></i>
                        <h4>Territory Analysis</h4>
                        <p>Discover available territories and market potential in your desired location.</p>
                    </div>
                    <div class="feature-box bg-white mb-3">
                        <i class="fas fa-graduation-cap feature-icon"></i>
                        <h4>Training & Support</h4>
                        <p>Understand our comprehensive training program and ongoing support systems.</p>
                    </div>
                </div>
                <div class="quote-section bg-white p-4 border rounded text-center">
                    <img src="/assets/images/consultant.jpg" alt="Franchise Advisor" class="img-fluid rounded-circle mb-3" >
                    <p class="font-italic">"Our franchise advisors are dedicated to helping you make an informed decision about your business future."</p>
                </div>
            </div>

            <!-- Right Column: Calendar and Form -->
            <div class="col-md-7">
                <h3 class="font-weight-bold mb-4">Select Appointment Date & Time</h3>
                <div class="calendar-section mb-4">
                    <input type="text" class="form-control" id="appointmentDate" placeholder="Select Date">
                    <div id="timeSlots" class="mt-3"></div>
                </div>
                <h3 class="font-weight-bold mb-4">Your Information</h3>
                    <form method="POST" action="">
                        <input type="hidden" id="appointmentTime" name="appointmentTime"> <!-- Added hidden input -->
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="preferredLocation">Preferred Location</label>
                            <input type="text" class="form-control" id="preferredLocation" name="preferredLocation" required>
                        </div>
                        <div class="form-group">
                            <label for="additionalInfo">Additional Information (Optional)</label>
                            <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3"></textarea>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a></label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Confirm Appointment</button>
                    </form>
            </div>
        </div>
    </div>
</section>

<!-- Modals for Terms and Privacy -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms of Service - JaniKing Appointment Booking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>By booking an appointment with JaniKing through this portal, you agree to the following Terms & Conditions:</p>
                
                <h5>Booking Confirmation</h5>
                <ul>
                    <li>All appointments made through this system are considered requests until confirmed by JaniKing.</li>
                    <li>You will receive confirmation by email or phone once the booking is approved.</li>
                </ul>

                <h5>Customer Responsibilities</h5>
                <ul>
                    <li>You must provide accurate and complete details when booking.</li>
                    <li>Any false or misleading information may result in cancellation.</li>
                </ul>

                <h5>Rescheduling & Cancellation</h5>
                <ul>
                    <li>Customers may reschedule or cancel appointments up to 24 hours before the scheduled time.</li>
                    <li>Late cancellations may be subject to fees (if applicable).</li>
                </ul>

                <h5>Service Availability</h5>
                <ul>
                    <li>Appointment times are subject to staff and equipment availability.</li>
                    <li>JaniKing reserves the right to modify or cancel bookings if necessary.</li>
                </ul>

                <h5>Payments</h5>
                <ul>
                    <li>Payment terms will be outlined during confirmation.</li>
                    <li>Where applicable, deposits or prepayments are non-refundable unless otherwise stated.</li>
                </ul>

                <h5>Liability</h5>
                <ul>
                    <li>JaniKing will not be liable for delays or service interruptions caused by unforeseen circumstances.</li>
                    <li>Customers are responsible for ensuring safe and accessible premises at the time of service.</li>
                </ul>

                <h5>Amendments</h5>
                <ul>
                    <li>JaniKing may update these Terms at any time. Continued use of the booking system constitutes acceptance of the updated Terms.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy - JaniKing Appointment Booking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Your privacy is important to us. This Privacy Policy explains how JaniKing collects, uses, and protects your information when you book an appointment.</p>
                
                <h5>Information We Collect</h5>
                <ul>
                    <li>Name, contact details (phone, email, address).</li>
                    <li>Appointment details (preferred date, time, and service).</li>
                </ul>

                <h5>How We Use Your Information</h5>
                <ul>
                    <li>To process and confirm your appointment.</li>
                    <li>To communicate changes, cancellations, or updates.</li>
                    <li>To improve our services and customer support.</li>
                </ul>

                <h5>Data Sharing</h5>
                <ul>
                    <li>We do not sell or rent your information to third parties.</li>
                    <li>Your details may only be shared with authorized staff or service providers for scheduling and service delivery.</li>
                </ul>

                <h5>Data Security</h5>
                <ul>
                    <li>We use secure systems to protect your information.</li>
                    <li>However, no online system is 100% secure, and JaniKing cannot guarantee absolute security.</li>
                </ul>

                <h5>Cookies & Tracking (if your system uses cookies)</h5>
                <ul>
                    <li>Our booking page may use cookies to improve your user experience.</li>
                    <li>You can disable cookies in your browser settings, but some features may not function properly.</li>
                </ul>

                <h5>User Rights</h5>
                <ul>
                    <li>You may request access, correction, or deletion of your personal data by contacting us.</li>
                    <li>You may opt out of communications at any time.</li>
                </ul>

                <h5>Policy Updates</h5>
                <ul>
                    <li>This Privacy Policy may be updated occasionally.</li>
                    <li>Any changes will be posted on this page with the updated date.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="jQuery('#privacyModal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Privacy Policy content -->
                <p>Your Privacy Policy text here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Link Custom JS -->
<script src="/assets/js/book_appointment.js"></script>

<?php include 'includes/guest_footer.php'; ?>