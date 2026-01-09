# ♻️ WasteWise AI – Garbage Classifier

**WasteWise AI** The WasteWise AI website is a web-based application designed to help users identify and properly classify different types of waste using artificial intelligence. Users can interact with the system in two ways: through real-time camera detection or by uploading an image of waste. By clicking the “Start Camera” button, the website activates the user’s device camera and continuously analyzes the waste shown in front of it. The system sends captured image frames to a Python-based AI model, which classifies the waste and returns confidence scores for each category. These confidence values are displayed visually in a dynamic bar chart to help users understand the model’s decision. Users may pause the camera using the “Capture” button to freeze the current result, resume live detection, or stop the camera entirely. Alternatively, users can upload an image, after which the system performs waste classification and provides an expert sustainability explanation powered by AI, including disposal recommendations. Overall, the website demonstrates how artificial intelligence, computer vision, and web technologies can be integrated to support responsible waste management and promote sustainable behavior.

---

## Features

### 📷 Live Camera Detection

* Start, stop, pause (freeze), and resume the camera feed
* Real-time AI predictions from webcam frames
* Mirrored camera preview for a more natural user experience

### 📊 Prediction Confidence Chart

* Displays confidence scores for all predicted classes
* Dynamically updates using **Chart.js**

### 🖼️ Image Upload Prediction

* Upload images manually for classification
* Sends images to the backend for prediction
* Includes AI-generated suggestions powered by **Gemini AI**

### ⚡ Real-Time Inference

* Automatically captures frames every **1.5 seconds**
* Sends frames to the AI prediction API for processing

---

## How to Use WasteWise AI

### 1️. Start the Backend (AI Model Server)

#### Prerequisites

Ensure the following are installed:

* **Herd**
* **Python 3.10.x**
* **Visual Studio Code**

#### Setup Steps

1. Place the project folder inside your Herd directory
   **Example (Windows):**

   ```
   C:/Users/{your-device-name}/Herd
   ```

2. Open a terminal and navigate to:

   ```
   garbage-classifier2-ml/api
   ```

3. Activate the virtual environment:

   ```
   venv\Scripts\activate
   ```

4. Start the AI model server:

   ```
   uvicorn api_server:app --host 127.0.0.1 --port 8000
   ```

#### ⚠️ Port Error Fix

If **Error 13** occurs:

* Use another port (e.g., `8001`)
* Update the API endpoint in:

  * `PredictController.php`
  * `predict.blade.php`

Change:

```
http://127.0.0.1:8000/predict
```

To:

```
http://127.0.0.1:8001/predict
```

Ensure the AI server is running at:

```
http://127.0.0.1:8000 (or your chosen port)
```

---

### 2️. Start the Web Application (Frontend)

1. Open **Herd**, go to the **Sites** page, and click the **unlocked lock icon** to enable HTTPS

   > ⚠️ This is required because insecure (`http`) sites block camera access.
2. Open the project in **Visual Studio Code**
3. Run the following commands:

   ```
   composer install
   php artisan migrate
   npm install
   npm run dev
   ```

---

### 3️. Open the Application

Open the following URL in a modern browser (Chrome recommended):

```
https://alp_waste_wise_ai.test
```

---

## 🎥 Live Camera Detection

1. Click **Start Camera**
2. Allow camera permissions
3. View real-time predictions
4. Available controls:

   * **Capture** → Freeze prediction
   * **Resume** → Continue live detection
   * **Stop Camera** → Turn off camera

---

## 🖼️ Upload Image Prediction

1. Choose an image file
2. Click **Predict Image**
3. View classification results along with AI-generated suggestions

---

## 🔒 Permissions & Notes

* Camera access is required for live detection
* AI inference runs on the backend; all other logic runs client-side
* Best experience on desktop browsers
* **HTTPS is required** for camera access in production environments

---
