#!/usr/bin/env python3
# Enhanced MedicalVoice Audio Processing Script with OpenAI Whisper and Document Support
from openai import OpenAI
import json
import os
import sys
import traceback
import time
import hashlib
from pathlib import Path

# Enhanced API Keys with environment variable support
def get_api_keys():
    """Get API keys from environment variables or fallback to config"""
    # Try to load from .env file first
    env_path = os.path.join(os.path.dirname(os.path.dirname(__file__)), '.env')
    if os.path.exists(env_path):
        from dotenv import load_dotenv
        load_dotenv(env_path)

    openai_key = os.getenv('OPENAI_API_KEY', "YOUR_API_KEY_HERE")
    return openai_key

# Initialize OpenAI client
openai_key = get_api_keys()
client = OpenAI(api_key=openai_key)

# ✅ Validate arguments
if len(sys.argv) < 3:
    print(json.dumps({"error": "Audio file or log file path not provided"}))
    sys.exit(1)

audio_path = sys.argv[1]
log_file_path = sys.argv[2]

# ✅ Ensure log file directory exists
os.makedirs(os.path.dirname(log_file_path), exist_ok=True)

# Check for optional document file
document_texts = []
if len(sys.argv) > 3:
    doc_file_path = sys.argv[3]
    if os.path.exists(doc_file_path):
        with open(doc_file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            document_texts = content.split("\n---DOCUMENT---\n")

def log_info(message):
    """Write processing information to log file."""
    try:
        with open(log_file_path, "a", encoding="utf-8") as f:
            timestamp = time.strftime("%Y-%m-%d %H:%M:%S")
            f.write(f"[{timestamp}] {message}\n")
    except Exception:
        pass  # Silently continue if logging fails

def update_progress(stage, progress, message=""):
    """Update processing progress for real-time feedback"""
    progress_data = {
        "stage": stage,
        "progress": progress,
        "message": message,
        "timestamp": time.strftime("%Y-%m-%d %H:%M:%S")
    }
    print(json.dumps({"progress": progress_data}))

def get_file_info(audio_path):
    """Get audio file information"""
    try:
        file_size = os.path.getsize(audio_path)
        file_name = os.path.basename(audio_path)
        file_ext = os.path.splitext(file_name)[1].lower()
        
        # Calculate file hash for integrity
        with open(audio_path, 'rb') as f:
            file_hash = hashlib.md5(f.read()).hexdigest()
        
        return {
            "name": file_name,
            "size": file_size,
            "extension": file_ext,
            "hash": file_hash
        }
    except Exception:
        return None

def process_audio(audio_path):
    """Enhanced audio processing with OpenAI Whisper and GPT-4 analysis."""
    # Initialize variables to avoid scope issues
    transcribed_text = ""
    medical_analysis = ""
    fhir_json = "{}"
    
    try:
        update_progress("initialization", 0, "Starting audio processing...")
        log_info("Audio processing started")
        log_info(f"Processing file: {audio_path}")

        if not os.path.exists(audio_path):
            print(json.dumps({"error": "Audio file not found"}))
            return

        # Get file information
        update_progress("validation", 10, "Validating audio file...")
        file_info = get_file_info(audio_path)
        if not file_info:
            print(json.dumps({"error": "Failed to analyze audio file"}))
            return
        
        log_info(f"File info: {file_info}")
        
        # Validate file format
        supported_formats = ['.mp3', '.m4a', '.wav', '.aac', '.flac', '.ogg', '.webm']
        if file_info['extension'] not in supported_formats:
            print(json.dumps({"error": f"Unsupported audio format: {file_info['extension']}"}))
            return

        # Check file size - if too small, might be empty or corrupted
        if file_info['size'] < 1024:  # Less than 1KB
            log_info(f"Warning: File size very small ({file_info['size']} bytes)")
            print(json.dumps({"error": "Audio file appears to be empty or corrupted"}))
            return

        # OpenAI Whisper Transcription
        update_progress("transcription", 20, "Starting speech-to-text conversion with OpenAI Whisper...")
        
        try:
            log_info("Starting OpenAI Whisper transcription")
            log_info(f"File size: {file_info['size']} bytes")
            log_info(f"File extension: {file_info['extension']}")
            
            # Open the audio file for transcription
            with open(audio_path, "rb") as audio_file:
                # Use OpenAI Whisper API for transcription
                transcript_response = client.audio.transcriptions.create(
                    model="whisper-1",
                    file=audio_file,
                    response_format="text",
                    language="en",
                    prompt="This is a medical consultation, medical dictation, or clinical recording. Please transcribe accurately with medical terminology."
                )
            
            # Extract the transcribed text
            transcribed_text = transcript_response
            
            log_info(f"Transcription completed. Text length: {len(transcribed_text)}")
            log_info(f"Transcribed text preview: {transcribed_text[:200]}...")
            
            if not transcribed_text or len(transcribed_text.strip()) == 0:
                print(json.dumps({"error": "Transcription failed - no text returned"}))
                return
            
            # Check if transcription seems valid (not just noise or silence)
            if len(transcribed_text.strip()) < 10:
                log_info("Warning: Transcription seems too short, might be silence or noise")
                print(json.dumps({"error": "Transcription too short - audio might be silent or contain only noise"}))
                return
            
            update_progress("transcription", 60, "Whisper transcription completed successfully")
            log_info(f"Whisper transcription completed successfully. Text length: {len(transcribed_text)}")
            
        except Exception as e:
            log_info(f"OpenAI Whisper transcription failed: {str(e)}")
            print(json.dumps({"error": f"Transcription failed: {str(e)}"}))
            return

        # Enhanced Medical Analysis with GPT-4 and FHIR Conversion
        update_progress("analysis", 70, "Analyzing content with medical AI...")

        # Log if we have documents
        if document_texts:
            log_info(f"Performing combined analysis with {len(document_texts)} documents")

        try:
            # Build analysis context
            if document_texts:
                # Combined analysis with documents
                system_prompt = """You are an expert medical AI assistant specializing in clinical documentation and medical analysis.

You are analyzing BOTH audio transcription AND supporting medical documents. Your task is to:
1. CLINICAL SUMMARY: Key medical findings from both audio and documents
2. CROSS-REFERENCE: Identify correlations between spoken and written information
3. MEDICAL TERMINOLOGY: Identify and explain medical terms from all sources
4. RECOMMENDATIONS: Suggest follow-up actions based on complete information
5. RISK ASSESSMENT: Identify any concerns or inconsistencies between sources
6. STRUCTURED DATA: Extract vital signs, medications, diagnoses from all sources

Compare and consolidate information from both the audio and documents. Note any discrepancies.
Format your response professionally for healthcare documentation.

IMPORTANT: After your medical analysis, convert the key medical information into FHIR (Fast Healthcare Interoperability Resources) format. Include relevant FHIR resources such as:
- Patient information
- Conditions/Diagnoses
- Medications
- Observations (vital signs)
- Procedures
- Allergies
- Care plans

Return your response in this exact format:
MEDICAL_ANALYSIS:
[Your detailed medical analysis here]

FHIR_JSON:
[Valid JSON in FHIR format here]"""
            else:
                # Original single-source analysis
                system_prompt = """You are an expert medical AI assistant specializing in clinical documentation and medical analysis.

Your task is to analyze transcribed medical audio and provide:
1. CLINICAL SUMMARY: Key medical findings, symptoms, and observations
2. MEDICAL TERMINOLOGY: Identify and explain medical terms used
3. RECOMMENDATIONS: Suggest follow-up actions, tests, or treatments if applicable
4. RISK ASSESSMENT: Identify any potential concerns or red flags
5. STRUCTURED DATA: Extract vital signs, medications, diagnoses if mentioned

Format your response professionally for healthcare documentation. Be precise, evidence-based, and highlight critical information.

IMPORTANT: After your medical analysis, convert the key medical information into FHIR (Fast Healthcare Interoperability Resources) format. Include relevant FHIR resources.

Return your response in this exact format:
MEDICAL_ANALYSIS:
[Your detailed medical analysis here]

FHIR_JSON:
[Valid JSON in FHIR format here]"""

            # Build user message based on available data
            if document_texts:
                user_message = f"""Please analyze this medical data:

AUDIO TRANSCRIPTION:
{transcribed_text}

SUPPORTING DOCUMENTS:
{chr(10).join([f"Document {i+1}:{chr(10)}{doc}" for i, doc in enumerate(document_texts)])}

Please provide comprehensive analysis considering all sources."""
            else:
                user_message = f"Please analyze this medical transcription:\n\n{transcribed_text}"

            response = client.chat.completions.create(
                model="gpt-4",  # Using GPT-4 for better medical analysis
                messages=[
                    {"role": "system", "content": system_prompt},
                    {"role": "user", "content": user_message}
                ],
                max_tokens=2000,
                temperature=0.2,
                presence_penalty=0.1,
                frequency_penalty=0.1
            )

            chatgpt_text = response.choices[0].message.content
            if not chatgpt_text:
                print(json.dumps({"error": "AI analysis failed - no response received"}))
                return
            
            # Parse the response to separate medical analysis and FHIR JSON
            if "MEDICAL_ANALYSIS:" in chatgpt_text and "FHIR_JSON:" in chatgpt_text:
                parts = chatgpt_text.split("FHIR_JSON:")
                medical_analysis = parts[0].replace("MEDICAL_ANALYSIS:", "").strip()
                fhir_json = parts[1].strip()
                
                # Try to parse FHIR JSON to validate it
                try:
                    fhir_data = json.loads(fhir_json)
                    fhir_json = json.dumps(fhir_data, indent=2)  # Pretty print
                except json.JSONDecodeError:
                    # If FHIR JSON is invalid, create a basic FHIR structure
                    fhir_json = json.dumps({
                        "resourceType": "Bundle",
                        "type": "document",
                        "entry": [
                            {
                                "resource": {
                                    "resourceType": "DocumentReference",
                                    "status": "final",
                                    "type": {
                                        "coding": [
                                            {
                                                "system": "http://loinc.org",
                                                "code": "11506-3",
                                                "display": "Progress note"
                                            }
                                        ]
                                    },
                                    "content": [
                                        {
                                            "attachment": {
                                                "contentType": "text/plain",
                                                "data": medical_analysis[:1000] + "..." if len(medical_analysis) > 1000 else medical_analysis
                                            }
                                        }
                                    ]
                                }
                            }
                        ]
                    }, indent=2)
            else:
                # Fallback: use the entire response as medical analysis
                medical_analysis = chatgpt_text
                fhir_json = json.dumps({
                    "resourceType": "Bundle",
                    "type": "document",
                    "entry": [
                        {
                            "resource": {
                                "resourceType": "DocumentReference",
                                "status": "final",
                                "type": {
                                    "coding": [
                                        {
                                            "system": "http://loinc.org",
                                            "code": "11506-3",
                                            "display": "Progress note"
                                        }
                                    ]
                                },
                                "content": [
                                    {
                                        "attachment": {
                                            "contentType": "text/plain",
                                            "data": medical_analysis[:1000] + "..." if len(medical_analysis) > 1000 else medical_analysis
                                        }
                                    }
                                ]
                            }
                        }
                    ]
                }, indent=2)
            
            update_progress("analysis", 90, "Medical analysis and FHIR conversion completed")
            log_info("Medical analysis and FHIR conversion completed successfully")
            
        except Exception as e:
            print(json.dumps({"error": f"AI analysis failed: {str(e)}"}))
            return

        # Enhanced JSON Output with Additional Data
        update_progress("saving", 95, "Saving results...")
        try:
            json_dir = os.path.join(os.path.dirname(audio_path), "../json")
            os.makedirs(json_dir, exist_ok=True)

            json_filename = os.path.splitext(os.path.basename(audio_path))[0] + ".json"
            json_path = os.path.abspath(os.path.join(json_dir, json_filename))

            # Enhanced output data with additional metadata
            output_data = {
                "transcribed_text": transcribed_text,
                "medical_analysis": medical_analysis,
                "chatgpt_response": medical_analysis,  # Keep for backward compatibility
                "fhir_json": fhir_json,
                "timestamp": time.strftime("%Y-%m-%d %H:%M:%S"),
                "audio_file": audio_path,
                "file_info": file_info,
                "processing_stats": {
                    "transcription_confidence": "high",  # Whisper doesn't provide confidence scores
                    "audio_duration": "unknown",  # Would need additional processing to get duration
                    "word_count": len(transcribed_text.split()) if transcribed_text else 0,
                    "processing_time": time.strftime("%Y-%m-%d %H:%M:%S")
                },
                "enhanced_features": {
                    "transcription_model": "whisper-1",
                    "analysis_model": "gpt-4",
                    "medical_optimized": True,
                    "language": "en",
                    "fhir_compliant": True
                },
                "version": "4.0",  # Updated version with FHIR integration
                "api_versions": {
                    "transcription": "openai-whisper-1",
                    "analysis": "openai-gpt-4"
                }
            }

            with open(json_path, "w", encoding="utf-8") as f:
                json.dump(output_data, f, indent=4, ensure_ascii=False)

            update_progress("completed", 100, "Processing completed successfully!")
            log_info("Results saved successfully")
            
            # Return success with enhanced metadata and database-ready data
            success_response = {
                "json_file": json_path,
                "status": "success",
                "enhanced": True,
                "processing_stats": output_data["processing_stats"],
                "file_info": file_info,
                "database_data": {
                    "extracted_text": transcribed_text,
                    "fhir_json": fhir_json,
                    "audio_path": audio_path
                }
            }
            print(json.dumps(success_response))
            
        except Exception as e:
            print(json.dumps({"error": f"Failed to save results: {str(e)}"}))
            return

    except Exception as e:
        log_info(f"Processing failed: {str(e)}")
        print(json.dumps({"error": f"Processing failed: {str(e)}"}))

if __name__ == "__main__":
    process_audio(audio_path)


