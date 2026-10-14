import sys
import json
import os
from PIL import Image
import google.generativeai as genai

# Configure Gemini API
genai.configure(api_key="YOUR_API_KEY_HERE")

# Debug: Check arguments
if len(sys.argv) < 2:
    print(json.dumps({"error": "No image path provided"}))
    sys.exit(1)

image_path = sys.argv[1]

# Check if the file exists
if not os.path.exists(image_path):
    print(json.dumps({"error": "Image file not found"}))
    sys.exit(1)

# Load the image
try:
    image = Image.open(image_path)
except Exception as e:
    print(json.dumps({"error": f"Exception in loading image: {str(e)}"}))
    sys.exit(1)

# Perform OCR using Gemini API
try:
    model = genai.GenerativeModel("gemini-1.5-flash")
    response = model.generate_content([image, "Extract the handwritten text from this image."])

    extracted_text = response.text.strip() if response.text else ""

    # Ensure response is valid JSON
    output = {
        "debug": "Processing completed successfully",
        "handwritten_text": extracted_text if extracted_text else None,
        "error": "No text detected" if not extracted_text else None
    }

    print(json.dumps(output))  # ✅ Print a single JSON object

except Exception as e:
    print(json.dumps({"error": f"Gemini API exception: {str(e)}"}))
    sys.exit(1)
