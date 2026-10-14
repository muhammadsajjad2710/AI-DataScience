import sys
import json
import os
from PIL import Image
import google.generativeai as genai

# Configure Gemini API
API_KEY = "YOUR_API_KEY_HERE"
genai.configure(api_key=API_KEY)

def main():
    try:
        if len(sys.argv) < 2:
            print(json.dumps({"error": "No image path provided"}))
            sys.exit(1)

        image_path = sys.argv[1]

        if not os.path.exists(image_path):
            print(json.dumps({"error": "Image file not found"}))
            sys.exit(1)

        image = Image.open(image_path)

        # --- OCR / extraction logic ---
        # Note: This uses Gemini sample call you provided earlier.
        # If you do not have Gemini access, replace this block with your OCR tool (Tesseract, etc.)
        model = genai.GenerativeModel("gemini-1.5-flash")
        # supply image and instruction
        response = model.generate_content([image, "Extract the handwritten text from this image."])

        extracted_text = response.text.strip() if getattr(response, "text", None) else ""

        output = {
            "status": "success" if extracted_text else "failure",
            "image_path": image_path,
            "handwritten_text": extracted_text if extracted_text else None,
            "error": None if extracted_text else "No text detected"
        }

        print(json.dumps(output, ensure_ascii=False))
    except Exception as e:
        print(json.dumps({"error": f"Exception: {str(e)}"}))
        sys.exit(1)

if __name__ == "__main__":
    main()