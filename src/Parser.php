<?php

namespace App;

use App\Url\Url;
use App\Url\UrlCheck;
use DiDom\Document;
use Psr\Http\Message\ResponseInterface;

class Parser
{
    private ResponseInterface $response;
    private Document $document;
    private Url $url;

    public function __construct(ResponseInterface $response, Url $Url)
    {
        $this->response = $response;
        $this->document = new Document();
        $html = $response->getBody()->__toString();
        $this->document->loadHtml($html);
        $this->url = $Url;
    }

    public function parseResponse(): UrlCheck
    {
        $response = $this->response;

        $code = $response->getStatusCode();

        $h1 = $this->getTag('h1');
        $title = $this->getTag('title');
        $metaNameS = $this->getTag('meta[name="description"]');
        $description = !$metaNameS ? "" : $metaNameS->getAttribute('content');

        $h1 = !$h1 ? "" : $h1->text();
        $title = !$title ? "" : $title->text();

        return new UrlCheck([
            'url_id' => $this->url->getId(),
            'status_code' => $code,
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
        ]);
    }

    private function getTag(string $tagSearch)
    {
        $document = $this->document;
        return $document->find($tagSearch)[0];
    }
}
