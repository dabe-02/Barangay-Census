<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Document Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/barangay_census/public/css/documentform.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="form-container">
    <img src="/barangay_census/public/images/logo.png" alt="Barangay Logo">
    <h2 class="text-center text-success fw-bold">Barangay San Juan Tiaong Quezon <br>Document Request Form</h2>

    <div class="form-step" data-step="2">
  <div id="requestFormCard" class="card mb-4">
    <div class="card-header bg-success text-white">Requester Information</div>
    <div class="card-body">
      <form id="documentRequestForm" action="submit_document_request.php" method="POST">
      

        <div class="mb-3">
            <label class="form-label">Full Name:</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Complete Address:</label>
            <input type="text" name="complete_address" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Number:</label>
            <input type="text" name="contact_number" class="form-control" required>
        </div>

        <h4 class="mt-3">Document Details</h4>

        <div class="mb-3">
            <label class="form-label">Type of Document:</label>
            <select name="document_type" class="form-select" required>
                <option value="">Select Document</option>
                <option value="Barangay Clearance">Barangay Clearance</option>
                <option value="Indigency Certificate">Indigency Certificate</option>
                <option value="Residency Certificate">Residency Certificate</option>
                <option value="Business Permit">Business Permit</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Purpose of Request:</label>
            <textarea name="purpose" class="form-control" required></textarea>
        </div>
       <!-- ✅ Loading bar -->
       <div id="loadingBar" class="progress mb-3" style="height: 8px; display: none;">
    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%"></div>
  </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success w-100">Submit Request</button>
            <p id="responseMessage" class="text-center mt-3"></p>
        </div>
    </form>

    <!-- ✅ Success Confirmation Box for Document Request -->
<div id="requestSuccessBox" class="alert alert-success text-center shadow rounded-3 p-4 mt-4" style="display: none; background-color: #ecfff1;">
    <div class="mb-2">
    <img src="/barangay_census/public/images/success-icon.png" width="40" alt="Check Icon" style="width: 40px;">
    <h4 class="fw-bold text-success" style="text-align: center;">Document Request Submitted</h4>
    <p id="documentTypeMessage"></p>
    <p>Thank you for your request. We will process it shortly.</p>
    <button class="btn btn-success" onclick="location.reload();">Submit Another Request</button>
</div>

<script>
  $(document).ready(function () {
    $("#documentRequestForm").on("submit", function (e) {
      e.preventDefault(); // Prevent default form submission

      // Clear any previous messages
      $("#responseMessage").html("").css("color", "");

      // Show loading bar
      $("#loadingBar").fadeIn();

      $.ajax({
        type: "POST",
        url: "submit_document_request.php",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
          setTimeout(function () {
            $("#loadingBar").fadeOut();

            if (response.status === "success") {
              $("#documentRequestForm").hide();
              $("#requestSuccessBox").fadeIn();
              $("#documentTypeMessage").html(`Your request for <strong>${response.document_type}</strong> has been received.`);
            } else {
              $("#responseMessage").html(response.message || "Something went wrong.").css("color", "red");
            }
          }, 1500);
        },
        error: function (xhr, status, error) {
          $("#loadingBar").fadeOut();
          console.error("AJAX Error:", error);
          $("#responseMessage").html("Error submitting form. Please try again.").css("color", "red");
        }
      });
    });
  });
</script>



</body>
</html>
