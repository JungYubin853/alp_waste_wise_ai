<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>WasteWise AI – Garbage Classifier</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 2.2em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        video {
            width: 100%;
            border-radius: 8px;
            background: black;
            transform: scaleX(-1);
            display: none;
        }

        canvas {
            display: none;
        }

        button {
            padding: 10px 16px;
            border: none;
            background: #2563eb;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 8px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        button.secondary {
            background: #6b7280;
        }

        button.secondary:hover {
            background: #4b5563;
        }

        .result {
            font-size: 18px;
            margin-top: 10px;
        }

        .confidence {
            font-weight: bold;
            color: #2563eb;
        }

        .upload-form {
            margin-top: 20px;
        }

        small {
            color: #666;
        }

        /* ========================================
           ENHANCED EXPERT ANALYSIS CARD STYLES
        ======================================== */
        
        #expert-card {
            margin-top: 20px;
            display: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
            overflow: hidden;
            position: relative;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #expert-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffd700, #4285F4, #34a853, #ea4335);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .expert-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 24px 30px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .expert-icon {
            font-size: 32px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .expert-header h3 {
            margin: 0;
            color: #667eea;
            font-size: 1.5em;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .expert-subtitle {
            color: #6b7280;
            font-size: 0.9em;
            margin: 4px 0 0 44px;
            font-style: italic;
        }

        .expert-content {
            background: white;
            padding: 30px;
            position: relative;
        }

        #gemini-result {
            line-height: 1.8;
            color: #2c3e50;
            font-size: 1.05em;
            position: relative;
            z-index: 1;
        }

        /* Beautiful text formatting for Gemini responses */
        #gemini-result::first-letter {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            float: left;
            line-height: 1;
            margin: 5px 8px 0 0;
        }

        /* Style for sections in the analysis */
        #gemini-result strong {
            color: #667eea;
            font-size: 1.1em;
            display: block;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
        }

        #gemini-result strong:first-child {
            margin-top: 0;
        }

        /* Decorative elements */
        .expert-content::after {
            content: '♻️';
            position: absolute;
            bottom: 20px;
            right: 30px;
            font-size: 80px;
            opacity: 0.05;
            z-index: 0;
        }

        /* Badge for AI-powered label */
        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: auto;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .ai-badge-icon {
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Loading state for expert card */
        .expert-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 40px;
            color: #667eea;
            font-weight: 600;
        }

        .expert-loading::before {
            content: '';
            width: 24px;
            height: 24px;
            border: 3px solid rgba(102, 126, 234, 0.2);
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Highlight important information */
        #gemini-result em {
            background: linear-gradient(120deg, #ffd70033 0%, #ffd70033 100%);
            padding: 2px 4px;
            border-radius: 3px;
            font-style: normal;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .expert-header h3 {
                font-size: 1.2em;
            }
            
            .expert-content {
                padding: 20px;
            }
            
            #gemini-result {
                font-size: 1em;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>♻️ WasteWise AI – Garbage Classifier</h1>

        <div class="grid">
            <!-- CAMERA -->
            <div class="card">
                <h3>Live Camera Detection</h3>

                <video id="video" autoplay playsinline></video>
                <canvas id="canvas"></canvas>

                <div>
                    <button onclick="startCamera()">Start Camera</button>
                    <button class="secondary" onclick="capture()">Capture</button>
                    <button class="secondary" onclick="resume()">Resume</button>
                    <button class="secondary" onclick="stopCamera()">Stop Camera</button>
                </div>

                <div class="result" id="liveResult">
                    Press "Start Camera" to begin live camera prediction.
                </div>
            </div>

            <!-- CHART -->
            <div class="card">
                <h3>Prediction Confidence</h3>
                <canvas id="chart"></canvas>
            </div>
        </div>

        <!-- ENHANCED EXPERT ANALYSIS CARD -->
        <div id="expert-card">
            <div class="expert-header">
                <span class="expert-icon">✨</span>
                <div style="flex: 1;">
                    <h3>Expert Sustainability Analysis</h3>
                    <div class="expert-subtitle">Powered by Advanced AI Intelligence</div>
                </div>
                <div class="ai-badge">
                    <span class="ai-badge-icon">🤖</span>
                    AI Insights
                </div>
            </div>
            <div class="expert-content">
                <div id="gemini-result"></div>
            </div>
        </div>

        <!-- UPLOAD IMAGE -->
        <div class="card upload-form">
            <h3>Upload Image</h3>
            <form id="uploadForm">
                <input type="file" name="image" id="imageInput" required>
                <button type="submit" id="uploadBtn">Predict Image</button>
            </form>
            <div id="loadingMessage" style="display:none; margin-top:10px; color:#2563eb;">
                ✨ AI is analyzing your waste... Please wait.
            </div>
        </div>
    </div>

    <script>
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const ctx = canvas.getContext("2d");
        const resultText = document.getElementById("liveResult");

        let stream = null;
        let intervalId = null;
        let frozen = false;

        /* ------------------ CHART ------------------ */
        const chartCtx = document.getElementById('chart').getContext('2d');
        const predictionChart = new Chart(chartCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Confidence',
                    data: [],
                    backgroundColor: '#2563eb'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1
                    }
                }
            }
        });

        /* ------------------ CAMERA ------------------ */
        function startCamera() {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(s => {
                    stream = s;
                    video.srcObject = stream;

                    video.onloadedmetadata = () => {
                        video.style.display = "block";
                        video.play();
                    };

                    frozen = false;
                    resultText.innerText = "Live prediction running";

                    requestAnimationFrame(drawLoop);

                    intervalId = setInterval(() => {
                        if (!frozen) {
                            predictFrame();
                        }
                    }, 2000);
                })
                .catch(() => alert("Camera access denied"));
        }

        function stopCamera() {
            frozen = true;

            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            clearInterval(intervalId);
            resultText.innerText = "Camera stopped";
        }

        /* ------------------ DRAW LOOP ------------------ */
        function drawLoop() {
            if (!frozen && video.videoWidth > 0) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();
            }
            requestAnimationFrame(drawLoop);
        }

        /* ------------------ PREDICTION ------------------ */
        function predictFrame() {
            canvas.toBlob(blob => {
                const formData = new FormData();
                formData.append("file", blob, "frame.jpg");

                fetch("http://127.0.0.1:8000/predict", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(updateUI)
                    .catch(console.error);
            }, "image/jpeg");
        }

        function updateUI(data) {
            const result = data.prediction ? data.prediction : data;

            resultText.innerHTML =
                `Detected: <span class="confidence">${result.class}</span>
             (${(result.confidence * 100).toFixed(1)}%)`;

            predictionChart.data.labels = Object.keys(result.all_predictions);
            predictionChart.data.datasets[0].data = Object.values(result.all_predictions);
            predictionChart.update();

            // Display Gemini Expert Analysis
            const expertCard = document.getElementById('expert-card');
            const geminiResult = document.getElementById('gemini-result');

            if (data.expert_analysis) {
                expertCard.style.display = 'block';
                geminiResult.innerHTML = data.expert_analysis.replace(/\n/g, '<br>');
            } else {
                expertCard.style.display = 'none';
            }
        }

        /* ------------------ CAPTURE / RESUME ------------------ */
        function capture() {
            frozen = true;
            video.pause();
            resultText.innerHTML = "<strong>Prediction frozen</strong>";
        }

        function resume() {
            frozen = false;
            video.play();
            resultText.innerText = "Live prediction resumed";
        }

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            const imageFile = document.getElementById('imageInput').files[0];
            const uploadBtn = document.getElementById('uploadBtn');
            const loadingMessage = document.getElementById('loadingMessage');

            if (!imageFile) return;

            uploadBtn.disabled = true;
            loadingMessage.style.display = 'block';

            formData.append("image", imageFile);
            formData.append("_token", "{{ csrf_token() }}");

            fetch("/predict", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.prediction) {
                        updateUI(data.prediction);
                    }

                    const expertCard = document.getElementById('expert-card');
                    const geminiResult = document.getElementById('gemini-result');

                    if (data.expert_analysis) {
                        expertCard.style.display = 'block';
                        geminiResult.innerHTML = data.expert_analysis;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Upload failed. Check your console for details.");
                })
                .finally(() => {
                    uploadBtn.disabled = false;
                    loadingMessage.style.display = 'none';
                });
        });
    </script>

</body>

</html>