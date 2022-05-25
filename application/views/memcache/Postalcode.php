<!DOCTYPE html>
<html lang="en">

<head>
    <title>Postalcode</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="container">
        <h2>Table Postalcode</h2>
        <p>Generate by : <i><b><?= $generate_by; ?></b></i> (<?= $execute_time; ?>)</p>
        <form method="GET" action="<?= base_url("memcache/postalcode/"); ?>" class="form-inline">
            <div class="col-md-6 offset-md-6 form-group mb-2">
                <select name="type" class="form-control mr-2">
                    <option value="" <?= ($type == "") ? "selected" : ""; ?>>All</option>
                    <option value="postalcode_id" <?= ($type == "postalcode_id") ? "selected" : ""; ?>>postalcode_id</option>
                    <option value="id_kecamatan" <?= ($type == "id_kecamatan") ? "selected" : ""; ?>>id_kecamatan</option>
                </select>
                <input type="text" name="search" class="form-control mr-2" value="<?= isset($search) ? $search : ''; ?>" />
                <button class="btn btn-primary">Search</button>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>postalcode_id</th>
                    <th>id_kecamatan</th>
                    <th>alias_kecamatan</th>
                    <th>kelurahan</th>
                    <th>alias_kelurahan</th>
                    <th>kodepos</th>
                    <th>lat</th>
                    <th>lon</th>
                    <th>shipper_area_id</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($data) {
                    foreach ($data as $row) {
                ?>
                        <tr>
                            <td><?= $row['postalcode_id']; ?></td>
                            <td><?= $row['id_kecamatan']; ?></td>
                            <td><?= $row['alias_kecamatan']; ?></td>
                            <td><?= $row['kelurahan']; ?></td>
                            <td><?= $row['alias_kelurahan']; ?></td>
                            <td><?= $row['kodepos']; ?></td>
                            <td><?= $row['lat']; ?></td>
                            <td><?= $row['lon']; ?></td>
                            <td><?= $row['shipper_area_id']; ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>