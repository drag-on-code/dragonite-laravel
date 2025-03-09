<?php

namespace Dragonite\Common\Helpers\Traits;

use Illuminate\Http\Response as HttpResponse;

trait Response
{
    public function getResponseMessage(int $code): ?string
    {

        return HttpResponse::$statusTexts[$code] ?? 'Unknown http status code '.htmlentities($code);
    }
}
