<?php

namespace Fabrikage\GitUpdater\TestPlugin\Data;

class PluginUpdate
{
    public function __construct(
        public string $slug,
        public string $pluginFile,
        public string $newVersion,
        public string $packageUrl,
        public string $infoUrl
    ) {}

    public function toArray(array $merge = []): array
    {
        return array_merge([
            'slug'        => $this->slug,
            'plugin'      => $this->pluginFile,
            'new_version' => $this->newVersion,
            'package'     => $this->packageUrl,
            'url'         => $this->infoUrl,
        ], $merge);
    }
}
