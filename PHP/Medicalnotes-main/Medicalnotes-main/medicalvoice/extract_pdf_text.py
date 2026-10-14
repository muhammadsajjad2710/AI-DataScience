#!/usr/bin/env python3
"""
Extract text from PDF files
"""

import sys
import PyPDF2
import json

def extract_pdf_text(pdf_path):
    """Extract text from a PDF file"""
    try:
        text = ""
        with open(pdf_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            num_pages = len(pdf_reader.pages)

            for page_num in range(num_pages):
                page = pdf_reader.pages[page_num]
                text += page.extract_text() + "\n"

        return text.strip()

    except Exception as e:
        return f"Error extracting PDF text: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: PDF file path not provided")
        sys.exit(1)

    pdf_path = sys.argv[1]
    extracted_text = extract_pdf_text(pdf_path)
    print(extracted_text)