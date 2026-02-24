<?php

namespace App\Http\Controllers;

use App\Services\XmlExport\XmlExportService;
use Illuminate\Http\Response;

class XmlFeedController extends Controller
{
    public function show(string $adapter, XmlExportService $service): Response
    {
        $available = $service->availableAdapters();

        if (!in_array($adapter, $available)) {
            abort(404, "Feed [{$adapter}] not found.");
        }

        $content = $service->getFeedContent($adapter);

        if ($content === null) {
            abort(404, "Feed [{$adapter}] has not been generated yet.");
        }

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
