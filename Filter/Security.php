<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class Security implements FilterInterface
{
    public function filter(mixed $value, FilterScope $scope): mixed
    {
       if (is_object($value)) {
            return $value;
        }

        $value = str_replace("\0", '', (string)$value);

        do {
            $previous = $value;
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
            $value = str_replace("\0", '', $value);
            $value = $this->stripTags($value);
        } while ($value !== $previous);

        do {
            $previous = $value;
            $value = preg_replace('/^[\s\x00-\x1f]*(?:javascript|vbscript|data)\s*:/i', '', $value);
        } while ($value !== $previous);

        return trim($value, " \f\n\r\t\v\x00");
    }

    private function stripTags(string $value): string
    {
        // Protect "<" signs that can not start a tag (like "5 <10"), because strip_tags() removes them
        $placeholder = "\u{E000}";
        $value = str_replace($placeholder, '', $value);
        $value = preg_replace('/<(?![a-zA-Z\/!?])/', $placeholder, $value);
        $value = strip_tags($value);

        return str_replace($placeholder, '<', $value);
    }
}
