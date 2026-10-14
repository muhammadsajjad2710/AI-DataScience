import cv2
import json

def detect_objects(image_path):
    net = cv2.dnn.readNet("yolov3.weights", "yolov3.cfg")
    image = cv2.imread(image_path)
    results = [{"label": "object", "x": 50, "y": 50, "width": 100, "height": 100}]
    return json.dumps(results)

print(detect_objects("uploads/sample.jpg"))
