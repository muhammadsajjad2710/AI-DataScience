import sys
import json
import os
from PIL import Image
import google.generativeai as genai

# Configure Gemini API
API_KEY = "YOUR_API_KEY_HERE"
genai.configure(api_key=API_KEY)

def debug(msg):
    sys.stderr.write(f"[DEBUG] {msg}\n")

try:
    debug("Script started")

    # Validate arguments
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No image path provided"}))
        sys.exit(1)

    image_path = sys.argv[1]
    debug(f"Image path received: {image_path}")

    # Check if image exists
    if not os.path.exists(image_path):
        debug("Image file not found")
        print(json.dumps({"error": "Image file not found"}))
        sys.exit(1)

    debug("Opening image...")
    image = Image.open(image_path)
    debug(f"Image opened successfully: size={image.size}, format={image.format}")

    # Perform OCR using Gemini API
    debug("Initializing Gemini model...")
    model = genai.GenerativeModel("gemini-1.5-flash")

    debug("Sending request to Gemini API...")
    response = model.generate_content([image, "Extract the handwritten text from this image."])
    debug("Response received from Gemini API")

    extracted_text = response.text.strip() if response.text else ""
    debug(f"Extracted text length: {len(extracted_text)}")

    # Prepare output
    output = {
        "status": "success" if extracted_text else "failure",
        "image_path": image_path,
        "handwritten_text": extracted_text if extracted_text else None,
        "error": None if extracted_text else "No text detected"
    }

    debug("Output JSON prepared")
    print(json.dumps(output))

except Exception as e:
    debug(f"Exception occurred: {str(e)}")
    print(json.dumps({"error": f"Exception: {str(e)}"}))
    sys.exit(1)
