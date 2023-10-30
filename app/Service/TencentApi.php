<?php

namespace App\Service;

class TencentApi implements MusicApi
{

    public function search($keyword, $type, $offset = 1, $limit = 20): array
    {
        // TODO: Implement search() method.
    }

    public function playList(string $id): array
    {
        // TODO: Implement playList() method.
    }

    public function artist(string $artistId): array
    {
        // TODO: Implement artist() method.
    }

    public function album(string $id): array
    {
        // TODO: Implement album() method.
    }

    public function song(string $songId): array
    {
        // TODO: Implement song() method.
    }

    public function lyric(string $songId, int $type): array
    {
        // TODO: Implement lyric() method.
    }

    public function url(string $songId): string
    {
        // TODO: Implement url() method.
    }

    public function topCategory(): array
    {
        // TODO: Implement topCategory() method.
    }

    public function top(): array
    {
        // TODO: Implement top() method.
    }
}