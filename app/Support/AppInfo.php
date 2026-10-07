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

        // 1. Try reading version.json generated during deployment if present
        $versionFile = base_path('version.json');
        if (File::exists($versionFile)) {
            try {
                $meta = json_decode(File::get($versionFile), true);
                if (is_array($meta)) {
                    $commitHash = $commitHash ?: ($meta['hash'] ?? null);
                    $commitMessage = $commitMessage ?: ($meta['message'] ?? null);
                    $commitDate = $commitDate ?: ($meta['date'] ?? null);
                    $branch = $branch ?: ($meta['branch'] ?? null);
                }
            } catch (\Throwable $e) {
                // Silently continue to fallback
            }
        }

        // 2. Try fetching directly via git command (with safe.directory exception)
        if (!$commitMessage || !$commitHash) {
            try {
                if (function_exists('exec') && function_exists('shell_exec')) {
                    $cmd = 'git -c safe.directory=* log -1 --pretty=format:"%h|%s|%cd" --date=short 2>nul || git -c safe.directory=* log -1 --pretty=format:"%h|%s|%cd" --date=short 2>/dev/null';
                    $gitLog = @shell_exec($cmd);
                    if ($gitLog && str_contains($gitLog, '|')) {
                        $parts = explode('|', trim($gitLog), 3);
                        $commitHash = $commitHash ?: ($parts[0] ?? null);
                        $commitMessage = $commitMessage ?: ($parts[1] ?? null);
                        $commitDate = $commitDate ?: ($parts[2] ?? null);
                    }

                    if (!$branch) {
                        $branchCmd = 'git -c safe.directory=* rev-parse --abbrev-ref HEAD 2>nul || git -c safe.directory=* rev-parse --abbrev-ref HEAD 2>/dev/null';
                        $gitBranch = @shell_exec($branchCmd);
                        if ($gitBranch) {
                            $branch = trim($gitBranch);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Silently fallback to file inspection
            }
        }

        // 3. Fallback: Parse .git/logs/HEAD (reflog) if git CLI is not accessible
        $reflogHead = base_path('.git/logs/HEAD');
        if ((!$commitMessage || !$commitHash) && File::exists($reflogHead)) {
            try {
                $lines = file($reflogHead, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if (!empty($lines)) {
                    $lastLine = end($lines);
                    // Format: <old-hash> <new-hash> <committer> <timestamp> <tz>\t<action>: <message>
                    if (str_contains($lastLine, "\t")) {
                        [$metaPart, $actionMessage] = explode("\t", $lastLine, 2);
                        $metaTokens = explode(' ', trim($metaPart));
                        if (isset($metaTokens[1]) && strlen($metaTokens[1]) >= 7) {
                            $commitHash = $commitHash ?: substr($metaTokens[1], 0, 7);
                        }

                        if (!$commitMessage && !empty($actionMessage)) {
                            // Extract message after action prefix (e.g., "commit: ...", "clone: ...", "pull: ...")
                            if (preg_match('/^(?:commit|clone|pull|merge|checkout):\s*(.+)$/i', trim($actionMessage), $matches)) {
                                $commitMessage = $matches[1];
                            } else {
                                $commitMessage = trim($actionMessage);
                            }
                        }

                        if (!$commitDate && count($metaTokens) >= 4) {
                            // Timestamp token is second to last
                            $timestamp = $metaTokens[count($metaTokens) - 2] ?? null;
                            if (is_numeric($timestamp)) {
                                $commitDate = date('Y-m-d', (int) $timestamp);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore fallback error
            }
        }

        // 4. Fallback: Reading .git/COMMIT_EDITMSG directly
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

        // 5. Fallback: Reading .git/HEAD
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
