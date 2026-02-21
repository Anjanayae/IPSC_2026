<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration IPSC 2026</title>
  <link rel="icon" href="iit_indore.ico" type="image/x-icon">
  <link rel="stylesheet" href="shared-styles.css">
  <link rel="stylesheet" href="registration-form.css">
  <link rel="stylesheet" href="registration-mobile.css">
  <link rel=""stylesheet href="registration-enhanced.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
  <nav class="navbar">
    <div class="navbar-logo-group">
      <img src="assets/iit_indore.svg" class="iit-logo">
      <img src="assets/logo.png" class="logo">
    </div>

    <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    <div class="nav-menu" id="navMenu">
      <ul class="navlinks">
        <li><a href="index.html">Home</a></li>
        <li><a href="committee.html">Committee</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">
            Programme
            <i class="fas fa-chevron-down dropdown-arrow"></i>
          </a>
          <div class="dropdown-menu">
            <a href="assets/Brochure_ipsc_2026.jpeg" target="_blank">Download Brochure</a>
            <a href="keynote-speakers.html">Keynote Speakers</a>
            <a href="program-glance.html">Program at a Glance</a>
            <a href="detailed-program-schedule.html">Detailed Program Schedule</a>
            <a href="submit-abstract.html">Submit Abstract</a>
          </div>
        </li>
        <li><a href="gallery.html">Gallery</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">
            Other Info
            <i class="fas fa-chevron-down dropdown-arrow"></i>
          </a>
          <div class="dropdown-menu">
            <a href="city-guide.html">City Guide</a>
            <a href="nearby-attractions.html">Nearby Attractions</a>
            <a href="travel.html">Travel</a>
            <a href="accommodation.html">Accommodation</a>
            <a href="sponsorship-and-exhibition.html">Sponsorship & Exhibition</a>
            <a href="contacts.html">Contact</a>
          </div>
        </li>
      </ul>
      <a href="registration.php" class="registration-btn">Registration</a>
    </div>
  </nav>
</header>

<div class="particles" id="particles"></div>

<main class="registration-main">
  <div class="registration-hero">
    <h1>Conference Registration</h1>
    <h2>IPSC 2026 - IIT Indore</h2>
  </div>

  <div class="registration-content">
    <div class="form-container">
      <div class="form-header">
        <h2>Registration Form</h2>
        <p>Please fill in all required fields marked with an asterisk</p>
      </div>

      <form id="registrationForm" class="registration-form" novalidate>
        <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">


        <!-- Personal Information Section -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-user"></i>
            Personal Information
          </h3>

          <div class="form-row">
            <div class="form-group">
              <label for="prefix">Prefix <span class="required">*</span></label>
              <select id="prefix" name="prefix" required>
                <option value="">Select Prefix</option>
                <option value="Prof.">Prof.</option>
                <option value="Dr.">Dr.</option>
                <option value="Mr.">Mr.</option>
                <option value="Ms.">Ms.</option>
                <option value="Mrs">Mrs</option>
              </select>
              <span class="error-message" id="prefix-error"></span>
            </div>

            <div class="form-group">
              <label for="gender">Gender <span class="required">*</span></label>
              <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
              <span class="error-message" id="gender-error"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="first_name">First Name <span class="required">*</span></label>
              <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
              <span class="error-message" id="first_name-error"></span>
            </div>

            <div class="form-group">
              <label for="last_name">Last Name <span class="required">*</span></label>
              <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
              <span class="error-message" id="last_name-error"></span>
            </div>
          </div>
        </div>

        <!-- Contact Information Section -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-address-book"></i>
            Contact Information
          </h3>

          <div class="form-row">
            <div class="form-group">
              <label for="phone">Phone Number <span class="required">*</span></label>
              <input type="tel" id="phone" name="phone" placeholder="+91 1234567890" required>
              <span class="error-message" id="phone-error"></span>
            </div>

            <div class="form-group">
              <label for="email">Email Address <span class="required">*</span></label>
              <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
              <span class="error-message" id="email-error"></span>
            </div>
          </div>

          <div class="form-group full-width">
            <label for="affiliation">Affiliation (Organization name with full address) <span class="required">*</span></label>
            <textarea id="affiliation" name="affiliation" rows="3" placeholder="Enter your organization name and complete address" required></textarea>
            <span class="error-message" id="affiliation-error"></span>
          </div>

          <div class="form-group">
            <label for="country">Country <span class="required">*</span></label>
            <input type="text" id="country" name="country" placeholder="Enter your country" required>
            <span class="error-message" id="country-error"></span>
          </div>
        </div>

        <!-- Paper Submission Section -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-file-alt"></i>
            Paper Submission Details
          </h3>

          <div class="form-group">
            <label>Did you submit a paper in IPSC-2026? <span class="required">*</span></label>
            <div class="radio-group">
              <label class="radio-label">
                <input type="radio" name="submitted_paper" value="Yes" required>
                <span>Yes</span>
              </label>
              <label class="radio-label">
                <input type="radio" name="submitted_paper" value="No" required>
                <span>No</span>
              </label>
            </div>
            <span class="error-message" id="submitted_paper-error"></span>
          </div>

          <div class="form-group">
            <label for="abstract_id">Abstract ID <span class="required">*</span></label>
            <input type="text" id="abstract_id" name="abstract_id" placeholder="Enter your Abstract ID or N/A" required>
            <small class="help-text">Write N/A if no abstract is submitted</small>
            <span class="error-message" id="abstract_id-error"></span>
          </div>
        </div>

        <!-- Registration Details Section -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-clipboard-list"></i>
            Registration Details
          </h3>

          <div class="form-group full-width">
            <label for="registration_type">Registration Type <span class="required">*</span></label>
            <select id="registration_type" name="registration_type" required>
              <option value="">Select Registration Type</option>
              <option value="Students/Research Scholars (Indian)">Students/Research Scholars (Indian)</option>
              <option value="Faculty/Scientists/Research Staffs (Indian)">Faculty/Scientists/Research Staffs (Indian)</option>
              <option value="Postdoc">Postdoc</option>
              <option value="Industry">Industry</option>
            </select>
            <span class="error-message" id="registration_type-error"></span>
          </div>
        </div>

        <!-- Payment Information Section -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-credit-card"></i>
            Payment Information
          </h3>

          <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
              <p><strong>Payment Instructions:</strong></p>
              <p>Registration is mandatory for all participants: invited speakers, contributed speakers, session chairs, poster presenters and those who wish to attend the sessions. All amounts indicated below are including GST.

