# # `pip3 install assemblyai` (macOS)
# # `pip install assemblyai` (Windows)

# # import assemblyai as aai

# # aai.settings.api_key = "35f010c057384f079f110265d2c6a164"
# # transcriber = aai.Transcriber()

# # transcript = transcriber.transcribe("https://assembly.ai/news.mp4")
# # # transcript = transcriber.transcribe("./my-local-audio-file.wav")

# # print(transcript.text)

# import assemblyai as aai

# # Set your AssemblyAI API key
# aai.settings.api_key = "35f010c057384f079f110265d2c6a164"

# # Create a transcriber instance
# transcriber = aai.Transcriber()

# # Path to your local audio file
# audio_path = "D:/Artificial Intelligence/Lang chain/sampleaudio.mp4"

# # Transcribe the local file
# transcript = transcriber.transcribe(audio_path)

# # Print the transcribed text
# print("Transcribed Text:\n", transcript.text)
#$api_key = 'YOUR_API_KEY_HERE';


# import assemblyai as aai
# import openai
# import json
# import os
# import sys

# # Set API keys
# aai.settings.api_key = "35f010c057384f079f110265d2c6a164"
# openai.api_key = "rtOXvCvBbC2O_uZ-AA"



# def process_audio(audio_path):
#     try:
#         # Check if file exists
#         if not os.path.exists(audio_path):
#             print(json.dumps({"error": f"Audio file not found: {audio_path}"}))
#             return

#         # Transcribe audio
#         transcriber = aai.Transcriber()
#         transcript = transcriber.transcribe(audio_path)

#         # Send transcript to ChatGPT
#         client = openai.OpenAI(api_key=openai.api_key)
#         response = client.chat.completions.create(
#             model="gpt-4",
#             messages=[
#                 {"role": "system", "content": "You are a helpful assistant."},
#                 {"role": "user", "content": transcript.text}
#             ]
#         )

#         # Save output JSON
#         json_filename = os.path.join(os.path.dirname(audio_path), os.path.splitext(os.path.basename(audio_path))[0] + ".json")
#         output_data = {
#             "transcribed_text": transcript.text,
#             "chatgpt_response": response.choices[0].message.content
#         }

#         with open(json_filename, "w", encoding="utf-8") as json_file:
#             json.dump(output_data, json_file, indent=4)

#         # ✅ Only print the final JSON response (no extra prints)
#         print(json.dumps({"json_file": json_filename}))

#     except Exception as e:
#         print(json.dumps({"error": str(e)}))

# if __name__ == "__main__":
#     if len(sys.argv) > 1:
#         process_audio(sys.argv[1])
#     else:
#         print(json.dumps({"error": "No audio file provided"}))
import assemblyai as aai
import openai
import json
import os
import sys

# Set your API keys
aai.settings.api_key = "35f010c057384f079f110265d2c6a164"
openai_client = openai.OpenAI(api_key="YOUR_API_KEY_HERE")  # Replace with your actual key


def process_audio(audio_path):
    try:
        if not os.path.exists(audio_path):
            print(json.dumps({"error": f"Audio file not found: {audio_path}"}))
            return

        # Transcribe audio using AssemblyAI
        transcriber = aai.Transcriber()
        transcript = transcriber.transcribe(audio_path)

        # ChatGPT request using OpenAI Python SDK (>=1.0.0)
        response = openai_client.chat.completions.create(
            model="gpt-4",  # or "gpt-3.5-turbo"
            messages=[
                {"role": "system", "content": "You are a helpful assistant."},
                {"role": "user", "content": transcript.text}
            ]
        )

        # Format and save response
        output_data = {
            "transcribed_text": transcript.text,
            "chatgpt_response": response.choices[0].message.content
        }

        json_filename = os.path.join(
            os.path.dirname(audio_path),
            os.path.splitext(os.path.basename(audio_path))[0] + ".json"
        )

        with open(json_filename, "w", encoding="utf-8") as f:
            json.dump(output_data, f, indent=4)

        print(json.dumps({"json_file": json_filename}))  # ✅ Final output

    except Exception as e:
        print(json.dumps({"error": str(e)}))


if __name__ == "__main__":
    if len(sys.argv) > 1:
        process_audio(sys.argv[1])
    else:
        print(json.dumps({"error": "No audio file provided"}))
