#!/var/www/html/medicalvoice/myenv/bin/python3
# ↑ This ensures the script uses your virtual environment's Python interpreter.
import assemblyai as aai
from openai import OpenAI
import json
import os
import sys
import traceback

# ✅ API Keys
aai.settings.api_key = "35f010c057384f079f110265d2c6a164"
# client = OpenAI(api_key="YOUR_API_KEY_HERE")
client = OpenAI(api_key="YOUR_API_KEY_HERE")


# ✅ Validate arguments
if len(sys.argv) < 3:
    print(json.dumps({"error": "Audio file or log file path not provided"}))
    sys.exit(1)

audio_path = sys.argv[1]
log_file_path = sys.argv[2]

# ✅ Ensure log file directory exists
os.makedirs(os.path.dirname(log_file_path), exist_ok=True)

def log_debug(message):
    """Write debug messages to the specific log file for this audio."""
    with open(log_file_path, "a", encoding="utf-8") as f:
        f.write(message + "\n")

def process_audio(audio_path):
    try:
        log_debug("=== Starting process for audio ===")
        log_debug(f"Audio path: {audio_path}")

        if not os.path.exists(audio_path):
            error_msg = f"Audio file not found: {audio_path}"
            log_debug(error_msg)
            print(json.dumps({"error": error_msg}))
            
            return

        # ✅ Transcribe Audio
        log_debug("Initializing AssemblyAI transcription...")
        transcript = aai.Transcriber().transcribe(audio_path)
        log_debug(f"Transcription completed. First 50 chars: {transcript.text[:50]}")

        # ✅ Send transcription to ChatGPT
        log_debug("Sending transcription to ChatGPT...")
        response = client.chat.completions.create(
            model="gpt-4",
            messages=[
                {"role": "system", "content": "You are a helpful medical assistant."},
                {"role": "user", "content": transcript.text}
            ]
        )

        chatgpt_text = response.choices[0].message.content
        log_debug(f"ChatGPT response received. First 50 chars: {chatgpt_text[:50]}")

        # ✅ Save JSON
        json_dir = os.path.join(os.path.dirname(audio_path), "../json")
        os.makedirs(json_dir, exist_ok=True)

        json_filename = os.path.splitext(os.path.basename(audio_path))[0] + ".json"
        json_path = os.path.abspath(os.path.join(json_dir, json_filename))

        output_data = {
            "transcribed_text": transcript.text,
            "chatgpt_response": chatgpt_text
        }

        with open(json_path, "w", encoding="utf-8") as f:
            json.dump(output_data, f, indent=4)

        log_debug(f"Saved JSON file at: {json_path}")
        print(json.dumps({"json_file": json_path}))

    except Exception as e:

        error_trace = traceback.format_exc()
        log_debug(f"Error occurred: {error_trace}")
        print(json.dumps({"error": str(e)}))

if __name__ == "__main__":
    process_audio(audio_path)


