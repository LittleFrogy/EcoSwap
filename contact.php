<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/contact.css">
<link rel="stylesheet" href="css/style.css">

<main class="contact-container">
    <header class="contact-header">
        <h1>Contact Us</h1>
        <p>We would love to hear from you. Please fill out the form below or reach out to us directly.</p>
    </header>

    <section class="contact-form">
        <h2>Get in Touch</h2>
        <form action="contact_process.php" method="POST" aria-label="Contact Form">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required />

            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required />

            <label for="message">Your Message</label>
            <textarea id="message" name="message" placeholder="Enter your message" required></textarea>

            <button type="submit" aria-label="Send Message">Send Message</button>
        </form>
    </section>

    <section class="contact-info">
        <div>
            <h3>Email</h3>
            <p>contact@EcoSwap.com</p>
        </div>
        <div>
            <h3>Phone</h3>
            <p>+1 (555) 123-4567</p>
        </div>
        <div>
            <h3>Address</h3>
            <p>123 Eco Street, Green City, Earth 45678</p>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
