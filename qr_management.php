<?php
include('../config/db.php');
include('../libraries/phpqrcode/qrlib.php');

// Fetch sitios and assigned BHWs
$sitioQuery = "SELECT id, sitio_name, bhw_incharge FROM sitio";
$sitioResult = $conn->query($sitioQuery);

$sitios = [];
while ($row = $sitioResult->fetch_assoc()) {
    $sitios[$row['id']] = [
        'name' => $row['sitio_name'],
        'bhw' => $row['bhw_incharge'],
        'residents' => []
    ];
}

// Fetch residents
$censusQuery = "SELECT c.id AS census_id, c.household_head AS name, c.household_number AS household, 
                COALESCE(c.date_recorded, c.created_at) AS date_recorded, c.sitio_id 
                FROM census_data c
                ORDER BY c.sitio_id, date_recorded DESC";

$censusResult = $conn->query($censusQuery);

while ($row = $censusResult->fetch_assoc()) {
    if (isset($sitios[$row['sitio_id']])) {
        $sitios[$row['sitio_id']]['residents'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Census Data & QR Code Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 font-sans p-4">
    <div class="w-full mt-4 px-2">
        <div class="bg-white w-full p-8 shadow-lg rounded-lg">
            <h2 class="text-4xl font-bold mb-8 text-center text-green-800">Census Data & QR Code Management</h2>

            <!-- QR Code Section -->
            <div class="flex flex-col lg:flex-row gap-6 mb-10">
                <div class="flex-1 bg-gray-50 p-6 rounded-lg shadow">
                    <h3 class="text-2xl font-semibold mb-3">Scan QR Code</h3>
                    <p class="text-gray-600 text-sm mb-4">Scan the QR code or click the link to update census data.</p>

                    <div class="flex items-center gap-2 mb-4">
                        <input type="text" id="censusLink" class="border p-3 rounded w-full text-sm" value="http://192.168.1.18/BARANGAY_CENSUS/src/views/census_form.php" readonly>
                        <button onclick="copyLink()" class="p-3 bg-gray-300 rounded">📋</button>
                    </div>

                    <div class="flex gap-3 flex-wrap">
                        <button onclick="downloadQR()" class="bg-black text-white px-4 py-2 rounded-lg text-sm">⬇ Download QR</button>
                        <button onclick="printQR()" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">🖨 Print QR</button>
                        <button onclick="shareLink()" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm">🔗 Share Link</button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow flex items-center justify-center w-full lg:w-auto">
                    <div class="text-center">
                        <img src="../../public/images/census_qr.png" alt="QR Code" class="w-60 h-60 mx-auto mb-2">
                        <p class="text-center text-gray-500 text-sm">Scan to update census</p>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="mt-4">
                <input type="text" id="searchInput" class="border p-3 rounded w-full mb-6" placeholder="Search residents...">
            </div>

            <!-- Sitio Carousel -->
            <div id="sitioCarousel" class="relative">
                <?php $index = 0; foreach ($sitios as $sitioId => $sitio): ?>
                    <div class="sitio-slide <?php echo $index === 0 ? 'block' : 'hidden'; ?> bg-white p-5 rounded-lg shadow-md w-full">
                        <h3 class="text-xl font-bold text-green-700 mb-3"><?php echo $sitio['name']; ?> 
                            <span class="text-gray-500 text-sm">(BHW: <?php echo $sitio['bhw']; ?>)</span>
                        </h3>

                        <div style="max-height: 450px; overflow-y: auto;">
                            <table class="table-auto w-full border border-gray-200 text-sm">
                                <thead class="bg-green-100">
                                    <tr>
                                        <th class="px-4 py-2 border">Household</th>
                                        <th class="px-4 py-2 border">Resident Name</th>
                                        <th class="px-4 py-2 border">Date Recorded</th>
                                        <th class="px-4 py-2 border">QR Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sitio['residents'] as $resident): 
                                        $residentQrText = "http://192.168.1.18/BARANGAY_CENSUS/src/views/resident_info.php?resident_id=" . $resident['census_id'];
                                        $qrFilePath = "../../public/images/resident_qr_" . $resident['census_id'] . ".png";
                                        QRcode::png($residentQrText, $qrFilePath, QR_ECLEVEL_L, 10);
                                    ?>
                                    <tr class="census-row hover:bg-gray-50">
                                        <td class="px-4 py-2 border"><?php echo $resident['household']; ?></td>
                                        <td class="px-4 py-2 border"><?php echo $resident['name']; ?></td>
                                        <td class="px-4 py-2 border"><?php echo date("F j, Y", strtotime($resident['date_recorded'])); ?></td>
                                        <td class="px-4 py-2 border text-center">
                                            <img src="<?php echo $qrFilePath; ?>" alt="QR" class="w-16 h-16 mx-auto mb-1 cursor-pointer" 
                                                 onclick="openModal('<?php echo $qrFilePath; ?>')">
                                            <div class="flex justify-center gap-2 text-xs text-blue-500">
                                                <button onclick="downloadQR('<?php echo $qrFilePath; ?>')">Download</button>
                                                <button onclick="printQR('<?php echo $qrFilePath; ?>')">Print</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php $index++; endforeach; ?>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button onclick="changeSlide(-1)" class="bg-green-700 text-white px-4 py-2 rounded-lg">← Previous</button>
                    <button onclick="changeSlide(1)" class="bg-green-700 text-white px-4 py-2 rounded-lg">Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for expanding QR Code -->
    <div id="qrModal" class="hidden fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg relative">
            <span onclick="closeModal()" class="absolute top-2 right-2 text-3xl cursor-pointer">&times;</span>
            <img id="qrModalImage" src="" alt="QR Code" class="w-72 h-72 mx-auto">
        </div>
    </div>

    <!-- Scripts -->
    <script>
    // Function to copy the link to clipboard
    function copyLink() {
        const copyText = document.getElementById("censusLink");
        copyText.select();
        document.execCommand("copy");
        alert("Link copied: " + copyText.value);
    }

    // Function to download the QR Code
    function downloadQR(qrPath = "../../public/images/census_qr.png") {
        const a = document.createElement("a");
        a.href = qrPath;
        a.download = "qr_code.png";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    // Function to print the QR Code
    function printQR(qrPath = "../../public/images/census_qr.png") {
        const qrWindow = window.open("", "_blank");
        qrWindow.document.write("<img src='" + qrPath + "' style='width:300px;'>");
        qrWindow.document.close();
        qrWindow.print();
    }

    // Function to share the link
    function shareLink() {
        const url = document.getElementById("censusLink").value;
        if (navigator.share) {
            navigator.share({
                title: "Census Form",
                url: url
            }).catch(err => alert("Sharing failed: " + err));
        } else {
            alert("Web Share API is not supported in this browser.");
        }
    }

    // Site Carousel Logic: Handle slide navigation
    let currentSlide = 0;
    const slides = document.querySelectorAll('.sitio-slide');

    // Function to update the visibility of the slides based on currentSlide index
    function updateSlideVisibility() {
        slides.forEach((slide, index) => {
            if (index === currentSlide) {
                slide.classList.remove('hidden');
            } else {
                slide.classList.add('hidden');
            }
        });
    }

    // Function to change slide direction (previous or next)
    function changeSlide(direction) {
        slides[currentSlide].classList.add('hidden');
        currentSlide += direction;
        if (currentSlide < 0) currentSlide = slides.length - 1;
        if (currentSlide >= slides.length) currentSlide = 0;
        updateSlideVisibility();
    }

    // Modal functionality: open and close the modal for QR Code
    function openModal(qrPath) {
        const modal = document.getElementById("qrModal");
        const modalImage = document.getElementById("qrModalImage");
        modalImage.src = qrPath;
        modal.classList.remove("hidden");
    }

    function closeModal() {
        const modal = document.getElementById("qrModal");
        modal.classList.add("hidden");
    }

    // Search Residents: Handle filtering of residents based on the search input
    $(document).ready(function () {
        $("#searchInput").on("keyup", function () {
            const value = $(this).val().toLowerCase();
            $(".census-row").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
    </script>
</body>
</html>
