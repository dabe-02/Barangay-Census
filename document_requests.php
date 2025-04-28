<?php
// Include database and QR Code generator
include('../config/db.php');
include('../libraries/phpqrcode/qrlib.php');

// Generate QR Code for document request
$qrText = "http://localhost/BARANGAY_CENSUS/src/views/document_request_form.php";
$qrFile = "../../public/images/document_request_qr.png";
QRcode::png($qrText, $qrFile, QR_ECLEVEL_L, 10);

// Fetch new requests count
$query = "SELECT COUNT(*) AS new_requests FROM document_requests WHERE status = 'new'";
$result = mysqli_query($conn, $query);
$newRequests = mysqli_fetch_assoc($result)['new_requests'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Requests</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 shadow-lg rounded-lg">

        <!-- Header -->
        <div class="mb-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Document Request System</h2>
                <p class="text-gray-600">Residents can scan the QR code or click the link to request barangay documents.</p>
            </div>

            <!-- Notification Badge -->
            <div class="relative">
                <button class="p-2 bg-blue-500 text-white rounded-full focus:outline-none" onclick="toggleNotification()">
                    <span>🔔</span>
                    <!-- Notification Badge -->
                    <span id="notificationBadge" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        <?php echo $newRequests; ?>
                    </span>
                </button>
            </div>
        </div>

        <!-- QR Code & Link -->
        <div class="flex flex-wrap gap-6">
            <div class="flex-1 bg-gray-50 p-4 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-2">Request Documents Online</h3>
                <p class="text-gray-600 text-sm mb-4">Scan the QR code or use the link below.</p>

                <div class="flex items-center gap-2 mb-4">
                    <input type="text" id="requestLink" class="border p-2 rounded w-full" value="<?php echo $qrText; ?>" readonly>
                    <button onclick="copyLink()" class="p-2 bg-gray-300 rounded">📋</button>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button onclick="downloadQR()" class="bg-black text-white px-4 py-2 rounded">⬇ Download</button>
                    <button onclick="printQR()" class="bg-gray-700 text-white px-4 py-2 rounded">🖨 Print</button>
                    <button onclick="shareLink()" class="bg-blue-500 text-white px-4 py-2 rounded">🔗 Share</button>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <img src="<?php echo $qrFile; ?>" alt="QR Code" class="w-48 h-48">
                <p class="text-center text-gray-500 text-sm">Scan to request documents</p>
            </div>
        </div>

        <!-- Document Requests Table -->
        <div class="mt-8">
            <h4 class="text-xl font-semibold mb-4">Submitted Document Requests</h4>

            <!-- Search & Refresh -->
            <div class="flex items-center gap-4 mb-4">
                <input type="text" id="searchInput" placeholder="Search..." class="p-2 border w-full rounded-md">
            </div>

            <!-- Refresh -->
            <button class="bg-gray-800 text-white px-4 py-2 rounded" onclick="loadRequests()">🔄 Refresh</button>

            <!-- Requests Table -->
            <div class="overflow-x-auto mt-4 max-h-96 overflow-y-auto">
                <table class="min-w-full bg-white border border-gray-300 text-sm">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="border p-2">Full Name</th>
                            <th class="border p-2">Email</th>
                            <th class="border p-2">Address</th>
                            <th class="border p-2">Contact</th>
                            <th class="border p-2">Document Type</th>
                            <th class="border p-2">Purpose</th>
                            <th class="border p-2">Date Requested</th>
                            <th class="border p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
let allRequests = [];

function toggleNotification() {
    alert('There are new document requests. Please check the table.');
}

function copyLink() {
    const link = document.getElementById("requestLink");
    link.select();
    document.execCommand("copy");
    alert("Link copied: " + link.value);
}

function downloadQR() {
    const a = document.createElement("a");
    a.href = "<?php echo $qrFile; ?>";
    a.download = "document_request_qr.png";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function printQR() {
    const w = window.open("");
    w.document.write("<img src='<?php echo $qrFile; ?>' style='width:300px;'>");
    w.document.close();
    w.print();
}

function shareLink() {
    if (navigator.share) {
        navigator.share({
            title: "Document Request Form",
            url: document.getElementById("requestLink").value
        }).catch(err => alert("Sharing failed: " + err));
    } else {
        alert("Your browser doesn't support the Share API.");
    }
}

function renderRequests(data) {
    let tableContent = data.length ? data.map(request => `
        <tr class="border">
            <td class="p-2">${request.full_name}</td>
            <td class="p-2">${request.email}</td>
            <td class="p-2">${request.complete_address}</td>
            <td class="p-2">${request.contact_number}</td>
            <td class="p-2">${request.document_type}</td>
            <td class="p-2">${request.purpose}</td>
            <td class="p-2">${request.date_requested}</td>
            <td class="p-2 space-x-2">
                <button class="bg-green-600 text-white px-2 py-1 rounded print-btn" onclick='printRequest(${JSON.stringify(request)})'>🖨 Print</button>
                <button class="bg-red-500 text-white px-2 py-1 rounded verify-btn" data-id="${request.id}">Verify</button>
            </td>
        </tr>
    `).join('') : `<tr><td colspan="8" class="text-center p-4">No document requests found.</td></tr>`;

    $("#requestsTable").html(tableContent);
}

function loadRequests(updateBadge = true) {
    $.ajax({
        url: "fetch_document_requests.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            allRequests = response;
            const filtered = filterRequests($("#searchInput").val());
            renderRequests(filtered);
            if (updateBadge) updateNotificationBadge();
        },
        error: function () {
            alert("Error loading data.");
        }
    });
}

function updateNotificationBadge() {
    const count = allRequests.filter(req => req.status === "new").length;
    $("#notificationBadge").text(count);
}

function filterRequests(searchTerm) {
    searchTerm = searchTerm.toLowerCase();
    return allRequests.filter(req =>
        req.full_name.toLowerCase().includes(searchTerm) ||
        req.email.toLowerCase().includes(searchTerm) ||
        req.document_type.toLowerCase().includes(searchTerm)
    );
}

function printRequest(request) {
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Document Request</title>
                <style>
                    body { font-family: sans-serif; padding: 20px; }
                    h2 { margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    td, th { padding: 10px; border: 1px solid #ccc; }
                </style>
            </head>
            <body>
                <h2>Document Request Details</h2>
                <table>
                    <tr><th>Full Name</th><td>${request.full_name}</td></tr>
                    <tr><th>Email</th><td>${request.email}</td></tr>
                    <tr><th>Address</th><td>${request.complete_address}</td></tr>
                    <tr><th>Contact</th><td>${request.contact_number}</td></tr>
                    <tr><th>Document Type</th><td>${request.document_type}</td></tr>
                    <tr><th>Purpose</th><td>${request.purpose}</td></tr>
                    <tr><th>Date Requested</th><td>${request.date_requested}</td></tr>
                </table>
                <br>
                <button onclick="window.print()">Print this page</button>
            </body>
        </html>
    `);
    printWindow.document.close();
}

function pollNewRequests() {
    $.ajax({
        url: "fetch_new_requests_count.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            $("#notificationBadge").text(data.new_requests);
        }
    });
}

$(document).ready(function () {
    loadRequests(); // Load initial data
    $("#searchInput").on("input", function () {
        const filtered = filterRequests($(this).val());
        renderRequests(filtered);
    });

    $(document).on("click", ".verify-btn", function () {
        const id = $(this).data("id");
        $.post("toggle_verification.php", { id: id }, function (res) {
            if (res.status === "success") {
                loadRequests(); // Refresh table and badge
            } else {
                alert("Error updating status.");
            }
        }, "json");
    });

    setInterval(pollNewRequests, 5000); // Real-time polling
});
</script>



</body>
</html> 