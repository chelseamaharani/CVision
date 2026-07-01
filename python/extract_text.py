import sys, fitz
doc = fitz.open(sys.argv[1])
print(doc[0].get_text() if doc else "")
doc.close()