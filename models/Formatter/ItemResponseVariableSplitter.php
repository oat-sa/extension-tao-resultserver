<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\taoResultServer\models\Formatter;

use oat\oatbox\service\ConfigurableService;

class ItemResponseVariableSplitter extends ConfigurableService
{
    public function splitByAttempt(array $itemVariables): array
    {
        $attempts = [];

        foreach ($itemVariables as $variable) {
            if ($variable['identifier'] == 'numAttempts') {
                $attempts[(string)$this->getVariableTime($variable['epoch'])] = [];
            }
        }

        foreach ($itemVariables as $variable) {
            $cand = null;
            $bestDist = null;

            foreach (array_keys($attempts) as $time) {
                $dist = abs($time - $this->getVariableTime($variable['epoch']));

                if (is_null($bestDist) || $dist < $bestDist) {
                    $bestDist = $dist;
                    $cand = $time;
                }
            }
            $attempts[$cand][] = $variable;
        }

        return $attempts;
    }

    public function splitObjByAttempt(array $itemVariables): array
    {
        $attempts = [];

        foreach ($itemVariables as $variable) {
            if ($variable->variable->getIdentifier() == 'numAttempts') {
                $attempts[(string)$variable->variable->getCreationTime()] = [];
            }
        }
        foreach ($itemVariables as $variable) {
            $cand = null;
            $bestDist = null;

            foreach (array_keys($attempts) as $time) {
                $dist = abs($time - $variable->variable->getCreationTime());

                if (is_null($bestDist) || $dist < $bestDist) {
                    $bestDist = $dist;
                    $cand = $time;
                }
            }
            $attempts[$cand][] = $variable;
        }

        return $attempts;
    }


    private function getVariableTime(string $epoch): float
    {
        [$usec, $sec] = explode(' ', $epoch);

        return ((float)$usec + (float)$sec);
    }
}
