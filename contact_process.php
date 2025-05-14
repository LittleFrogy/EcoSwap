<?php
// Assume $status and $message are set based on the form processing
// $status = 'success'; or 'error'
// $message = 'Your message has been sent successfully!' or 'An error occurred. Please try again.'

$status = isset($_GET['status']) ? $_GET['status'] : 'success'; // Example
$message = isset($_GET['message']) ? $_GET['message'] : 'Thank you for contacting us!';

?>

<link rel="stylesheet" href="css/contact_process.css">

<div class="message-container">
  <div class="message-box <?php echo htmlspecialchars($status); ?>">
    <h1>
      <?php echo $status === 'success' ? 'Success!' : 'Error'; ?>
    </h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="contact.php">Go Back</a>
  </div>
</div>
