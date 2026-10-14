from flask import Flask, request, jsonify
import base64
import cv2
import numpy as np
import torch
from ultralytics import YOLO  # Install with: pip install ultralytics

app = Flask(__name__)

# Load YOLOv5 model
model = YOLO("yolov5su.pt")  # Download a pretrained YOLO model

@app.route("/analyze", methods=["POST"])
def analyze():
    try:
        data = request.json
        image_data = data["image"].split(",")[1]  # Extract base64 image
        image_bytes = base64.b64decode(image_data)
        image_np = np.frombuffer(image_bytes, np.uint8)
        image = cv2.imdecode(image_np, cv2.IMREAD_COLOR)

        # Convert OpenCV image to YOLO input format
        results = model(image)  # Perform object detection

        # Extract detected labels
        labels = [model.names[int(pred.cls)] for pred in results[0].boxes]

        return jsonify({"labels": labels})

    except Exception as e:
        return jsonify({"error": str(e)})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
