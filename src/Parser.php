<?php

namespace App;

use App\Url\Url;
use App\Url\UrlCheck;
use DiDom\Document;
use Psr\Http\Message\ResponseInterface;

class Parser
{
    private ResponseInterface $response;

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
        $metaNameS = $this->getTag('meta[name="description"]', false);
        $description = !$metaNameS ? "" : $metaNameS->getAttribute('content');

        return new UrlCheck([
            'url_id' => $this->url->getId(),
            'status_code' => $code,
            'h1' => $h1 ?? '',
            'title' => $title ?? '',
            'description' => $description ?? '',
        ]);
    }

    private function getTag($tagSearch, $getText = true)
    {
        $document = $this->document;
        if ($document->has($tagSearch)) {
            if ($getText) {
                return $document->find($tagSearch)[0]->text();
            }
            $res = $document->find($tagSearch)[0];
        } else {
            $res = '';
        }
        return $res;
    }
}
