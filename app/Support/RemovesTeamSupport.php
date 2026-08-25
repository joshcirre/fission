<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

final readonly class RemovesTeamSupport
{
    public function __construct(
        private Filesystem $files,
        private ?string $basePath = null,
        private ?string $stubPath = null,
    ) {}

    public function handle(): void
    {
        foreach ($this->directoriesToDelete() as $directory) {
            $this->files->deleteDirectory($this->path($directory));
        }

        foreach ($this->filesToDelete() as $file) {
            $this->files->delete($this->path($file));
        }

        foreach ($this->replacementFiles() as $target => $stubRelative) {
            $from = resource_path('stubs/no-teams/'.$stubRelative);
            $to   = $this->path($target);

            if (! file_exists($from)) {
                throw new RuntimeException("Stub missing: {$from}");
            }

            $this->files->ensureDirectoryExists(dirname($to));
            $this->files->copy($from, $to);
        }
    }

    /** @return list<string> */
    private function directoriesToDelete(): array
    {
        return [
            'app/Actions/Teams',
            'app/Notifications/Teams',
            'resources/views/pages/teams',
        ];
    }

    /** @return list<string> */
    private function filesToDelete(): array
    {
        return [
            'app/Concerns/GeneratesUniqueTeamSlugs.php',
            'app/Concerns/HasTeams.php',
            'app/Enums/TeamRole.php',
            'app/Models/Membership.php',
            'app/Models/Team.php',
            'app/Models/TeamInvitation.php',
            'app/Policies/TeamPolicy.php',
            'app/Rules/TeamName.php',
            'app/Rules/UniqueTeamInvitation.php',
            'app/Support/TeamPermissions.php',
            'database/factories/TeamFactory.php',
            'database/factories/TeamInvitationFactory.php',
            'database/migrations/2026_03_30_000003_create_teams_table.php',
            'tests/Feature/TeamsTest.php',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function replacementFiles(): array
    {
        $v = "\u{26A1}"; // ⚡ — escape only, never paste the emoji

        return [
            'app/Models/User.php' => 'app/Models/User.php',
            "resources/views/components/{$v}navigation.blade.php" => 'resources/views/components/navigation.blade.php',
            "resources/views/pages/auth/{$v}register.blade.php"   => 'resources/views/pages/auth/register.blade.php',
            "resources/views/pages/profile/{$v}index.blade.php"  => 'resources/views/pages/profile/index.blade.php',
            'routes/web.php' => 'routes/web.php',
            'tests/Feature/Auth/RegisterTest.php' => 'tests/Feature/Auth/RegisterTest.php',
        ];
    }

    private function path(string $relativePath): string
    {
        return base_path($relativePath);
    }
}
