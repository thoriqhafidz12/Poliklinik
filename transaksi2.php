
<?php 

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan kembali ke halaman login
if(!isset($_SESSION["username"])){
    header("location: index.php?page=loginUser");
    exit;
}
?>

<!-- FORM -->
<div class="form-group mx-sm-3 mb-2">
    <form action="" onsubmit="return(validate());" method="post">
        <!-- AMBIL DATA UNTUK UBAH -->
            <?php
            include ('koneksi.php');
            $id_periksa = '';
            $id_obat = '';
            if (isset($_GET['id'])) {
                $ambil = mysqli_query($mysqli, 
                "SELECT * FROM detail_periksa 
                WHERE id='" . $_GET['id'] . "'");
                while ($row = mysqli_fetch_array($ambil)) {
                    $id_periksa = $row['id_periksa'];
                    $id_obat = $row['id_obat'];
                }
            ?>
                <input type=hidden name="id_periksa" value="<?php echo
                $_GET['id'] ?>">
            <?php
            }
            ?>
        <!-- SELECT PASIEN -->
        <label class="fw-bold">Data Periksa</label>
        <select class="form-control my-2" name="id_periksa">
           <?php
           include ('koneksi.php');
           $selected = '';
           $periksa = mysqli_query($mysqli, "SELECT * FROM periksa");
           while ($data = mysqli_fetch_array($periksa)) {
               if ($data['id'] == $id_periksa) {
                   $selected = 'selected="selected"';
               } else {
                   $selected = '';
               }
           ?>
               <option value="<?php echo $data['id'] ?>"  <?php echo $selected?>> <?php echo $data['id_pasien'] ?></option>
           <?php
           }
           ?>
        </select>

        <!-- SELECT DOKTER -->
       <label class="fw-bold">Obat</label>
       <select class="form-control my-2" name="id_obat">
           <?php
           include ('koneksi.php');
           $selected = '';
           $obat = mysqli_query($mysqli, "SELECT * FROM obat");
           while ($data = mysqli_fetch_array($obat)) {
               if ($data['id'] == $id_obat) {
                   $selected = 'selected="selected"';
               } else {
                   $selected = '';
               }
           ?>
               <option value="<?php echo $data['id'] ?>"  <?php echo $selected ?>><?php echo $data['nama_obat'] ?> | Rp.    <?php echo $data['harga'] ?></option>
           <?php
           }
           ?>
       </select>

       <button class="btn btn-primary" type="submit" name="simpan" >Submit</button>
    </form>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                <th scope="col">No</th>
                <th scope="col">Dokter</th>
                <th scope="col">Pasien</th>
                <th scope="col">Obat</th>
                <th scope="col">Harga</th>
                </tr>
            </thead>
            <?php
            include ('koneksi.php');
            date_default_timezone_set("Asia/Jakarta");
            $result = mysqli_query($mysqli, "SELECT dp.*,
            o.nama_obat as 'nama_obat', o.harga as 'harga', pr.id_pasien as 'nama_pasien', pr.id_dokter as 'nama_dokter'
            FROM detail_periksa dp 
            LEFT JOIN obat o ON (dp.id_obat=o.id)
            LEFT JOIN periksa pr ON (dp.id_periksa=pr.id)
            ") ;
            $no = 1;
            while ($data = mysqli_fetch_array($result)) {
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $data['nama_pasien'] ?></td>
                    <td><?php echo $data['nama_dokter'] ?></td>
                    <td><?php echo $data['nama_obat'] ?></td>
                    <td><?php echo $data['harga'] ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                <th scope="col">No</th>
                <th scope="col">Nama</th>
                <th scope="col">Dokter</th>
                <th scope="col">Tanggal Periksa</th>
                <th scope="col">Catatan</th>
                <th scope="col">Biaya Periksa</th>
                <th scope="col">Aksi</th>
                </tr>
            </thead>
            <?php
            include ('koneksi.php');
            date_default_timezone_set("Asia/Jakarta");
            $result = mysqli_query($mysqli, "SELECT pr.*,d.nama as 'nama_dokter', p.nama as 'nama_pasien' FROM periksa pr LEFT JOIN dokter d ON (pr.id_dokter=d.id) LEFT JOIN pasien p ON (pr.id_pasien=p.id) ORDER BY pr.tgl_periksa ASC");
            $no = 1;
            while ($data = mysqli_fetch_array($result)) {
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $data['nama_pasien'] ?></td>
                    <td><?php echo $data['nama_dokter'] ?></td>
                    <td><?php echo date('d-M-Y H:i:s', strtotime ($data['tgl_periksa']))  ?></td>
                    <td><?php echo $data['catatan'] ?></td>
                    <td>
                        <?php if ($data['biaya_periksa'] == null){?>
                        <a class="btn btn-danger rounded-pill px-3" 
                        href="index.php?page=transaksi&id=<?php echo $data['id'] ?>">Detail</a>
                        <?php
                        } else{
                        echo 'Rp. '.$data['biaya_periksa'];
                        } 
                        ?>
                    </td>
                    <td>
                        <a class="btn btn-success rounded-pill px-3" 
                        href="index.php?page=periksa&id=<?php echo $data['id'] ?>">
                        Ubah</a>
                        <a class="btn btn-danger rounded-pill px-3" 
                        href="index.php?page=periksa&id=<?php echo $data['id'] ?>&aksi=hapus">Hapus</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>

<!-- FUNGSI CRUD -->
<?php
include ('koneksi.php');
if (isset($_POST['simpan'])) {
    $id_periksa = $_POST['id_periksa'];
    $id_obat = $_POST['id_obat'];
    if (isset($_POST['id'])) {
        $sql = "UPDATE detail_periksa SET id_periksa='$id_periksa',id_obat='$id_obat',tgl_periksa='$tgl_periksa',catatan = '$catatan' WHERE id = ". $_POST['id']."";
    } else {
        $sql = "INSERT INTO detail_periksa (id_periksa,id_obat) VALUES ('$id_periksa','$id_obat')";
    }
        
    if ($mysqli->query($sql) == TRUE)
    { echo "<script> 
        document.location='index.php?page=transaksi2';
        </script>";}
    else
    {echo "Error: " . $sql . "<br>" . $mysqli->error;}
    $mysqli->close();
}

if (isset($_GET['aksi'])) {
    if ($_GET['aksi'] == 'hapus') {
        $hapus = mysqli_query($mysqli, "DELETE FROM periksa WHERE id = '" . $_GET['id'] . "'");
    }

    echo "<script> 
            alert('Data Berhasil Dihapus');
            document.location='index.php?page=periksa';
            </script>";
}
?>