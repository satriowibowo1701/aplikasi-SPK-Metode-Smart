<?php
include "header.php";
$page = isset($_GET['page']) ? $_GET['page'] : "";
?>
<div class="row cells4">
	<div class="cell colspan2">
		<h3>Eksekusi Perhitungan</h3>
	</div>
	<div class="cell colspan2 align-right">
		<a href="perangkingan.php" class="button info">Kembali</a>
	</div>
</div>
<table class="table striped hovered cell-hovered border bordered dataTable" data-role="datatable" data-searching="true">
	<thead>
		<tr>
			<th width="50">No</th>
			<th>Alternatif</th>
			<?php
			$stmt2x = $db->prepare("select * from smart_kriteria");
			$stmt2x->execute();
			while ($row2x = $stmt2x->fetch()) {
			?>
				<th><?php echo $row2x['nama_kriteria'] ?></th>
			<?php
			}
			?>
			<th>Hasil</th>
			<th>Keterangan</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>-</td>
			<td>Bobot</td>
			<?php
			$stmt2x1 = $db->prepare("select * from smart_kriteria");
			$stmt2x1->execute();
			while ($row2x1 = $stmt2x1->fetch()) {
			?>
				<td><?php echo $row2x1['bobot_kriteria'] ?></td>
			<?php
			}
			?>
			<td>-</td>
			<td>-</td>
		</tr>
		<?php
		$stmtx = $db->prepare("select * from smart_alternatif");
		$noxx = 1;
		$stmtx->execute();
		while ($rowx = $stmtx->fetch()) {
		?>
			<tr>
				<td><?php echo $noxx++ ?></td>
				<td><?php echo $rowx['nama_alternatif'] ?></td>
				<?php
				$stmt3x = $db->prepare("select * from smart_kriteria");
				$stmt3x->execute();
				while ($row3x = $stmt3x->fetch()) {
				?>
					<td>
						<?php
						$stmt4x = $db->prepare("select * from smart_alternatif_kriteria where id_kriteria='" . $row3x['id_kriteria'] . "' and id_alternatif='" . $rowx['id_alternatif'] . "'");
						$stmt4x->execute();
						while ($row4x = $stmt4x->fetch()) {
							$ida = $row4x['id_alternatif'];
							$idk = $row4x['id_kriteria'];
							echo number_format($row4x['nilai_alternatif_kriteria'] * $row3x['bobot_kriteria'], 4);
							$data = ($row4x['nilai_alternatif_kriteria'] - $row3x['min']) <= 0 ? 0 : (($row4x['nilai_alternatif_kriteria'] - $row3x['min']) / ($row3x['max'] - $row3x['min']));
							$kal = 100 * $data;
							$stmt2x3 = $db->prepare("update smart_alternatif_kriteria set bobot_alternatif_kriteria=? where id_alternatif=? and id_kriteria=?");
							$stmt2x3->bindParam(1, $kal);
							$stmt2x3->bindParam(2, $ida);
							$stmt2x3->bindParam(3, $idk);
							$stmt2x3->execute();
						}
						?>
					</td>
				<?php
				}
				?>
				<td>
					<?php
					$stmt3x2 = $db->prepare("select s.bobot_alternatif_kriteria,a.bobot_kriteria from smart_alternatif_kriteria s join smart_kriteria a on s.id_kriteria = a.id_kriteria where s.id_alternatif='" . $rowx['id_alternatif'] . "'");
					$stmt3x2->execute();
					$row3x2 = $stmt3x2->fetchAll();
					$hsl = 0;
					foreach ($row3x2 as $res) {
						$hsl += $res["bobot_alternatif_kriteria"] * $res["bobot_kriteria"];
					}
					$hsl = number_format($hsl, 4, '.', '');
					$ideas = $rowx['id_alternatif'];
					echo $hsl;
					if ($hsl >= 85) {
						$ket = "Sangat Bagus";
					} else if ($hsl >= 70) {
						$ket = "Bagus";
					} else if ($hsl >= 55) {
						$ket = "Perlu Dievaluasi";
					} else {
						$ket = "Tidak Bagus";
					}
					$stmt2x3y = $db->prepare("update smart_alternatif set hasil_alternatif=?, ket_alternatif=? where id_alternatif=?");
					$stmt2x3y->bindParam(1, $hsl);
					$stmt2x3y->bindParam(2, $ket);
					$stmt2x3y->bindParam(3, $ideas);
					$stmt2x3y->execute();
					?>
				</td>
				<td>
					<?php
					$data = explode('.', $hsl)[1];
					echo $data >= 5000 ? ceil($hsl) . "%" : floor($hsl) . "%";
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>
<p><br /></p>
<?php
include "footer.php";
?>