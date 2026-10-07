<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class AppInfo
{
    /**
     * Cache the info during a single request lifecycle.
     */
    protected static ?array $cachedInfo = null;

    /**
     * Retrieve application version and latest git commit information.
     */
    public static function get(): array
    {
        if (static::$cachedInfo !== null) {
            return static::$cachedInfo;
        }

        $version = config('app.version', env('APP_VERSION', 'v1.0.0'));
        $commitMessage = env('APP_LAST_COMMIT_MESSAGE');
        $commitHash = env('APP_LAST_COMMIT_HASH');
        $commitDate = env('APP_LAST_COMMIT_DATE');
        $branch = env('APP_GIT_BRANCH');

        // 1. Try fetching directly via git command if available
        if (!$commitMessage || !$commitHash) {
            try {
                if (function_exists('exec') && function_exists('shell_exec')) {
                    $gitLog = @shell_exec('git log -1 --pretty=format:"%h|%s|%cd" --date=short 2>nul || git log -1 --pretty=format:"%h|%s|%cd" --date=short 2>/dev/null');
                    if ($gitLog && str_contains($gitLog, '|')) {
                        $parts = explode('|', trim($gitLog), 3);
                        $commitHash = $commitHash ?: ($parts[0] ?? null);
                        $commitMessage = $commitMessage ?: ($parts[1] ?? null);
                        $commitDate = $commitDate ?: ($parts[2] ?? null);
                    }

                    if (!$branch) {
                        $gitBranch = @shell_exec('git rev-parse --abbrev-ref HEAD 2>nul || git rev-parse --abbrev-ref HEAD 2>/dev/null');
                        if ($gitBranch) {
                            $branch = trim($gitBranch);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Silently fallback to file inspection
            }
        }

        // 2. Fallback to reading .git directory directly if exec failed or was disabled
        if (!$commitMessage && File::exists(base_path('.git/COMMIT_EDITMSG'))) {
            try {
                $msg = trim(File::get(base_path('.git/COMMIT_EDITMSG')));
                if (!empty($msg)) {
                    $lines = explode("\n", $msg);
                    $commitMessage = trim($lines[0] ?? '');
                }
            } catch (\Throwable $e) {
                // Ignore fallback error
            }
        }

        if (!$commitHash && File::exists(base_path('.git/HEAD'))) {
            try {
                $head = trim(File::get(base_path('.git/HEAD')));
                if (str_starts_with($head, 'ref:')) {
                    $refPath = trim(substr($head, 4));
                    if (!$branch) {
                        $branch = basename($refPath);
                    }
                    $fullRef = base_path('.git/' . $refPath);
                    if (File::exists($fullRef)) {
                        $commitHash = substr(trim(File::get($fullRef)), 0, 7);
                    }
                } else {
                    $commitHash = substr($head, 0, 7);
                }
            } catch (\Throwable $e) {
                // Ignore fallback error
            }
        }

        static::$cachedInfo = [
            'version' => $version ?: 'v1.0.0',
            'commit_message' => $commitMessage ?: 'Initial release',
            'commit_hash' => $commitHash ?: 'main',
            'commit_date' => $commitDate ?: null,
            'branch' => $branch ?: 'main',
            'laravel_version' => app()->version(),
        ];

        return static::$cachedInfo;
    }
}
