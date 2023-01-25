<?php

namespace Adapti\FakerImage;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class FakerImage
{
    const BASE_URL = 'https://picsum.photos';
    const EXTENSION = 'jpg';

    public function __construct(
        private string $disk = 'public',
    )
    {
    }

    private function getUri($width, $height): string
    {
        $extension = self::EXTENSION;

        return "$width/$height.$extension";
    }

    private function getFileName(): string
    {
        $extension = self::EXTENSION;

        return Str::random(30) . ".$extension";
    }

    private function enqueueQuery($query, $parameter): string
    {
        $query .= ($query === '') ? '' : '&';
        $query .= $parameter;

        return $query;
    }

    private function mountQuery($grayscale, $blur, $randomize): string
    {
        $query = '';

        if ($grayscale) {
            $query = $this->enqueueQuery($query, 'grayscale');
        }

        if ($blur) {
            $query .= $this->enqueueQuery($query, 'blur=2');
        }

        if ($randomize) {
            $query .= $this->enqueueQuery($query, 'random=2');
        }

        if ($query !== '') {
            $query = "?$query";
        }

        return $query;
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequest($uri): string
    {
        $client = new \GuzzleHttp\Client(['base_uri' => self::BASE_URL]);
        $response = $client->request('GET', $uri, ['stream' => true]);

        return $response->getBody()->getContents();
    }

    private function saveImage($image, $path): string
    {
        $fullPath = $path . '/' . $this->getFileName();

        Storage::disk($this->disk)->put($fullPath, ($image));

        return ($this->disk === 'public') ? "/storage/$fullPath" : "/$fullPath";
    }

    /**
     * @throws GuzzleException
     */
    public function image(string $path, $width = 640, $height = 480, $grayscale = false, $blur = false, $randomize = true): string
    {
        $uri = $this->getUri($width, $height);
        $uri = $uri . $this->mountQuery($grayscale, $blur, $randomize) . '.' . self::EXTENSION;

        $image = $this->makeRequest($uri);

        return $this->saveImage($image, $path);
    }
}
