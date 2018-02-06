<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class MoveHistoryWorker
{
    /**
     * @var int
     */
    private const CHUNK_SIZE = 60;

    /**
     * @var string
     */
    private const GIT_MV_WITH_HISTORY_BASH_FILE = __DIR__ . '/../bash/git-mv-with-history.sh';

    public function prependHistoryToNewPackageFiles(
        Finder $finder,
        string $monorepoDirectory,
        string $packageSubdirectory
    ): void {
        $fileInfos = iterator_to_array($finder->getIterator());

        // this is needed due to long CLI arguments overflow error
        $fileInfosChunks = array_chunk($fileInfos, self::CHUNK_SIZE, true);

        foreach ($fileInfosChunks as $fileInfosChunk) {
            $processInput = $this->createGitMoveWithHistoryProcessInput($fileInfosChunk, $packageSubdirectory);
            $process = new Process($processInput, $monorepoDirectory, null, null, null);
            $process->run();
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return mixed[]
     */
    private function createGitMoveWithHistoryProcessInput(array $fileInfos, string $packageSubdirectory): array
    {
        $processInput = [self::GIT_MV_WITH_HISTORY_BASH_FILE];
        foreach ($fileInfos as $fileInfo) {
            $processInput[] = sprintf(
                '%s=%s',
                $fileInfo->getRelativePathname(),
                $packageSubdirectory . '/' . $fileInfo->getRelativePathname()
            );
        }

        return $processInput;
    }
}
