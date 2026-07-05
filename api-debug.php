<?php
$pageTitle = 'API Debug Test';
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">API Debug Test</h5>
                </div>
                <div class="card-body">
                    <form id="testForm" class="mb-4">
                        <div class="mb-3">
                            <label for="license" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="license" value="10012022000002" placeholder="Enter license number">
                        </div>
                        <button type="submit" class="btn btn-primary">Test API</button>
                    </form>

                    <hr>

                    <h6>Response:</h6>
                    <pre id="response" class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;">
Waiting for test...
                    </pre>

                    <h6 class="mt-4">Network Timing:</h6>
                    <div id="timing" class="alert alert-info">
                        Start time will be shown here
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('testForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const license = document.getElementById('license').value;
        const responseEl = document.getElementById('response');
        const timingEl = document.getElementById('timing');

        responseEl.textContent = 'Testing API...\n';

        const startTime = performance.now();
        timingEl.innerHTML = `<strong>Start Time:</strong> ${new Date().toLocaleTimeString()}<br>Request in progress...`;

        console.log('Sending request to API...');
        console.log('License:', license);

        fetch('backend/verify-license.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                license_number: license,
                input_method: 'Manual'
            })
        })
        .then(response => {
            const endTime = performance.now();
            const duration = (endTime - startTime).toFixed(2);

            console.log('Response received!');
            console.log('Status:', response.status);
            console.log('Headers:', response.headers);
            console.log('Duration:', duration, 'ms');

            timingEl.innerHTML = `<strong>Status:</strong> ${response.status}<br>
                                  <strong>Duration:</strong> ${duration}ms<br>
                                  <strong>Content-Type:</strong> ${response.headers.get('Content-Type')}`;

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('JSON parsed successfully!');
            console.log('Data:', data);
            responseEl.textContent = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            console.error('Error:', error);
            responseEl.textContent = 'ERROR: ' + error.message + '\n\n' + error.stack;
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>