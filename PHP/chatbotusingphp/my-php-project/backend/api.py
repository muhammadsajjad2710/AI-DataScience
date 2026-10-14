import openai

# Replace with your actual OpenAI API key
openai.api_key = "YOUR_API_KEY_HERE"

try:
    response = openai.ChatCompletion.create(
        model="gpt-3.5-turbo",  # or "gpt-4" if you have access
        messages=[
            {"role": "user", "content": "Hello, who are you?"}
        ]
    )
    print("✅ API Key is valid!")
    print("Response:", response.choices[0].message["content"])

except Exception as e:
    print("❌ API Key test failed.")
    print("Error:", e)
