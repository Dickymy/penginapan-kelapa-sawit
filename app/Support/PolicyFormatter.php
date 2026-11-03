<?php

namespace App\Support;

use Illuminate\Support\Str;

class PolicyFormatter
{
    /**
     * Parse raw policy text into structured sections with metadata, icons, and items.
     *
     * @param string|null $content
     * @return array
     */
    public static function parse(?string $content): array
    {
        if (blank($content)) {
            return [];
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", trim($content));
        $lines = explode("\n", $normalized);

        $sections = [];
        $currentSection = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            // Check if this line looks like a section header
            if (self::isHeader($trimmed)) {
                if ($currentSection) {
                    $sections[] = self::finalizeSection($currentSection);
                }

                $cleanTitle = preg_replace('/^#+\s*/', '', $trimmed);
                $cleanTitle = trim($cleanTitle, " \t\n\r\0\x0B*");

                $currentSection = [
                    'id' => Str::slug($cleanTitle) ?: 'section-' . (count($sections) + 1),
                    'title' => $cleanTitle,
                    'raw_items' => [],
                ];
            } else {
                // If there's no current section, create a default first section
                if (!$currentSection) {
                    $currentSection = [
                        'id' => 'ketentuan-umum',
                        'title' => 'Ketentuan Umum',
                        'raw_items' => [],
                    ];
                }

                $currentSection['raw_items'][] = $trimmed;
            }
        }

        if ($currentSection) {
            $sections[] = self::finalizeSection($currentSection);
        }

        return $sections;
    }

    /**
     * Determine if a line is a section header.
     */
    protected static function isHeader(string $line): bool
    {
        // Explicit markdown headers: # Title, ## Title
        if (preg_match('/^#{1,3}\s+\S+/', $line)) {
            return true;
        }

        // Must not start with list bullets or numbers
        if (preg_match('/^(\d+[\.\)]|\-|\*|•)\s+/', $line)) {
            return false;
        }

        // Must not end with a sentence-ending period unless very short
        if (str_ends_with($line, '.') && strlen($line) > 40) {
            return false;
        }

        // Length limit for a title
        if (strlen($line) > 75) {
            return false;
        }

        // Known header keywords
        $patterns = [
            '/^(syarat|ketentuan|kebijakan|tata\s+tertib|aturan|fasilitas|waktu\s+check|prosedur|hak|kewajiban)/i',
            '/^(kebijakan\s+privasi|kebijakan\s+pembatalan|syarat\s+dan\s+ketentuan)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        // Title case single line without punctuation
        if (!preg_match('/[,;:!?]/', $line) && strlen($line) <= 50 && ucwords($line) === $line) {
            return true;
        }

        return false;
    }

    /**
     * Finalize and enrich a section with appropriate icons, badges, and categorized items.
     */
    protected static function finalizeSection(array $section): array
    {
        $title = $section['title'];
        $lowerTitle = strtolower($title);

        // Assign Icon & Badge based on topic
        if (str_contains($lowerTitle, 'batal') || str_contains($lowerTitle, 'refund') || str_contains($lowerTitle, 'cancel')) {
            $icon = 'calendar-cancel';
            $badge = 'Reservasi & Pembatalan';
            $color = 'amber';
        } elseif (str_contains($lowerTitle, 'privasi') || str_contains($lowerTitle, 'data') || str_contains($lowerTitle, 'rahasia')) {
            $icon = 'shield';
            $badge = 'Perlindungan Data';
            $color = 'blue';
        } elseif (str_contains($lowerTitle, 'bayar') || str_contains($lowerTitle, 'tarif') || str_contains($lowerTitle, 'deposit')) {
            $icon = 'credit-card';
            $badge = 'Pembayaran & Keuangan';
            $color = 'emerald';
        } elseif (str_contains($lowerTitle, 'check-in') || str_contains($lowerTitle, 'check in') || str_contains($lowerTitle, 'waktu')) {
            $icon = 'clock';
            $badge = 'Jadwal & Kedatangan';
            $color = 'indigo';
        } else {
            $icon = 'home-check';
            $badge = 'Ketentuan Menginap';
            $color = 'primary';
        }

        $parsedItems = [];
        foreach ($section['raw_items'] as $raw) {
            // Check if numbered: e.g. "1. Text" or "1) Text"
            if (preg_match('/^(\d+)[\.\)]\s*(.*)$/u', $raw, $matches)) {
                $itemNum = $matches[1];
                $itemText = $matches[2];
                $isWarning = self::isWarningText($itemText);

                $parsedItems[] = [
                    'type' => 'numbered',
                    'number' => $itemNum,
                    'text' => $itemText,
                    'is_warning' => $isWarning,
                ];
            }
            // Check if bullet: e.g. "- Text", "* Text", "• Text"
            elseif (preg_match('/^[\-\*•]\s*(.*)$/u', $raw, $matches)) {
                $itemText = $matches[1];
                $isWarning = self::isWarningText($itemText);

                $parsedItems[] = [
                    'type' => 'bullet',
                    'text' => $itemText,
                    'is_warning' => $isWarning,
                ];
            } else {
                // Paragraph text
                $parsedItems[] = [
                    'type' => 'paragraph',
                    'text' => $raw,
                    'is_warning' => false,
                ];
            }
        }

        return [
            'id' => $section['id'],
            'title' => $title,
            'badge' => $badge,
            'icon' => $icon,
            'color' => $color,
            'items' => $parsedItems,
        ];
    }

    /**
     * Check if a policy item text represents a critical rule or warning.
     */
    protected static function isWarningText(string $text): bool
    {
        $lower = strtolower($text);
        $warningKeywords = [
            'dilarang',
            'tidak diperbolehkan',
            'tidak diizinkan',
            'senjata tajam',
            'minuman keras',
            'narkoba',
            'merokok di dalam kamar',
            'denda',
            'biaya penggantian',
            'dikenakan biaya 100%',
            'no-show',
            'sanksi',
        ];

        foreach ($warningKeywords as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }

        return false;
    }
}

