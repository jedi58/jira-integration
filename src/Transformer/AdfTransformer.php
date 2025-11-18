<?php

namespace Inachis\Component\JiraIntegration\Transformer;

class AdfTransformer
{
    private static $instance;

    /**
     * Returns a singleton instance of this class
     * @return AdfTransformer The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Turn formatted ADF content into a string
     * @param mixed $description
     * @return string
     */
    public function transformFromAdf(mixed $description): string
    {
        if (!empty($description->content)) {
            return $this->transformFromAdf($description->content) .
                ($description->type == 'paragraph' ? PHP_EOL : '');
        }
        elseif (is_array($description)) {
            $output = '';
            foreach ($description as $item) {
                $output .= $this->transformFromAdf($item);
            }
            return $output;
        }
        elseif (!empty($description->text)) {
            return $description->text;
        }
        elseif (!empty($description->type) && $description->type == 'hardBreak') {
            return PHP_EOL;
        }
        elseif (!empty($description->type) && $description->type == 'rule') {
            return PHP_EOL . '---' . PHP_EOL;
        }

        return '';
    }

    /**
     * Turn a string into structured ADF JSON
     * @param $description
     * @return string|object|array
     */
    public function transformToAdf($description) : array
    {
        return [
            'type' => 'doc',
            'version' => 1,
            'content' => $this->convertToAdf(explode(PHP_EOL . PHP_EOL, $description)),
        ];
    }

    /**
     * Turns a line of text into an ADF partial
     * @param $lines
     * @return array
     */
    private function convertToAdf($lines) : array
    {
        $output = [];
        if (is_array($lines)) {
            foreach ($lines as $line) {
                $output[] = [
                    'type' => 'paragraph',
                    'content' => $this->convertToAdf($line),
                ];
            }
        } elseif (is_string($lines) && !empty($lines)) {
            $output[] = [
                'type' => 'text',
                'text' => $lines,
            ];
        } elseif (is_string($lines) && empty($lines)) {
            $output[] = [
                'type' => 'hardBreak',
            ];
        }
        return $output;
    }
}
