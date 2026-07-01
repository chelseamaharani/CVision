"""
PDF Text Extraction Service
Uses PyMuPDF (fitz) to extract text from PDF files.
"""

import fitz


def extract_pdf(pdf_path: str) -> str:
    """
    Extract text from a PDF file using PyMuPDF.

    Args:
        pdf_path: Path to the PDF file

    Returns:
        Extracted text content as string
    """
    doc = fitz.open(pdf_path)
    text = ""
    for page in doc:
        text += page.get_text()
    doc.close()
    return text


def extract_pdf_from_bytes(pdf_bytes: bytes) -> str:
    """
    Extract text from PDF bytes using PyMuPDF.

    Args:
        pdf_bytes: Raw PDF file bytes

    Returns:
        Extracted text content as string
    """
    doc = fitz.open(stream=pdf_bytes, filetype="pdf")
    text = ""
    for page in doc:
        text += page.get_text()
    doc.close()
    return text