<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamService
{
    private $path;
    private $stream;
    private $buffer = 102400;
    private $size;
    private $start = -1;
    private $end = -1;
    private $mimeType;

    public function __construct($path)
    {
        $this->path = $path;
        $this->mimeType = $this->getMimeType();
    }

    public function streamVideo()
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $this->mimeType); 
        $response->headers->set('Cache-Control', 'max-age=2592000, public');
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT');

        $this->start = 0;
        $this->size = filesize($this->path);
        $this->end = $this->size - 1;

        $response->headers->set('Accept-Ranges', 'bytes');

        // Handle range request
        if (isset($_SERVER['HTTP_RANGE'])) {
            $ranges = $this->parseRangeHeader($_SERVER['HTTP_RANGE']);
            $this->start = $ranges['start'];
            $this->end = $ranges['end'] ?? $this->size - 1;

            $response->setStatusCode(206);
            $response->headers->set('Content-Range', sprintf('bytes %d-%d/%d', $this->start, $this->end, $this->size));
            $response->headers->set('Content-Length', $this->end - $this->start + 1);
        } else {
            $response->headers->set('Content-Length', $this->size);
        }

        $response->setCallback(function () {
            $this->stream = fopen($this->path, 'rb');

            if ($this->start > 0) {
                fseek($this->stream, $this->start);
            }

            $remaining = $this->end - $this->start + 1;
            $readBytes = 0;

            while (!feof($this->stream) && $readBytes < $remaining) {
                $bytesToRead = min($this->buffer, $remaining - $readBytes);
                $data = fread($this->stream, $bytesToRead);
                $readBytes += strlen($data);
                echo $data;
                flush();
            }

            fclose($this->stream);
        });

        return $response;
    }

    private function parseRangeHeader($rangeHeader)
    {
        if (preg_match('/bytes=(\d+)-(\d+)?/', $rangeHeader, $matches)) {
            return [
                'start' => intval($matches[1]),
                'end' => isset($matches[2]) ? intval($matches[2]) : null
            ];
        }
        return ['start' => 0, 'end' => null];
    }

    private function getMimeType()
    {
        $extension = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'wmv' => 'video/x-ms-wmv',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
