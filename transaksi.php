
<?php 

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan kembali ke halaman login
if(!isset($_SESSION["username"])){
    header("location: index.php?page=loginUser");
    exit;
}
?>

<div class="container text-center">
    <div class="row justify-content-center">
        <div class="col-4 card" style="width: 18rem;">
            <table class="table table-responsive text-center">
                <?php
                include ('koneksi.php');
                date_default_timezone_set("Asia/Jakarta");
                $result = mysqli_query($mysqli, "SELECT pr.*,d.nama as 'nama_dokter', p.nama as 'nama_pasien' FROM periksa pr LEFT JOIN dokter d ON (pr.id_dokter=d.id) LEFT JOIN pasien p ON (pr.id_pasien=p.id) WHERE pr.id='" . $_GET['id'] . "'");
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                ?>
                    <tr><td><?php echo $data['nama_pasien'] ?></td></tr>
                    <tr><td><?php echo $data['nama_dokter'] ?></td></tr>
                    <tr><td><?php echo date('d-M-Y H:i:s', strtotime ($data['tgl_periksa']))  ?></td></tr>
                    <tr><td><?php echo $data['catatan'] ?></td></tr>
                    <tr></tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>



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
            <input type=hidden name="id" value="<?php echo
            $_GET['id'] ?>">
        <?php
        }
        ?>

    <!-- SELECT OBAT -->
    <input type="text" name="id_periksa" value="<?php echo $_GET['id'] ?>" hidden>
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
            $total = 150000 + $data['harga'] ;
        ?>
            <option value="<?php echo $data['id'] ?>"  <?php echo $selected ?>><?php echo $data['nama_obat'] ?> | <?php echo $data['kemasan'] ?> | Rp. <?php echo $data['harga'] ?> | Rp. <?= $total; ?></option>
        <?php
        }
        ?>
    </select>

    <button class="btn btn-primary" type="submit" name="simpan" >Submit</button>
</form>

<!-- FUNGSI CRUD -->
<?php
include ('koneksi.php');
if (isset($_POST['simpan'])) {
    $id_periksa = $_POST['id_periksa'];
    $id_obat = $_POST['id_obat'];
    $total = $_POST['biaya_periksa'];
    if (isset($_POST['id_periksa'])) {
        $sql = "INSERT INTO detail_periksa (id_periksa,id_obat) VALUES ('$id_periksa','$id_obat')";
    }
        
    if ($mysqli->query($sql) == TRUE)
    {echo "<script> 
     document.location='index.php?page=periksa';
     </script>";}
    else
    {echo "Error: " . $sql . "<br>" . $mysqli->error;}
    $mysqli->close();
}
?>