# ♻️ WasteWise AI – Garbage Classifier

WasteWise AI is a web-based garbage classification system that uses a live camera feed or uploaded images to predict waste categories using an AI backend. It provides real-time predictions along with confidence visualization using charts.

---

## Features

* 📷 **Live Camera Detection**

  * Start, stop, pause (freeze), and resume camera feed
  * Real-time AI predictions from webcam frames
  * Mirrored camera preview for natural user experience

* 📊 **Prediction Confidence Chart**

  * Displays confidence scores for all predicted classes
  * Updates dynamically using Chart.js

* 🖼️ **Image Upload Prediction**

  * Upload an image manually for classification
  * Sends image to backend for prediction
  * AI Suggestion powered with Gemini AI

* ⚡ **Real-Time Inference**

  * Automatically captures frames every 1.5 seconds
  * Sends frames to an AI prediction API

---

## 🛠️ Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript (Vanilla)
* [Chart.js](https://www.chartjs.org/) for confidence visualization
* WebRTC (`getUserMedia`) for camera access

### Backend

* AI prediction API running locally
* Endpoint:

  ```
  POST http://127.0.0.1:8001/predict
  ```
* Accepts image files and returns JSON predictions

---

## 📂 Project Structure

```
.
├── index.html        # Main frontend UI
└── README.md         # Project documentation
```

---

## 🔌 API Response Format

The backend `/predict` endpoint is expected to return JSON in the following format:

```json
{
  "class": "Plastic",
  "confidence": 0.87,
  "all_predictions": {
    "Plastic": 0.87,
    "Paper": 0.08,
    "Metal": 0.03,
    "Glass": 0.02
    ...
  }
}
```

---

## How to Use ♻️ WasteWise AI

### 1. Start the Backend

Make sure your AI model server is running on:

```
http://127.0.0.1:8001
```

### 2. Open the Frontend

Open `index.html` in a modern browser (Chrome recommended).

### 3. Live Camera Detection

1. Click **Start Camera**
2. Allow camera permissions
3. View real-time predictions
4. Use:

   * **Capture** → freeze prediction
   * **Resume** → continue live detection
   * **Stop Camera** → turn off camera

### 4. Upload Image

1. Choose an image file
2. Click **Predict Image**
3. View prediction results with AI suggestion

---

## 🔒 Permissions & Notes

* Camera access is required for live detection
* Runs fully on the client side except for AI inference
* Best used on desktop browsers
* HTTPS is needed for camera access in production

---

## 🌱 Future Improvements

* Add waste category icons
* Display history of predictions
* Improve mobile responsiveness
* Add model loading status
* Deploy backend to cloud

---

## 📜 License

This project is open-source and intended for educational and research purposes.
