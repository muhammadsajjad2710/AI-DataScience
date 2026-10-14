import sys
import json
import os
from PIL import Image
import google.generativeai as genai

# Configure Gemini API
genai.configure(api_key="YOUR_API_KEY_HERE")

# Set hardcoded image path for local testing
image_path = r"C:\Users\DELL\Downloads\test.jpeg"

# Check if the file exists
if not os.path.exists(image_path):
    print(json.dumps({"error": "Image file not found at " + image_path}))
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

    output = {
        "debug": "Processing completed successfully",
        "handwritten_text": extracted_text if extracted_text else None,
        "error": "No text detected" if not extracted_text else None
    }

    print(json.dumps(output))  # ✅ Print a single JSON object only

except Exception as e:
    print(json.dumps({"error": f"Gemini API exception: {str(e)}"}))
    sys.exit(1)