</p>
              
              <p style="margin-top: 15px; margin-bottom: 10px;"><strong>Registration Fee Structure:</strong></p>
              <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                <tr >
                  <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Registration Type</th>
                  <th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Amount (Rs.)</th>
                </tr>
                <tr>
                  <td style="border: 1px solid #ddd; padding: 10px;">Students/Research Scholars (Indian)</td>
                  <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">3,000</td>
                </tr>
                <tr >
                  <td style="border: 1px solid #ddd; padding: 10px;">Faculty/Scientists/Research Staffs (Indian)</td>
                  <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">6,500</td>
                </tr>
                <tr>
                  <td style="border: 1px solid #ddd; padding: 10px;">Postdoc</td>
                  <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">6,500</td>
                </tr>
                <tr >
                  <td style="border: 1px solid #ddd; padding: 10px;">Industry</td>
                  <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">10,000</td>
                </tr>
              </table>
              
              <p style="margin-top: 20px; margin-bottom: 15px; padding-top: 15px; border-top: 1px solid #ddd;"><strong style="font-size: 16px;">Step 1: Fee Payment</strong></p>
              <p style="margin-bottom: 15px;"><strong>MODE OF PAYMENT of registration fee</strong></p>
              
              <div style="padding: 15px; border-left: 4px solid #007bff; margin-bottom: 20px;">
                <p style="margin: 0 0 10px 0;"><strong>Mode 1: Payment through Paytm</strong></p>
                <ol style="margin-top: 10px; padding-left: 20px;">
                  <li>Please visit the home page of IIT Indore website.</li>
                  <li>Go to PayTm link option in the facilities menu.</li>
                  <li>In "select your Institute's area": go to Registration for events – select "workshop/conference".</li>
                  <li>Select fee details: Participants may fill in the required details and proceed further. Please fill "IPSC 2026" for the title of the event.</li>
                </ol>
              </div>
              
              <div style="padding: 15px; border-left: 4px solid #28a745; margin-bottom: 20px;">
                <p style="margin: 0 0 15px 0;"><strong>Mode 2: Payment through QR Code or UPI</strong></p>
                <p>Scan the QR code or use the UPI ID given below to pay the registration fee.</p>
                <div style="text-align: center; margin: 20px 0;">
                  <img src="assets/qr.png" alt="QR Code for Payment" style="max-width: 250px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                </div>
              </div>
              
              <div style="padding: 15px; border-left: 4px solid #dc3545; margin-bottom: 20px;">
                <p style="margin: 0 0 15px 0;"><strong>Mode 3: Direct RTGS Payment</strong></p>
                <p>Alternatively, you can pay through RTGS directly. Here are the details given below:</p>
                <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                  <tr>
                    <td style="padding: 8px; font-weight: bold; width: 40%;">Name of account holder:</td>
                    <td style="padding: 8px;">Registrar IIT Indore</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">Agency Name (CPMS):</td>
                    <td style="padding: 8px;">Indian Institute of Technology Indore</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">Agency code (CPMS):</td>
                    <td style="padding: 8px;">IITIND</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">Bank Name:</td>
                    <td style="padding: 8px;">Canara Bank</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">Branch Name:</td>
                    <td style="padding: 8px;">Simrol IIT Branch</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">Account Number:</td>
                    <td style="padding: 8px;">1476101027440</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">Account Type:</td>
                    <td style="padding: 8px;">Saving account</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">MICR Code:</td>
                    <td style="padding: 8px;">452015026</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">IFSC Code:</td>
                    <td style="padding: 8px;">CNRB0006223</td>
                  </tr>
                  <tr>
                    <td style="padding: 8px; font-weight: bold;">SWIFT Code:</td>
                    <td style="padding: 8px;">CNRBINBBISG</td>
                  </tr>
                </table>
              </div>
              
              <p style="padding-top: 15px; border-top: 1px solid #ddd;"><strong>After Payment and Form Submission:</strong></p>
              <p>Please send an email to <strong>ipsc2026@iiti.ac.in</strong> with the following details:</p>
              <ul style="margin-left: 20px; margin-top: 10px;">
                <li><strong>Subject:</strong> registration fee - [Your Name]</li>
                <li><strong>Content should include:</strong>
                  <ul style="margin-top: 8px;">
                    <li>Payment Mode </li>
                    <li>Screenshot of the payment proof/receipt</li>
                    <li>Abstract ID (or N/A if no paper submitted)</li>
                    <li>Your full name</li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="payment_reference">Payment Reference Number <span class="required">*</span></label>
              <input type="text" id="payment_reference" name="payment_reference" placeholder="Enter reference number" required>
              <span class="error-message" id="payment_reference-error"></span>
            </div>

            <div class="form-group">
              <label for="amount_transferred">Amount Transferred <span class="required">*</span></label>
              <input type="number" id="amount_transferred" name="amount_transferred" placeholder="0" step="0.01" min="0" required>
              <span class="error-message" id="amount_transferred-error"></span>
            </div>
          </div>

          <div class="form-group">
            <label>Payment Status <span class="required">*</span></label>
            <div class="radio-group">
              <label class="radio-label">
                <input type="radio" name="payment_status" value="Already Paid" required>
                <span>Already Paid</span>
              </label>
            </div>
            <span class="error-message" id="payment_status-error"></span>
          </div>

          <div class="form-group full-width">
            <label for="remarks">Any Remarks</label>
            <textarea id="remarks" name="remarks" rows="3" placeholder="Enter any additional comments or special requirements"></textarea>
            <span class="error-message" id="remarks-error"></span>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="form-actions">
          <button type="submit" class="submit-btn" id="submitBtn">
            <i class="fas fa-paper-plane"></i>
            <span>Submit Registration</span>
          </button>
        </div>

        <!-- Form Messages -->
        <div id="formMessage" class="form-message" style="display: none;"></div>
      </form>
    </div>
  </div>
</main>

<footer>
  <div class="footer-content">
    <p>&copy; 2026 IPSC. All rights reserved.</p>
    <p><i class="fas fa-code"></i> Maintained by Web Team @ DAASE</p>
  </div>
</footer>

<script src="particles.js"></script>
<script src="registration-form.js"></script>
<script>
  // Hamburger Toggle
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const navMenu = document.getElementById("navMenu");

  hamburgerBtn.addEventListener("click", () => {
    hamburgerBtn.classList.toggle("active");
    navMenu.classList.toggle("open");
  });

  // Close menu when a link is clicked
  navMenu.querySelectorAll(".navlinks > li:not(.dropdown) > a, .dropdown-menu a").forEach((link) => {
    link.addEventListener("click", () => {
      hamburgerBtn.classList.remove("active");
      navMenu.classList.remove("open");
    });
  });

  // Mobile Dropdown Accordion
  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");
    toggle.addEventListener("click", (e) => {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        document.querySelectorAll(".dropdown").forEach((other) => {
          if (other !== dropdown) other.classList.remove("open");
        });
        dropdown.classList.toggle("open");
      }
    });
  });
</script>

</body>
</html>