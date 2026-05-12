<?php
declare(strict_types=1);

/**
 * Shared MySQL persistence for register-farm wizard (XAMPP).
 * Included by register_farm_farmer.php and farmer_register.php — do not emit output here.
 */

if (!function_exists('beanthentic_lipa_barangays')) {
    function beanthentic_lipa_barangays(): array
    {
        return [
            'Adya', 'Antipolo del Sur', 'Bagong Pook', 'Bulacnin', 'Halang', 'Kayumanggi', 'Latag', 'Lodlod', 'Lumbang',
            'Malagonlong', 'Malitlit', 'Pagolingin', 'Pangao', 'Pinagkawitan', 'Pinagtong-Ulan', 'Pusil', 'Quezon', 'Rizal',
            'San Benito', 'San Celestino', 'San Isidro', 'San Salvador', 'Santo Niño', 'Santo Toribio', 'Talisay', 'Tangob',
            'Tangway', 'Tipakan',
        ];
    }
}

if (!function_exists('fr_qty_to_kg')) {
    function fr_qty_to_kg($qty, string $unit): float
    {
        $q = is_numeric($qty) ? (float)$qty : 0.0;
        if ($q <= 0) {
            return 0.0;
        }
        $u = strtoupper(trim($unit));
        if ($u === 'G') {
            return $q / 1000.0;
        }
        if ($u === 'LB') {
            return $q * 0.45359237;
        }
        return $q;
    }
}

if (!function_exists('fr_map_ownership_wizard')) {
    /** Maps wizard ownership_status to farm_information ENUM (owner|tenant|co-owner|other). */
    function fr_map_ownership_wizard(string $raw): string
    {
        $s = strtolower(trim($raw));
        $map = [
            'landowner' => 'owner',
            'cloa_holder' => 'owner',
            'list_holder' => 'other',
            'sessional_farm_worker' => 'tenant',
            'others' => 'other',
            'owned' => 'owner',
            'owner' => 'owner',
            'tenant' => 'tenant',
            'lessee' => 'tenant',
            'co-owner' => 'co-owner',
            'co_owner' => 'co-owner',
            'coowner' => 'co-owner',
            'usufruct' => 'other',
            'other' => 'other',
        ];
        return $map[$s] ?? 'other';
    }
}

if (!function_exists('fr_rsbsa_tiny')) {
    function fr_rsbsa_tiny(string $raw): int
    {
        $s = strtolower(trim($raw));
        return $s === 'yes' ? 1 : 0;
    }
}

if (!function_exists('beanthentic_validate_farmer_payload')) {
    /**
     * @return array<string,string> field => message (empty if valid)
     */
    function beanthentic_validate_farmer_payload(array $d, int $userId): array
    {
        $err = [];
        if ($userId <= 0) {
            $err['user_id'] = 'Missing account. Log in again via XAMPP.';
        }
        $first = trim((string)($d['first_name'] ?? ''));
        $last = trim((string)($d['last_name'] ?? ''));
        if (strlen($first) < 2) {
            $err['first_name'] = 'Enter your first name.';
        }
        if (strlen($last) < 2) {
            $err['last_name'] = 'Enter your last name.';
        }
        $barangay = trim((string)($d['barangay'] ?? ''));
        if ($barangay === '') {
            $err['barangay'] = 'Select your barangay in Lipa City.';
        } elseif (!in_array($barangay, beanthentic_lipa_barangays(), true)) {
            $err['barangay'] = 'Barangay must be within Lipa City.';
        }

        $role = trim((string)($d['affiliation_role'] ?? ''));
        if ($role === '') {
            $err['affiliation_role'] = 'Select your role.';
        }
        $ncfrs = strtolower(trim((string)($d['ncfrs'] ?? '')));
        if (!in_array($ncfrs, ['yes', 'no'], true)) {
            $err['ncfrs'] = 'Select NCFRS (Yes or No).';
        }
        $rsb = strtolower(trim((string)($d['rsbsa_registered'] ?? '')));
        if (!in_array($rsb, ['yes', 'no', 'pending'], true)) {
            $err['rsbsa_registered'] = 'Select RSBSA registration status.';
        }
        $rsbNum = trim((string)($d['rsbsa_number'] ?? ''));
        if ($rsb === 'yes' && strlen($rsbNum) < 4) {
            $err['rsbsa_number'] = 'Enter your RSBSA number.';
        }

        $own = strtolower(trim((string)($d['ownership_status'] ?? '')));
        $okOwn = ['landowner', 'cloa_holder', 'list_holder', 'sessional_farm_worker', 'others'];
        if (!in_array($own, $okOwn, true)) {
            $err['ownership_status'] = 'Select status of ownership.';
        }
        $unit = strtolower(trim((string)($d['plant_area_unit'] ?? '')));
        if (!in_array($unit, ['ha', 'sqm', 'ac'], true)) {
            $err['plant_area_unit'] = 'Select a unit.';
        }
        $rawA = isset($d['plant_area_value']) ? trim((string)$d['plant_area_value']) : '';
        if ($rawA === '') {
            $err['plant_area_value'] = 'Enter total plant area.';
        } else {
            $a = (float)$rawA;
            if (!is_finite($a) || $a <= 0) {
                $err['plant_area_value'] = 'Enter an area greater than zero.';
            } elseif ($a > 1e6) {
                $err['plant_area_value'] = 'Value is too large.';
            }
        }

        foreach (['liberica', 'robusta', 'excelsa'] as $v) {
            foreach ([$v . '_bearing', $v . '_non_bearing'] as $key) {
                $raw = isset($d[$key]) ? trim((string)$d[$key]) : '';
                if ($raw === '') {
                    continue;
                }
                $n = (int)$raw;
                if ((string)$n !== $raw || $n < 0) {
                    $err[$key] = 'Use a whole number ≥ 0.';
                }
            }
        }

        $photo = trim((string)($d['profile_photo_data'] ?? ''));
        if ($photo === '') {
            $err['profile_photo_data'] = 'Please take or upload a profile photo.';
        }

        if (($d['agree_registration'] ?? '') !== 'yes') {
            $err['agree_registration'] = 'Please confirm the declaration before submitting.';
        }

        return $err;
    }
}

