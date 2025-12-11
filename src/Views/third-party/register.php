<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Face Registration</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inclusive+Sans:ital,wght@0,300..700;1,300..700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="/public/styles/main.css">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-linear-to-br from-blue-50 to-indigo-100 min-h-screen">
  <?php if (isset($student) && is_array($student)): ?>
    <div class="min-h-screen max-w-md mx-auto bg-white shadow-xl">
      <!-- Header -->
      <div class="bg-indigo-600 text-white px-4 py-5 relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
          <h1 class="text-xl font-bold flex items-center">
            <i data-lucide="camera" class="w-6 h-6 mr-2"></i>
            Face Registration
          </h1>
          <p class="text-blue-100 text-sm mt-1">Complete your biometric registration</p>
        </div>
      </div>

      <!-- Content -->
      <div class="px-4 py-6 space-y-6">
        <!-- Student Profile Card -->
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-4 shadow-sm">
          <div class="flex items-center space-x-4">
            <div class="relative">
              <img src="<?php echo htmlspecialchars($student['image'] ?? '/public/default-avatar.png'); ?>"
                alt="Student Photo"
                onerror="this.src='/public/default-avatar.png'"
                class="w-16 h-16 rounded-full object-cover border-3 border-blue-200">
              <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                <i data-lucide="check" class="w-3 h-3 text-white"></i>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 text-lg">
                <?php echo htmlspecialchars($student['student_name'] ?? 'N/A'); ?>
              </h3>
              <div class="space-y-1 mt-1">
                <div class="flex items-center text-sm text-gray-600">
                  <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                  Form: <?php echo htmlspecialchars($student['form_no'] ?? 'N/A'); ?>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <i data-lucide="graduation-cap" class="w-4 h-4 mr-2"></i>
                  Class: <?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>


        <!-- Face Capture Section -->
        <div class="bg-white border-2 border-dashed border-gray-300 rounded-xl py-4">
          <div class="text-center py-4 px-4">
            <i data-lucide="camera" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
            <h3 class="text-lg font-medium text-gray-900">Face Capture</h3>
            <p class="text-sm text-gray-600">Position yourself and capture your face</p>
          </div>
          <div class="bg-white overflow-hidden">
            <iframe id="captureFrame"
              src="https://face.nafish.me/frame/capture"
              class="w-full border-0"
              allow="camera; microphone; autoplay"></iframe>
          </div>

        </div>

        <!--- Instructions -->
        <div class="px-4 py-3 bg-red-50 rounded-lg border border-red-200 mt-4">
          <h4 class="text-sm font-bold text-red-800 mb-2 flex items-center">
            <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 text-red-600"></i>
            Critical Instructions
          </h4>
          <ul class="text-sm text-red-700 space-y-1">
            <li class="flex items-start">
              <i data-lucide="x-circle" class="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
              Ensure you are in a well-lit area.
            </li>
            <li class="flex items-start">
              <i data-lucide="x-circle" class="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
              Position your face within the frame displayed in the capture area.
            </li>
            <li class="flex items-start">
              <i data-lucide="x-circle" class="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
              Avoid wearing hats, sunglasses, or anything that may obscure your face.
            </li>
            <li class="flex items-start">
              <i data-lucide="x-circle" class="w-4 h-4 mr-2 text-red-600 mt-0.5 shrink-0"></i>
              Follow the on-screen prompts to capture your face.
            </li>
          </ul>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="min-h-screen max-w-md mx-auto bg-white flex items-center justify-center p-6">
      <div class="text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
        </div>
        <h2 class="text-xl font-semibold text-red-600 mb-2">Invalid Student Data</h2>
        <p class="text-gray-600">Unable to load student information. Please try again.</p>
      </div>
    </div>
  <?php endif; ?>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    const iframe = document.getElementById('captureFrame');

    // Keep track of last successful height response
    let lastHeightTime = 0;
    let lastHeight = 0;

    // Minimum and default heights (mobile-first)
    const MIN_HEIGHT = 300;
    const DEFAULT_RATIO = 0.60; // 60% of viewport height

    function applyFallbackHeight() {
      const fallback = Math.max(MIN_HEIGHT, Math.round(window.innerHeight * DEFAULT_RATIO));
      iframe.style.height = fallback + 'px';
      lastHeight = fallback;
    }

    // Request the iframe to send its height via postMessage.
    function requestIframeHeight() {
      try {
        if (iframe.contentWindow) {
          iframe.contentWindow.postMessage({
            type: 'getHeight'
          }, '*');
        }
      } catch (e) {
        // ignore
      }
    }

    //dataURL to File conversion
    function dataURLtoFile(dataurl, filename) {
      var arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);
      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }
      return new File([u8arr], filename, {
        type: mime
      });
    }

    // Listen for messages from iframe
    window.addEventListener('message', async function(event) {
      const d = event.data || {};
      if (d.type === 'resize' && d.height) {
        iframe.style.height = d.height + 'px';
        lastHeight = d.height;
        lastHeightTime = Date.now();
        return;
      }

      if (d.type === 'face-confirmed' && d.payload && d.payload.image) {
        //convert data url to file
        const imageDataUrl = d.payload.image;
        const file = dataURLtoFile(imageDataUrl, 'image.jpg');
        // Submit face data via AJAX
        const formData = new FormData();
        formData.append('image', file);
        formData.append('payload', JSON.stringify(<?php echo json_encode($payload); ?>));
        formData.append('query', JSON.stringify(<?php echo json_encode($query); ?>));

        try {
          const response = await fetch('<?php echo $url; ?>', {
            method: 'POST',
            headers: {
              'Authorization': 'Bearer <?php echo $token; ?>'
            },
            body: formData
          });
          if (response.ok) {
            alert('Face data submitted successfully!');
            // Optionally redirect or update UI
          } else {
            const result = await response.json();
            alert('Failed to submit face data: ' + (result?.message || response.statusText));
          }
        } catch (e) {
          alert('Failed to submit face data. Please try again.');
          return;
        }
        return;
      }
    });

    // On iframe load, apply fallback and request height
    iframe.addEventListener('load', function() {
      applyFallbackHeight();
      requestIframeHeight();
    });

    // Periodically request height if we haven't received a recent height
    setInterval(function() {
      const now = Date.now();
      if (now - lastHeightTime > 2000) {
        requestIframeHeight();
      }
      if (now - lastHeightTime > 5000) {
        applyFallbackHeight();
      }
    }, 700);

    // On parent window resize re-request iframe height and adjust fallback
    window.addEventListener('resize', function() {
      applyFallbackHeight();
      requestIframeHeight();
    });
  </script>
</body>

</html>