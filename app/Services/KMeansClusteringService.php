<?php

namespace App\Services;

class KMeansClusteringService
{
    public function cluster(array $records, int $k = 4): array
    {
        if ($k <= 0 || empty($records)) {
            return [];
        }

        $normalized = $this->normalize($records);
        $centroids = $this->initializeCentroids($normalized, $k);

        for ($iteration = 0; $iteration < 15; $iteration++) {
            $assignments = [];
            foreach ($normalized as $index => $record) {
                $bestCluster = 0;
                $bestDistance = INF;

                foreach ($centroids as $clusterIndex => $centroid) {
                    $distance = $this->distance($record, $centroid);
                    if ($distance < $bestDistance) {
                        $bestDistance = $distance;
                        $bestCluster = $clusterIndex;
                    }
                }

                $assignments[$index] = $bestCluster;
            }

            $newCentroids = [];
            for ($clusterIndex = 0; $clusterIndex < $k; $clusterIndex++) {
                $members = array_values(array_filter($normalized, function ($_, $index) use ($assignments, $clusterIndex) {
                    return ($assignments[$index] ?? 0) === $clusterIndex;
                }, ARRAY_FILTER_USE_BOTH));

                if (empty($members)) {
                    $newCentroids[$clusterIndex] = $centroids[$clusterIndex];
                    continue;
                }

                $newCentroids[$clusterIndex] = $this->average($members);
            }

            if ($this->centroidsEqual($centroids, $newCentroids)) {
                break;
            }

            $centroids = $newCentroids;
        }

        $clusters = [];
        for ($clusterIndex = 0; $clusterIndex < $k; $clusterIndex++) {
            $members = [];
            foreach ($normalized as $index => $record) {
                if (($assignments[$index] ?? 0) === $clusterIndex) {
                    $members[] = $records[$index];
                }
            }

            $clusters[] = [
                'cluster' => $clusterIndex + 1,
                'members' => $members,
                'centroid' => $this->centroidSummary($centroids[$clusterIndex] ?? []),
            ];
        }

        usort($clusters, function ($left, $right) {
            return ($right['centroid']['idm'] ?? 0) <=> ($left['centroid']['idm'] ?? 0);
        });

        return $clusters;
    }

    private function normalize(array $records): array
    {
        $normalized = [];
        foreach ($records as $record) {
            $normalized[] = [
                'iks' => (float) ($record['skor_iks'] ?? 0),
                'ike' => (float) ($record['skor_ike'] ?? 0),
                'ikl' => (float) ($record['skor_ikl'] ?? 0),
                'idm' => (float) ($record['skor_komposit'] ?? 0),
            ];
        }

        return $normalized;
    }

    private function initializeCentroids(array $records, int $k): array
    {
        $centroids = [];
        $step = max(1, intdiv(count($records), $k));
        for ($i = 0; $i < $k; $i++) {
            $index = min($i * $step, max(0, count($records) - 1));
            $centroids[] = $records[$index];
        }

        return $centroids;
    }

    private function distance(array $left, array $right): float
    {
        return sqrt(
            pow(($left['iks'] ?? 0) - ($right['iks'] ?? 0), 2)
            + pow(($left['ike'] ?? 0) - ($right['ike'] ?? 0), 2)
            + pow(($left['ikl'] ?? 0) - ($right['ikl'] ?? 0), 2)
            + pow(($left['idm'] ?? 0) - ($right['idm'] ?? 0), 2)
        );
    }

    private function average(array $records): array
    {
        $count = count($records);
        if ($count === 0) {
            return ['iks' => 0, 'ike' => 0, 'ikl' => 0, 'idm' => 0];
        }

        return [
            'iks' => array_sum(array_column($records, 'iks')) / $count,
            'ike' => array_sum(array_column($records, 'ike')) / $count,
            'ikl' => array_sum(array_column($records, 'ikl')) / $count,
            'idm' => array_sum(array_column($records, 'idm')) / $count,
        ];
    }

    private function centroidsEqual(array $left, array $right): bool
    {
        if (count($left) !== count($right)) {
            return false;
        }

        foreach ($left as $index => $centroid) {
            if ($this->distance($centroid, $right[$index] ?? []) > 0.000001) {
                return false;
            }
        }

        return true;
    }

    private function centroidSummary(array $centroid): array
    {
        return [
            'iks' => round($centroid['iks'] ?? 0, 4),
            'ike' => round($centroid['ike'] ?? 0, 4),
            'ikl' => round($centroid['ikl'] ?? 0, 4),
            'idm' => round($centroid['idm'] ?? 0, 4),
        ];
    }
}
