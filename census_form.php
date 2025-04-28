<!-- census_form.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Census Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Your custom CSS -->
  <link rel="stylesheet" href="/barangay_census/public/css/censusform.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
  /* Hide all steps by default */
  .form-step { display: none; }
  /* Show only the active step */
  .form-step.active { display: block; }

  /* Next / Back button styling */
  .btn-next, .btn-prev {
    border-radius: 16px;
    width: 100px;         /* Set the button width */
    height: 36px;         /* Set the button height */
    padding: 0;           /* Remove extra padding */
    font-size: 0.85rem;   /* Control font size */
    text-transform: none;
    text-align: center;
    line-height: 36px;    /* Vertically center text */
  }

  .btn-next {
    background: #1f7a1f;
    color: #fff;
    border: none;
  }

  .btn-prev {
    background: #e0e0e0;
    color: #202124;
    border: none;
  }

  .btn-next:hover { background: #28a745; }
  .btn-prev:hover { background: #cacaca; }
</style>

</head>
<body>
  <div class="container my-5">
    <div class="text-center mb-4">
      <img src="/barangay_census/public/images/logo.png" class="sidebar-logo mb-3" alt="Logo">
      <h2 class="text-success fw-bold">
        Barangay San Juan Tiaong Quezon<br>
        Census Data Collection Form
      </h2>
    </div>

    <form id="censusForm" action="submit_census.php" method="POST">
      <!-- STEP 1: Household Info -->
      <div class="form-step active" data-step="1">
        <div class="card mb-4">
          <div class="card-header bg-success text-white">Household Information</div>
          <div class="card-body">
            <!-- Name of Household Head -->
            <div class="mb-3">
              <label class="form-label">Name of Household Head:</label>
              <input type="text" name="household_head" class="form-control" required>
            </div>
            <!-- Sitio -->
            <div class="mb-3">
              <label class="form-label">Select Sitio:</label>
              <select name="sitio_id" class="form-select" required>
                <option value="">-- Select Sitio --</option>
                <?php
                include '../config/db.php';
                $result = mysqli_query($conn, "SELECT * FROM sitio");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='{$row['id']}'>{$row['sitio_name']} - {$row['bhw_incharge']}</option>";
                }
                ?>
              </select>
            </div>
            <!-- Address -->
            <div class="mb-3">
              <label class="form-label">Complete Address:</label>
              <input type="text" name="address" class="form-control" required>
            </div>
            <!-- Household Members & Contact -->
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">No. of Household Members:</label>
                <input type="number" name="household_members" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number:</label>
                <input type="text" name="contact_number" class="form-control" required>
              </div>
            </div>
            <!-- Birthdate & Occupation -->
            <div class="row g-3 mt-3">
              <div class="col-md-6">
                <label class="form-label">Birthdate:</label>
                <input type="date" name="birthdate" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Occupation:</label>
                <input type="text" name="occupation" class="form-control">
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button type="button" class="btn-next">Next</button>
        </div>
      </div>

      <!-- STEP 2: Socio-Economic Info -->
      <div class="form-step" data-step="2">
        <div class="card mb-4">
          <div class="card-header bg-success text-white">Socio-Economic Information</div>
          <div class="card-body">
            <!-- Source of Income -->
            <div class="mb-3">
              <label class="form-label">Source of Income:</label>
              <select name="income_source[]" class="form-select" multiple>
                <option>Farming</option>
                <option>Fishing</option>
                <option>Business</option>
                <option>Government Employee</option>
                <option>Private Employee</option>
                <option>OFW</option>
                <option>No Income</option>
              </select>
              <input type="text" name="other_income" class="form-control mt-2" placeholder="Other income source">
            </div>
            <!-- Housing Type & Ownership -->
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Type of Housing:</label>
                <select name="housing_type" class="form-select" required>
                  <option>Concrete</option>
                  <option>Semi-Concrete</option>
                  <option>Light Materials</option>
                  <option>Makeshift</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">House Ownership:</label>
                <select name="house_ownership" class="form-select" required>
                  <option>Own</option>
                  <option>Rent</option>
                  <option>Living with relatives</option>
                </select>
              </div>
            </div>
            <!-- Utilities -->
            <div class="row g-3 mt-3">
              <div class="col-md-6">
                <label class="form-label">Water Source:</label>
                <select name="water_source" class="form-select" required>
                  <option>Deep Well</option>
                  <option>NAWASA</option>
                  <option>Others</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Electricity:</label>
                <select name="electricity" class="form-select" required>
                  <option>Yes</option>
                  <option>No</option>
                </select>
              </div>
            </div>
            <!-- Sanitation & Special Needs -->
            <div class="row g-3 mt-3">
              <div class="col-md-6">
                <label class="form-label">Toilet Available?</label>
                <select name="toilet" class="form-select" required>
                  <option>Yes</option>
                  <option>No</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">PWD in Household?</label>
                <select name="pwd" class="form-select" required>
                  <option>Yes</option>
                  <option>No</option>
                </select>
              </div>
            </div>
            <!-- Senior Citizen -->
            <div class="mt-3">
              <label class="form-label">Senior Citizen in Household?</label>
              <select name="senior_citizen" class="form-select" required>
                <option>Yes</option>
                <option>No</option>
              </select>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between">
          <button type="button" class="btn-prev">Back</button>
          <button type="button" class="btn-next">Next</button>
        </div>
      </div>

          <!-- STEP 3: Family Member Info -->
<div class="form-step" data-step="3">
  <div class="card mb-4">
    <div class="card-header bg-success text-white">Family Member Information</div>
    <div class="card-body" id="familyMembersContainer">

      <!-- Family Member Template -->
      <div class="family-member mb-4 border p-3 rounded bg-light">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Surname</label>
            <input type="text" name="surname[]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" name="firstname[]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middlename[]" class="form-control">
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <label class="form-label">Position in Family</label>
            <input type="text" name="family_position[]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Sex</label>
            <select name="sex[]" class="form-select" required>
              <option value="">-- Select --</option>
              <option value="M">M</option>
              <option value="F">F</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Age</label>
            <input type="number" name="age[]" class="form-control" required>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <label class="form-label">Marital Status</label>
            <select name="marital_status[]" class="form-select">
              <option>Single</option>
              <option>Married</option>
              <option>Widowed</option>
              <option>Divorced</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Sector</label>
            <input type="text" name="sector[]" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">PWD (if yes)</label>
            <input type="text" name="pwd_info[]" class="form-control">
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <label class="form-label">Senior ID</label>
            <input type="text" name="senior_id[]" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Educational Attainment</label>
            <input type="text" name="education[]" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Contact Number</label>
            <input type="text" name="member_contact[]" class="form-control">
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <label class="form-label">Religion</label>
            <input type="text" name="religion[]" class="form-control">
          </div>
          <div class="col-md-6 d-flex align-items-end justify-content-end">
            <button type="button" class="btn btn-danger btn-sm removeRow">Remove</button>
          </div>
        </div>
      </div>
      <!-- End Family Member Template -->
   
      <!-- Add Member Button -->
       <div><button type="button" id="addRowBtn" class="btn btn-success btn-sm mt-2">Add Family Member</button>
    </div>
  </div>
</div>
 <!-- Loading & Success -->
 <div id="loadingBar" class="progress mb-3" style="height:8px; display:none;">
      <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width:100%"></div>
    </div>
        <div class="d-flex justify-content-between">
          <button type="button" class="btn-prev">Back</button>
          <button type="submit" class="btn-next">Submit</button>
        </div>
      </div>
    </form>


    <!-- ✅ Success Confirmation Box for Census Form -->
    <div id="censusSuccessBox" class="alert alert-success text-center shadow rounded-3 p-4 mt-4" style="display:none; background-color:#ecfff1">
      <img src="/barangay_census/public/images/success-icon.png" width="40"><br>
      <h4 class="fw-bold text-success">Census Form Submitted</h4>
      <p id="householdNumberMessage"></p>
      <p>Thank you for submitting your information.</p>
      <button class="btn btn-success" onclick="location.reload()">Submit Another Household</button>
    </div>
  </div>

  <script>
    $(function(){
      let current = 1, total = 3;
      function showStep(n){
        $('.form-step').removeClass('active');
        $(`.form-step[data-step="${n}"]`).addClass('active');
      }
      $('.btn-next').click(()=>{
        if(current < total) { current++; showStep(current); }
      });
      $('.btn-prev').click(()=>{
        if(current > 1) { current--; showStep(current); }
      });
 // Add / Remove family rows
 $('#addRowBtn').click(() => {
    // Clone the first family member row
    let newRow = $('.family-member:first').clone();
    
    // Append the new row to the family members container
    $('#familyMembersContainer').append(newRow);

    // After adding the row, move the 'Add Family Member' button to the bottom
    $('#familyMembersContainer').append($('#addRowBtn'));
  });
  
  $(document).on('click', '.removeRow', function() {
    if ($('#familyMembersContainer .family-member').length > 1) {
      $(this).closest('.family-member').remove();
      // Re-append the 'Add Family Member' button to the bottom
      $('#familyMembersContainer').append($('#addRowBtn'));
    }
  });
      // AJAX submit (unchanged)
      $('#censusForm').on('submit', function(e){
        e.preventDefault();
        $('#loadingBar').fadeIn();
        $.ajax({
          type: 'POST',
          url: 'submit_census.php',
          data: $(this).serialize(),
          dataType: 'json',
          success(resp){ 
            setTimeout(()=>{
              $('#loadingBar').fadeOut();
              if(resp.status==='success'){
                $('#censusForm').hide();
                $('#censusSuccessBox').fadeIn();
                $('#householdNumberMessage')
                  .html(`Your household number is <strong>${resp.household_number}</strong>.`);
              } else {
                alert(resp.message);
              }
            },1500);
          },
          error(){
            $('#loadingBar').fadeOut();
            alert('Error submitting form. Please try again.');
          }
        });
      });
    });
  </script>
</body>
</html>
