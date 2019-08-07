<?php


namespace Isofman\LaravelRestier;

class Restier implements HttpMethods
{
    const DEFAULT_OPTIONS = ['verify' => false, 'timeout' => 60];

    public function get(string $url, $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::get($url, $headers, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function post(string $url, $data = [], $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::post($url, $headers, $data, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function delete(string $url, $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::delete($url, $headers, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function head(string $url, $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::head($url, $headers, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function trace(string $url, $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::trace($url, $headers, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function put(string $url, $data = [], $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::put($url, $headers, $data, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function options(string $url, $data = [], $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::options($url, $headers, $data, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function patch(string $url, $data = [], $headers = [], $options = []): Package
    {
        return $this->resolveRequest(
            \Requests::patch($url, $headers, $data, $this->resolveOptions($options)), func_get_args()
        );
    }

    public function postJson(string $url, $data = [], $headers = [], $options = []): Package
    {
        return $this->post($url,
            json_encode($data),
            array_merge($headers, ['Content-Type' => 'application/json']),
            $options
        );
    }

    public function json(string $method, string $url, $data = [], $headers = [], $options = [])
    {
        $headers = array_merge($headers, ['Content-Type' => 'application/json']);
        $data = json_encode($data);
        return \Requests::request($url, $headers, $data, $method, $options);
    }

    protected function resolveRequest(\Requests_Response $request, $args): Package
    {
        return (new Package($request))->attachPayload($args['data'] ?? []);
    }

    protected function resolveOptions($options = [])
    {
        return array_merge(self::DEFAULT_OPTIONS, $options);
    }
}