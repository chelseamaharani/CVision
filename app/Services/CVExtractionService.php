<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service for extracting text from CV files (PDF, DOC, DOCX).
 *
 * Primary method uses the FastAPI backend for PDF extraction.
 * Falls back to local PHP libraries if Python is unavailable.
 */
class CVExtractionService
{
    /**
     * Extract text from a CV file at the given storage path.
     *
     * @param string $storagePath Relative path in the 'public' disk
     * @return string Extracted text content
     *
     * @throws \RuntimeException If file cannot be read or text cannot be extracted
     */
    public function extract(string $storagePath): string
    {
        $fullPath = Storage::disk('public')->path($storagePath);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("CV file not found: {$fullPath}");
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf'  => $this->extractPdf($fullPath),
            'docx' => $this->extractDocx($fullPath),
            'doc'  => $this->extractDoc($fullPath),
            default => throw new \RuntimeException("Unsupported file format: {$extension}"),
        };
    }

    /**
     * Extract text from a PDF file.
     * Uses Python/FastAPI backend if available, otherwise falls back to PHP.
     */
    private function extractPdf(string $path): string
    {
        // Use PHP PDF parser if available
        if (class_exists('\Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                $text = $pdf->getText();
                if (trim($text)) {
                    return $text;
                }
            } catch (\Throwable $e) {
                Log::warning('PHP PDF parser failed: ' . $e->getMessage());
            }
        }

        // Last resort: Use Python subprocess directly
        return $this->extractPdfViaPython($path);
    }

    /**
     * Fallback: Extract PDF text using Python subprocess.
     */
    private function extractPdfViaPython(string $path): string
    {
        $pythonPath = config('services.ai.python_path', base_path('venv/Scripts/python.exe'));

        // Auto-detect Python on Windows if default path doesn't exist
        if (!file_exists($pythonPath)) {
            $alternatives = [
                base_path('venv/Scripts/python.exe'),
                'C:\\laragon\\www\\CVision\\venv\\Scripts\\python.exe',
                'python',
                'python3',
            ];
            foreach ($alternatives as $alt) {
                if ($alt === 'python' || $alt === 'python3') {
                    $pythonPath = $alt;
                    break;
                }
                if (file_exists($alt)) {
                    $pythonPath = $alt;
                    break;
                }
            }
        }

        $script = base_path('python/extract_text.py');

        // Create a simple extraction script if it doesn't exist
        if (!file_exists($script)) {
            file_put_contents($script, <<<'PYTHON'
import sys, fitz
doc = fitz.open(sys.argv[1])
text = ""
for page in doc:
    text += page.get_text()
print(text)
doc.close()
PYTHON
            );
        }

        $escapedPath = escapeshellarg($path);
        $escapedScript = escapeshellarg($script);
        $output = shell_exec("\"{$pythonPath}\" {$escapedScript} {$escapedPath} 2>&1");

        if ($output === null) {
            throw new \RuntimeException('Failed to execute Python PDF extraction');
        }

        return trim($output);
    }

    /**
     * Extract text from a DOCX file.
     */
    private function extractDocx(string $path): string
    {
        if (!class_exists('\PhpOffice\PhpWord\IOFactory')) {
            throw new \RuntimeException('PhpWord library is required for DOCX extraction. Run: composer require phpoffice/phpword');
        }

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            return trim($text);
        } catch (\Throwable $e) {
            throw new \RuntimeException("DOCX extraction failed: {$e->getMessage()}");
        }
    }

    /**
     * Extract text from a DOC file (legacy format).
     * Requires antiword or similar tool.
     */
    private function extractDoc(string $path): string
    {
        // Try using antiword (Linux/Mac)
        $output = shell_exec("antiword " . escapeshellarg($path) . " 2>&1");
        if ($output && !str_contains($output, 'command not found')) {
            return trim($output);
        }

        // Try using catdoc (Linux/Mac)
        $output = shell_exec("catdoc " . escapeshellarg($path) . " 2>&1");
        if ($output && !str_contains($output, 'command not found')) {
            return trim($output);
        }

        throw new \RuntimeException(
            'DOC file extraction requires antiword or catdoc. ' .
            'Convert the file to PDF or DOCX format.'
        );
    }
}