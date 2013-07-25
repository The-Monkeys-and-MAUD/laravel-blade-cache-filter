<?php namespace Themonkeys\BladeCacheFilter;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;

class BladeCacheFilter {
    protected function generateCacheKey($route, $request) {
        return 'route-' . Str::slug(Request::fullUrl());
    }

    public function filter($route, $request, $response = null) {
        $ttl = Config::get('blade-cache-filter::bladeCacheExpiry');
        if ($ttl > 0) {
            $key = $this->generateCacheKey($route, $request);

            if(is_null($response) && Cache::has($key)) {
                return static::convertFromCacheableResponse(Cache::get($key));
            } elseif (!is_null($response) && !Cache::has($key)) {
                // adjust the cache-control header
                $response->setClientTtl($ttl * 60); // convert from minutes to seconds
                Cache::put($key, static::convertToCacheableResponse($response), $ttl);
            }
        }
    }

    protected function convertToCacheableResponse($response) {
        return array(
            'content' => $response->getContent(),
            'headers' => $response->headers,
            'version' => $response->getProtocolVersion(),
            'statusCode' => $response->getStatusCode(),
            'charset' => $response->getCharset(),
        );
    }

    protected function convertFromCacheableResponse($cacheable) {
        $response = Response::create($cacheable['content'], $cacheable['statusCode']);
        $response->headers = $cacheable['headers'];
        $response->setCharset($cacheable['charset']);
        $response->setProtocolVersion($cacheable['version']);
        return $response;
    }

}