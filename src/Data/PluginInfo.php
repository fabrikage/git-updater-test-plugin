<?php

namespace Fabrikage\GitHubUpdater\TestPlugin\Data;

class PluginInfo
{
    public function __construct(
        public string $slug,
        public string $version,
        public string $homepage,
        public array $sections = [] // ['changelog' => '...']
    ) {}

    public function toArray(array $merge = []): array
    {
        return array_merge([
            'name'     => $this->slug,
            'slug'     => $this->slug,
            'version'  => $this->version,
            'homepage' => $this->homepage,
            'sections' => $this->sections,
        ], $merge);
    }
}
