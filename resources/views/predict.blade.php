<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>WasteWise AI – Garbage Classifier</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
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
            /* mirror preview */

            display: none;
            /* ✅ hide before camera starts */
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
        }

        button.secondary {
            background: #6b7280;
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

        <!-- UPLOAD IMAGE -->
        <div class="card upload-form">
            <h3>Upload Image</h3>
            <form action="/predict" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="image" required>
                <button type="submit">Predict Image</button>
            </form>
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
                        video.style.display = "block"; // ✅ show video now
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
            resultText.innerHTML =
                `Detected: <span class="confidence">${data.class}</span>
             (${(data.confidence * 100).toFixed(1)}%)`;

            predictionChart.data.labels = Object.keys(data.all_predictions);
            predictionChart.data.datasets[0].data = Object.values(data.all_predictions);
            predictionChart.update();
        }

        /* ------------------ CAPTURE / RESUME ------------------ */
        function capture() {
            frozen = true;
            video.pause(); // 🔥 FREEZE CAMERA
            resultText.innerHTML = "<strong>Prediction frozen</strong>";
        }

        function resume() {
            frozen = false;
            video.play(); // ▶ RESUME CAMERA
            resultText.innerText = "Live prediction resumed";
        }
    </script>

</body>

</html>
