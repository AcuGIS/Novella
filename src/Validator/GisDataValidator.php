<?php

declare(strict_types=1);

namespace GeoLibre\Validator;

use Psr\Http\Message\UploadedFileInterface;

class GisDataValidator extends Validator
{
    public function validate(array $data, array $rules = []): bool
    {
        // Default rules for GIS data validation
        $defaultRules = [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'westBound' => 'numeric',
            'eastBound' => 'numeric',
            'southBound' => 'numeric',
            'northBound' => 'numeric',
            'pointOfContactEmail' => 'email',
            'distributionUrl' => 'url'
        ];

        // Merge default rules with any custom rules passed in
        $rules = array_merge($defaultRules, $rules);

        return parent::validate($data, $rules);
    }
} 