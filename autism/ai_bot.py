from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import google.generativeai as genai

app = Flask(__name__)
CORS(app)  # Enable Cross-Origin Resource Sharing

# Configure API key
genai.configure(api_key=os.getenv("GEMINI_API_KEY"))

# Model Configuration
generation_config = {
    "temperature": 0.7,
    "top_p": 1,
    "top_k": 40,
    "max_output_tokens": 1024,
    "response_mime_type": "text/plain",
}

# Initialize Model
model = genai.GenerativeModel(
    model_name="gemini-1.5-flash",
    generation_config=generation_config,
    system_instruction="You are an expert on autism and neurodiverse individuals. Your task is to help parents understand neurodiverse individuals. Keep conversations short and engaging."
)

history = []  # Chat history

@app.route("/")
def home():
    return "AI Chatbot API is running!"

@app.route("/chat", methods=["POST"])
def chat():
    data = request.get_json()
    user_message = data.get("message", "")

    if not user_message:
        return jsonify({"response": "Please enter a valid message."})

    chat_session = model.start_chat(history=history)
    response = chat_session.send_message(user_message)

    model_response = response.text
    history.append({"role": "user", "parts": [user_message]})
    history.append({"role": "model", "parts": [model_response]})

    return jsonify({"response": model_response})

if __name__ == "__main__":
    app.run(debug=True)
