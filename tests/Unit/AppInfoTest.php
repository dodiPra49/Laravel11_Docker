<?php

namespace Tests\Unit;

use App\Support\AppInfo;
use Tests\TestCase;

class AppInfoTest extends TestCase
{
    public function test_it_returns_application_info_with_version_and_commit(): void
    {
        $info = AppInfo::get();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('commit_message', $info);
        $this->assertArrayHasKey('commit_hash', $info);
        $this->assertArrayHasKey('branch', $info);
        $this->assertArrayHasKey('laravel_version', $info);

        $this->assertNotEmpty($info['version']);
        $this->assertNotEmpty($info['commit_message']);
    }

    public function test_it_renders_version_card_partial(): void
    {
        $info = AppInfo::get();
        $rendered = view('partials.app-version-card')->render();

        $this->assertStringContainsString($info['version'], $rendered);
        $this->assertStringContainsString($info['commit_message'], $rendered);
        $this->assertStringContainsString('#' . $info['commit_hash'], $rendered);
        $this->assertStringContainsString('app-version-card', $rendered);
    }
}