if (!function_exists('beanthentic_farmer_mysql_save')) {
    /**
     * @param array<string,mixed> $body
     */
    function beanthentic_farmer_mysql_save(PDO $pdo, int $userId, array $body): int
    {
        $chk = $pdo->prepare('SELECT user_id FROM users WHERE user_id = ? AND is_active = 1 LIMIT 1');
        $chk->execute([$userId]);
        if (!$chk->fetch()) {
            throw new RuntimeException('Invalid user.');
        }

        $first = trim((string)($body['first_name'] ?? ''));
        $last = trim((string)($body['last_name'] ?? ''));
        $phone = beanthentic_normalize_phone(trim((string)($body['phone'] ?? '')));
        if ($phone === '') {
            $phone = null;
        }
        $barangay = trim((string)($body['barangay'] ?? ''));
        $farmAddr = trim((string)($body['farm_address'] ?? ''));
        $ownership = fr_map_ownership_wizard((string)($body['ownership_status'] ?? ''));
        $plantVal = $body['plant_area_value'] ?? null;
        $plantUnit = strtolower(trim((string)($body['plant_area_unit'] ?? '')));
        $plantHa = null;
        if (is_numeric($plantVal)) {
            $pv = (float)$plantVal;
            if ($plantUnit === 'ha') {
                $plantHa = $pv;
            } elseif ($plantUnit === 'sqm') {
                $plantHa = $pv / 10000.0;
            } elseif ($plantUnit === 'ac') {
                $plantHa = $pv * 0.40468564224;
            }
        }

        $year = (int)($body['production_year'] ?? date('Y'));
        if ($year < 2000 || $year > 2100) {
            $year = (int)date('Y');
        }

        $libKg = fr_qty_to_kg($body['liberica_prod_qty'] ?? 0, (string)($body['liberica_prod_unit'] ?? 'kg'));
        $robKg = fr_qty_to_kg($body['robusta_prod_qty'] ?? 0, (string)($body['robusta_prod_unit'] ?? 'kg'));
        $excKg = fr_qty_to_kg($body['excelsa_prod_qty'] ?? 0, (string)($body['excelsa_prod_unit'] ?? 'kg'));

        $fed = trim((string)($body['federation'] ?? $body['affiliation_role'] ?? ''));
        $assoc = trim((string)($body['association'] ?? ''));
        $rsb = (string)($body['rsbsa_registered'] ?? '');
        $rsbNo = trim((string)($body['rsbsa_number'] ?? ''));

        $pdo->beginTransaction();

        $selF = $pdo->prepare('SELECT farmer_id FROM farmers WHERE user_id = ? LIMIT 1');
        $selF->execute([$userId]);
        $row = $selF->fetch();
        if ($row) {
            $farmerId = (int)$row['farmer_id'];
            $pdo->prepare('UPDATE farmers SET status = "active" WHERE farmer_id = ?')->execute([$farmerId]);
        } else {
            $pdo->prepare('INSERT INTO farmers (user_id, status) VALUES (?, "active")')->execute([$userId]);
            $farmerId = (int)$pdo->lastInsertId();
        }

        $addrLine = $farmAddr !== '' ? $farmAddr : ($barangay !== '' ? $barangay : null);

        $piSel = $pdo->prepare('SELECT personal_info_id FROM personal_information WHERE farmer_id = ? LIMIT 1');
        $piSel->execute([$farmerId]);
        if ($piSel->fetch()) {
            $pdo->prepare(
                'UPDATE personal_information SET first_name = ?, last_name = ?, contact_number = COALESCE(?, contact_number),
                 barangay = ?, current_address = COALESCE(?, current_address) WHERE farmer_id = ?'
            )->execute([
                $first !== '' ? $first : null,
                $last !== '' ? $last : null,
                $phone,
                $barangay !== '' ? $barangay : null,
                $addrLine,
                $farmerId,
            ]);
        } else {
            $pdo->prepare(
                'INSERT INTO personal_information (farmer_id, first_name, last_name, contact_number, barangay, current_address)
                 VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([
                $farmerId,
                $first !== '' ? $first : null,
                $last !== '' ? $last : null,
                $phone,
                $barangay !== '' ? $barangay : null,
                $addrLine,
            ]);
        }

        $fiSel = $pdo->prepare('SELECT farm_info_id FROM farm_information WHERE farmer_id = ? LIMIT 1');
        $fiSel->execute([$farmerId]);
        if ($fiSel->fetch()) {
            $pdo->prepare(
                'UPDATE farm_information SET ownership_status = ?, farm_address = COALESCE(?, farm_address), barangay = COALESCE(?, barangay), farm_size_ha = COALESCE(?, farm_size_ha) WHERE farmer_id = ?'
            )->execute([$ownership, $addrLine, $barangay !== '' ? $barangay : null, $plantHa, $farmerId]);
        } else {
            $pdo->prepare(
                'INSERT INTO farm_information (farmer_id, ownership_status, farm_address, barangay, farm_size_ha)
                 VALUES (?, ?, ?, ?, ?)'
            )->execute([$farmerId, $ownership, $addrLine, $barangay !== '' ? $barangay : null, $plantHa]);
        }

        $affSel = $pdo->prepare('SELECT affiliation_info_id FROM affiliation_information WHERE farmer_id = ? LIMIT 1');
        $affSel->execute([$farmerId]);
        if ($affSel->fetch()) {
            $pdo->prepare(
                'UPDATE affiliation_information SET federation_assoc = ?, coop_name = ?, rsbsa_registered = ?, rsbsa_number = ? WHERE farmer_id = ?'
            )->execute([$fed !== '' ? $fed : null, $assoc !== '' ? $assoc : null, fr_rsbsa_tiny($rsb), $rsbNo !== '' ? $rsbNo : null, $farmerId]);
        } else {
            $pdo->prepare(
                'INSERT INTO affiliation_information (farmer_id, federation_assoc, coop_name, rsbsa_registered, rsbsa_number)
                 VALUES (?, ?, ?, ?, ?)'
            )->execute([$farmerId, $fed !== '' ? $fed : null, $assoc !== '' ? $assoc : null, fr_rsbsa_tiny($rsb), $rsbNo !== '' ? $rsbNo : null]);
        }

        $rb = (int)($body['robusta_bearing'] ?? 0);
        $rn = (int)($body['robusta_non_bearing'] ?? 0);
        $lb = (int)($body['liberica_bearing'] ?? 0);
        $ln = (int)($body['liberica_non_bearing'] ?? 0);
        $eb = (int)($body['excelsa_bearing'] ?? 0);
        $en = (int)($body['excelsa_non_bearing'] ?? 0);

        $tcSel = $pdo->prepare('SELECT tree_count_id FROM tree_counts WHERE farmer_id = ? AND record_year = ? LIMIT 1');
        $tcSel->execute([$farmerId, $year]);
        if ($tcSel->fetch()) {
            $pdo->prepare(
                'UPDATE tree_counts SET robusta_bearing = ?, robusta_non_bearing = ?, liberica_bearing = ?, liberica_non_bearing = ?, excelsa_bearing = ?, excelsa_non_bearing = ? WHERE farmer_id = ? AND record_year = ?'
            )->execute([$rb, $rn, $lb, $ln, $eb, $en, $farmerId, $year]);
        } else {
            $pdo->prepare(
                'INSERT INTO tree_counts (farmer_id, record_year, robusta_bearing, robusta_non_bearing, liberica_bearing, liberica_non_bearing, excelsa_bearing, excelsa_non_bearing)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute([$farmerId, $year, $rb, $rn, $lb, $ln, $eb, $en]);
        }

        $totalKg = $libKg + $robKg + $excKg;
        $prSel = $pdo->prepare('SELECT production_info_id FROM production_information WHERE farmer_id = ? AND production_year = ? LIMIT 1');
        $prSel->execute([$farmerId, $year]);
        if ($prSel->fetch()) {
            $pdo->prepare(
                'UPDATE production_information SET robusta_qty_kg = ?, liberica_qty_kg = ?, excelsa_qty_kg = ?, beans_remaining_kg = ? WHERE farmer_id = ? AND production_year = ?'
            )->execute([$robKg, $libKg, $excKg, $totalKg, $farmerId, $year]);
        } else {
            $pdo->prepare(
                'INSERT INTO production_information (farmer_id, production_year, robusta_qty_kg, liberica_qty_kg, excelsa_qty_kg, beans_remaining_kg)
                 VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([$farmerId, $year, $robKg, $libKg, $excKg, $totalKg]);
        }

        $pdo->commit();
        return $farmerId;
    }
}
