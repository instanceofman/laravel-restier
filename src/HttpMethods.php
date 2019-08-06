<?php


namespace Isofman\LaravelRestier;


interface HttpMethods
{
    public function get(string $url, $headers = [], $options = []) : Package;

    public function post(string $url, $data = [], $headers = [], $options = []) : Package;

    public function delete(string $url, $headers = [], $options = []) : Package;

    public function head(string $url, $headers = [], $options = []) : Package ;

    public function trace(string $url, $headers = [], $options = []) : Package;

    public function put(string $url, $headers = [], $data = [], $options = []) : Package;

    public function options(string $url, $headers = [], $data = [], $options = []) : Package;

    public function patch(string $url, $headers, $data = [], $options = []) : Package;
}