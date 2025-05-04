<?php

namespace Fabrikage\GitHubUpdater\TestPlugin\Data;

class ReleaseInfo
{
    public function __construct(
        public string $version,
        public string $htmlUrl,
        public ?string $changelog = null,
        public array $assets = []
    ) {}
}
